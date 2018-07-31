@extends('layouts.front')

@section('pageTitle',_lang('app.categories'))

@section('js')

@endsection

@section('content')


<div class="page-head_agile_info_w3l">
    <div class="container">
        <div class="services-breadcrumb">
            <div class="agile_inner_breadcrumb">

                <ul class="w3_short">
                    <li>
                        <a href="{{_url('')}}">{{_lang('app.home')}}</a>
                        @if(!empty($cat->path_arr))
                        <i>|</i>
                        @endif
                    </li>
                    @foreach($cat->path_arr as $one)
                    @if(!$loop->last)
                    <li>
                        <a href="{{$one->url}}">{{$one->title}}</a>
                        <i>|</i>
                    </li>
                    @else
                     <li>{{$one->title}}</li>
                    @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="banner-bootom-w3-agileits main-dep">
    <div class="container">
        <div class="agile_ab_w3ls_info">
            @foreach($cats as $one)
            <div class="col-md-4 bb-grids bb-middle-agileits-w3layouts">
                <a href="{{$one->url}}">
                    <div class="bb-middle-agileits-w3layouts grid">
                        <figure class="effect-roxy">
                            <img src="{{$one->image}}" alt=" " class="img-responsive" />
                            <figcaption>
                                <h3>{{$one->title}}</h3>
                                <p>{{_lang('app.view_products')}}</p>
                            </figcaption>			
                        </figure>
                    </div>
                </a>
            </div>
            @endforeach
           
            <div class="clearfix"></div>
        </div>
    </div> 
</div>








@endsection

