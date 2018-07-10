<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends MyModel
{
    protected $table = "slider";

    public function translations() {
        return $this->hasMany(SliderTranslation::class, 'slider_id');
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($slider) {
            foreach ($slider->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($slider) {
            self::deleteUploaded('slider',$slider->image);
        });
    }
}
