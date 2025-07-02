<?php

declare(strict_types=1);

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use WebXScripts\SmsApiNotification\SmsApiMessage;

it('can send test sms to real api', function () {
    if (empty(env('SMSAPI_REAL_TOKEN'))) {
        test()->markTestSkipped('SMSAPI_REAL_TOKEN not provided');
    }

    config(['smsapi.api_token' => env('SMSAPI_REAL_TOKEN')]);
    config(['smsapi.service' => env('SMSAPI_SERVICE')]);
    config(['smsapi.from' => env('SMSAPI_FROM')]);

    $notification = new class extends Notification
    {
        public function via($notifiable): array
        {
            return ['smsapi'];
        }

        public function toSmsApi($notifiable): SmsApiMessage
        {
            return SmsApiMessage::create('test - ' . now()->format('H:i:s'))
                ->test();
        }
    };

    $phoneNumber = env('TEST_PHONE_NUMBER', '+48123456789');

    NotificationFacade::route('smsapi', $phoneNumber)
        ->notify($notification);

    expect(true)->toBeTrue();
});

it('can send real sms with confirmation', function () {
    if (! env('SEND_REAL_SMS', false)) {
        test()->markTestSkipped('Set SEND_REAL_SMS=true to send real SMS');
    }

    if (empty(env('SMSAPI_REAL_TOKEN'))) {
        test()->markTestSkipped('SMSAPI_REAL_TOKEN not provided');
    }

    config(['smsapi.api_token' => env('SMSAPI_REAL_TOKEN')]);
    config(['smsapi.service' => env('SMSAPI_SERVICE')]);
    config(['smsapi.from' => env('SMSAPI_FROM')]);

    $notification = new class extends Notification
    {
        public function via($notifiable): array
        {
            return ['smsapi'];
        }

        public function toSmsApi($notifiable): SmsApiMessage
        {
            return SmsApiMessage::create('test - ' . now()->format('H:i:s'))
                ->test(false);
        }
    };

    $phoneNumber = env('TEST_PHONE_NUMBER', '+48123456789');

    NotificationFacade::route('smsapi', $phoneNumber)
        ->notify($notification);

    expect(true)->toBeTrue();
});
