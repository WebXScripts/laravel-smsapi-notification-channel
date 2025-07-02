<?php

declare(strict_types=1);

namespace WebXScripts\SmsApiNotification;

use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use Smsapi\Client\Feature\Sms\Bag\SendSmsBag;
use Smsapi\Client\Service\SmsapiComService;
use Smsapi\Client\Service\SmsapiPlService;
use Smsapi\Client\SmsapiClient;
use WebXScripts\SmsApiNotification\Contracts\SmsApiNotifiable;
use WebXScripts\SmsApiNotification\Exceptions\InvalidNotificationException;
use WebXScripts\SmsApiNotification\Exceptions\MissingApiTokenException;
use WebXScripts\SmsApiNotification\Exceptions\MissingPhoneNumberException;

final readonly class SmsApiChannel
{
    public function __construct(
        private SmsapiClient $client,
        private array $config = []
    ) {}

    /**
     * Send the given notification.
     *
     * @throws InvalidNotificationException
     * @throws MissingApiTokenException
     * @throws MissingPhoneNumberException
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        $message = $this->getMessage($notification, $notifiable);
        $phoneNumber = $this->getPhoneNumber($notifiable);
        $service = $this->getService();
        $sms = $this->buildSms($phoneNumber, $message);

        $service->smsFeature()->sendSms($sms);
    }

    private function getMessage(Notification $notification, mixed $notifiable): SmsApiMessage
    {
        if (! method_exists($notification, 'toSmsApi')) {
            throw InvalidNotificationException::missingToSmsApiMethod();
        }

        $message = $notification->toSmsApi($notifiable);

        if (! $message instanceof SmsApiMessage) {
            throw InvalidNotificationException::invalidReturnType();
        }

        return $message;
    }

    private function getPhoneNumber(mixed $notifiable): string
    {
        if ($notifiable instanceof AnonymousNotifiable) {
            return $this->getPhoneFromAnonymousNotifiable($notifiable);
        }

        $phoneNumber = match (true) {
            $notifiable instanceof SmsApiNotifiable => $notifiable->getSmsApiPhoneNumber(),
            method_exists($notifiable, 'routeNotificationForSmsApi') => $notifiable->routeNotificationForSmsApi(),
            method_exists($notifiable, 'routeNotificationForSms') => $notifiable->routeNotificationForSms(),
            isset($notifiable->phone) => $notifiable->phone,
            isset($notifiable->phone_number) => $notifiable->phone_number,
            default => null,
        };

        if (empty($phoneNumber)) {
            throw MissingPhoneNumberException::forNotifiable($notifiable::class);
        }

        return $phoneNumber;
    }

    private function getPhoneFromAnonymousNotifiable(AnonymousNotifiable $notifiable): string
    {
        $routes = $notifiable->routes ?? [];

        if (! empty($routes['smsapi'])) {
            return $routes['smsapi'];
        }

        if (! empty($routes['sms'])) {
            return $routes['sms'];
        }

        throw MissingPhoneNumberException::forAnonymousNotifiable();
    }

    private function getService(): SmsapiComService|SmsapiPlService
    {
        $apiToken = $this->config['api_token'] ?? '';
        $service = $this->config['service'] ?? 'com';
        $uri = $this->config['uri'] ?? null;

        if (empty($apiToken)) {
            throw MissingApiTokenException::create();
        }

        return match ($service) {
            'pl' => $uri
                ? $this->client->smsapiPlServiceWithUri($apiToken, $uri)
                : $this->client->smsapiPlService($apiToken),
            'com' => $uri
                ? $this->client->smsapiComServiceWithUri($apiToken, $uri)
                : $this->client->smsapiComService($apiToken),
            default => throw new InvalidArgumentException("Unsupported service: {$service}"),
        };
    }

    private function buildSms(
        #[\SensitiveParameter] string $phoneNumber,
        SmsApiMessage $message
    ): SendSmsBag {
        $sms = SendSmsBag::withMessage($phoneNumber, $message->content);

        $defaultFrom = $this->config['from'] ?? null;
        if ($message->from || $defaultFrom) {
            $sms->from = $message->from ?? $defaultFrom;
        }

        $this->applyMessageProperties($sms, $message);

        return $sms;
    }

    private function applyMessageProperties(SendSmsBag $sms, SmsApiMessage $message): void
    {
        $properties = [
            'encoding', 'test', 'fast', 'normalize', 'noUnicode', 'single',
            'notifyUrl', 'expirationDate', 'timeRestriction', 'partnerId',
            'checkIdx', 'idx', 'template', 'param1', 'param2', 'param3', 'param4',
        ];

        foreach ($properties as $property) {
            if ($message->$property !== null) {
                $sms->$property = $message->$property;
            }
        }
    }
}
