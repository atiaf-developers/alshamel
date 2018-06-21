<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends MyModel
{
    protected $table = 'packages';


    public static function getAll() {
        return static::join('packages_translations as trans', 'packages.id', '=', 'trans.package_id')
                        ->orderBy('packages.this_order', 'ASC')
                        ->where('packages.active',true)
                        ->where('trans.locale', static::getLangCode())
                        ->select('packages.id','trans.title','packages.num_of_ads','packages.price')
                        ->paginate(static::$limit);
    }




    public function translations() {
        return $this->hasMany(PackageTranslation::class, 'package_id');
    }
    public function translationsAdmin() {
        return $this->hasOne(PackageTranslation::class, 'package_id')
        ->where('packages_translations.locale', static::getLangCode());
    }


    public function transform($item)
    {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;
        $transformer->price = $item->price;
        $transformer->num_of_ads = $item->num_of_ads;

        return $transformer;

    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($package) {
            foreach ($package->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function() {
            
        });
    }
}
