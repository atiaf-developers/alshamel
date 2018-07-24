<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingUser extends MyModel {

    protected $table = 'rating_users';
    
    public function rating()
    {
    	return $this->belongsTo(Rating::class,'rating_id','id');
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($rate) {
          
        });

        static::deleted(function($rate) {
            
            $ratings = $rate->rating;
        	$rating_total_rates = $ratings->total_rates;
            $ratings->total_rates = $rating_total_rates - 1;
            $ratings->save();
            
        });
    }

    

}
