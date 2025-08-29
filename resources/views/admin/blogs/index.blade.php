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
                        <h2 class="content-header-title float-start mb-0">Blogs</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">Blogs</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                    Add Blog
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
                                            <th>Id</th>
                                            <th>Category</th>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Publish Date</th>
                                            <th data-stuff="Active,InActive">Status</th>
                                            <th data-stuff="High Priority,Low Priority">Featured</th>
                                            <th>Icon</th>
                                            <th data-search="false">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    const sweetalert_delete_title = "Delete Blog?";
    const sweetalert_change_status = "Change Status of Blog";
    const sweetalert_change_priority_status = "Change Featured Status of Blog";

    // base form and data URLs
    const form_url = '/blogs';
    datatable_url = '/getDataBlogs';

    $.extend(true, $.fn.dataTable.defaults, {
        columns: [
            {
                data: null,
                name: 'id',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'category', name: 'category' },
            { data: 'title', name: 'title' },
            { data: 'author', name: 'author' },
            { data: 'publish_date', name: 'publish_date' },
            { data: 'status', name: 'status' },
            { data: 'featured', name: 'featured' },
            { data: 'icon', name: 'icon', orderable: false },
            { data: 'action', name: 'action', orderable: false },
        ],
        order: [[0, 'DESC']],
    });
</script>
<script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{time()}}"></script>
@endsection
