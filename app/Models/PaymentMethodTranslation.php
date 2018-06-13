<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodTranslation extends MyModel {

    protected $table = "payment_methods_translations";
    protected $fillable=['title'];

 

}
