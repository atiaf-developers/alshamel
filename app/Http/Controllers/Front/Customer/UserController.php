<?php

namespace App\Http\Controllers\Front\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Noti;

class UserController extends CustomerController {

    private $edit_rules = array(
    );

    public function __construct() {
        parent::__construct();
    }

    public function showEditForm() {
        $view = 'customer/edit';
        return $this->_view($view);
    }

    public function edit(Request $request) {
        $User = $this->User;
        if ($request->input('password')) {
            $this->edit_rules['password'] = "required";
            $this->edit_rules['confirm_password'] = "required|same:password";
        }
        if ($request->file('image')) {
            $this->edit_rules['image'] = "image|mimes:gif,png,jpeg|max:1000";
        }
        $rules['mobile'] = "required|unique:users,mobile,$User->id";
        $rules['username'] = "required|unique:users,username,$User->id";
        $rules['email'] = "required|unique:users,email,$User->id";
        $rules['name'] = "required";

        $validator = Validator::make($request->all(), $this->edit_rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();
            return _json('error', $this->errors);
        }

        try {
            $User->name = $request->input('name');
            $User->username = $request->input('username');
            $User->mobile = $request->input('mobile');
            $User->email = $request->input('email');
            if ($image = $request->input('password')) {
                $User->password = bcrypt($request->input('password'));
            }
            if ($image = $request->file('image')) {
                User::deleteUploaded('users', $User->image);

                $User->image = User::upload($image, 'users', true);
            }
            $User->save();
            $message = _lang('app.updated_successfully');
            return _json('success', $message);
        } catch (\Exception $ex) {
            //dd($ex->getMessage());
            $message = _lang('app.error_is_occured');
            return _json('error', $message);
        }
    }

    public function notifications() {
        $where_array['notifier_id'] = $this->User->id;
        $where_array['notifiable_type'] = 1;
        $where_array['created_at'] = $this->User->created_at;
        $this->data['noti'] = Noti::getNoti($where_array, 'ForFront');
        //dd($this->data['noti']);
        $view = 'customer.notifications';
        return $this->_view($view);
    }

}
