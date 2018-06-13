<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Bath;
use App\Models\BathTranslation;
use Validator;
use DB;

class bathesController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required|unique:bathes'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:bathes,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:bathes,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:bathes,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:bathes,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('bathes/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('bathes/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->rules = array_merge($this->rules,  $this->lang_rules(['title' => 'required|unique:bathes_translations,title']));
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $bath = new Bath;
            $bath->active = $request->input('active');
            $bath->this_order = $request->input('this_order');

            $bath->save();

            $bath_translations = array();
            $bath_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $bath_translations[] = array(
                    'locale' => $key,
                    'title' => $bath_title[$key],
                    'bath_id' => $bath->id
                );
            }
            BathTranslation::insert($bath_translations);
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
        $find = Bath::find($id);

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
        $bath = Bath::find($id);

        if (!$bath) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = BathTranslation::where('bath_id', $id)->get()->keyBy('locale');
        $this->data['bath'] = $bath;

        return $this->_view('bathes/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $bath = Bath::find($id);
        if (!$bath) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['this_order'] = 'required|unique:bathes,this_order,' . $id;
        $this->rules = array_merge($this->rules, $this->lang_rules(['title' =>'required|unique:bathes_translations,title,' . $id . ',bath_id']));

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {

            $bath->active = $request->input('active');
            $bath->this_order = $request->input('this_order');

            $bath->save();

            $bath_translations = array();

            BathTranslation::where('bath_id', $bath->id)->delete();

            $bath_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $bath_translations[] = array(
                    'locale' => $key,
                    'title' => $bath_title[$key],
                    'bath_id' => $bath->id
                );
            }
            BathTranslation::insert($bath_translations);

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
        $bath = Bath::find($id);
        if (!$bath) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $bath->delete();
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

        $bathes = Bath::Join('bathes_translations', 'bathes.id', '=', 'bathes_translations.bath_id')
                ->where('bathes_translations.locale', $this->lang_code)
                ->select([
            'bathes.id', "bathes_translations.title", "bathes.this_order", 'bathes.active',
        ]);

        return \Datatables::eloquent($bathes)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('bathes', 'edit') || \Permissions::check('bathes', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('bathes', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('bathes.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('bathes', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Bathes.delete(this);return false;" data-id = "' . $item->id . '">';
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
