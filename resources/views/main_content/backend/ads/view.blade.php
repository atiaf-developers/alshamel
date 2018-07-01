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
                                                            @if ($ad->form_type == 1)
                                                                <tr>
                                                                    <td>{{ _lang('app.area')}}</td>
                                                                    <td>{{$ad->area}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ _lang('app.rooms_number')}}</td>
                                                                    <td>{{$ad->rooms_number}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ _lang('app.baths_number')}}</td>
                                                                    <td>{{$ad->baths_number}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ _lang('app.is_furnished')}}</td>
                                                                    <td>{{$ad->is_furnished}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ _lang('app.has_parking')}}</td>
                                                                    <td>{{$ad->area}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ _lang('app.property_type')}}</td>
                                                                    <td>{{$ad->property_type}}</td>
                                                                </tr>

                                                            @elseif($ad->form_type == 2)
                                                                <tr>
                                                                    <td>{{ _lang('app.area')}}</td>
                                                                    <td>{{$ad->area}}</td>
                                                                </tr>
                                                            @elseif($ad->form_type == 3)
                                                                <tr>
                                                                    <td>{{ _lang('app.motion_vector')}}</td>
                                                                    <td>{{$ad->motion_vector}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ _lang('app.engine_capacity')}}</td>
                                                                    <td>{{$ad->engine_capacity}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ _lang('app.propulsion_system')}}</td>
                                                                    <td>{{$ad->propulsion_system}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ _lang('app.fuel_type')}}</td>
                                                                    <td>{{$ad->fuel_type}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ _lang('app.mileage')}}</td>
                                                                    <td>{{$ad->mileage}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ _lang('app.mileage_unit')}}</td>
                                                                    <td>{{$ad->mileage_unit}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ _lang('app.status')}}</td>
                                                                    <td>{{$ad->status}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{{ _lang('app.manufacturing_year')}}</td>
                                                                    <td>{{$ad->manufacturing_year}}</td>
                                                                </tr>
                                                                
                                                            @endif

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                          @endif
                           










                        </div>


                        <script>
                            var new_lang = {};
                        </script>
                        @endsection
