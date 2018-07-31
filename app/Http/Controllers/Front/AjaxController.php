<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\Category;
use App\Models\Location;
use App\Models\BasicData;
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

        $city = Location::getAllFront(['parent_id' => $country_id]);

        return _json('success', $city);
    }

    public function getCategories($category_id) {

        $categories = Category::getAllFront(['parent_id' => $category_id]);

        return _json('success', $categories);
    }

    public function getBasicData(Request $request,$category_id) {
        $category=Category::find($category_id);
        if(!$category){
            
        }
        $request->merge(['form_type' => $category->form_type]);
        if($request->form_type==1){
            $view='property';
        }else if($request->form_type==3){
            $view='cars';
        }else if($request->form_type==2){
            $view='lands';
        }else{
            $view='default';
        }
        $this->data['basic_data'] = BasicData::getAll($request);
    
        echo $this->_view('ajax.ads.'.$view)->render();
    }

    public function resend_code(Request $request) {
        $mobile = $request->input('mobile');
        $activation_code = Random(4);
        //$this->sendSMS([$request->input('mobile')], $activation_code);
        return _json('success', ['activation_code' => $activation_code]);
    }

}
