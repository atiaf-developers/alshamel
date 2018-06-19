<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Ad;
use App\Models\Category;
use App\Models\UserPackage;
use App\Models\RealStateAd;
use App\Models\LandAd;
use App\Models\VechileAd;
use Validator;
use DB;
class AdsController extends ApiController
{
     
    private $s_rules=array(
        'city_id' => 'required',
        'lat' => 'required',
        'lng' => 'required',
    );

    private $rules = [
        'form_type' => 'required|in:1,2,3,4',
        'category_one_id' => 'required',
        'category_two_id' => 'required',
        'country_id' => 'required',
        'city_id' => 'required',
        'title' => 'required',
        'details' => 'required',
        'lat' => 'required',
        'lng' => 'required',
        'email' => 'required',
        'mobile' => 'required',
        'images' => 'required'
    ];

    private $real_states_rules = [
        'price' => 'required',
        'area' => 'required',
        'property_type' => 'required',
        'rooms_count' => 'required',
        'baths_count' => 'required',
        'is_furnished' => 'required',
        'has_parking' => 'required'
    ];

    private $land_rules = [
        'price' => 'required',
        'area' => 'required'
    ];


    private $vehicle_rules = [
        'price' => 'required',
        'status' => 'required',
        'category_three_id' => 'required',
        'manufacturing_year' => 'required',
        'motion_vector' => 'required',
        'engine_capacity' => 'required',
        'propulsion_system' => 'required',
        'mileage' => 'required',
        'mileage_unit' => 'required',
        'fuel_type' => 'required'
    ];

    public function index(Request $request){
       
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json([], ['errors' => $errors], 400);
        }
        return _api_json(Ad::get_all($request));
    }


    public function store(Request $request){
      try {
            if (!$request->input('form_type')) {
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
            if ($request->input('form_type') == 1) {
               $validation_rules = $this->real_states_rules;
            }else if($request->input('form_type') == 2){
                $validation_rules = $this->land_rules;
            }
            else if($request->input('form_type') == 3){
                $validation_rules = $this->vehicle_rules;
            }
           
            $rules = in_array($request->input('form_type'),[1,2,3]) ? array_merge($validation_rules, $this->rules) : $this->rules;

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            }

            $user=$this->auth_user();
            if($user->num_free_ads == 0){
                $avaliable_ads = UserPackage::where('user_id',$user->id)->where('available_of_ads','!=',0)->where('status',1)->first();
                if(!$avaliable_ads){
                    $message = ['message' => _lang('app.You_do_not_have_package_to_add_your_ad')];
                    return _api_json('', $message, 400);
                }
            }

            DB::beginTransaction();
            try{
                $this->handleAd($request);
                DB::commit();
                return _api_json('',['message'=>_lang('app.added_successfully')]);
            }catch(\Exception $ex){
                DB::rollback();
                return _api_json('', ['message'=> _lang('app.error_is_occured')], 400);
            }
          
      } catch (\Exception $e) {
        return _api_json('', ['message'=> _lang('app.error_is_occured')], 400);
      }
       
    }


    public function update(Request $request,$id)
    {
        try {
            $user = $this->auth_user();
            $ad = Ad::where('id',$id)->where('user_id',$user->id)->first();
            if (!$ad) {
                $message = ['message' => _lang('app.not_found')];
                return _api_json('', $message, 404);
            }
            if (!$request->input('form_type')) {
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
            if ($request->input('form_type') == 1) {
               $validation_rules = $this->real_states_rules;
            }else if($request->input('form_type') == 2){
                $validation_rules = $this->land_rules;
            }
            else if($request->input('form_type') == 3){
                unset($this->vehicle_rules['category_three_id']);
                $validation_rules = $this->vehicle_rules;
            }
           
           unset($this->rules['category_one_id'],$this->rules['category_two_id']);
            $rules = in_array($request->input('form_type'),[1,2,3]) ? array_merge($validation_rules, $this->rules) : $this->rules;

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            }
            $user=$this->auth_user();
            DB::beginTransaction();
            try{
                $this->handleAd($request,$ad);
                DB::commit();
                return _api_json('',['message'=>_lang('app.updated_successfully')]);
            }catch(\Exception $ex){
                DB::rollback();
                return _api_json('', ['message'=> _lang('app.error_is_occured')], 400);
            }
        } catch (\Exception $e) {
            return _api_json('', ['message'=> _lang('app.error_is_occured')], 400);
        }  
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $user = $this->auth_user();
            $ad = Ad::where('id',$id)->where('user_id',$user->id)->first();
            if (!$ad) {
                $message = ['message' => _lang('app.not_found')];
                return _api_json('', $message, 404);
            }
            $ad->delete();
            DB::commit();
            return _api_json('',['message'=>_lang('app.deleted_successfully')]);
        }catch(\Exception $e){
            DB::rollback();
            return _api_json('', ['message'=> _lang('app.error_is_occured')], 400);
        }
    }
   

    private function handleAd($request,$ad = null)
    {
            $user = $this->auth_user();
            if (!$ad) {
                $ad= new Ad;
                $ad->category_one_id = $request->input('category_one_id');
                $ad->category_two_id = $request->input('category_two_id');
                if($request->input('category_three_id')){
                    $ad->category_three_id=$request->input('category_three_id');  
                }
                $ad->user_id = $user->id;
            }

            $ad->country_id = $request->input('country_id');
            $ad->city_id = $request->input('city_id');
            $ad->title = $request->input('title');
            $ad->details = $request->input('details');
            $ad->lat = $request->input('lat');
            $ad->lng = $request->input('lng');
            $ad->email = $request->input('email');
            $ad->mobile = $request->input('mobile');

            if(isset($request->images) && !empty($request->images)){
                if ($ad->images) {
                    foreach (json_decode($ad->images) as $image) {
                         Ad::deleteUploaded('ads', $image);
                    }
                }
                $images = [];
                foreach (json_decode($request->images) as $image) {
                    $image = preg_replace("/\r|\n/", "", $image);
                    if (!isBase64image($image)) {
                        continue;
                    }
                    $images[] = Ad::upload($image, 'ads', true, false, true);
                }
                $ad->images = json_encode($images);
            }

            $ad->save();

            if(in_array($request->input('form_type'),[1,2,3])){

                if($request->input('form_type')==1){
                    $this->handleRealStateAdDetails($request,$ad->id);
                }elseif($request->input('form_type')==2){
                    $this->handleLandAdDetails($request,$ad->id);
                }elseif($request->input('form_type')==3){
                    $this->handleVechileAdDetails($request,$ad->id);
                }
            }
    }


    private function handleRealStateAdDetails($request,$ad_id)
    {
        $real_state_ad =  RealStateAd::where('ad_id',$ad_id)->first();
        if (!$real_state_ad) {
            $real_state_ad = new RealStateAd;
            $real_state_ad->ad_id = $ad_id;
        }
        $real_state_ad->price = $request->input('price');
        $real_state_ad->area = $request->input('area');
        $real_state_ad->is_furnished = $request->input('is_furnished');
        $real_state_ad->has_parking = $request->input('has_parking');
        $real_state_ad->property_type_id = $request->input('property_type');
        $real_state_ad->rooms_id = $request->input('rooms_count');
        $real_state_ad->bathes_id = $request->input('baths_count');
        
        $real_state_ad->save();

    }


    private function handleLandAdDetails($request,$ad_id)
    {
        $land_ad =  LandAd::where('ad_id',$ad_id)->first();
        if (!$land_ad) {
            $land_ad = new LandAd;
            $land_ad->ad_id = $ad_id;
        }
        $land_ad->price = $request->input('price');
        $land_ad->area = $request->input('area');
        
        $land_ad->save();
    }


    private function handleVechileAdDetails($request,$ad_id)
    {
        $vehicle_ad =  VechileAd::where('ad_id',$ad_id)->first();
        if (!$vehicle_ad) {
            $vehicle_ad = new VechileAd;
            $vehicle_ad->ad_id = $ad_id;
        }
        $vehicle_ad->price = $request->input('price');
        $vehicle_ad->status = $request->input('status');
        $vehicle_ad->manufacturing_year = $request->input('manufacturing_year');
        $vehicle_ad->motion_vector_id = $request->input('motion_vector');
        $vehicle_ad->engine_capacity_id = $request->input('engine_capacity');
        $vehicle_ad->propulsion_system_id = $request->input('propulsion_system');
        $vehicle_ad->fuel_type_id = $request->input('fuel_type');
        $vehicle_ad->mileage_id = $request->input('mileage');
        $vehicle_ad->mileage_unit = $request->input('mileage_unit');

        $vehicle_ad->save();
    }

}
