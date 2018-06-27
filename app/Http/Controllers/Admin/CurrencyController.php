<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Currency;
use App\Models\CurrencyTranslation;
use DB;
use Validator;

class CurrencyController extends BackendController
{
    private $rules = array(
        'this_order' => 'required',
        'active' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:currency,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:currency,add', ['only' => ['create','store']]);
        $this->middleware('CheckPermission:currency,edit', ['only' => ['edit', 'update']]);
        $this->middleware('CheckPermission:currency,delete', ['only' => ['delete']]);
    }
    public function index(){
        return $this->_view('currency/index', 'backend');
    }
    public function create() {
        return $this->_view('currency/create', 'backend');
    }
    public function store(Request $request){
        $columns_arr = array(
            'title' => 'required|unique:currency_translations,title',
            'sign' => 'required|unique:currency_translations,sign',
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
            $currency = new Currency;
            $currency->active = $request->input('active');
            $currency->this_order = $request->input('this_order');
            $currency->save();

            $currency_translations = array();
            $title = $request->input('title');
            $sign = $request->input('sign');

            foreach ($this->languages as $key => $value) {
                $currency_translations[] = array(
                    'locale' => $key,
                    'title' => $title[$key],
                    'sign' => $sign[$key],
                    'currency_id' => $currency->id
                );
            }
            CurrencyTranslation::insert($currency_translations);
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }
    public function edit($id){
        $find = Currency::find($id);
        if (!$find) {
            return $this->err404();
        }
        $this->data['currency'] = $find;
        $this->data['translations'] = CurrencyTranslation::where('currency_id', $id)->get()->keyBy('locale');
        return $this->_view('currency/edit', 'backend');
    }
    public function update(Request $request, $id){
        $currency = Currency::find($id);
        if (!$currency) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $columns_arr = array(
            'title' => 'required|unique:currency_translations,title,'.$id .',currency_id',
            'sign'  => 'required|unique:currency_translations,sign,'.$id .',currency_id',
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
            $currency->active = $request->input('active');
            $currency->this_order = $request->input('this_order');
            $currency->save();
            CurrencyTranslation::where('currency_id', $currency->id)->delete();

            $currency_translations = array();
            $title = $request->input('title');
            $sign = $request->input('sign');

            foreach ($this->languages as $key => $value) {
                $currency_translations[] = array(
                    'locale' => $key,
                    'title' => $title[$key],
                    'sign' => $sign[$key],
                    'currency_id' => $currency->id
                );
            }
            CurrencyTranslation::insert($currency_translations);
            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', $ex->getMessage(), 400);
        }
    }
    public function destroy($id){
        $currency = Currency::find($id);
        if (!$currency) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $currency->delete();
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
        $currency = Currency::Join('currency_translations', 'currency.id', '=', 'currency_translations.currency_id')
        ->where('currency_translations.locale', $this->lang_code)
        ->select([
            'currency.id','currency.this_order', "currency_translations.title","currency.active"
        ]);
        return \Datatables::eloquent($currency)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('currency', 'edit') || \Permissions::check('currency', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('currency', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('currency.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('currency', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Currency.delete(this);return false;" data-id = "' . $item->id . '">';
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
