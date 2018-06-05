<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Traits\ModelTrait;

use Request;
use Image;

class MyModel extends Model {

    use ModelTrait;
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

    protected static function auth_user() {
        $token = Request::header('authorization');
        $token = Authorization::validateToken($token);
        $user = null;
        if ($token) {
            $user = User::find($token->id);
        }

        return $user;
    }
    protected static function iniDiffLocations($tableName, $lat, $lng)
    {
        $diffLocations = "SQRT(POW(69.1 * ($tableName.lat - {$lat}), 2) + POW(69.1 * ({$lng} - $tableName.lng) * COS($tableName.lat / 57.3), 2)) as distance";
        return $diffLocations;
    }

 
   

    

}
