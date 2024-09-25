<?php

namespace App\Http\Cache;

use Illuminate\Cache\RateLimiting\Limit as RateLimitingLimit;

class Limit extends RateLimitingLimit
{
    public static function perSeconds($decaySeconds, $maxAttempts)
    {
        return new static('', $maxAttempts, $decaySeconds / 60.0);
    }
}
