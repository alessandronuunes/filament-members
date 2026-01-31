<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Support;

use AlessandroNuunes\FilamentMember\Enums\TenantRole;
use AlessandroNuunes\FilamentMember\Models\Tenant;
use AlessandroNuunes\FilamentMember\Models\TenantInvite;
use App\Models\User;

class ConfigHelper
{
    private const CONFIG_KEY = 'filament-member';

    /**
     * Get a configuration value with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return config(self::CONFIG_KEY.'.'.$key, $default);
    }

    /**
     * Get the User model class.
     */
    public static function getUserModel(): string
    {
        return self::get('models.user', User::class);
    }

    /**
     * Get the Tenant model class.
     */
    public static function getTenantModel(): string
    {
        return self::get('models.tenant', Tenant::class);
    }

    /**
     * Get the TenantInvite model class.
     */
    public static function getTenantInviteModel(): string
    {
        return self::get('models.tenant_invite', TenantInvite::class);
    }

    /**
     * Get the TenantRole enum class.
     */
    public static function getTenantRoleEnum(): string
    {
        return self::get('enums.tenant_role', TenantRole::class);
    }

    /**
     * Get a table name.
     */
    public static function getTable(string $table): string
    {
        return (string) self::get('tables.' . $table, $table);
    }

    /**
     * Get a relationship column name.
     */
    public static function getRelationshipColumn(string $column): string
    {
        return (string) self::get('relationships.' . $column, $column);
    }

    /**
     * Get the tenant foreign key column name (pivot and queries).
     * Use 'tenant_id' or 'reseller_id' when using Reseller as tenant.
     */
    public static function getTenantForeignKeyColumn(): string
    {
        return (string) self::get('relationships.tenant_foreign_key', 'tenant_id');
    }

    /**
     * Get a tenancy configuration.
     */
    public static function getTenancyConfig(string $key, mixed $default = null): mixed
    {
        return self::get('tenancy.' . $key, $default);
    }

    /**
     * Get a route configuration.
     */
    public static function getRoute(string $key): mixed
    {
        return self::get('routes.' . $key);
    }

    /**
     * Get a view path.
     */
    public static function getView(string $type, string $view): string
    {
        return (string) self::get(sprintf('views.%s.%s', $type, $view));
    }

    /**
     * Get an invite configuration.
     */
    public static function getInviteConfig(string $key, mixed $default = null): mixed
    {
        return self::get('invites.' . $key, $default);
    }

    /**
     * Get a default value.
     */
    public static function getDefault(string $key, mixed $default = null): mixed
    {
        return self::get('defaults.' . $key, $default);
    }

    /**
     * Get a notification configuration.
     */
    public static function getNotificationConfig(string $key, mixed $default = null): mixed
    {
        return self::get('notifications.' . $key, $default);
    }

    /**
     * Get a sorting configuration.
     */
    public static function getSortingConfig(string $key, mixed $default = null): mixed
    {
        return self::get('sorting.' . $key, $default);
    }

    /**
     * Get a validation configuration.
     */
    public static function getValidationConfig(string $key, mixed $default = null): mixed
    {
        return self::get('validation.' . $key, $default);
    }
}
