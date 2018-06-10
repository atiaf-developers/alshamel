@extends('layouts.backend')

@section('pageTitle', _lang('app.users'))

@section('js')

<script src="{{url('public/backend/js')}}/ads.js" type="text/javascript"></script>
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
        </div>
    
        </div> 


        <!--Table Wrapper Finish-->
    </div>
</div>
<script>
    var new_lang = {
        special: "{{ _lang('app.special')}}",
        not_special: "{{ _lang('app.not_special')}}",
    };
</script>
@endsection
