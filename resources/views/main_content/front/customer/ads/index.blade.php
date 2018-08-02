
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
                    <li>{{_lang('app.my_ads')}}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="profile favourite myads">
    <div class="container">
        <div class="col-md-12">
            <div class="row">
                <h3 class="title">{{_lang('app.my_ads')}}</h3>
                <div class="profile-area">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-9">
                                @foreach($ads as $one)
                                <div class="block">
                                    <div class="blog-comment">
                                        <div class="col-md-9">
                                            @if($one->title)
                                            <a href="{{$one->url}}"><h4>{{$one->title}}</h4></a>
                                            @endif
                                            <div class="rate">
                                                <i class="fa fa-star {{$one->rate>=1?'yellow-star':'gray-star'}}" aria-hidden="true"></i>
                                                <i class="fa fa-star {{$one->rate>=2?'yellow-star':'gray-star'}}" aria-hidden="true"></i>
                                                <i class="fa fa-star {{$one->rate>=3?'yellow-star':'gray-star'}}" aria-hidden="true"></i>
                                                <i class="fa fa-star {{$one->rate>=4?'yellow-star':'gray-star'}}" aria-hidden="true"></i>
                                                <i class="fa fa-star {{$one->rate>=5?'yellow-star':'gray-star'}}" aria-hidden="true"></i>
                                            </div>
                                              <h6>{{$one->price.' '.$one->currency}}</h6>
                                            <div class="details-icon right">
                                                <h5><i class="fa fa-calendar date"></i> {{$one->created_at}}</h5>
                                                <h5><i class="fa fa-map-marker road"></i>{{$one->distance}}</h5>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown" aria-expanded="false"><i class="glyphicon glyphicon-option-vertical"></i></button>
                                                <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="{{_url('customer/ads/'.$one->id.'/edit')}}">{{_lang('app.edit')}}</a></li>
                                                    <li role="presentation"><a role="menuitem" tabindex="-1" onclick="Ads.delete(this);return false;">{{_lang('app.delete')}}</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-3" href="{{$one->url}}">
                                            <div class="row">
                                                <img class="img-responsive" src="{{$one->image}}" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach



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
</div>




@endsection
