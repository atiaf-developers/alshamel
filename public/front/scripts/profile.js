
var Profile = function () {

    var init = function () {
        handle_edit();
        handleChangeCountry();
        handleChangeCategory();
        handleChangeSubCategory();



    }

    var handleChangeCountry = function (ele, suite) {
        $(document).on('change', 'select[name="ad_country"]', function () {
            var country = $(this).val();
            var html = '<option value="">' + lang.choose + '</option>';
            if (country && country != '') {
                $.get('' + config.ajax_url + '/get-cities/' + country, function (data) {
                    if (data.data.length != 0)
                    {
                        $.each(data.data, function (index, Obj) {
                            html += '<option value="' + Obj.id + '">' + Obj.title + '</option>';
                        });
                    }
                    $('select[name="ad_city"]').html(html);

                }, "json");
            } else {
                $('select[name="ad_city"]').html(html);
            }
        });


    }
    var handleChangeCategory = function (ele, suite) {
        $(document).on('change', 'select[name="main_category"]', function () {
            var main_category = $(this).val();
            var html = '<option value="">' + lang.choose + '</option>';
            if (country && country != '') {
                $.get('' + config.ajax_url + '/get-cats/' + main_category, function (data) {
                    if (data.data.length != 0)
                    {
                        $.each(data.data, function (index, Obj) {
                            html += '<option value="' + Obj.id + '">' + Obj.title + '</option>';
                        });
                    }
                    $('select[name="sub_category"]').html(html);

                }, "json");
            } else {
                $('select[name="sub_category"]').html(html);
            }
        });


    }
      var handleChangeSubCategory = function () {
        $(document).on('change', 'select[name="sub_category"]', function () {
            var sub_category = $(this).val();
            var url = config.ajax_url + '/get-basic-data/' + sub_category;

            $('#loader').show();
            $('.ad-form-content').addClass('loading');
            setTimeout(function () {
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'text',
                    success: function (data)
                    {
                        console.log(data);
                        $('#loader').hide();
                        $('.ad-form-content').removeClass('loading');
                          $('#basic-data-content').html(data);


                    },
                    error: function (xhr, textStatus, errorThrown) {
                       
                        App.ajax_error_message(xhr);
                    },
                });

            }, 1000);




        });
    }
    var handle_edit = function () {
        $("#loginform").validate({
            rules: {
//                name: {
//                    required: true
//                },
//                username: {
//                    required: true
//                },
//                mobile: {
//                    required: true
//                },
//                email: {
//                    email: true
//                },
//                password: {
//                    required: true
//                },
//                confirm_password: {
//                    required: true,
//                    equalTo: "#password"
//                }
            },

            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('');

            },
            errorPlacement: function (error, element) {
                errorElements1.push(element);
                $(element).closest('.form-group').find('.help-block').html($(error).html());
            }

        });
        $('#loginform .submit-form').click(function () {
            var validate_2 = $('#loginform').validate().form();
            errorElements = errorElements1.concat(errorElements2);
            if (validate_2) {
                $('#loginform .submit-form').prop('disabled', true);
                $('#loginform .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#loginform').submit();
                }, 1000);

            }
            if (errorElements.length > 0) {
                App.scrollToTopWhenFormHasError($('#loginform'));
            }

            return false;

        });
        $('#loginform input').keypress(function (e) {
            if (e.which == 13) {
                var validate_2 = $('#loginform').validate().form();
                errorElements = errorElements1.concat(errorElements2);
                if (validate_2) {
                    $('#loginform .submit-form').prop('disabled', true);
                    $('#loginform .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#loginform').submit();
                    }, 1000);

                }
                if (errorElements.length > 0) {
                    App.scrollToTopWhenFormHasError($('#loginform'));
                }

                return false;
            }
        });
        $('#loginform').submit(function () {
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: config.customer_url + "/user/edit",
                type: 'POST',
                dataType: 'json',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data)
                {
                    console.log(data);

//                    $('#loginform .submit-form').prop('disabled', false);
//                    $('#loginform .submit-form').html(lang.save);
                    if (data.type == 'success') {
//                        $('.alert-danger').hide();
//                        $('.alert-success').show().find('.message').html(data.message);
                        setTimeout(function () {
                            window.location.href = config.customer_url + '/dashboard';
                        }, 3000);

                    } else {
                        $('#loginform .submit-form').prop('disabled', false);
                        $('#loginform .submit-form').html(lang.edit);
                        if (typeof data.errors !== 'undefined') {
                            console.log(data.errors);
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error').removeClass("has-success");
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(data.errors[i][0])
                            }
                        }
                        if (typeof data.message !== 'undefined') {
                            $('.alert-success').hide();
                            $('.alert-danger').show().find('.message').html(data.message);
                        }
                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#loginform .submit-form').prop('disabled', false);
                    $('#loginform .submit-form').html(lang.save);
                    App.ajax_error_message(xhr);

                },
            });

            return false;
        });

    }


    return {
        init: function () {
            init();
        }

    }

}();

jQuery(document).ready(function () {
    Profile.init();
});


