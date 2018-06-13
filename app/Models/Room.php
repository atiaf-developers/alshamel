<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends MyModel {

    protected $table = "rooms";

    public static function getAll() {
        return static::join('rooms_translations as trans', 'rooms.id', '=', 'trans.room_id')
                        ->orderBy('rooms.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->where('rooms.active', true)
                        ->select('rooms.id','trans.title')
                        ->get();
    }

    public function translations() {
        return $this->hasMany(RoomTranslation::class, 'room_id');
    }

    public static function transform($item) {
        return $item;
    }


    protected static function boot() {
        parent::boot();

        static::deleting(function($room) {
            foreach ($room->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($property_type) {
           
        });
    }

}
