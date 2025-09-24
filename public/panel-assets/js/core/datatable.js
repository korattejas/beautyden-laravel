$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    if ($('table').hasClass('dt-column-search')) {
        let dt_filter_table = $('.dt-column-search');

        // Setup - add a text input to each footer cell
        $('.dt-column-search thead tr').clone(true).appendTo('.dt-column-search thead');
        $('.dt-column-search thead tr:eq(1) th').each(function (i) {
            var title = $(this).text();
            if (!$(this).attr("data-search")) {
                if (!$(this).attr("data-stuff")) {
                    $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Search ' + title + '" />');
                    $('input', this).on('keyup change', function () {
                        if (dt_filter.columns(i).search() !== this.value) {
                            dt_filter.columns(i).search(this.value).draw();
                        }
                    });
                } else {
                    var data_attribute_array = $(this).attr("data-stuff").split(",");
                    var oselect_text = '<option value="">All</option>';

                    $.each(data_attribute_array, function (index, value) {
                        oselect_text += '<option value="' + value + '">' + value + '</option>';
                    });

                    $(this).html('<select type="text" class="form-control form-control-sm">' + oselect_text + '</select>');
                    $('select', this).on('keyup change', function () {
                        if (dt_filter.columns(i).search() !== this.value) {
                            dt_filter.columns(i).search(this.value).draw();
                        }
                    });
                }
            } else if (!$(this).attr("data-search") && $(this).attr("data-checkbox")) {
                $(this).html('<div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="check_all" value="checked"  /></div>');
                $('#check_all', this).on('keyup click', function () {
                    if (dt_filter.columns(i).search() !== this.value) {
                        dt_filter.columns(i).search(this.value).draw();
                    }
                });
            } else {
                $(this).html('-');
            }
        });

        // Initialize DataTable
        var dt_filter = dt_filter_table.DataTable({
            processing: true,
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"p>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            ajax: {
                url: APP_URL + datatable_url,
                data: function (d) {
                    d.status = $('#filter-status').val();
                    d.popular = $('#filter-popular').val();
                    d.featured = $('#filter-featured').val();
                    d.created_date = $('#filter-created-date').val();
                    d.review_date = $('#filter-review-date').val();
                    d.publish_date = $('#filter-publish-date').val();
                    d.min_experience = $('#filter-min-exp').val();
                    d.max_experience = $('#filter-max-exp').val();
                    d.salary_range = $('#filter-salary').val();
                    d.year_of_experience = $('#filter-year-of-experience').val();
                    d.launch_quarter = $('#filter-launch-quarter').val();
                    d.appointment_date = $('#filter-appointment-date').val();
                    d.appointment_time = $('#filter-appointment-time').val();
                    d.city_id = $('#filter-city').val();
                    d.signed_date = $('#filter-signed-date').val();
                }
            },
            orderCellsTop: true,
            language: {
                paginate: {
                    previous: '&nbsp;',
                    next: '&nbsp;'
                }
            }
        });

        $('#btn-apply-filters').on('click', function () {
            dt_filter.ajax.reload();
        });

        $('#btn-reset-filters').on('click', function () {
            $('#filter-status').val('');
            $('#filter-popular').val('');
            $('#filter-featured').val('');
            $('#filter-created-date').val('');
            $('#filter-review-date').val('');
            $('#filter-publish-date').val('');
            $('#filter-year-of-experience').val('');
            $('#filter-min-exp').val('');
            $('#filter-max-exp').val('');
            $('#filter-salary').val('');
            $('#filter-launch-quarter').val('');
            $('#filter-appointment-date').val('');
            $('#filter-appointment-time').val('');
            $('#filter-city').val('');
            $('#filter-signed-date').val('');
            dt_filter.ajax.reload();
        });
    }



    $(document).on('click', '.delete-single', function () {
        const value_id = $(this).data('id')

        Swal.fire({
            title: sweetalert_delete_title,
            text: sweetalert_delete_text,
            icon: "warning",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: delete_button_text,
            cancelButtonText: cancel_button_text,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                deleteRecord(value_id)
            }
        });
    })

    function deleteRecord(value_id) {
        loaderView();
        axios
            .delete(APP_URL + form_url + '/' + value_id)
            .then(function (response) {
                dt_filter.ajax.reload();
                notificationToast(response.data.message, 'success');
                loaderHide();

            })
            .catch(function (error) {
                notificationToast(error.response.data.message, 'warning')
                loaderHide();
            });

    }

    $(document).on('click', '.status-change', function () {
        const value_id = $(this).data('id');
        const status = $(this).data('change-status');
        Swal.fire({
            title: sweetalert_change_status,
            text: sweetalert_change_status_text,
            icon: "warning",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: yes_change_it,
            cancelButtonText: cancel_button_text,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false,
            showClass: {
                popup: 'animate__animated animate__flipInX'
            },
        }).then((result) => {
            if (result.isConfirmed) {
                changeStatus(value_id, status)
            }
        });

    });
    $(document).on('click', '.assign-member', function () {
        const value_id = $(this).data('id');
        const status = $(this).data('change-status');
        $('#value_id').val(value_id);
        $("#c-assignModal").addClass("show");

    });

    const changeStatus = (value_id, status) => {
        loaderView();
        axios
            .get(APP_URL + form_url + '/status' + '/' + value_id + '/' + status)
            .then(function (response) {
                dt_filter.ajax.reload();
                notificationToast(response.data.message, 'success');
                loaderHide();
            })
            .catch(function (error) {
                notificationToast(error.response.data.message, 'warning');
                loaderHide();
            });
    }


    $(document).on('click', '.priority-status-change', function () {
        const value_id = $(this).data('id');
        const status = $(this).data('priority-change-status');
        Swal.fire({
            title: sweetalert_change_priority_status,
            text: sweetalert_change_priority_status_text,
            icon: "warning",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: yes_change_it,
            cancelButtonText: cancel_button_text,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false,
            showClass: {
                popup: 'animate__animated animate__flipInX'
            },
        }).then((result) => {
            if (result.isConfirmed) {
                changePriorityStatus(value_id, status)
            }
        });

    });


    $(document).on('click', '.new-old-priority-status-change', function () {
        const value_id = $(this).data('id');
        const status = $(this).data('new-old-priority-status-change');
        Swal.fire({
            title: sweetalert_change_old_new_image_priority_status,
            text: sweetalert_change_old_new_image_priority_status_text,
            icon: "warning",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: yes_change_it,
            cancelButtonText: cancel_button_text,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false,
            showClass: {
                popup: 'animate__animated animate__flipInX'
            },
        }).then((result) => {
            if (result.isConfirmed) {
                changeOldNewPriorityStatus(value_id, status)
            }
        });

    });

    const changePriorityStatus = (value_id, status) => {
        loaderView();
        axios
            .get(APP_URL + form_url + '/priority-status' + '/' + value_id + '/' + status)
            .then(function (response) {
                dt_filter.ajax.reload();
                notificationToast(response.data.message, 'success');
                loaderHide();
            })
            .catch(function (error) {
                notificationToast(error.response.data.message, 'warning');
                loaderHide();
            });
    }

    const changeOldNewPriorityStatus = (value_id, status) => {
        loaderView();
        axios
            .get(APP_URL + form_url + '/old-new-image-priority-status' + '/' + value_id + '/' + status)
            .then(function (response) {
                dt_filter.ajax.reload();
                notificationToast(response.data.message, 'success');
                loaderHide();
            })
            .catch(function (error) {
                notificationToast(error.response.data.message, 'warning');
                loaderHide();
            });
    }

    $(document).on('click', '.detail-button', function () {
        const value_id = $(this).data('id');
        loaderView();
        axios
            .get(APP_URL + modal_url + '/' + value_id)
            .then(function (response) {
                $('#details_modal_title').html(response.data.modal_title);
                $('#details_modal_body').html(response.data.data);
                $('#detailsModal').modal('show')
                loaderHide();
            })
            .catch(function (error) {
                loaderHide();
                console.log(error)
            });
    });

    integerOnly();
}
)

// Filter column wise function
function filterColumn(i, val) {
    if (i == 5) {
        var startDate = $('.start_date').val(),
            endDate = $('.end_date').val();
        if (startDate !== '' && endDate !== '') {
            filterByDate(i, startDate, endDate); // We call our filter function
        }

        $('.dt-advanced-search').dataTable().fnDraw();
    } else {
        $('.dt-advanced-search').DataTable().column(i).search(val, false, true).draw();
    }
}

// Datepicker for advanced filter
var separator = ' - ',
    rangePickr = $('.flatpickr-range'),
    dateFormat = 'MM/DD/YYYY';
var options = {
    autoUpdateInput: false,
    autoApply: true,
    locale: {
        format: dateFormat,
        separator: separator
    },
    opens: $('html').attr('data-textdirection') === 'rtl' ? 'left' : 'right'
};

//
if (rangePickr.length) {
    rangePickr.flatpickr({
        mode: 'range',
        dateFormat: 'm/d/Y',
        onClose: function (selectedDates, dateStr, instance) {
            var startDate = '',
                endDate = new Date();
            if (selectedDates[0] != undefined) {
                startDate =
                    selectedDates[0].getMonth() + 1 + '/' + selectedDates[0].getDate() + '/' + selectedDates[0].getFullYear();
                $('.start_date').val(startDate);
            }
            if (selectedDates[1] != undefined) {
                endDate =
                    selectedDates[1].getMonth() + 1 + '/' + selectedDates[1].getDate() + '/' + selectedDates[1].getFullYear();
                $('.end_date').val(endDate);
            }
            $(rangePickr).trigger('change').trigger('keyup');
        }
    });
}

// Advance filter function
// We pass the column location, the start date, and the end date
var filterByDate = function (column, startDate, endDate) {
    // Custom filter syntax requires pushing the new filter to the global filter array
    $.fn.dataTableExt.afnFiltering.push(function (oSettings, aData, iDataIndex) {
        var rowDate = normalizeDate(aData[column]),
            start = normalizeDate(startDate),
            end = normalizeDate(endDate);

        // If our date from the row is between the start and end
        if (start <= rowDate && rowDate <= end) {
            return true;
        } else if (rowDate >= start && end === '' && start !== '') {
            return true;
        } else if (rowDate <= end && start === '' && end !== '') {
            return true;
        } else {
            return false;
        }
    });
};

// converts date strings to a Date object, then normalized into a YYYYMMMDD format (ex: 20131220). Makes comparing
// dates easier. ex: 20131220 > 20121220
var normalizeDate = function (dateString) {
    var date = new Date(dateString);
    var normalized =
        date.getFullYear() + '' + ('0' + (date.getMonth() + 1)).slice(-2) + '' + ('0' + date.getDate()).slice(-2);
    return normalized;
};
