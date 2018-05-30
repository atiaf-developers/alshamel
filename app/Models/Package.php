<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends MyModel
{
    protected $table = 'packages';

    public function translations() {
        return $this->hasMany(PackageTranslation::class, 'package_id');
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
