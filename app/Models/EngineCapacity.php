<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngineCapacity extends MyModel {

    protected $table = "engine_capacities";

    public static function getAll() {
        return static::join('engine_capacities_translations as trans', 'engine_capacities.id', '=', 'trans.engine_capacity_id')
                        ->orderBy('engine_capacities.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->where('engine_capacities.active', true)
                        ->select('engine_capacities.id','trans.title')
                        ->get();
    }

    public function translations() {
        return $this->hasMany(EngineCapacityTranslation::class, 'engine_capacity_id');
    }

    public static function transform($item) {
        return $item;
    }


    protected static function boot() {
        parent::boot();

        static::deleting(function($engine_capacity) {
            foreach ($engine_capacity->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($engine_capacity) {
           
        });
    }

}
