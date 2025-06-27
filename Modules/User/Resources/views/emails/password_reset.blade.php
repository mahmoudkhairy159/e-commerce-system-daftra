@component('mail::message')
# {{ __('user::app.auth.password_reset.subject') }}


{{ __('user::app.auth.password_reset.intro_line') }}

@component('mail::button', ['url' => $resetUrl])
{{ __('user::app.auth.password_reset.button_text') }}
@endcomponent

{{ __('user::app.auth.password_reset.outro_line') }}

@component('mail::panel')
{{ __('user::app.auth.password_reset.trouble_text', ['url' => $resetUrl]) }}
@endcomponent

Thanks,<br>
{{ $websiteName }}
@endcomponent
