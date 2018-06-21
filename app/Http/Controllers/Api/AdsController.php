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
use Carbon\Carbon;
class AdsController extends ApiController
{
     
    private $ads_rules=array(
        'city_id' => 'required',
        'lat' => 'required',
        'lng' => 'required',
    );

    private $rules = [
        'form_type' => 'required|in:1,2,3,4',
        'category_id' => 'required',
        'country_id' => 'required',
        'city_id' => 'required',
        'title' => 'required',
        'details' => 'required',
        'lat' => 'required',
        'lng' => 'required',
        'email' => 'required',
        'mobile' => 'required'
    ];

    private $real_states_rules = [
        'price' => 'required',
        'area' => 'required',
        'property_type' => 'required',
        'rooms_number' => 'required',
        'baths_number' => 'required',
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
        'manufacturing_year' => 'required',
        'motion_vector' => 'required',
        'engine_capacity' => 'required',
        'propulsion_system' => 'required',
        'mileage' => 'required',
        'mileage_unit' => 'required',
        'fuel_type' => 'required'
    ];

    public function index(Request $request){
       
        $validator = Validator::make($request->all(), $this->ads_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json([], ['errors' => $errors], 400);
        }
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        $columns = ['ads.id','ads.rate','ads.special','ads.created_at',DB::raw($this->iniDiffLocations('ads', $lat, $lng)),'categories.form_type','users.name'];

        $ads = Ad::join('categories','ads.category_id','=','categories.id')
                   ->join('users','users.id','=','ads.user_id')
                   ->where('ads.active',true)
                   ->where('ads.city_id',$request->input('city_id'));

            if ($request->input('category_id')) {
                $ads->where('ads.category_id',$request->input('category_id'));
            }
             
            $options = new \stdClass();
            if ($request->input('filter')) {
                $options = json_decode($request->input('filter'));
            }
        //show [1 => special ,2 => added today ,3  => only address ,4 => contain images ,5 => near to me]
            if (isset($options->show)) {
               if (in_array(1,$options->show)) {
                $ads->where('ads.special',1);
               }else if (in_array(2,$options->show)) {
                $ads->where('ads.created_at',Carbon::today());
               }
               else if (in_array(3,$options->show)) {
                  $ads->whereNull('ads.images');
               }
               else if (in_array(4,$options->show)) {
                  $ads->whereNotNull('ads.images');
               }
               else if (in_array(5,$options->show)) {
                  $ads->orderBy('distance');
               }
            }

            if ($request->input('form_type')) {
                // real states
                if ($request->input('form_type') == 1) {

                   $ads->join('real_states_ads','real_states_ads.ad_id','=','ads.id');
                   if (isset($options->property_type)) {
                       $ads->whereIn('real_states_ads.property_type_id',$options->property_type);
                   }
                   if (isset($options->rooms_number)) {

                       $ads->whereBetween('real_states_ads.rooms_number',[$options->rooms_number[0],$options->rooms_number[1]]);
                   }
                    if (isset($options->baths_number)) {
                       $ads->whereBetween('real_states_ads.baths_number',[$options->baths_number[0],$options->baths_number[1]]);
                   }
                   if (isset($options->is_furnished)) {
                       $ads->where('real_states_ads.is_furnished',$options->is_furnished);
                   }
                   if (isset($options->has_parking)) {
                       $ads->where('real_states_ads.has_parking',$options->has_parking);
                   }
                   if (isset($options->area)) {
                       $ads->whereBetween('real_states_ads.area',[$options->area[0],$options->area[1]]);
                   }
                   if (isset($options->price)) {
                       $ads->whereBetween('ads.price',[$options->price[0],$options->price[1]]);
                   }
                   
                }// lands
                else if ($request->input('form_type') == 2){
                    $ads->join('lands_ads','lands_ads.ad_id','=','ads.id');
                    if (isset($options->area)) {
                       $ads->whereBetween('lands_ads.area',[$options->area[0],$options->area[1]]);
                    }
                    if (isset($options->price)) {
                       $ads->whereBetween('ads.price',[$options->price[0],$options->price[1]]);
                    }
                }// cars
                else if ($request->input('form_type') == 3){

                    $ads->join('vehicles_ads','vehicles_ads.ad_id','=','ads.id');
                    if (isset($options->status)) {
                       $ads->where('vehicles_ads.status',$options->status);
                    }
                    if (isset($options->price)) {
                       $ads->whereBetween('ads.price',[$options->price[0],$options->price[1]]);
                    }
                    if (isset($options->manufacturing_year)) {
                       $ads->whereBetween('vehicles_ads.manufacturing_year',[$options->manufacturing_year[0],$options->manufacturing_year[1]]);
                    }
                    if (isset($options->motion_vector)) {
                       $ads->whereIn('vehicles_ads.motion_vector_id',$options->motion_vector);
                    }
                    if (isset($options->engine_capacity)) {
                       $ads->whereIn('vehicles_ads.engine_capacity_id',$options->engine_capacity);
                    }
                    if (isset($options->propulsion_system)) {
                       $ads->whereIn('vehicles_ads.propulsion_system_id',$options->propulsion_system);
                    }
                    if (isset($options->fuel_type)) {
                       $ads->whereIn('vehicles_ads.fuel_type_id',$options->fuel_type);
                    }
                }
                if (isset($options->category)) {
                    $ads->whereIn('ads.category_id',$options->category);
                }

                
            }
            
        
        $ads->select($columns);
        $ads = $ads->get();

        return _api_json($ads->toArray());
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
                $ad->category_id = $request->input('category_id');
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
            $ad->price = $request->input('price');

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
            
            // decreament available ads
            if($user->num_free_ads == 0){
                $avaliable_ads = UserPackage::where('user_id',$user->id)->where('available_of_ads','!=',0)->where('status',1)->first();
                if(!$avaliable_ads){
                    $message = ['message' => _lang('app.You_do_not_have_package_to_add_your_ad')];
                    return _api_json('', $message, 400);
                }
                $avaliable_ads->available_of_ads -= 1;
                $avaliable_ads->save();
            }else{
                $user->num_free_ads -= 1;
                $user->save();
            }
    }


    private function handleRealStateAdDetails($request,$ad_id)
    {
        $real_state_ad =  RealStateAd::where('ad_id',$ad_id)->first();
        if (!$real_state_ad) {
            $real_state_ad = new RealStateAd;
            $real_state_ad->ad_id = $ad_id;
        }
        $real_state_ad->area = $request->input('area');
        $real_state_ad->is_furnished = $request->input('is_furnished');
        $real_state_ad->has_parking = $request->input('has_parking');
        $real_state_ad->property_type_id = $request->input('property_type');
        $real_state_ad->rooms_number = $request->input('rooms_number');
        $real_state_ad->baths_number = $request->input('baths_number');
        
        $real_state_ad->save();

    }


    private function handleLandAdDetails($request,$ad_id)
    {
        $land_ad =  LandAd::where('ad_id',$ad_id)->first();
        if (!$land_ad) {
            $land_ad = new LandAd;
            $land_ad->ad_id = $ad_id;
        }
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
