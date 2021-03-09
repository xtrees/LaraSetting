<?php
/**
 * Created by PhpStorm.
 * User: God
 * DateTime: 2019/12/1811:04 AM
 */

namespace Xtrees\LaraSetting\Http;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Xtrees\LaraSetting\Models\SettingGroup;
use Xtrees\LaraSetting\Models\SettingModel;

class LaraSettingController extends BaseController
{
    public function index(Request $request)
    {
        $group = $request->input('group');
        $groups = SettingGroup::query()->select(['id', 'title', 'key', 'order'])->orderBy('order')->get();

        $group = $groups->first(function ($g) use ($group) {
            return $g->key == $group;
        });

        if (empty($group)) {
            $group = $groups->first();
        }
        $settings = SettingModel::query()->where('group', object_get($group, 'key'))->orderBy('id')->get();

        return view('setting::index', compact('groups', 'group', 'settings'));
    }

    public function update(Request $request)
    {
        $data = $request->only(['group', 'key', 'value', 'eager']);
        $validator = \Validator::make($data, [
            'group' => 'required|alpha',
            'key' => 'required|alpha',
            'value' => 'required',
//            'eager'=>'required|boolean'
        ]);
        if ($validator->fails()) {
            return $this->error($validator->messages()->first());
        }
        try {
            $where = $request->only([
                'group', 'key'
            ]);
            $setting = SettingModel::query()->where($where)->firstOrFail();
            $setting->fill($data);
            $setting->save();

            $instance = app()->make('lara-setting');
            $instance->clearCache($setting->fullKey);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        return $this->success();
    }

    public function create(Request $request)
    {
        $data = $request->only(['group', 'title', 'key', 'type', 'value']);
        $validator = \Validator::make($data, [
            'group' => 'required|alpha',
            'key' => 'required|alpha',
            'title' => 'required',
            'value' => 'required',
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->messages()->first());
        }
        try {
            $setting = SettingModel::query()->firstOrNew([
                'group' => $data['group'],
                'key' => $data['key']
            ]);
            throw_if($setting->exists, new \Exception('Record exists'));

            $setting->fill($data);
            $setting->save();
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
        return $this->success();
    }

    public function createGroup(Request $request)
    {
        $data = $request->only(['title', 'key']);
        $validator = \Validator::make($data, [
            'title' => 'required',
            'key' => 'required|alpha'
        ]);
        if ($validator->fails()) {
            return $this->error($validator->messages()->first());
        }
        try {
            $g = SettingGroup::query()->firstOrCreate($data);
            throw_if(!$g->wasRecentlyCreated, new \Exception('Record exists!'));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
        return $this->success();
    }

    public function sortGroup(Request $request)
    {
        $data = $request->input('sort', []);

        $validator = \Validator::make($data, [
            'sort' => 'array',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->messages()->first());
        }
        try {
            $groups = SettingGroup::query()->get();
            foreach ($data as $index => $g) {
                $group = $groups->firstWhere('id', $g);
                if (empty($group)) {
                    continue;
                }
                $group->order = $index;
                $group->save();
            }
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
        return $this->success();
    }


    public function success($code = 200)
    {
        return \Response::json(['code' => $code, 'msg' => 'success']);
    }

    public function error($msg = 'failed', $code = 400)
    {
        return \Response::json(['code' => $code, 'msg' => $msg]);
    }
}
