<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | SmsApi API Token
    |--------------------------------------------------------------------------
    |
    | Your SmsApi API token. You can get it from your SmsApi dashboard.
    | https://ssl.smsapi.com/ or https://ssl.smsapi.pl/
    |
    */
    'api_token' => env('SMSAPI_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | SmsApi Service
    |--------------------------------------------------------------------------
    |
    | The SmsApi service to use:
    | - 'com' for SMSAPI.COM (international)
    | - 'pl' for SMSAPI.PL (Poland)
    |
    */
    'service' => env('SMSAPI_SERVICE', 'com'),

    /*
    |--------------------------------------------------------------------------
    | Custom SmsApi URI
    |--------------------------------------------------------------------------
    |
    | Custom URI for SmsApi service (optional).
    | For SMSAPI.SE: https://api.smsapi.se/
    | For SMSAPI.BG: https://api.smsapi.bg/
    |
    */
    'uri' => env('SMSAPI_URI'),

    /*
    |--------------------------------------------------------------------------
    | Default Sender Name
    |--------------------------------------------------------------------------
    |
    | Default sender name for SMS messages. This can be overridden
    | per message using the from() method on SmsApiMessage.
    |
    */
    'from' => env('SMSAPI_FROM'),
];
