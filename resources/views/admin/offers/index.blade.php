@extends('admin.layouts.app')

@section('header_style_content')
<style>
    /* Photo Stack CSS from Portfolio */
    .photo-stack {
        display: flex;
        align-items: center;
    }

    .photo-stack-item {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        border: 2px solid #fff;
        object-fit: cover;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-left: -12px;
        transition: 0.3s;
    }

    .photo-stack-item:first-child {
        margin-left: 0;
    }

    .photo-stack-item:hover {
        transform: translateY(-3px) scale(1.1);
        z-index: 10;
        box-shadow: 0 5px 10px rgba(0,0,0,0.15);
    }

    .photo-count-badge {
        width: 30px;
        height: 30px;
        background: #f1f5f9;
        color: #102365;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        margin-left: 8px;
        border: 1px solid #e2e8f0;
    }
</style>
@endsection

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Offer Management</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item active">Offers</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <div class="mb-1 breadcrumb-right">
                        <a href="{{ route('admin.offers.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Add New Offer
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <section id="offers-list">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-datatable table-responsive p-2">
                                    <table class="dt-column-search table w-100 dataTable" id="table-1">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">#</th>
                                                <th>Title</th>
                                                <th>Position</th>
                                                <th>Media Assets</th>
                                                <th data-search="false">Priority</th>
                                                <th data-stuff="Active,InActive" style="width: 120px;">Status</th>
                                                <th data-search="false" style="width: 150px;">Operations</th>
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

    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div id="viewDetailsContent">
                    <!-- Loaded via AJAX -->
                    <div class="p-5 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        const sweetalert_delete_title = "Remove Offer?";
        const sweetalert_change_status = "Update Offer Status";
        const form_url = '/offers';
        datatable_url = '/getDataOffers';

        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 25,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            columns: [
                {
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return `<span class="text-muted fw-bold">#${meta.row + 1}</span>`;
                    }
                },
                {
                    data: 'title',
                    name: 'title',
                    render: function(data) {
                        return `<span class="fw-bold text-dark">${data}</span>`;
                    }
                },
                {
                    data: 'position_label',
                    name: 'position',
                    render: function(data) {
                        return `<span class="badge bg-light-primary text-primary px-3 py-2 rounded-pill">${data}</span>`;
                    }
                },
                {
                    data: 'media',
                    name: 'media',
                    orderable: false
                },
                {
                    data: 'priority',
                    name: 'priority',
                    render: function(data) {
                        return `<div class="text-center fw-bold">${data}</div>`;
                    }
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
            order: [[0, 'desc']],
            language: {
                search: "",
                searchPlaceholder: "Search Offers...",
                paginate: {
                    previous: "&nbsp;",
                    next: "&nbsp;"
                }
            }
        });

        // Handle Quick View Click
        $(document).on('click', '.btn-view', function() {
            const id = $(this).data('id');
            $('#viewDetailsContent').html('<div class="p-5 text-center"><div class="spinner-border text-primary" role="status"></div></div>');
            $('#viewDetailsModal').modal('show');

            $.ajax({
                url: `/admin/offers-view/${id}`,
                type: 'GET',
                success: function(response) {
                    $('#viewDetailsContent').html(response);
                },
                error: function() {
                    $('#viewDetailsContent').html('<div class="p-5 text-center text-danger">Error loading details.</div>');
                }
            });
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection


