
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

<div class="profile">
    <div class="container">
        <div class="col-md-12">
            <div class="row">
                <h3 class="title">صفحتى</h3>
                <div class="profile-area">
                    <div class="col-md-12">
                        <div class="col-md-9">

                            <div class="row">
                                <form id="loginform" class="editProfileForm form-horizontal" role="form">
                                     {{ csrf_field() }}
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">تعديل الاسم</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <input type="text" name="name" class="form-control" value="{{$User->name}}">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="help-block"></span>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">تعديل اسم المستخدم</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <input type="text" name="username" class="form-control" value="{{$User->username}}">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="help-block"></span>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">تعديل البريد الالكترونى</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <input type="text" name="email" class="form-control" value="{{$User->email}}">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="help-block"></span>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">رقم الجوال</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <input type="text" name="mobile" class="form-control" value="{{$User->mobile}}">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="help-block"></span>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">تعديل كلمة المرور</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <input type="password" name="password" id="password" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="help-block"></span>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">اعادة كلمة المرور الجديدة</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <input type="password" name="confirm_password" id="confirm_passowrd" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="help-block"></span>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-sm-3 col-form-label">تغيير الصورة الشخصية</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <input type="file"  name="image">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="help-block"></span>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-12 controls">
                                            <a id="btn-login" href="#" class="btn bg submit-form">حفظ </a>
                                        </div>
                                    </div>

                                </form>
                            </div>
                            <div class="alert alert-success" style="display:{{Session('successMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-check" aria-hidden="true"></i> <span class="message">{{Session::get('successMessage')}}</span></div>
                            <div class="alert alert-danger" style="display:{{Session('errorMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span class="message">{{Session::get('errorMessage')}}</span></div>

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
