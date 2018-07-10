<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Slider;
use App\Models\SliderTranslation;
use Validator;
use DB;

class SliderController extends BackendController
{
    private $rules = array(
        'image' => 'required',
        'url' => 'required',
        'this_order' => 'required',
        'active' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:slider,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:slider,add', ['only' => ['create','store']]);
        $this->middleware('CheckPermission:slider,edit', ['only' => ['edit', 'update']]);
        $this->middleware('CheckPermission:slider,delete', ['only' => ['delete']]);
    }
    public function index(){
        return $this->_view('slider/index', 'backend');
    }
    public function create() {
        return $this->_view('slider/create', 'backend');
    }
    public function store(Request $request){
        $columns = array(
            'title' => 'required',
        );
        $lang_rules = $this->lang_rules($columns);
        $this->rules = array_merge($this->rules, $lang_rules);
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $slider = new Slider;
            $slider->image = Slider::upload($request->file('image'), 'slider', true);
            $slider->active = $request->input('active');
            $slider->this_order = $request->input('this_order');
            $slider->url = $request->input('url');
            $slider->save();
            
            $slider_translations = array();
            $title = $request->input('title');
            foreach ($this->languages as $key => $value) {
                $slider_translations[] = array(
                    'locale' => $key,
                    'title' => $title[$key],
                    'slider_id' => $slider->id
                );
            }
            SliderTranslation::insert($slider_translations);
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }
    public function edit($id){
        $find = Slider::find($id);
        if (!$find) {
            return $this->err404();
        }
        $this->data['slider'] = $find;
        $this->data['translations'] = SliderTranslation::where('slider_id', $id)->get()->keyBy('locale');
        return $this->_view('slider/edit', 'backend');
    }


    public function update(Request $request, $id){
        $slider = Slider::find($id);
        if (!$slider) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $columns = array(
            'title' => 'required',
        );
        $lang_rules = $this->lang_rules($columns);
        $this->rules = array_merge($this->rules, $lang_rules);

        unset($this->rules['image']);

        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            if ($request->file('image')) {
                if ($slider->image) {
                    $old_image = $slider->image;
                    Slider::deleteUploaded('slider', $old_image);
                }
                $slider->image = Slider::upload($request->file('image'), 'slider', true);
            }
            $slider->active = $request->input('active');
            $slider->this_order = $request->input('this_order');
            $slider->url = $request->input('url');
            $slider->save();

            SliderTranslation::where('slider_id', $slider->id)->delete();

            $slider_translations = array();
            $title = $request->input('title');
            foreach ($this->languages as $key => $value) {
                $slider_translations[] = array(
                    'locale' => $key,
                    'title' => $title[$key],
                    'slider_id' => $slider->id
                );
            }
            SliderTranslation::insert($slider_translations);

            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', $ex->getMessage(), 400);
        }
    }
    public function destroy($id){
        $slider = Slider::find($id);
        if (!$slider) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $slider->delete();
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
        $slider = slider::Join('slider_translations', 'slider.id', '=', 'slider_translations.slider_id')
        ->where('slider_translations.locale', $this->lang_code)
        ->select([
            'slider.id','slider.this_order', "slider.image","slider.active"
        ]);
        return \Datatables::eloquent($slider)
        ->addColumn('options', function ($item) {

            $back = "";
            if (\Permissions::check('slider', 'edit') || \Permissions::check('slider', 'delete')) {
                $back .= '<div class="btn-group">';
                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                $back .= '<i class="fa fa-angle-down"></i>';
                $back .= '</button>';
                $back .= '<ul class = "dropdown-menu" role = "menu">';
                if (\Permissions::check('slider', 'edit')) {
                    $back .= '<li>';
                    $back .= '<a href="' . route('slider.edit', $item->id) . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                if (\Permissions::check('slider', 'delete')) {
                    $back .= '<li>';
                    $back .= '<a href="" data-toggle="confirmation" onclick = "Slider.delete(this);return false;" data-id = "' . $item->id . '">';
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
        ->editColumn('image', function ($item) {
            $back = '<img src="' . url('public/uploads/slider/' . $item->image) . '" style="height:70px;width:120px;"/>';
            return $back;
        })
        ->escapeColumns([])
        ->make(true);
    }
}
