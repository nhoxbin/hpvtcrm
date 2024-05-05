<?php

namespace App\Http\Mixins;

use Closure;
use GuzzleHttp\Promise\Each;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Factory;

class HttpMixin
{
    public function concurrent(): Closure
    {
        return function (
            int $concurrency,
            callable $requests,
            callable $onFulfilled = null,
            callable $onRejected = null
        ): void {
            /**
             * @var $this Factory
             */
            $requests = $requests(...)(new Pool($this));

            Each::ofLimit(
                $requests,
                $concurrency,
                $onFulfilled,
                $onRejected
            )->wait();
        };
    }
}
