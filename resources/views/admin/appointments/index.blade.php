@extends('admin.layouts.app')
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Appointments</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Appointments</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary">
                        Add Appointments
                    </a>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 300px;">
                            <div class="mb-2">
                                <label class="form-label">Status</label>
                                <select id="filter-status" class="form-select">
                                    <option value="">All</option>
                                    <option value="1">Pending</option>
                                    <option value="2">Assigned</option>
                                    <option value="3">Completed</option>
                                    <option value="4">Rejected</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Appointment Date</label>
                                <input type="date" id="filter-appointment-date" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Appointment Time</label>
                                <input type="time" id="filter-appointment-time" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Created Date</label>
                                <input type="date" id="filter-created-date" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">City</label>
                                <select id="filter-city" class="form-select">
                                    <option value="">All Cities</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button id="btn-apply-filters" class="btn btn-sm btn-primary">
                                    Apply
                                </button>
                                <button id="btn-reset-filters" class="btn btn-sm btn-secondary">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Column Search -->
                <section id="column-search-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-datatable">
                                    <table class="dt-column-search table w-100 dataTable" id="table-appointments">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                {{-- <th>Service Category</th> --}}
                                                {{-- <th>Service</th> --}}
                                                <th>Order Number</th>
                                                <th>First Name</th>
                                                {{-- <th>Last Name</th> --}}
                                                <th>Phone</th>
                                                {{-- <th>Quantity</th> --}}
                                                {{-- <th>Price</th> --}}
                                                {{-- <th>Discount Price</th> --}}
                                                {{-- <th>Service Address</th> --}}
                                                <th>Appointment Date</th>
                                                <th>Appointment Time</th>
                                                <th data-stuff="Pending,Assigned,Completed,Rejected">Status</th>
                                                <th data-search="false">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--/ Column Search -->
            </div>
        </div>
    </div>
    <!-- Assign Team Members Modal -->
    <div id="c-assignModal" class="c-modal">
        <div class="c-modal-dialog">
            <div class="c-modal-content">

                <!-- Header -->
                <div class="c-modal-header">
                    <h5 class="c-modal-title"><i class="bi bi-people-fill"></i> Assign Team Members</h5>
                    <button class="c-close-btn" data-c-close>&times;</button>
                </div>

                <!-- Body -->
                <div class="c-modal-body">
                    <form id="assignForm">
                        <input type="hidden" id="value_id" name="value_id">

                        <div class="mb-3">
                            <label for="team_members" class="form-label">Select Members</label>
                            <select id="team_members" name="team_members[]" class="form-control" multiple>
                                @foreach ($teamMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="c-modal-footer">
                    <button class="c-btn c-btn-secondary" data-c-close>
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                    <button type="button" id="saveMembers" class="c-btn c-btn-primary">
                        <i class="bi bi-check-circle"></i> Save
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div id="c-viewAppointmentModal" class="c-modal">
        <div class="c-modal-dialog">
            <div class="c-modal-content">
                <div class="c-modal-header">
                    <h5 class="c-modal-title"><i class="bi bi-journal-text"></i> Appoinment Details</h5>
                    <button class="c-close-btn" data-c-close>&times;</button>
                </div>
                <div class="c-modal-body" id="c-appointment-details">
                    <div class="c-loader">
                        <div class="c-spinner"></div>
                        <span>Fetching details...</span>
                    </div>
                </div>
                <div class="c-modal-footer">
                    <small><i class="bi bi-clock"></i> Updated just now</small>
                    <button class="c-btn" data-c-close><i class="bi bi-x-circle"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        const sweetalert_delete_title = "Delete Appointment?";
        const sweetalert_change_status = "Change Status of Appointment";
        const form_url = '/appointments';
        datatable_url = '/getDataAppointments';
        $('#team_members').select2({
            dropdownParent: $('#c-assignModal')
        });

        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 100,
            lengthMenu: [
                [10, 25, 50, 100, 200, -1],
                [10, 25, 50, 100, 200, "All"]
            ],
            columns: [{
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                // {
                //     data: 'service_category_name',
                //     name: 'service_category_name'
                // },
                // {
                //     data: 'service_name',
                //     name: 'service_name'
                // },
                {
                    data: 'order_number',
                    name: 'order_number'
                },
                {
                    data: 'first_name',
                    name: 'first_name'
                },
                // {
                //     data: 'last_name',
                //     name: 'last_name'
                // },
                {
                    data: 'phone',
                    name: 'phone'
                },
                // { data: 'quantity', name: 'quantity' },
                // { data: 'price', name: 'price' },
                // { data: 'discount_price', name: 'discount_price' },
                // { data: 'service_address', name: 'service_address' },
                {
                    data: 'appointment_date',
                    name: 'appointment_date'
                },
                {
                    data: 'appointment_time',
                    name: 'appointment_time'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false
                },
            ],
            order: [
                [0, 'DESC']
            ],
        });

        $(document).on('click', '#saveMembers', function() {
            let selected = $('#team_members').val();
            let value_id = $('#value_id').val();

            $.ajax({
                url: 'appointments/assign_member',
                method: 'POST',
                data: {
                    value_id: value_id,
                    members: selected
                },
                success: function(res) {
                    location.reload();
                    $('#c-assignModal').removeClass("show");
                }
            });
        });

        $(document).on("click", "[data-c-close]", function() {
            $("#c-assignModal").removeClass("show");
        });

        $(document).on('click', '.btn-view', function(e) {
            e.preventDefault();
            let id = $(this).data('id');

            $("#c-viewAppointmentModal").addClass("show");
            $("#c-appointment-details").html(
                `<div class="c-loader"><div class="c-spinner"></div><span>Loading...</span></div>`
            );

            $.ajax({
                url: '/admin/appointments-view/' + id, // 👈 Laravel route
                type: 'GET',
                success: function(response) {
                    let data = response.data;
                    console.log('Appointment data:', data);

                    // 🔹 Services
                    let servicesHtml = '-';
                    if (data.services && Array.isArray(data.services) && data.services.length > 0) {
                        servicesHtml = data.services.map(s =>
                            `<span class="c-include-badge">${s}</span>`).join(" ");
                    }

                    // 🔹 Team Members
                    let teamHtml = '-';
                    if (data.team_members && Array.isArray(data.team_members) && data.team_members
                        .length > 0) {
                        teamHtml = data.team_members.map(m =>
                            `<span class="c-include-badge">${m}</span>`).join(" ");
                    }

                    let html = `
                <div class="c-row">
                    <div class="c-col-6"><div class="c-detail-card"><label>City</label><p>${data.city_name ?? '-'}</p></div></div>
                    <div class="c-col-6"><div class="c-detail-card"><label>Category</label><p>${data.service_category ?? '-'}</p></div></div>
                    <div class="c-col-6"><div class="c-detail-card"><label>Sub Category</label><p>${data.service_sub_category ?? '-'}</p></div></div>
                    <div class="c-col-6"><div class="c-detail-card"><label>Services</label><p>${servicesHtml}</p></div></div>

                    <div class="c-col-6"><div class="c-detail-card"><label>Order #</label><p>${data.order_number ?? '-'}</p></div></div>
                    <div class="c-col-6"><div class="c-detail-card"><label>Quantity</label><p>${data.quantity ?? '-'}</p></div></div>

                    <div class="c-col-6"><div class="c-detail-card"><label>First Name</label><p>${data.first_name ?? '-'}</p></div></div>
                    <div class="c-col-6"><div class="c-detail-card"><label>Last Name</label><p>${data.last_name ?? '-'}</p></div></div>

                    <div class="c-col-6"><div class="c-detail-card"><label>Email</label><p>${data.email ?? '-'}</p></div></div>
                    <div class="c-col-6"><div class="c-detail-card"><label>Phone</label><p>${data.phone ?? '-'}</p></div></div>

                    <div class="c-col-6"><div class="c-detail-card"><label>Price</label><p>${data.price ?? '-'}</p></div></div>
                    <div class="c-col-6"><div class="c-detail-card"><label>Discount Price</label><p>${data.discount_price ?? '-'}</p></div></div>

                    <div class="c-col-6"><div class="c-detail-card"><label>Appointment Date</label><p>${data.appointment_date ?? '-'}</p></div></div>
                    <div class="c-col-6"><div class="c-detail-card"><label>Appointment Time</label><p>${data.appointment_time ?? '-'}</p></div></div>

                    <div class="c-col-12"><div class="c-detail-card"><label>Service Address</label><p>${data.service_address ?? '-'}</p></div></div>

                    <div class="c-col-12"><div class="c-detail-card"><label>Special Notes</label><p>${data.special_notes ?? '-'}</p></div></div>

                    <div class="c-col-6"><div class="c-detail-card"><label>Status</label>
                        <p>${
                            data.status == 1 ? '<span class="badge bg-warning">Pending</span>' :
                            data.status == 2 ? '<span class="badge bg-info">Assigned</span>' :
                            data.status == 3 ? '<span class="badge bg-success">Completed</span>' :
                            data.status == 4 ? '<span class="badge bg-danger">Rejected</span>' : '-'
                        }</p>
                    </div></div>

                    <div class="c-col-6"><div class="c-detail-card"><label>Team Members</label><p>${teamHtml}</p></div></div>

                    <div class="c-col-6"><div class="c-detail-card"><label>Assigned By</label><p>${data.assigned_by ?? '-'}</p></div></div>

                    <div class="c-col-6"><div class="c-detail-card"><label>Created At</label><p>${data.created_at ? new Date(data.created_at).toLocaleString() : '-'}</p></div></div>
                    <div class="c-col-6"><div class="c-detail-card"><label>Updated At</label><p>${data.updated_at ? new Date(data.updated_at).toLocaleString() : '-'}</p></div></div>
                </div>
            `;

                    $("#c-appointment-details").html(html);
                },
                error: function() {
                    $("#c-appointment-details").html(
                        `<div class="c-detail-card" style="color:red">Failed to load details.</div>`
                    );
                }
            });
        });

        $(document).on("click", "[data-c-close]", function() {
            $("#c-viewAppointmentModal").removeClass("show");
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
