<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotionVector extends MyModel {

    protected $table = "motion_vectors";

    public static function getAll() {
        return static::join('motion_vectors_translations as trans', 'motion_vectors.id', '=', 'trans.motion_vector_id')
                        ->orderBy('motion_vectors.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->where('motion_vectors.active', true)
                        ->select('motion_vectors.id','trans.title')
                        ->get();
    }

    public function translations() {
        return $this->hasMany(MotionVectorTranslation::class, 'motion_vector_id');
    }

    public static function transform($item) {
        return $item;
    }


    protected static function boot() {
        parent::boot();

        static::deleting(function($motion_vector) {
            foreach ($motion_vector->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($motion_vector) {
           
        });
    }

}
