@extends('layouts.front')

@section('pageTitle',_lang('app.login'))

@section('js')
<script src=" {{ url('public/front/scripts') }}/login.js"></script>
@endsection

@section('content')

<div class="page-head_agile_info_w3l">
    <div class="container">
        <div class="services-breadcrumb">
            <div class="agile_inner_breadcrumb">

                <ul class="w3_short">
                    <li><a href="{{_url('')}}">{{_lang('app.home')}}</a><i>|</i></li>
                    <li>{{_lang('app.login')}}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="login"> 
    <div class="container">
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <div class="panel-title"><span><i class="fa fa-sign-in"></i></span>
                        {{_lang('app.login')}}
                    </div>
                </div>     

                <div class="panel-body" >

                    <div class="alert alert-success" style="display:{{Session('successMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-check" aria-hidden="true"></i> <span class="message">{{Session::get('successMessage')}}</span></div>
                    <div class="alert alert-danger" style="display:{{Session('errorMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span class="message">{{Session::get('errorMessage')}}</span></div>
                    <form  id = "login-form" class="form-horizontal" role="form">
                        {{ csrf_field() }}

                        <div class="form-group input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input id="login-username" type="text" class="form-control" name="username" value="" placeholder="{{_lang('app.username')}}">
                            <span class="help-block"></span>
                        </div>


                        <div class="form-group input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input id="login-password" type="password" class="form-control" name="password" placeholder="{{_lang('app.password')}}">
                            <span class="help-block"></span>
                        </div>

                        <!--<div class="forget"><a href="forget.php">نسيت كلمة المرور؟</a></div>-->

                        <div class="form-group">

                            <div class="col-sm-12 controls">
                                <a id="btn-login" href="#" class="submit-form btn bg">{{_lang('app.sign_in')}}  </a>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 control">
                                <div class="signin" >
                                    <a href="{{_url('register')}}">{{_lang('app.register')}}</a>
                                </div>
                            </div>
                        </div>    
                    </form>  




                </div>                     
            </div>  
        </div>

    </div>
</div>

@endsection