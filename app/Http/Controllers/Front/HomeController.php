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
        $this->data['slider'] = Slider::getAllFront();
        return $this->_view('index');
    }

  

}
