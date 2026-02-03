<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Listeners;

use BackedEnum;
use AlessandroNuunes\FilamentMember\Models\TenantInvite;
use AlessandroNuunes\FilamentMember\Support\ConfigHelper;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\Login;

class AcceptPendingInviteAfterLogin
{
    public function handle(Login $event): void
    {
        $token = session('invite_token');

        if (blank($token)) {
            return;
        }

        $invite = TenantInvite::query()
            ->with('tenant')
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if (blank($invite)) {
            session()->forget('invite_token');

            return;
        }

        $user = $event->user;

        if ($user->email !== $invite->email) {
            Notification::make()
                ->title(__('filament-member::default.notification.email_not_match'))
                ->body(__('filament-member::default.notification.email_not_match_body'))
                ->warning()
                ->send();

            session()->forget('invite_token');

            return;
        }

        $roleColumn = ConfigHelper::getRelationshipColumn('tenant_user_role_column');
        $roleValue = $invite->role instanceof BackedEnum ? $invite->role->value : $invite->role;

        $invite->tenant->users()->syncWithoutDetaching([
            $user->getKey() => [$roleColumn => $roleValue],
        ]);

        $invite->update(['accepted_at' => now()]);

        session()->forget('invite_token');

        session(['filament_redirect_after_login' => Filament::getPanel('admin')->getUrl($invite->tenant)]);

        Notification::make()
            ->title(__('filament-member::default.notification.invite_accepted'))
            ->body(__('filament-member::default.notification.invite_accepted_body'))
            ->success()
            ->send();
    }
}
