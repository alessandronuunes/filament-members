<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Support;

use App\Models\User;
use AlessandroNuunes\FilamentMember\Models\Tenant;
use AlessandroNuunes\FilamentMember\Enums\TenantRole;
use AlessandroNuunes\FilamentMember\Models\TenantInvite;

class ConfigHelper
{
    /**
     * Get a configuration value with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return config('filament-member.' . $key, $default);
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
        return self::get('tables.' . $table, $table);
    }

    /**
     * Get a relationship column name.
     */
    public static function getRelationshipColumn(string $column): string
    {
        return self::get('relationships.' . $column, $column);
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
        return self::get(sprintf('views.%s.%s', $type, $view));
    }

    /**
     * Get an invite configuration.
     */
    public static function getInviteConfig(string $key, mixed $default = null): mixed
    {
        return self::get('invites.' . $key, $default);
    }

    /**
     * Get a permission configuration.
     */
    public static function getPermissionConfig(string $key, mixed $default = null): mixed
    {
        return self::get('permissions.' . $key, $default);
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

    /**
     * Get a navigation configuration value.
     */
    public static function getNavigationConfig(string $pageKey, string $key, mixed $default = null): mixed
    {
        return self::get("navigation.{$pageKey}.{$key}", $default);
    }
}
