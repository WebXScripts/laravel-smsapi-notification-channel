# Laravel SmsApi Notification Channel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/webxscripts/laravel-smsapi-notification-channel.svg?style=flat-square)](https://packagist.org/packages/webxscripts/laravel-smsapi-notification-channel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/webxscripts/laravel-smsapi-notification-channel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/webxscripts/laravel-smsapi-notification-channel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/webxscripts/laravel-smsapi-notification-channel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/webxscripts/laravel-smsapi-notification-channel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/webxscripts/laravel-smsapi-notification-channel.svg?style=flat-square)](https://packagist.org/packages/webxscripts/laravel-smsapi-notification-channel)

Modern Laravel Notification Channel for SmsApi PHP Client with support for Laravel 11+ and PHP 8.2+.

## Features

- 🚀 **Modern PHP 8.2+ features** - Readonly classes, named arguments, match expressions
- 📱 **Full SmsApi support** - All SmsApi features including templates, parameters, and delivery options
- 🔧 **Type-safe** - Full type hints and PHPStan level 8 compliance
- 🧪 **Well tested** - Comprehensive test suite with Pest PHP
- 🎯 **Laravel 11+ optimized** - Built specifically for modern Laravel versions
- 🔒 **Immutable messages** - Thread-safe message building with fluent interface
- 📋 **Multiple phone resolution methods** - Flexible ways to get phone numbers from notifiables

## Installation

```bash
composer require webxscripts/laravel-smsapi-notification-channel
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=smsapi-config
```

## Configuration

Add these environment variables to your `.env` file:

```env
SMSAPI_TOKEN=your_api_token_here
SMSAPI_SERVICE=com
SMSAPI_FROM=YourApp
```

### Available Services

- `com` - SMSAPI.COM (international, default)
- `pl` - SMSAPI.PL (Poland)

For SMSAPI.SE or SMSAPI.BG, use:
```env
SMSAPI_SERVICE=com
SMSAPI_URI=https://api.smsapi.se/
```

## Usage

### Basic Usage

Create a notification:

```bash
php artisan make:notification OrderConfirmation
```

Implement the `toSmsApi` method:

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use WebXScripts\SmsApiNotification\SmsApiMessage;

class OrderConfirmation extends Notification
{
    public function __construct(
        private readonly string $orderNumber
    ) {}

    public function via($notifiable): array
    {
        return ['smsapi'];
    }

    public function toSmsApi($notifiable): SmsApiMessage
    {
        return SmsApiMessage::create("Your order #{$this->orderNumber} has been confirmed!")
            ->from('MyShop');
    }
}
```

### Advanced Usage with All Options

```php
public function toSmsApi($notifiable): SmsApiMessage
{
    return SmsApiMessage::create('Your verification code: 123456')
        ->from('MyApp')
        ->encoding('utf-8')
        ->test(app()->environment('testing'))
        ->fast(true)
        ->normalize(true)
        ->single(false)
        ->expirationDate(now()->addMinutes(5))
        ->notifyUrl('https://example.com/sms-delivery-report')
        ->template('verification_code')
        ->param1($this->code)
        ->param2($notifiable->name);
}
```

### Phone Number Resolution

The channel resolves phone numbers in this priority order:

1. **SmsApiNotifiable interface** (recommended):
```php
use WebXScripts\SmsApiNotification\Contracts\SmsApiNotifiable;

class User extends Model implements SmsApiNotifiable
{
    public function getSmsApiPhoneNumber(): string
    {
        return $this->phone_number;
    }
}
```

2. **Notification routing method**:
```php
class User extends Model
{
    public function routeNotificationForSmsApi(): string
    {
        return $this->phone_number;
    }
}
```

3. **Generic SMS routing method**:
```php
public function routeNotificationForSms(): string
{
    return $this->phone;
}
```

4. **Model attributes**: `phone` or `phone_number`

### Sending Notifications

```php
use App\Notifications\OrderConfirmation;

// Single user
$user = User::find(1);
$user->notify(new OrderConfirmation('ORD-12345'));

// Multiple users
$users = User::whereNotNull('phone')->get();
Notification::send($users, new OrderConfirmation('ORD-12345'));

// On-demand notifications
Notification::route('smsapi', '+48123456789')
    ->notify(new OrderConfirmation('ORD-12345'));
```

## Available Message Methods

All methods return a new immutable instance:

```php
SmsApiMessage::create('content')
    ->from(string $sender)                    // Sender name/number
    ->encoding(string $encoding)              // Message encoding (default: utf-8)
    ->test(bool $test)                        // Test mode
    ->fast(bool $fast)                        // Fast delivery
    ->normalize(bool $normalize)              // Normalize phone numbers  
    ->noUnicode(bool $noUnicode)             // Disable unicode
    ->single(bool $single)                    // Send as single message
    ->notifyUrl(string $url)                  // Delivery report webhook URL
    ->expirationDate(DateTimeInterface $date) // Message expiration
    ->timeRestriction(string $restriction)    // Time-based delivery restrictions
    ->partnerId(string $partnerId)            // Partner ID
    ->checkIdx(bool $checkIdx)               // Validate IDX
    ->idx(array $idx)                        // IDX array for external tracking
    ->template(string $template)              // Template name
    ->param1(string $param)                   // Template parameter 1
    ->param2(string $param)                   // Template parameter 2  
    ->param3(string $param)                   // Template parameter 3
    ->param4(string $param);                  // Template parameter 4
```

## Error Handling

The package includes specific exceptions:

```php
use WebXScripts\SmsApiNotification\Exceptions\{
    InvalidNotificationException,
    MissingApiTokenException,
    MissingPhoneNumberException
};

try {
    $user->notify(new OrderConfirmation('ORD-12345'));
} catch (MissingPhoneNumberException $e) {
    Log::warning('User has no phone number', ['user' => $user->id]);
} catch (MissingApiTokenException $e) {
    Log::error('SmsApi token not configured');
}
```

## Testing

```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Format code
composer format

# Analyze code
composer analyse
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.