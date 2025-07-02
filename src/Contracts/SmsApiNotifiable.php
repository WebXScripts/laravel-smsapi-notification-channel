<?php

declare(strict_types=1);

namespace WebXScripts\SmsApiNotification\Contracts;

interface SmsApiNotifiable
{
    public function getSmsApiPhoneNumber(): string;
}
