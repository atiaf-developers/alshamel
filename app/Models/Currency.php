<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends MyModel
{
    protected $table = "currency";

    public function translations() {
        return $this->hasOne(CurrencyTranslation::class, 'currency_id')->where('currency_translations.locale',static::getLangCode());
    }



    protected static function boot() {
        parent::boot();

        static::deleting(function($currency) {
            foreach ($currency->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function() {
            
        });
    }
}
