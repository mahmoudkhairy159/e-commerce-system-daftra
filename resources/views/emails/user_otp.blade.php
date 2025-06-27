@component('mail::message')
# {{ __('user::app.auth.otp.otp_email_subject') }}



{{-- Greeting Section --}}
<p style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 10px; color: #4a4a4a;">
    {{ __('user::app.auth.otp.greeting_message') }}
</p>

{{-- OTP Code Section --}}
<div style="text-align: center; background-color: #f6f6f6; padding: 20px; border-radius: 10px; margin: 20px 0;">
    <p style="font-size: 32px; font-weight: bold; color: #2c3e50; margin: 0;">
        {{ __('user::app.auth.otp.your_otp_code_is', ['otp' => $otpCode]) }}
    </p>
</div>

{{-- Validity Section --}}
<p style="text-align: center; font-size: 16px; color: #7f8c8d; margin-bottom: 20px;">
    {{ __('user::app.auth.otp.otp_code_valid_for_x_minutes', ['minutes' => $minutes]) }}
</p>


{{-- Ignorance Message and Footer Section --}}
<p style="text-align: center; font-size: 14px; color: #7f8c8d; margin-top: 20px;">
    {{ __('user::app.auth.otp.ignore_message') }}
</p>

<p style="text-align: center; font-size: 14px; color: #7f8c8d; margin-top: 10px;">
    {{ __('user::app.auth.otp.footer_message', ['website' => $websiteName]) }}
</p>

Thanks,<br>
{{ $websiteName }}
@endcomponent
