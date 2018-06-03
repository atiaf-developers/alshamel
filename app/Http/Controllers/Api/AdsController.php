<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Ad;
use App\Models\Category;
use App\Models\UserPackage;
use App\Models\Feature;
use Validator;
use DB;
class AdsController extends ApiController
{
    private $rules = array(
        'form_type' => 'required',
        'category_one_id'=>'required',
    );
    
   
    public function index(Request $req){
        $rules=array(
            'city_id' => 'required',
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        }
        return _api_json(Ad::get_all($req));
    }
    public function store(Request $req){
        $validator = Validator::make($req->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        }else{
            $level=Category::find($req->category_one_id)->no_of_levels;
            if(isset(Ad::$form_types[$req->form_type])){
                if($req->form_type==1){
                    foreach(Ad::$fields_type_one as $value){
                        $this->ruels[$value]='required';
                    }
                }elseif($req->form_type==2){
                    foreach(Ad::$fields_type_two as $value){
                        $this->ruels[$value]='required';
                    }
                }elseif($req->form_type==3){
                    foreach(Ad::$fields_type_three as $value){
                        $this->ruels[$value]='required';
                    }
                }elseif($req->form_type==4){
                    foreach(Ad::$fields_type_four as $value){
                        $this->ruels[$value]='required';
                    }
                }
                if($level==3){
                    $this->ruels['category_three_id']='required';  
                }
                $validator = Validator::make($req->all(), $this->rules);
                if ($validator->fails()) {
                    $errors = $validator->errors()->toArray();
                    return _api_json(new \stdClass(), ['errors' => $errors], 400);
                }else{
                    $user=$this->auth_user();
                    if($user->num_free_ads==0){
                        $avaliable_ads=UserPackage::where('user_id',$user->id)->where('num_of_ads','<>',0)->first();
                        if(!$avaliable_ads){
                            $message = ['message' => _lang('app.You_do_not_have_package_to_add_your_ad')];
                            return _api_json([], $message, 400);
                        }
                    }
                    DB::beginTransaction();
                    try{
                        $Ad=new Ad;
                        foreach(Ad::$fields_type_four as $value){
                            $Ad->$value=$req->$value;
                        }
                        if($level==3){
                            $Ad->category_three_id=$req->$category_three_id;  
                        }
                        $Ad->user_id=$user->id;
                        if(isset($req->images) && !empty($req->images)){
                            $images = [];
                            foreach (json_decode($req->images) as $image) {
                                $image = preg_replace("/\r|\n/", "", $image);
                                if (!isBase64image($image)) {
                                    continue;
                                }
                                $images[] = Ad::upload($image, 'ads', true, false, true);
                            }
                            $Ad->images = json_encode($images);
                        }
                        $Ad->save();
                        if(in_array($req->form_type,[1,2,3])){
                            $array=[];
                            $arr=[];
                            if($req->form_type==1){
                                foreach(Ad::$fields_type_one as $key=>$value){
                                    $arr['name']=$value;
                                    $arr['value']=$req->$value;
                                    $arr['ad_id']=$Ad->id;
                                    $array[]=$arr;
                                }
                            }elseif($req->form_type==2){
                                foreach(Ad::$fields_type_two as $value){
                                    $arr['name']=$value;
                                    $arr['value']=$req->$value;
                                    $arr['ad_id']=$Ad->id;
                                    $array[]=$arr;
                                }
                            }elseif($req->form_type==3){
                                foreach(Ad::$fields_type_three as $value){
                                    $arr['name']=$value;
                                    $arr['value']=$req->$value;
                                    $arr['ad_id']=$Ad->id;
                                    $array[]=$arr;
                                }
                            }
                            Feature::insert($array);
                        }
                        DB::commit();
                        return _api_json(new \stdClass(),['message'=>_lang('app.added_successfully')]);
                    }catch(\Exception $ex){
                        DB::rollback();
                        return _api_json(new \stdClass(), ['message'=>$ex->getMessage()], 400);
                    }
                }
            }else{
                return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
            }
        }
    }
}
