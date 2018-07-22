var Contact_messages_grid;
var contact_message_id;

var Contact_messages = function () {
    var ids = [];
    var init = function () {
        $.extend(lang, new_lang);
        handleRecords();
        handleCheckAll();
        handleSubmit();


    };
    var handleSubmit = function () {

        $('#replyForm').validate({
            rules: {
                reply: {
                    required: true

                }
            },
            //messages: lang.messages,
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('').css('opacity', 0);

            },
            errorPlacement: function (error, element) {
                $(element).closest('.form-group').find('.help-block').html($(error).html()).css('opacity', 1);
            }
        });
        $('#reply .submit-form').click(function () {
            if ($('#replyForm').validate().form()) {
                $('#reply .submit-form').prop('disabled', true);
                $('#reply .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#replyForm').submit();
                }, 1000);

            }
            return false;
        });
        $('#replyForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#replyForm').validate().form()) {
                    $('#reply .submit-form').prop('disabled', true);
                    $('#reply .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#replyForm').submit();
                    }, 1000);

                }
                return false;
            }
        });



        $('#replyForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/contact_messages/reply';
            var formData = new FormData($(this)[0]);
            formData.append('contact_message_id', contact_message_id);

            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    $('#reply .submit-form').prop('disabled', false);
                    $('#reply .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        $('#reply').modal('hide');
                        Contact_messages_grid.api().ajax.reload();

                    } else {
                        console.log(data)
                        if (typeof data.errors === 'object') {
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('#' + i).closest('.form-group').find(".help-block").html(data.errors[i][0]).css('opacity', 1)
                            }
                        } 
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#reply .submit-form').prop('disabled', false);
                    $('#reply .submit-form').html(lang.send);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });

            return false;

        })




    }
    var handleCheckAll = function () {
        $("#check-all-messages").on('change', function () {
            $('.check-one-message').not(this).prop('checked', this.checked);
            enableOrDisableDeleteBtn();
            getCheckedIds();
        });
        $(document).on('change', '.check-one-message', function () {
            if ($(".check-one-message:checked").length == 0) {
                $('#check-all-messages').prop('checked', false);
            }
            enableOrDisableDeleteBtn();
            getCheckedIds();
        });
    }
    var enableOrDisableDeleteBtn = function () {
        if ($(document).find(".check-one-message:checked").length == 0) {
            $(document).find('.btn-delete').prop('disabled', true);
        } else {
            $(document).find('.btn-delete').prop('disabled', false);
        }
    }
    var getCheckedIds = function () {
        var checked_ids = [];
        $(".check-one-message").each(function () {
            if ($(this).is(':checked')) {
                checked_ids.push($(this).data('id'));
            }
        });
        ids = checked_ids;
    }
    var handleRecords = function () {
        Contact_messages_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/contact_messages/data",
                "type": "POST",
                "data": {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "input", orderable: false},
                {"data": "type"},
                {"data": "name"},
                {"data": "email"},
                {"data": "created_at"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [4, "desc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    return {
        init: function () {
            init();
        },
        delete: function (t) {
            $(t).prop('disabled', true);
            $(t).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
            if (ids.length > 0) {

                setTimeout(function () {


                    My.deleteForm({
                        element: t,
                        url: config.admin_url + '/contact_messages/delete',
                        data: {_method: 'DELETE', ids: ids, _token: $('input[name="_token"]').val()},
                        success: function (data)
                        {
                            $(t).prop('disabled', false);
                            $(t).html(lang.delete);
                            Contact_messages_grid.api().ajax.reload();
                            enableOrDisableDeleteBtn();
                            ids = [];
                        }
                    });
                }, 1000);
            } else {
                $.alert({
                    title: lang.error,
                    content: lang.no_item_selected,
                    type: 'red',
                    typeAnimated: true,
                    buttons: {
                        okay: {
                            text: lang.close,
                            btnClass: 'btn-red',
                            action: function () {
                            }
                        }
                    }
                });
            }


        },
        viewMessage: function (ele) {
            $(ele).prop('disabled', true);
            $(ele).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
            setTimeout(function () {
                $.ajax({
                    url: config.admin_url + "/contact_messages/" + $(ele).data('id'),
                    async: false,
                    success: function (data) {
                        console.log(data);
                        $(ele).prop('disabled', false);
                        $(ele).html(lang.view);
                        if (data.type == 'success')
                        {
                            Contact_messages.empty();
                            contact_message_id=data.message.id;
                            if (data.message.reply == "") {
                                $('#reply').modal('show');
                                $('#reply .message').html(data.message.message);
                            } else {
                                My.setModalTitle('#viewMessage', lang.message);
                                $('#viewMessage').modal('show');
                                $('#viewMessage .message').html(data.message.message);
                                $('#viewMessage .reply').html(data.message.reply);
                            }



                        } else {

                        }
                    },
                    error: function (xhr, textStatus, errorThrown) {

                        My.ajax_error_message(xhr);
                    },
                    dataType: "json",
                    type: "GET"
                });
            }, 1000);


        },
        empty: function () {
            $('#id').val(0)
            $('#active').find('option').eq(0).prop('selected', true);
            $('.image_uploaded').html('<img src="' + config.base_url + 'no-image.jpg" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Contact_messages.init();
});

