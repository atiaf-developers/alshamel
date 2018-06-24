<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\BasicData;
use App\Models\BasicDataTranslation;
use Validator;
use DB;

class BasicDataController extends BackendController
{
    private $rules = array(
        'active' => 'required',
        // 'type' => 'required',
    );
    public function __construct() {
        
        parent::__construct();
        // $this->data['type']=$request->type;
        // $type=BasicData::$types[$request->type];
       
        
    }

    public function index(Request $request) {
        
        if(array_key_exists($request->type,BasicData::$types)){
            $this->data['type']=$request->type;
            $this->data['type_title']=BasicData::$types[$request->type];
            // dd($this->data['type_title']);
            $this->middleware('CheckPermission:'.$this->data['type_title'].',open');
            return $this->_view('basic_data/index', 'backend');
        }else{
            $this->err404();
        }
      
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if(array_key_exists($request->type,BasicData::$types)){
            $this->data['type']=$request->type;
            $this->data['type_title']=BasicData::$types[$request->type];
            $this->middleware('CheckPermission:'.$this->data['type_title'].',add', ['only' => ['create']]);
           // dd($this->middleware('CheckPermission:'.$this->data['type_title'].',add', ['only' => ['create']]));
            return $this->_view('basic_data/create', 'backend');
        }else{
            $this->err404();
        }  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if(array_key_exists($request->input('type'),BasicData::$types)){
            $this->data['type_title']=BasicData::$types[$request->input('type')];
            $this->middleware('CheckPermission:'.$this->data['type_title'].',add', ['only' => ['store']]);

            $this->rules = array_merge($this->rules,  $this->lang_rules(['title' => 'required']));
            $this->rules = array_merge($this->rules,['this_order' => "required|unique:basic_data,this_order,NULL,id,type,{$request->type}"]);
            $validator = Validator::make($request->all(), $this->rules);
            // dd($validator->fails());
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _json('error', $errors);
            }
            // dd('asas');
            DB::beginTransaction();
            try {
                $data = new BasicData;
                $data->active = $request->input('active');
                $data->type = $request->input('type');
                $data->this_order = $request->input('this_order');
    
                $data->save();
    
                $data_translations = array();
                $data_title = $request->input('title');
    
                foreach ($this->languages as $key => $value) {
                    $data_translations[] = array(
                        'locale' => $key,
                        'title' => $data_title[$key],
                        'basic_data_id' => $data->id
                    );
                }
                BasicDataTranslation::insert($data_translations);
                DB::commit();
                return _json('success', _lang('app.added_successfully'));
            } catch (\Exception $ex) {
                DB::rollback();
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }else{
            $this->err404();
        }

    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = BasicData::find($id);

        if ($find) {
            if(array_key_exists($find->type,BasicData::$types)){
                $this->data['type_title']=BasicData::$types[$find->type];
                $this->middleware('CheckPermission:'.$this->data['type_title'].',edit', ['only' => ['show']]);
                return _json('success', $find);
            }else{
                $this->err404();
            }
           
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
        $data = BasicData::find($id);

        if (!$data) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        if(array_key_exists($data->type,BasicData::$types)){
            $this->data['type_title']=BasicData::$types[$data->type];
            $this->middleware('CheckPermission:'.$this->data['type_title'].',edit', ['only' => ['edit']]);
            $this->data['translations'] = BasicDataTranslation::where('basic_data_id', $id)->get()->keyBy('locale');
            $this->data['info'] = $data;

            return $this->_view('basic_data/edit', 'backend');
        }else{
            $this->err404();
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $basic_data = BasicData::find($id);
        if (!$basic_data) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        if(array_key_exists($basic_data->type,BasicData::$types)){
            $this->data['type_title']=BasicData::$types[$basic_data->type];
            $this->middleware('CheckPermission:'.$this->data['type_title'].',edit', ['only' => ['update']]);
            $this->rules['this_order'] = "required|unique:basic_data,this_order,{$id},id,type,{$basic_data->type}";
            $this->rules = array_merge($this->rules, $this->lang_rules(['title' => 
                "required|unique:basic_data_translations,title,{$id},basic_data_id"]));

            $validator = Validator::make($request->all(), $this->rules);
            
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _json('error', $errors);
            }
            
            DB::beginTransaction();
            try {
    
                $basic_data->active = $request->input('active');
                $basic_data->this_order = $request->input('this_order');
    
                $basic_data->save();
    
                $basic_data_translations = array();
    
                BasicDataTranslation::where('basic_data_id', $basic_data->id)->delete();
    
                $basic_data_title = $request->input('title');
    
                foreach ($this->languages as $key => $value) {
                    $basic_data_translations[] = array(
                        'locale' => $key,
                        'title' => $basic_data_title[$key],
                        'basic_data_id' => $basic_data->id
                    );
                }
                BasicDataTranslation::insert($basic_data_translations);
    
                DB::commit();
                return _json('success', _lang('app.updated_successfully'));
            } catch (\Exception $ex) {
                DB::rollback();
                return _json('error', $ex, 400);
            }
        }else{
            $this->err404();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $basic_data = BasicData::find($id);
        if (!$basic_data) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        if(array_key_exists($basic_data->type,BasicData::$types)){
            DB::beginTransaction();
            $this->data['type_title']=BasicData::$types[$basic_data->type];
            $this->middleware('CheckPermission:'.$this->data['type_title'].',delete', ['only' => ['destroy']]);

            try {
                $basic_data->delete();
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
            
        }else{
            $this->err404();
        }
        

    }

    public function data(Request $request) {
        $type=$request->type;
        $this->data['type_title']=BasicData::$types[$type];
        $bathes = BasicData::Join('basic_data_translations', 'basic_data.id', '=', 'basic_data_translations.basic_data_id')
                ->where('basic_data_translations.locale', $this->lang_code)
                ->where('basic_data.type', $type)
                ->select([
            'basic_data.id', "basic_data_translations.title", "basic_data.this_order", 'basic_data.active',
        ]);
                    // dd($bathes);
        return \Datatables::eloquent($bathes)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check(''.$this->data['type_title'].'', 'edit') || \Permissions::check(''.$this->data['type_title'].'', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check(''.$this->data['type_title'].'', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('basic_data.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check(''.$this->data['type_title'].'', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "BasicData.delete(this);return false;" data-id = "' . $item->id . '">';
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
