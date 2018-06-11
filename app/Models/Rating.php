<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends MyModel {

    protected $table = 'rating';

    public static function transform($item)
    {
    	$transformer = new \stdClass();
    	$transformer->name = $item->name;
    	$transformer->image = url('public/uploads/users').'/'.$item->image;
    	$transformer->rate = $item->score;
    	$transformer->comment = $item->comment;
        $transformer->date = date('Y F d',strtotime($item->created_at));

        return $transformer;
    	
    }
  


}
