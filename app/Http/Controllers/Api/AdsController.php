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
     
    private $ads_rules =array(
        'city_id' => 'required',
        'lat' => 'required',
        'lng' => 'required',
    );
    
    private $ad_rules =array(
        'form_type' => 'required|in:1,2,3,4',
        'lat' => 'required',
        'lng' => 'required',
    );


    private $favourites_rules =array(
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
        $rules = array();
       if (!$request->input('options')){
         $rules = $this->ads_rules;
       }else if ($request->input('options') && $request->input('options') == 2) {
         $rules =  $this->favourites_rules;
       }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json([], ['errors' => $errors], 400);
        }
        $ads = $this->getAds($request);
        return _api_json($ads);
    }

     public function show(Request $request,$id){
       
        $validator = Validator::make($request->all(), $this->ad_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        }

        $ad = $this->getAds($request,$id);
        if (!$ad) {
             return _api_json(new \stdClass(), ['message' => _lang('app.not_found')], 404);
        }
        return _api_json($ad);
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
                $validation_rules = $this->vehicle_rules;
            }
           
           unset($this->rules['category_id']);
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
                $ad->active = true;
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


    private function getAds($request,$id = null)
    {
        $user = $this->auth_user();

        $columns = ['ads.id','ads.lat','ads.lng','ads.title','ads.rate','ads.special','ads.created_at','ads.price','ads.mobile','ads.email','categories.form_type','users.name','locations_translations.title as city','ads.details','ads.images'];

        $ads = Ad::join('categories','ads.category_id','=','categories.id')
                 ->join('locations','ads.city_id','=','locations.id')
                 ->join('locations_translations', 'locations.id', '=', 'locations_translations.location_id')
                 ->join('users','users.id','=','ads.user_id')
                 ->where('ads.active',true) 
                 ->where('locations_translations.locale',$this->lang_code);
            if ($id) {
                $ads->where('ads.id',$id);
            } 
            if ($user) {

                if ($request->input('options')) {

                   if ($request->input('options') == 1) {
                       $ads->where('ads.user_id',$user->id);

                   }else if($request->input('options') == 2){
                       $ads->join('favourites',function($join) use($user){
                            $join->on('favourites.ad_id','=','ads.id')
                               ->where('favourites.user_id',$user->id);
                        });
                    }
                }

                else{
                    $ads->leftJoin('favourites',function($join) use($user){
                          $join->on('favourites.ad_id','=','ads.id')
                               ->where('favourites.user_id',$user->id);
                        });
                   
                }
                $columns[] = "favourites.id as is_favourite";
            }

            
            if ($request->input('city_id')) {
                $ads->where('ads.city_id',$request->input('city_id'));
            }

            if ($request->input('lat') && $request->input('lng')) {
                $lat = $request->input('lat');
                $lng = $request->input('lng');
                $columns[] = DB::raw($this->iniDiffLocations("ads", $lat, $lng));
            }

            $options = new \stdClass();

            if ($request->input('filter')) {
                $options = json_decode($request->input('filter'));
            }

            if ($request->input('category_id') && !isset($options->category)) {
                $ads->where('ads.category_id',$request->input('category_id'));
            }else if (isset($options->category)) {
                $ads->whereIn('ads.category_id',$options->category);
            }

            //show [1 => special ,2 => added today ,3  => only address ,4 => contain images ,5 => near to me]
            if (isset($options->show)) {
               if (in_array(1,$options->show)) {
                $ads->where('ads.special',1);
               } if (in_array(2,$options->show)) {
                $ads->where('ads.created_at','>=',Carbon::today());
               }
                if (in_array(3,$options->show)) {
                  $ads->whereNull('ads.images');
               }
                if (in_array(4,$options->show)) {
                  $ads->whereNotNull('ads.images');
               }
                if (in_array(5,$options->show)) {
                  $ads->orderBy('distance');
               }
            }

            if ($request->input('form_type')) {
                // real states
                if ($request->input('form_type') == 1) {
                  
                   $ads->join('real_states_ads','real_states_ads.ad_id','=','ads.id');
                   $ads->join('basic_data','real_states_ads.property_type_id','=','basic_data.id');
                   $ads->join('basic_data_translations as trans', 'basic_data.id', '=', 'trans.basic_data_id');
                   $ads->where('trans.locale',$this->lang_code);

                   

                    $columns[] = "real_states_ads.area";
                    $columns[] = "real_states_ads.property_type_id";
                    $columns[] = "real_states_ads.rooms_number";
                    $columns[] = "real_states_ads.baths_number";
                    $columns[] = "real_states_ads.is_furnished";
                    $columns[] = "real_states_ads.has_parking";
                    $columns[] = "trans.title as property_type";

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
                }// lands
                else if ($request->input('form_type') == 2){
                    $ads->join('lands_ads','lands_ads.ad_id','=','ads.id');
                    if (isset($options->area)) {
                       $ads->whereBetween('lands_ads.area',[$options->area[0],$options->area[1]]);
                    }
                     $columns[] = "lands_ads.area";
                }// cars
                else if ($request->input('form_type') == 3){

                    $ads->join('vehicles_ads','vehicles_ads.ad_id','=','ads.id');

                    $ads->join('basic_data as b1','vehicles_ads.motion_vector_id','=','b1.id');
                    $ads->join('basic_data as b2','vehicles_ads.engine_capacity_id','=','b2.id');
                    $ads->join('basic_data as b3','vehicles_ads.propulsion_system_id','=','b3.id');
                    $ads->join('basic_data as b4','vehicles_ads.fuel_type_id','=','b4.id');
                    $ads->join('basic_data as b5','vehicles_ads.mileage_id','=','b5.id');


                    $ads->join('basic_data_translations as motion_vector_trans', 'b1.id'  , '=', 'motion_vector_trans.basic_data_id');
                    $ads->join('basic_data_translations as engine_capacity_trans', 'b2.id', '=', 'engine_capacity_trans.basic_data_id');
                    $ads->join('basic_data_translations as propulsion_system_trans', 'b3.id', '=', 'propulsion_system_trans.basic_data_id');
                    $ads->join('basic_data_translations as fuel_type_trans', 'b4.id', '=', 'fuel_type_trans.basic_data_id');
                    $ads->join('basic_data_translations as mileage_trans', 'b5.id', '=', 'mileage_trans.basic_data_id');

                    $ads->where('motion_vector_trans.locale',$this->lang_code);
                    $ads->where('engine_capacity_trans.locale',$this->lang_code);
                    $ads->where('propulsion_system_trans.locale',$this->lang_code);
                    $ads->where('fuel_type_trans.locale',$this->lang_code);
                    $ads->where('mileage_trans.locale',$this->lang_code);
                    
                    
                    
                    $columns[] = "b1.id as motion_vector_id";
                    $columns[] = "motion_vector_trans.title as motion_vector";
                    $columns[] = "b2.id as engine_capacity_id";
                    $columns[] = "engine_capacity_trans.title as engine_capacity";
                    $columns[] = "b3.id as propulsion_system_id";
                    $columns[] = "propulsion_system_trans.title as propulsion_system";
                    $columns[] = "b4.id as fuel_type_id";
                    $columns[] = "fuel_type_trans.title as fuel_type";
                    $columns[] = "b5.id as mileage_id";
                    $columns[] = "mileage_trans.title as mileage";
                    $columns[] = "vehicles_ads.mileage_unit";
                    $columns[] = "vehicles_ads.status";
                    $columns[] = "vehicles_ads.manufacturing_year";
                   
                    if (isset($options->status)) {
                       $ads->where('vehicles_ads.status',$options->status);
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
                if (isset($options->price)) {
                    $ads->whereBetween('ads.price',[$options->price[0],$options->price[1]]);
                } 
            }
            $ads->select($columns);
            if ($id) {
                $ads = $ads->first();
                if (!$ads) {
                    return false;
                }
                return Ad::transformDetailsApi($ads,['user' => $user]);
            } else {
                $ads = $ads->paginate($this->limit);
                return Ad::transformCollection($ads,'PaginationApi',['user' => $user]);
            }
            
             
       //return  $ads = $ads->get();
    }





}
