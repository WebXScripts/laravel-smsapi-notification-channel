<?php

declare(strict_types=1);

namespace WebXScripts\SmsApiNotification\Exceptions;

final class MissingPhoneNumberException extends SmsApiNotificationException
{
    public static function forNotifiable(string $notifiableClass): self
    {
        return new self(
            "Unable to determine phone number for notifiable of type {$notifiableClass}. " .
            'Please implement SmsApiNotifiable interface or add routeNotificationForSmsApi method.'
        );
    }

    public static function forAnonymousNotifiable(): self
    {
        return new self(
            'Unable to determine phone number for AnonymousNotifiable. ' .
            'Make sure you are using Notification::route(\'smsapi\', $phoneNumber) correctly.'
        );
    }
}