<?php

declare(strict_types=1);

namespace WebXScripts\SmsApiNotification;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;
use Smsapi\Client\Curl\SmsapiHttpClient;
use Smsapi\Client\SmsapiClient;

class SmsApiNotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerSmsApiClient();
        $this->registerChannel();
    }

    public function boot(): void
    {
        $this->registerNotificationChannel();
        $this->publishConfiguration();
        $this->mergeConfiguration();
    }

    private function registerSmsApiClient(): void
    {
        $this->app->singleton(SmsapiClient::class, fn () => new SmsapiHttpClient);
    }

    private function registerChannel(): void
    {
        $this->app->singleton(SmsApiChannel::class, function ($app) {
            return new SmsApiChannel(
                client: $app->make(SmsapiClient::class),
                config: $app->make('config')->get('smsapi', [])
            );
        });
    }

    private function registerNotificationChannel(): void
    {
        $this->app->make(ChannelManager::class)->extend('smsapi', function ($app) {
            return $app->make(SmsApiChannel::class);
        });
    }

    private function publishConfiguration(): void
    {
        $this->publishes([
            __DIR__ . '/../config/smsapi.php' => config_path('smsapi.php'),
        ], ['smsapi-config', 'config']);
    }

    private function mergeConfiguration(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/smsapi.php', 'smsapi');
    }
}
