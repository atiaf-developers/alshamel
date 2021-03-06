<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Location;
use App\Models\LocationTranslation;
use App\Models\Currency;
use App\Models\CurrencyTranslation;
use Validator;
use DB;

class LocationsController extends BackendController {

    private $rules = array(
        'code' => 'required',
        'this_order' => 'required',
        'currency_id'=>'required',
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:countries,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:countries,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:countries,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:countries,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        $parent_id = $request->input('parent') ? $request->input('parent') : 0;
        $this->data['path'] = $this->node_path($parent_id);
        $this->data['parent_id'] = $parent_id;
        return $this->_view('locations/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
         $parent_id = $request->input('parent') ? $request->input('parent') : 0;
        $this->data['path'] = $this->node_path($request->input('parent'),true);
        $this->data['parent_id'] = $parent_id;
        if($parent_id==0){
            $this->data['currency']=Currency::join('currency_translations', function ($join) {
                $join->on('currency.id', '=', 'currency_translations.currency_id')
                ->where('currency_translations.locale', $this->lang_code);
            })
            ->where('currency.active',1)
            ->select(['currency_translations.title','currency.id'])->get();
        }
        
        return $this->_view('locations/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if($request->input('parent_id')!=0){
            unset($this->rules['code']);
            unset($this->rules['currency_id']);
        }else{
            $this->rules['location_image'] = 'required|image|mimes:gif,png,jpeg|max:1000';
        }
        $columns_arr = array(
            'title' => 'required|unique:locations_translations,title'
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
            $location = new Location;
            $location->code = $request->input('code');
            $location->this_order = $request->input('this_order');
            $location->parent_id = $request->input('parent_id');
            if($request->input('parent_id')==0){
                $location->currency_id = $request->input('currency_id');
                $location->image = Location::upload($request->file('location_image'), 'locations', true);
            }
           
            if ($location->parent_id != 0) {
                $parent = Location::find($location->parent_id);
                $location->level = $parent->level + 1;

                if ($parent->parents_ids == null) {
                    $location->parents_ids = $parent->id;
                }
                else{
                    $parent_ids = explode(",",$parent->parents_ids);
                    array_push($parent_ids,$parent->id);
                    $location->parents_ids = implode(",", $parent_ids);
                }
               

            } else {
                $location->level = 1;
            }
            $location->save();

            $location_translations = array();

            foreach ($request->input('title') as $key => $value) {
                $location_translations[] = array(
                    'locale' => $key,
                    'title' => $value,
                    'location_id' => $location->id
                );
            }

            LocationTranslation::insert($location_translations);

            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            dd($ex);
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
        $find = Location::find($id);

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
        $location = Location::where('locations.id', $id)
                ->select('locations.id', 'locations.parent_id', 'locations.this_order','locations.image', 'locations.code','locations.currency_id')
                ->first();

        if (!$location) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->data['path'] = $this->node_path($location->parent_id,true);
        $this->data['translations'] = LocationTranslation::where('location_id', $id)->get()->keyBy('locale');

        $this->data['parent_id'] = $location->parent_id;
        $this->data['location'] = $location;
        if($location->parent_id==0){
            $this->data['currency']=Currency::join('currency_translations', function ($join) {
                $join->on('currency.id', '=', 'currency_translations.currency_id')
                ->where('currency_translations.locale', $this->lang_code);
            })
            ->where('currency.active',1)
            ->select(['currency_translations.title','currency.id'])
            ->get();


        }
       
        return $this->_view('locations/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $location = Location::find($id);
        if (!$location) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
       
        $columns_arr = array(
            'title' => 'required|unique:locations_translations,title,' . $id . ',location_id'
        );
        if($request->input('parent_id')!=0){
            unset($this->rules['code']);
            unset($this->rules['currency_id']);
        }
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }


        DB::beginTransaction();
        try {

            $location->code = $request->input('code');
            $location->this_order = $request->input('this_order');
            if($request->input('parent_id') == 0) {
                $location->currency_id = $request->input('currency_id');
                if ($request->file('location_image')) {
                    Location::deleteUploaded('locations',$location->image);
                    $location->image = Location::upload($request->file('location_image'), 'locations', true);
                }
            }
            $location->save();
            LocationTranslation::where('location_id', $location->id)->delete();

            $location_translations = array();
            foreach ($request->input('title') as $key => $value) {
                $location_translations[] = array(
                    'locale' => $key,
                    'title' => $value,
                    'location_id' => $location->id
                );
            }
            LocationTranslation::insert($location_translations);
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
        $location = Location::find($id);
        if (!$location) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $location->delete();
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
        $parent_id = $request->input('parent_id');
        $locations = Location::Join('locations_translations', 'locations.id', '=', 'locations_translations.location_id')
                ->where('locations.parent_id', $parent_id)
                ->where('locations_translations.locale', $this->lang_code)
                ->select([
            'locations.id', "locations_translations.title", "locations.this_order", 'locations.level', 'locations.parent_id'
        ]);

        return \Datatables::eloquent($locations)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('countries', 'edit') || \Permissions::check('countries', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> '._lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('countries', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('locations.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('countries', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Locations.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->editColumn('title', function ($item) {

                            if ($item->level == 2) {
                                $back = $item->title;
                            } else {
                                $back = '<a href="' . route('locations.index') . '?parent=' . $item->id . '">' . $item->title . '</a>';
                            }
                            return $back;
                        })
                        /* ->addColumn('active', function ($item) {
                          if ($item->active == 1) {
                          $message = _lang('app.active');
                          $class = 'label-success';
                          } else {
                          $message = _lang('app.not_active');
                          $class = 'label-danger';
                          }
                          $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                          return $back;
                          }) */
                        ->escapeColumns([])
                        ->make(true);
    }

    private function node_path($id,$action=false) {
        $location = Location::where('id', $id)->first();
        $locations = null;
        if ($location) {
            $parents_ids = explode(',', $location->parents_ids);
            $parents_ids[] = $id;
            $locations = Location::leftJoin('locations_translations as trans', 'locations.id', '=', 'trans.location_id')
                    ->whereIn('locations.id', $parents_ids)
                    ->where('trans.locale', $this->lang_code)
                    ->orderBy('locations.id', 'ASC')
                    ->select('locations.id', 'trans.title')
                    ->get();
            $locations= $this->format_path($locations,$action);
        }
        return $locations;
    }

    private function format_path($locations,$action) {
        $html = '';
        if ($locations && $locations->count() > 0) {
            foreach ($locations as $key => $location) {
                if ($key < ($locations->count() - 1)) {
                    $html .= '<li><a href="' . url('admin/locations?parent=' . $location->id) . '">' . $location->title . '</a><i class="fa fa-circle"></i></li>';
                } else {
                    if($action){
                        $html .= '<li><a href="' . url('admin/locations?parent=' . $location->id) . '">' . $location->title . '</a><i class="fa fa-circle"></i></li>';
                    }else{
                         $html .= '<li><span>' . $location->title . '</span></li>';
                    }
                   
                }
            }
        }
        return $html;
    }

}
