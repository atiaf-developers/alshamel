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

class AdsController extends ApiController {

    private $ads_rules = array(
        'country_id' => 'required',
        'lat' => 'required',
        'lng' => 'required',
    );
    private $ad_rules = array(
        'form_type' => 'required|in:1,2,3,4',
        'lat' => 'required',
        'lng' => 'required',
    );
    private $favourites_rules = array(
        'lat' => 'required',
        'lng' => 'required',
    );
    private $rules = [
        'form_type' => 'required|in:1,2,3,4',
        'category_id' => 'required',
        'country_id' => 'required',
        'city_id' => 'required',
        //'title' => 'required',
        'details' => 'required',
        'lat' => 'required',
        'lng' => 'required',
        'email' => 'required',
        'mobile' => 'required'
    ];
    private $real_states_rules = [
        'area' => 'required',
        'property_type' => 'required',
        'rooms_number' => 'required',
        'baths_number' => 'required',
        'is_furnished' => 'required',
        'has_parking' => 'required',
        'price' => 'required'
    ];
    private $land_rules = [
        'area' => 'required'
    ];
    private $vehicle_rules = [
        'status' => 'required',
        'manufacturing_year' => 'required',
        'motion_vector' => 'required',
        'engine_capacity' => 'required',
        'propulsion_system' => 'required',
        'mileage' => 'required',
        'mileage_unit' => 'required',
        'fuel_type' => 'required',
        'price' => 'required'
    ];

    public function index(Request $request) {
        try {
            $rules = array();
            if (!$request->input('options')) {
                $rules = $this->ads_rules;
            } else if ($request->input('options') && $request->input('options') == 2) {
                $rules = $this->favourites_rules;
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json([], ['errors' => $errors], 400);
            }
            $user = $this->auth_user();
            if ($request->input('page_type') == 1) {
                $special_ads = Ad::getAdsApi($request, $user, null, 1);
                $oridnary_ads = Ad::getAdsApi($request, $user, null, 2);
                $ads = array_merge($special_ads, $oridnary_ads);
            } else if ($request->input('page_type') == 2) {
                $ads = Ad::getAdsApi($request, $user, null, 3);
            } else {
                $ads = Ad::getAdsApi($request, $user);
            }

            return _api_json($ads);
        } catch (\Exception $e) {
            dd($e);
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function show(Request $request, $id) {

        $validator = Validator::make($request->all(), $this->ad_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        }
        $user = $this->auth_user();
        $ad = Ad::getAdsApi($request, $user, $id);
        if (!$ad) {
            return _api_json(new \stdClass(), ['message' => _lang('app.not_found')], 404);
        }
        return _api_json($ad);
    }

    public function store(Request $request) {
        try {
            if (!$request->input('form_type')) {
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
            if ($request->input('form_type') == 1) {
                $validation_rules = $this->real_states_rules;
            } else if ($request->input('form_type') == 2) {
                $validation_rules = $this->land_rules;
            } else if ($request->input('form_type') == 3) {
                $validation_rules = $this->vehicle_rules;
            }

            $rules = in_array($request->input('form_type'), [1, 2, 3]) ? array_merge($validation_rules, $this->rules) : $this->rules;

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            }

            $user = $this->auth_user();
            $avaliable_ads = null;
            if ($user->num_free_ads == 0) {
                $avaliable_ads = UserPackage::where('user_id', $user->id)->where('available_of_ads', '!=', 0)->where('status', 1)->first();
                if (!$avaliable_ads) {
                    $message = ['message' => _lang('app.you_can_not_add_more_ads')];
                    return _api_json('', $message, 400);
                }
            }

            DB::beginTransaction();
            try {
                $ad = $this->handleAd($request, null, $user, $avaliable_ads);
                DB::commit();
                $notification['title'] = 'الشامل';
                $notification['body'] = 'اعلان جديد  - ' . $ad->title;
                $notification['type'] = 1;
                $notification['id'] = $ad->id;
                $notification['form_type'] = $request->input('form_type');
                $this->send_noti_fcm($notification, false, '/topics/alshamel_and', 1);
                $this->send_noti_fcm($notification, false, '/topics/alshamel_ios', 2);

                return _api_json('', ['message' => _lang('app.added_successfully')]);
            } catch (\Exception $ex) {
                DB::rollback();
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
        } catch (\Exception $e) {
            return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function update(Request $request, $id) {
        try {
            $user = $this->auth_user();
            $ad = Ad::where('id', $id)->where('user_id', $user->id)->first();
            if (!$ad) {
                $message = ['message' => _lang('app.not_found')];
                return _api_json('', $message, 404);
            }
            if (!$request->input('form_type')) {
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
            if ($request->input('form_type') == 1) {
                $validation_rules = $this->real_states_rules;
            } else if ($request->input('form_type') == 2) {
                $validation_rules = $this->land_rules;
            } else if ($request->input('form_type') == 3) {
                $validation_rules = $this->vehicle_rules;
            }

            unset($this->rules['category_id']);
            $rules = in_array($request->input('form_type'), [1, 2, 3]) ? array_merge($validation_rules, $this->rules) : $this->rules;

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            }
            $user = $this->auth_user();
            DB::beginTransaction();
            try {
                $this->handleAd($request, $ad, $user);
                DB::commit();
                return _api_json('', ['message' => _lang('app.updated_successfully')]);
            } catch (\Exception $ex) {
                DB::rollback();
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
        } catch (\Exception $e) {
            return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function destroy($id) {
        DB::beginTransaction();
        try {
            $user = $this->auth_user();
            $ad = Ad::where('id', $id)->where('user_id', $user->id)->first();
            if (!$ad) {
                $message = ['message' => _lang('app.not_found')];
                return _api_json('', $message, 404);
            }
            $ad->delete();
            DB::commit();
            return _api_json('', ['message' => _lang('app.deleted_successfully')]);
        } catch (\Exception $e) {
            DB::rollback();
            return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    private function handleAd($request, $ad = null, $user, $avaliable_ads = null) {
        //$user = $this->auth_user();
        $action = 'edit';
        if (!$ad) {
            $ad = new Ad;
            $ad->category_id = $request->input('category_id');
            $ad->user_id = $user->id;
            $ad->active = true;
            $action = 'add';
        }

        $ad->country_id = $request->input('country_id');
        $ad->city_id = $request->input('city_id');
        $ad->title = $request->input('title');
        $ad->details = $request->input('details');
        $ad->lat = $request->input('lat');
        $ad->lng = $request->input('lng');
        $ad->email = $request->input('email');
        $ad->mobile = $request->input('mobile');
        if ($request->input('price')) {
            $ad->price = $request->input('price');
        } else {
            $ad->price = 0;
        }
        if (isset($request->images) && !empty($request->images)) {
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

            if (count($images) > 0) {
                $ad->images = json_encode($images);
            }
        }
        $ad->save();

        if (in_array($request->input('form_type'), [1, 2, 3])) {
            if ($request->input('form_type') == 1) {
                $this->handleRealStateAdDetails($request, $ad->id);
            } elseif ($request->input('form_type') == 2) {
                $this->handleLandAdDetails($request, $ad->id);
            } elseif ($request->input('form_type') == 3) {
                $this->handleVechileAdDetails($request, $ad->id);
            }
        }

        // decreament available ads
        if ($action == 'add' && $avaliable_ads) {
            if ($user->num_free_ads == 0) {
                $avaliable_ads->available_of_ads -= 1;
                $avaliable_ads->save();
            } else {
                $user->num_free_ads -= 1;
                $user->save();
            }
        }
        if ($action == 'add' && $user->num_free_ads != 0) {
            $user->num_free_ads -= 1;
            $user->save();
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
        $real_state_ad->is_furnished = $request->input('is_furnished');
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
