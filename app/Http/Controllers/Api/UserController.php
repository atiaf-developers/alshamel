<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Device;
use App\Models\Ad;
use App\Models\Favourite;
use App\Models\AdReport;
use Validator;
use DB;

class UserController extends ApiController {

    private $rate_rules = array(
        'ad_id' => 'required',
        'rate' => 'required',
    );

    private $report_rules = array(
        'ad_id' => 'required',
        'type' => 'required|in:1,2,3',
    );
    

    private $favourite_rules = array(
        'ad_id' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

    protected function update(Request $request) {
        $user = $this->auth_user();

        $rules = array();

        if ($request->input('name')) {
            $rules['name'] = "required";
        }
        if ($request->input('username')) {
            $rules['username'] = "required|unique:users,username,$user->id";
        }
        if ($request->input('email')) {
            $rules['email'] = "required|email|unique:users,email,$user->id";
        }
        if ($request->input('mobile')) {
            $rules['step'] = "required";
            $rules['dial_code'] =  "required";
            $rules['mobile'] =  "required|unique:users,mobile,$user->id,id,dial_code,{$request->input('dial_code')}";
        }

        if ($request->input('password')) {
            $rules['password'] = "required";
        }

        if ($request->input('image')) {
            $rules['image'] = "required";
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        } 

        DB::beginTransaction();
        try {
            if ($request->input('name')) {
                $user->name = $request->input('name');
            }
            if ($request->input('username')) {
                $user->username = $request->input('username');
            }
            if ($request->input('email')) {
                $user->email = $request->input('email');
            }
            if ($request->input('mobile')) {
                if ($request->step == 1) {
                    $verification_code = Random(4);
                    return _api_json(new \stdClass(), ['code' => $verification_code]);
                }else if ($request->step == 2){
                    $user->mobile = $request->input('mobile');
                    $user->dial_code = $request->input('dial_code');
                }else{
                    $message = _lang('app.error_is_occured');
                    return _api_json(new \stdClass(), ['message' => $message], 400);
                }
            }
            if ($password = $request->input('password')) {
                $user->password = bcrypt($request->input('password')); 
            }
            if ($image=$request->input('image')) {
                $image = preg_replace("/\r|\n/", "", $image);
                if ($user->image != 'default.png') {
                    User::deleteUploaded('users', $user->image);
                }
                if (isBase64image($image)) {
                    $user->image = User::upload($image, 'users', true, false, true);
                }
            }
            $user->save();
            $user = User::transform($user);
            DB::commit();
            return _api_json($user, ['message' => _lang('app.updated_successfully')]);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json(new \stdClass(), ['message' => $message], 400);
        }

    }

    public function getUser()
    {
        try {
            $user = User::transform($this->auth_user());
            return _api_json($user);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json(new \stdClass(), ['message' => $message], 400);
        }
    }

    public function rate(Request $request) {
        $validator = Validator::make($request->all(), $this->rate_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        }
        
        $user = $this->auth_user();
        $ad = Ad::find($request->input('ad_id'));
        if (!$ad) {
            $message = _lang('app.not_found');
            return _api_json('', ['message' => $message], 404);
        }
        DB::beginTransaction();
        try {
            $comment = $request->input('comment') ? $request->input('comment') : "";
            $this->new_rate($ad->id, $request->rate, $user->id, $comment);
            $ad->rate = $this->countRates($ad->id);
            $ad->save();
            DB::commit();
            $message = _lang('app.rated_successfully');
            return _api_json('', ['message' => $message]);
        } catch (\Exception $e) {
            DB::rollback();

            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    public function handleFavourites(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), $this->favourite_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            } 

            $user = $this->auth_user();
            $Ad = Ad::find($request->input('ad_id'));
            if (!$Ad) {
                $message = _lang('app.not_found');
                return _api_json('', ['message' => $message], 404);
            }

            $check = Favourite::where('ad_id',$request->input('ad_id'))
            ->where('user_id',$user->id)
            ->first();

            if ($check) {
                $check->delete();
                $message = _lang('app.ad_has_been_deleted_from_favorites');

            }
            else{
                $favourite = new Favourite;
                $favourite->ad_id = $request->input('ad_id');
                $favourite->user_id = $user->id;
                $favourite->save();
                $message = _lang('app.ad_has_been_added_to_favorites');
            }
            return _api_json('',['message' => $message]);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message],400);
        }
    }



    public function reportAd(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), $this->report_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            } 

            $ad = Ad::find($request->input('ad_id'));
            if (!$ad) {
                $message = _lang('app.not_found');
                return _api_json('', ['message' => $message], 404);
            }
            
            $user = $this->auth_user();
            $report = new AdReport;
            $report->user_id = $user->id;
            $report->ad_id = $request->input('ad_id');
            $report->type = $request->input('type');
            $report->save();

            $message = _lang('app.ad_was_reported');
            return _api_json('', ['message' => $message]);
            
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message],400);
        }
        
    }


    public function favourites(Request $request) {
        try {
            $user = $this->auth_user();

            $favourites = Product::Join('favourites', function ($join) use($user) {
                $join->on('favourites.product_id', '=', 'products.id');
                $join->where('favourites.user_id', $user->id);    
            }) 
            ->join('stores', 'stores.id', '=', 'products.store_id')
            ->where('stores.active',true)
            ->select("products.id",'products.name','products.description','products.images','products.quantity',
                'products.price',"favourites.id as is_favourite","stores.id as store_id","stores.name as store_name","stores.image as store_image","stores.rate as store_rate","stores.available as store_available")
            ->paginate($this->limit);

            return _api_json(Product::transformCollection($favourites));
        } catch (\Exception $e) {
            $message = ['message' => _lang('app.error_occured')];
            return _api_json([], $message, 400);
        }
    }

    public function logout(Request $request) {
        Device::where('user_id', $this->auth_user()->id)->where('device_id', $request->input('device_id'))->update(['device_token'=>'']);
        return _api_json(new \stdClass(), array(), 201);
    }

}
