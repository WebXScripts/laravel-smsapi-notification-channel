<?php

declare(strict_types=1);

use Illuminate\Notifications\Notification;
use Smsapi\Client\Feature\Sms\Bag\SendSmsBag;
use Smsapi\Client\Feature\Sms\SmsFeature;
use Smsapi\Client\Service\SmsapiComService;
use Smsapi\Client\SmsapiClient;
use WebXScripts\SmsApiNotification\Contracts\SmsApiNotifiable;
use WebXScripts\SmsApiNotification\Exceptions\InvalidNotificationException;
use WebXScripts\SmsApiNotification\Exceptions\MissingApiTokenException;
use WebXScripts\SmsApiNotification\Exceptions\MissingPhoneNumberException;
use WebXScripts\SmsApiNotification\SmsApiChannel;
use WebXScripts\SmsApiNotification\SmsApiMessage;

beforeEach(function () {
    $this->client = Mockery::mock(SmsapiClient::class);
    $this->service = Mockery::mock(SmsapiComService::class);
    $this->smsFeature = Mockery::mock(SmsFeature::class);

    $this->config = [
        'api_token' => 'test-token',
        'service' => 'com',
        'from' => 'TestApp',
    ];
});

afterEach(function () {
    Mockery::close();
});

it('can send a notification successfully', function () {
    $this->client->shouldReceive('smsapiComService')
        ->with('test-token')
        ->andReturn($this->service);

    $this->service->shouldReceive('smsFeature')
        ->andReturn($this->smsFeature);

    $this->smsFeature->shouldReceive('sendSms')
        ->with(Mockery::type(SendSmsBag::class))
        ->once();

    $channel = new SmsApiChannel($this->client, $this->config);
    $notifiable = new TestNotifiable;
    $notification = new TestNotification;

    $channel->send($notifiable, $notification);
});

it('throws exception when notification has no toSmsApi method', function () {
    $channel = new SmsApiChannel($this->client, $this->config);
    $notifiable = new TestNotifiable;
    $notification = new InvalidTestNotification;

    $channel->send($notifiable, $notification);
})->throws(InvalidNotificationException::class, 'Notification must have a toSmsApi method');

it('throws exception when toSmsApi returns invalid type', function () {
    $channel = new SmsApiChannel($this->client, $this->config);
    $notifiable = new TestNotifiable;
    $notification = new InvalidReturnTypeNotification;

    $channel->send($notifiable, $notification);
})->throws(InvalidNotificationException::class, 'toSmsApi must return an instance of SmsApiMessage');

it('throws exception when phone number cannot be determined', function () {
    $channel = new SmsApiChannel($this->client, $this->config);
    $notifiable = new TestNotifiableWithoutPhone;
    $notification = new TestNotification;

    $channel->send($notifiable, $notification);
})->throws(MissingPhoneNumberException::class);

it('throws exception when API token is missing', function () {
    $config = ['api_token' => ''];
    $channel = new SmsApiChannel($this->client, $config);
    $notifiable = new TestNotifiable;
    $notification = new TestNotification;

    $channel->send($notifiable, $notification);
})->throws(MissingApiTokenException::class);

it('can get phone number from SmsApiNotifiable interface', function () {
    $this->client->shouldReceive('smsapiComService')
        ->with('test-token')
        ->andReturn($this->service);

    $this->service->shouldReceive('smsFeature')
        ->andReturn($this->smsFeature);

    $this->smsFeature->shouldReceive('sendSms')
        ->with(Mockery::on(function (SendSmsBag $sms) {
            return $sms->to === '+48123456789';
        }))
        ->once();

    $channel = new SmsApiChannel($this->client, $this->config);
    $notifiable = new TestSmsApiNotifiable;
    $notification = new TestNotification;

    $channel->send($notifiable, $notification);
});

// Test classes
class TestNotifiable
{
    public string $phone = '+48123456789';
}

class TestNotifiableWithoutPhone
{
    // No phone property
}

class TestSmsApiNotifiable implements SmsApiNotifiable
{
    public function getSmsApiPhoneNumber(): string
    {
        return '+48123456789';
    }
}

class TestNotification extends Notification
{
    public function toSmsApi($notifiable): SmsApiMessage
    {
        return SmsApiMessage::create('Test message');
    }
}

class InvalidTestNotification extends Notification
{
    // No toSmsApi method
}

class InvalidReturnTypeNotification extends Notification
{
    public function toSmsApi($notifiable): string
    {
        return 'Invalid return type';
    }
}
