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

    .portfolio-index-container {
        padding: 2rem;
        background: var(--mst-bg-body);
        min-height: 100vh;
    }

    .portfolio-header {
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

    .btn-add-portfolio {
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

    .btn-add-portfolio:hover {
        background: #0a1740;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(16, 35, 101, 0.3);
        color: #fff;
    }

    /* Premium Table Styling */
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

    .dataTable tbody tr {
        transition: 0.2s;
    }

    .dataTable tbody tr:hover {
        background-color: #fcfdfe !important;
    }

    /* Photo Preview Component */
    .photo-stack {
        display: flex;
        align-items: center;
    }

    .photo-stack-item {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        border: 3px solid #fff;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-left: -15px;
        transition: 0.3s;
    }

    .photo-stack-item:first-child {
        margin-left: 0;
    }

    .photo-stack-item:hover {
        transform: translateY(-5px) scale(1.1);
        z-index: 10;
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }

    .photo-count-badge {
        width: 32px;
        height: 32px;
        background: var(--mst-indigo-light);
        color: var(--mst-indigo);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        margin-left: 10px;
    }

    /* Status Badges */
    .badge-luxury {
        padding: 6px 14px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
    }

</style>
@endsection

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="portfolio-index-container">
                
                <div class="portfolio-header">
                    <div class="title-area">
                        <h2>Master Portfolio ✨</h2>
                        <p>Manage and showcase your best work to clients.</p>
                    </div>
                    <a href="{{ route('admin.portfolio.create') }}" class="btn-add-portfolio">
                        <i class="bi bi-plus-lg"></i> Add New Collection
                    </a>
                </div>

                <div class="premium-table-card">
                    <div class="card-datatable table-responsive">
                        <table class="dt-column-search table w-100 dataTable" id="table-1">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Portfolio Name</th>
                                    <th data-search="false" style="width: 250px;">Digital Assets</th>
                                    <th data-stuff="Active,InActive" style="width: 150px;">Status</th>
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
        const sweetalert_delete_title = "Remove Portfolio?";
        const sweetalert_change_status = "Update Visibility Status";
        const form_url = '/portfolio';
        datatable_url = '/getDataPortfolio';

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
                    data: 'name',
                    name: 'name',
                    render: function(data) {
                        return `<span class="fw-bold text-dark" style="font-size: 1.05rem;">${data}</span>`;
                    }
                },
                {
                    data: 'photos',
                    name: 'photos',
                    orderable: false
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
            order: [[0, 'DESC']],
            language: {
                search: "",
                searchPlaceholder: "Search Portfolio...",
                paginate: {
                    previous: "&nbsp;",
                    next: "&nbsp;"
                }
            }
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
