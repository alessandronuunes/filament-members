<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Pages;

use AlessandroNuunes\FilamentMember\Support\ConfigHelper;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;
use Filament\Schemas\Schema;

class EditTenant extends BaseEditTenantProfile
{
    public static function getLabel(): string
    {
        return __('filament-member::default.tenant_profile.label');
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
                    ->unique(ConfigHelper::getTenantModel(), 'slug', ignoreRecord: true)
                    ->rules(['alpha_dash']),
            ]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('filament-member::default.tenant_profile.notification_saved');
    }
}
