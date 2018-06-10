var Ads_grid;

var Ads = function() {

    var init = function() {
        $.extend(lang, new_lang);
        handleRecords();
    };



    var handleRecords = function() {

        Ads_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/ads/data",
                "type": "POST",
                data: { _token: $('input[name="_token"]').val() },
            },
            "columns": [
                { "data": "title" },
                { "data": "email" },
                { "data": "mobile" },
                { "data": "special" },
                { "data": "active" },
                { "data": "options", orderable: false, searchable: false }
            ],
            "order": [
                [1, "desc"]
            ],
            "oLanguage": { "sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json' }

        });
    }

    return {
        init: function() {
            init();
        },
        active: function(t) {
            var id = $(t).data("id");
            $(t).prop('disabled', true);
            $(t).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');

            $.ajax({
                url: config.admin_url + '/ads/active/' + id,
                success: function(data) {
                    $(t).prop('disabled', false);
                    if ($(t).hasClass("btn-info")) {
                        $(t).addClass('btn-danger').removeClass('btn-info');
                        $(t).html(lang.not_active);

                    } else {
                        $(t).addClass('btn-info').removeClass('btn-danger');
                        $(t).html(lang.active);
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    My.ajax_error_message(xhr);
                },
            });
        },

        special: function(t) {
            var id = $(t).data("id");
            $(t).prop('disabled', true);
            $(t).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');

            $.ajax({
                url: config.admin_url + '/ads/special/' + id,
                success: function(data) {
                    $(t).prop('disabled', false);
                    if ($(t).hasClass("btn-info")) {
                        $(t).addClass('btn-danger').removeClass('btn-info');
                        $(t).html(lang.not_special);

                    } else {
                        $(t).addClass('btn-info').removeClass('btn-danger');
                        $(t).html(lang.special);
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    My.ajax_error_message(xhr);
                },
            });
        },
        delete: function(t) {
            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/ads/' + id + '',
                data: { _method: 'DELETE', _token: $('input[name="_token"]').val() },
                success: function(data) {

                    Famous_grid.api().ajax.reload();


                }
            });
        },
        empty: function() {
            $('#id').val(0);
            $('#active').find('option').eq(0).prop('selected', true);
            $('#user_image').val(null);
            $('.user_image_box').html('<img src="' + config.url + '/no-image.png" class="user_image" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        },
    };
}();
$(document).ready(function() {
    Ads.init();
});