<?php
/**
 * User: Monster
 * Date: 2018/3/1
 * Time: 17:30
 */

return [
    /*
   |--------------------------------------------------------------------------
   | Cache settings
   |--------------------------------------------------------------------------
   | Cache use framework's cache drive.
   | Supported cache mode : 'single', 'batch'
   | single :  get/update/cache settings records one by one
   | batch  :  cache all of the setting records together
   |
   */
    'cache' => [
        'mode' => 'batch',
        'enable' => true,
        'prefix' => 'settings:',
        //cache time .minutes
        'ttl' => 60,
    ],

    //Facade name   LaraSetting::get(..)
    'facade' => 'LaraSetting',
];