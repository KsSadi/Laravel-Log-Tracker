<?php

namespace Kssadi\LogTracker\Facades;

use Illuminate\Support\Facades\Facade;

class LogTracker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'log-tracker';
    }
}
