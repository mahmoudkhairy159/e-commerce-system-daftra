@component('mail::message')
# {{ __('user::app.auth.otp.otp_email_subject') }}



{{ __('user::app.auth.otp.your_otp_code_is', ['otp' => $otpCode]) }}

{{ __('user::app.auth.otp.otp_code_valid_for_x_minutes', ['minutes' => $minutes]) }}

@component('mail::button', ['url' => config('app.url')])
{{ __('user::app.auth.otp.visit_website') }}
@endcomponent

Thanks,<br>
{{ $websiteName }}
@endcomponent
