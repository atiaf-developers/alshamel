<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\ModelTrait;
use DB;



class User extends Authenticatable {

    use Notifiable;
    use ModelTrait;

    protected $casts = array(
        'id' => 'integer',
        'mobile' => 'string',
    );
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );


    public function store()
    {
        return $this->hasOne(Store::class,'user_id');
    }

    public static function transform($item)
    {
        $transformer = new \stdClass();
        $transformer->name = $item->name;
        $transformer->username = $item->username;
        $transformer->email = $item->email;
        $transformer->mobile = $item->mobile;
        $transformer->image = url('public/uploads/users').'/'.$item->image;
        return $transformer;
    }
    
    protected static function boot() {
        parent::boot();

        static::deleted(function($user) {
            if ($user->user_image != 'default.png') {
                User::deleteUploaded('users',$user->image);
            }
        });
    }
   

}
