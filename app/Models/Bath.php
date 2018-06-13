<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bath extends MyModel {

    protected $table = "bathes";

    public static function getAll() {
        return static::join('bathes_translations as trans', 'bathes.id', '=', 'trans.bath_id')
                        ->orderBy('bathes.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->where('bathes.active', true)
                        ->select('bathes.id','trans.title')
                        ->get();
    }

    public function translations() {
        return $this->hasMany(BathTranslation::class, 'bath_id');
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
