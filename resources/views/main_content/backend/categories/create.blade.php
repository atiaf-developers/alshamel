@extends('layouts.backend')

@section('pageTitle',_lang('app.add_category'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
@if($path)
<li><a href="{{route('categories.index')}}">{{_lang('app.categories')}}</a> <i class="fa fa-circle"></i></li>
{!!$path!!}
@else
<li><a href="{{route('categories.index')}}">{{_lang('app.categories')}}</a> <i class="fa fa-circle"></i></li>
@endif
<li><span> {{_lang('app.create')}}</span></li>
@endsection

@section('js')
<script src="{{url('public/backend/js')}}/categories.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditCategoriesForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.title') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="0">

                @foreach ($languages as $key => $value)

                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="">
                    <label for="title">{{ _lang('app.'.$value) }}</label>
                    <span class="help-block"></span>
                </div>

                @endforeach


            </div>
        </div>


    </div>



  




    <div class="panel panel-default">

        <div class="panel-body">


            <div class="form-body">
                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control" id="active" name="active">
                        <option  value="1">{{ _lang('app.active') }}</option>
                        <option  value="0">{{ _lang('app.not_active') }}</option>
                    </select>
                    <label for="status">{{_lang('app.status') }}</label>
                    <span class="help-block"></span>
                </div> 
                
                @if ($level == 1)

                    <div class="form-group form-md-line-input col-md-3">
                        <select class="form-control" id="num_of_levels" name="num_of_levels">
                            <option  value="2">{{ _lang('app.two') }}</option>
                            <option  value="3">{{ _lang('app.three') }}</option>
                        </select>
                        <label for="num_of_levels">{{_lang('app.num_of_levels') }}</label>
                        <span class="help-block"></span>
                    </div> 

                    <div id="label-container" style="display:none">
                        @foreach ($languages as $key => $value)

                            <div class="form-group form-md-line-input col-md-6">
                                <input type="text" class="form-control" id="label[{{ $key }}]" name="label[{{ $key }}]" value="">
                                <label for="label">{{ _lang('app.'.$value) }}</label>
                                <span class="help-block"></span>
                            </div>

                        @endforeach
                    </div>
                   
                @endif
                

                @if($level==2||$level==3)
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control" id="form_type" name="form_type" required>
                        <option value="">{{ _lang('app.choose') }}</option>
                        @foreach($form_types as $key=>$value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <label for="form_type">{{_lang('app.form_type') }}</label>
                    <span class="help-block"></span>
                </div>
                @endif

                <div class="clearfix"></div>
                @if ($level==1||$level==2)
                <div class="form-group col-md-6">
                    <label class="control-label">{{_lang('app.image')}}</label>

                    <div class="image_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="image" />
                    </div>
                    <input type="file" name="image" id="image" style="display:none;">     
                    <span class="help-block"></span>             
                </div>
                @endif
           



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
    parent_id: "{{$parent_id}}",
    level: "{{$level}}",
};

</script>
@endsection