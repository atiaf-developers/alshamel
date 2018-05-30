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

  
    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:users,open', ['only' => ['index','show']]);
        $this->middleware('CheckPermission:users,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:users,edit', ['only' => ['update']]);
        $this->middleware('CheckPermission:users,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {

        return $this->_view('users/index', 'backend');
    }

    public function status($id){
        try {
            $user = User::find($id);
            $user->active = !$user->active;
            $user->save();
            return _json('success', _lang('app.success'));
        } catch (\Exception $e) {
            return _json('error', _lang('app.error_is_occured'));
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id) {
        $user = User::find($id);
        if (!$user) {
            return $this->err404();
        } 
        $this->data['user'] = $user;
        return $this->_view('users/view', 'backend');
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
        $user = User::select(['id', 'email', 'username','name', 'mobile', 'active','image']);

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
                                $back .= '<a href="" onclick = "Users.edit(this);return false;" data-id = "' . $item->id . '">';
                                $back .= '<i class = "icon-docs"></i>' . _lang('app.show');
                                $back .= '</a>';
                                $back .= '</li>';
                            }
                            if (\Permissions::check('users', 'open')) {
                                $back .= '<li>';
                                $back .= '<a href="'.route('users.show',$item->id).'" onclick = "" data-id = "' . $item->id . '">';
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
                ->editColumn('active', function ($item) {
                    if ($item->active == 1) {
                        $message = _lang('app.active');
                        $class = 'btn-info';
                    } else {
                        $message = _lang('app.not_active');
                        $class = 'btn-danger';
                    }
                    $back = '<a class="btn ' . $class . '" onclick = "Users.status(this);return false;" data-id = "' . $item->id . '" data-status = "' . $item->active . '">' . $message . ' <a>';
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
