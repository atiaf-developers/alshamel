<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;

class CustomerController extends FrontController {

    public function __construct() {
        parent::__construct();
         $this->middleware('auth');
    }

 

  

}
