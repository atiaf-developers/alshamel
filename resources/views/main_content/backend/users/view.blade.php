@extends('layouts.backend')

@section('pageTitle', _lang('app.users'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/users')}}">{{_lang('app.users')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.view')}}</span></li>

@endsection
@section('js')
<script src="{{url('public/backend/js')}}/users.js" type="text/javascript"></script>
@endsection
@section('content')


<div class="row">
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-12">

                <!-- BEGIN SAMPLE TABLE PORTLET-->
                <div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cogs"></i>{{ _lang('app.basic_info')}}
                        </div>
                      
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            <table class="table table-hover">

                                <tbody>
                                    <tr>
                                        <td>{{ _lang('app.name')}}</td>
                                        <td>{{$user->name}}</td>

                                    </tr>
                                    
                                    <tr>
                                            <td>{{ _lang('app.username')}}</td>
                                            <td>{{$user->username}}</td>
    
                                        </tr>
                                    <tr>
                                        <td>{{ _lang('app.mobile')}}</td>
                                        <td>{{$user->mobile}}</td>

                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.email')}}</td>
                                        <td>{{$user->email}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _lang('app.image')}}</td>
                                        <td><img style="width: 100px;height: 100px;" alt="" src="{{url('public/uploads/users')}}/{{$user->image}}"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                 
                    </div>
                </div>
                <!-- END SAMPLE TABLE PORTLET-->


            </div>
        </div>
      
    </div>
    <div class="row">










    </div>


</div>


<script>
var new_lang = {

};

</script>
@endsection
