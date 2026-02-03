<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'navigation.group' => '',
    'navigation.label' => 'Members',

    /*
    |--------------------------------------------------------------------------
    | Page Titles
    |--------------------------------------------------------------------------
    */

    'page.title.members' => 'Members',
    'page.title.accept_invite' => 'Accept Invite',
    'page.heading.invited' => 'You have been invited!',
    'page.heading.invalid_invite' => 'Invalid Invite',
    'page.subheading.invalid_invite' => 'This invite does not exist, has already been accepted, or has expired.',
    'page.subheading.generic_invite' => 'You have been invited to join the organization :name',
    'page.subheading.individual_invite' => ':name invited you to join the organization',

    /*
    |--------------------------------------------------------------------------
    | Tenant Registration
    |--------------------------------------------------------------------------
    */

    'tenant_registration.label' => 'Register organization',
    'tenant_registration.slug' => 'Slug',

    /*
    |--------------------------------------------------------------------------
    | Tenant Profile (Edit)
    |--------------------------------------------------------------------------
    */

    'tenant_profile.label' => 'Organization profile',
    'tenant_profile.notification_saved' => 'Organization profile saved.',

    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Name',
    'column.email' => 'Email',
    'column.role' => 'Role',
    'column.joined_at' => 'Joined on',
    'column.invited_by' => 'Invited by',
    'column.expires_at' => 'Expires at',
    'column.avatar' => 'Photo',

    /*
    |--------------------------------------------------------------------------
    | Form Labels
    |--------------------------------------------------------------------------
    */

    'label.name' => 'Name',
    'label.email' => 'Email',
    'label.password' => 'Password',
    'label.password_confirmation' => 'Confirm Password',
    'label.role' => 'Role',
    'label.new_role' => 'New Role',
    'label.email_address' => 'Email Address',

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    'action.invite' => 'Invite',
    'action.invite_link' => 'Invite Link',
    'action.add_another_member' => 'Add another member',
    'action.change_role' => 'Change Role',
    'action.remove' => 'Remove',
    'action.remove_member' => 'Remove Member',
    'action.resend' => 'Resend',
    'action.resend_invite' => 'Resend Invite',
    'action.cancel_invite' => 'Cancel Invite',
    'action.save' => 'Save',
    'action.login' => 'Login',
    'action.register' => 'Create Account',
    'action.submit_register' => 'Create Account and Accept Invite',
    'action.back' => 'Back',
    'action.go_to_login' => 'Go to Login',
    'action.accept_invite' => 'Accept Invite',
    'action.accept_invite_button' => 'Accept invite',
    'action.actions' => 'Actions',

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    'notification.invite_copied' => 'Invite link copied',
    'notification.invite_sent_single' => 'Invite sent successfully.',
    'notification.invite_sent_multiple' => ':count invites sent successfully.',
    'notification.no_invites_sent' => 'No invites were sent.',
    'notification.all_emails_have_pending' => 'All emails already have pending invites.',
    'notification.role_changed' => 'Role changed',
    'notification.role_changed_body' => 'The role of :name has been changed to :role',
    'notification.member_removed' => 'Member removed',
    'notification.member_removed_body' => ':name has been removed from the organization.',
    'notification.invite_resent' => 'Invite resent',
    'notification.invite_resent_body' => 'The invite has been resent to :email',
    'notification.invite_accepted' => 'Invite accepted!',
    'notification.invite_accepted_body' => 'You have been successfully added to the organization.',
    'notification.already_member' => 'You are already a member',
    'notification.already_member_body' => 'You are already part of this organization.',
    'notification.different_email' => 'Different email',
    'notification.different_email_body' => 'The invite was sent to another email. Please login with the correct email or create an account.',
    'notification.email_not_match' => 'Email does not match',
    'notification.email_not_match_body' => 'The invite was sent to another email.',
    'notification.invite_title' => 'Invite to :tenant',
    'notification.invite_body' => ':inviter invited you to join the organization :tenant.',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'message.invite_members_description' => 'Invite members to the tenant.',
    'message.limit_5_invites' => 'There is a limit of 5 invites at a time.',
    'message.change_role_modal' => 'Change the role of :name',
    'message.remove_member_confirm' => 'Are you sure you want to remove :name from the organization?',
    'message.resend_invite_confirm' => 'Do you want to resend the invite to :email?',
    'message.invite_sent_to' => 'Invite sent to',
    'message.role_label' => 'Role',
    'message.organization' => 'Organization',
    'message.to_accept_invite' => 'To accept the invite, please login or create an account.',
    'message.invite_valid_7_days' => 'This link is valid for 7 days',
    'message.unexpected_invite' => 'If you did not expect this invite, you can safely ignore it.',
    'message.regards' => 'Regards',
    'message.someone' => 'Someone',
    'message.member' => 'Member',
    'message.organization_name' => 'an organization',

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    'validation.email_placeholder' => 'example@email.com',
    'validation.email_already_pending' => 'This email already has a pending invite.',
    'validation.already_member' => 'This email is already a member of the tenant.',

    /*
    |--------------------------------------------------------------------------
    | Enums
    |--------------------------------------------------------------------------
    */

    'enum.role.owner' => 'Owner',
    'enum.role.admin' => 'Administrator',
    'enum.role.member' => 'Member',

    /*
    |--------------------------------------------------------------------------
    | Tabs
    |--------------------------------------------------------------------------
    */

    'tab.members' => 'Members',
    'tab.pending_invitations' => 'Pending Invitations',

    /*
    |--------------------------------------------------------------------------
    | Mail
    |--------------------------------------------------------------------------
    */

    'mail.invite_subject' => 'Invite to :tenant - :app',
    'mail.invite_title' => 'Invite to :tenant',
    'mail.invite_body' => ':inviter invited you to join the organization :tenant.',
    'mail.invite_button' => 'Click the button below to accept the invite',
    'mail.invite_info_title' => 'Invite Information',
    'mail.invite_invited_by' => 'Invited by',
    'mail.invite_organization' => 'Organization',
    'mail.invite_role' => 'Role',
    'mail.invite_valid_days' => 'This link is valid for 7 days',
    'mail.invite_unexpected' => 'If you did not expect this invite, you can safely ignore it.',
];
