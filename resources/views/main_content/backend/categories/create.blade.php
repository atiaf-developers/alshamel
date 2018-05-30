@extends('layouts.backend')

@section('pageTitle',_lang('app.add_category'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
@if($path)
<li><a href="{{route('categories.index')}}">{{_lang('app.categories')}}</a> <i class="fa fa-circle"></i></li>
{!!$path!!}
<li><span> {{_lang('app.create')}}</span></li>
@else
<li><span> {{_lang('app.categories')}}</span></li>
@endif
@endsection

@section('js')
<script src="{{url('public/backend/js')}}/categories.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditCategoriesForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.category_title') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="0">

                @foreach ($languages as $key => $value)

                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="">
                    <label for="title">{{_lang('app.title') }} {{ _lang('app. '.$key.'') }}</label>
                    <span class="help-block"></span>
                </div>

                @endforeach


            </div>
        </div>


    </div>

    @if ($parent_id == 0)
    <div class="panel panel-default" id="description">
            <div class="panel-heading">
                <h3 class="panel-title">{{_lang('app.Number_of_levels') }}</h3>
            </div>
            <div class="panel-body">
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="no_of_levels" name="no_of_levels">
                        <option value="2">{{ _lang('app.Two_level') }}</option>
                        <option value="3">{{ _lang('app.Three_level') }}</option>
                    </select>
                    <label for="no_of_levels">{{_lang('app.Number_of_levels') }}</label>
                    <span class="help-block"></span>
                </div>
            </div>
    </div>
 @endif
 @if($no_of_levels)
 @if($level==$no_of_levels)
 <div class="panel panel-default" id="description">
    <div class="panel-heading">
        <h3 class="panel-title">{{_lang('app.form_type') }}</h3>
    </div>
    <div class="panel-body">
        <div class="form-group form-md-line-input col-md-3">
            <select class="form-control edited" id="form_type" name="form_type" required>
                <option value="">{{ _lang('app.Select') }}</option>
                @foreach($form_type as $key=>$value)
                  <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            <label for="form_type">{{_lang('app.form_type') }}</label>
            <span class="help-block"></span>
        </div>
    </div>
</div>
 @endif
 @endif
     


    <div class="panel panel-default">
       <div class="panel-heading">
                <h3 class="panel-title"></h3>
            </div>
        <div class="panel-body">

         
            <div class="form-body">
                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="active" name="active">
                        <option  value="1">{{ _lang('app.active') }}</option>
                        <option  value="0">{{ _lang('app.not_active') }}</option>
                    </select>
                     <label for="status">{{_lang('app.status') }}</label>
                    <span class="help-block"></span>
                </div> 

                <div class="clearfix"></div>
                <div class="form-group col-md-6">
                    <label class="control-label">{{_lang('app.image')}}</label>

                    <div class="image_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="image" />
                    </div>
                    <input type="file" name="image" id="image" style="display:none;">     
                    <span class="help-block"></span>             
                </div>

            </div>
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