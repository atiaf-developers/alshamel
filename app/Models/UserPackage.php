<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPackage extends MyModel
{
    protected $table = "users_packages";

    public static $status=[
        0=>'waiting',
        1=>'accepted',
        2=>'rejected'
    ];
   
}
