var main = function () {

    var init = function () {

        handleChangeCity();
        handleSearch();
        var country_id = config.country_id;
        if (!country_id) {
            $(window).on('load', function () {
                $('#searchModal').modal('show');
            });
        }



    }
    var handleChangeCity = function (ele, suite) {
        $(document).on('change', 'select[name="country"]', function () {
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
                    $('select[name="city"]').html(html);

                }, "json");
            } else {
                $('select[name="city"]').html(html);
            }
        });


    }




    var handleSearch = function () {
        $("#searchForm").validate({
            rules: {
                country: {
                    required: true,
                }
            },

            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('');

            },
            errorPlacement: function (error, element) {
                $(element).closest('.form-group').find('.help-block').html($(error).html());
            }

        });
        $('#searchModal .submit-form').click(function () {
            var validate_2 = $('#searchForm').validate().form();
            if (validate_2) {
                $('#searchModal .submit-form').prop('disabled', true);
                $('#searchModal .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#searchForm').submit();
                }, 1000);

            }
            if (errorElements.length > 0) {
                App.scrollToTopWhenFormHasError($('#search-form'));
            }

            return false;
        });

        $('#searchForm input').keypress(function (e) {
            if (e.which == 13) {
                var validate_2 = $('#searchForm').validate().form();

                if (validate_2) {
                    $('#searchModal .submit-form').prop('disabled', true);
                    $('#searchModal .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {

                        $('#searchForm').submit();
                    }, 500);

                }

                return false;
            }
        });
        $('#searchForm').submit(function () {
            $.ajax({
                url: config.ajax_url + "/change-location",
                type: 'GET',
                dataType: 'json',
                data: $(this).serialize(),
                async: false,

                success: function (data)
                {
                    console.log(data);
                    if (data.type == 'success') {
                        window.location.reload();
                    } else {
                        $('#searchModal .submit-form').prop('disabled', false);
                        $('#searchModal .submit-form').html(lang.apply);
                        if (typeof data.errors === 'object') {
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error').removeClass("has-info");
                                $('#' + i).closest('.form-group').find(".help-block").html(data.errors[i])
                            }
                        } else {
                            $('#alert-message').removeClass('alert-success').addClass('alert-danger').fadeIn(500).delay(3000).fadeOut(2000);
                            var message = '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span>' + data.message + '</span> ';
                            $('#alert-message').show().html(message);
                        }
                    }


                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#searchModal .submit-form').prop('disabled', false);
                    $('#searchModal .submit-form').html(lang.apply);
                    App.ajax_error_message(xhr);
                },
            });

            return false;
        });

    }
    return {
        init: function () {
            init();
        },

        handleFavourites: function (t) {

            var _config = $(t).data("config");
            $(t).prop('disabled', true);
            $(t).html('<i class="fa fa-spinner fa-spin fa-fw"></i><span class="sr-only">Loading...</span>');
//            console.log(config.url + '/add-favourite?meal=' + _config.meal_id+'&branch='+_config.resturant_branch_id);
//            return false;
            $.ajax({
                dataType: 'json',
                url: config.url + '/add-favourite?meal=' + _config.meal_id + '&branch=' + _config.resturant_branch_id,
                success: function (data) {
                    console.log(data);
                    $(t).prop('disabled', false);
                    if (data.type == 'success') {
                        if (data.message == true) {
                            $(t).addClass('active');
                        } else {
                            $(t).removeClass('active');
                        }

                        $(t).html('<i class="fa fa-heart-o" aria-hidden="true"></i>');
                    } else {

                    }

                },
                error: function (xhr, textStatus, errorThrown) {
                    App.ajax_error_message(xhr);
                    $(t).html('<i class="fa fa-heart-o" aria-hidden="true"></i>');
                },
            });

        },
        changelang: function () {

            $.get('' + config.base_url + '/ajax/changelang', function (data) {
            }).done(function (data) {
                window.location.reload();
            });

        },
    }


}();

$(document).ready(function () {
    main.init();
});