<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Here you may specify the Eloquent model class names used by the plugin.
    | You can customize these if your application uses different models for
    | users, tenants, or tenant invites.
    |
    */

    'models' => [
        'user' => App\Models\User::class,
        'tenant' => AlessandroNuunes\FilamentMember\Models\Tenant::class,
        'tenant_invite' => AlessandroNuunes\FilamentMember\Models\TenantInvite::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Enums
    |--------------------------------------------------------------------------
    |
    | Here you may specify the enum class used for tenant member roles. The
    | enum should implement Filament's HasLabel and HasColor contracts
    | for proper display in the admin panel.
    |
    */

    'enums' => [
        'tenant_role' => AlessandroNuunes\FilamentMember\Enums\TenantRole::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Tables
    |--------------------------------------------------------------------------
    |
    | These are the database table names used by the plugin. If you change
    | these values, you will need to publish and modify the migrations
    | accordingly.
    |
    */

    'tables' => [
        'tenants' => 'tenants',
        'tenant_user' => 'tenant_user',
        'tenant_invites' => 'tenant_invites',
    ],

    /*
    |--------------------------------------------------------------------------
    | Relationship Columns
    |--------------------------------------------------------------------------
    |
    | Here you may specify the role column name on the tenant_user pivot
    | table used when attaching members to tenants. tenant_foreign_key is
    | the column name for the tenant/reseller FK in the pivot table (e.g.
    | 'tenant_id' or 'reseller_id' when using Reseller as tenant).
    |
    */

    'relationships' => [
        'tenant_foreign_key' => 'tenant_id',
        'tenant_user_role_column' => 'role',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Tenancy
    |--------------------------------------------------------------------------
    |
    | These options configure Filament's multi-tenancy. The slug_attribute
    | is the tenant field used in URLs; ownership_relationship is the
    | name of the relation from tenant to owner. Set route_prefix to
    | null for /admin/{slug} or a string (e.g. "tenant") for /admin/tenant/{slug}.
    |
    */

    'tenancy' => [
        'slug_attribute' => 'slug',
        'ownership_relationship' => 'user',
        'route_prefix' => 'tenant',
    ],

    /*
    |--------------------------------------------------------------------------
    | Invite Accept Route
    |--------------------------------------------------------------------------
    |
    | Here you may configure the route used when users accept an invitation.
    | The path, route name, and middleware (e.g. "signed" for secure links)
    | can be customized to match your application's routing needs.
    |
    */

    'routes' => [
        'invite_accept_path' => '/invite/{token}/accept',
        'invite_accept_name' => 'invite.accept',
        'invite_accept_middleware' => ['signed'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Views and Pages
    |--------------------------------------------------------------------------
    |
    | Here you may specify the view names for mail templates and the page
    | class names for Filament (e.g. tenant members page, accept invite
    | page). Override these to use your own views or page components.
    |
    */

    'views' => [
        'mail' => [
            'tenant_invitation' => 'filament-member::mail.tenant-invitation',
        ],
        'pages' => [
            'tenant_members' => AlessandroNuunes\FilamentMember\Pages\TenantMembers::class,
            'accept_invite' => AlessandroNuunes\FilamentMember\Pages\AcceptInvite::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Invite Settings
    |--------------------------------------------------------------------------
    |
    | These options control invite behavior: the default role assigned when
    | accepting an invite, how many days the invite link is valid, and
    | whether registration is required before accepting.
    |
    */

    'invites' => [
        'default_role' => 'member',
        'expiration_days' => 7,
        'require_registration' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    |
    | These values are used when creating new tenants. Customize them to
    | match your application's defaults.
    |
    */

    'defaults' => [
        'tenant_status' => 'active',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Here you may configure whether invite emails are sent, which queue
    | to use (null for synchronous sending), and the from address and
    | name used for invite emails.
    |
    */

    'notifications' => [
        'send_invite_email' => true,
        'invite_email_queue' => null,
        'invite_email_from_address' => config('mail.from.address'),
        'invite_email_from_name' => config('mail.from.name'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    |
    | These options control how members are ordered in the list: the priority
    | order of roles for sorting (e.g. owner first, then admin, then member).
    |
    */

    'sorting' => [
        'members_role_priority' => ['owner', 'admin', 'member'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | Here you may enable or disable validation: whether a role is required
    | when sending an invite.
    |
    */

    'validation' => [
        'require_role_on_invite' => true,
    ],

];
