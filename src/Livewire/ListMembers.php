<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Livewire;

use AlessandroNuunes\FilamentMember\Support\ConfigHelper;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;
use Illuminate\Contracts\View\View;

class ListMembers extends TableComponent
{
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

        return collect($enumClass::cases())
            ->mapWithKeys(fn ($case): array => [
                $case->value => method_exists($case, 'getLabel') ? $case->getLabel() : $case->name,
            ])
            ->all();
    }

    protected function buildRoleOrderBy(string $pivotTable, string $roleColumn, array $priority): string
    {
        $cases = collect(array_keys($priority))
            ->map(fn (int $index): string => 'WHEN ? THEN '.($index + 1))
            ->implode("\n");

        return "CASE {$pivotTable}.{$roleColumn}\n{$cases}\nELSE ".(count($priority) + 1)."\nEND";
    }

    public function table(Table $table): Table
    {
        $tenant = Filament::getTenant();
        $currentUserId = auth()->id();

        $userModel = ConfigHelper::getUserModel();
        $pivotTable = ConfigHelper::getTable('tenant_user');
        $tenantFkColumn = ConfigHelper::getTenantForeignKeyColumn();
        $roleColumn = ConfigHelper::getRelationshipColumn('tenant_user_role_column');
        $rolePriority = ConfigHelper::getSortingConfig('members_role_priority', ['owner', 'admin', 'member']);

        $query = $tenant
            ? $userModel::query()
                ->withoutGlobalScopes()
                ->join($pivotTable, 'users.id', '=', $pivotTable . '.user_id')
                ->where($pivotTable . '.' . $tenantFkColumn, $tenant->getKey())
                ->select('users.*', sprintf('%s.%s as pivot_role', $pivotTable, $roleColumn), $pivotTable . '.created_at as joined_at')
                ->orderByRaw($this->buildRoleOrderBy($pivotTable, $roleColumn, $rolePriority), $rolePriority)
                ->orderBy('users.name')
            : $userModel::query()->whereRaw('1 = 0');

        $enumClass = $this->getTenantRoleEnum();
        $ownerValue = enum_exists($enumClass) && defined($enumClass.'::Owner')
            ? $enumClass::Owner->value
            : 'owner';

        return $table
            ->query($query)
            ->reorderableColumns(false)
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->label(__('filament-member::default.column.avatar'))
                        ->circular()
                        ->defaultImageUrl(fn ($record) => method_exists($record, 'getFilamentAvatarUrl') ? $record->getFilamentAvatarUrl() : null)
                        ->grow(false),
                    Stack::make([
                        TextColumn::make('name')
                            ->label(__('filament-member::default.column.name'))
                            ->weight(FontWeight::Medium)
                            ->searchable()
                            ->sortable(),
                        TextColumn::make('email')
                            ->label(__('filament-member::default.column.email'))
                            ->weight(FontWeight::Light)
                            ->color('gray')
                            ->searchable(),
                    ])
                        ->grow(),
                    Stack::make([
                        TextColumn::make('pivot_role')
                            ->label(__('filament-member::default.column.role'))
                            ->badge()
                            ->formatStateUsing(function (?string $state) use ($enumClass) {
                                if (blank($state)) {
                                    return '-';
                                }

                                if (! enum_exists($enumClass)) {
                                    return $state;
                                }

                                $case = $enumClass::tryFrom($state);

                                return $case && method_exists($case, 'getLabel')
                                    ? $case->getLabel()
                                    : $state;
                            })
                            ->color(function (?string $state) use ($enumClass) {
                                if (blank($state) || ! enum_exists($enumClass)) {
                                    return 'gray';
                                }

                                $case = $enumClass::tryFrom($state);

                                return $case && method_exists($case, 'getColor')
                                    ? $case->getColor()
                                    : 'gray';
                            }),
                        TextColumn::make('joined_at')
                            ->label(__('filament-member::default.column.joined_at'))
                            ->date('M d, Y')
                            ->color('gray')
                            ->size('sm'),
                    ])
                        ->grow(false)
                        ->alignment(Alignment::End)
                        ->extraAttributes(['class' => 'min-w-28']),
                ]),
            ])
            ->filters([
                SelectFilter::make('pivot_role')
                    ->label(__('filament-member::default.column.role'))
                    ->options($this->getTenantRoleOptions())
                    ->query(function ($query, array $data) use ($pivotTable, $roleColumn) {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->where(sprintf('%s.%s', $pivotTable, $roleColumn), $data['value']);
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('changeRole')
                        ->label(__('filament-member::default.action.change_role'))
                        ->icon('heroicon-o-user-circle')
                        ->modalHeading(fn ($record): string|array|null => __('filament-member::default.message.change_role_modal', ['name' => $record->name]))
                        ->modalSubmitActionLabel(__('filament-member::default.action.save'))
                        ->schema([
                            Select::make('role')
                                ->label(__('filament-member::default.label.new_role'))
                                ->options($this->getTenantRoleOptions())
                                ->required()
                                ->default(fn ($record) => $record->pivot_role),
                        ])
                        ->modalWidth(Width::Small)
                        ->action(function ($record, array $data) use ($enumClass): void {
                            $tenant = Filament::getTenant();
                            $roleColumn = ConfigHelper::getRelationshipColumn('tenant_user_role_column');

                            $tenant?->users()->updateExistingPivot($record->id, [
                                $roleColumn => $data['role'],
                            ]);

                            $roleLabel = __('filament-member::default.message.member');
                            if (enum_exists($enumClass)) {
                                $case = $enumClass::tryFrom($data['role']);
                                $roleLabel = $case && method_exists($case, 'getLabel')
                                    ? $case->getLabel()
                                    : __('filament-member::default.message.member');
                            }

                            Notification::make()
                                ->title(__('filament-member::default.notification.role_changed'))
                                ->body(__('filament-member::default.notification.role_changed_body', [
                                    'name' => $record->name,
                                    'role' => $roleLabel,
                                ]))
                                ->success()
                                ->send();
                        }),
                    Action::make('remove')
                        ->label(__('filament-member::default.action.remove'))
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('filament-member::default.action.remove_member'))
                        ->modalDescription(fn ($record): string|array|null => __('filament-member::default.message.remove_member_confirm', ['name' => $record->name]))
                        ->action(function ($record): void {
                            $tenant = Filament::getTenant();
                            $tenant?->users()->detach($record->id);

                            Notification::make()
                                ->title(__('filament-member::default.notification.member_removed'))
                                ->body(__('filament-member::default.notification.member_removed_body', ['name' => $record->name]))
                                ->success()
                                ->send();
                        }),
                ])
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->tooltip(__('filament-member::default.action.actions'))
                    ->visible(
                        fn ($record): bool => $record->id !== $currentUserId &&
                        $record->pivot_role !== $ownerValue
                    ),
            ]);
    }

    public function render(): View
    {
        return view('filament-member::livewire.tenant.list-members');
    }
}
