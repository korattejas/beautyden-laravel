<div class="modal-header border-0 pb-0">
    <h5 class="modal-title fw-bolder text-primary" id="viewDetailsModalLabel">
        <i class="bi bi-info-circle me-1"></i> Offer Details
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row g-4">
        <!-- Title & Basic Info -->
        <div class="col-md-7">
            <div class="detail-card h-100 p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <h4 class="fw-bold text-dark mb-3">{{ $offer->title }}</h4>
                
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="badge bg-light-primary text-primary px-3 py-2 rounded-pill">
                        <i class="bi bi-geo-alt-fill me-1"></i> {{ ucfirst(str_replace('_', ' ', $offer->position)) }}
                    </span>
                    @if($offer->status == 1)
                        <span class="badge bg-light-success text-success px-3 py-2 rounded-pill">
                            <i class="bi bi-check-circle-fill me-1"></i> Active
                        </span>
                    @else
                        <span class="badge bg-light-danger text-danger px-3 py-2 rounded-pill">
                            <i class="bi bi-x-circle-fill me-1"></i> Inactive
                        </span>
                    @endif
                    <span class="badge bg-light-info text-info px-3 py-2 rounded-pill">
                        <i class="bi bi-sort-numeric-down me-1"></i> Priority: {{ $offer->priority }}
                    </span>
                </div>

                <div class="info-group mb-3">
                    <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Redirect Link</label>
                    @if($offer->link)
                        <a href="{{ $offer->link }}" target="_blank" class="text-primary fw-medium text-break">
                            {{ $offer->link }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    @else
                        <span class="text-muted italic">No link provided</span>
                    @endif
                </div>

                <div class="info-group">
                    <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Created At</label>
                    <span class="text-dark fw-medium">{{ $offer->created_at->format('d M Y, h:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Media Preview -->
        <div class="col-md-5">
            <div class="detail-card h-100 p-4 rounded-4" style="background: #ffffff; border: 1px solid #e2e8f0;">
                <label class="text-muted small text-uppercase fw-bold mb-3 d-block">Media Assets ({{ $offer->media ? count($offer->media) : 0 }})</label>
                
                <div class="media-container overflow-auto" style="max-height: 300px;">
                    @if($offer->media)
                        <div class="row g-2">
                            @foreach($offer->media as $item)
                                @if($offer->media_type == 'image')
                                    <div class="col-6">
                                        <div class="ratio ratio-1x1 rounded-3 overflow-hidden border">
                                            <img src="{{ asset('uploads/offers/images/'.$item) }}" class="img-fluid object-fit-cover hover-zoom" alt="Offer Image">
                                        </div>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden border bg-black">
                                            <video src="{{ asset('uploads/offers/videos/'.$item) }}" controls class="w-100 h-100"></video>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-image text-muted display-4"></i>
                            <p class="text-muted mt-2">No media found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer border-0">
    <button type="button" class="btn btn-light-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
    <a href="{{ route('admin.offers.edit', encryptId($offer->id)) }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-pencil-square me-1"></i> Edit Offer
    </a>
</div>

<style>
    .hover-zoom {
        transition: transform 0.3s ease;
        cursor: pointer;
    }
    .hover-zoom:hover {
        transform: scale(1.1);
    }
    .btn-light-secondary {
        background: #f1f5f9;
        color: #475569;
        border: none;
    }
    .btn-light-secondary:hover {
        background: #e2e8f0;
    }
</style>
