<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyType extends MyModel {

    protected $table = "property_types";

    public static function getAll() {
        return static::join('property_types_translations as trans', 'property_types.id', '=', 'trans.property_type_id')
                        ->orderBy('property_types.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->where('property_types.active', true)
                        ->select('property_types.id','property_types.parent_id','trans.title','property_types.image')
                        ->get();
    }

    public function translations() {
        return $this->hasMany(PropertyTypeTranslation::class, 'property_type_id');
    }

    public static function transform($item) {
  
        return $item;
    }


    protected static function boot() {
        parent::boot();

        static::deleting(function($property_type) {
            foreach ($property_type->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($property_type) {
            PropertyType::deleteUploaded('property_types', $property_type->image);
        });
    }

}
