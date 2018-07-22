<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Package;
use App\Models\PackageTranslation;
use DB;
use Validator;

class PackagesController extends BackendController
{
    private $rules = array(
        'num_of_ads' => 'required',
        'price' => 'required',
        'this_order' => 'required',
        'active' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:packages,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:packages,add', ['only' => ['create','store']]);
        $this->middleware('CheckPermission:packages,edit', ['only' => ['edit', 'update']]);
        $this->middleware('CheckPermission:packages,delete', ['only' => ['delete']]);
    }
    public function index(){
        return $this->_view('packages/index', 'backend');
    }
    public function create() {
        return $this->_view('packages/create', 'backend');
    }
    public function store(Request $request){
        $columns_arr = array(
            'title' => 'required|unique:packages_translations,title',
        );
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $package = new Package;
            $package->num_of_ads = $request->input('num_of_ads');
            $package->price = $request->input('price');
            $package->active = $request->input('active');
            $package->this_order = $request->input('this_order');
            $package->save();

            $package_translations = array();
            $title = $request->input('title');
            foreach ($this->languages as $key => $value) {
                $package_translations[] = array(
                    'locale' => $key,
                    'title' => $title[$key],
                    'package_id' => $package->id
                );
            }
            PackageTranslation::insert($package_translations);
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }
    public function edit($id){
        $find = Package::find($id);
        if (!$find) {
            return $this->err404();
        }
        $this->data['package'] = $find;
        $this->data['translations'] = PackageTranslation::where('package_id', $id)->get()->keyBy('locale');
        return $this->_view('packages/edit', 'backend');
    }
    public function update(Request $request, $id){
        $package = Package::find($id);
        if (!$package) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $columns_arr = array(
            'title' => 'required|unique:packages_translations,title,'.$id .',package_id',
        );

        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $package->num_of_ads = $request->input('num_of_ads');
            $package->price = $request->input('price');
            $package->active = $request->input('active');
            $package->this_order = $request->input('this_order');
            $package->save();
            PackageTranslation::where('package_id', $package->id)->delete();

            $packageTranslation = array();
            $title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $packageTranslation[] = array(
                    'locale' => $key,
                    'title' => $title[$key],
                    'package_id' => $package->id
                ); 
            }
            
            PackageTranslation::insert($packageTranslation);
            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', $ex->getMessage(), 400);
        }
    }
    public function destroy($id){
        $package = Package::find($id);
        if (!$package) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $package->delete();
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
    public function data(Request $request){
        $package = Package::Join('packages_translations', 'packages.id', '=', 'packages_translations.package_id')
        ->where('packages_translations.locale', $this->lang_code)
        ->select([
            'packages.id','packages.this_order', "packages_translations.title","packages.active","packages.price"
        ]);
        return \Datatables::eloquent($package)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('packages', 'edit') || \Permissions::check('packages', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('packages', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('packages.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('packages', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Packages.delete(this);return false;" data-id = "' . $item->id . '">';
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
                                $class = 'label-success';
                            } else {
                                $message = _lang('app.not_active');
                                $class = 'label-danger';
                            }
                            $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                            return $back;
                        })
                        ->escapeColumns([])
                        ->make(true);
    }
}
