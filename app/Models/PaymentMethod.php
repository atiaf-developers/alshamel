<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends MyModel {

    protected $table = "payment_methods";

    public static function getAll() {
        return static::join('payment_methods_translations as trans', 'payment_methods.id', '=', 'trans.payment_method_id')
                        ->orderBy('payment_methods.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->where('payment_methods.active', true)
                        ->select('payment_methods.id','trans.title')
                        ->get();
    }

    public function translations() {
        return $this->hasMany(PaymentMethodTranslation::class, 'payment_method_id');
    }

    public static function transform($item) {
        return $item;
    }


    protected static function boot() {
        parent::boot();

        static::deleting(function($payment_method) {
            foreach ($payment_method->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($payment_method) {
           
        });
    }

}
