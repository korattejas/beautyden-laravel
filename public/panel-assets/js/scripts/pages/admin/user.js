$(function () {

    let dt_filter_table = $('.dt-column-search');

    // Setup - add a text input to each footer cell
    $('.dt-column-search thead tr').clone(true).appendTo('.dt-column-search thead');
    $('.dt-column-search thead tr:eq(1) th').each(function (i) {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Search ' + title + '" />');

        $('input', this).on('keyup change', function () {
            if (dt_filter.column(i).search() !== this.value) {
                dt_filter.column(i).search(this.value).draw();
            }
        });
    });
    if (dt_filter_table.length) {
        var dt_filter = dt_filter_table.dataTable({
            processing: true,
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            ajax: APP_URL + '/getDatatableUser',
            columns: [
                {data: 'id', name: 'users.id'},
                {data: 'name', name: 'users.name'},
                {data: 'email', name: 'users.email'},
                {data: 'mobile_no', name: 'users.mobile_no'},
                {data: 'user_type', name: 'user_types.name'},
                {data: 'status', name: 'users.status'},
                {data: 'action', name: 'action'}
            ],
            orderCellsTop: true,
            language: {
                paginate: {
                    // remove previous & next text from pagination
                    previous: '&nbsp;',
                    next: '&nbsp;'
                }
            }
        });
    }
});
$(document).on('click', '.status-change', function () {
    const value_id = $(this).data('id');
    const status = $(this).data('status');
    console.log(status)
    swal({
        title: 'Status Change',
        text: status_msg,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: "#8379E7",
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        closeOnConfirm: true,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            changeStatus(value_id, status)
        }
    });
});

$(document).on('click', '.delete-single', function () {
    const value_id = $(this).data('id')

    swal({
        title: 'Delete Record',
        text: status_msg,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: "#8379E7",
        confirmButtonClass: "btn-danger",
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        closeOnConfirm: true,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            deleteRecord(value_id)
        }
    });
})
