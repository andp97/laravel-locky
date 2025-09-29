<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Time To Live (TTL)
    |--------------------------------------------------------------------------
    |
    | The maximum number of seconds a lock should be held. After this time,
    | the lock will be automatically released. It's crucial to set this
    | to a value greater than the worst-case execution time of your
    | critical section to prevent premature releases.
    |
    */
    'ttl_seconds' => env('LOCKY_TTL_SECONDS', 10),

    /*
    |--------------------------------------------------------------------------
    | Maximum Lock Attempts
    |--------------------------------------------------------------------------
    |
    | The maximum number of times the system will attempt to acquire a lock
    | before giving up. A value of 1 means no retries.
    |
    */
    'max_attempts' => env('LOCKY_MAX_ATTEMPTS', 6),

    /*
    |--------------------------------------------------------------------------
    | Base Delay (Milliseconds)
    |--------------------------------------------------------------------------
    |
    | The initial delay in milliseconds before the first retry. This value
    | serves as the starting point for the backoff calculation.
    |
    */
    'base_delay_ms' => env('LOCKY_BASE_DELAY_MS', 50),

    /*
    |--------------------------------------------------------------------------
    | Backoff Multiplier
    |--------------------------------------------------------------------------
    |
    | The factor by which the delay increases with each subsequent retry.
    | A multiplier of 2.0 means the delay doubles each time, creating an
    | exponential backoff strategy.
    |
    */
    'multiplier' => env('LOCKY_MULTIPLIER', 2.0),

    /*
    |--------------------------------------------------------------------------
    | Maximum Delay (Milliseconds)
    |--------------------------------------------------------------------------
    |
    | An optional upper limit for the retry delay. This prevents the delay
    | from growing indefinitely, ensuring it doesn't exceed a reasonable
    | timeframe. Set to null for no maximum.
    |
    */
    'max_delay_ms' => env('LOCKY_MAX_DELAY_MS', 2000),

    /*
    |--------------------------------------------------------------------------
    | Jitter Strategy
    |--------------------------------------------------------------------------
    |
    | The strategy for adding randomness to the backoff delay. Jitter helps
    | prevent multiple processes from retrying in sync (thundering herd).
    | Supported: "none", "equal", "full".
    |
    */
    'jitter' => env('LOCKY_JITTER', 'full'),
];