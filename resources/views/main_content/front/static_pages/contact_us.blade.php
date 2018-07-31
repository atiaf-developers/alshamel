@extends('layouts.front')

@section('pageTitle',_lang('app.contact_us'))

@section('js')

<script src=" {{ url('public/front/scripts') }}/contact.js"></script>
@endsection

@section('content')
<div class="content">
        
        <div class="page-head_agile_info_w3l">
        <div class="container">
                    <div class="services-breadcrumb">
                       <div class="agile_inner_breadcrumb">

                          <ul class="w3_short">
                                       <li><a href="{{ _url('/') }}">{{ _lang('app.home') }}</a><i>|</i></li>
                                       <li>{{ _lang('app.contact_us') }}</li>
                               </ul>
                        </div>
                   </div>
                </div>
         </div>
        
       <div class="banner_bottom_agile_info">
            <div class="container">
               <div class="agile-contact-grids">
                        <div class="agile-contact-left">
                                <div class="col-md-6 address-grid">
                                    <div class="mail-agileits-w3layouts">
                                            <i class="fa fa-volume-control-phone" aria-hidden="true"></i>
                                            <div class="contact-right">
                                                    <p>{{ _lang('app.phone') }} </p>
                                                    @foreach ($settings['phone'] as $phone)
                                                       <span>{{ $phone }}</span>
                                                    @endforeach
                                                    
                                                    
                                            </div>
                                            <div class="clearfix"> </div>
                                    </div>
                                    <div class="mail-agileits-w3layouts">
                                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                            <div class="contact-right">
                                                    <p>{{ _lang('app.email') }}</p>
                                                    @foreach ($settings['email'] as $email)
                                                       <a href="mailto:{{ $email }}">{{ $email }}</a>
                                                    @endforeach
                                            </div>
                                            <div class="clearfix"> </div>
                                    </div>
                                </div>
                                <div class="col-md-6 contact-form">
                                        <h4 class="purple-w3ls">راسلنا</h4>
                                        <form role="form" method="post" id="contacts-form">
                                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                                <label class="control-label" for="name">{{ _lang('app.name') }}</label>
                                                <input type="text" class="form-control" name="name" />
                                            </div>      
                                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                                <label class="control-label">{{ _lang('app.email') }}</label>
                                                <input type="text" class="form-control" name="email" />
                                            </div>
                                            <div class="form-group {{ $errors->has('type') ? ' has-error' : '' }}">
                                                <label class="control-label">{{ _lang('app.subject') }}</label>
                                                <select class="form-control" name="type">
                                                   @foreach ($types as $key => $type)
                                                       <option value="{{ $key }}">{{ _lang('app.'.$type) }}</option>
                                                       
                                                   @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group {{ $errors->has('message') ? ' has-error' : '' }}">
                                                <label class="control-label" for="Mensaje">تفاصيل الرسالة</label>
                                                <textarea rows="5" cols="30" class="form-control" name="message"></textarea>
                                            </div>
                                            <div class="form-group">                
                                                <input type="submit" class="submit-form btn btn-primary bg" value="ارسال">
                                                <input type="reset" class="btn btn-default" value="مسح">                
                                            </div>
                                        </form>
                                </div>
                        </div>
                        <div class="clearfix"> </div>
                </div>
           </div>
    </div>
</div>

@endsection