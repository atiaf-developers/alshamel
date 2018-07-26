<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends MyModel {

    protected $table = "categories";
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 700, 'height' => 700),
    );

    protected function childrens() {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public static function getAll($parent_id = 0) {
        $data = static::join('categories_translations as trans', 'categories.id', '=', 'trans.category_id')
                ->orderBy('categories.this_order', 'ASC')
                ->where('trans.locale', static::getLangCode())
                ->where('categories.active', true);
        $data->where('categories.parent_id', $parent_id);
        $data->select('categories.id', 'categories.parent_id', 'trans.title', 'categories.image', 'categories.form_type', 'categories.parents_ids');
        $data = $data->get();

        return $data;
    }
    public static function getAllFront($where_array=array()) {
        $categories = static::Join('categories_translations','categories.id','=','categories_translations.category_id')
        ->where('categories_translations.locale',static::getLangCode())
        ->where('categories.active',true)
        ->where('categories.parent_id',0)
        ->orderBy('categories.this_order')
        ->select('categories.slug','categories.image','categories_translations.title')
        ->get(); 

        return static::transformCollection($categories, 'Front');
    }

    public function translations() {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }

    public static function transformAdmin($item) {
        $transformer = new \stdClass();
        $transformer->slug = $item->slug;
        $transformer->title = $item->title;
        $transformer->image = "";
        if ($item->image) {
            $category_image = static::rmv_prefix($item->image);   
            $transformer->image =  url('public/uploads/categories') . '/m_'.$category_image;
        }    
        $transformer->has_sub = $item->childrens->count() > 0 ? true : false;
        return $transformer;
    }
    public static function transformFront($item) {
        $transformer = new \stdClass();
        $transformer->slug = $item->slug;
        $transformer->title = $item->title;
        $transformer->image = "";
        if ($item->image) {
            $category_image = static::rmv_prefix($item->image);   
            $transformer->image =  url('public/uploads/categories') . '/m_'.$category_image;
        }    
        $transformer->has_sub = $item->childrens->count() > 0 ? true : false;
        return $transformer;
    }

    public static function transform($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;
        $transformer->form_type = $item->form_type;
        if ($item->image) {
            $transformer->image = url('public/uploads/categories') . '/' . $item->image;
        }


        $transformer->has_sub = $item->childrens->count() > 0 ? true : false;
        return $transformer;
    }

    public static function transformFrontHome($item) {

        $item->image = url('public/uploads/categories/m_' . static::rmv_prefix($item->image));
        $item->url = _url('category/' . $item->slug);

        return $item;
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($category) {
            foreach ($category->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($category) {
            Category::deleteUploaded('categories', $category->image);
        });
    }

}
