<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Pages;

use AlessandroNuunes\FilamentMember\Events\TenantInviteCreated;
use AlessandroNuunes\FilamentMember\Rules\AlreadyMember;
use AlessandroNuunes\FilamentMember\Support\ConfigHelper;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Resources\Concerns\HasTabs;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Override;

class TenantMembers extends Page
{
    use HasTabs;

    public ?array $data = [];

    protected string $view = 'filament-member::filament.pages.tenant-members';

    protected static ?string $slug = 'members';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 2;

    #[Override]
    public static function getNavigationGroup(): ?string
    {
        return __('filament-member::default.navigation.group');
    }

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('filament-member::default.navigation.label');
    }

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return __('filament-member::default.page.title.members');
    }

    public static function getRelativeRouteName(Panel $panel): string
    {
        return 'members';
    }

    protected function getTenantRoleEnum(): string
    {
        return ConfigHelper::getTenantRoleEnum();
    }

    protected function getTenantRoleOptions(): array
    {
        $enumClass = $this->getTenantRoleEnum();

        if (! enum_exists($enumClass)) {
            return [];
        }

        $ownerValue = defined($enumClass . '::Owner')
            ? $enumClass::Owner->value
            : 'owner';

        return collect($enumClass::cases())
            ->mapWithKeys(fn ($case): array => [
                $case->value => method_exists($case, 'getLabel') ? $case->getLabel() : $case->name,
            ])
            ->except($ownerValue)
            ->all();
    }

    public function mount(): void
    {
        $this->getSchema('form')?->fill();
        $this->loadDefaultActiveTab();
    }

    public function form(Schema $schema): Schema
    {
        $tenant = Filament::getTenant();

        return $schema
            ->statePath('data')
            ->components([
                Section::make()
                    ->description(__('filament-member::default.message.invite_members_description'))
                    ->schema([
                        Repeater::make('emailAddresses')
                            ->label(__('filament-member::default.label.email_address'))
                            ->hiddenLabel()
                            ->minItems(1)
                            ->maxItems(5)
                            ->defaultItems(1)
                            ->deletable(fn ($state): bool => count((array) $state) > 1)
                            ->reorderable(false)
                            ->addActionLabel(__('filament-member::default.action.add_another_member'))
                            ->schema([
                                Grid::make()
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('email')
                                            ->required()
                                            ->columnSpan(2)
                                            ->placeholder(__('filament-member::default.validation.email_placeholder'))
                                            ->email()
                                            ->distinct()
                                            ->rule(Rule::unique(ConfigHelper::getTenantInviteModel(), 'email')->where('tenant_id', $tenant?->id)->whereNull('accepted_at'))
                                            ->rule(new AlreadyMember())
                                            ->validationMessages([
                                                'unique' => __('filament-member::default.validation.email_already_pending'),
                                            ]),
                                        Select::make('role')
                                            ->label(__('filament-member::default.label.role'))
                                            ->options($this->getTenantRoleOptions())
                                            ->required(ConfigHelper::getValidationConfig('require_role_on_invite', true)),
                                    ]),

                            ]),
                    ])
                    ->headerActions([
                        Action::make('inviteLink')
                            ->label(__('filament-member::default.action.invite_link'))
                            ->icon('heroicon-o-clipboard')
                            ->button()
                            ->size('sm')
                            ->action(function (): void {
                                $tenant = Filament::getTenant();
                                if (blank($tenant)) {
                                    return;
                                }

                                if (blank($tenant->invitation_token)) {
                                    $tenant->update(['invitation_token' => Str::random(32)]);
                                    $tenant->refresh();
                                }

                                $routeName = ConfigHelper::getRoute('invite_accept_name');
                                $signedRoute = URL::signedRoute(
                                    'filament.admin.'.$routeName,
                                    ['token' => $tenant->invitation_token],
                                    now()->addDays(30)
                                );
                                $this->js(
                                    'window.navigator.clipboard.writeText("'.$signedRoute.'");'.
                                    '$tooltip("'.__('filament-member::default.notification.invite_copied').'", { timeout: 1500 });'
                                );
                                Notification::make()
                                    ->title(__('filament-member::default.notification.invite_copied'))
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->id('form')
                    ->footerActionsAlignment(Alignment::Between)
                    ->footerActions([
                        Action::make('description')
                            ->label(fn (): HtmlString => new HtmlString('<span class="text-sm text-gray-500 dark:text-gray-400 font-normal">'.__('filament-member::default.message.limit_5_invites').'</span>'))
                            ->link()
                            ->disabled(),
                        Action::make('invite')
                            ->label(__('filament-member::default.action.invite'))
                            ->action('create'),
                    ]),
            ]);
    }

    public function create(): void
    {
        $formData = $this->getSchema('form')?->getState() ?? [];
        $emailAddresses = Arr::get($formData, 'emailAddresses', []);
        $tenant = Filament::getTenant();

        if (blank($tenant)) {
            return;
        }

        $count = 0;

        $inviteModel = ConfigHelper::getTenantInviteModel();
        $defaultRole = ConfigHelper::getInviteConfig('default_role', 'member');
        $expirationDays = ConfigHelper::getInviteConfig('expiration_days', 7);

        foreach ($emailAddresses as $item) {
            $emailAddress = $item['email'] ?? null;
            $role = $item['role'] ?? $defaultRole;
            if (blank($emailAddress)) {
                continue;
            }

            if (! filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $existing = $inviteModel::query()
                ->where('tenant_id', $tenant->getKey())
                ->where('email', $emailAddress)
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->exists();

            if ($existing) {
                continue;
            }

            tap(
                $inviteModel::create([
                    'tenant_id' => $tenant->getKey(),
                    'user_id' => auth()->id(),
                    'email' => $emailAddress,
                    'token' => Str::random(32),
                    'role' => $role,
                    'expires_at' => now()->addDays($expirationDays),
                ]),
                fn ($invite) => event(new TenantInviteCreated($invite))
            );

            $count++;
        }

        if ($count > 0) {
            Notification::make()
                ->title($count === 1
                    ? __('filament-member::default.notification.invite_sent_single')
                    : __('filament-member::default.notification.invite_sent_multiple', ['count' => $count]))
                ->success()
                ->send();

            $this->dispatch('$refresh');
        } else {
            Notification::make()
                ->title(__('filament-member::default.notification.no_invites_sent'))
                ->body(__('filament-member::default.notification.all_emails_have_pending'))
                ->warning()
                ->send();
        }

        $this->getSchema('form')?->fill();
    }

    public function getTabs(): array
    {
        $tenant = Filament::getTenant();

        return [
            'members' => Tab::make(__('filament-member::default.tab.members')),
            'pending-invitations' => Tab::make(__('filament-member::default.tab.pending_invitations'))
                ->badge(ConfigHelper::getTenantInviteModel()::where('tenant_id', $tenant?->getKey())->whereNull('accepted_at')->count()),
        ];
    }

    public function updatedActiveTab(): void
    {
    }
}
