<div class="modal-header border-0 pb-0" style="background: #ffffff;">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-2">
            <i class="bi bi-megaphone-fill text-primary fs-5"></i>
        </div>
        <div>
            <h5 class="modal-title fw-bolder text-dark mb-0">Offer Details</h5>
        </div>
    </div>
    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-3" style="background: #ffffff;">
    <div class="row g-3">
        <!-- Main Info -->
        <div class="col-md-12">
            <div class="card border-0 shadow-none bg-light rounded-4 overflow-hidden mb-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-white text-primary border px-2 py-1 rounded-pill fw-bold small">
                            <i class="bi bi-geo-alt-fill me-1"></i> {{ ucfirst(str_replace('_', ' ', $offer->position)) }}
                        </span>
                        @if($offer->status == 1)
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill fw-bold small">
                                <i class="bi bi-check-circle-fill me-1"></i> Active
                            </span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill fw-bold small">
                                <i class="bi bi-x-circle-fill me-1"></i> Inactive
                            </span>
                        @endif
                    </div>

                    <h4 class="fw-bold text-dark mb-3">{{ $offer->title }}</h4>
                    
                    <div class="info-row mb-3">
                        <label class="text-muted small text-uppercase fw-800 ls-1 d-block mb-1">Redirect Link</label>
                        @if($offer->link)
                            <div class="d-flex align-items-center bg-white p-2 rounded-3 border">
                                <i class="bi bi-link-45deg fs-5 text-primary me-2"></i>
                                <a href="{{ $offer->link }}" target="_blank" class="text-primary fw-bold text-decoration-none text-break small">
                                    {{ $offer->link }}
                                </a>
                                <i class="bi bi-box-arrow-up-right ms-auto text-muted small"></i>
                            </div>
                        @else
                            <div class="d-flex align-items-center bg-white p-2 rounded-3 border border-dashed text-center justify-content-center py-3">
                                <span class="text-muted italic small">No redirection link provided</span>
                            </div>
                        @endif
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 bg-white rounded-3 border">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-0" style="font-size: 10px;">Priority</label>
                                <span class="text-dark fw-bolder small"><i class="bi bi-sort-numeric-down text-warning me-1"></i> {{ $offer->priority }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-white rounded-3 border">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-0" style="font-size: 10px;">Date Added</label>
                                <span class="text-dark fw-bold small">{{ $offer->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Assets -->
        <div class="col-md-12">
            <div class="card border-0 shadow-none bg-white rounded-4 overflow-hidden border mt-1">
                <div class="card-header bg-white border-0 pt-3 px-3 pb-0">
                    <h6 class="fw-bold text-dark mb-0">Media Assets ({{ $offer->media ? count($offer->media) : 0 }})</h6>
                </div>
                <div class="card-body p-3">
                    <div class="media-container pe-1">
                        @if($offer->media)
                            <div class="row g-2">
                                @foreach($offer->media as $item)
                                    <div class="col-4 col-md-3">
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
                            <div class="text-center py-4 opacity-50">
                                <i class="bi bi-images display-6 d-block mb-2"></i>
                                <p class="mb-0 small">No media uploaded</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer border-0 p-3 pt-0" style="background: #ffffff;">
    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold small" data-bs-dismiss="modal">Close</button>
    <a href="{{ route('admin.offers.edit', encryptId($offer->id)) }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm small">
        <i class="bi bi-pencil-square me-2"></i> Edit Offer
    </a>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .ls-1 { letter-spacing: 0.5px; }
    .premium-media-card {
        transition: all 0.3s ease;
        position: relative;
    }
    .premium-media-card:hover {
        transform: scale(1.05);
        z-index: 5;
    }
    .media-container::-webkit-scrollbar {
        width: 4px;
    }
    .media-container::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
</style>
