@extends('admin.layouts.app')

@section('header_style_content')
<style>
    :root {
        --mst-indigo: #102365;
        --mst-indigo-light: #f5f7ff;
        --mst-success: #059669;
        --mst-danger: #dc2626;
        --mst-text-main: #1e293b;
        --mst-text-muted: #64748b;
        --mst-bg-body: #f8fafc;
        --mst-border: #e2e8f0;
    }

    .offers-index-container {
        padding: 2rem;
        background: var(--mst-bg-body);
        min-height: 100vh;
    }

    .offers-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
    }

    .title-area h2 {
        font-weight: 800;
        color: var(--mst-indigo);
        font-size: 2rem;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .title-area p {
        color: var(--mst-text-muted);
        margin: 5px 0 0;
    }

    .btn-add-offer {
        background: var(--mst-indigo);
        color: #fff;
        padding: 12px 28px;
        border-radius: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: 0.3s;
        box-shadow: 0 4px 12px rgba(16, 35, 101, 0.2);
        border: none;
    }

    .btn-add-offer:hover {
        background: #0a1740;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(16, 35, 101, 0.3);
        color: #fff;
    }

    .premium-table-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--mst-border);
        overflow: hidden;
        padding: 1rem;
    }

    .dataTable thead th {
        background: #f8fafc;
        color: var(--mst-text-main);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
        padding: 20px 15px !important;
        border-bottom: 1px solid var(--mst-border) !important;
    }

    .dataTable tbody td {
        padding: 18px 15px !important;
        vertical-align: middle !important;
        color: var(--mst-text-main);
        font-weight: 500;
    }

    .media-preview img {
        transition: 0.3s;
    }

    .media-preview img:hover {
        transform: scale(1.2);
        z-index: 10;
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }
</style>
@endsection

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="offers-index-container">
                
                <div class="offers-header">
                    <div class="title-area">
                        <h2>Offer Management 🎁</h2>
                        <p>Manage promotional banners, sliders, and footer offers.</p>
                    </div>
                    <a href="{{ route('admin.offers.create') }}" class="btn-add-offer">
                        <i class="bi bi-plus-lg"></i> Add New Offer
                    </a>
                </div>

                <div class="premium-table-card">
                    <div class="card-datatable table-responsive">
                        <table class="dt-column-search table w-100 dataTable" id="table-1">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Title</th>
                                    <th>Position</th>
                                    <th>Media</th>
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
                        return `<span class="badge bg-light-primary text-primary">${data}</span>`;
                    }
                },
                {
                    data: 'media',
                    name: 'media',
                    orderable: false
                },
                {
                    data: 'priority',
                    name: 'priority'
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
            order: [[4, 'asc']],
            language: {
                search: "",
                searchPlaceholder: "Search Offers...",
                paginate: {
                    previous: "&nbsp;",
                    next: "&nbsp;"
                }
            }
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
