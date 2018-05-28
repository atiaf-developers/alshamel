<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\User;
use App\Models\Massage;
use App\Models\Chat;
use App\Models\Jobs;
use App\Models\ConsultationGroup;
use Validator;

class UsersController extends BackendController {

    private $rules = array(
        'name' => 'required',
        'username' => 'required|unique:users,username',
        'mobile' => 'required|unique:users,mobile',
        'password' => 'required',
       
    );
    private $ruels_page;
    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:users,open');
        $this->middleware('CheckPermission:users,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:users,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:users,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {

        return $this->_view('users/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if ($request->input('email')) {
            $this->rules['email'] = 'required|email|unique:users,email';
        }
        if ($request->file('user_image')) {
             $this->rules['user_image'] = 'required|image|mimes:gif,png,jpeg|max:1000';
            
        }
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {
         
            try {
            $User = new User;
            $User->name = $request->input('fullname');
            $User->username = $request->input('username');
            $User->email = $request->input('email');
            $User->mobile = $request->input('mobile');
            $User->password = bcrypt($request->input('password'));
            $User->active = $request->input('active');
            $User->type = $request->input('type');
            if ($request->file('user_image')) {
                  $User->image = User::upload($request->file('user_image'), 'users',true);
            }
          
                $User->save();
                return _json('success', _lang('app.added_successfully'));
            } catch (\Exception $ex) {
                return _json('error', _lang('app.error_is_occured'));
            }
        }
    }

    public function status($id){
        $User = User::find($id);
      if (!$User) {
          return _json('error', _lang('app.error_is_occured'));
      }
      if($User->active==0){
        $User->active=1;
      }else{
        $User->active=0;
      }
      $User->save();
      return _json('success', _lang('app.updated_successfully'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id) {
        
        
            $User = User::find($id);
            

            if ($User != null) {
                if ($request->ajax()) {
                    return _json('success', $User);
                }
                return $this->_view('users/show', 'backend');
            } else {
                if ($request->ajax()) {
                    return _json('error', _lang('app.error_is_occured'));
                }
                return $this->err404();
            }
        
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $User = User::find($id);
            

        if ($User != null) {
            // dd($User->name);
            $this->data['data']=$User;
            return $this->_view('users/view', 'backend');
        } else {
            if ($request->ajax()) {
                return _json('error', _lang('app.error_is_occured'));
            }
            return $this->err404();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function update(Request $request, $id) {
    //   echo $id;die;
        $User = User::find($id);
        if (!$User) {
            return _json('error', _lang('app.error_is_occured'));
        }
        if ($request->file('user_image')) {
            $rules['user_image'] = 'required|image|mimes:gif,png,jpeg|max:1000';
        }
        if ($request->input('email')) {
            $rules['email'] = "required|unique:users,email,$User->id";
        }
        $rules['username'] = "required|unique:users,username,$User->id";
       
        $rules['mobile'] = "required|unique:users,mobile,$User->id";
        if ($request->input('password') === null) {
            unset($rules['password']);
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {
          $User->name = $request->input('fullname');
          $User->username = $request->input('username');
          $User->email = $request->input('email');
          $User->mobile = $request->input('mobile');
            if ($request->input('password')) {
                $User->password = bcrypt($request->input('password'));
            }
            $User->active = $request->input('active');
            if ($request->file('user_image')) {
                $old_image = $User->user_image;
                if ($old_image != 'default.png') {
                    User::deleteUploaded('users',$old_image);
                }
                $User->image = User::upload($request->file('user_image'), 'users',true);
            }
            try {
                $User->save();
                return _json('success', _lang('app.updated_successfully'));
            } catch (Exception $ex) {
                return _json('error', _lang('app.error_is_occured'));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id) {
        $User = User::find($id);
        if ($User == null) {
            return _json('error', _lang('app.error_is_occured'));
        }
        try {

            $User->delete();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {

            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }
    public function data(Request $request) {
        $user = User::select('id', 'email', 'username','name', 'mobile', 'active','image');

        return \Datatables::eloquent($user)
                ->addColumn('options', function ($item){
                    $js='Users';
                    $back = "";

                        if (\Permissions::check('users', 'open') || \Permissions::check('users', 'delete')) {
                            $back .= '<div class="btn-group">';
                            $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                            $back .= '<i class="fa fa-angle-down"></i>';
                            $back .= '</button>';
                            $back .= '<ul class = "dropdown-menu" role = "menu">';
                            if (\Permissions::check('users', 'edit')) {
                                $back .= '<li>';
                                $back .= '<a href="" onclick = "'.$js.'.edit(this);return false;" data-id = "' . $item->id . '">';
                                $back .= '<i class = "icon-docs"></i>' . _lang('app.show');
                                $back .= '</a>';
                                $back .= '</li>';
                            }
                            if (\Permissions::check('users', 'open')) {
                                $back .= '<li>';
                                $back .= '<a href="'.route('users.show',$item->id).'/edit" onclick = "" data-id = "' . $item->id . '">';
                                $back .= '<i class = "icon-docs"></i>' . _lang('app.show');
                                $back .= '</a>';
                                $back .= '</li>';
                            }
                            if (\Permissions::check('users', 'delete')) {
                                $back .= '<li>';
                                $back .= '<a href="" data-toggle="confirmation" onclick = "'.$js.'.delete(this);return false;" data-id = "' . $item->id . '">';
                                $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                $back .= '</a>';
                                $back .= '</li>';
                            }
                            $back .= '</ul>';
                            $back .= ' </div>';
                        }


                    return $back;
                })
                ->addColumn('active', function ($item) {
                    $js='Users';
                    if ($item->active == 1) {
                        $message = _lang('app.active');
                        $class = 'btn-info';
                    } else {
                        $message = _lang('app.not_active');
                        $class = 'btn-danger';
                    }
                    $back = '<a class="btn ' . $class . '" onclick = "'.$js.'.status(this);return false;" data-id = "' . $item->id . '" data-status = "' . $item->active . '">' . $message . ' <a>';
                    return $back;
                })
                ->addColumn('image', function ($item) {
                   if (!$item->image) {
                       $item->image = 'default.png';
                   }
                    $back = '<img src="' . url('public/uploads/users/' . $item->image) . '" style="height:64px;width:64px;"/>';
                    return $back;
                })
                ->escapeColumns([])
                ->make(true);
    }
}
