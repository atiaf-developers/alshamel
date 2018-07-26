<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends MyModel {

    protected $table = "slider";

    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
        'l' => array('width' => 1000, 'height' => 600),
    );
    public function translations() {
        return $this->hasMany(SliderTranslation::class, 'slider_id');
    }

    public static function getAllFront($where_array = array()) {
        $slider = Slider::join('slider_translations', 'slider.id', '=', 'slider_translations.slider_id')
                ->where('slider_translations.locale', static::getLangCode())
                ->where('slider.active', true)
                ->orderBy('slider.this_order')
                ->select('slider.image', 'slider_translations.title', 'slider.url')
                ->get();
        
        return static::transformCollection($slider);
    }

    public static function transform($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;
        $transformer->url = $item->url;
        $transformer->image = url('public/uploads/slider') . '/l_' . static::rmv_prefix($item->image);
        return $transformer;
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($slider) {
            foreach ($slider->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($slider) {
            self::deleteUploaded('slider', $slider->image);
        });
    }

}
