<?php

namespace Huangdijia\Curl\Facades;

use Huangdijia\Curl\Curl as Accessor;
use Illuminate\Support\Facades\Facade;

class Curl extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Accessor::class;
    }
}
