<?php

namespace App\Http\Controllers\Front\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class AdsController extends FrontController {

    private $edit_rules = array(
    );

    public function __construct() {
        parent::__construct();
    }

    public function create(Request $request) {
       
        $this->data['main_categories']=Category::getAllFront(['parent_id' => 0]);
        $view = 'customer.ads.create';
        return $this->_view($view);
    }

}
