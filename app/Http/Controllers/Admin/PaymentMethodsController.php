<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodTranslation;
use Validator;
use DB;

class PaymentMethodsController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required|unique:payment_methods'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:payment_methods,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:payment_methods,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:payment_methods,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:payment_methods,delete', ['only' => ['delete']]);
    }


    public function index(Request $request) {
        return $this->_view('payment_methods/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('payment_methods/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->rules = array_merge($this->rules,  $this->lang_rules(['title' => 'required|unique:payment_methods_translations,title']));
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $payment_method = new PaymentMethod;
            $payment_method->active = $request->input('active');
            $payment_method->this_order = $request->input('this_order');

            $payment_method->save();

            $payment_method_translations = array();
            $payment_method_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $payment_method_translations[] = array(
                    'locale' => $key,
                    'title' => $payment_method_title[$key],
                    'payment_method_id' => $payment_method->id
                );
            }
            PaymentMethodTranslation::insert($payment_method_translations);
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = PaymentMethod::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $payment_method = PaymentMethod::find($id);

        if (!$payment_method) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = PaymentMethodTranslation::where('payment_method_id', $id)->get()->keyBy('locale');
        $this->data['payment_method'] = $payment_method;

        return $this->_view('payment_methods/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $payment_method = PaymentMethod::find($id);
        if (!$payment_method) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['this_order'] = 'required|unique:payment_methods,this_order,' . $id;
        $this->rules = array_merge($this->rules, $this->lang_rules(['title' =>'required|unique:payment_methods_translations,title,' . $id . ',payment_method_id']));

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {

            $payment_method->active = $request->input('active');
            $payment_method->this_order = $request->input('this_order');

            $payment_method->save();

            $payment_method_translations = array();

            PaymentMethodTranslation::where('payment_method_id', $payment_method->id)->delete();

            $payment_method_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $payment_method_translations[] = array(
                    'locale' => $key,
                    'title' => $payment_method_title[$key],
                    'payment_method_id' => $payment_method->id
                );
            }
            PaymentMethodTranslation::insert($payment_method_translations);

            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $payment_method = PaymentMethod::find($id);
        if (!$payment_method) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $payment_method->delete();
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

    public function data(Request $request) {

        $payment_methods = PaymentMethod::Join('payment_methods_translations', 'payment_methods.id', '=', 'payment_methods_translations.payment_method_id')
                ->where('payment_methods_translations.locale', $this->lang_code)
                ->select([
            'payment_methods.id', "payment_methods_translations.title", "payment_methods.this_order", 'payment_methods.active',
        ]);

        return \Datatables::eloquent($payment_methods)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('payment_methods', 'edit') || \Permissions::check('payment_methods', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('payment_methods', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('payment_methods.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('payment_methods', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "PaymentMethods.delete(this);return false;" data-id = "' . $item->id . '">';
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
