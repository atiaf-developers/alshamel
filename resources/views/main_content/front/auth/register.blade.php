@extends('layouts.front')

@section('pageTitle',_lang('app.register'))

@section('js')
<script src=" {{ url('public/front/scripts') }}/login.js"></script>
@endsection

@section('content')

<div class="page-head_agile_info_w3l">
    <div class="container">
        <div class="services-breadcrumb">
            <div class="agile_inner_breadcrumb">
                <ul class="w3_short">
                    <li><a href="index.php">الرئيسية</a><i>|</i></li>
                    <li>تسجيل دخول</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="login"> 
    <div class="container">
        <div id="loginbox" class="col-md-8 col-md-offset-2">                    
            <form id="regForm">
                {{ csrf_field() }}
                <div class="panel-info">
                    <div class="panel-heading">
                        <div class="panel-title"><span><i class="fa fa-sign-in"></i></span>
                            {{_lang('app.register')}}
                        </div>
                    </div>
                    <div class="panel-padding">
                        <div class="tab form-group">
                            <h3>{{ _lang('app.enter_your_mobile_number_to_create_a_new_account') }}</h3>
                            <div class="tab-details">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="row">
                                                <select name="dial_code" class="country form-control">
                                                    <option value="966">Saudi Arabia (+966)</option>        
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="mobile" id="mobile">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab">
                            <div class="alert alert-success" style="display:none; " role="alert"><i class="fa fa-check" aria-hidden="true"></i> <span class="message"></span></div>
                            <div class="row form-w3agile">
                                 <h3 class="h3-dir">{{ _lang('app.you_will_receive_a_text_message_with_activation_code_on_your_mobile_number') }} <span id="mobile-message"></span> <a href="#" class="change-num" onclick="Login.nextPrev(this, -1)">{{ _lang('app.change_number') }}</a></h3>
                               
                                <div class="form-group col-sm-3 inputbox">
                                    <input type="text" class="form-control text-center" name="code[0]" placeholder="0">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-3 inputbox">
                                    <input type="text" class="form-control text-center" name="code[1]" placeholder="0">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-3 inputbox">
                                    <input type="text" class="form-control text-center" name="code[2]" placeholder="0">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group col-sm-3 inputbox">
                                    <input type="text" class="form-control text-center" name="code[3]" placeholder="0">
                                    <span class="help-block"></span>
                                </div>
                                <div class="msg-error" style="display: none;">
                                    <span id="activation-code-message" ></span>
                                </div>
                                <a class="a-signin" href="#" onclick="Login.resend_code(this);return false;"><strong>{{ _lang('app.send_the_code_again') }}</strong></a>
                            </div>
                        </div>
                        <div class="tab dir form-group">
                            <h3>{{ _lang('app.complete_the_data') }}</h3>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="row">
                                        <input type="text" class="form-control" name="name" placeholder="{{ _lang('app.name') }}">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="row">
                                        <input type="text" class="form-control" name="username" placeholder="{{ _lang('app.username') }}">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="row">
                                        <input type="text" class="form-control" name="email" placeholder="{{ _lang('app.email') }}">
                                        <span class="help-block"></span>
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="row">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="{{ _lang('app.password') }}">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="row">
                                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="{{ _lang('app.confirm_password') }}">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                            </div>


                        </div>
                         <div class="tab">
                            <div class="alert alert-success" style="display:{{Session('successMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-check" aria-hidden="true"></i> <span class="message">{{Session::get('successMessage')}}</span></div>
                            <div class="alert alert-danger" style="display:{{Session('errorMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span class="message">{{Session::get('errorMessage')}}</span></div>
                        </div>
                        <div class="next2">
                            <button type="button" id="nextBtn" data-type="next" onclick="Login.nextPrev(this, 1)">{{_lang('app.next')}}</button>
                            <button type="button" id="prevBtn" data-type="prev" onclick="Login.nextPrev(this, -1)">{{_lang('app.prev')}}</button>
                        </div>
                        <!-- Circles which indicates the steps of the form: -->
                        <div class="steps">
                            <span class="step"></span>
                            <span class="step"></span>
                            <span class="step"></span>
                            <span class="step"></span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

</div>





@endsection