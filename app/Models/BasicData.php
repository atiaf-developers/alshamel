<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicData extends MyModel {

    protected $table = "basic_data";
    public static $types=[
        1=>'property_types',
        2=>'rooms',
        3=>'bathes',
        4=>'engine_capacities',
        5=>'fuel_types',
        6=>'motion_vectors',
        7=>'payment_methods'
    ];
    public static function getAll() {
        return static::join('basic_data_translations as trans', 'basic_data.id', '=', 'trans.basic_data_id')
                        ->orderBy('basic_data.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->where('basic_data.active', true)
                        ->select('basic_data.id','trans.title')
                        ->get();
    }

    public function translations() {
        return $this->hasMany(BasicDataTranslation::class, 'basic_data_id');
    }

    public static function transform($item) {
        return $item;
    }


    protected static function boot() {
        parent::boot();

        static::deleting(function($bath) {
            foreach ($bath->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($bath) {
           
        });
    }

}
