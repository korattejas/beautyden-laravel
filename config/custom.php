<?php

return [
    'common_error_message' => 'Something went wrong. Please try again after sometime.',
    'exception_error_code' => 500,
    'validator_error_code' => 422,
    'status_code_for_success' => 200,
    'status_code_for_expire_session' => 400,
    'status_code_for_refresh_session' => 401,
    'status_code_for_backend_error' => 406,
    'status_code_for_validation_error' => 422,
    'status_code_for_exception_error' => 500,
    'status_code_for_inactive_user' => 403,
    'status_code_for_multiple_session_login' => 410,
    'status_code_for_pin_code_validation_error' => 409,
    'status_code_for_failed_notification' => 503,
    'status_code_for_failed_user_verified' => 408,
    'status_code_for_failed_user_daily_verify' => 407,
    'status_code_for_payment_required' => 402,
    'aws_url' => env('AWS_URL'),
    'aws_region' => env('AWS_DEFAULT_REGION'),
    'aws_access_key_id' => env('AWS_ACCESS_KEY_ID'),
    'AWS_secret_access_key' => env('AWS_SECRET_ACCESS_KEY'),
    'aws_bucket' => env('AWS_BUCKET'),

    'filter_short_value' => ['Whats New', 'Popularity', 'Review'],

    'email_provider_array' => [
        'gmail.com',
        'yahoo.com',
        'outlook.com',
        'mail.com',
    ],
    
    'otp_expiration_time' => env('OTP_EXPIRATION_TIME'),
    'resend_otp_time' => env('RESEND_OTP_TIME'),
    'resend_otp_max_limit' => env('RESEND_OTP_MAX_LIMIT'),
    'mail_from_address' => env('MAIL_FROM_ADDRESS'),
    'mail_from_name' => env('MAIL_FROM_NAME'),
    'encrypt_decrypt_key_app' => env('ENCRYPT_DECRYPT_KEY_APP'),
    'encrypt_decrypt_key_app_iv_app' => env('ENCRYPT_DECRYPT_IV_APP'),

    'all_dropdown_type' => 0,
    'category_wise_sub_category_dropdown_type' => 1,
    'photographer_service_dropdown_type' => 2,
    'city_dropdown_type' => 3,
    'category_dropdown_type' => 4,

];