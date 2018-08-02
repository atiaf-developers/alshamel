<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\FrontController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Auth;
use Validator;

class LoginController extends FrontController {

    use AuthenticatesUsers;

    private $rules = array(
        'username' => 'required',
        'password' => 'required'
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('guest', ['except' => ['logout']]);
    }

    public function showLoginForm() {
        return $this->_view('auth/login');
    }

    public function login(Request $request) {
        
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
             return _json('error', $errors);
        } else {
            $username = $request->input('username');
            $password = $request->input('password');
            $User = $this->checkAuth($username);
            $is_logged_in = false;
            if ($User) {
                  if (password_verify($password, $User->password)) {
                        Auth::guard('web')->login($User);
                        return _json('success', route('home'));
                    }
            }
            return _json('error', _lang('app.invalid_credentials'));
         
        }
    }

    public function logout() {
        Auth::guard('web')->logout();
        return redirect('/login');
    }

    private function checkAuth($username) {
        $user = User::where('username', $username)->first();
        //dd($user);
        return $user;
    }

}
