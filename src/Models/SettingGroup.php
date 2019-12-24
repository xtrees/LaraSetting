<?php
/**
 * User: Monster
 * Date: 2018/3/2
 * Time: 16:28
 */

namespace Xtrees\LaraSetting\Models;


use Illuminate\Database\Eloquent\Model;

class SettingGroup extends Model
{
    protected $table = 'setting_groups';

    protected $fillable = ['title', 'key', 'order'];

}