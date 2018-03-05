<?php

namespace JasonXt\LaraSetting;

use Illuminate\Support\Facades\Cache;
use JasonXt\LaraSetting\Models\Settings;

/**
 * User: Monster
 * Date: 2018/3/2
 * Time: 16:14
 */
class Setting
{
    private $cacheEnabled = false;

    private $cachePrefix = '';

    private $cacheTTL = 60;

    private $runtimeCacheEnabled = true;

    private $runtimeCache = [];


    function __construct()
    {
        $this->cacheEnabled = config('lara-setting.cache.enable') ? true : false;
        $this->cachePrefix = config('lara-setting.cache.prefix');
        $this->cacheTTL = config('lara-setting.cache.ttl', 60);

        $this->runtimeCacheEnabled = config('lara-setting.runtime') ? true : false;

    }


    /**
     * Get setting from cache or db by full key
     *
     * @param $key
     * @param bool $update
     * @return mixed|null
     */
    public function get($key, $update = false)
    {
        try {
            list($group, $k) = $this->prepareKey($key);

            //try to get from the runtime cache
            if ($update) {
                //Load from db  and update  cache
                return $this->loadFromDB($group, $k);
            } else {
                if ($this->runtimeCacheEnabled && isset($this->runtimeCache[$group][$k])) {
                    return $this->runtimeCache[$group][$k];
                }

                $cacheKey = $this->cacheKey($group, $k);
                //load from framework's cache
                $value = Cache::get($cacheKey, null);

                if (is_null($value)) {
                    return $this->loadFromDB($group, $k);
                }
                if ($this->runtimeCacheEnabled) {
                    $this->runtimeCache[$group][$k] = $value;
                }
                return $value;
            }
        } catch (\Exception $exception) {
            return null;
        }
    }


    public function loadFromDB($group, $key)
    {
        try {
            $model = Settings::where(compact('group', 'key'))->first();
            // get the value  save to cache
            if (empty($model)) throw  new  \Exception();
            $value = $model->getAttributeValue('value');

            //can not cache null value it will load from db every time
            if (is_null($value)) return null;

            if ($this->cacheEnabled) {
                $cacheKey = $this->cacheKey($group, $key);
                Cache::put($cacheKey, $value, $this->cacheTTL);
            }

            if ($this->runtimeCacheEnabled) {
                $this->runtimeCache[$group][$key] = $value;
            }

            return $value;
        } catch (\Exception $exception) {
            //record do not exist or ..
            return null;
        }
    }

    /**
     * Update  or create  setting  by  full key
     *
     * @param $key
     * @param $value
     * @return bool
     */
    public function set($key, $value)
    {
        try {

            list($group, $k) = $this->prepareKey($key);

            $model = Settings::query()->firstOrNew(['group' => $group, 'key' => $k]);
            $model->setAttribute('value', $value);
            $model->save();

            //update runtime cache
            if ($this->runtimeCacheEnabled) $this->runtimeCache[$group][$k] = $value;

            //update framework's cache
            if ($this->cacheEnabled) Cache::put($this->cacheKey($group, $k), $value, $this->cacheTTL);

            return true;

        } catch (\Exception $exception) {
            return false;
        }
    }
    

    /**
     * Remove one setting from db and cache
     *
     * @param $fKey
     * @return bool
     */
    public function forget($fKey)
    {
        try {
            list($group, $key) = $this->prepareKey($fKey);

            $model = Settings::query()->where(compact('group', 'key'))->firstOrFail();

            $model->delete();

            unset($this->runtimeCache[$group][$key]);
            Cache::forget($this->cacheKey($group, $key));

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Parse full key  to  group and key in db
     *
     * @param $key
     * @return array
     * @throws \Exception
     */
    private function prepareKey($key)
    {
        list($group, $k) = explode('.', $key);
        //check the key
        if (empty($group) || empty($k)) throw new \Exception("Setting Key is wrong");

        return [$group, $k];
    }

    public function clearRuntimeCache()
    {
        $this->runtimeCache = [];
    }

    public function enableRuntimeCache()
    {
        $this->runtimeCacheEnabled = true;
    }

    public function disableRuntimeCache()
    {
        $this->runtimeCacheEnabled = false;
        $this->clearRuntimeCache();
    }


    private function cacheKey($group, $key)
    {
        return $this->cachePrefix . ':' . $group . ':' . $key;
    }
}