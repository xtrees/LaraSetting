<?php
/**
 * Created by PhpStorm.
 * User: xutao
 * Date: 2018/5/16
 * Time: 10:22
 */

if (!function_exists('settings')) {
    function settings($key, $default = null)
    {
        try {
            $instance = app()->make('lara-setting');
            $setting = $instance->get($key);
            if (is_null($setting)) {
                return $default;
            }
            return $setting;
        } catch (Exception $exception) {
            return $default;
        }
    }
}