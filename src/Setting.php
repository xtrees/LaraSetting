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

    const CACHE_BATCH = 'batch';
    const CACHE_SINGLE = 'single';

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

    protected $cacheMode = 'single';

    protected $batchCacheKey = 'batch';

    function __construct()
    {
        $this->cacheMode = config('lara-setting.cache.mode', 'single');
        $this->cacheEnabled = config('lara-setting.cache.enable') ? true : false;
        $this->cachePrefix = config('lara-setting.cache.prefix');
        $this->cacheTTL = config('lara-setting.cache.ttl', 60);
        //Only in BATCH mode ,should load all of the config from cache to runtime
        $this->loadToRunTime();
    }

    protected function getCache($key, $default = null)
    {
        if ($this->cacheEnabled) {
            return Cache::get($key, $default);
        }
        return null;
    }

    protected function setCache($key, $value)
    {
        if ($this->cacheEnabled) {
            return Cache::set($key, $value, $this->cacheTTL);
        }
        return false;
    }

    protected function forgetCache($key)
    {
        if ($this->cacheEnabled) {
            return Cache::forget($key);
        }
        return false;
    }

    /**
     *  Load all of the records from cache to runtime;
     *  for bacth mode
     */
    protected function loadToRunTime()
    {
        if ($this->cacheMode == self::CACHE_BATCH) {
            $cache = $this->getCache($this->batchKey());
            if (empty($cache)) {
                //no records in Cache ,load all from db
                $this->loadFromDB();
            } else {
                $this->runtimeCache = $cache;
            }
        }
    }

    protected function batchKey()
    {
        return $this->cachePrefix . $this->batchCacheKey;
    }

    /**
     * Get all or single records from db
     *
     * @param $group
     * @param $key
     * @return string|array
     */
    protected function loadFromDB($group = null, $key = null)
    {
        $builder = Settings::query()->select(['group', 'key', 'value']);
        if (!empty($group) && !empty($key)) {
            $builder->where(compact('group', 'key'));
        }
        $records = $builder->get();
        /** @var Settings $record */
        foreach ($records as $record) {
            $gr = $record->getAttribute('group');
            $ke = $record->getAttribute('key');
            $val = $record->getAttribute('value');
            //save to cache in SINGLE mode
            if ($this->cacheMode == self::CACHE_SINGLE) {
                $cacheKey = $this->cacheKey($gr, $ke);
                $this->setCache($cacheKey, $val);
            }
            //save to runtime
            $this->runtimeCache[$gr][$ke] = $val;
        }
        if ($this->cacheMode == self::CACHE_BATCH) {
            Cache::set($this->batchKey(), $this->runtimeCache, $this->cacheTTL);
        }

        if (!empty($group) && !empty($key)) {
            return array_get($this->runtimeCache, $group . '.' . $key);
        }
        return $this->runtimeCache;
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

            if ($update) {
                return $need = $this->loadFromDB($group, $k);
            } else {
                $need = array_get($this->runtimeCache, $key, null);
                if (!is_null($need)) return $need;
                //load from framework's cache
                if ($this->cacheEnabled) {
                    $cacheKey = $this->cacheKey($group, $k);
                    $need = $this->getCache($cacheKey, null);
                    if (!is_null($need)) return $need;
                }
                return $this->loadFromDB($group, $k);
            }
        } catch (\Exception $exception) {
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
            $this->runtimeCache[$group][$k] = $value;
            //update framework's cache
            if ($this->cacheMode == self::CACHE_BATCH) {
                $this->setCache($this->batchKey(), $this->runtimeCache);
            } else {
                $this->setCache($this->cacheKey($group, $k), $value);
            }
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
            $this->forgetCache($this->cacheKey($group, $key));
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

    private function cacheKey($group, $key)
    {
        return $this->cachePrefix . ':' . $group . ':' . $key;
    }
}
