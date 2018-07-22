@extends('layouts.backend')

@section('pageTitle',  _lang('app.contact_messages'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.contact_messages')}}</span></li>
@endsection

@section('js')
<script src="{{url('public/backend/js')}}/contact_messages.js" type="text/javascript"></script>
@endsection
@section('content')
{{ csrf_field() }}
<div class="modal fade" id="reply" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="replyLabel">{{_lang('app.reply')}}</h4>
            </div>

            <div class="modal-body">
                <form role="form"  id="replyForm"  enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div>
                        <h3>{{_lang('app.message')}}</h3>
                        <p class="message">

                        </p>
                    </div>
                    <div class="form-group form-md-line-input">
                        <textarea class="form-control" id="reply" name="reply"  cols="30" rows="10"></textarea>
                        <label for="reply">{{_lang('app.reply') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="clearfix"></div>
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-info submit-form"
                        >{{_lang("app.send")}}</button>
                <button type="button" class="btn btn-white"
                        data-dismiss="modal"> {{ _lang("app.close") }} </button>
            </div>
        </div>
    </div>
</div>   
<div class="modal fade" id="viewMessage" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="viewMessageLabel">{{_lang('app.message')}}</h4>
            </div>

            <div class="modal-body">
                <form role="form"  id="replyForm"  enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div>
                        <h3>{{_lang('app.message')}}</h3>
                        <p class="message">

                        </p>
                    </div>
                    <div>
                        <h3>{{_lang('app.reply')}}</h3>
                        <p class="reply">

                        </p>
                    </div>
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-white"
                        data-dismiss="modal"> {{ _lang("app.close") }} </button>
            </div>
        </div>
    </div>
</div>   
<div class="panel panel-default">

    <div class="panel-body">
        <div class="row">
            <div class="col-md-2" style="margin-bottom: 10px;">

                <button type="button" class="btn btn-sm btn-default btn-delete" disabled onclick="Contact_messages.delete(this);
    return false;">{{_lang('app.delete')}}</button>


            </div>
        </div>
        <!--Table Wrapper Start-->

        <table class="table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
            <thead>
                <tr>
                    <th>
                        <div class="md-checkbox col-md-4" style="margin-left:40%;">
                            <input type="checkbox" id="check-all-messages" class="md-check"  value="">
                            <label for="check-all-messages">
                                <span></span>
                                <span class="check"></span>
                                <span class="box"></span>
                            </label>
                        </div>
                    </th>

                    <th>{{ _lang('app.type')}}</th>
                    <th>{{ _lang('app.name')}}</th>
                    <th>{{ _lang('app.email')}}</th>
                    <th>{{ _lang('app.created_at')}}</th>
                    <th></th>
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
        view : "{{ _lang('app.view') }}"
    };
</script>
@endsection
