var Settings = function() {

    var init = function() {
        handleSubmit();
        My.readImageMulti('about_image');
    };

    var handleSubmit = function() {

        $('#editSettingsForm').validate({
            ignore: "",
            rules: {
                'setting[num_free_ads]': {
                    required: true
                },
                'setting[phone]': {
                    required: true
                },
                'setting[email]': {
                    required: true
                },
                'setting[android_url]': {
                    required: true
                },
                'setting[ios_url]': {
                    required: true
                },
                'setting[manufacturing_year_start]': {
                    required: true
                },
                'setting[rooms_range][from]': {
                    required: true
                },
                'setting[rooms_range][to]': {
                    required: true
                },
                'setting[baths_range][from]': {
                    required: true
                },
                'setting[baths_range][to]': {
                    required: true
                },
                'setting[social_media][facebook]': {
                    required: true
                },
                'setting[social_media][twitter]': {
                    required: true
                },
                'setting[social_media][google]': {
                    required: true
                },
                'setting[social_media][youtube]': {
                    required: true
                },

            },
            messages: lang.messages,
            highlight: function(element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('').css('opacity', 0);
            },
            errorPlacement: function(error, element) {
                $(element).closest('.form-group').find('.help-block').html($(error).html()).css('opacity', 1);
            }
        });
        var langs = JSON.parse(config.languages);
        for (var x = 0; x < langs.length; x++) {
            var about_us = "textarea[name='about_us[" + langs[x] + "]']";
            var usage_conditions = "textarea[name='policy[" + langs[x] + "]']";
            var description = "textarea[name='description[" + langs[x] + "]']";
            var key_words = "textarea[name='key_words[" + langs[x] + "]']";
            $(key_words).rules('add', {
                required: true
            });
            $(description).rules('add', {
                required: true
            });
            $(about_us).rules('add', {
                required: true
            });
            $(usage_conditions).rules('add', {
                required: true
            });
        }
        $('#editSettingsForm .submit-form').click(function() {
            if ($('#editSettingsForm').validate().form()) {
                $('#editSettingsForm .submit-form').prop('disabled', true);
                $('#editSettingsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function() {
                    $('#editSettingsForm').submit();
                }, 500);
            }
            return false;
        });
        $('#editSettingsForm input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#editSettingsForm').validate().form()) {
                    $('#editSettingsForm .submit-form').prop('disabled', true);
                    $('#editSettingsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function() {
                        $('#editSettingsForm').submit();
                    }, 500);
                }
                return false;
            }
        });



        $('#editSettingsForm').submit(function() {
            var id = $('#id').val();
            var action = config.admin_url + '/settings';
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#editSettingsForm .submit-form').prop('disabled', false);
                    $('#editSettingsForm .submit-form').html(lang.save);

                    if (data.type == 'success') {

                        toastr.options = {
                            "debug": false,
                            "positionClass": "toast-bottom-left",
                            "onclick": null,
                            "fadeIn": 300,
                            "fadeOut": 1000,
                            "timeOut": 5000,
                            "extendedTimeOut": 1000
                        };
                        toastr.success(lang.updated_successfully, 'رسالة');

                    } else {
                        console.log(data)
                        if (typeof data.errors === 'object') {
                            for (i in data.errors) {
                                var message = data.errors[i];
                                var key_arr = i.split('.');
                                var name = '';
                                for (var x = 0; x < key_arr.length; x++) {
                                    if (x == 0) {
                                        name += key_arr[x];
                                    } else {
                                        name += '[' + key_arr[x] + ']';
                                    }
                                }
                                i = name;


                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(message).css('opacity', 1);
                            }
                        }
                        if (typeof data.message !== 'undefined') {
                            $.confirm({
                                title: lang.error,
                                content: data.message,
                                type: 'red',
                                typeAnimated: true,
                                buttons: {
                                    tryAgain: {
                                        text: lang.try_again,
                                        btnClass: 'btn-red',
                                        action: function() {}
                                    }
                                }
                            });
                        }
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    $('#editSettingsForm .submit-form').prop('disabled', false);
                    $('#editSettingsForm .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });

            return false;

        })




    }

    return {
        init: function() {
            init();
        }
    };

}();
jQuery(document).ready(function() {
    Settings.init();
});