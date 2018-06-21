var Ad_Reports_grid;


var Ad_Reports = function() {
    var ids = [];
    var init = function() {
        $.extend(lang, new_lang);
        handleRecords();
        handleCheckAll();


    };
    var handleCheckAll = function() {
        $("#check-all-messages").on('change', function() {
            $('.check-one-message').not(this).prop('checked', this.checked);
            enableOrDisableDeleteBtn();
            getCheckedIds();
        });
        $(document).on('change', '.check-one-message', function() {
            if ($(".check-one-message:checked").length == 0) {
                $('#check-all-messages').prop('checked', false);
            }
            enableOrDisableDeleteBtn();
            getCheckedIds();
        });
    }
    var enableOrDisableDeleteBtn = function() {
        if ($(document).find(".check-one-message:checked").length == 0) {
            $(document).find('.btn-delete').prop('disabled', true);
        } else {
            $(document).find('.btn-delete').prop('disabled', false);
        }
    }
    var getCheckedIds = function() {
        var checked_ids = [];
        $(".check-one-message").each(function() {
            if ($(this).is(':checked')) {
                checked_ids.push($(this).data('id'));
            }
        });
        ids = checked_ids;
    }
    var handleRecords = function() {
        Ad_Reports_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/ad_reports/data",
                "type": "POST",
                "data": { _token: $('input[name="_token"]').val() },
            },
            "columns": [
                { "data": "input", orderable: false },
                { "data": "user" },
                { "data": "ad" },
                { "data": "report" },
                { "data": "created_at" },
            ],
            "order": [
                [4, "desc"]
            ],
            "oLanguage": { "sUrl": config.base_url + '/datatable-lang-' + config.lang_code + '.json' }

        });
    }


    return {
        init: function() {
            init();
        },
        delete: function(t) {
            $(t).prop('disabled', true);
            $(t).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
            if (ids.length > 0) {

                setTimeout(function() {


                    My.deleteForm({
                        element: t,
                        url: config.admin_url + '/ad_reports/delete',
                        data: { _method: 'DELETE', ids: ids, _token: $('input[name="_token"]').val() },
                        success: function(data) {
                            $(t).prop('disabled', false);
                            $(t).html(lang.delete);
                            Ad_Reports_grid.api().ajax.reload();
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
                            action: function() {}
                        }
                    }
                });
            }


        },
        empty: function() {
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
jQuery(document).ready(function() {
    Ad_Reports.init();
});