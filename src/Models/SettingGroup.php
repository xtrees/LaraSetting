<?php
/**
 * User: Monster
 * Date: 2018/3/2
 * Time: 16:28
 */

namespace JasonXt\LaraSetting\Models;


use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';

    protected $fillable = ['group', 'key', 'value'];

}