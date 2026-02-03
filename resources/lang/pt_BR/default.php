<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'navigation.group' => '',
    'navigation.label' => 'Membros',

    /*
    |--------------------------------------------------------------------------
    | Page Titles
    |--------------------------------------------------------------------------
    */

    'page.title.members' => 'Membros',
    'page.title.accept_invite' => 'Aceitar Convite',
    'page.heading.invited' => 'Você foi convidado!',
    'page.heading.invalid_invite' => 'Convite Inválido',
    'page.subheading.invalid_invite' => 'Este convite não existe, já foi aceito ou expirou.',
    'page.subheading.generic_invite' => 'Você foi convidado para se juntar à organização :name',
    'page.subheading.individual_invite' => ':name convidou você para se juntar à organização',

    /*
    |--------------------------------------------------------------------------
    | Tenant Registration
    |--------------------------------------------------------------------------
    */

    'tenant_registration.label' => 'Registrar organização',
    'tenant_registration.slug' => 'Slug da URL',

    /*
    |--------------------------------------------------------------------------
    | Tenant Profile (Edit)
    |--------------------------------------------------------------------------
    */

    'tenant_profile.label' => 'Perfil da organização',
    'tenant_profile.notification_saved' => 'Perfil da organização salvo.',

    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nome',
    'column.email' => 'E-mail',
    'column.role' => 'Função',
    'column.joined_at' => 'Entrou em',
    'column.invited_by' => 'Convidado por',
    'column.expires_at' => 'Expira em',
    'column.avatar' => 'Foto',

    /*
    |--------------------------------------------------------------------------
    | Form Labels
    |--------------------------------------------------------------------------
    */

    'label.name' => 'Nome',
    'label.email' => 'Email',
    'label.password' => 'Senha',
    'label.password_confirmation' => 'Confirmar Senha',
    'label.role' => 'Role',
    'label.new_role' => 'Nova função',
    'label.email_address' => 'Endereço de E-mail',

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    'action.invite' => 'Convidar',
    'action.invite_link' => 'Link de convite',
    'action.add_another_member' => 'Adicionar outro membro',
    'action.change_role' => 'Alterar função',
    'action.remove' => 'Remover',
    'action.remove_member' => 'Remover membro',
    'action.resend' => 'Reenviar',
    'action.resend_invite' => 'Reenviar convite',
    'action.cancel_invite' => 'Cancelar convite',
    'action.save' => 'Salvar',
    'action.login' => 'Fazer Login',
    'action.register' => 'Criar Conta',
    'action.submit_register' => 'Criar Conta e Aceitar Convite',
    'action.back' => 'Voltar',
    'action.go_to_login' => 'Ir para Login',
    'action.accept_invite' => 'Aceitar Convite',
    'action.accept_invite_button' => 'Aceitar convite',
    'action.actions' => 'Ações',

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    'notification.invite_copied' => 'Link de convite copiado',
    'notification.invite_sent_single' => 'Convite enviado com sucesso.',
    'notification.invite_sent_multiple' => ':count convites enviados com sucesso.',
    'notification.no_invites_sent' => 'Nenhum convite foi enviado.',
    'notification.all_emails_have_pending' => 'Todos os emails já possuem convites pendentes.',
    'notification.role_changed' => 'Função alterada',
    'notification.role_changed_body' => 'A função de :name foi alterada para :role',
    'notification.member_removed' => 'Membro removido',
    'notification.member_removed_body' => ':name foi removido da organização.',
    'notification.invite_resent' => 'Convite reenviado',
    'notification.invite_resent_body' => 'O convite foi reenviado para :email',
    'notification.invite_accepted' => 'Convite aceito!',
    'notification.invite_accepted_body' => 'Você foi adicionado à organização com sucesso.',
    'notification.already_member' => 'Você já é membro',
    'notification.already_member_body' => 'Você já faz parte desta organização.',
    'notification.different_email' => 'Email diferente',
    'notification.different_email_body' => 'O convite foi enviado para outro email. Faça login com o email correto ou crie uma conta.',
    'notification.email_not_match' => 'Email não corresponde',
    'notification.email_not_match_body' => 'O convite foi enviado para outro email.',
    'notification.invite_title' => 'Convite para :tenant',
    'notification.invite_body' => ':inviter convidou você para se juntar à organização :tenant.',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'message.invite_members_description' => 'Convide membros para o tenant.',
    'message.limit_5_invites' => 'Há um limite de 5 convites por vez.',
    'message.change_role_modal' => 'Alterar a função de :name',
    'message.remove_member_confirm' => 'Tem certeza que deseja remover :name da organização?',
    'message.resend_invite_confirm' => 'Deseja reenviar o convite para :email?',
    'message.invite_sent_to' => 'Convite enviado para',
    'message.role_label' => 'Função',
    'message.organization' => 'Organização',
    'message.to_accept_invite' => 'Para aceitar o convite, faça login ou crie uma conta.',
    'message.invite_valid_7_days' => 'Este link é válido por 7 dias',
    'message.unexpected_invite' => 'Se você não esperava este convite, pode ignorá-lo com segurança.',
    'message.regards' => 'Atenciosamente',
    'message.someone' => 'Alguém',
    'message.member' => 'Membro',
    'message.organization_name' => 'uma organização',

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    'validation.email_placeholder' => 'exemplo@email.com',
    'validation.email_already_pending' => 'Este email já possui um convite pendente.',
    'validation.already_member' => 'Este email já é membro do tenant.',

    /*
    |--------------------------------------------------------------------------
    | Enums
    |--------------------------------------------------------------------------
    */

    'enum.role.owner' => 'Proprietário',
    'enum.role.admin' => 'Administrador',
    'enum.role.member' => 'Membro',

    /*
    |--------------------------------------------------------------------------
    | Tabs
    |--------------------------------------------------------------------------
    */

    'tab.members' => 'Membros',
    'tab.pending_invitations' => 'Convites Pendentes',

    /*
    |--------------------------------------------------------------------------
    | Mail
    |--------------------------------------------------------------------------
    */

    'mail.invite_subject' => 'Convite para :tenant - :app',
    'mail.invite_title' => 'Convite para :tenant',
    'mail.invite_body' => ':inviter convidou você para se juntar à organização :tenant.',
    'mail.invite_button' => 'Clique no botão abaixo para aceitar o convite',
    'mail.invite_info_title' => 'Informações do Convite',
    'mail.invite_invited_by' => 'Convidado por',
    'mail.invite_organization' => 'Organização',
    'mail.invite_role' => 'Função',
    'mail.invite_valid_days' => 'Este link é válido por 7 dias',
    'mail.invite_unexpected' => 'Se você não esperava este convite, pode ignorá-lo com segurança.',
];
