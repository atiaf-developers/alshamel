@extends('layouts.backend')

@section('pageTitle',_lang('app.settings'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.settings')}}</span></li>

@endsection

@section('js')

<script src="{{url('public/backend/js')}}/settings.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="editSettingsForm"  enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="panel panel-default" id="editSiteSettings">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">

                <div class="form-group form-md-line-input col-md-6">
                    <input type="number" class="form-control" id="search_range_for_stores" name="setting[num_free_ads]" value="{{ isset($settings['num_free_ads']) ? $settings['num_free_ads']->value : '' }}">
                    <label for="phone">{{_lang('app.num_of_free_ads') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="clearfix"></div>

            </div>
            <!--Table Wrapper Finish-->
        </div>

    </div>
    {{--  <div class="panel panel-default">

        <div class="panel-body">

            <div class="form-body">  --}}

                @foreach ($languages as $key => $value)
                <div class="panel panel-default">

                    <div class="panel-body">

                        <div class="form-body">
                            <div class="col-md-12">


                                <div class="form-group form-md-line-input col-md-6">
                                    <textarea class="form-control" id="about_us[{{ $key }}]" name="about_us[{{ $key }}]"  cols="30" rows="10">{{isset($settings_translations[$key])?$settings_translations[$key]->about_us:''}}</textarea>
                                    <label for="about_us">{{_lang('app.about_us') }} {{ _lang('app.'.$value.'') }}</label>
                                    <span class="help-block"></span>
                                </div>

                                <div class="form-group form-md-line-input col-md-6">
                                    <textarea class="form-control" id="policy[{{ $key }}]" name="policy[{{ $key }}]"  cols="30" rows="10">{{isset($settings_translations[$key])?$settings_translations[$key]->policy:''}}</textarea>
                                    <label for="policy">{{_lang('app.policy') }} {{ _lang('app.'.$value.'') }}</label>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>




                        <!--Table Wrapper Finish-->
                    </div>

                </div>
                @endforeach
                <div class="panel panel-default">
                        <div class="panel-body">
                                <div class="col-md-12">
                                    <div class="form-group form-md-line-input col-md-6">
                                        <input type="text" class="form-control" id="setting[social_media][facebook]" name="setting[social_media][facebook]" value="{{ isset($settings['social_media']->facebook) ? $settings['social_media']->facebook :'' }}">
                                        <label for="setting[social_media][facebook]">{{_lang('app.facebock') }}</label>
                                        <span class="help-block"></span>
                                    </div>
                                    <div class="form-group form-md-line-input col-md-6">
                                            <input type="text" class="form-control" id="setting[social_media][twitter]" name="setting[social_media][twitter]" value="{{ isset($settings['social_media']->twitter) ? $settings['social_media']->twitter :'' }}">
                                            <label for="setting[social_media][twitter]">{{_lang('app.twitter') }}</label>
                                            <span class="help-block"></span>
                                        </div>
                                    <div class="form-group form-md-line-input col-md-6">
                                        <input type="text" class="form-control" id="setting[social_media][google]" name="setting[social_media][google]" value="{{ isset($settings['social_media']->google) ?$settings['social_media']->google :'' }}">
                                        <label for="setting[sochiel][google]">{{_lang('app.google') }}</label>
                                        <span class="help-block"></span>
                                    </div>
                                    <div class="form-group form-md-line-input col-md-6">
                                        <input type="text" class="form-control" id="setting[social_media][youtube]" name="setting[social_media][youtube]" value="{{ isset($settings['social_media']->youtube) ? $settings['social_media']->youtube :'' }}">
                                        <label for="setting[sochiel][youtupe]">{{_lang('app.youtupe') }}</label>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                        </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-footer text-center">
                        <button type="button" class="btn btn-info submit-form"
                        >{{_lang('app.save') }}</button>
                    </div>

                </div>


            </form>
            @endsection