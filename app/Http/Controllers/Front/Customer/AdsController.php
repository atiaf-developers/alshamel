<?php

namespace App\Http\Controllers\Front\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Validator;
use App\Models\Ad;
use App\Models\AdCategory;
use App\Models\Category;
use App\Models\UserPackage;
use App\Models\RealStateAd;
use App\Models\LandAd;
use App\Models\VechileAd;
use App\Models\BasicData;
use App\Models\Location;
use DB;

class AdsController extends CustomerController {

    private $favourites_rules = array(
        'lat' => 'required',
        'lng' => 'required',
    );
    private $rules = [
        'ad_country' => 'required',
        'ad_city' => 'required',
        'main_category' => 'required',
        'sub_category' => 'required',
        //'title' => 'required',
        'details' => 'required',
        'email' => 'required',
        'mobile' => 'required'
    ];

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        $this->data['ads'] = Ad::getAdsFront(['user_id' => $this->User->id]);
        //dd($this->data['ads']);
        $view = 'customer.ads.index';
        return $this->_view($view);
    }

    public function create(Request $request) {

        $this->data['main_categories'] = Category::getAllFront(['parent_id' => 0]);
        $view = 'customer.ads.create';
        return $this->_view($view);
    }

    public function edit($id) {
        $ad = Ad::getAdsFront(['id' => $id]);
        //dd($ad);
        if (!$ad) {
            return $this->err404();
        }
        $ad = Ad::getAdsFront(['id' => $id, 'form_type' => $ad->form_type]);
        $this->data['ad'] = $ad;
        //dd($this->data['ad']);
        $this->data['basic_data'] = BasicData::getAllFront(['category_id' => $ad->category_id, 'form_type' => $ad->form_type]);
        $this->data['cities'] = Location::getAllFront(['parent_id' => $ad->country_id]);
        $view = 'customer.ads.edit';
        return $this->_view($view);
    }

    public function store(Request $request) {

        try {
            $category = Category::find($request->input('category_id'));
            //dd($request->all());
            if ($category) {
                $basicDataRules = $this->getValidationForBasicData($category);
                $this->rules = array_merge($basicDataRules, $this->rules);
            }
            $validator = Validator::make($request->all(), $this->rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _json('error', $errors);
            }
            $avaliable_ads = null;
            if ($this->User->num_free_ads == 0) {
                $avaliable_ads = UserPackage::where('user_id', $this->User->id)->where('available_of_ads', '!=', 0)->where('status', 1)->first();
                if (!$avaliable_ads) {
                    return _json('error', _lang('app.you_can_not_add_more_ads'));
                }
            }

            DB::beginTransaction();
            $ad = $this->handleAd($request, null, $category);
            if ($avaliable_ads) {
                $avaliable_ads->available_of_ads -= 1;
                $avaliable_ads->save();
            } else {
                $this->User->num_free_ads -= 1;
                $this->User->save();
            }

            DB::commit();
            $notification['title'] = 'الشامل';
            $notification['body'] = 'اعلان جديد  - ' . $ad->title;
            $notification['type'] = 1;
            $notification['id'] = $ad->id;
            $notification['user_id'] = $this->User->id;
            $notification['form_type'] = $category->form_type;
            $this->send_noti_fcm($notification, false, '/topics/alshamel_and', 1);
            $this->send_noti_fcm($notification, false, '/topics/alshamel_ios', 2);
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return _json('error', _lang('app.error_is_occured'));
        }
    }

    public function update(Request $request, $id) {

        try {
            $ad = Ad::getAdsFront(['id' => $id]);
            //dd($ad);
            if (!$ad) {
                return _json('error', _lang('app.ad_not_found'));
            }
            $category = Category::find($request->input('category_id'));
            if ($category) {
                $basicDataRules = $this->getValidationForBasicData($category);
                $this->rules = array_merge($basicDataRules, $this->rules);
            }
            $validator = Validator::make($request->all(), $this->rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _json('error', $errors);
            }


            DB::beginTransaction();
            $ad = $this->handleAd($request, null, $category);
            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return _json('error', _lang('app.error_is_occured'));
        }
    }

    private function handleAd($request, $ad = null, $category) {

        if (!$ad) {
            $ad = new Ad;
            $ad->user_id = $this->User->id;
            $ad->active = true;
        }
        //dd($request->all());
        $ad->country_id = $request->input('ad_country');
        $ad->city_id = $request->input('ad_city');
        $ad->title = $request->input('title');
        $ad->details = $request->input('details');
        $ad->lat = $request->input('lat');
        $ad->lng = $request->input('lng');
        $ad->email = $request->input('email');
        $ad->mobile = $request->input('mobile');

        $ad->price = $request->input('price') ? $ad->price = $request->input('price') : 0;
//        if (isset($request->images) && !empty($request->images)) {
//            if ($ad->images) {
//                foreach (json_decode($ad->images) as $image) {
//                    Ad::deleteUploaded('ads', $image);
//                }
//            }
//            $images = [];
//            foreach (json_decode($request->images) as $image) {
//                $image = preg_replace("/\r|\n/", "", $image);
//                if (!isBase64image($image)) {
//                    continue;
//                }
//                $images[] = Ad::upload($image, 'ads', true, false, true);
//            }
//
//            if (count($images) > 0) {
//                $ad->images = json_encode($images);
//            }
//        }
        $ad->save();
        if (!$ad) {
            $category_parents = explode(',', $category->parents_ids);
            $ad_categories = array_merge($category_parents, [$category->id]);
            foreach ($ad_categories as $one) {
                $ad_categories_data[] = array(
                    'ad_id' => $ad->id,
                    'category_id' => $one
                );
            }
            AdCategory::insert($ad_categories_data);
        }


        if ($category->form_type == 1) {
            $this->handleRealStateAdDetails($request, $ad->id);
        } elseif ($category->form_type == 2) {
            $this->handleLandAdDetails($request, $ad->id);
        } elseif ($category->form_type == 3) {
            $this->handleVechileAdDetails($request, $ad->id);
        }




        return $ad;
    }

    private function handleRealStateAdDetails($request, $ad_id) {
        $real_state_ad = RealStateAd::where('ad_id', $ad_id)->first();
        if (!$real_state_ad) {
            $real_state_ad = new RealStateAd;
            $real_state_ad->ad_id = $ad_id;
        }
        $real_state_ad->area = $request->input('area');
        $real_state_ad->is_furnished = $request->input('furnished');
        $real_state_ad->has_parking = $request->input('has_parking');
        $real_state_ad->property_type_id = $request->input('property_type');
        $real_state_ad->rooms_number = $request->input('rooms_number');
        $real_state_ad->baths_number = $request->input('baths_number');

        $real_state_ad->save();
    }

    private function handleLandAdDetails($request, $ad_id) {
        $land_ad = LandAd::where('ad_id', $ad_id)->first();
        if (!$land_ad) {
            $land_ad = new LandAd;
            $land_ad->ad_id = $ad_id;
        }
        $land_ad->area = $request->input('area');

        $land_ad->save();
    }

    private function handleVechileAdDetails($request, $ad_id) {
        $vehicle_ad = VechileAd::where('ad_id', $ad_id)->first();
        if (!$vehicle_ad) {
            $vehicle_ad = new VechileAd;
            $vehicle_ad->ad_id = $ad_id;
        }
        //dd($request->all());
        $vehicle_ad->status = $request->input('status');
        $vehicle_ad->manufacturing_year = $request->input('manufacturing_year');
        $vehicle_ad->motion_vector_id = $request->input('motion_vector');
        $vehicle_ad->engine_capacity_id = $request->input('engine_capacity');
        $vehicle_ad->propulsion_system_id = $request->input('propulsion_system');
        $vehicle_ad->fuel_type_id = $request->input('fuel_type');
        $vehicle_ad->mileage_unit = $request->input('measruing_unit');
        $vehicle_ad->mileage_id = $request->input('car_speedometer');
        $vehicle_ad->save();
    }

    private function getValidationForBasicData($category) {
        $rules = [];
        $basic_data = BasicData::getDataFrontAjax(['category_id' => $category->id, 'form_type' => $category->form_type]);
        if (!empty($basic_data)) {
            foreach ($basic_data as $one) {
                $rules[$one['name']] = $one['rules'];
            }
        }
        return $rules;
    }

}
