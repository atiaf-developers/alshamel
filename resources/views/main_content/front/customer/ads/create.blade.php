
@extends('layouts.front')

@section('pageTitle',_lang('app.my_profile'))


@section('js')
<script src=" {{ url('public/front/scripts') }}/profile.js"></script>
@endsection

@section('content')
<div class="page-head_agile_info_w3l">
    <div class="container">
        <div class="services-breadcrumb">
            <div class="agile_inner_breadcrumb">

                <ul class="w3_short">
                    <li><a href="index.php">الرئيسية</a><i>|</i></li>
                    <li>صفحتى</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<style>
    #loader {
        position: absolute;
        left: 50%;
        top: 45%;
        z-index: 1;
        width: 60px;
        height: 60px;
        margin: -75px 0 0 -75px;
        border: 10px solid #8a5c8b;
        border-radius: 50%;
        border-top: 10px solid #c5bfc0;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        display: none;
        z-index: 99999;
    }
    .loading{
        opacity: 0.3;
    }
</style>
<div class="profile newad">
    <div class="container">
        <div class="col-md-12">
            <div class="row">
                <h3 class="title">صفحتى</h3>
                <div class="profile-area">
                    <div class="col-md-12">
                        <div class="col-md-9 ad-form-content">
                            <div id="loader"></div>
                            <h4>متبقى من الاعلانات <span class="no">0</span> اعلان مجانى </h4>
                            <h4>باقى من الاعلانات <span class="no">5</span> اعلان </h4>
                            <a href="offers.php" class="btn bg baka">الباقات</a>
                            
                            <form id="loginform" class="form-horizontal" role="form">


                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="col-sm-3 col-form-label">{{_lang('app.country')}}</label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <select class="frm-field required sect form-control" name="ad_country">
                                                    <option value="">{{_lang('app.choose_country')}}</option>
                                                    @foreach ($countries as $key => $one)
                                                    <option {{$country_id==$one->id?'selected':''}} value="{{$one->id}}">{{$one->title}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="col-sm-3 col-form-label">{{_lang('app.city')}}</label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <select class="frm-field required sect form-control" name="ad_city">
                                                    @if(isset($cities))
                                                    @foreach ($cities as $key => $one)
                                                    <option {{$city_id==$one->id?'selected':''}} value="{{$one->id}}">{{$one->title}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="col-sm-3 col-form-label">{{_lang('app.main_category')}}</label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <select class="frm-field required sect form-control" name="main_category">
                                                    <option value="">{{_lang('app.choose')}}</option>
                                                    @foreach ($main_categories as $key => $one)
                                                    <option  value="{{$one->id}}">{{$one->title}}</option>
                                                    @endforeach		
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="col-sm-3 col-form-label">{{_lang('app.sub_category')}}</label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <select class="frm-field required sect form-control" name="sub_category">
                                                    <option value="">{{_lang('app.choose')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="col-sm-3 col-form-label">اسم الاعلان</label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <input type="email" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="col-sm-3 col-form-label">رقم الجوال</label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <input type="email" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="col-sm-3 col-form-label">البريد الالكترونى</label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <input type="email" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="col-sm-3 col-form-label">الوصف</label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <textarea rows="5" cols="30" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="basic-data-content">
                                    
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="col-sm-3 col-form-label">الموقع على الخريطة</label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <iframe src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d6909.863923820662!2d31.42740812695312!3d30.01010996899653!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sus!4v1527056238238" width="100%" height="250" frameborder="0" style="border:0" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="col-sm-3 col-form-label">يمكنك ارفاق 8 من الصور</label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <input type="file" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="col-sm-3 col-form-label">السعر</label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>













                                <div class="form-group">
                                    <div class="col-sm-12 controls">
                                        <a id="btn-login" href="#" class="btn bg">أضف الاعلان  </a>
                                    </div>
                                </div>


                            </form>
                        </div>
                        <div class="col-md-3">

                            @include('components/front/profile/sidebar')


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
