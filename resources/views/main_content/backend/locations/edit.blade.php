@extends('layouts.backend')

@section('pageTitle',_lang('app.edit_location'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
@if($path)
<li><a href="{{url('admin/locations')}}">{{_lang('app.locations')}}</a> <i class="fa fa-circle"></i></li>
{!!$path!!}
<li><span> {{_lang('app.edit')}}</span></li>
@else
<li><span> {{_lang('app.locations')}}</span></li>
@endif
@endsection


@section('js')
<script src="{{url('public/backend/js')}}/locations.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditLocationsForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="{{ $location->id }}">

                @foreach ($languages as $key => $value)

                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="{{ $translations["$key"]->title }}">
                    <label for="title">{{_lang('app.title') }} {{ _lang('app. '.$key.'') }}</label>
                    <span class="help-block"></span>
                </div>

                @endforeach
                <br>

            </div>
        </div>


    </div>


    <div class="panel panel-default">

        <div class="panel-body">


            <div class="form-body">
                <div class="form-group form-md-line-input col-md-6">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="{{ $location->this_order }}">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>
                @if($parent_id==0)
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="code" name="code" value="{{ $location->code }}">
                    <label for="code">{{_lang('app.code') }}</label>
                    <span class="help-block"></span>
                </div>
                @endif
            </div>
        </div>


    </div>


    <div class="panel panel-default">
       
        <div class="panel-body">
                @if($parent_id==0)
            <div class="form-body">
                    <div class="form-group form-md-line-input col-md-6">
                            <select class="form-control edited" id="currency_id" name="currency_id">
                                @foreach($currency as $value)  
                                <option value="{{ $value->id }}" {{ $value->id == $location->currency_id ? 'selected' : '' }}>{{ $value->title }}</option>
                                @endforeach
                            </select>
                             <label for="currancy">{{_lang('app.currancy') }}</label>
                            <span class="help-block"></span>
                        </div>
                <div class="form-group col-md-6">
                    <label class="control-label">{{_lang('app.image')}}</label>

                    <div class="location_image_box">
                        <img src="{{url('public/uploads/locations').'/'.$location->image}}" width="100" height="80" class="location_image" />
                    </div>
                    <input type="file" name="location_image" id="location_image" style="display:none;">     
                    <span class="help-block"></span>             
                </div>
                

            </div>
            @endif
        </div>

        <div class="panel-footer text-center">
            <button type="button" class="btn btn-info submit-form"
                    >{{_lang('app.save') }}</button>
        </div>


    </div>


</form>
<script>
var new_lang = {

};
var new_config = {
    parent_id: "{{$parent_id}}"
};

</script>
@endsection