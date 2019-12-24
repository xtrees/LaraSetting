<?php

namespace Xtrees\LaraSetting;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Xtrees\LaraSetting\Models\SettingModel;

/**
 * User: Monster
 * Date: 2018/3/2
 * Time: 16:14
 */
class Setting
{
    protected $cacheEnabled = false;
    /**
     *  Cache Key prefix
     *
     * @var  string
     */
    protected $cachePrefix = '';
    /**
     *  Cache TTL (minutes)
     *
     * @var int
     */
    protected $cacheTTL = 60;
    /**
     * Store setiings during life time
     *
     * @var array
     */
    protected $runtimeCache = [];


    public function __construct()
    {
        $this->cacheEnabled = config('lara-setting.cache.enable') ? true : false;
        $this->cachePrefix = config('lara-setting.cache.prefix');
        $this->cacheTTL = config('lara-setting.cache.ttl', 60);

        $this->eager();
    }

    protected function eager()
    {
        $key = $this->cacheKey('eager');
        if ($this->cacheEnabled) {
            //query from cache
            $cache = Cache::get($key, null);
            if (!is_null($cache)) {
                $this->runtime($cache);
                return true;
            }
        }
        $settings = SettingModel::query()->eager()->get();

        $data = $this->build($settings);
        $this->runtime($data);

        if ($this->cacheEnabled) {
            //put to cache
            Cache::put($key, $data, $this->cacheTTL);
        }
        return true;
    }

    protected function single($fKey)
    {
        try {
            list($group, $key) = $this->parseKey($fKey);
            $setting = SettingModel::query()->where([
                'group' => $group,
                'key' => $key
            ])->firstOrFail();
        } catch (\Exception $e) {
            return null;
        }

        $val = $setting->getAttribute('value');
        $data = [
            $fKey => $val
        ];
        $this->runtime($data);

        if ($this->cacheEnabled) {
            $ck = $this->cacheKey($fKey);
            Cache::put($ck, $val, $this->cacheTTL);
        }
        return $val;
    }

    protected function cacheKey($key = '')
    {
        return $this->cachePrefix . $key;
    }

    public function clearCache($fKey)
    {
        if ($this->cacheEnabled) {
            $ck = $this->cacheKey($fKey);
            Cache::forget($ck);
            $ck = $this->cacheKey('eager');
            Cache::forget($ck);
        }
    }

    /**
     * @param $key
     * @return array
     * @throws \Exception
     */
    protected function parseKey($key)
    {
        list($group, $k) = explode('.', $key);
        //check the key
        if (empty($group) || empty($k)) throw new \Exception("Setting Key is wrong");
        return [$group, $k];
    }

    protected function build($settings)
    {
        $data = [];
        /** @var SettingModel $setting */
        foreach ($settings as $setting) {
            $ck = $setting->getAttribute('fullKey');
            $val = $setting->getAttribute('value');
            $data[$ck] = $val;
        }
        return $data;
    }

    protected function runtime($cf = [])
    {
        foreach ($cf as $ck => $cv) {
            Arr::set($this->runtimeCache, $ck, $cv);
        }
    }

    public function get($key)
    {
        //runtime
        $run = Arr::get($this->runtimeCache, $key, null);
        if (!is_null($run)) return $run;
        //cache
        if ($this->cacheEnabled && !is_null($ca = Cache::get($this->cacheKey($key)))) {
            return $ca;
        }
        //db
        $result = $this->single($key);

        return $result;
    }

    public function set($fKey, $val)
    {
        try {
            list($group, $key) = $this->parseKey($fKey);
            $setting = SettingModel::query()->where([
                'group' => $group,
                'key' => $key
            ])->firstOrFail();
        } catch (\Exception $e) {
            return false;
        }

        $setting->setAttribute('value', $val);
        $setting->save();
        $this->runtime([$fKey => $val]);

        if ($this->cacheEnabled) {
            $ck = $this->cacheKey($key);
            Cache::put($ck, $val, $this->cacheTTL);
        }
        return true;
    }
}
