var Bathes_grid;

var Bathes = function () {

    var init = function () {
        $.extend(lang, new_lang);
        $.extend(config, new_config);
        handleRecords();
        handleSubmit();
    };


    var handleRecords = function () {
        Bathes_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/bathes/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "title","name":"bathes_translations.title"},
                {"data": "active","name":"bathes.active", orderable: false, searchable: false},
                {"data": "this_order","name":"bathes.this_order"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [2, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {
        $('#addEditBathesForm').validate({
            rules: {
                 this_order: {
                     required: true,
                 },
                 active:{
                     required : true,
                 },

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
        
        
       var langs = JSON.parse(config.languages);
        for (var x = 0; x < langs.length; x++) {
             var title = "input[name='title[" + langs[x] + "]']";
             $(title).rules('add', {
                 required: true
             });
         }

        $('#addEditBathesForm .submit-form').click(function () {

            if ($('#addEditBathesForm').validate().form()) {
                $('#addEditBathesForm .submit-form').prop('disabled', true);
                $('#addEditBathesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditBathesForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditBathesForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditBathesForm').validate().form()) {
                    $('#addEditBathesForm .submit-form').prop('disabled', true);
                    $('#addEditBathesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditBathesForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditBathesForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/bathes';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/bathes/' + id;
            }
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditBathesForm .submit-form').prop('disabled', false);
                    $('#addEditBathesForm .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        if (id == 0) {
                            Bathes.empty();
                        }


                    } else {
                        if (typeof data.errors !== 'undefined') {
                            for (i in data.errors)
                            {
                                var message=data.errors[i];
                                 if (i.startsWith('title')) {
                                    var key_arr = i.split('.');
                                    var key_text = key_arr[0] + '[' + key_arr[1] + ']';
                                    i = key_text;
                                }
                                 
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(message).css('opacity', 1);
                            }
                        } 
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addEditBathesForm .submit-form').prop('disabled', false);
                    $('#addEditBathesForm .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });


            return false;

        })




    }

    return {
        init: function () {
            init();
        },
       
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/bathes/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    Bathes_grid.api().ajax.reload();
                }
            });

        },
        add: function () {
            Bathes.empty();
            if (parent_id > 0) {
                $('.for-country').hide();
                $('.for-city').show();
            } else {
                $('.for-country').show();
                $('.for-city').hide();
            }

            My.setModalTitle('#addEditBathesForm', lang.add_location);
            $('#addEditBathesForm').modal('show');
        },

        error_message: function (message) {
            $.alert({
                title: lang.error,
                content: message,
                type: 'red',
                typeAnimated: true,
                buttons: {
                    tryAgain: {
                        text: lang.try_again,
                        btnClass: 'btn-red',
                        action: function () {
                        }
                    }
                }
            });
        },
        empty: function () {
            $('#id').val(0);
            $('#category_icon').val('');
            $('#active').find('option').eq(0).prop('selected', true);
            $('input[type="checkbox"]').prop('checked', false);
            $('.image_box').html('<img src="' + config.url + '/no-image.png" class="image" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Bathes.init();
});

