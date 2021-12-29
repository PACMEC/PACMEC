jQuery(function ($) {
    $(document.body).on('click', '#create_new_wallet', function(){
        var $new_row = '<li><input type="text" name="pl8apppro_redux_options[pl8app_addresses][]" value="" class="regular-text">' +
            '<a href="javascript:void(0);" class="deletion">Remove</a></li>';

        $('#pl8app_addresses-ul').append($new_row);
    });

    $(document.body).on('click', 'a.deletion', function(){
        $(this).closest('li').remove();
    });

    $(document.body).on('click', 'input:checkbox[name="pl8apppro_redux_options[qr_code][no_selected]"]', function () {
        if (this.checked) {
            $('input:checkbox[name="pl8apppro_redux_options[qr_code][receiver]"]').prop('checked', false);
            $('input:checkbox[name="pl8apppro_redux_options[qr_code][info]"]').prop('checked', false);
        }
    });

    $(document.body).on('click', 'input:checkbox[name="pl8apppro_redux_options[qr_code][receiver]"], input:checkbox[name="pl8apppro_redux_options[qr_code][info]"]', function () {
        if (this.checked) {
            $('input:checkbox[name="pl8apppro_redux_options[qr_code][no_selected]"]').prop('checked', false);
        }
    });
});