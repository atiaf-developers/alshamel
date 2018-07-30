<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\Category;
use Validator;

class CategoriesController extends FrontController {

    private $contact_rules = array(
        'message' => 'required',
        'email' => 'required|email',
        'type' => 'required',
        'name' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        $path_arr = explode('/', $request->path());
        $last_segment = end($path_arr);
        $category = Category::getAllFront(['slug' => $last_segment]);
        if (!$category) {
            return $this->err404();
        }
        if ($category->level == 1) {
            $this->data['category'] = $category;
            $this->data['categories'] = Category::getAllFront(['parent_id' => $category->id]);
            return $this->_view('categories.index');
        } else {
            $this->data['ads'] = $ads;
            return $this->_view('ads.index');
        }
        dd($category);
    }

}
