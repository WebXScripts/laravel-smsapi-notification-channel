<?php

declare(strict_types=1);

use WebXScripts\SmsApiNotification\SmsApiMessage;

it('can create a basic message', function () {
    $message = SmsApiMessage::create('Hello World');

    expect($message->content)->toBe('Hello World')
        ->and($message->encoding)->toBe('utf-8')
        ->and($message->from)->toBeNull();
});

it('can create message with fluent interface', function () {
    $message = SmsApiMessage::create('Hello World')
        ->from('TestApp')
        ->test(true)
        ->fast(true);

    expect($message->content)->toBe('Hello World')
        ->and($message->from)->toBe('TestApp')
        ->and($message->test)->toBeTrue()
        ->and($message->fast)->toBeTrue();
});

it('creates immutable instances', function () {
    $original = SmsApiMessage::create('Hello');
    $modified = $original->from('TestApp');

    expect($original->from)->toBeNull()
        ->and($modified->from)->toBe('TestApp');
});

it('can chain multiple methods', function () {
    $message = SmsApiMessage::create('Test')
        ->from('Sender')
        ->encoding('utf-8')
        ->test(true)
        ->fast(false)
        ->normalize(true);

    expect($message->from)->toBe('Sender')
        ->and($message->encoding)->toBe('utf-8')
        ->and($message->test)->toBeTrue()
        ->and($message->fast)->toBeFalse()
        ->and($message->normalize)->toBeTrue();
});
