<?php

namespace App\Facades;

class Paginator extends \Illuminate\Support\Facades\Facade
{
    public static function getFacadeAccessor()
    {
        return 'paginator';
    }
}
