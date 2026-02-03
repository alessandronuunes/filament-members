<?php

declare(strict_types=1);

return [
    // ============================================
    // MODELS
    // ============================================
    // Personalize as classes de models utilizadas pelo plugin
    'models' => [
        'user' => App\Models\User::class,
        'tenant' => AlessandroNuunes\FilamentMember\Models\Tenant::class,
        'tenant_invite' => AlessandroNuunes\FilamentMember\Models\TenantInvite::class,
    ],

    // ============================================
    // ENUMS
    // ============================================
    // Personalize os enums utilizados pelo plugin
    'enums' => [
        'tenant_role' => AlessandroNuunes\FilamentMember\Enums\TenantRole::class,
    ],

    // ============================================
    // TABELAS DO BANCO DE DADOS
    // ============================================
    // Personalize os nomes das tabelas (requer migrações customizadas)
    'tables' => [
        'tenants' => 'tenants',
        'tenant_user' => 'tenant_user',
        'tenant_invites' => 'tenant_invites',
    ],

    // ============================================
    // RELACIONAMENTOS
    // ============================================
    // Personalize os nomes das colunas de relacionamento
    'relationships' => [
        'tenant_owner_column' => 'user_id',
        'tenant_user_role_column' => 'role',
    ],

    // ============================================
    // ROTAS
    // ============================================
    // Personalize as rotas do plugin
    'routes' => [
        'invite_accept_path' => '/invite/{token}/accept',
        'invite_accept_name' => 'invite.accept',
        'invite_accept_middleware' => ['signed'],
    ],

    // ============================================
    // VIEWS E TEMPLATES
    // ============================================
    // Personalize as views e páginas do plugin
    'views' => [
        'mail' => [
            'tenant_invitation' => 'filament-member::mail.tenant-invitation',
        ],
        'pages' => [
            'tenant_members' => AlessandroNuunes\FilamentMember\Pages\TenantMembers::class,
            'accept_invite' => AlessandroNuunes\FilamentMember\Pages\AcceptInvite::class,
        ],
    ],

    // ============================================
    // CONFIGURAÇÕES DE CONVITES
    // ============================================
    // Personalize o comportamento do sistema de convites
    'invites' => [
        'default_role' => 'member',
        'expiration_days' => 7,
        'allow_generic_invites' => true,
        'require_registration' => true,
    ],

    // ============================================
    // CONFIGURAÇÕES DE PERMISSÕES
    // ============================================
    // Personalize o sistema de permissões baseado em papéis
    'permissions' => [
        'roles' => [
            'can_invite_members' => ['owner', 'admin'],
            'can_remove_members' => ['owner', 'admin'],
            'can_change_roles' => ['owner'],
        ],
        'owner_cannot_be_removed' => true,
        'owner_cannot_change_role' => true,
    ],

    // ============================================
    // VALORES PADRÃO
    // ============================================
    // Defina valores padrão para novos registros
    'defaults' => [
        'tenant_status' => 'active',
        'member_role' => 'member',
    ],

    // ============================================
    // CONFIGURAÇÕES DE NOTIFICAÇÕES
    // ============================================
    // Personalize o envio de emails e notificações
    'notifications' => [
        'send_invite_email' => true,
        'invite_email_queue' => null,
        'invite_email_from_address' => config('mail.from.address'),
        'invite_email_from_name' => config('mail.from.name'),
    ],

    // ============================================
    // CONFIGURAÇÕES DE ORDENAÇÃO
    // ============================================
    // Personalize como os membros são ordenados
    'sorting' => [
        'members_default_column' => 'name',
        'members_default_direction' => 'asc',
        'members_role_priority' => ['owner', 'admin', 'member'],
    ],

    // ============================================
    // CONFIGURAÇÕES DE VALIDAÇÃO
    // ============================================
    // Personalize regras de validação
    'validation' => [
        'email_unique_in_tenant' => true,
        'require_role_on_invite' => true,
    ],

    // ============================================
    // CONFIGURAÇÕES DE NAVEGAÇÃO DO FILAMENT
    // ============================================
    // Personalize grupo, ordem, ícone e label da página de membros no menu
    'navigation' => [
        'tenant_members_page' => [
            'group' => null, // null = usa __('filament-member::default.navigation.group')
            'sort' => 2,
            'icon' => 'heroicon-o-users',
            'label' => null, // null = usa __('filament-member::default.navigation.label')
        ],
    ],
];
