<?php

namespace Modules\User\Traits;

use Modules\User\Notifications\SendUserOtpNotification;
use Modules\User\Notifications\SendUserResetPasswordOtpNotification;
use Modules\User\Repositories\UserOTPRepository;

trait UserOtpTrait
{


    private static function generateOtp($length = 6)
    {
        return rand(pow(10, $length - 1), pow(10, $length) - 1);
    }

    private static function generateOtpExpiryDateTime($length = 5)
    {
        return now()->addMinutes($length);
    }



    public  function sendOtpCode($user)
    {

        $otpCode = $this::generateOtp();
        $expiryDateTime = $this::generateOtpExpiryDateTime();

        app(UserOTPRepository::class)->storeOtp($user->id, $otpCode, $expiryDateTime);

        $user->notify(new SendUserOtpNotification($otpCode));

        return $otpCode;
    }
    public  function sendResetPasswordOtpCode($user)
    {

        $otpCode = $this::generateOtp();
        $expiryDateTime = $this::generateOtpExpiryDateTime();

        app(UserOTPRepository::class)->storeOtp($user->id, $otpCode, $expiryDateTime);

        $user->notify(new SendUserResetPasswordOtpNotification($otpCode));

        return $otpCode;
    }


    public  function isValidOtpCode($user, $otpCode)
    {

        $otp = app(UserOTPRepository::class)->getByUserId($user->id);
        if ($otp && $otp->otp == $otpCode) {
            return true;
        }
        return false;
    }
    public function checkOtpCodeExpirationByUserId($userId)
    {
        return app(UserOTPRepository::class)->CheckOtpCodeExpirationByUserId($userId);
    }


    public  function resendOtpCode($user)
    {

        if (app(UserOTPRepository::class)->getByUserId($user->id)) {
            return false;
        }
        $otpCode = $this::generateOtp();
        $expiryDateTime = $this::generateOtpExpiryDateTime();

        app(UserOTPRepository::class)->storeOtp($user->id, $otpCode, $expiryDateTime);

        $user->notify(new SendUserOtpNotification($otpCode));

        return true;
    }
    public  function resendResetPasswordOtpCode($user)
    {

        if (app(UserOTPRepository::class)->getByUserId($user->id)) {
            return false;
        }
        $otpCode = $this::generateOtp();
        $expiryDateTime = $this::generateOtpExpiryDateTime();

        app(UserOTPRepository::class)->storeOtp($user->id, $otpCode, $expiryDateTime);

        $user->notify(new SendUserResetPasswordOtpNotification($otpCode));

        return true;
    }
}
