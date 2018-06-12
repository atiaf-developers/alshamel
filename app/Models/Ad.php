<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Ad extends MyModel {

    protected $table = "ads";
    private static $level;
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );
    public static $filter = [1, 2, 3, 4];
    public static $form_types = [
        1 => 'real estates',
        2 => 'lands',
        3 => 'cars',
        4 => 'defualt'
    ];
    public static $real_states_features = [
        'price',
        'area',
        'property_type',
        'rooms_count',
        'baths_count',
        'is_furnished',
        'has_parking'
    ];
    public static $lands_features = [
        'price',
        'area',
    ];
    public static $cars_features = [
        'price',
        'car_status',
        'car_model',
        'manufacturing_year',
        'motion_vector',
        'engine_capacity',
        'propulsion_system',
        'counter',
        'unit',
        'fuel_type',
    ];
    public static $defualt_features = [
        'form_type',
        'category_one_id',
        'category_two_id',
        'country_id',
        'city_id',
        'title',
        'details',
        'lat',
        'lng',
        'email',
        'mobile',
    ];

    public static function get_all($req) {
        $user = static::auth_user();
        $Ads = Ad::leftJoin('favourites', function ($join) use($user) {
                    $join->on('favourites.ad_id', 'ads.id')
                            ->where('favourites.user_id', $user->id);
                });

        $Ads = $Ads->where('active', 1);
        if ($req->cat_id) {
            $category = Category::find($req->cat_id);
            if ($category->no_of_levels == 2) {
                $Ads = $Ads->where('category_two_id', $req->cat_id);
            } else {
                $Ads = $Ads->where('category_three_id', $req->cat_id);
            }
        }
        $Ads = $Ads->where('ads.city_id', $req->city_id);
        if ($req->is_filter == 1) {
            $data_filter = $req->filter;
            $joins = '';
            $f = 1;
            $ad2 = new Ad;
            foreach ((Array) json_decode($data_filter) as $key => $value) {
                $ad2 = $ad2->join('features as f' . $f . '', function ($join) use($f, $key, $value) {
                    if (is_array($value)) {
                        $join->on('f' . $f . '.ad_id', 'ads.id')
                                ->where('f' . $f . '.name', $key)
                                ->whereBetween('f' . $f . '.value', [$value[0], $value[1]]);
                    } else {
                        $join->on('f' . $f . '.ad_id', 'ads.id')
                                ->where('f' . $f . '.name', $key)
                                ->where('f' . $f . '.value', $value);
                    }
                });
                $f++;
            }
            $ad2 = $ad2->select(['ads.id'])->groupBy('ads.id')->get()->toArray();
            if ($ad2)
                $Ads = $Ads->whereIn('ads.id', [$ad2]);
            else
                $Ads = $Ads->whereIn('ads.id', array());

            if ($req->filter_show) {
                $filter_show_array = json_decode($req->filter_show);
                if (is_array($filter_show_array)) {
                    if (in_array(1, $filter_show_array)) {
                        $day = date('Y-m-d');
                        $Ads = $Ads->whereDate('ads.created_at', $day);
                    } elseif (in_array(2, $filter_show_array)) {
                        $Ads = $Ads->whereNull('ads.images');
                    } elseif (in_array(3, $filter_show_array)) {
                        $Ads = $Ads->whereNotNull('ads.images');
                    } elseif (in_array(4, $filter_show_array)) {
                        $Ads = $Ads->orderBy('distance', 'asc');
                    }
                }
            }
        }
        $Ads = $Ads->select(['ads.*', 'favourites.id as is_favourite', DB::raw(static::iniDiffLocations('ads', $req->lat, $req->lng))])->get();
        // dd($Ads);
        return Ad::transformCollection($Ads);
    }

    public static function validation_rules($type) {
        $features = array();
        $rules = array();
        switch ($type) {
            case 1:
                $validation_rules = static::$real_states_features;
                break;
            case 2:
                $validation_rules = static::$lands_features;
                break;
            case 3:
                $validation_rules = static::$cars_features;
                break;
            default:
                $validation_rules = array();
                break;
        }
        $features = array_merge($validation_rules, static::$defualt_features);
        foreach ($features as $value) {
            $rules[$value] = 'required';
        }
        return $rules;
    }

    public static function transform(Ad $item, $filters = array()) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;
        $transformer->rate = $item->rate;
        $prefixed_array = preg_filter('/^/', url('public/uploads/ads') . '/', json_decode($item->images));
        $transformer->images = $prefixed_array;
        Ad::$level = 1;
        $catagory_level = $item->Categories->no_of_levels;
        if ($catagory_level == 2) {
            Ad::$level = 2;
            $form_type = $item->Categories->form_type;
        } else {
            Ad::$level = 3;
            $form_type = $item->Categories->form_type;
        }
        if ($form_type != 4) {
            $featuers = $item->Features;
            foreach ($featuers as $value) {
                if ($form_type == 1)
                    $array = Ad::$real_states_features;
                elseif ($form_type == 2)
                    $array = Ad::$lands_features;
                else
                    $array = Ad::$cars_features;
                $title = $value->name;
                if (in_array($title, $array)) {
                    $value = $value->value;
                    $transformer->$title = $value;
                }
            }
            $transformer->currancy = $item->Location->currancy->translations->title;
        }

        $transformer->is_favourite = $item->is_favourite ? 1 : 0;
        $transformer->is_special = $item->special == 0 ? 0 : 1;
        return $transformer;
    }

    public function Features() {
        return $this->hasMany(Feature::class, 'ad_id');
    }

    public function Categories() {
        if (Ad::$level == 1)
            return $this->hasOne(Category::class, 'id', 'category_one_id');
        elseif (Ad::$level == 2)
            return $this->hasOne(Category::class, 'id', 'category_two_id');
        else
            return $this->hasOne(Category::class, 'id', 'category_three_id');
    }

    public function Location() {
        return $this->hasOne(Location::class, 'id', 'city_id');
    }

}
