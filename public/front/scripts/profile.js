
var Profile = function () {

    var init = function () {
        handle_edit();
 
     
   


    }
 

    
    var handle_edit = function () {
        $(".editProfileForm").validate({
            rules: {
                name: {
                    required: true
                },
                username: {
                    required: true
                },
                mobile: {
                    required: true
                },
                email: {
                    email: true
                },
                confirm_password: {
                    equalTo: "#password"
                }
            },

                   highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');


            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).tooltip('destroy');

            },
            errorPlacement: function (error, element) {
                errorElements.push(element);
                App.showValidateTooltip($(element), $(error).html(), 'bottom', '14', false)

            }

        });
        $('.editProfileForm .submit-form').click(function () {
            var validate_2 = $('.editProfileForm').validate().form();
       
            if (validate_2) {
                $('.editProfileForm .submit-form').prop('disabled', true);
                $('.editProfileForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('.editProfileForm').submit();
                }, 1000);

            }
            if (errorElements.length > 0) {
                App.scrollToTopWhenFormHasError($('.editProfileForm'));
            }

            return false;

        });
        $('.editProfileForm input').keypress(function (e) {
            if (e.which == 13) {
                var validate_2 = $('.editProfileForm').validate().form();
                if (validate_2) {
                    $('.editProfileForm .submit-form').prop('disabled', true);
                    $('.editProfileForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('.editProfileForm').submit();
                    }, 1000);

                }
                if (errorElements.length > 0) {
                    App.scrollToTopWhenFormHasError($('.editProfileForm'));
                }

                return false;
            }
        });
        $('.editProfileForm').submit(function () {
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

//                    $('.addEditAdsForm .submit-form').prop('disabled', false);
//                    $('.addEditAdsForm .submit-form').html(lang.save);
                    if (data.type == 'success') {
//                        $('.alert-danger').hide();
//                        $('.alert-success').show().find('.message').html(data.message);
                        setTimeout(function () {
                            window.location.href = config.customer_url + '/dashboard';
                        }, 3000);

                    } else {
                        $('.addEditAdsForm .submit-form').prop('disabled', false);
                        $('.addEditAdsForm .submit-form').html(lang.edit);
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
                    $('.editProfileForm .submit-form').prop('disabled', false);
                    $('.editProfileForm .submit-form').html(lang.save);
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


