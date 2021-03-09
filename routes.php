<?php
/**
 * Created by PhpStorm.
 * User: God
 * DateTime: 2019/12/181:15 PM
 */

use \Xtrees\LaraSetting\Http\LaraSettingController;


Route::group(['prefix' => config('lara-setting.route.prefix'), 'middleware' => config('lara-setting.route.middleware')], function () {
    Route::get('/settings', LaraSettingController::class . '@index')->name('lara.setting.index');
    Route::put('/settings/create', LaraSettingController::class . '@create');
    Route::post('/settings/update', LaraSettingController::class . '@update');
    Route::put('/settings/group', LaraSettingController::class . '@createGroup');
    Route::post('/settings/group/sort', LaraSettingController::class . '@sortGroup');
});



