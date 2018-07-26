@extends('layouts.backend')

@section('pageTitle', _lang('app.ads'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.ads')}}</span></li>


@endsection

@section('js')
<script src="{{url('public/backend/js')}}/ads.js" type="text/javascript"></script>
@endsection
@section('content')


<div class = "panel panel-default">
{{ csrf_field() }}
    <div class = "panel-body">


        <div class="table-toolbar">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                    </div>
                </div>
            </div>
        </div>

        <table class = "table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
            <thead>
                <tr>
                   <th>{{_lang('app.title')}}</th>
                        <th>{{ _lang('app.email') }}</th>
                        <th>{{_lang('app.mobile')}}</th>
                        <th>{{_lang('app.special')}}</th>
                        <th>{{_lang('app.status')}}</th>
                        <th>{{_lang('app.options')}}</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <!--Table Wrapper Finish-->
    </div>
</div>
<script>
var new_lang = {

};
var new_config = {
    parent_id:
};
</script>
@endsection
