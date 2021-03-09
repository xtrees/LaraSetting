<?php
/**
 * User: Monster
 * Date: 2018/3/1
 * Time: 17:30
 */

return [
    'facade' => 'LaraSetting',
    'route' => [
        'prefix' => '',
        'middleware' => 'web',
    ],
    'cache' => [
        'enable' => false,
        'ttl' => 60,
        'prefix' => 'settings:',
    ],
];
