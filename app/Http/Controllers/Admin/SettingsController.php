<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Setting;
use App\Models\SettingTranslation;
use DB;

class SettingsController extends BackendController {

    private $rules = array(
        'setting.num_free_ads' => 'required',
        'setting.phone' => 'required',
        'setting.email' => 'required',
        'setting.android_url' => 'required',
        'setting.ios_url' => 'required',
        'setting.social_media.facebook' => 'required',
        'setting.social_media.twitter' => 'required',
        'setting.social_media.google' => 'required',
        'setting.social_media.youtube' => 'required',
        'setting.rooms_range.from' => 'required',
        'setting.rooms_range.to' => 'required',
        'setting.baths_range.from' => 'required',
        'setting.baths_range.to' => 'required',
        'setting.manufacturing_year_start' => 'required',
    );

    public function index() {

        $this->data['settings'] = Setting::get()->keyBy('name');
        $this->data['settings_translations'] = SettingTranslation::get()->keyBy('locale');
        if (isset($this->data['settings']['social_media'])) {
            $this->data['settings']['social_media'] = json_decode($this->data['settings']['social_media']->value);
            $this->data['settings']['rooms_range'] = isset($this->data['settings']['rooms_range']) ? json_decode($this->data['settings']['rooms_range']->value) : '';
            $this->data['settings']['baths_range'] = isset($this->data['settings']['baths_range']) ? json_decode($this->data['settings']['baths_range']->value) : '';
        }
        return $this->_view('settings/index', 'backend');
    }

    public function store(Request $request) {

        $columns_arr = array(
            'about_us' => 'required',
            'policy' => 'required',
        );

        $this->rules = array_merge($this->rules, $this->lang_rules($columns_arr));
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {

            DB::beginTransaction();
            try {
                $setting = $request->input('setting');

                $data_update = [];


                foreach ($setting as $key => $value) {
                    if ($key == 'social_media' || $key == 'rooms_range' || $key == 'baths_range') {
                        $value = json_encode($value);
                    }
                    $data_update['value'][] = [
                        'value' => $value,
                        'cond' => [['name', '=', "'$key'"]],
                    ];
                }
                $this->updateValues('\App\Models\Setting', $data_update, true);
                $about_us = $request->input('about_us');
                $policy = $request->input('policy');
                foreach ($this->languages as $key => $value) {
                    SettingTranslation::updateOrCreate(
                            ['locale' => $key], ['locale' => $key, 'policy' => $policy[$key], 'about_us' => $about_us[$key]]
                    );
                }

                DB::commit();
                return _json('success', _lang('app.updated_successfully'));
            } catch (\Exception $ex) {
                DB::rollback();
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function store2(Request $request) {

        $columns_arr = array(
            'about_us' => 'required',
            'policy' => 'required',
        );

        $this->rules = array_merge($this->rules, $this->lang_rules($columns_arr));
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {

            DB::beginTransaction();
            try {
                $setting = $request->input('setting');

                foreach ($setting as $key => $value) {
                    if ($key == 'social_media' || $key == 'rooms_range' || $key == 'baths_range') {
                        Setting::updateOrCreate(['name' => $key], ['value' => json_encode($value)]);
                    } else {
                        Setting::updateOrCreate(['name' => $key], ['value' => $value]);
                    }
                }
                $about_us = $request->input('about_us');
                $policy = $request->input('policy');
                foreach ($this->languages as $key => $value) {
                    SettingTranslation::updateOrCreate(
                            ['locale' => $key], ['locale' => $key, 'policy' => $policy[$key], 'about_us' => $about_us[$key]]
                    );
                }
                DB::commit();
                return _json('success', _lang('app.updated_successfully'));
            } catch (\Exception $ex) {
                DB::rollback();
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

}
