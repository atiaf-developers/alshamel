@extends('layouts.backend')

@section('pageTitle', _lang('app.users'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.users')}}</span></li>

@endsection
@section('js')

<script src="{{url('public/backend/js')}}/users.js" type="text/javascript"></script>
@endsection
@section('content')
{{ csrf_field() }}
<div class = "panel panel-default">
    <div class = "panel-body">
        <div class="clearfix"></div>

        <div id="" class="table-container">
            
            <table class = "table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
                <thead>
                    <tr>
                        
                        <th>{{_lang('app.username')}}</th>
                        <th>{{ _lang('app.name') }}</th>
                        <th>{{_lang('app.image')}}</th>
                        <th>{{_lang('app.mobile')}}</th>
                        <th>{{_lang('app.status')}}</th>
                        <th>{{_lang('app.options')}}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    
        </div> 


        <!--Table Wrapper Finish-->
    </div>
</div>
<script>
    var new_lang = {
        
    };
</script>
@endsection
