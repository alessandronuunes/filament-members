<?php

declare(strict_types=1);

namespace AlessandroNuunes\FilamentMember\Mail;

use AlessandroNuunes\FilamentMember\Support\ConfigHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class InviteTenantMember extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly object $invite
    ) {
    }

    public function envelope(): Envelope
    {
        $tenantName = $this->invite->tenant?->name ?? __('filament-member::default.message.organization_name');
        $fromAddress = ConfigHelper::getNotificationConfig('invite_email_from_address', config('mail.from.address'))
            ?? config('mail.from.address')
            ?? 'noreply@example.com';
        $fromName = ConfigHelper::getNotificationConfig('invite_email_from_name', config('mail.from.name'))
            ?? config('mail.from.name')
            ?? '';

        return new Envelope(
            from: new Address($fromAddress, (string) $fromName),
            subject: __('filament-member::default.mail.invite_subject', [
                'tenant' => $tenantName,
                'app' => config('app.name'),
            ]),
        );
    }

    public function content(): Content
    {
        $routeName = ConfigHelper::getRoute('invite_accept_name');
        $acceptUrl = URL::signedRoute('filament.admin.'.$routeName, ['token' => $this->invite->token]);

        $inviterName = $this->invite->user?->name ?? __('filament-member::default.message.someone');
        $tenantName = $this->invite->tenant?->name ?? __('filament-member::default.message.organization_name');
        $enumClass = ConfigHelper::getTenantRoleEnum();
        $roleLabel = ($this->invite->role instanceof $enumClass && method_exists($this->invite->role, 'getLabel'))
            ? $this->invite->role->getLabel()
            : __('filament-member::default.message.member');

        $viewPath = ConfigHelper::getView('mail', 'tenant_invitation');

        return new Content(
            markdown: $viewPath,
            with: [
                'inviterName' => $inviterName,
                'tenantName' => $tenantName,
                'url' => $acceptUrl,
                'role' => $roleLabel,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
