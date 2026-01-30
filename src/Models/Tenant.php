<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Models;

use AlessandroNuunes\FilamentMember\Support\ConfigHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'status',
        'invitation_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ConfigHelper::getUserModel());
    }

    public function users(): BelongsToMany
    {
        $userModel = ConfigHelper::getUserModel();
        $pivotTable = ConfigHelper::getTable('tenant_user');
        $roleColumn = ConfigHelper::getRelationshipColumn('tenant_user_role_column');

        return $this->belongsToMany($userModel, $pivotTable)
            ->withPivot($roleColumn)
            ->withTimestamps();
    }

    public function tenantInvites(): HasMany
    {
        return $this->hasMany(ConfigHelper::getTenantInviteModel());
    }
}
