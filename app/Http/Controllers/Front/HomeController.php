<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\Slider;


class HomeController extends FrontController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
    	$this->data['slider'] = $this->getSlider();
        return $this->_view('index');
    }


    private function getSlider()
    {
    	$slider = Slider::join('slider_translations', 'slider.id', '=', 'slider_translations.slider_id')
                                    ->where('slider_translations.locale', $this->lang_code)
                                    ->where('slider.active',true)
                                    ->orderBy('slider.this_order')
                                    ->select('slider.image','slider_translations.title','slider.url')
                                    ->get();
        return $slider;
    }



}
