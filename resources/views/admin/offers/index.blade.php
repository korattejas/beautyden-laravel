@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root {
        --off-primary: #1a237e;
        --off-primary-soft: rgba(26, 35, 126, 0.08);
        --off-bg: #f8fafc;
        --off-card-bg: #ffffff;
        --off-text-main: #1e293b;
        --off-text-muted: #64748b;
        --off-radius: 16px;
        --off-shadow: 0 4px 20px rgba(0,0,0,0.06);
        --off-shadow-hover: 0 12px 30px rgba(0,0,0,0.12);
    }

    body {
        background-color: var(--off-bg);
        font-family: 'Poppins', sans-serif;
    }

    .offer-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem 0;
    }

    .offer-banner-card {
        background: var(--off-card-bg);
        border-radius: var(--off-radius);
        overflow: hidden;
        box-shadow: var(--off-shadow);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        border: 1px solid #eef2f7;
        position: relative;
    }

    .offer-banner-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--off-shadow-hover);
        border-color: var(--off-primary-soft);
    }

    .card-media-wrapper {
        position: relative;
        height: 160px;
        background: #eee;
        overflow: hidden;
    }

    .card-media-wrapper img, .card-media-wrapper video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.5s;
    }

    .offer-banner-card:hover .card-media-wrapper img {
        transform: scale(1.1);
    }

    .media-overlay {
        position: absolute;
        top: 12px;
        right: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        z-index: 2;
    }

    .badge-glass {
        backdrop-filter: blur(8px);
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: var(--off-text-main);
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .status-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 2;
    }

    .card-content {
        padding: 1rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .offer-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--off-text-main);
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .offer-position {
        font-size: 0.85rem;
        color: var(--off-primary);
        background: var(--off-primary-soft);
        display: inline-block;
        padding: 4px 12px;
        border-radius: 50px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .offer-meta {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }

    .priority-badge {
        font-size: 0.8rem;
        color: var(--off-text-muted);
        font-weight: 600;
    }

    .priority-badge i {
        color: #f59e0b;
        margin-right: 4px;
    }

    .card-actions {
        display: flex;
        gap: 8px;
        margin-top: 15px;
    }

    .btn-action-pill {
        flex: 1;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: var(--off-text-muted);
        text-decoration: none !important;
    }

    .btn-action-pill:hover {
        background: var(--off-primary-soft);
        color: var(--off-primary);
        border-color: var(--off-primary);
        transform: translateY(-2px);
    }

    .btn-delete:hover {
        background: #fef2f2;
        color: #ef4444;
        border-color: #ef4444;
    }

    .header-btn {
        padding: 0 24px !important;
        font-weight: 700 !important;
        border-radius: 12px !important;
        display: flex;
        align-items: center;
        gap: 10px;
        height: 48px;
        background: linear-gradient(135deg, #1a237e 0%, #311b92 100%) !important;
        border: none !important;
        color: #fff !important;
        box-shadow: 0 4px 15px rgba(26, 35, 126, 0.25) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        white-space: nowrap;
        text-decoration: none !important;
    }

    .header-btn:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(26, 35, 126, 0.4) !important;
        color: #fff !important;
    }

    /* Modal Animation */
    .modal.fade .modal-dialog {
        transform: scale(0.9);
        transition: transform 0.3s ease-out;
    }
    .modal.show .modal-dialog {
        transform: scale(1);
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
                            <h2 class="content-header-title float-start mb-0">Offer Banners</h2>
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
                    <a href="{{ route('admin.offers.create') }}" class="header-btn ms-auto">
                        <i class="bi bi-plus-lg"></i> Add New Offer
                    </a>
                </div>
            </div>

            <div class="content-body">
                <div class="offer-card-grid">
                    @forelse($offers as $offer)
                        <div class="offer-banner-card">
                            <div class="card-media-wrapper">
                                @if($offer->media_type == 'image' && !empty($offer->media))
                                    <img src="{{ asset('uploads/offers/images/' . $offer->media[0]) }}" alt="{{ $offer->title }}">
                                @elseif($offer->media_type == 'video' && !empty($offer->media))
                                    <video muted loop onmouseover="this.play()" onmouseout="this.pause()">
                                        <source src="{{ asset('uploads/offers/videos/' . $offer->media[0]) }}" type="video/mp4">
                                    </video>
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted">
                                        <i class="bi bi-image display-4"></i>
                                    </div>
                                @endif

                                <div class="status-badge">
                                    @if($offer->status == 1)
                                        <span class="badge bg-success shadow-sm rounded-pill px-3">Active</span>
                                    @else
                                        <span class="badge bg-danger shadow-sm rounded-pill px-3">Inactive</span>
                                    @endif
                                </div>

                                <div class="media-overlay">
                                    <span class="badge-glass">
                                        <i class="bi {{ $offer->media_type == 'image' ? 'bi-image' : 'bi-play-circle' }}"></i> 
                                        {{ $offer->media ? count($offer->media) : 0 }} Files
                                    </span>
                                </div>
                            </div>

                            <div class="card-content">
                                <span class="offer-position">{{ ucfirst(str_replace('_', ' ', $offer->position)) }}</span>
                                <h5 class="offer-title">{{ $offer->title }}</h5>
                                
                                <div class="offer-meta">
                                    <span class="priority-badge">
                                        <i class="bi bi-sort-numeric-down"></i> Priority: {{ $offer->priority }}
                                    </span>
                                    <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> {{ $offer->created_at->format('d M') }}</small>
                                </div>

                                <div class="card-actions">
                                    <button class="btn-action-pill btn-view" data-id="{{ $offer->id }}" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="{{ route('admin.offers.edit', encryptId($offer->id)) }}" class="btn-action-pill" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button class="btn-action-pill status-change" data-id="{{ $offer->id }}" data-change-status="{{ $offer->status == 1 ? 0 : 1 }}" title="Toggle Status">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <button class="btn-action-pill btn-delete delete-single" data-id="{{ $offer->id }}" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="card p-5 shadow-sm border-0 rounded-4">
                                <i class="bi bi-inbox display-1 text-muted opacity-25"></i>
                                <h4 class="mt-3 text-muted">No offers found</h4>
                                <p class="text-muted small">Start by creating a new offer banner</p>
                                <a href="{{ route('admin.offers.create') }}" class="btn btn-primary mt-2 mx-auto" style="width: 200px;">
                                    Create First Offer
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div id="viewDetailsContent">
                    <div class="p-5 text-center">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Loading offer details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        var sweetalert_delete_title = "Remove Offer Banner?";
        var sweetalert_delete_text = "This banner will be permanently removed from the application.";
        var sweetalert_change_status = "Update Banner Status";
        var sweetalert_change_status_text = "Do you want to change the visibility of this banner?";
        var form_url = '/offers';

        // View Details Modal
        $(document).on('click', '.btn-view', function() {
            const id = $(this).data('id');
            $('#viewDetailsContent').html(`
                <div class="p-5 text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted font-weight-bold">Fetching latest data...</p>
                </div>
            `);
            $('#viewDetailsModal').modal('show');

            $.ajax({
                url: `/admin/offers-view/${id}`,
                type: 'GET',
                success: function(response) {
                    $('#viewDetailsContent').html(response);
                },
                error: function() {
                    $('#viewDetailsContent').html('<div class="p-5 text-center text-danger"><i class="bi bi-exclamation-triangle display-4"></i><p class="mt-2">Failed to load offer details. Please try again.</p></div>');
                }
            });
        });

        // Status Change
        $(document).on('click', '.status-change', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const status = $(this).data('change-status');
            
            Swal.fire({
                title: sweetalert_change_status,
                text: sweetalert_change_status_text,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, Update",
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ms-1' },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    loaderView();
                    axios.get(APP_URL + form_url + '/status/' + id + '/' + status).then(function(response) {
                        loaderHide();
                        Swal.fire({
                            title: 'Updated!',
                            text: response.data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        }).then(() => { window.location.reload(); });
                    }).catch(function(error) {
                        notificationToast(error.response.data.message, 'warning');
                        loaderHide();
                    });
                }
            });
        });

        // Delete Single
        $(document).on('click', '.delete-single', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            
            Swal.fire({
                title: sweetalert_delete_title,
                text: sweetalert_delete_text,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Remove It",
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-outline-secondary ms-1' },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    loaderView();
                    axios.delete(APP_URL + form_url + '/' + id).then(function(response) {
                        loaderHide();
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.data.message,
                            icon: 'success',
                            confirmButtonText: 'Great',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        }).then(() => { window.location.reload(); });
                    }).catch(function(error) {
                        notificationToast(error.response.data.message, 'warning');
                        loaderHide();
                    });
                }
            });
        });
    </script>
@endsection
