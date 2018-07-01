<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Store;
use App\Models\StoreCtegory;
use App\Models\Device;
use DB;

class RegisterController extends ApiController {

    private $step_one_rules = array(
        'step' => 'required',
        'dial_code' => 'required'
    );
    private $rules = array(
        'step' => 'required',
        'name' => 'required',
        'username' => 'required|unique:users',
        'email' => 'email|unique:users',
        'dial_code' => 'required',
        'password' => 'required',
        'device_id' => 'required',
        'device_token' => 'required',
        'device_type' => 'required',
    );
    public function __construct() {
        parent::__construct();
    }

    public function register(Request $request) {

        if ($request->step == 1) {
            $rules = $this->step_one_rules;
        } else if ($request->step == 2) {
            $rules = $this->rules;
        } else {
            return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
        }
        $rules['mobile'] = "required|unique:users,mobile,NULL,id,dial_code,{$request->input('dial_code')}";
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        }
        if ($request->step == 1){
            $verification_code = Random(4);
            return _api_json(new \stdClass(), ['code' => $verification_code]);
        }elseif($request->step == 2){
            DB::beginTransaction();
            try {
                $user = $this->createUser($request);
                //dd($user);
                DB::commit();

                $token = new \stdClass();
                $token->id = $user->id;
                $token->expire = strtotime('+' . $this->expire_no . $this->expire_type);
                $token->device_id = $request->input('device_id');
                $expire_in_seconds = $token->expire;
                return _api_json(User::transform($user), ['token' => AUTHORIZATION::generateToken($token), 'expire' => $expire_in_seconds], 201);
            } catch (\Exception $e) {
                DB::rollback();
                $message = _lang('app.error_is_occured');
                return _api_json(new \stdClass(), ['message' => $e->getMessage()], 400);
            }
        } else {
            return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    private function createUser($request) {

        $user = new User;
        
        $settings = $this->settings();
        $num_free_ads = $settings['num_free_ads']->value;

        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->mobile = $request->input('mobile');
        $user->dial_code = $request->input('dial_code');
        $user->image = "default.png";
        $user->num_free_ads = $num_free_ads;
        $user->active = 1;
        $user->save();

        $device = new Device;
        $device->device_id = $request->input('device_id');
        $device->device_token = $request->input('device_token');
        $device->device_type = $request->input('device_type');
        $device->user_id = $user->id;
        $device->save();

        return $user;
    }
}
