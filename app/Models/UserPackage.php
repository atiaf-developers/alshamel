<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPackage extends Model
{
    protected $table = "users_packages";
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );

    private $status=[
        0=>'waiting',
        1=>'accept',
        2=>'reject'
    ];
    public function user() {
        return $this->hasOne(User::class, 'user_id');
    }
    public function package() {
        return $this->hasOne(Package::class, 'package_id');
    }
}
