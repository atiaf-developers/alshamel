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

    public function index(Request $request){
        $rules=array(
            'city_id' => 'required',
            'lat' => 'required',
            'lng' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        }
        return _api_json(Ad::get_all($request));
    }


    public function store(Request $request){

        if (!$request->input('form_type') || !$request->input('category_one_id')) {
            return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
        }
        $num_of_levels = Category::find($request->input('category_one_id'))->no_of_levels;
        if($num_of_levels == 3){
            $ruels['category_three_id']='required';
        }
        $rules = Ad::validation_rules($request->input('form_type'));
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        }else{
            $user=$this->auth_user();
            if($user->num_free_ads==0){
                $avaliable_ads= UserPackage::where('user_id',$user->id)->where('num_of_ads','<>',0)->first();
                if(!$avaliable_ads){
                    $message = ['message' => _lang('app.You_do_not_have_package_to_add_your_ad')];
                    return _api_json('', $message, 400);
                }
            }
            DB::beginTransaction();
            try{
                $ad= new Ad;
                $ad->category_one_id = $request->input('category_one_id');
                $ad->category_two_id = $request->input('category_two_id');
                $ad->country_id = $request->input('country_id');
                $ad->city_id = $request->input('city_id');
                $ad->title = $request->input('title');
                $ad->details = $request->input('details');
                $ad->lat = $request->input('lat');
                $ad->lng = $request->input('lng');
                $ad->email = $request->input('email');
                $ad->mobile = $request->input('mobile');

                if($num_of_levels==3){
                    $ad->category_three_id=$request->input('category_three_id');  
                }

                $ad->user_id = $user->id;

                if(isset($request->images) && !empty($request->images)){
                    $images = [];
                    foreach (json_decode($request->images) as $image) {
                        $image = preg_replace("/\r|\n/", "", $image);
                        if (!isBase64image($image)) {
                            continue;
                        }
                        $images[] = Ad::upload($image, 'ads', true, false, true);
                    }
                    $ad->images = json_encode($images);
                }
                $ad->save();


                if(in_array($request->input('form_type'),[1,2,3])){
                    $data=[];
                    $feature=[];

                    if($request->input('form_type')==1){
                       $features = Ad::real_states_features;
                    }elseif($request->input('form_type')==2){
                       $features = Ad::lands_features;
                    }elseif($request->input('form_type')==3){
                       $features = Ad::cars_features;
                    }
                    foreach($features as $key=>$value){
                            $feature['name']=$value;
                            $feature['value']=$request->$value;
                            $feature['ad_id']=$Ad->id;
                            $data[]=$feature;
                    }
                    Feature::insert($data);
                }
                DB::commit();
                return _api_json(new \stdClass(),['message'=>_lang('app.added_successfully')]);
            }catch(\Exception $ex){
                DB::rollback();
                return _api_json(new \stdClass(), ['message'=>$ex->getMessage()], 400);
            }

        }
    }

}
