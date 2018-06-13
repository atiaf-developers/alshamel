<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\MotionVector;
use App\Models\MotionVectorTranslation;
use Validator;
use DB;

class MotionVectorsController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required|unique:motion_vectors'
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:motion_vectors,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:motion_vectors,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:motion_vectors,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:motion_vectors,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('motion_vectors/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('motion_vectors/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->rules = array_merge($this->rules,  $this->lang_rules(['title' => 'required|unique:motion_vectors_translations,title']));
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $motion_vector = new MotionVector;
            $motion_vector->active = $request->input('active');
            $motion_vector->this_order = $request->input('this_order');

            $motion_vector->save();

            $motion_vector_translations = array();
            $motion_vector_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $motion_vector_translations[] = array(
                    'locale' => $key,
                    'title' => $motion_vector_title[$key],
                    'motion_vector_id' => $motion_vector->id
                );
            }
            MotionVectorTranslation::insert($motion_vector_translations);
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
        $find = MotionVector::find($id);

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
        $motion_vector = MotionVector::find($id);

        if (!$motion_vector) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = MotionVectorTranslation::where('motion_vector_id', $id)->get()->keyBy('locale');
        $this->data['motion_vector'] = $motion_vector;

        return $this->_view('motion_vectors/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $motion_vector = MotionVector::find($id);
        if (!$motion_vector) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['this_order'] = 'required|unique:motion_vectors,this_order,' . $id;
        $this->rules = array_merge($this->rules, $this->lang_rules(['title' =>'required|unique:motion_vectors_translations,title,' . $id . ',motion_vector_id']));

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {

            $motion_vector->active = $request->input('active');
            $motion_vector->this_order = $request->input('this_order');

            $motion_vector->save();

            $motion_vector_translations = array();

            MotionVectorTranslation::where('motion_vector_id', $motion_vector->id)->delete();

            $motion_vector_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $motion_vector_translations[] = array(
                    'locale' => $key,
                    'title' => $motion_vector_title[$key],
                    'motion_vector_id' => $motion_vector->id
                );
            }
            MotionVectorTranslation::insert($motion_vector_translations);

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
        $motion_vector = MotionVector::find($id);
        if (!$motion_vector) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $motion_vector->delete();
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

        $motion_vectors = MotionVector::Join('motion_vectors_translations', 'motion_vectors.id', '=', 'motion_vectors_translations.motion_vector_id')
                ->where('motion_vectors_translations.locale', $this->lang_code)
                ->select([
            'motion_vectors.id', "motion_vectors_translations.title", "motion_vectors.this_order", 'motion_vectors.active',
        ]);

        return \Datatables::eloquent($motion_vectors)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('motion_vectors', 'edit') || \Permissions::check('motion_vectors', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('motion_vectors', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('motion_vectors.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('motion_vectors', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "MotionVectors.delete(this);return false;" data-id = "' . $item->id . '">';
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
