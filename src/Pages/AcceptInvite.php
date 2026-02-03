<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Pages;

use AlessandroNuunes\FilamentMember\Support\ConfigHelper;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Override;

class AcceptInvite extends SimplePage
{
    public mixed $invite = null;

    public mixed $tenant = null;

    public ?string $token = null;

    public ?array $data = [];

    public bool $showRegisterForm = false;

    public bool $isGenericInvite = false;

    protected static bool $shouldRegisterNavigation = false;

    protected function getUserModel(): string
    {
        return ConfigHelper::getUserModel();
    }

    protected function getTenantModel(): string
    {
        return ConfigHelper::getTenantModel();
    }

    protected function getTenantInviteModel(): string
    {
        return ConfigHelper::getTenantInviteModel();
    }

    protected function getTenantRoleEnum(): string
    {
        return ConfigHelper::getTenantRoleEnum();
    }

    protected function hasValidInvite(): bool
    {
        return $this->invite !== null || $this->tenant !== null;
    }

    protected function isInvalidInvite(): bool
    {
        return ! $this->hasValidInvite();
    }

    public function mount(string $token): void
    {
        $this->token = $token;

        $inviteModel = $this->getTenantInviteModel();
        $this->invite = $inviteModel::query()
            ->with(['tenant', 'user'])
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $this->invite) {
            $tenantModel = $this->getTenantModel();
            $this->tenant = $tenantModel::query()
                ->where('invitation_token', $token)
                ->first();

            if ($this->tenant) {
                $this->isGenericInvite = true;
            }
        }

        if ($this->isInvalidInvite()) {
            return;
        }

        if (auth()->check()) {
            $this->handleLoggedInUser();
        }

        $this->form->fill([
            'email' => $this->invite?->email,
        ]);
    }

    protected function handleLoggedInUser(): void
    {
        $user = auth()->user();

        if ($this->isGenericInvite) {
            $this->acceptInvite($user);

            return;
        }

        if ($user->email !== $this->invite->email) {
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();

            Notification::make()
                ->title(__('filament-member::default.notification.different_email'))
                ->body(__('filament-member::default.notification.different_email_body'))
                ->warning()
                ->send();

            return;
        }

        $this->acceptInvite($user);
    }

    protected function acceptInvite(mixed $user): void
    {
        $targetTenant = $this->isGenericInvite ? $this->tenant : $this->invite->tenant;
        $enumClass = $this->getTenantRoleEnum();
        $defaultRole = ConfigHelper::getInviteConfig('default_role', 'member');

        $role = $this->isGenericInvite
            ? $defaultRole
            : ($this->invite->role ?? $defaultRole);

        if (enum_exists($enumClass) && $role instanceof $enumClass) {
            $role = $role->value;
        }

        if ($targetTenant->users()->where('user_id', $user->id)->exists()) {
            Notification::make()
                ->title(__('filament-member::default.notification.already_member'))
                ->body(__('filament-member::default.notification.already_member_body'))
                ->warning()
                ->send();

            $this->redirect(
                Filament::getPanel('admin')->getUrl($targetTenant),
                navigate: false
            );

            return;
        }

        $roleColumn = ConfigHelper::getRelationshipColumn('tenant_user_role_column');
        $targetTenant->users()->syncWithoutDetaching([
            $user->id => [$roleColumn => $role],
        ]);

        if ($this->invite) {
            $this->invite->update(['accepted_at' => now()]);
        }

        Notification::make()
            ->title(__('filament-member::default.notification.invite_accepted'))
            ->body(__('filament-member::default.notification.invite_accepted_body'))
            ->success()
            ->send();

        $this->redirect(
            Filament::getPanel('admin')->getUrl($targetTenant),
            navigate: false
        );
    }

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return __('filament-member::default.page.title.accept_invite');
    }

    #[Override]
    public function getHeading(): string|Htmlable|null
    {
        if ($this->isInvalidInvite()) {
            return __('filament-member::default.page.heading.invalid_invite');
        }

        return __('filament-member::default.page.heading.invited');
    }

    #[Override]
    public function getSubheading(): string|Htmlable|null
    {
        if ($this->isInvalidInvite()) {
            return __('filament-member::default.page.subheading.invalid_invite');
        }

        if ($this->isGenericInvite) {
            return __('filament-member::default.page.subheading.generic_invite', ['name' => $this->tenant->name]);
        }

        $inviterName = optional($this->invite->user)->name ?? __('filament-member::default.message.someone');

        return __('filament-member::default.page.subheading.individual_invite', ['name' => $inviterName]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filament-member::default.label.name'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                TextInput::make('email')
                    ->label(__('filament-member::default.label.email'))
                    ->email()
                    ->required()
                    ->unique(ConfigHelper::getUserModel(), 'email')
                    ->disabled(fn (): bool => ! $this->isGenericInvite)
                    ->dehydrated(),

                TextInput::make('password')
                    ->label(__('filament-member::default.label.password'))
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->revealable(),

                TextInput::make('password_confirmation')
                    ->label(__('filament-member::default.label.password_confirmation'))
                    ->password()
                    ->required()
                    ->same('password')
                    ->revealable(),
            ]);
    }

    public function register(): void
    {
        $data = $this->form->getState();

        $email = $this->isGenericInvite ? $data['email'] : $this->invite->email;

        $userModel = $this->getUserModel();
        $user = $userModel::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        auth()->login($user);

        $this->acceptInvite($user);
    }

    public function goToLogin(): void
    {
        if (! ConfigHelper::getInviteConfig('require_registration', true)) {
            return;
        }

        session(['invite_token' => $this->token]);

        $email = $this->isGenericInvite ? null : $this->invite->email;

        $this->redirect(
            route('filament.admin.auth.login', array_filter(['email' => $email])),
            navigate: false
        );
    }

    public function showRegister(): void
    {
        $this->showRegisterForm = true;
    }

    public function hideRegister(): void
    {
        $this->showRegisterForm = false;
    }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->label(__('filament-member::default.action.login'))
            ->icon('heroicon-o-arrow-right-on-rectangle')
            ->action('goToLogin');
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->label(__('filament-member::default.action.register'))
            ->icon('heroicon-o-user-plus')
            ->color('gray')
            ->action('showRegister');
    }

    public function submitRegisterAction(): Action
    {
        return Action::make('submitRegister')
            ->label(__('filament-member::default.action.submit_register'))
            ->submit('register');
    }

    public function backAction(): Action
    {
        return Action::make('back')
            ->label(__('filament-member::default.action.back'))
            ->color('gray')
            ->action('hideRegister');
    }

    public function goToLoginAction(): Action
    {
        return Action::make('goToLogin')
            ->label(__('filament-member::default.action.go_to_login'))
            ->url(route('filament.admin.auth.login'));
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getInviteInfoComponent(),
                $this->getActionsComponent(),
                $this->getRegisterFormComponent(),
                $this->getInvalidInviteComponent(),
            ]);
    }

    protected function getInviteInfoComponent(): Component
    {
        return Section::make()
            ->compact()
            ->schema([
                Text::make('tenant_info')
                    ->content(fn (): View => view('filament-member::filament.pages.invite-info', [
                        'invite' => $this->invite,
                        'tenant' => $this->tenant,
                        'isGenericInvite' => $this->isGenericInvite,
                    ]))
                    ->extraAttributes(['class' => 'w-full']),
            ])
            ->visible(fn (): bool => $this->hasValidInvite() && ! $this->showRegisterForm);
    }

    protected function getActionsComponent(): Component
    {
        return Actions::make([
            $this->loginAction(),
            $this->registerAction(),
        ])
            ->fullWidth()
            ->visible(fn (): bool => $this->hasValidInvite() && ! $this->showRegisterForm);
    }

    protected function getRegisterFormComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('register')
            ->footer([
                Actions::make([
                    $this->submitRegisterAction(),
                ])
                    ->fullWidth(),
            ])
            ->visible(fn (): bool => $this->hasValidInvite() && $this->showRegisterForm);
    }

    protected function getInvalidInviteComponent(): Component
    {
        return Actions::make([
            $this->goToLoginAction(),
        ])
            ->fullWidth()
            ->visible(fn (): bool => $this->isInvalidInvite());
    }
}
