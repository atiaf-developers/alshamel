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

    public static function get_All() {
        
    }

    public static function transformPaginationApi($item,$extra_params = array())
    {
        $lang = static::getLangCode();

        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;
        $transformer->rate = $item->rate;
        $transformer->lat = $item->lat;
        $transformer->lng = $item->lng;
        $transformer->special = $item->special;
        $transformer->created_at = date('Y-m-d H:i',strtotime($item->created_at));
        $transformer->price = $item->price;
        $transformer->form_type = $item->form_type;
        $transformer->distance = round($item->distance,1);
        $ad_images =  json_decode($item->images);
        foreach ($ad_images as $key => $value) {
            $ad_images[$key] =  static::rmv_prefix($value);
        }
        $prefixed_array = preg_filter('/^/', url('public/uploads/ads') . '/m_', $ad_images);
        $transformer->images = $prefixed_array;

        if ((isset($extra_params['user']) && $extra_params['user'] != null)) {
            $transformer->is_favourite = $item->is_favourite ? 1 : 0 ;
        }else{
            $transformer->is_favourite = 0;
        }
        return $transformer;
    }


    public static function transformDetailsApi($item,$extra_params = array())
    {

        $lang = static::getLangCode();

        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->city = $item->city;
        $transformer->title = $item->title;
        $transformer->details = $item->details;
        $transformer->rate = $item->rate;
        $transformer->lat = $item->lat;
        $transformer->lng = $item->lng;
        $transformer->address = getAddress($item->lat,$item->lng,$lang);
        $transformer->special = $item->special;
        $transformer->created_at = date('Y-m-d H:i',strtotime($item->created_at));
        $transformer->price = $item->price;
        $transformer->form_type = $item->form_type;
        $transformer->distance = round($item->distance,1);
        $ad_images =  json_decode($item->images);
        foreach ($ad_images as $key => $value) {
            $ad_images[$key] =  static::rmv_prefix($value);
        }
        $prefixed_array = preg_filter('/^/', url('public/uploads/ads') . '/m_', $ad_images);
        $transformer->images = $prefixed_array;

        if ((isset($extra_params['user']) && $extra_params['user'] != null)) {
            $transformer->is_favourite = $item->is_favourite ? 1 : 0 ;
        }else{
            $transformer->is_favourite = 0;
        }
        if ($item->form_type == 1) {
            $transformer->area = $item->area;
            $transformer->rooms_number = $item->rooms_number;
            $transformer->baths_number = $item->baths_number;
            $transformer->is_furnished = $item->is_furnished;
            $transformer->has_parking = $item->has_parking;
            $transformer->property_type_id = $item->property_type_id;
            $transformer->property_type = $item->property_type;

        }else if ($item->form_type == 2){
            $transformer->area = $item->area;
        }
        else if ($item->form_type == 3){

            $transformer->motion_vector_id = $item->motion_vector_id;
            $transformer->motion_vector = $item->motion_vector;
            $transformer->engine_capacity_id = $item->engine_capacity_id;
            $transformer->engine_capacity = $item->engine_capacity;
            $transformer->propulsion_system_id = $item->propulsion_system_id;
            $transformer->propulsion_system = $item->propulsion_system;
            $transformer->fuel_type_id = $item->fuel_type_id;
            $transformer->fuel_type = $item->fuel_type;
            $transformer->mileage_id = $item->mileage_id;
            $transformer->mileage = $item->mileage;
            $transformer->mileage_unit = $item->mileage_unit;
            $transformer->status = $item->status == 0 ? _lang('app.new') : _lang('app.used');
            $transformer->manufacturing_year = $item->manufacturing_year;
        }
        
        $transformer->name = $item->name;
        $transformer->mobile = $item->mobile;
        $transformer->email = $item->email;
        
        
        return $transformer;
    }





    
    public static function transformAdmin(Ad $item){
        dd('asdasd');
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
        $parents=$item->Categories->parents_ids;
        if (strpos($parents, ',')) {
            $parents_array=explode(",",$parents);
            for($i=0;$i<=count($parents_array);$i++){
                $catagory_array[]=self::catagory_by_id($parents_array[$i]);
            }
        }else{
            $catagory_array[]=self::catagory_by_id($parents);
        }
       $form_type = $item->Categories->form_type;
       if($form_type==1){
        $transformer->Feature=$item->realStateAd;
       }elseif($form_type==2){
        $transformer->Feature=$item->landAd;
       }elseif($form_type==3){
        $transformer->Feature=$item->vehicleAd;
       }else{
        $transformer->Feature=[]; 
       }
       dd($transformer);
       return $transformer;
    }
    public function rates() {
        return $this->hasMany(Rating::class, 'entity_id');
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


    protected static function boot() {
        parent::boot();

        static::deleting(function($ad) {
            foreach ($ad->rates as $rate) {
                $rate->delete();
            }
            if ($ad->realStateAd) {
                $ad->realStateAd->delete();
            }else if($ad->landAd){
                $ad->landAd->delete();
            }else if($ad->vehicleAd){
                $ad->vehicleAd->delete();
            }
        });

        static::deleted(function($ad) {
            foreach (json_decode($ad->images) as $image) {
                Ad::deleteUploaded('ads', $image);
            }
        });
    }

    protected static function catagory_by_id($id){
        return Category::join('catagory_lang','catagory_lang.cat_id','catagory.id')
        ->where('catagory.id',$id)
        ->where('catagory_lang.lang',static::getLangCode())
        ->select(['catagory_lang.name','catagory.id'])
        ->find();
    }

}
