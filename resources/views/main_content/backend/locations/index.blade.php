@extends('layouts.backend')

@section('pageTitle', _lang('app.locations'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>

@if($path)
<li><a href="{{url('admin/locations')}}">{{_lang('app.locations')}}</a> <i class="fa fa-circle"></i></li>
{!!$path!!}
@else
<li><span> {{_lang('app.locations')}}</span></li>
@endif


@endsection

@section('js')
<script src="{{url('public/backend/js')}}/locations.js" type="text/javascript"></script>
@endsection
@section('content')

 {{ csrf_field() }}


<div class = "panel panel-default">

    <div class = "panel-body">


        <div class="table-toolbar">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <a class="btn green" style="margin-bottom: 40px;" href = "{{ route('locations.create') }}{{ $parent_id!=0?'?parent='.$parent_id:'' }}" onclick="">{{ _lang('app.add_new')}}<i class="fa fa-plus"></i> </a>
                    </div>
                </div>
            </div>
        </div>

        <table class = "table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
            <thead>
                <tr>
                    <th>{{_lang('app.title')}}</th>
                    <th>{{_lang('app.this_order')}}</th>
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
    parent_id: "{{$parent_id}}"
};
</script>
@endsection