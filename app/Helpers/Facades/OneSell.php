<?php

namespace App\Helpers\Facades;

use Illuminate\Support\Facades\Facade;

class OneSell extends Facade
{
    protected static function getFacadeAccessor()
    { 
        return 'OneSell';
    }
}
