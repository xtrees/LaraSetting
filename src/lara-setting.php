<?php
/**
 * User: Monster
 * Date: 2018/3/1
 * Time: 17:30
 */

return [

    //use framework's cache drive
    'cache' => [

        'enable' => true,
        'prefix' => 'settings_',
        //cache time .minutes
        'ttl' => 60,
    ],

    //Runtime cache
    'runtime' => true,

    //Facade name   LaraSetting::get(..)
    'facade' => 'LaraSetting',
];