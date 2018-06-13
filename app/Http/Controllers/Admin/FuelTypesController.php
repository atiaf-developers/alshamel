<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\FuelType;
use App\Models\FuelTypeTranslation;
use Validator;
use DB;

class FuelTypesController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required|unique:fuel_types'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:fuel_types,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:fuel_types,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:fuel_types,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:fuel_types,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('fuel_types/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('fuel_types/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->rules = array_merge($this->rules,  $this->lang_rules(['title' => 'required|unique:fuel_types_translations,title']));
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $fuel_type = new FuelType;
            $fuel_type->active = $request->input('active');
            $fuel_type->this_order = $request->input('this_order');

            $fuel_type->save();

            $fuel_type_translations = array();
            $fuel_type_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $fuel_type_translations[] = array(
                    'locale' => $key,
                    'title' => $fuel_type_title[$key],
                    'fuel_type_id' => $fuel_type->id
                );
            }
            FuelTypeTranslation::insert($fuel_type_translations);
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
        $find = FuelType::find($id);

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
        $fuel_type = FuelType::find($id);

        if (!$fuel_type) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = FuelTypeTranslation::where('fuel_type_id', $id)->get()->keyBy('locale');
        $this->data['fuel_type'] = $fuel_type;

        return $this->_view('fuel_types/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $fuel_type = FuelType::find($id);
        if (!$fuel_type) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['this_order'] = 'required|unique:fuel_types,this_order,' . $id;
        $this->rules = array_merge($this->rules, $this->lang_rules(['title' =>'required|unique:fuel_types_translations,title,' . $id . ',fuel_type_id']));

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {

            $fuel_type->active = $request->input('active');
            $fuel_type->this_order = $request->input('this_order');

            $fuel_type->save();

            $fuel_type_translations = array();

            FuelTypeTranslation::where('fuel_type_id', $fuel_type->id)->delete();

            $fuel_type_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $fuel_type_translations[] = array(
                    'locale' => $key,
                    'title' => $fuel_type_title[$key],
                    'fuel_type_id' => $fuel_type->id
                );
            }
            FuelTypeTranslation::insert($fuel_type_translations);

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
        $fuel_type = FuelType::find($id);
        if (!$fuel_type) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $fuel_type->delete();
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

        $fuel_types = FuelType::Join('fuel_types_translations', 'fuel_types.id', '=', 'fuel_types_translations.fuel_type_id')
                ->where('fuel_types_translations.locale', $this->lang_code)
                ->select([
            'fuel_types.id', "fuel_types_translations.title", "fuel_types.this_order", 'fuel_types.active',
        ]);

        return \Datatables::eloquent($fuel_types)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('fuel_types', 'edit') || \Permissions::check('fuel_types', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('fuel_types', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('fuel_types.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('fuel_types', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "FuelTypes.delete(this);return false;" data-id = "' . $item->id . '">';
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
