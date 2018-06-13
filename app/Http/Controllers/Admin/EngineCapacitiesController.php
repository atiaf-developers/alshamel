<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\EngineCapacity;
use App\Models\EngineCapacityTranslation;
use Validator;
use DB;

class EngineCapacitiesController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required|unique:engine_capacities'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:engine_capacities,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:engine_capacities,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:engine_capacities,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:engine_capacities,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('engine_capacities/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('engine_capacities/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->rules = array_merge($this->rules,  $this->lang_rules(['title' => 'required|unique:engine_capacities_translations,title']));
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $engine_capacity = new EngineCapacity;
            $engine_capacity->active = $request->input('active');
            $engine_capacity->this_order = $request->input('this_order');

            $engine_capacity->save();

            $engine_capacity_translations = array();
            $engine_capacity_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $engine_capacity_translations[] = array(
                    'locale' => $key,
                    'title' => $engine_capacity_title[$key],
                    'engine_capacity_id' => $engine_capacity->id
                );
            }
            EngineCapacityTranslation::insert($engine_capacity_translations);
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
        $find = EngineCapacity::find($id);

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
        $engine_capacity = EngineCapacity::find($id);

        if (!$engine_capacity) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = EngineCapacityTranslation::where('engine_capacity_id', $id)->get()->keyBy('locale');
        $this->data['engine_capacity'] = $engine_capacity;

        return $this->_view('engine_capacities/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $engine_capacity = EngineCapacity::find($id);
        if (!$engine_capacity) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['this_order'] = 'required|unique:engine_capacities,this_order,' . $id;
        $this->rules = array_merge($this->rules, $this->lang_rules(['title' =>'required|unique:engine_capacities_translations,title,' . $id . ',engine_capacity_id']));

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {

            $engine_capacity->active = $request->input('active');
            $engine_capacity->this_order = $request->input('this_order');

            $engine_capacity->save();

            $engine_capacity_translations = array();

            EngineCapacityTranslation::where('engine_capacity_id', $engine_capacity->id)->delete();

            $engine_capacity_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $engine_capacity_translations[] = array(
                    'locale' => $key,
                    'title' => $engine_capacity_title[$key],
                    'engine_capacity_id' => $engine_capacity->id
                );
            }
            EngineCapacityTranslation::insert($engine_capacity_translations);

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
        $engine_capacity = EngineCapacity::find($id);
        if (!$engine_capacity) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $engine_capacity->delete();
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

        $engine_capacities = EngineCapacity::Join('engine_capacities_translations', 'engine_capacities.id', '=', 'engine_capacities_translations.engine_capacity_id')
                ->where('engine_capacities_translations.locale', $this->lang_code)
                ->select([
            'engine_capacities.id', "engine_capacities_translations.title", "engine_capacities.this_order", 'engine_capacities.active',
        ]);

        return \Datatables::eloquent($engine_capacities)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('engine_capacities', 'edit') || \Permissions::check('engine_capacities', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('engine_capacities', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('engine_capacities.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('engine_capacities', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "EngineCapacities.delete(this);return false;" data-id = "' . $item->id . '">';
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
