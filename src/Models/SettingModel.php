<?php
/**
 * User: Monster
 * Date: 2018/3/2
 * Time: 16:28
 */

namespace Xtrees\LaraSetting\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SettingModel
 * @method Builder  eager()
 * @package Xtrees\LaraSetting\Models
 */
class SettingModel extends Model
{
    protected $table = 'settings';

    protected $fillable = ['title', 'group', 'key', 'value', 'eager'];


    protected $casts =[
        'eager'=>'boolean',
    ];

    public function scopeEager(Builder $query)
    {
        return $query->where('eager', true);
    }

    public function getFullKeyAttribute()
    {
        $group = $this->getAttribute('group');
        $key = $this->getAttribute('key');

        if (empty($group) || empty($key)) {
            return false;
        }

        return $group . '.' . $key;
    }

    public function getValueAttribute($val)
    {
        $type = $this->getAttribute('type');

        if ($type == 'tag') {
            return empty($val) ? [] : explode(',', $val);
        }
        return $val;
    }
}
