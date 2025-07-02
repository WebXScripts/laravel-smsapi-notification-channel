<?php

declare(strict_types=1);

namespace WebXScripts\SmsApiNotification\Exceptions;

final class InvalidNotificationException extends SmsApiNotificationException
{
    public static function missingToSmsApiMethod(): self
    {
        return new self('Notification must have a toSmsApi method');
    }

    public static function invalidReturnType(): self
    {
        return new self('toSmsApi must return an instance of SmsApiMessage');
    }
}
