<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Traits;

use AlessandroNuunes\FilamentMember\Support\ConfigHelper;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

trait HasTenantRelations
{
    /**
     * Get the tenant model class name.
     * Can be overridden in the model if a different tenant class is used.
     */
    protected function getTenantModelClass(): string
    {
        return ConfigHelper::getTenantModel();
    }

    /**
     * Get all tenants that the user can access.
     */
    public function getTenants(Panel $panel): Collection
    {
        return $this->tenants->merge($this->memberTenants)->unique('id')->values();
    }

    /**
     * Check if the user can access a specific tenant.
     */
    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->tenants()->whereKey($tenant)->exists()) {
            return true;
        }

        return $this->memberTenants()->whereKey($tenant)->exists();
    }

    /**
     * Get tenants owned by the user.
     */
    public function tenants(): HasMany
    {
        return $this->hasMany($this->getTenantModelClass());
    }

    /**
     * Get tenants where the user is a member.
     */
    public function memberTenants(): BelongsToMany
    {
        $tenantModel = ConfigHelper::getTenantModel();
        $pivotTable = ConfigHelper::getTable('tenant_user');
        $tenantFkColumn = ConfigHelper::getTenantForeignKeyColumn();
        $roleColumn = ConfigHelper::getRelationshipColumn('tenant_user_role_column');

        return $this->belongsToMany($tenantModel, $pivotTable, 'user_id', $tenantFkColumn)
            ->withPivot($roleColumn);
    }
}
