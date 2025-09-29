<?php

namespace Pavons\Locky;

use Closure;
use Illuminate\Support\Facades\Cache;

class Locky
{
    // Core
    protected string $key;
    protected int $ttlSeconds;

    // Retry + Backoff
    protected int $maxAttempts;
    protected int $baseDelayMs;
    protected float $multiplier;
    protected ?int $maxDelayMs;
    protected string $jitter; // none|equal|full

    // Hooks
    protected ?Closure $onFail = null; // fn(string $key, int $attempts): mixed
    protected ?Closure $onRetry = null; // fn(string $key, int $attempt, int $sleepMs): void

    public static function make(string $key): self
    {
        $self = new self;
        $self->key = $key;
        $self->ttlSeconds = config('locky.ttl_seconds');
        $self->maxAttempts = config('locky.max_attempts');
        $self->baseDelayMs = config('locky.base_delay_ms');
        $self->multiplier = config('locky.multiplier');
        $self->maxDelayMs = config('locky.max_delay_ms');
        $self->jitter = config('locky.jitter');
        return $self;
    }

    /** Configuration (fluent) */
    public function ttl(int $seconds): static { $this->ttlSeconds = max(1, $seconds); return $this; }
    public function attempts(int $attempts): static { $this->maxAttempts = max(1, $attempts); return $this; }
    public function baseDelayMs(int $ms): static { $this->baseDelayMs = max(0, $ms); return $this; }
    public function multiplier(float $m): static { $this->multiplier = max(1.0, $m); return $this; }
    public function maxDelayMs(?int $ms): static { $this->maxDelayMs = $ms !== null ? max(1, $ms) : null; return $this; }
    public function jitter(string $strategy): static
    {
        $strategy = strtolower($strategy);
        if (!in_array($strategy, ['none', 'equal', 'full'], true)) {
            throw new \InvalidArgumentException('Jitter must be one of: none, equal, full.');
        }
        $this->jitter = $strategy;
        return $this;
    }
    public function onFail(Closure $cb): static { $this->onFail = $cb; return $this; }
    public function onRetry(Closure $cb): static { $this->onRetry = $cb; return $this; }

    /**
     * Run a critical section with an exclusive Redis lock and retry/backoff.
     *
     * @template T
     * @param  Closure():T  $critical
     * @return T
     * @throws \RuntimeException on failure (unless onFail returns a value)
     */
    public function run(Closure $critical)
    {
        $attempt = 0;

        while (true) {
            $attempt++;

            $lock = Cache::lock($this->key, $this->ttlSeconds);

            if ($lock->get()) {
                try {
                    /** @var T */
                    $result = $critical();
                    return $result;
                } finally {
                    // Best-effort safe release
                    try { $lock->release(); } catch (\Throwable $e) { /* TTL may have expired; ignore */ }
                }
            }

            if ($attempt >= $this->maxAttempts) {
                if ($this->onFail) {
                    return ($this->onFail)($this->key, $attempt);
                }
                throw new \RuntimeException("Could not acquire lock '{$this->key}' after {$attempt} attempts.");
            }

            $sleepMs = $this->computeSleepMs($attempt);
            if ($this->onRetry) {
                ($this->onRetry)($this->key, $attempt, $sleepMs);
            }
            usleep($sleepMs * 1000);
        }
    }

    /** Backoff calculators */
    protected function computeSleepMs(int $attempt): int
    {
        // Exponential growth from attempt 2 onward (attempt 1 sleeps base)
        $raw = (int) round($this->baseDelayMs * ($this->multiplier ** max(0, $attempt - 1)));
        $cap = $this->maxDelayMs ? min($raw, $this->maxDelayMs) : $raw;

        return match ($this->jitter) {
            'none'  => $cap,
            'equal' => $cap / 2 + random_int(0, (int) max(1, $cap / 2)),
            'full'  => random_int(0, max(1, $cap)),
            default => throw new \InvalidArgumentException('Jitter must be one of: none, equal, full.'),
        };
    }
}