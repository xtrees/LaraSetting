<?php
/**
 * User: Monster
 * Date: 2018/3/2
 * Time: 16:28
 */

namespace JasonXt\LaraSetting\Models;


use Illuminate\Database\Eloquent\Model;

class SettingGroup extends Model
{
    protected $table = 'setting_group';

    protected $fillable = ['name', 'key', 'order'];

}