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
    <div id="c-viewBlogModal" class="c-modal">
        <div class="c-modal-dialog">
            <div class="c-modal-content">
                <div class="c-modal-header">
                    <h5 class="c-modal-title"><i class="bi bi-journal-text"></i> Blog Details</h5>
                    <button class="c-close-btn" data-c-close>&times;</button>
                </div>
                <div class="c-modal-body" id="c-blog-details">
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
        const sweetalert_delete_title = "Delete Blog?";
        const sweetalert_change_status = "Change Status of Blog";
        const sweetalert_change_priority_status = "Change Featured Status of Blog";

        // base form and data URLs
        const form_url = '/blogs';
        datatable_url = '/getDataBlogs';

        $.extend(true, $.fn.dataTable.defaults, {
            columns: [{
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'publish_date',
                    name: 'publish_date'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'featured',
                    name: 'featured'
                },
                {
                    data: 'icon',
                    name: 'icon',
                    orderable: false
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

        // Blog View Modal
        $(document).on('click', '.btn-view', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            const baseUrl = "{{ asset('uploads/blogs') }}/";

            $("#c-viewBlogModal").addClass("show");
            $("#c-blog-details").html(
                `<div class="c-loader"><div class="c-spinner"></div><span>Loading...</span></div>`);

            $.ajax({
                url: '/admin/blogs-view/' + id,
                type: 'GET',
                success: function(response) {
                    let data = response.data;
                    let html = `
                        <div class="c-row">
                            <div class="c-col-6"><div class="c-detail-card"><label>Category</label><p>${data.category ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Title</label><p>${data.title ?? '-'}</p></div></div>
                            <div class="c-col-12"><div class="c-detail-card"><label>Excerpt</label><p>${data.excerpt ?? '-'}</p></div></div>
                            <div class="c-col-12"><div class="c-detail-card"><label>Content</label><p>${data.content ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Read Time</label><p>${data.read_time ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Author</label><p>${data.author ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Publish Date</label><p>${data.publish_date ?? '-'}</p></div></div>
                            <div class="c-col-12"><div class="c-detail-card"><label>Tags</label><p>${
                                data.tags ? JSON.parse(data.tags).map(tag => `<span class="c-include-badge">${tag}</span>`).join(" ") : '-'
                            }</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Featured</label>
                                <p>${data.featured == 1 ? '<span class="badge badge-glow bg-primary">Featured</span>' : '<span class="badge badge-glow bg-secondary">Normal</span>'}</p>
                            </div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Status</label>
                                <p>${data.status == 1 ? '<span class="badge badge-glow bg-success">Active</span>' : '<span class="badge badge-glow bg-danger">InActive</span>'}</p>
                            </div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Created At</label><p>${data.created_at ? new Date(data.created_at).toLocaleString() : '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Updated At</label><p>${data.updated_at ? new Date(data.updated_at).toLocaleString() : '-'}</p></div></div>

                            <div class="c-col-12 text-center">
                                <label>Icon</label><br>
                                ${data.icon 
                                    ? `<img src="${baseUrl + data.icon}" alt="Blog Icon" class="img-fluid service-icon" style="max-width:250px; cursor:pointer;" onclick="window.open('${baseUrl + data.icon}', '_blank')">`
                                    : '<p>-</p>'
                                }
                            </div>
                        </div>`;
                    $("#c-blog-details").html(html);
                },
                error: function() {
                    $("#c-blog-details").html(
                        `<div class="c-detail-card" style="color:red">Failed to load details.</div>`
                    );
                }
            });
        });

        $(document).on("click", "[data-c-close]", function() {
            $("#c-viewBlogModal").removeClass("show");
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
