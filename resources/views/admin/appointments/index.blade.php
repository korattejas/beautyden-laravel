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
                                                <th>Service Category</th>
                                                <th>Service</th>
                                                <th>Order Number</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Phone</th>
                                                {{-- <th>Quantity</th> --}}
                                                {{-- <th>Price</th> --}}
                                                {{-- <th>Discount Price</th> --}}
                                                {{-- <th>Service Address</th> --}}
                                                <th>Appointment Date</th>
                                                <th>Appointment Time</th>
                                                <th data-stuff="Active,Inactive">Status</th>
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
@endsection

@section('footer_script_content')
    <script>
        const sweetalert_delete_title = "Delete Appointment?";
        const sweetalert_change_status = "Change Status of Appointment";
        const form_url = '/appointments';
        datatable_url = '/getDataAppointments';

        $.extend(true, $.fn.dataTable.defaults, {
            columns: [
                {
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1; 
                    }
                },
                { data: 'service_category_name', name: 'service_category_name' },
                { data: 'service_name', name: 'service_name' },
                { data: 'order_number', name: 'order_number' },
                { data: 'first_name', name: 'first_name' },
                { data: 'last_name', name: 'last_name' },
                { data: 'phone', name: 'phone' },
                // { data: 'quantity', name: 'quantity' },
                // { data: 'price', name: 'price' },
                // { data: 'discount_price', name: 'discount_price' },
                // { data: 'service_address', name: 'service_address' },
                { data: 'appointment_date', name: 'appointment_date' },
                { data: 'appointment_time', name: 'appointment_time' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false },
            ],
            order: [[0, 'DESC']],
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
