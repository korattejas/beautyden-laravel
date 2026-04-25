<div class="modal-header border-0 pb-0" style="background: #f8fafc;">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-2">
            <i class="bi bi-megaphone-fill text-primary fs-4"></i>
        </div>
        <div>
            <h5 class="modal-title fw-bolder text-dark mb-0">Offer Details</h5>
            <small class="text-muted">Quick summary of the banner</small>
        </div>
    </div>
    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4" style="background: #f8fafc;">
    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-light-primary text-primary px-3 py-2 rounded-pill fw-bold">
                            <i class="bi bi-geo-alt-fill me-1"></i> {{ ucfirst(str_replace('_', ' ', $offer->position)) }}
                        </span>
                        @if($offer->status == 1)
                            <span class="badge bg-light-success text-success px-3 py-2 rounded-pill fw-bold">
                                <i class="bi bi-check-circle-fill me-1"></i> Active
                            </span>
                        @else
                            <span class="badge bg-light-danger text-danger px-3 py-2 rounded-pill fw-bold">
                                <i class="bi bi-x-circle-fill me-1"></i> Inactive
                            </span>
                        @endif
                    </div>

                    <h3 class="fw-bold text-dark mb-4">{{ $offer->title }}</h3>
                    
                    <div class="info-row mb-4">
                        <label class="text-muted small text-uppercase fw-800 letter-spacing-1 d-block mb-2">Redirect Link</label>
                        @if($offer->link)
                            <div class="d-flex align-items-center bg-light p-3 rounded-3 border">
                                <i class="bi bi-link-45deg fs-4 text-primary me-2"></i>
                                <a href="{{ $offer->link }}" target="_blank" class="text-primary fw-bold text-decoration-none text-break">
                                    {{ $offer->link }}
                                </a>
                                <i class="bi bi-box-arrow-up-right ms-auto text-muted"></i>
                            </div>
                        @else
                            <div class="d-flex align-items-center bg-light p-3 rounded-3 border border-dashed">
                                <i class="bi bi-link-45deg fs-4 text-muted me-2"></i>
                                <span class="text-muted italic">No redirection link provided</span>
                            </div>
                        @endif
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Priority</label>
                                <span class="text-dark fw-bolder fs-5"><i class="bi bi-sort-numeric-down text-warning me-1"></i> {{ $offer->priority }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Created At</label>
                                <span class="text-dark fw-bold d-block small">{{ $offer->created_at->format('d M Y') }}</span>
                                <small class="text-muted">{{ $offer->created_at->format('h:i A') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Assets -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">Media Assets ({{ $offer->media ? count($offer->media) : 0 }})</h6>
                </div>
                <div class="card-body p-4">
                    <div class="media-scroll-area overflow-auto pe-1" style="max-height: 350px;">
                        @if($offer->media)
                            <div class="row g-3">
                                @foreach($offer->media as $item)
                                    <div class="col-6">
                                        <div class="premium-media-card rounded-3 overflow-hidden shadow-sm border">
                                            @if($offer->media_type == 'image')
                                                <a href="{{ asset('uploads/offers/images/'.$item) }}" target="_blank">
                                                    <img src="{{ asset('uploads/offers/images/'.$item) }}" class="img-fluid w-100 h-100 object-fit-cover" style="aspect-ratio: 1/1;">
                                                </a>
                                            @else
                                                <div class="ratio ratio-1x1 bg-black">
                                                    <video src="{{ asset('uploads/offers/videos/'.$item) }}" controls class="w-100 h-100"></video>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5 opacity-50">
                                <i class="bi bi-images display-3 d-block mb-2"></i>
                                <p class="mb-0">No media uploaded</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer border-0 p-4" style="background: #ffffff;">
    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Close</button>
    <a href="{{ route('admin.offers.edit', encryptId($offer->id)) }}" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
        <i class="bi bi-pencil-square me-2"></i> Edit This Offer
    </a>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .letter-spacing-1 { letter-spacing: 1px; }
    .premium-media-card {
        transition: all 0.3s ease;
        position: relative;
    }
    .premium-media-card:hover {
        transform: scale(1.05);
        z-index: 5;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .media-scroll-area::-webkit-scrollbar {
        width: 4px;
    }
    .media-scroll-area::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
</style>
