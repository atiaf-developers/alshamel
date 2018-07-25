<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

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
    private static $columns = ['ads.id', 'ads.lat', 'ads.lng','ads.category_id', 'ads.title', 'ads.rate', 'ads.special', 'ads.created_at', 'ads.price', 'ads.mobile', 'ads.email','categories.form_type','categories.parent_id as category_parent','categories_translations.title as category', 'users.name','locations.id as city_id' , 'locations.parent_id as country_id','locations_translations.title as city', 'ads.details', 'ads.images','ads.user_id','ads.active','currency_translations.title as currency'];

    public static function get_All() {

    }

    public static function getAdsApi($request, $user, $id = null, $type = null) {
        $lang_code = static::getLangCode();

        $columns = ['ads.id', 'ads.lat', 'ads.lng', 'ads.title', 'ads.rate', 'ads.special', 'ads.created_at', 'ads.price', 'ads.mobile', 'ads.email','categories.form_type','categories_translations.title as category' ,'users.name', 'locations_translations.title as city', 'ads.details', 'ads.images','ads.user_id'];

        $ads = Ad::join('categories', 'ads.category_id', '=', 'categories.id')
        ->join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
        ->join('locations', 'ads.city_id', '=', 'locations.id')
        ->join('locations_translations', 'locations.id', '=', 'locations_translations.location_id')
        ->join('users', 'users.id', '=', 'ads.user_id')
        ->join('locations as l2', 'ads.country_id', '=', 'l2.id')
        ->join('currency', 'l2.currency_id', '=', 'currency.id')
        ->join('currency_translations', function($join) {
                            $join->on('currency.id', '=', 'currency_translations.currency_id')
                            ->where('currency_translations.locale', static::getLangCode());
        });
        if ($request->status != "all") {
           $ads->where('ads.active', true);
        }
        $ads->where('locations_translations.locale', $lang_code);
        $ads->where('categories_translations.locale', $lang_code);
        if ($id) {
            $ads->where('ads.id', $id);
        }

        if ($request->input('search')) {
            $ads->whereRaw(handleKeywordWhere(['ads.title','ads.details'], $request->input('search')));
        }

        if ($user) {

            if ($request->input('options')) {

                if ($request->input('options') == 1) {
                    $ads->where('ads.user_id', $user->id);
                } else if ($request->input('options') == 2) {
                    $ads->join('favourites', function($join) use($user) {
                        $join->on('favourites.ad_id', '=', 'ads.id')
                        ->where('favourites.user_id', $user->id);
                    });
                    static::$columns[] = "favourites.id as is_favourite";
                }
            } else {
                $ads->leftJoin('favourites', function($join) use($user) {
                    $join->on('favourites.ad_id', '=', 'ads.id')
                    ->where('favourites.user_id', $user->id);
                });
                //$ads->where('ads.user_id','!=' ,$user->id);
                static::$columns[] = "favourites.id as is_favourite";
            }
        }

        if ($request->input('country_id')) {
             $ads->where('ads.country_id', $request->input('country_id'));
        }
        if ($request->input('city_id')) {
            $ads->where('ads.city_id', $request->input('city_id'));
        }

        if ($request->input('lat') && $request->input('lng')) {
            $lat = $request->input('lat');
            $lng = $request->input('lng');
            static::$columns[] = DB::raw(static::iniDiffLocations("ads", $lat, $lng));
        }

        $options = new \stdClass();

        if ($request->input('filter')) {
            $options = json_decode($request->input('filter'));
        }

        if ($request->input('category_id') && !isset($options->category)) {
            $ads->where('ads.category_id', $request->input('category_id'));
        } else if (isset($options->category) && !empty($options->category)) {
            $ads->whereIn('ads.category_id', $options->category);
        }

        //show [1 => added today ,2  => only address ,3 => contain images ,4 => near to me]
        if (isset($options->show) && !empty($options->show)) {
            if (in_array(1, $options->show)) {
                $ads->where('ads.created_at', '>=', Carbon::today());
            }
            if (in_array(2, $options->show)) {
                $ads->whereNull('ads.images');
            }
            if (in_array(3, $options->show)) {
                $ads->whereNotNull('ads.images');
            }
            if (in_array(4, $options->show)) {
                $ads->orderBy('distance');
            }
        }

        if ($type == 3) {
            $ads->where('ads.special', 1);
        }
        $ads = static::handleFromTypeWhere($options,$ads, $request);

        //here
        $ads->select(static::$columns);

        if ($id) {

            $ads = $ads->first();
            if (!$ads) {
                return false;
            }
            if ($request->input('action') == 'edit') {
                return Ad::transformEditApi($ads, ['user' => $user]);
            }else{
                return Ad::transformDetailsApi($ads, ['user' => $user]);
            }
            
        } else {

            if ($type == 1) {
                $ads->where('ads.special', 1);
                $ads = $ads->paginate(2);
            } else if ($type == 2) {
                $ads->where('ads.special', 0);
                $ads = $ads->paginate(10);
            } else {
                $ads = $ads->paginate(10);
            }


            return Ad::transformCollection($ads, 'PaginationApi', ['user' => $user]);
        }
        //return  $ads = $ads->get();
    }

    private static function handleFromTypeWhere($options,$ads, $request) {
        $lang_code = static::getLangCode();
        // real states
        if ($request->input('form_type') == 1) {
            
            $ads->join('real_states_ads', 'real_states_ads.ad_id', '=', 'ads.id');
            $ads->join('basic_data', 'real_states_ads.property_type_id', '=', 'basic_data.id');
            $ads->join('basic_data_translations as trans', 'basic_data.id', '=', 'trans.basic_data_id');
            $ads->where('trans.locale', $lang_code);

            
            static::$columns = array_merge(static::$columns, [
                "trans.title as property_type", "real_states_ads.has_parking", "real_states_ads.area", "real_states_ads.property_type_id", "real_states_ads.rooms_number",
                "real_states_ads.baths_number", "real_states_ads.is_furnished"]);

            if (isset($options->property_type) && !empty($options->property_type)) {
                $ads->whereIn('real_states_ads.property_type_id', $options->property_type);
            }
            if (isset($options->rooms_number) && !empty($options->rooms_number)) {

                $ads->whereBetween('real_states_ads.rooms_number', [$options->rooms_number[0], $options->rooms_number[1]]);
            }
            if (isset($options->baths_number) && !empty($options->baths_number)) {
                $ads->whereBetween('real_states_ads.baths_number', [$options->baths_number[0], $options->baths_number[1]]);
            }
            if (isset($options->is_furnished) && $options->is_furnished != -1) {
                $ads->where('real_states_ads.is_furnished', $options->is_furnished);
            }
            if (isset($options->has_parking)  && $options->has_parking != -1) {
                $ads->where('real_states_ads.has_parking', $options->has_parking);
            }
            if (isset($options->area) && !empty($options->area)) {
                $ads->whereBetween('real_states_ads.area', [$options->area[0], $options->area[1]]);
            }
        }// lands
        else if ($request->input('form_type') == 2) {
            $ads->join('lands_ads', 'lands_ads.ad_id', '=', 'ads.id');
            if (isset($options->area) && !empty($options->area)) {
                $ads->whereBetween('lands_ads.area', [$options->area[0], $options->area[1]]);
            }
            static::$columns = array_merge(static::$columns, ["lands_ads.area"]);
        }// cars
        else if ($request->input('form_type') == 3) {

            $ads->join('vehicles_ads', 'vehicles_ads.ad_id', '=', 'ads.id');

            $ads->join('basic_data as b1', 'vehicles_ads.motion_vector_id', '=', 'b1.id');
            $ads->join('basic_data as b2', 'vehicles_ads.engine_capacity_id', '=', 'b2.id');
            $ads->join('basic_data as b3', 'vehicles_ads.propulsion_system_id', '=', 'b3.id');
            $ads->join('basic_data as b4', 'vehicles_ads.fuel_type_id', '=', 'b4.id');
            $ads->join('basic_data as b5', 'vehicles_ads.mileage_id', '=', 'b5.id');


            $ads->join('basic_data_translations as motion_vector_trans', 'b1.id', '=', 'motion_vector_trans.basic_data_id');
            $ads->join('basic_data_translations as engine_capacity_trans', 'b2.id', '=', 'engine_capacity_trans.basic_data_id');
            $ads->join('basic_data_translations as propulsion_system_trans', 'b3.id', '=', 'propulsion_system_trans.basic_data_id');
            $ads->join('basic_data_translations as fuel_type_trans', 'b4.id', '=', 'fuel_type_trans.basic_data_id');
            $ads->join('basic_data_translations as mileage_trans', 'b5.id', '=', 'mileage_trans.basic_data_id');

            $ads->where('motion_vector_trans.locale', $lang_code);
            $ads->where('engine_capacity_trans.locale', $lang_code);
            $ads->where('propulsion_system_trans.locale', $lang_code);
            $ads->where('fuel_type_trans.locale', $lang_code);
            $ads->where('mileage_trans.locale', $lang_code);

            static::$columns = array_merge(static::$columns, ["b1.id as motion_vector_id", "motion_vector_trans.title as motion_vector", "b2.id as engine_capacity_id", "engine_capacity_trans.title as engine_capacity",
                "b3.id as propulsion_system_id", "propulsion_system_trans.title as propulsion_system", "b4.id as fuel_type_id", "fuel_type_trans.title as fuel_type",
                "b5.id as mileage_id", "mileage_trans.title as mileage", "vehicles_ads.mileage_unit", "vehicles_ads.status", "vehicles_ads.manufacturing_year"]);

            if (isset($options->status)  && $options->status != -1) {
                $ads->where('vehicles_ads.status', $options->status);
            }
            if (isset($options->manufacturing_year)  && !empty($options->manufacturing_year)) {
                $ads->whereBetween('vehicles_ads.manufacturing_year', [$options->manufacturing_year[0], $options->manufacturing_year[1]]);
            }
            if (isset($options->motion_vector) && !empty($options->motion_vector)) {
                $ads->whereIn('vehicles_ads.motion_vector_id', $options->motion_vector);
            }
            if (isset($options->engine_capacity) && !empty($options->engine_capacity)) {
                $ads->whereIn('vehicles_ads.engine_capacity_id', $options->engine_capacity);
            }
            if (isset($options->propulsion_system) && !empty($options->propulsion_system)) {
                $ads->whereIn('vehicles_ads.propulsion_system_id', $options->propulsion_system);
            }
            if (isset($options->fuel_type) && !empty($options->fuel_type)) {
                $ads->whereIn('vehicles_ads.fuel_type_id', $options->fuel_type);
            }
            if (isset($options->mileage_unit) && $options->mileage_unit != -1) {
                $ads->where('vehicles_ads.mileage_unit', $options->mileage_unit);
            }
            if (isset($options->mileage)  && !empty($options->mileage)) {
                $ads->whereIn('vehicles_ads.mileage_id', $options->mileage);
            }
        }
        if (isset($options->price) && !empty($options->price)) {
            $ads->whereBetween('ads.price', [$options->price[0], $options->price[1]]);
        }


        return $ads;
    }

    public static function transformPaginationApi($item, $extra_params = array()) {
        $lang = static::getLangCode();

        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;
        $transformer->rate = $item->rate;
        $transformer->lat = $item->lat;
        $transformer->lng = $item->lng;
        $transformer->special = $item->special == 1 ? true : false;
        $transformer->created_at = date('Y-m-d H:i', strtotime($item->created_at));
        $transformer->price = $item->price;
        $transformer->form_type = $item->form_type;
        $transformer->distance = round($item->distance, 1);
        $ad_images = json_decode($item->images);
        $transformer->images = array();
        if (count($ad_images) > 0) {
            foreach ($ad_images as $key => $value) {
                $ad_images[$key] = static::rmv_prefix($value);
            }
            $prefixed_array = preg_filter('/^/', url('public/uploads/ads') . '/m_', $ad_images);
            $transformer->images = $prefixed_array;
        }


        if ((isset($extra_params['user']) && $extra_params['user'] != null)) {
            $transformer->is_favourite = $item->is_favourite ? 1 : 0;
        } else {
            $transformer->is_favourite = 0;
        }
        return $transformer;
    }

    public static function transformEditApi($item,$extra_params = array())
    {
       
        $lang = static::getLangCode();

        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->city_id = (int)$item->city_id;
        $transformer->country_id = (int)$item->country_id;
        $transformer->title = $item->title;
        $transformer->details = $item->details;
        $transformer->lat = $item->lat;
        $transformer->lng = $item->lng;
        $transformer->address = getAddress($item->lat,$item->lng,$lang);
        $transformer->special = $item->special;
        $transformer->created_at = date('Y-m-d H:i',strtotime($item->created_at));
        $transformer->price = $item->price;
        $transformer->form_type = $item->form_type;
       $prefixed_array = array();
        $ad_images = json_decode($item->images);
        if (count($ad_images) > 0) {
            foreach ($ad_images as $key => $value) {
                $ad_images[$key] = static::rmv_prefix($value);
            }
            $prefixed_array = preg_filter('/^/', url('public/uploads/ads') . '/m_', $ad_images);
            $transformer->images = $prefixed_array;
        }else{
             $transformer->images = $prefixed_array;
        }
        if ($item->form_type == 1) {
            $transformer->area = $item->area;
            $transformer->rooms_number = (int)$item->rooms_number;
            $transformer->baths_number = (int)$item->baths_number;
            $transformer->is_furnished = (int)$item->is_furnished;
            $transformer->has_parking = (int)$item->has_parking;
            $transformer->property_type = (int)$item->property_type_id;

        }else if ($item->form_type == 2){
            $transformer->area = $item->area;
        }
        else if ($item->form_type == 3){
            $transformer->motion_vector = (int)$item->motion_vector_id;
            $transformer->engine_capacity = (int)$item->engine_capacity_id;
            $transformer->propulsion_system = (int)$item->propulsion_system_id;
            $transformer->fuel_type = (int)$item->fuel_type_id;
            $transformer->mileage = (int)$item->mileage_id;
            $transformer->mileage_unit = (int)$item->mileage_unit;
            $transformer->status = (int)$item->status;
            $transformer->manufacturing_year = (int)$item->manufacturing_year;
        }
        
        $transformer->name = $item->name;
        $transformer->mobile = $item->mobile;
        $transformer->email = $item->email;

        $category = Category::where('id',$item->category_parent)->first();
        if ($category->parent_id == 0) {
            $transformer->category_id = $item->category_id;
        }else{
            $transformer->category_id = $category->id;
        }
        return $transformer;
    }

    public static function transformDetailsApi($item, $extra_params = array()) {

        $lang = static::getLangCode();

        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->city = $item->city;
        $transformer->city_id = (int)$item->city_id;
        $transformer->country_id = (int)$item->country_id;
        $transformer->title = $item->title;
        $transformer->details = $item->details;
        $transformer->rate = $item->rate;
        $transformer->lat = $item->lat;
        $transformer->lng = $item->lng;
        $transformer->address = getAddress($item->lat, $item->lng, $lang);
        $transformer->special = $item->special == 1 ? true : false;
        $transformer->created_at = date('Y-m-d H:i', strtotime($item->created_at));
        $transformer->price = $item->price.' '.$item->currency;
        $transformer->form_type = $item->form_type;
        $transformer->distance = round($item->distance, 1);
        $prefixed_array = array();
        $ad_images = json_decode($item->images);
        if (count($ad_images) > 0) {
            foreach ($ad_images as $key => $value) {
                $ad_images[$key] = static::rmv_prefix($value);
            }
            $prefixed_array = preg_filter('/^/', url('public/uploads/ads') . '/m_', $ad_images);
            $transformer->images = $prefixed_array;
        }else{
             $transformer->images = $prefixed_array;
        }
        

        if ((isset($extra_params['user']) && $extra_params['user'] != null)) {
            $transformer->is_favourite = $item->is_favourite ? 1 : 0;
            $transformer->is_my_ad = $item->user_id == $extra_params['user']->id ? true : false;
        } else {
            $transformer->is_favourite = 0;
            $transformer->is_my_ad = false;
        }
        if ($item->form_type == 1) {
            $furnished = $item->is_furnished == 1 ? _lang('app.yes') : _lang('app.no');
            $parking = $item->has_parking == 1 ? _lang('app.exist') : _lang('app.none');
            $data = array(
                [
                    'name' => _lang('app.property_type'),
                    'value' => $item->property_type
                ],
                [
                    'name' => _lang('app.rooms_number'),
                    'value' => $item->rooms_number
                ],
                [
                    'name' => _lang('app.baths_number'),
                    'value' => $item->baths_number
                ],
                [
                    'name' => _lang('app.furnished'),
                    'value' => $furnished
                ],
                [
                    'name' => _lang('app.parking_place'),
                    'value' => $parking
                ],
                [
                    'name' => _lang('app.area'),
                    'value' => $item->area
                ],
                
            );
          $transformer->features = $data;
          
        } else if ($item->form_type == 2) {
            $data = array(
                [
                    'name' => _lang('app.area'),
                    'value' => $item->area
                ]
            );
          $transformer->features = $data;
        } else if ($item->form_type == 3) {
            $mileage_unit = $item->mileage_unit == 1 ? _lang('app.km') : _lang('app.ml');
            $mileage = $item->mileage.' '.$mileage_unit;
            $status = $item->status == 0 ? _lang('app.new') : _lang('app.used');
            $data = array(
                [
                    'name' => _lang('app.car_condition'),
                    'value' => $status
                ],
                [
                    'name' => _lang('app.model'),
                    'value' => $item->category
                ],
                [
                    'name' => _lang('app.manufacturing_year'),
                    'value' => $item->manufacturing_year
                ],
                [
                    'name' => _lang('app.motion_vector'),
                    'value' => $item->motion_vector
                ],
                [
                    'name' => _lang('app.engine_capacity'),
                    'value' => $item->engine_capacity
                ],
                [
                    'name' => _lang('app.propulsion_system'),
                    'value' => $item->propulsion_system
                ],
                [
                    'name' => _lang('app.kilometers'),
                    'value' => $mileage
                ],
                [
                    'name' => _lang('app.fuel_type'),
                    'value' => $item->fuel_type
                ],
            );
          $transformer->features = $data;
        }

        $transformer->name = $item->name;
        $transformer->mobile = $item->mobile;
        $transformer->email = $item->email;


        return $transformer;
    }

    public static function transformAdmin($item) {

        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;
        $transformer->rate = $item->rate;
        $transformer->lat = $item->lat;
        $transformer->lng = $item->lng;
        $transformer->details = $item->details;
        $transformer->active = $item->active;
        $transformer->email = $item->email;
        $transformer->mobile = $item->mobile;
        $transformer->user_id = $item->user_id;
        $transformer->user_name = $item->user->username;
        $prefixed_array = preg_filter('/^/', url('public/uploads/ads') . '/', json_decode($item->images));
        $transformer->catagory_id = $item->Categories->id;
        $transformer->catagory_title = $item->Categories->translations->title;
        $parents = $item->Categories->parents_ids;
        if (strpos($parents, ',')) {
            $parents_array = explode(",", $parents);
            for ($i = 0; $i <= count($parents_array); $i) {
                $catagory_array[] = self::catagory_by_id($parents_array[$i]);
            }
        } else {
            $catagory_array[] = self::catagory_by_id($parents);
        }
        $form_type = $item->Categories->form_type;
        if ($form_type == 1) {
            $transformer->Feature = $item->realStateAd;
        } elseif ($form_type == 2) {
            $transformer->Feature = $item->landAd;
        } elseif ($form_type == 3) {
            $transformer->Feature = $item->vehicleAd;
        } else {
            $transformer->Feature = [];
        }
        dd($transformer);
        return $transformer;
    }

    public function rates() {
        return $this->hasMany(Rating::class, 'entity_id');
    }

    public function reports() {
        return $this->hasMany(AdReport::class, 'ad_id');
    }

    public function realStateAd() {
        return $this->hasOne(RealStateAd::class, 'ad_id');
    }

    public function landAd() {
        return $this->hasOne(LandAd::class, 'ad_id');
    }

    public function vehicleAd() {
        return $this->hasOne(VechileAd::class, 'ad_id');
    }

    public function user() {
        return $this->hasOne(User::class, 'user_id');
    }

    public function Categories() {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function Location() {
        return $this->hasOne(Location::class, 'id', 'city_id');
    }

    public function favourites() {
        return $this->hasMany(Favourit::class, 'ad_id');
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($ad) {
            foreach ($ad->rates as $rate) {
                $rate->delete();
            }
            foreach ($ad->reports as $report) {
                $report->delete();
            }
            foreach ($ad->favourites as $favourite) {
                $favourite->delete();
            }
            if ($ad->realStateAd) {
                $ad->realStateAd->delete();
            } else if ($ad->landAd) {
                $ad->landAd->delete();
            } else if ($ad->vehicleAd) {
                $ad->vehicleAd->delete();
            }
        });

        static::deleted(function($ad) {
            $images = json_decode($ad->images);
            if (count($images) > 0) {
                foreach ($images as $image) {
                    Ad::deleteUploaded('ads', $image);
                }
            }
            
        });
    }

    protected static function catagory_by_id($id) {
        return Category::join('catagory_lang', 'catagory_lang.cat_id', 'catagory.id')
        ->where('catagory.id', $id)
        ->where('catagory_lang.lang', static::getLangCode())
        ->select(['catagory_lang.name', 'catagory.id'])
        ->find();
    }

}
