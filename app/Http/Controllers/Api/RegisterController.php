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

    private $client_rules_step_one = array(
        'step' => 'required',
        'mobile' => 'required|unique:users',
    );
    private $client_rules = array(
        'step' => 'required',
        'name' => 'required',
        'username' => 'required|unique:users',
        'email' => 'email|unique:users',
        'mobile' => 'required|unique:users',
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
            $rules = $this->client_rules_step_one;
        } else if ($request->step == 2) {
            $rules = $this->client_rules;
        } else {
            return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
        }

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

        $User = new User;
        $active_user=1;
        $settings = $this->settings();
        $num_free_ads = $settings['num_free_ads']->value;

        $User->username = $request->input('username');
        $User->email = $request->input('email');
        $User->password = bcrypt($request->input('password'));
        $User->mobile = $request->input('mobile');
        $User->name = $request->input('name');
        $User->image = "default.png";
        $User->num_free_ads = $num_free_ads;
        $User->active = $active_user;
        $User->save();
        $Device = new Device;
        $Device->device_id = $request->input('device_id');
        $Device->device_token = $request->input('device_token');
        $Device->device_type = $request->input('device_type');
        $Device->user_id = $User->id;
        $Device->save();
        return $User;
    }
}
