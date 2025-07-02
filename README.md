# Laravel SmsApi Notification Channel

[![Latest Version](https://img.shields.io/packagist/v/webxscripts/laravel-smsapi-notification-channel.svg?style=flat-square)](https://packagist.org/packages/webxscripts/laravel-smsapi-notification-channel)
[![Total Downloads](https://img.shields.io/packagist/dt/webxscripts/laravel-smsapi-notification-channel.svg?style=flat-square)](https://packagist.org/packages/webxscripts/laravel-smsapi-notification-channel)

A Laravel 11+ notification channel for [SmsApi](https://www.smsapi.com/) with PHP 8.2+ support.

---

## Requirements

- PHP 8.2 or higher
- Laravel 11 or newer
- SmsApi account and API token

---

## Installation

```bash
composer require webxscripts/laravel-smsapi-notification-channel
php artisan vendor:publish --tag=smsapi-config
```

---

## Configuration

Add the following to your `.env` file:

```env
SMSAPI_TOKEN=your_api_token_here
SMSAPI_SERVICE=com
SMSAPI_FROM=YourApp
```

Supported services:
- `com` (default)
- `pl`
- For other regions, use:
  ```env
  SMSAPI_URI=https://api.smsapi.se/
  ```

---

## Basic Usage

Create a notification:

```bash
php artisan make:notification OrderConfirmation
```

Example implementation:

```php
use Illuminate\Notifications\Notification;
use WebXScripts\SmsApiNotification\SmsApiMessage;

class OrderConfirmation extends Notification
{
    public function __construct(private string $orderNumber) {}

    public function via($notifiable): array
    {
        return ['smsapi'];
    }

    public function toSmsApi($notifiable): SmsApiMessage
    {
        return SmsApiMessage::create("Order #{$this->orderNumber} confirmed.")
            ->from('MyShop');
    }
}
```

---

## Advanced Example

```php
SmsApiMessage::create('Code: 123456')
    ->from('MyApp')
    ->encoding('utf-8')
    ->test(app()->environment('testing'))
    ->fast(true)
    ->normalize(true)
    ->single(false)
    ->expirationDate(now()->addMinutes(5))
    ->notifyUrl('https://example.com/sms-delivery')
    ->template('verification_code')
    ->param1($this->code)
    ->param2($notifiable->name);
```

---

## Phone Number Resolution Order

1. `SmsApiNotifiable` interface:
   ```php
   public function getSmsApiPhoneNumber(): string
   {
       return $this->phone_number;
   }
   ```

2. `routeNotificationForSmsApi()` method
3. `routeNotificationForSms()` method
4. Model attributes: `phone` or `phone_number`

---

## Sending Notifications

```php
$user->notify(new OrderConfirmation('ORD-12345'));

Notification::send($users, new OrderConfirmation('ORD-12345'));

Notification::route('smsapi', '+48123456789')
    ->notify(new OrderConfirmation('ORD-12345'));
```

---

## Available Message Methods

```php
SmsApiMessage::create('...')
    ->from(string)
    ->encoding(string)
    ->test(bool)
    ->fast(bool)
    ->normalize(bool)
    ->noUnicode(bool)
    ->single(bool)
    ->notifyUrl(string)
    ->expirationDate(DateTimeInterface)
    ->timeRestriction(string)
    ->partnerId(string)
    ->checkIdx(bool)
    ->idx(array)
    ->template(string)
    ->param1(string)
    ->param2(string)
    ->param3(string)
    ->param4(string);
```

All methods return an immutable instance.

---

## Error Handling

Custom exceptions provided:

- `MissingPhoneNumberException`
- `MissingApiTokenException`
- `InvalidNotificationException`

Example:

```php
try {
    $user->notify(new OrderConfirmation('ORD-12345'));
} catch (MissingPhoneNumberException $e) {
    Log::warning('User has no phone number');
}
```

---

## Testing & Code Quality

```bash
composer test            # Run tests
composer test-coverage   # Run with coverage
composer format          # Format code
composer analyse         # Static analysis
```

---

## License

MIT. See [LICENSE.md](LICENSE.md).
