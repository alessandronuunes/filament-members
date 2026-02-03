<x-mail::message>
# {{ __('filament-member::default.mail.invite_title', ['tenant' => $tenantName]) }}

{{ __('filament-member::default.mail.invite_body', ['inviter' => $inviterName, 'tenant' => $tenantName]) }}.

{{ __('filament-member::default.mail.invite_button') }}:

<x-mail::button :url="$url">
{{ __('filament-member::default.action.accept_invite') }}
</x-mail::button>

## {{ __('filament-member::default.mail.invite_info_title') }}

- {{ __('filament-member::default.mail.invite_invited_by') }}: <strong>{{ $inviterName }}</strong>
- {{ __('filament-member::default.mail.invite_organization') }}: <strong>{{ $tenantName }}</strong>
- {{ __('filament-member::default.mail.invite_role') }}: <strong>{{ $role }}</strong>
- {{ __('filament-member::default.mail.invite_valid_days') }}

---

{{ __('filament-member::default.mail.invite_unexpected') }}

{{ __('filament-member::default.message.regards') }},  
{{ config('app.name') }}
</x-mail::message>
