$(function () {
    $('#all').click(function () {
        if ($(this).is(':checked')) {
            $("#create").attr('checked', true);
            $("#update").attr('checked', true);
            $("#read").attr('checked', true);
            $("#delete").attr('checked', true);
        } else {
            $("#create").attr('checked', false);
            $("#update").attr('checked', false);
            $("#read").attr('checked', false);
            $("#delete").attr('checked', false);
        }
    });
});


