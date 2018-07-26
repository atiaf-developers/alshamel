<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends MyModel {

    protected $table = "locations";
    protected $casts = [
        'id' => 'integer'
    ];
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );

    public static function getAll($parent_id = 0) {
        return static::join('locations_translations', 'locations.id', '=', 'locations_translations.location_id')
                        ->leftJoin('currency', 'locations.currency_id', '=', 'currency.id')
                        ->leftJoin('currency_translations', function($join) {
                            $join->on('currency.id', '=', 'currency_translations.currency_id')
                            ->where('currency_translations.locale', static::getLangCode());
                        })
                        ->orderBy('locations.this_order', 'ASC')
                        ->where('locations.parent_id', $parent_id)
                        ->where('locations_translations.locale', static::getLangCode())
                        ->select('locations.id', 'locations.parent_id', 'locations_translations.title', 'locations.image', 'currency_translations.sign')
                        ->get();
    }

    public static function getAllFront($where_array = array()) {
        $locations = static::join('locations_translations', 'locations.id', '=', 'locations_translations.location_id');
        $locations->leftJoin('currency', 'locations.currency_id', '=', 'currency.id');
        $locations->leftJoin('currency_translations', function($join) {
            $join->on('currency.id', '=', 'currency_translations.currency_id')
                    ->where('currency_translations.locale', static::getLangCode());
        });
        $locations->orderBy('locations.this_order', 'ASC');
        if (isset($where_array['parent_id'])) {
            $locations->where('locations.parent_id', $where_array['parent_id']);
        }  else {
            $locations->where('locations.parent_id', 0);
        }

        $locations->where('locations_translations.locale', static::getLangCode());
        $locations->select('locations.id', 'locations.parent_id', 'locations_translations.title', 'locations.image', 'currency_translations.sign');
        $locations = $locations->get();
        return static::transformCollection($locations);
    }

    public function currancy() {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function childrens() {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function translations() {
        return $this->hasMany(LocationTranslation::class, 'location_id');
    }

    public static function transform($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;

        if ($item->parent_id == 0) {
            $transformer->image = url('public/uploads/locations') . '/' . $item->image;
            $transformer->sign = $item->sign;
        }

        return $transformer;
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($location) {
            foreach ($location->childrens as $child) {
                foreach ($child->translations as $translation) {
                    $translation->delete();
                }
                $child->delete();
            }

            foreach ($location->translations as $translation) {
                $translation->delete();
            }
        });
    }

}
