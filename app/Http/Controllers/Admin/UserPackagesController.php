<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\UserPackage;
use DB;
use Validator;

class UserPackagesController extends BackendController
{
    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:user_packages,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:user_packages,add', ['only' => ['store']]);
    }
    public function index() {
        return $this->_view('user_packages/index', 'backend');
    }
    public function show($id) {
        $find = UserPackage::find($id);
        if ($find) {
            return response()->json([
                'type' => 'success',
                'message' => UserPackage::transform($find)
            ]);
        } else {
            return response()->json([
                'type' => 'success',
                'message' => 'error'
            ]);
        }
    }

    public function status($id,Request $request){
        try {
            $user_package = UserPackage::find($id);
            if (!$user_package) {
                return _json('error', _lang('app.not_found'));
            }
            $user_package->status = $request->input('status');
            $user_package->save();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $e) {
            return _json('error', _lang('app.error_is_occured'));
        }
    }


    public function destroy(Request $request) {
        $ids = $request->input('ids');
        try {
            UserPackage::destroy($ids);
            return _json('success', _lang('app.deleted_successfully'));
        } catch (Exception $ex) {
            return _json('error', _lang('app.error_is_occured'));
        }
    }
    public function data() {
        $users_packages = UserPackage::Join('users','users_packages.user_id','=','users.id')
        ->join('packages','users_packages.package_id','=','packages.id')
        ->join('packages_translations','packages_translations.package_id','=','packages.id')
        ->where('packages_translations.locale',$this->lang_code)
        ->select(['users_packages.id','users.name as user','packages_translations.title as package','users_packages.status','users_packages.created_at','users_packages.user_id']);
        
        
        return \Datatables::eloquent($users_packages)
        ->addColumn('input', function ($item) {
            $back = '';
            $back = '<div class="md-checkbox col-md-4" style="margin-left:40%;">';
            $back .= '<input type="checkbox" id="' . $item->id . '" data-id="' . $item->id . '" class="md-check check-one-message"  value="">';
            $back .= '<label for="' . $item->id . '">';
            $back .= '<span></span>';
            $back .= '<span class="check"></span>';
            $back .= '<span class="box"></span>';
            $back .= '</label>';
            $back .= '</div>';

            return $back;
        })
        ->addColumn('user', function ($item) {
            $back = "";
            $back .= '<a href="'.route('users.show',$item->user_id).'">';
            $back .= $item->user;
            $back .= '</a>';
            return $back;
        })
        ->addColumn('package', function ($item) {
            $back = $item->package;
            return $back;
        })
        ->addColumn('status', function ($item) {
            $back = _lang('app.'.UserPackage::$status[$item->status]);
            return $back;
        })
        ->addColumn('option', function ($item) {

           $back = "";

           $back .= '<div class="btn-group">';
           $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
           $back .= '<i class="fa fa-angle-down"></i>';
           $back .= '</button>';
           $back .= '<ul class = "dropdown-menu" role = "menu">';

           $back .= '<li>';
           $back .= '<a href="" onclick = "UserPackages.status(this);return false;" data-id = "' . $item->id . '" data-status = "0">';
           $back .= '<i class = "icon-docs"></i>' . _lang('app.pending');
           $back .= '</a>';
           $back .= '</li>';
           
           $back .= '<li>';
           $back .= '<a href="" onclick = "UserPackages.status(this);return false;" data-id = "' . $item->id . '" data-status = "1">';
           $back .= '<i class = "icon-docs"></i>' . _lang('app.accept');
           $back .= '</a>';
           $back .= '</li>';



           $back .= '<li>';
           $back .= '<a href="" data-toggle="confirmation" onclick = "UserPackages.status(this);return false;" data-id = "' . $item->id . '" data-status = "2">';
           $back .= '<i class = "icon-docs"></i>' . _lang('app.reject');
           $back .= '</a>';
           $back .= '</li>';

           $back .= '</ul>';
           $back .= ' </div>';

           return $back;
       })
        ->escapeColumns([])
        ->make(true);
    }
}
