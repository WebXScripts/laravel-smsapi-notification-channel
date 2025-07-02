<?php

declare(strict_types=1);

namespace WebXScripts\SmsApiNotification\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use WebXScripts\SmsApiNotification\SmsApiNotificationServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadEnvironmentVariables();
    }

    protected function getPackageProviders($app): array
    {
        return [
            SmsApiNotificationServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('smsapi.api_token', 'test-token');
        $app['config']->set('smsapi.service', 'com');
        $app['config']->set('smsapi.from', 'TestApp');
    }

    private function loadEnvironmentVariables(): void
    {
        $envFile = __DIR__ . '/../.env.testing';

        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                    putenv(trim($key) . '=' . trim($value));
                }
            }
        }
    }
}