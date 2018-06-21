<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdReport extends MyModel
{
    protected $table = "ad_reports";
    public static $types=[
        1=>"this_ad_is_not_true",
        2=>"this_ad_displays_its_goods_prohibited_from_selling",
        3=>"this_ad_is_valid",
    ];
    public static function transform($item){
        $transformer = new \stdClass();
        $transformer->id=$item->id;
       
        $transformer->report=_lang('app.'.self::$types[$item->type]);
        
        $transformer->user_id=$item->user->id;
       
        $transformer->user_name=$item->user->username;
        
        $transformer->ad_id=$item->ad->id;
       
        $transformer->ad_title=$item->ad->title;
        
        $transformer->creared_at=date('Y-m-d',strtotime($item->created_at));
        // dd($transformer);
        return $transformer;
    }

    
    public function user() {
        return $this->hasOne(User::class,'id' ,'user_id');
    }
    public function ad() {
        return $this->hasOne(Ad::class,'id' ,'ad_id');
    }
}


