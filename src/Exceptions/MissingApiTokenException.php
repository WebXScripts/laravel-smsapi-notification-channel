<?php

declare(strict_types=1);

namespace WebXScripts\SmsApiNotification\Exceptions;

final class MissingApiTokenException extends SmsApiNotificationException
{
    public static function create(): self
    {
        return new self('SmsApi API token is required. Please set SMSAPI_TOKEN in your .env file.');
    }
}
