<?php

namespace App\Base\Repositories\Services\Cache;

interface CacheInterface
{
    public function get($key);

    public function put($key, $value, $minutes = false);

    public function has($key);
 
    public function flush();
}
