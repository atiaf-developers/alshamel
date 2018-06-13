<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelType extends MyModel {

    protected $table = "fuel_types";

    public static function getAll() {
        return static::join('fuel_types_translations as trans', 'fuel_types.id', '=', 'trans.fuel_type_id')
                        ->orderBy('fuel_types.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->where('fuel_types.active', true)
                        ->select('fuel_types.id','trans.title')
                        ->get();
    }

    public function translations() {
        return $this->hasMany(FuelTypeTranslation::class, 'fuel_type_id');
    }

    public static function transform($item) {
        return $item;
    }


    protected static function boot() {
        parent::boot();

        static::deleting(function($fuel_type) {
            foreach ($fuel_type->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($fuel_type) {
           
        });
    }

}
