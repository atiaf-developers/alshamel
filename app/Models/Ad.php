<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends MyModel
{
    protected $table = "ads";
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );
    public $fields_type_one=[
        'price',
        'area',
        'aqar_type',
        'room_count',
        'bath_count',
        'is_furnished',
        'car_waiting'
    ];
    public $fields_type_two=[
        'price',
        'area',
        'aqar_type'
    ];
    public $fields_type_three=[
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
    public $fields_type_four=[
        'category_one_id',
        'category_two_id',
        'country_id',
        'city_id',
        'title',
        'details',
        'lat',
        'lng',
        'email',
        'phone',
    ];
}
