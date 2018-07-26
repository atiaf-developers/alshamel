<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\GameAvailability;
use App\Models\Reservation;
use App\Models\Location;
use App\Notifications\GeneralNotification;
use App\Helpers\Fcm;
use App\Mail\GeneralMail;
use Mail;
use Validator;
use Notification;
use DB;

class AjaxController extends FrontController {

    public function __construct() {
        parent::__construct();
        $this->middleware('auth', ['only' => ['reserve_submit']]);
    }

    private $reserve_rules = array(
        'name' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'reservation_date' => 'required',
        'reservation_time' => 'required',
    );

    public function changeLocation(Request $request) {

        $validator = Validator::make($request->all(), ['country' => 'required']);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        try {

            $country_id = $request->input('country');
            $city_id = $request->input('city');
            $long = 7 * 60 * 24;
            if ($country_id && $city_id) {
                return _json('success')->cookie('country_id', $country_id, $long)->cookie('city_id', $city_id, $long);
            } else if ($country_id && !$city_id) {
                return _json('success')->cookie('country_id', $country_id, $long);
            }

        } catch (\Exception $ex) {

            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }
     public function getCities($country_id) {

        $city = Location::getAllFront(['parent_id'=>$country_id]);

        return _json('success', $city);
    }

}
