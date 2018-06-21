<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Ad;
use Validator;
use DB;

class CategoriesController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required'
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:categories,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:categories,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:categories,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:categories,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {

        $parent_id = $request->input('parent') ? $request->input('parent') : 0;
        $this->data['path'] = $this->node_path($parent_id);
        $this->data['parent_id'] = $parent_id;

        return $this->_view('categories/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $parent_id = $request->input('parent') ? $request->input('parent') : 0;
        $this->data['path'] = $this->node_path($request->input('parent'), true);
        $this->data['parent_id'] = $parent_id;
        $this->data['form_types'] = Ad::$form_types;
        if ($parent_id != 0) {
            $category = Category::find($parent_id);
            $this->data['level'] = $category->level + 1;
        } else {
            $this->data['level'] = 1;
        }

        return $this->_view('categories/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->rules['this_order'] = "required|unique:categories,this_order,NULL,id,parent_id,{$request->parent_id}";
        if ($request->level == 1 || $request->level == 2) {
            $this->rules['image'] = 'required|image|mimes:gif,png,jpeg|max:1000';
        }
        if ($request->level == 2 || $request->level == 3) {
            $this->rules['form_type'] = 'required';
        }
        if ($request->input('num_of_levels') == 3) {
           $this->rules = array_merge($this->rules, $this->lang_rules(['label' => 'required|unique:categories_translations,label']));
        }
        $this->rules = array_merge($this->rules, $this->lang_rules(['title' => 'required|unique:categories_translations,title']));
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {


            $category = new Category;
            $category->slug = str_slug($request->input('title')['en']);
            $category->active = $request->input('active');
            $category->this_order = $request->input('this_order');
            $category->parent_id = $request->input('parent_id');
            if ($request->level == 2 || $request->level == 3) {
                $category->form_type = $request->form_type;
            }
            if ($request->level == 1 || $request->level == 2) {
                $category->image = Category::upload($request->file('image'), 'categories', true);
            }

            if ($category->parent_id != 0) {
                $parent = Category::find($category->parent_id);
                $category->level = $parent->level + 1;
                if ($parent->parents_ids == null) {
                    $category->parents_ids = $parent->id;
                } else {
                    $parent_ids = explode(",", $parent->parents_ids);
                    array_push($parent_ids, $parent->id);
                    $category->parents_ids = implode(",", $parent_ids);
                }
            } else {
                $category->level = 1;
                $category->num_of_levels = $request->input('num_of_levels');
            }

            $category->save();

            $category_translations = array();

            $title = $request->input('title');
            $label = $request->input('label');

            if ($label) {
                foreach ($this->languages as $key => $value) {
                     $category_translations[] = array(
                    'locale' => $key,
                    'title' => $title[$key],
                    'label' => $label[$key],
                    'category_id' => $category->id
                    ); 
                }
            }else{
                foreach ($this->languages as $key => $value) {
                   $category_translations[] = array(
                        'locale' => $key,
                        'title' => $title[$key],
                        'category_id' => $category->id
                    );
                }
            }
            CategoryTranslation::insert($category_translations);

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
        $find = Category::find($id);

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
        $category = Category::find($id);

        if (!$category) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->data['path'] = $this->node_path($category->parent_id, true);
        $this->data['translations'] = CategoryTranslation::where('category_id', $id)->get()->keyBy('locale');
        $this->data['parent_id'] = $category->parent_id;
        $this->data['category'] = $category;
        $this->data['form_types'] = Ad::$form_types;
        $this->data['level'] = $category->level;

        return $this->_view('categories/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {


        $category = category::find($id);
        if (!$category) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
     
        
        $this->rules['this_order'] = "required|unique:categories,this_order,{$id},id,parent_id,{$request->parent_id}";
        if ($request->level == 1 || $request->level == 2) {
            $this->rules['image'] = 'image|mimes:gif,png,jpeg|max:1000';
        }
        if ($request->level == 2 || $request->level == 3) {
            $this->rules['form_type'] = 'required';
        }
        if ($request->input('num_of_levels') == 3) {
           $this->rules = array_merge($this->rules, $this->lang_rules(['label' => "required|unique:categories_translations,label,{$id},category_id"]));
        }
        $this->rules = array_merge($this->rules, $this->lang_rules(['title' => 'required|unique:categories_translations,title,' . $id . ',category_id']));
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }


        DB::beginTransaction();
        try {
            $category->slug = str_slug($request->input('title')['en']);
            $category->active = $request->input('active');
            $category->this_order = $request->input('this_order');
            if ($request->level == 2 || $request->level == 3) {
                $category->form_type = $request->form_type;
            }
            if ($request->level == 1 || $request->level == 2) {
                if ($request->file('image')) {
                    if ($category->image) {
                        $old_image = $category->image;
                        Category::deleteUploaded('categories', $old_image);
                    }
                    $category->image = Category::upload($request->file('image'), 'categories', true);
                }
            }
            if ($request->input('level') == 1) {
                $category->num_of_levels = $request->input('num_of_levels');
            }
            $category->save();

            CategoryTranslation::where('category_id', $category->id)->delete();

            $category_translations = array();
            $title = $request->input('title');
            $label = $request->input('label');

            if ($label) {
                foreach ($this->languages as $key => $value) {
                     $category_translations[] = array(
                    'locale' => $key,
                    'title' => $title[$key],
                    'label' => $label[$key],
                    'category_id' => $category->id
                    ); 
                }
            }else{
                foreach ($this->languages as $key => $value) {
                   $category_translations[] = array(
                        'locale' => $key,
                        'title' => $title[$key],
                        'category_id' => $category->id
                    );
                }
            }
            CategoryTranslation::insert($category_translations);
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
        $category = Category::find($id);
        if (!$category) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $category->delete();
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

        $categories = Category::Join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
                              ->leftJoin('categories as c2','c2.id','=','categories.parent_id')
                              ->leftJoin('categories as c3','c3.id','=','c2.parent_id')
                ->where('categories.parent_id', $parent_id)
                ->where('categories_translations.locale', $this->lang_code)
                ->orderBy('categories.this_order')
                ->select([
            'categories.id', "categories_translations.title", "categories.this_order", "categories.active", 'categories.level', 'categories.parent_id','c2.num_of_levels as first_num_of_levels','c3.num_of_levels as second_num_of_levels'
        ]);

        return \Datatables::eloquent($categories)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('categories', 'edit') || \Permissions::check('categories', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('categories', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('categories.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('categories', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Categories.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }
                                if (\Permissions::check('products', 'open')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . url('admin/products') . '?category_id=' . $item->id . '" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.products');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->editColumn('title', function ($item) {

                             if ($item->first_num_of_levels) {
                                $num_of_levels = $item->first_num_of_levels;
                             }else{
                                $num_of_levels = $item->second_num_of_levels;
                             }
                            if ($item->level == $num_of_levels) {
                                $back = $item->title;
                            } else {
                                $back = '<a href="' . route('categories.index') . '?parent=' . $item->id . '">' . $item->title . '</a>';
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

    private function node_path($id, $action = false) {
        $category = Category::where('id', $id)->first();
        $categories = null;
        if ($category) {
            $parents_ids = explode(',', $category->parents_ids);
            $parents_ids[] = $id;
            $categories = Category::leftJoin('categories_translations as trans', 'categories.id', '=', 'trans.category_id')
                    ->whereIn('categories.id', $parents_ids)
                    ->where('trans.locale', $this->lang_code)
                    ->orderBy('categories.id', 'ASC')
                    ->select('categories.id', 'trans.title')
                    ->get();
            $categories = $this->format_path($categories, $action);
        }
        return $categories;
    }

    private function format_path($categories, $action) {
        $html = '';
        if ($categories && $categories->count() > 0) {
            foreach ($categories as $key => $category) {
                if ($key < ($categories->count() - 1)) {
                    $html .= '<li><a href="' . url('admin/categories?parent=' . $category->id) . '">' . $category->title . '</a><i class="fa fa-circle"></i></li>';
                } else {
                    if ($action) {
                        $html .= '<li><a href="' . url('admin/categories?parent=' . $category->id) . '">' . $category->title . '</a><i class="fa fa-circle"></i></li>';
                    } else {
                        $html .= '<li><span>' . $category->title . '</span></li>';
                    }
                }
            }
        }
        return $html;
    }

}
