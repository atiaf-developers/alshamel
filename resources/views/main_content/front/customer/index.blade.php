@extends('layouts.front')

@section('pageTitle',_lang('app.my_profile'))


@section('js')
<script src=" {{ url('public/front/scripts') }}/contact.js"></script>
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
                            <table>
                                <tr>
                                    <td>{{_lang('app.name')}}</td>
                                    <td>{{$User->name}}</td>
                                </tr>
                                <tr>
                                    <td>{{_lang('app.username')}}</td>
                                    <td>{{$User->username}}</td>
                                </tr>
                                <tr>
                                    <td>{{_lang('app.email')}}</td>
                                    <td>{{$User->email}}</td>
                                </tr>
                                <tr>
                                    <td>{{_lang('app.mobile')}}</td>
                                    <td>{{$User->mobile}}</td>
                                </tr>
                                <tr>
                                    <td>{{_lang('app.password')}}</td>
                                    <td>*******</td>
                                </tr>

                            </table>
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