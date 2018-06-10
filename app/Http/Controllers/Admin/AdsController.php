<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Ad;
use App\Models\Feature;
use Validator;
use DB;

class AdsController extends BackendController
{
    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:ads,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:ads,delete', ['only' => ['delete']]);
        $this->middleware('CheckPermission:ads,delete', ['only' => ['delete']]);
    }
    public function index(Request $request) {
        return $this->_view('ads/index', 'backend');
    }
    public function active($id){
        try {
            $find = Ad::find($id);
            $find->active = !$find->active;
            $find->save();
            return _json('success', _lang('app.success'));
        } catch (\Exception $e) {
            return _json('error', _lang('app.error_is_occured'));
        }
    }
    public function special($id){
        try {
            $find = Ad::find($id);
            $find->special = !$find->special;
            $find->save();
            return _json('success', _lang('app.success'));
        } catch (\Exception $e) {
            return _json('error', _lang('app.error_is_occured'));
        }
    }
    public function edit($id){
        $find = Ad::find($id);
        if (!$find) {
            return $this->err404();
        }
        $this->data['package'] = $find;
        $this->data['Features']=$find->Features;
       dd($this->data);
        return $this->_view('ads/edit', 'backend');
    }
    public function destroy($id){
        $Ad = Ad::find($id);
        if (!$Ad) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            Feature::where('ad_id',$id)->delete();
            $Ad->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }
    public function data(Request $req){
        if($req->category_id){
            $request=$req->category_id;
            $where='ads.category_id';
        }else{
            if($req->user_id){
                $request=$req->category_id;
                $where='ads.user_id';
            }
        }
        $Ad = Ad::Join('categories_translations',function ($join){
                $join->on('categories_translations.id', '=', 'ads.category_two_id')
                ->where('categories_translations.locale', $this->lang_code);
        });
        if($req->category_id || $req->user_id){
            $Ad = $Ad->where($where,$request);
        }
        $Ad = $Ad->select([
            'ads.id',
            'ads.title',
            "ads.email",
            "ads.mobile",
            "ads.special",
            "ads.active",
            "categories_translations.title as cat_title"
        ]);
        
        return \Datatables::eloquent($Ad)
                ->addColumn('options', function ($item) {

                $back = "";
                if (\Permissions::check('ads', 'edit') || \Permissions::check('ads', 'delete')) {
                    $back .= '<div class="btn-group">';
                    $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                    $back .= '<i class="fa fa-angle-down"></i>';
                    $back .= '</button>';
                    $back .= '<ul class = "dropdown-menu" role = "menu">';
                    if (\Permissions::check('ads', 'edit')) {
                        $back .= '<li>';
                        $back .= '<a href="' . route('ads.edit', $item->id) . '">';
                        $back .= '<i class = "icon-docs"></i>' . _lang('app.view');
                        $back .= '</a>';
                        $back .= '</li>';
                    }

                    if (\Permissions::check('ads', 'delete')) {
                        $back .= '<li>';
                        $back .= '<a href="" data-toggle="confirmation" onclick = "Ads.delete(this);return false;" data-id = "' . $item->id . '">';
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
                if ($item->active == 1) {
                    $message = _lang('app.active');
                    $class = 'btn-primary';
                } else {
                    $message = _lang('app.not_active');
                    $class = 'btn-danger';
                }
                $back = '<a class="btn ' . $class . '" onclick = "Ads.active(this);return false;" data-id = "' . $item->id . '" data-status = "' . $item->active . '">' . $message . ' <a>';
                return $back;
            })
            ->addColumn('special', function ($item) {
                if ($item->special == 1) {
                    $message = _lang('app.special');
                    $class = 'btn-primary';
                } else {
                    $message = _lang('app.not_special');
                    $class = 'btn-danger';
                }
                $back = '<a class="btn ' . $class . '" onclick = "Ads.special(this);return false;" data-id = "' . $item->id . '" data-status = "' . $item->active . '">' . $message . ' <a>';
                return $back;
            })
            ->escapeColumns([])
            ->make(true);
    }
}
