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
                            <h2 class="content-header-title float-start mb-0">Hirings</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Hirings</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <a href="{{ route('admin.hirings.create') }}" class="btn btn-primary">
                        Add Hiring
                    </a>
                </div>
            </div>

            <div class="content-body">
                <!-- Column Search -->
                <section id="column-search-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-datatable">
                                    <table class="dt-column-search table w-100 dataTable" id="table-1">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>City</th>
                                                <th>Experience Level</th>
                                                <th>Experience (Min - Max)</th>
                                                <th>Salary Range</th>
                                                <th data-stuff="Full-time,Part-time,Internship,Work from home">Hiring Type</th>
                                                <th data-stuff="Female,Male,Any">Gender Preference</th>
                                                <th data-stuff="Active,Inactive">Status</th>
                                                <th data-stuff="Yes,No">Is Popular</th>
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
        const sweetalert_delete_title = "Delete Hiring?";
        const sweetalert_change_status = "Change Status of Hiring";
        const sweetalert_change_priority_status = "Change Popular Status of Hiring";

        const form_url = '/hirings';
        datatable_url = '/getDataHirings';

        $.extend(true, $.fn.dataTable.defaults, {
            columns: [
                {
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'title', name: 'title' },
                { data: 'city', name: 'city' },
                { data: 'experience_level', name: 'experience_level' },
                {
                    data: null,
                    name: 'experience',
                    render: function(data, type, row) {
                        return (row.min_experience ?? '-') + ' - ' + (row.max_experience ?? '-');
                    }
                },
                { data: 'salary_range', name: 'salary_range' },
                { data: 'hiring_type', name: 'hiring_type' },
                { data: 'gender_preference', name: 'gender_preference' },
                { data: 'status', name: 'status' },
                { data: 'is_popular', name: 'is_popular' },
                { data: 'action', name: 'action', orderable: false },
            ],
            order: [[0, 'DESC']],
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
