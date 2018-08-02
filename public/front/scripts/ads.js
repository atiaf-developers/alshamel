
var Ads = function () {

    var init = function () {

        handleAdSubmit();
        handleChangeCountry();
        handleChangeCategory();
        handleChangeSubCategory();
        handleChangeMeasruingUnit();
        if ($('#map').length > 0) {
            Map.initMap(true, true, true, false);
        }

    }
    var showValidateTooltip = function (element, title, placement, fontSize, container) {
        if (element.attr('name') == 'latlng') {
            element.closest('.form-group').addClass('location');
        } else if (element.attr('type') == 'radio') {
            element.closest('.form-group').addClass('ad-radio');
            element = element.closest('.form-group');
        }

        element.tooltip({'template': '<div class="tooltip newValidateTooltip" style="font-size:' + fontSize + '" role="tooltip"><div class="tooltip-arrow  alshamel-tooltip-arrow"></div><div class="tooltip-inner  alshamel-tooltip"></div></div>', 'container': container, 'title': title, 'placement': placement, 'trigger': 'manual', 'animation': false}).tooltip('show');


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
    var handleChangeMeasruingUnit = function (ele, suite) {
        $(document).on('change', 'select[name="measruing_unit"]', function () {
            var country = $(this).val();
            var html = '<option value="">' + lang.choose + '</option>';
            if (country && country != '') {
                $.get('' + config.ajax_url + '/get-car-speedometer/' + country, function (data) {
                    if (data.data.length != 0)
                    {
                        $.each(data.data, function (index, Obj) {
                            html += '<option value="' + Obj.id + '">' + Obj.title + '</option>';
                        });
                    }
                    $('select[name="car_speedometer"]').html(html);

                }, "json");
            } else {
                $('select[name="car_speedometer"]').html(html);
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

    var createValidationForParams = function (data) {
        console.log(data);
        if (data.data.length > 0) {
            for (var x = 0; x < data.data.length; x++) {
                var item = data.data[x];
                var ele = '';
                if (item.type == 'dropdown' || item.type == 'range') {
                    ele = "select[name='" + item.name + "']";
                } else if (item.type == 'text' || item.type == 'radio') {
                    ele = "input[name='" + item.name + "']";
                    console.log(ele);
                }

                if (ele != '') {
                    if ($(ele).length > 0) {
                        $(ele).rules('add', {
                            required: true
                        });
                    }

                }

            }
        }
    }

    var handleChangeSubCategory = function () {
        $(document).on('change', 'select[name="sub_category"]', function () {
            var sub_category = $(this).val();
            var urlGetHtml = config.ajax_url + '/get-basic-data/' + sub_category;
            var urlGetParams = config.ajax_url + '/get-params/' + sub_category;
            $('#loader').show();
            $('.ad-form-content').addClass('loading');
            setTimeout(function () {
                var ajaxGetHtml = $.ajax({url: urlGetHtml, type: 'GET', dataType: 'text', });
                var ajaxGetParams = $.ajax({url: urlGetParams, type: 'GET', dataType: 'json', });
                $.when(ajaxGetHtml, ajaxGetParams).then(
                        function (resp1, resp2) {
                            console.log(resp1);
                            console.log(resp2);
                            $('#loader').hide();
                            $('.ad-form-content').removeClass('loading');
                            $('#basic-data-content').html(resp1[0]);
                            //createValidationForParams(resp2[0]);
                        }, function (err1, err2) {
                    console.log(err2);
                }
                );


            }, 1000);




        });
    }

    var handleChangeSubCategory2 = function () {
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
    var handleAdSubmit = function () {
        $.validator.addMethod('location', function (value, element, params) {
            //whatever
            return true;
            if (!value) {
                return true;
            }
        }, lang.required);

        $('.addEditAdsForm').validate({
            ignore: "",
            rules: {
//                latlng: {
//                    required: true,
//                },
//                ad_country: {
//                    required: true,
//                },
//                ad_city: {
//                    required: true,
//                },
//                main_category: {
//                    required: true,
//                },
//                sub_category: {
//                    required: true,
//                },
//                title: {
//                    required: true,
//                },
//                email: {
//                    required: true,
//                },
//                mobile: {
//                    required: true,
//                },
//                details: {
//                    required: true,
//                },


            },
            //messages: lang.messages,
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');


            },
            unhighlight: function (element) {
                console.log($(element).attr('name'));
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success').tooltip('destroy');
                $(element).tooltip('destroy');



            },
            errorPlacement: function (error, element) {
                errorElements.push(element);
                showValidateTooltip($(element), $(error).html(), 'bottom', '14', false)

            }
        });


        $('.addEditAdsForm .submit-form').click(function () {

            if ($('.addEditAdsForm').validate().form()) {
                $('.addEditAdsForm .submit-form').prop('disabled', true);
                $('.addEditAdsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('.addEditAdsForm').submit();
                }, 1000);
            } else {
                if (errorElements.length > 0) {
                    App.scrollToTopWhenFormHasError($('.addEditAdsForm'));
                }
            }

            return false;
        });
        $('.addEditAdsForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('.addEditAdsForm').validate().form()) {
                    $('.addEditAdsForm .submit-form').prop('disabled', true);
                    $('.addEditAdsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('.addEditAdsForm').submit();
                    }, 1000);
                } else {
                    if (errorElements.length > 0) {
                        App.scrollToTopWhenFormHasError($('.addEditAdsForm'));
                    }
                }

                return false;
            }
        });



        $('.addEditAdsForm').submit(function () {
            var id = $('#id').val();
            var action = config.customer_url + '/ads';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.customer_url + '/ads/' + id;
            }
            if ($('#category').val()) {
                formData.append('category_id', $('#category').val());
            } else {
                formData.append('category_id', $('#sub_category').val());
            }

            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    $('.addEditAdsForm .submit-form').prop('disabled', false);
                    $('.addEditAdsForm .submit-form').html(lang.save);

                    if (data.type == 'success') {

                        $('.alert-danger').hide();
                        $('.alert-success').show().find('.message').html(data.message);
                        setTimeout(function () {
                            window.location.href = config.customer_url + '/ads';
                        }, 2000);

                    } else {
                        if (typeof data.errors !== 'undefined') {
                            console.log(data.errors);
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error').removeClass("has-success");
                                showValidateTooltip($('[name="' + i + '"]'), data.errors[i][0], 'bottom', '14', false);
                                errorElements.push($('[name="' + i + '"]'));
                            }
                            if (errorElements.length > 0) {
                                App.scrollToTopWhenFormHasError($('.addEditAdsForm'));
                            }
                        }
                        if (typeof data.message !== 'undefined') {
                            $('.alert-success').hide();
                            $('.alert-danger').show().find('.message').html(data.message);
                        }
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('.addEditAdsForm .submit-form').prop('disabled', false);
                    $('.addEditAdsForm .submit-form').html(lang.save);
                    App.ajax_error_message(xhr);
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
        }

    }

}();

jQuery(document).ready(function () {
    Ads.init();
});


