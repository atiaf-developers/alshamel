
@extends('layouts.front')

@section('pageTitle',_lang('app.my_profile'))


@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>

<script src="{{url('public/front/scripts')}}/map.js" type="text/javascript"></script>
<script src=" {{ url('public/front/scripts') }}/ads.js"></script>
@endsection

@section('content')
<div class="page-head_agile_info_w3l">
    <div class="container">
        <div class="services-breadcrumb">
            <div class="agile_inner_breadcrumb">

                <ul class="w3_short">
                    <li><a href="{{_url('')}}">{{_lang('app.home')}}</a><i>|</i></li>
                    <li><a href="{{_url('customer/ads')}}">{{_lang('app.my_ads')}}</a><i>|</i></li>
                    <li>صفحتى</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<style>
    .ad-form-content{
        position:relative;
    }
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
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
<div class="profile newad">
    <div class="container">
        <div class="col-md-12">
            <div class="row">
                <h3 class="title">صفحتى</h3>
                <div class="profile-area">
                    <div class="col-md-12">

                        <div class="col-md-9">
                            <div id="loader"></div>
                            <div class="ad-form-content">
                                <h4>متبقى من الاعلانات <span class="no">0</span> اعلان مجانى </h4>
                                <h4>باقى من الاعلانات <span class="no">5</span> اعلان </h4>
                                <a href="offers.php" class="btn bg baka">الباقات</a>

                                <form id="loginform" class="addEditAdsForm form-horizontal" role="form" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" id="id" value="{{$ad->id}}">
                                    <input type="hidden" name="category_id" id="category_id" value="{{$ad->category_id}}">

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">{{_lang('app.country')}}</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <select class="frm-field required sect form-control" name="ad_country">
                                                        <option value="">{{_lang('app.choose_country')}}</option>
                                                        @foreach ($countries as $key => $one)
                                                        <option {{$ad->country_id==$one->id?'selected':''}} value="{{$one->id}}">{{$one->title}}</option>
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
                                                        <option value="">{{_lang('app.choose')}}</option>
                                                        @if(isset($cities))
                                                        @foreach ($cities as $key => $one)
                                                        <option {{$ad->city_id==$one->id?'selected':''}} value="{{$one->id}}">{{$one->title}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>




                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">{{_lang('app.title')}}</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <input type="text" class="form-control" name="title" value="{{$ad->title}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">{{_lang('app.mobile')}}</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <input type="text" class="form-control" name="mobile" value="{{$ad->mobile}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">{{_lang('app.email')}}</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <input type="email" class="form-control" name="email" value="{{$ad->email}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">{{_lang('app.details')}}</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <textarea rows="5" cols="30" class="form-control" name="details">{{$ad->details}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="basic-data-content">
                                        @include('main_content/front/ajax/ad_form')
                                    </div>

                                  
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">{{_lang('app.location')}}</label>
                                            <div class="col-sm-9 form-group">
                                                <input value="" type="hidden"  id="latlng" name="latlng" value="{{$ad->lat.','.$ad->lng}}">
                                                <input value="" type="hidden"  id="lat" name="lat" value="{{$ad->lat}}">
                                                <input value="" type="hidden"  id="lng" name="lng" value="{{$ad->lng}}">
                                                <div class="row">

                                                    <input id="pac-input" class="controls" type="text" placeholder="Enter a location">
                                                    <div id="map" style="height: 500px; width:100%;"></div>
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
                                        <div class="col-sm-12 controls">
                                            <a id="btn-login" href="#" class="btn bg submit-form">{{_lang('app.save')}}  </a>
                                        </div>
                                    </div>


                                </form>
                                <div class="alert alert-success" style="display:{{Session('successMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-check" aria-hidden="true"></i> <span class="message">{{Session::get('successMessage')}}</span></div>
                            <div class="alert alert-danger" style="display:{{Session('errorMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span class="message">{{Session::get('errorMessage')}}</span></div>
                            </div>
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
