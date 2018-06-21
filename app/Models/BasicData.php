<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicData extends MyModel {

    protected $table = "basic_data";
    public static $types=[
        1=>'property_types',
        2=>'engine_capacities',
        3=>'fuel_types',
        4=>'motion_vectors',
        5=>'propulsion_systems',
        6=>'mileage_kms',
        7=>'mileage'
    ];
    private static $form_type_basic_data = [
        1 => [1],
        3 => [2,3,4,5,6,7]
    ];


    private static function getData($request){
        if ($request->input('form_type') == 1) {
            $data = array(
            'property_types' => [
                'name' => 'property_type',
                'lable' => _lang('app.property_type'),
                'values' => array()
            ],
            'rooms_range' => [
                'from' => 1,
                'to'   => 5
            ],
            'baths_range' => [
                'from' => 1,
                'to'   => 5
            ]);
        }else if($request->input('form_type') == 3){
            $data = array(
            'engine_capacities' => [
                'name' => 'engine_capacity',
                'lable' => _lang('app.engine_capacity'),
                'values' => array()
            ],
            'fuel_types' => [
                'name' => 'fuel_type',
                'lable' => _lang('app.fuel_type'),
                'values' => array()
            ],
            'motion_vectors' => [
                'name' => 'motion_vector',
                'lable' => _lang('app.motion_vector'),
                'values' => array()
            ],
            'propulsion_systems' => [
                'name' => 'propulsion_system',
                'lable' => _lang('app.propulsion_system'),
                'values' => array()
            ],
            'mileage_kms' => [
                'name' => 'mileage',
                'lable' => _lang('app.mileage_kms'),
                'values' => array()
            ],
            'mileage' => [
                'name' => 'mileage',
                'lable' => _lang('app.mileage'),
                'values' => array()
            ],
         );

        }

        $category_childrens = Category::getAll($request->input('category_id'));
        if ( $category_childrens->count() > 0) {
            $data['sub_categories'] = [
                'name' => '',
                'label' => '',
                'values' => self::transformCollection($category_childrens)
            ];
        }
       

        return $data;
    }
    public static function getAll($request) {
        
         $result =  static::join('basic_data_translations as trans', 'basic_data.id', '=', 'trans.basic_data_id')
                        ->orderBy('basic_data.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->where('basic_data.active', true)
                        ->whereIn('basic_data.type',self::$form_type_basic_data[$request->input('form_type')])
                        ->select('basic_data.id','trans.title','basic_data.type')
                        ->get();
         
         $data = self::getData($request);
         if ($request->input('form_type') == 1){
            $data['property_types']['values'] = self::transformCollection($result);          
         }else if ($request->input('form_type') == 3) {
            
            foreach ($result as $item) {
                switch ($item->type) {
                    case 2:
                        $data['engine_capacities']['values'][] = self::transform($item);
                        break;
                    case 3:
                        $data['fuel_types']['values'][] = self::transform($item);
                        break;
                    case 4:
                        $data['motion_vectors']['values'][] = self::transform($item);
                        break;
                    case 5:
                        $data['propulsion_systems']['values'][] = self::transform($item);
                        break;
                    case 6:
                        $data['mileage_kms']['values'][] = self::transform($item);
                        break;
                    case 7:
                        $data['mileage']['values'][] = self::transform($item);
                        break;
                    
                    default:
                        // code...
                        break;
                }
            }
                
        }         
       return $data;

    }

    public function translations() {
        return $this->hasMany(BasicDataTranslation::class, 'basic_data_id');
    }

    public static function transform($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;
        return $transformer;
    }


    protected static function boot() {
        parent::boot();

        static::deleting(function($bath) {
            foreach ($bath->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($bath) {
           
        });
    }

}
