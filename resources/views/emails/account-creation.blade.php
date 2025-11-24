<x-mail::message>


{{ __('emails.account_creation.greeting', ['name' => $user->name]) }}

{{ __('emails.account_creation.intro') }}

## {{ __('emails.account_creation.credentials_title') }}
- **{{ __('emails.account_creation.email') }}** : {{ $user->email }}
- **{{ __('emails.account_creation.password') }}** : {{ __('emails.account_creation.password_text') }}

<x-mail::button :url="$loginUrl">
{{ __('emails.account_creation.access_button') }}
</x-mail::button>

**{{ __('emails.account_creation.team') }}**  
{{ config('app.name') }}

---
<small style="color: #666;">
{{ __('emails.account_creation.footer') }}
</small>
</x-mail::message>
