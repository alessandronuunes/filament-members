<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Listeners;

use AlessandroNuunes\FilamentMember\Events\TenantInviteCreated;
use AlessandroNuunes\FilamentMember\Mail\InviteTenantMember;
use AlessandroNuunes\FilamentMember\Support\ConfigHelper;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendTenantInviteNotification
{
    public function handle(TenantInviteCreated $event): void
    {
        $invite = $event->invite;

        $existingUser = ConfigHelper::getUserModel()::query()
            ->where('email', $invite->email)
            ->first();

        if (filled($existingUser)) {
            $inviterName = $invite->user?->name ?? __('filament-member::default.message.someone');
            $tenantName = $invite->tenant?->name ?? __('filament-member::default.message.organization_name');

            $routeName = ConfigHelper::getRoute('invite_accept_name');

            Notification::make()
                ->title(__('filament-member::default.notification.invite_title', ['tenant' => $tenantName]))
                ->body(__('filament-member::default.notification.invite_body', ['inviter' => $inviterName, 'tenant' => $tenantName]))
                ->actions([
                    Action::make('join')
                        ->label(__('filament-member::default.action.accept_invite_button'))
                        ->button()
                        ->url(URL::signedRoute('filament.admin.'.$routeName, ['token' => $invite->token]))
                        ->markAsRead(),
                ])
                ->sendToDatabase($existingUser);
        }

        if (ConfigHelper::getNotificationConfig('send_invite_email', true)) {
            Mail::to($invite->email)
                ->send(new InviteTenantMember($invite));
        }
    }
}
