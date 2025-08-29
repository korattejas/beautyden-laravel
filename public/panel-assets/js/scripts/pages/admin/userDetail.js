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
        // var user_id = $(this).val('user_id');
        // console.log($('#user_id').val())
        var dt_filter = dt_filter_table.dataTable({
            processing: true,
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            ajax: APP_URL + '/getDatatableUserHorse',
            // data: { user_id: 1 },
            // data: function (d) {
            //     d.user_id = 1;
            // },
            columns: [
                {data: 'id', name: 'horses.id'},
                {data: 'name', name: 'horses.name'},
                {data: 'price', name: 'horses.price'},
                {data: 'status', name: 'horses.status'},
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
    let dt_filter_table_1 = $('.dt-column-search-1');

    // Setup - add a text input to each footer cell
    $('.dt-column-search-1 thead tr').clone(true).appendTo('.dt-column-search-1 thead');
    $('.dt-column-search-1 thead tr:eq(1) th').each(function (i) {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Search ' + title + '" />');

        $('input', this).on('keyup change', function () {
            if (dt_filter_1.column(i).search() !== this.value) {
                dt_filter_1.column(i).search(this.value).draw();
            }
        });
    });
    if (dt_filter_table_1.length) {
        // var user_id = $(this).val('user_id');
        // console.log($('#user_id').val())
        var dt_filter_1 = dt_filter_table_1.dataTable({
            processing: true,
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            ajax: APP_URL + '/getDatatableOffer',
            // data: { user_id: 1 },
            // data: function (d) {
            //     d.user_id = 1;
            // },
            columns: [
                {data: 'horse_id', name: 'offers.horse_id'},
                {data: 'name', name: 'offers.name'},
                {data: 'price', name: 'offers.price'},
                {data: 'buyer', name: 'users.price'},
                {data: 'seller', name: 'users.price'},
                {data: 'price_range', name: 'offers.price'},
                {data: 'status', name: 'offers.status'},
                {data: 'action', name: 'action', orderable: false},
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
    $(document).on('click', '.status-change', function () {
        const value_id = $(this).data('id');
        const status = $(this).data('status');
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
}

);


