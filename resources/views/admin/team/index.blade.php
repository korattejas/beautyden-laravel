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
                            <h2 class="content-header-title float-start mb-0">Team Members</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Team Members</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <a href="{{ route('admin.team.create') }}" class="btn btn-primary">
                        Add Team Member
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
                                                <th>Name</th>
                                                <th>Role</th>
                                                <th>Experience (Years)</th>
                                                <th>Photo</th>
                                                <th data-stuff="Active,InActive">Status</th>
                                                <th data-stuff="High Priority,Low Priority">Is Popular</th>
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
        const sweetalert_delete_title = "Delete Team Member?";
        const sweetalert_change_status = "Change Status of Team Member";
        const sweetalert_change_priority_status = "Change Popularity Status of Team Member";

        // base form and data URLs
        const form_url = '/team';
        datatable_url = '/getDataTeamMembers';

        $.extend(true, $.fn.dataTable.defaults, {
            columns: [{
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'role',
                    name: 'role'
                },
                {
                    data: 'experience_years',
                    name: 'experience_years'
                },
                {
                    data: 'photo',
                    name: 'photo',
                    orderable: false
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'is_popular',
                    name: 'is_popular'
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
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
