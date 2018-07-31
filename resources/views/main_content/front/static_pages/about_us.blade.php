@extends('layouts.front')

@section('pageTitle',_lang('app.about_us'))

@section('js')
	
@endsection

@section('content')
    <div class="content">
        
        <div class="page-head_agile_info_w3l">
        <div class="container">
                    <div class="services-breadcrumb">
                       <div class="agile_inner_breadcrumb">

                          <ul class="w3_short">
                                       <li><a href="{{ _url('/') }}">{{ _lang('app.home') }}</a><i>|</i></li>
                                       <li>{{ _lang('app.about_alshamel') }}</li>
                               </ul>
                        </div>
                   </div>
                </div>
         </div>
        
        <div class="banner_bottom_agile_info">
        <div class="container">
                <div class="agile_ab_w3ls_info">
                    <h3 class="title">{{ _lang('app.about_alshamel') }}</h3>
                     <div class="col-md-8 ab_pic_w3ls_text_info">
                        {{ $settings['info']->about_us }}
                     </div>
                    <div class="col-md-4 ab_pic_w3ls">
                            <img src="{{url('public/uploads/settings/'.$settings['about_image']->value)}}" alt=" " class="img-responsive">
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div> 
        </div>
    </div>
  

@endsection