<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Basic;
use Auth;
use App\Models\Setting;
use App\Models\SettingTranslation;
use App\Models\Category;
use App\Models\Location;

class FrontController extends Controller {

    use Basic;

    protected $lang_code;
    protected $User = false;
    protected $isUser = false;
    protected $selectedCountry = null;
    protected $selectedCity = null;
    protected $_Request = false;
    protected $limit = 1;
    protected $settings;
    protected $data = array();

    public function __construct() {

        $this->init();
    }

    private function init() {
        $this->check_auth();
        $this->getLangCode();
        $this->check_selected_country();
        $this->data['categories'] = Category::getAllFront(['parent_id' => 0]);
        $this->data['countries'] = Location::getAllFront();
        $this->data['settings'] = Setting::getAll();
        $this->settings = $this->data['settings'];
        $this->data['locations'] = Location::getAllFront();
    }

    private function getLangCode() {
        $this->lang_code = app()->getLocale();
        $this->data['lang_code'] = $this->lang_code;
        session()->put('lang_code', $this->lang_code);
        if ($this->data['lang_code'] == 'ar') {
            $this->data['next_lang_code'] = 'en';
            $this->data['next_lang_text'] = 'English';
            $this->data['currency_sign'] = 'جنيه';
        } else {
            $this->data['next_lang_code'] = 'ar';
            $this->data['next_lang_text'] = 'العربية';
            $this->data['currency_sign'] = 'EGP';
        }
        $this->slugsCreate();
    }

    private function check_auth() {
        if (Auth::guard('web')->user() != null) {
            $this->User = Auth::guard('web')->user();
            $this->isUser = true;
        }
        $this->data['User'] = $this->User;
        $this->data['isUser'] = $this->isUser;
    }

    private function check_selected_country() {

        if (\Cookie::get('country_id') !== null || \Cookie::get('city_id') !== null) {
            $this->selectedCountry = \Cookie::get('country_id') ? decrypt(\Cookie::get('country_id')) : null;
            $this->selectedCity = \Cookie::get('city_id') ? decrypt(\Cookie::get('city_id')) : null;
            $this->data['cities'] = Location::getAllFront(['parent_id' => $this->selectedCountry]);
        }
        $this->data['country_id'] = $this->selectedCountry;
        $this->data['city_id'] = $this->selectedCity;
    }

    protected function _view($main_content, $type = 'front') {
        $main_content = "main_content/$type/$main_content";
        return view($main_content, $this->data);
    }

    protected function err404($code = false, $message = false) {
        if (!$message) {
            $message = _lang('app.page_not_found');
        }
        if (!$code) {
            $code = 404;
        }
        $this->data['code'] = $code;
        $this->data['message'] = $message;
        return $this->_view('err404');
    }

}
