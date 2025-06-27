<?php

return [
    "users" => [
        "users" => "Users",
        "user" => "User",
        "show" => "Show Users",
        "create" => "create a User",
        "update" => "update a User",
        "delete" => "delete a User",
        "destroy" => "destroy a User",
        "created-successfully" => "User created successfully",
        "updated-successfully" => "User updated successfully",
        "deleted-successfully" => "User deleted successfully",
        "followed-successfully" => "User followed successfully",
        "unFollowed-successfully" => "User unFollowed successfully",
        "created-failed" => "User created failed",
        "updated-failed" => "User updated failed",
        "deleted-failed" => "User deleted failed",
        "followed-failed" => "User followed failed",
        "unFollowed-failed" => "User unFollowed failed",
        "current-password-incorrect" => "User current password incorrect",
        "already-banned" => "User already banned",
        "already-unbanned" => "User already unbanned",
        "banned-successfully" => "User banned successfully",
        "banned-failed" => "User banned failed",
        "unbanned-successfully" => "User unbanned successfully",
        "unbanned-failed" => "User unbanned failed",
    ],
    "userProfiles" => [
        "userProfiles" => "User Profiles",
        "UserProfile" => "User Profile",
        "show" => "Show User Profiles",
        "create" => "create a User Profile",
        "update" => "update a User Profile",
        "delete" => "delete a User Profile",
        "destroy" => "destroy a User Profile",
        "created-successfully" => "User Profile created successfully",
        "updated-successfully" => "User Profile updated successfully",
        "deleted-successfully" => "User Profile deleted successfully",
        "created-failed" => "User Profile created failed",
        "updated-failed" => "User Profile updated failed",
        "deleted-failed" => "User Profile deleted failed",
    ],
    "userAddresses" => [
        "userAddresses" => "User Addresses",
        "userAddress" => "User Address",
        "show" => "Show User Addresses",
        "create" => "create a User Address",
        "update" => "update a User Address",
        "delete" => "delete a User Address",
        "destroy" => "destroy a User Address",
        "created-successfully" => "User Address created successfully",
        "updated-successfully" => "User Address updated successfully",
        "deleted-successfully" => "User Address deleted successfully",
        "created-failed" => "User Address created failed",
        "updated-failed" => "User Address updated failed",
        "deleted-failed" => "User Address deleted failed",
        "set-default-address-successfully" => "User Address set as default successfully",
        "set-default-address-failed" => "User Address set as default failed",
    ],

    'auth' => [

        'otp' => [

            'your_otp_code_is' => 'Your OTP Code Is : :otp',
            'otp_code_valid_for_x_minutes' => 'This OTP Code  is valid for :minutes minutes.',
            'otp_email_subject' => 'Daftra OTP Code',
            'ignore_message' => 'If you did not request this code, please ignore this email. No further action is required.',
            'greeting_message' => 'Hello, ! You requested an OTP code for verification.',
            'footer_message' => 'This email was sent to you by :website for verification purposes. If you have any questions, please contact support.',
            'visit_website'=> 'Visit Website'


        ],
        'register' => [
            'success_register_message' => 'Registration successful, And The Verification code sent via email. '
        ],
        'login' => [
            'invalid_email_or_password' => 'Invalid Email or Password',
            'your_account_is_blocked' => 'Your Account is blocked',
            'your_account_is_inactive' => 'Your Account is inactive',
            'logged_in_successfully' => 'Logged In Successfully',
            'logged_in_successfully_and_Verification_code_sent' => 'Logged In Successfully And The Verification code sent via email. ',
            'logged_in_successfully_and_Verification_code_already_sent' => 'Logged In Successfully And The Verification code already sent via email. ',

        ],
        'verification' => [
            'invalid_otp' => 'Invalid otp',
            'valid_otp' => 'Valid otp',
            'verification_failed' => 'Verification Failed',
            'already_verified' => 'Already Verified',
            'verified_successfully' => 'Verification Successfully',
            'cant_resend_verification_otp_code' => 'Cant Resend Verification OTP Code',
            'verification_otp_code_resend_successfully' => 'Verification OTP Code Resend SuccessFully',
            'already_sent_verification_otp_code' => 'verification otp code is already sent to your email'
        ],
        'forgotPassword' => [
            'user_not_found' => 'User not found with this email address.',
            'otp_code_email_sent_successfully' => 'OTP code for reset password sent successfully',
        ],
        'resetPassword' => [
            'reset-successfully' => 'Password reset successfully',
            'reset-failed' => 'Unable to reset password. Please try again later.y',
        ],

        'logout' => [
            'logout_successfully' => 'User Logged out successfully.',
            'otp_code_email_sent_successfully' => 'OTP code for reset password sent successfully',
        ],

        'reset_password_otp' => [
            'subject' => 'Reset Password OTP Code',
            'greeting_message' => 'You requested to reset your password.',
            'your_otp_code_is' => 'Your OTP code is: :otp',
            'otp_code_valid_for_x_minutes' => 'This OTP code is valid for :minutes minutes.',
            'ignore_message' => 'If you did not request a password reset, please ignore this email.',
            'footer_message' => 'Thank you for using :website.',
        ],

    ]
];