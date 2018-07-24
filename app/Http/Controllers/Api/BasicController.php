<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Setting;
use App\Models\SettingTranslation;
use App\Models\Category;
use App\Models\Location;
use App\Models\ContactMessage;
use App\Models\BasicData;
use App\Models\Package;
use App\Models\UserPackage;
use App\Models\Rating;
use App\Models\Ad;
use App\Models\Favourite;
use App\Helpers\Fcm;
use Carbon\Carbon;
use DB;

class BasicController extends ApiController {

    private $contact_rules = array(
        'name' => 'required',
        'email' => 'required|email',
        'message' => 'required',
        'type' => 'required',
    );
    private $raters_rules = array(
        'ad_id' => 'required',
    );
    private $basic_data_rules = array(
        'form_type' => 'required|in:1,2,3,4',
        'category_id' => 'required'
    );
    private $package_rules = array(
        'package_id' => 'required',
    );

    public function getToken(Request $request) {
        $token = $request->header('authorization');
        if ($token != null) {
            $token = Authorization::validateToken($token);
            if ($token) {
                $new_token = new \stdClass();
                $find = User::find($token->id);
                if ($find != null) {
                    $new_token->id = $find->id;
                    $new_token->expire = strtotime('+ ' . $this->expire_no . $this->expire_type);
                    $expire_in_seconds = $new_token->expire;
                    return _api_json('', ['token' => AUTHORIZATION::generateToken($new_token), 'expire' => $expire_in_seconds]);
                } else {
                    return _api_json('', ['message' => 'user not found'], 401);
                }
            } else {
                return _api_json('', ['message' => 'invalid token'], 401);
            }
        } else {
            return _api_json('', ['message' => 'token not provided'], 401);
        }
    }

    public function getSettings() {
        try {
            $settings = Setting::select('name', 'value')->get()->keyBy('name');
            $settings['social_media'] = json_decode($settings['social_media']->value);
            $settings['phone'] = explode(",", $settings['phone']->value);
            $settings['email'] = explode(",", $settings['email']->value);
            $settings['info'] = SettingTranslation::where('locale', $this->lang_code)->first();
            unset($settings['num_free_ads']);
            return _api_json($settings);
        } catch (\Exception $e) {
            return _api_json(new \stdClass(), ['message' => $e->getMessage()], 400);
        }
    }

    public function sendContactMessage(Request $request) {
        $validator = Validator::make($request->all(), $this->contact_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        } else {
            try {
                $ContactMessage = new ContactMessage;
                $ContactMessage->name = $request->input('name');
                $ContactMessage->email = $request->input('email');
                $ContactMessage->message = $request->input('message');
                $ContactMessage->type = $request->input('type');
                $ContactMessage->save();
                return _api_json('', ['message' => _lang('app.message_is_sent_successfully')]);
            } catch (\Exception $ex) {
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
        }
    }

    public function getNumOfAvailableAds() {
        try {
            $user = $this->auth_user();
            if ($user->num_free_ads == 0) {
                $avaliable_ads = UserPackage::where('user_id', $user->id)->where('available_of_ads', '!=', 0)->where('status', 1)->first();
                if (!$avaliable_ads) {
                    return _api_json('', ['num_of_ads' => 0, 'free' => false]);
                }
                return _api_json('', ['num_of_ads' => $avaliable_ads->available_of_ads, 'free' => false]);
            } else {
                return _api_json('', ['num_of_ads' => $user->num_free_ads, 'free' => true]);
            }
        } catch (\Exception $e) {
            return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getLocations(Request $request) {
        try {
            $location = $request->input('location') ? $request->input('location') : 0;
            $locations = Location::getAll($location);
            return _api_json(Location::transformCollection($locations));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getCategories(Request $request) {
        try {
            $category = $request->input('category') ? $request->input('category') : 0;
            $categories = Category::getAll($category);

            return _api_json(Category::transformCollection($categories));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getAdRaters(Request $request) {
        try {

            $validator = Validator::make($request->all(), $this->raters_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            }
            $ad = Ad::find($request->input('ad_id'));
            if (!$ad) {
                $message = _lang('app.not_found');
                return _api_json('', ['message' => $message], 404);
            }
            $raters = array();

            $raters = Rating::join('rating_users', 'rating.id', '=', 'rating_users.rating_id')
                    ->join('users', 'users.id', '=', 'rating_users.user_id')
                    ->where('rating.entity_id', $ad->id)
                    ->where('rating_users.active',true)
                    ->select('users.name', 'users.image', 'rating.score', 'rating_users.comment', 'rating.created_at')
                    ->paginate($this->limit);

                    
            return _api_json(Rating::transformCollection($raters));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getPackages(Request $request) {
        try {
            $packages = Package::getAll();
            return _api_json(Package::transformCollection($packages));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function sendPackageRequest(Request $request) {
        try {

            $validator = Validator::make($request->all(), $this->package_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            }

            $package = Package::find($request->input('package_id'));
            if (!$package) {
                $message = _lang('app.not_found');
                return _api_json('', ['message' => $message], 404);
            }
            $user = $this->auth_user();

            $check = UserPackage::where('user_id', $user->id)
                    ->where(function ($query) use($user) {
                        $query->where('available_of_ads', '!=', 0);
                        $query->where('user_id', $user->id);
                        $query->where('status', 1);
                    })
                    ->orWhere(function ($query) use($user) {
                        $query->where('user_id', $user->id);
                        $query->where('status', 0);
                    })
                    ->first();
            //dd($check);
            if ($check) {
                if ($check->status == 0) {
                    $message = _lang('app.you_have_already_sent_a_request');
                } else if ($check->status == 1) {
                    $message = _lang('app.you_are_already_subscribed_to_the_package');
                }
                return _api_json('', ['message' => $message], 400);
            }

            $user_package = new UserPackage;
            $user_package->user_id = $user->id;
            $user_package->package_id = $package->id;
            $user_package->num_of_ads = $package->num_of_ads;
            $user_package->available_of_ads = $package->num_of_ads;
            $user_package->status = 0;

            $user_package->save();
            return _api_json('', ['message' => _lang('app.sent_successfully')]);
        } catch (\Exception $e) {
            return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getBasicData(Request $request) {
        try {
            $validator = Validator::make($request->all(), $this->basic_data_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json([], ['errors' => $errors], 400);
            }
            $data = BasicData::getAll($request);
            return _api_json($data);
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }


    public function deleteFavourites()
    {
        try {
            $user = $this->auth_user();
            Favourite::where('user_id', $user->id)->delete();
            return _api_json('',['message' => _lang('app.deleted_successfully')]);
        } catch (\Exception $e) {
            return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    private function buildTree($elements, $parentId = 0) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->id);
                if ($children) {
                    $element['childrens'] = $children;
                }
                $branch[] = Category::transform($element);
            }
        }
        return $branch;
    }

}
