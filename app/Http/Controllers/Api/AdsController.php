<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Ad;
use App\Models\Category;
use App\Models\UserPackage;
use App\Models\Feature;
class AdsController extends ApiController
{
    private $rules = array(
        'form_type' => 'required',
    );
    private $form_types=[1,2,3,4];
   
    public function index(){}
    public function store(Request $req){
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        }else{
            $level=Category::find($req->category_one_id)->level;
            if(in_array($req->form_type, $this->form_types)){
                if($this->form_type==1){
                    foreach(Ad::fields_type_one as $value){
                        $this->ruels[$value]='required';
                    }
                }elseif($this->form_type==2){
                    foreach(Ad::fields_type_two as $value){
                        $this->ruels[$value]='required';
                    }
                }elseif($this->form_type==3){
                    foreach(Ad::fields_type_three as $value){
                        $this->ruels[$value]='required';
                    }
                }elseif($this->form_type==4){
                    foreach(Ad::fields_type_four as $value){
                        $this->ruels[$value]='required';
                    }
                }
                if($level==3){
                    $this->ruels['category_three_id']='required';  
                }
                $validator = Validator::make($request->all(), $this->rules);
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
                        foreach(Ad::fields_type_four as $value){
                            $Ad->$value=$req->$value;
                        }
                        if($level==3){
                            $Ad->category_three_id=$req->$category_three_id;  
                        }
                        $Ad->save();
                        $Features=new Feature;
                        if(in_array($this->form_type,[1,2,3])){
                            if($this->form_type==1){
                                foreach(Ad::fields_type_one as $key=>$value){
                                    $Features->name=$value;
                                    $Features->value=$req->$value;
                                    $Features->ad_id=$Ad->id;
                                }
                            }elseif($this->form_type==2){
                                foreach(Ad::fields_type_two as $value){
                                    $Features->name=$value;
                                    $Features->value=$req->$value;
                                    $Features->ad_id=$Ad->id;
                                }
                            }elseif($this->form_type==3){
                                foreach(Ad::fields_type_three as $value){
                                    $Features->name=$value;
                                    $Features->value=$req->$value;
                                    $Features->ad_id=$Ad->id;
                                }
                            }
                        }
                        $Features->save();
                        DB::commit();
                        return _api_json('message', _lang('app.added_successfully'),201);
                    }catch(\Exception $ex){
                        DB::rollback();
                        return _api_json('error', $ex->getMessage(), 400);
                    }
                }
            }else{
                return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
            }
        }
    }
}
