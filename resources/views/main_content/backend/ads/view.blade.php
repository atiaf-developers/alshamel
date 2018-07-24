@extends('layouts.backend')

@section('pageTitle',  $ad->title)
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('ads.index')}}">{{_lang('app.ads')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{ $ad->title }}</span></li>

@endsection
@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>
<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>
<script src="{{url('public/backend/js')}}/ads.js" type="text/javascript"></script>

@endsection
@section('content')

<input type="hidden" name="lat" id="lat" value="{{ $ad->lat}}">
<input type="hidden" name="lng" id="lng" value="{{ $ad->lng }}">

{{ csrf_field() }}

<div class="row">
    <div class="col-md-6">
        <div class="col-md-12">

            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet box red">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>{{ $ad->title }}
                    </div>
                        <!--                        <div class="tools">
                                                    <a href="javascript:;" class="collapse" ad-original-title="" title="">
                                                    </a>                
                                                    <a href="javascript:;" class="remove" ad-original-title="" title="">
                                                    </a>
                                                </div>-->
                                            </div>
                                            <div class="portlet-body">
                                                <div class="table-scrollable">
                                                    <table class="table table-hover">

                                                        <tbody>
                                                            <tr>
                                                                <td>{{ _lang('app.title')}}</td>
                                                                <td>{{$ad->title}}</td>

                                                            </tr>
                                                            <tr>
                                                                <td>{{ _lang('app.details')}}</td>
                                                                <td>{{$ad->details}}</td>

                                                            </tr>
                                                            <tr>
                                                                <td>{{ _lang('app.city')}}</td>
                                                                <td>{{$ad->city}}</td>

                                                            </tr>
                                                            <tr>
                                                                <td>{{ _lang('app.rate')}}</td>
                                                                <td>{{$ad->rate}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ _lang('app.address')}}</td>
                                                                <td>{{$ad->address}}</td>

                                                            </tr>
                                                            <tr>
                                                                <td>{{ _lang('app.created_at')}}</td>
                                                                <td>{{$ad->created_at}}</td>

                                                            </tr>

                                                            <tr>
                                                                <td>{{ _lang('app.price')}}</td>
                                                                <td>{{$ad->price}}</td>

                                                            </tr>

                                                            <tr>
                                                                <td>{{ _lang('app.special')}}</td>
                                                                @if($ad->special == 1)
                                                                <td>{{ _lang('app.special')}}</td>
                                                                @else
                                                                <td>{{ _lang('app.not_special')}}</td>
                                                                @endif


                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>

                                                @if(count($ad->images) > 0 )

                                                <h3>{{_lang('app.gallery')}}</h3>
                                                <ul class="list-inline blog-images">
                                                    @foreach($ad->images as $one)
                                                    <li>
                                                        <a class="fancybox-button" product-rel="fancybox-button" title="390 x 220 - keenthemes.com" href="{{$one}}">
                                                            <img style="width: 100px;height: 100px;" alt="" src="{{$one}}">
                                                        </a>
                                                    </li>
                                                    @endforeach
                                                </ul>

                                                @endif

                                            </div>
                                        </div>
                                        <!-- END SAMPLE TABLE PORTLET-->


                                    </div>
                                    <div class="col-md-12">


                                        <!-- BEGIN SAMPLE TABLE PORTLET-->
                                        <div class="portlet box red">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cogs"></i>{{ _lang('app.user_info')}}
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                             <div class="table-scrollable">
                                                <table class="table table-hover">

                                                    <tbody>
                                                        <tr>
                                                            <td>{{ _lang('app.name')}}</td>
                                                            <td>{{$ad->name}}</td>

                                                        </tr>
                                                        <tr>
                                                            <td>{{ _lang('app.mobile')}}</td>
                                                            <td>{{$ad->mobile}}</td>

                                                        </tr>

                                                        <tr>
                                                            <td>{{ _lang('app.email')}}</td>
                                                            <td>{{$ad->email}}</td>

                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- BEGIN SAMPLE TABLE PORTLET-->
                                <div class="portlet box red">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i> {{ _lang('app.location')}}
                                        </div>

                                    </div>
                                    <div class="portlet-body">

                                        <div class="maplarger">

                                            <div id="map" style="height: 300px; width:100%;"></div>
                                            <div id="infowindow-content">
                                                <span id="place-name"  class="title"></span><br>
                                                Place ID <span id="place-id"></span><br>
                                                <span id="place-address"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (in_array($ad->form_type , [1,2,3]))
                            <div class="col-md-6">
                                <!-- BEGIN SAMPLE TABLE PORTLET-->
                                <div class="portlet box red">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i>{{ _lang('app.ad_features')}}
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                     <div class="table-scrollable">
                                        <table class="table table-hover">
                                            <tbody>
                                                @foreach ($ad->features as $feature)
                                                <tr>
                                                    <td>{{ $feature['name'] }}</td>
                                                    <td>{{ $feature['value'] }}</td>
                                                </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        @endif




                        <div class="col-md-12">


                            <!-- BEGIN SAMPLE TABLE PORTLET-->
                            <div class="portlet box red">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-cogs"></i>{{ _lang('app.comments')}}
                                    </div>
                                </div>
                                <div class="portlet-body">
                                   <div class="blog-single-content bordered blog-container">
                                    <div class="blog-comments">
                                        <h3 class="sbold blog-comments-title">{{ _lang('app.comments') }}({{ $rates->count() }})</h3>
                                        <hr>
                                        <div class="c-comment-list" style="height: 200px; overflow-y: auto;">


                                         @foreach ($rates as $rate)
                                         <div class="media">
                                           <div class="media-left">

                                            <a href="#">
                                                <img class="media-object" alt="" src="{{ url('public/uploads/users/'.$rate->image) }}" width="75" height="75"> </a>
                                            </div>

                                            <div class="media-body">
                                                <h4 class="media-heading"><span class="c-date">{{ $rate->created_at }}</span></h4>
                                                <br>
                                                {{ $rate->comment }}
                                            </div>

                                            <a href="javascript:;" data-id="{{$rate->id}}" onclick="Ads.delete_comment(this);return false;" class="btn btn-danger btn-sm pull-right">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </a>
                                            <a class="btn btn-sm pull-right {{$rate->active==1?'btn-primary':'btn-warning'}}" onclick = "Ads.CommentStatus(this);return false;" data-id = "{{$rate->id}}" > 
                                                {{$rate->active==1?_lang('app.active'):_lang('app.not_active')}}
                                            </a>

                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

           




            </div>







            <script>
                var new_lang = {};


            </script>
            @endsection
