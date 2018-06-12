<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\PropertyType;
use App\Models\PropertyTypeTranslation;
use Validator;
use DB;

class PropertyTypesController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required|unique:property_types'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:property_types,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:property_types,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:property_types,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:property_types,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('property_types/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('property_types/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->rules = array_merge($this->rules,  $this->lang_rules(['title' => 'required|unique:property_types_translations,title']));
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $property_type = new PropertyType;
            $property_type->active = $request->input('active');
            $property_type->this_order = $request->input('this_order');

            $property_type->save();

            $property_type_translations = array();
            $property_type_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $property_type_translations[] = array(
                    'locale' => $key,
                    'title' => $property_type_title[$key],
                    'property_type_id' => $property_type->id
                );
            }
            PropertyTypeTranslation::insert($property_type_translations);
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
        $find = PropertyType::find($id);

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
        $property_type = PropertyType::find($id);

        if (!$property_type) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = PropertyTypeTranslation::where('property_type_id', $id)->get()->keyBy('locale');
        $this->data['property_type'] = $property_type;

        return $this->_view('property_types/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $property_type = PropertyType::find($id);
        if (!$property_type) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['this_order'] = 'required|unique:property_types,this_order,' . $id;
        $this->rules = array_merge($this->rules, $this->lang_rules(['title' =>'required|unique:property_types_translations,title,' . $id . ',property_type_id']));

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {

            $property_type->active = $request->input('active');
            $property_type->this_order = $request->input('this_order');

            $property_type->save();

            $property_type_translations = array();

            PropertyTypeTranslation::where('property_type_id', $property_type->id)->delete();

            $property_type_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $property_type_translations[] = array(
                    'locale' => $key,
                    'title' => $property_type_title[$key],
                    'property_type_id' => $property_type->id
                );
            }
            PropertyTypeTranslation::insert($property_type_translations);

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
        $property_type = PropertyType::find($id);
        if (!$property_type) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $property_type->delete();
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

        $property_types = PropertyType::Join('property_types_translations', 'property_types.id', '=', 'property_types_translations.property_type_id')
                ->where('property_types_translations.locale', $this->lang_code)
                ->select([
            'property_types.id', "property_types_translations.title", "property_types.this_order", 'property_types.active',
        ]);

        return \Datatables::eloquent($property_types)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('property_types', 'edit') || \Permissions::check('property_types', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('property_types', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('property_types.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('property_types', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "PropertyTypes.delete(this);return false;" data-id = "' . $item->id . '">';
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
