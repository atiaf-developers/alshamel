<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Ad extends MyModel
{
    protected $table = "ads";
    private static $level;
    public static function get_all($req){
        $user=static::auth_user();
        $Ads=Ad::leftJoin('favourites',function ($join) use($user) {
            $join->on('favourites.ad_id','ads.id')
                 ->where('favourites.user_id', $user->id);
        });
        
        $Ads=$Ads->where('active',0);
        if($req->cat_id){
            $category=Category::find($req->cat_id);
            if($category->no_of_levels==2){
                $Ads=$Ads->where('category_two_id',$req->cat_id);
            }else{
                $Ads=$Ads->where('category_three_id',$req->cat_id);
            }
        }else{
            $Ads=$Ads->where('ads.special',1);
        }

         $Ads=$Ads->where('ads.city_id',$req->city_id);
        if($req->is_filter==1){
            $data_filter=$req->filter;
            $joins='';
            $f=1;
            foreach((Array)json_decode($data_filter) as $key=>$value){
                $joins .=' INNER JOIN features f'.$f.' on f'.$f.'.ad_id=ad.id AND f'.$f.'.name="'.$key.'" AND f'.$f.'.value="'.$value.'"';
                $f++;
            }
            $Ads=$Ads->whereIn('ads.id',[DB::raw('select ad.id from ads ad '.$joins.' GROUP BY ad.id')]);
        }
         $Ads=$Ads->select(['ads.*','favourites.id as is_favourite'])->get();
         return Ad::transformCollection($Ads);
    }
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );
    public static $form_types=[
        1=>'real estates',
        2=>'land',
        3=>'cars',
        4=>'defualt'
    ];
    public static $fields_type_one=[
        'price',
        'area',
        'aqar_type',
        'room_count',
        'bath_count',
        'is_furnished',
        'car_waiting'
    ];
    public static $fields_type_two=[
        'price',
        'area',
        'aqar_type'
    ];
    public static $fields_type_three=[
        'price',
        'car_status',
        'car_model',
        'manufacturing_year',
        'motion_vector',
        'power',
        'drive_system',
        'counter',
        'unit',
        'fuel_type',
    ];
    public static $fields_type_four=[
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
    public static function transform(Ad $item,$filters=array()){
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;
        $transformer->rate = $item->rate;
        $prefixed_array = preg_filter('/^/', url('public/uploads/ads') . '/', json_decode($item->images));
        $transformer->images = $prefixed_array;
        Ad::$level=1;
        $catagory_level=$item->Categories->no_of_levels;
        if($catagory_level==2){
            Ad::$level=2;
            $form_type=$item->Categories->form_type;
        }else{
            Ad::$level=3;
            $form_type=$item->Categories->form_type;
        }
        if($form_type!=4){
            $featuers=$item->Features;
            foreach($featuers as $value){
                if($form_type==1)
                $array=Ad::$fields_type_one;
                elseif($form_type==2)
                $array=Ad::$fields_type_two;
                else
                $array=Ad::$fields_type_three;
                $title=$value->name;
                if(in_array($title,$array)){
                    $value=$value->value;
                    $transformer->$title=$value;
                }
            }
            $transformer->currancy=$item->Location->currancy->translations->title;
        }

        $transformer->is_favourite = $item->is_favourite ? 1 : 0;
        $transformer->is_special = $item->special==0 ? 0 : 1;
        if(!empty($filters)){
            
        }
        return $transformer;
    }

    public function Features() {
        return $this->hasMany(Feature::class, 'ad_id');
    }
    public function Categories() {
        if(Ad::$level==1)
        return $this->hasOne(Category::class,'id', 'category_one_id');
        elseif(Ad::$level==2)
        return $this->hasOne(Category::class,'id', 'category_two_id');
        else
        return $this->hasOne(Category::class,'id', 'category_three_id');
    }
    public function Location(){
        return $this->hasOne(Location::class,'id', 'city_id');
    }
    
}
