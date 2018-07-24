<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdReport extends MyModel
{
    protected $table = "ad_reports";
    
    public static $types=[
        1=>"this_ad_is_not_real",
        2=>"this_ad_displays_item_banned_from_sale",
        3=>"this_ad_is_expired",
    ];
   
}


