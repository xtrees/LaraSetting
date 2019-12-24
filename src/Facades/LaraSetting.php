<?php

namespace Xtrees\LaraSetting\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * User: Monster
 * Date: 2018/3/2
 * Time: 16:40
 */
class LaraSetting extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lara-setting';
    }
}