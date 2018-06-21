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
        $reprot = UserPackage::select('*');
        // $reprot=AdReport::transformCollection($reprot);
        
        return \Datatables::eloquent($reprot)
        
                ->addColumn('input', function ($item) {
                    // dd($item);
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

                    $back .= '<a href="'.route('users.show',$item->user->id).'"  data-id = "' . $item->user->id . '">';
                    $back .= $item->user->username;
                    $back .= '</a>';
                    return $back;
                })
                ->addColumn('package', function ($item) {

                    $back = "";

                    $back .= '<a href="'.route('ads.show',$item->package->id).'"  data-id = "' . $item->package->id . '">';
                    $back .= $item->package->translationsAdmin->title;
                    $back .= '</a>';
                    return $back;
                })
                ->addColumn('status', function ($item) {

                    $back = "";
                    $back .= _lang('app.'.UserPackages::$status[$item->status]);
                    return $back;
                })
                ->addColumn('option', function ($item) {

                    $back = "";

                    $back .= '<a href="" class="btn btn-info" onclick = "Contact_messages.accept(this);return false;" data-id = "' . $item->id . '">';
                    $back .= '' . _lang('app.accept') . '';
                    $back .= '</a>';
                    $back .= '<a href="" class="btn btn-danger" onclick = "Contact_messages.reject(this);return false;" data-id = "' . $item->id . '">';
                    $back .= '' . _lang('app.reject') . '';
                    $back .= '</a>';
                    return $back;
                })
                
                ->escapeColumns([])
                ->make(true);
    }
}
