<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember;

use AlessandroNuunes\FilamentMember\Pages\EditTenant;
use AlessandroNuunes\FilamentMember\Pages\RegisterTenant;
use AlessandroNuunes\FilamentMember\Support\ConfigHelper;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class MemberPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-member';
    }

    public function register(Panel $panel): void
    {
        $tenantModel = ConfigHelper::getTenantModel();
        $slugAttribute = ConfigHelper::getTenancyConfig('slug_attribute', 'slug');
        $ownershipRelationship = ConfigHelper::getTenancyConfig('ownership_relationship', 'user');
        $tenantRoutePrefix = ConfigHelper::getTenancyConfig('route_prefix');

        $panel->tenant($tenantModel, $slugAttribute, $ownershipRelationship);
        $panel->tenantRegistration(RegisterTenant::class);
        $panel->tenantProfile(EditTenant::class);

        if (filled($tenantRoutePrefix)) {
            $panel->tenantRoutePrefix($tenantRoutePrefix);
        }

        $tenantMembersPage = ConfigHelper::getView('pages', 'tenant_members');
        $acceptInvitePage = ConfigHelper::getView('pages', 'accept_invite');

        $panel
            ->pages([$tenantMembersPage])
            ->routes(function () use ($acceptInvitePage): void {
                $path = ConfigHelper::getRoute('invite_accept_path');
                $name = ConfigHelper::getRoute('invite_accept_name');
                $middleware = Arr::wrap(ConfigHelper::getRoute('invite_accept_middleware'));

                Route::get($path, $acceptInvitePage)
                    ->middleware($middleware)
                    ->name($name);
            });
    }

    public function boot(Panel $panel): void
    {
    }

    public static function make(): self
    {
        return new self();
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(resolve(static::class)->getId());

        return $plugin;
    }
}
