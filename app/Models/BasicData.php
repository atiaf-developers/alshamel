<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicData extends MyModel {

    protected $table = "basic_data";
    public static $types = [
        1 => 'property_types',
        2 => 'engine_capacities',
        3 => 'fuel_types',
        4 => 'motion_vectors',
        5 => 'propulsion_systems',
        6 => 'mileage_kms',
        7 => 'mileage'
    ];
    private static $form_type_basic_data = [
        1 => [1],
        3 => [2, 3, 4, 5, 6, 7],
        2 => [],
        4 => []
    ];

    public static function getDataFrontAjax($params = array()) {
        $data = array();

        if ($params['form_type'] == 1) {
            $data = array(
                [
                    'name' => 'property_type',
                    'type' => 'dropdown',
                    'rules' => 'required'
                ],
                [
                    'name' => 'rooms_number',
                    'type' => 'range',
                    'rules' => 'required'
                ],
                [
                    'name' => 'baths_number',
                    'type' => 'range',
                    'rules' => 'required'
                ],
                [
                    'name' => 'has_parking',
                    'type' => 'radio',
                    'rules' => 'required'
                ],
                [
                    'name' => 'furnished',
                    'type' => 'radio',
                    'rules' => 'required'
                ],
                [
                    'name' => 'area',
                    'type' => 'text',
                    'rules' => 'required'
                ],
                [
                    'name' => 'price',
                    'type' => 'text',
                    'rules' => 'required'
                ]
            );
        } else if ($params['form_type'] == 2) {
            $data = array(
                [
                    'name' => 'area',
                    'type' => 'text',
                    'rules' => 'required'
                ],
                [
                    'name' => 'price',
                    'type' => 'text',
                    'rules' => 'required'
                ]
            );
        } else if ($params['form_type'] == 3) {
            $data = array(
                [
                    'name' => 'engine_capacity',
                    'type' => 'dropdown',
                    'rules' => 'required'
                ],
                [
                    'name' => 'fuel_type',
                    'type' => 'dropdown',
                    'rules' => 'required'
                ],
                [
                    'name' => 'motion_vector',
                    'type' => 'dropdown',
                    'rules' => 'required'
                ],
                [
                    'name' => 'propulsion_system',
                    'type' => 'dropdown',
                    'rules' => 'required'
                ],
                [
                    'name' => 'measruing_unit',
                    'type' => 'dropdown',
                    'rules' => 'required'
                ],
                [
                    'name' => 'car_speedometer',
                    'type' => 'dropdown',
                    'rules' => 'required'
                ],
                [
                    'name' => 'manufacturing_year',
                    'type' => 'range',
                    'rules' => 'required'
                ],
                [
                    'name' => 'status',
                    'type' => 'dropdown',
                    'rules' => 'required'
                ],
                [
                    'name' => 'price',
                    'type' => 'text',
                    'rules' => 'required'
                ]
            );
        }
        $category_childrens = Category::getAll($params['category_id']);

        if ($category_childrens->count() > 0) {
            $data[] = [
                'name' => 'category',
                'type' => 'dropdown',
                'rules' => 'required'
            ];
        }



        return $data;
    }

    private static function getDataFront($params) {
        $data = array();
        $settings = Setting::get()->keyBy('name');
        $settings['rooms_range'] = json_decode($settings['rooms_range']->value);
        $settings['baths_range'] = json_decode($settings['baths_range']->value);

        if ($params['form_type'] == 1) {
            $data = array(
                'property_types' => [
                    'name' => 'property_type',
                    'label' => _lang('app.property_type'),
                    'type' => 'dropdown',
                    'values' => array()
                ],
                'rooms_number' => [
                    'name' => 'rooms_number',
                    'label' => _lang('app.rooms_number'),
                    'type' => 'range',
                    'from' => (int) $settings['rooms_range']->from,
                    'to' => (int) $settings['rooms_range']->to
                ],
                'baths_number' => [
                    'name' => 'baths_number',
                    'label' => _lang('app.baths_number'),
                    'type' => 'range',
                    'from' => (int) $settings['baths_range']->from,
                    'to' => (int) $settings['baths_range']->to
                ],
                'furnished' => [
                    'name' => 'furnished',
                    'label' => _lang('app.furnished'),
                    'type' => 'radio',
                    'values' => [(object) ['id' => 1, 'title' => _lang('app.yes')], (object) ['id' => 0, 'title' => _lang('app.no')]]
                ],
                'has_parking' => [
                    'name' => 'has_parking',
                    'label' => _lang('app.has_parking'),
                    'type' => 'radio',
                    'values' => [(object) ['id' => 1, 'title' => _lang('app.yes')], (object) ['id' => 0, 'title' => _lang('app.no')]]
                ],
                'area' => [
                    'name' => 'area',
                    'label' => _lang('app.area') . ' ( ' . _lang('app.m²') . ' )',
                    'type' => 'text',
                    'values' => []
                ],
                'price' => [
                    'name' => 'price',
                    'label' => _lang('app.price'),
                    'type' => 'text',
                    'values' => []
                ]
            );
        } else if ($params['form_type'] == 2) {
            $data = array(
                'area' => [
                    'name' => 'area',
                    'label' => _lang('app.area') . ' ( ' . _lang('app.m²') . ' )',
                    'type' => 'text',
                    'values' => []
                ],
                'price' => [
                    'name' => 'price',
                    'label' => _lang('app.price'),
                    'type' => 'text',
                    'values' => []
                ]
            );
        } else if ($params['form_type'] == 3) {
            $current_year = (int) date('Y');
            $data = array(
                'engine_capacities' => [
                    'name' => 'engine_capacity',
                    'label' => _lang('app.engine_capacity'),
                    'type' => 'dropdown',
                    'values' => array()
                ],
                'fuel_types' => [
                    'name' => 'fuel_type',
                    'label' => _lang('app.fuel_type'),
                    'type' => 'dropdown',
                    'values' => array()
                ],
                'motion_vectors' => [
                    'name' => 'motion_vector',
                    'label' => _lang('app.motion_vector'),
                    'type' => 'dropdown',
                    'values' => array()
                ],
                'propulsion_systems' => [
                    'name' => 'propulsion_system',
                    'label' => _lang('app.propulsion_system'),
                    'type' => 'dropdown',
                    'values' => array()
                ],
                'measruing_unit' => [
                    'name' => 'measruing_unit',
                    'label' => _lang('app.measruing_unit'),
                    'type' => 'dropdown',
                    'values' => [(object) ['id' => 6, 'title' => _lang('app.kilo')], (object) ['id' => 7, 'title' => _lang('app.mileage')]]
                ],
                'car_speedometer' => [
                    'name' => 'car_speedometer',
                    'label' => _lang('app.car_speedometer'),
                    'type' => 'dropdown',
                    'values' => array()
                ],
                'manufacturing_year' => [
                    'name' => 'manufacturing_year',
                    'label' => _lang('app.manufacturing_year'),
                    'type' => 'range',
                    'from' => (int) $settings['manufacturing_year_start']->value,
                    'to' => $current_year
                ],
                 'status' => [
                    'name' => 'status',
                    'label' => _lang('app.status'),
                    'type' => 'dropdown',
                    'values' => [(object) ['id' => 1, 'title' => _lang('app.new')], (object) ['id' => 0, 'title' => _lang('app.used')]]
                ],
                'price' => [
                    'name' => 'price',
                    'label' => _lang('app.price'),
                    'type' => 'text',
                    'values' => []
                ]
            );
        }

        $category_childrens = Category::getAll($params['category_id']);

        if ($category_childrens->count() > 0) {
            $first_parent_id = explode(",", $category_childrens[0]->parents_ids);
            $first_parent = Category::join('categories_translations as trans', 'categories.id', '=', 'trans.category_id')
                    ->where('trans.locale', static::getLangCode())
                    ->select('trans.label')
                    ->where('categories.id', $first_parent_id[0])
                    ->first();
            $data[] = [
                'name' => 'category',
                'label' => $first_parent->label,
                'type' => 'dropdown',
                'values' => self::transformCollection($category_childrens)
            ];
        }


        return $data;
    }

    public static function getAllFront($params = array()) {

        $result = static::join('basic_data_translations as trans', 'basic_data.id', '=', 'trans.basic_data_id')
                ->orderBy('basic_data.this_order', 'ASC')
                ->where('trans.locale', static::getLangCode())
                ->where('basic_data.active', true)
                ->whereIn('basic_data.type', self::$form_type_basic_data[$params['form_type']])
                ->select('basic_data.id', 'trans.title', 'basic_data.type')
                ->get();

        $data = static::getDataFront($params);
        //dd($params);
        if ($params['form_type'] == 1) {
            $data['property_types']['values'] = self::transformCollection($result);
        } else if ($params['form_type'] == 3) {

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


                    default:
                        // code...
                        break;
                }
            }
        }
        if (empty($data)) {
            $data = new \stdClass();
        }
        return $data;
    }

    private static function getData($request) {
        $data = array();
        $settings = Setting::get()->keyBy('name');
        $settings['rooms_range'] = json_decode($settings['rooms_range']->value);
        $settings['baths_range'] = json_decode($settings['baths_range']->value);

        if ($request->input('form_type') == 1) {
            $data = array(
                'property_types' => [
                    'name' => 'property_type',
                    'lable' => _lang('app.property_type'),
                    'values' => array()
                ],
                'rooms_range' => [
                    'name' => 'rooms_number',
                    'lable' => _lang('app.rooms_number'),
                    'from' => (int) $settings['rooms_range']->from,
                    'to' => (int) $settings['rooms_range']->to
                ],
                'baths_range' => [
                    'name' => 'baths_number',
                    'lable' => _lang('app.baths_number'),
                    'from' => (int) $settings['baths_range']->from,
                    'to' => (int) $settings['baths_range']->to
            ]);
        } else if ($request->input('form_type') == 3) {
            $current_year = (int) date('Y');
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
                'manufacturing_year' => [
                    'name' => 'manufacturing_year',
                    'lable' => _lang('app.manufacturing_year'),
                    'from' => (int) $settings['manufacturing_year_start']->value,
                    'to' => $current_year
                ],
            );
        }

        $category_childrens = Category::getAll($request->input('category_id'));

        if ($category_childrens->count() > 0) {
            $first_parent_id = explode(",", $category_childrens[0]->parents_ids);
            $first_parent = Category::join('categories_translations as trans', 'categories.id', '=', 'trans.category_id')
                    ->where('trans.locale', static::getLangCode())
                    ->select('trans.label')
                    ->where('categories.id', $first_parent_id[0])
                    ->first();
            $data['sub_categories'] = [
                'name' => 'category_id',
                'label' => $first_parent->label,
                'values' => self::transformCollection($category_childrens)
            ];
        }


        return $data;
    }

    public static function getAll($request) {

        $result = static::join('basic_data_translations as trans', 'basic_data.id', '=', 'trans.basic_data_id')
                ->orderBy('basic_data.this_order', 'ASC')
                ->where('trans.locale', static::getLangCode())
                ->where('basic_data.active', true)
                ->whereIn('basic_data.type', self::$form_type_basic_data[$request->input('form_type')])
                ->select('basic_data.id', 'trans.title', 'basic_data.type')
                ->get();

        $data = self::getData($request);
        if ($request->input('form_type') == 1) {
            $data['property_types']['values'] = self::transformCollection($result);
        } else if ($request->input('form_type') == 3) {

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
        if (empty($data)) {
            $data = new \stdClass();
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
