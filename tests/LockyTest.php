<?php

use Illuminate\Support\Facades\Cache;
use Pavons\Locky\Exceptions\CouldNotAcquireLockException;
use Pavons\Locky\Facades\Locky;

it('can acquire lock and run critical section', function () {
    $lock = Mockery::mock();
    $lock->shouldReceive('get')->once()->andReturn(true);
    $lock->shouldReceive('release')->once();

    Cache::shouldReceive('lock')
        ->with('test-key', 10)
        ->once()
        ->andReturn($lock);

    $result = Locky::make('test-key')
        ->ttl(10)
        ->run(function () {
            return 'success';
        });

    expect($result)->toBe('success');
});

it('calls onFail when lock cannot be acquired', function () {
    $lock = Mockery::mock();
    $lock->shouldReceive('get')->times(3)->andReturn(false);

    Cache::shouldReceive('lock')
        ->with('test-key', 10)
        ->times(3)
        ->andReturn($lock);

    $onFailCalled = false;

    $result = Locky::make('test-key')
        ->ttl(10)
        ->attempts(3)
        ->baseDelayMs(1)
        ->onFail(function (string $key, int $attempts) use (&$onFailCalled) {
            $onFailCalled = true;
            expect($key)->toBe('test-key')
                ->and($attempts)->toBe(3);

            return 'failed';
        })
        ->run(function () {
            return 'success';
        });

    expect($onFailCalled)->toBeTrue()
        ->and($result)->toBe('failed');
});

it('throws exception when lock cannot be acquired and onFail is not set', function () {
    $lock = Mockery::mock();
    $lock->shouldReceive('get')->times(3)->andReturn(false);

    Cache::shouldReceive('lock')
        ->with('test-key', 10)
        ->times(3)
        ->andReturn($lock);

    Locky::make('test-key')
        ->ttl(10)
        ->attempts(3)
        ->baseDelayMs(1)
        ->run(function () {
            return 'success';
        });
})->throws(CouldNotAcquireLockException::class, "Could not acquire lock 'test-key' after 3 attempts.");

it('calls onRetry when lock is not acquired', function () {
    $lock = Mockery::mock();
    $lock->shouldReceive('get')->once()->andReturn(false);
    $lock->shouldReceive('get')->once()->andReturn(true);
    $lock->shouldReceive('release')->once();

    Cache::shouldReceive('lock')
        ->with('test-key', 10)
        ->twice()
        ->andReturn($lock);

    $onRetryCalled = false;

    $result = Locky::make('test-key')
        ->ttl(10)
        ->attempts(3)
        ->baseDelayMs(1)
        ->onRetry(function (string $key, int $attempt, int $sleepMs) use (&$onRetryCalled) {
            $onRetryCalled = true;
            expect($key)->toBe('test-key')
                ->and($attempt)->toBe(1)
                ->and($sleepMs)->toBeGreaterThanOrEqual(0);
        })
        ->run(function () {
            return 'success';
        });

    expect($onRetryCalled)->toBeTrue()
        ->and($result)->toBe('success');
});
