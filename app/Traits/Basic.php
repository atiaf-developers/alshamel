<?php

namespace App\Traits;

use App\Models\Setting;
use Image;
use App\Models\NotiObject;
use App\Models\Noti;
use App\Helpers\Fcm;
use App\Models\Device;
use DB;

trait Basic {

    protected $languages = array(
        'ar' => 'arabic',
        'en' => 'english'
    );

    protected static function getLangCode() {
        $lang_code = app()->getLocale();

        return $lang_code;
    }

    protected function inputs_check($model, $inputs = array(), $id = false, $return_errors = true) {
        $errors = array();
        foreach ($inputs as $key => $value) {
            $where_array = array();
            $where_array[] = array($key, '=', $value);
            if ($id) {
                $where_array[] = array('id', '!=', $id);
            }

            $find = $model::where($where_array)->get();

            if (count($find)) {

                $errors[$key] = array(_lang('app.' . $key) . ' ' . _lang("app.added_before"));
            }
        }

        return $errors;
    }

    public function _view($main_content, $type = 'front') {
        $main_content = "main_content/$type/$main_content";
        return view($main_content, $this->data);
    }

    protected function settings() {
        $settings = Setting::get();
        $settings[0]->noti_status = json_decode($settings[0]->noti_status);
        return $settings[0];
    }

    protected function slugsCreate() {
        $this->title_slug = 'title_' . $this->lang_code;
        $this->data['title_slug'] = $this->title_slug;
    }

   protected function send_noti_fcm($notification, $user_id = false, $device_token = false, $device_type = false) {
        if(!isset($notification['title'])){
            $notification['title']= env('APP_NAME');
        }
        $Fcm = new Fcm;
        if ($user_id) {
            $token_and = Device::whereIn('user_id', $user_id)
                    ->where('device_type', 1)
                    ->pluck('device_token');
            $token_ios = Device::whereIn('user_id', $user_id)
                    ->where('device_type', 2)
                    ->pluck('device_token');
            $token_and = $token_and->toArray();
            $token_ios = $token_ios->toArray();
            if (count($token_and) > 0) {
                $Fcm->send($token_and, $notification, 'and');
            } 
            if (count($token_ios) > 0) {
                $Fcm->send($token_ios, $notification, 'ios');
            }
        } else {
            $device_type = $device_type == 1 ? 'and' : 'ios';
            $Fcm->send($device_token, $notification, $device_type);
           
        }
    }

  

    protected function create_noti($entity_id, $notifier_id, $entity_type, $notifible_type = 1) {
        $NotiObject = new NotiObject;
        $NotiObject->entity_id = $entity_id;
        $NotiObject->entity_type_id = $entity_type;
        $NotiObject->notifiable_type = $notifible_type;
        $NotiObject->save();
        $Noti = new Noti;
        $Noti->notifier_id = $notifier_id;
        $Noti->noti_object_id = $NotiObject->id;

        $Noti->save();
    }

    protected function lang_rules($columns_arr = array()) {
        $rules = array();

        if (!empty($columns_arr)) {
            foreach ($columns_arr as $column => $rule) {
                foreach ($this->languages as $lang_key => $locale) {
                    $key = $column . '.' . $lang_key;
                    $rules[$key] = $rule;
                }
            }
        }
        return $rules;
    }

     public function updateValues($model, $data,$quote=false) {
        //dd($values);
        $table = $model::getModel()->getTable();
        //dd($table);

        $columns = array_keys($data);

           $where_arr=[];
        $sql_arr = [];
        $count=0;
        foreach ($data as $column => $value_arr) {
            //dd($value_arr);
            $cases = [];
            foreach ($value_arr as $one) {
          
                $value =  $one['value'];
                $cond =  $one['cond'];
                $where_str=[];
                foreach($cond as $one_cond){
                    $where_str[]=$one_cond[0].' '.$one_cond[1].' '.$one_cond[2];
                }
                $where_str=implode(' and ', $where_str);
                $where_arr[]="($where_str)";
                if($quote){
                      $cases[] = "WHEN $where_str then '{$value}'";
                }else{
                      $cases[] = "WHEN $where_str then {$value}";
                }
            
            }
                
            $cases = implode(' ', $cases);
           
            if($count==0){
                 $sql_arr[] = "SET `{$column}` = CASE  {$cases} END";
            }else{
                 $sql_arr[] = "`{$column}` = CASE  {$cases} END";
            }
            $count++;
        }
     
        $where_arr = implode(' or ', $where_arr);
        //dd($where_arr);
        $sql_str = implode(',', $sql_arr);
        //dd("UPDATE `$table` $sql_str WHERE $where_arr");
        return DB::update("UPDATE `$table` $sql_str WHERE $where_arr");
    }

}
