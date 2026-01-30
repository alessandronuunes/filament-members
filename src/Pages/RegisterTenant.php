<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Pages;

use AlessandroNuunes\FilamentMember\Support\ConfigHelper;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant as BaseRegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class RegisterTenant extends BaseRegisterTenant
{
    public static function getLabel(): string
    {
        return __('filament-member::default.tenant_registration.label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filament-member::default.label.name'))
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('slug', ($state ?? '').toString().toLowerCase().trim().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '').replace(/-+/g, '-').replace(/^-|-$/g, ''))
                    JS),

                TextInput::make('slug')
                    ->label(__('filament-member::default.tenant_registration.slug'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ConfigHelper::getTenantModel(), 'slug')
                    ->rules(['alpha_dash']),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $user = auth()->user();
        $tenantModel = ConfigHelper::getTenantModel();
        $roleColumn = ConfigHelper::getRelationshipColumn('tenant_user_role_column');

        $tenant = $tenantModel::create(array_merge(
            Arr::only($data, ['name', 'slug']),
            [
                'user_id' => $user->getKey(),
                'status' => ConfigHelper::getDefault('tenant_status', 'active'),
            ]
        ));

        $tenant->users()->attach($user->getKey(), [$roleColumn => 'owner']);

        return $tenant;
    }
}
