@extends('admin.layouts.app')

@section('header_style_content')
<style>
    :root {
        --mst-indigo: #102365;
        --mst-indigo-light: #f5f7ff;
        --mst-text-main: #1e293b;
        --mst-text-muted: #64748b;
        --mst-bg-body: #f8fafc;
        --mst-border: #e2e8f0;
    }

    .premium-create-container {
        padding: 2rem;
        background: var(--mst-bg-body);
        min-height: calc(100vh - 60px);
    }

    .create-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .create-title h2 {
        font-weight: 800;
        color: var(--mst-indigo);
        font-size: 1.75rem;
        margin: 0;
    }

    .create-form-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--mst-border);
        padding: 2.5rem;
        max-width: 1000px;
        margin: 0 auto;
    }

    .form-label-luxury {
        font-weight: 700;
        color: var(--mst-text-main);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        display: block;
    }

    .luxury-input {
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        padding: 12px 16px;
        font-size: 1rem;
        transition: 0.3s;
        background: #fbfcfe;
    }

    .luxury-input:focus {
        border-color: var(--mst-indigo);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(16, 35, 101, 0.08);
    }

    .btn-submit-luxury {
        background: var(--mst-indigo);
        color: #fff;
        padding: 14px 32px;
        border-radius: 12px;
        font-weight: 700;
        border: none;
        transition: 0.3s;
        box-shadow: 0 4px 12px rgba(16, 35, 101, 0.2);
    }

    .btn-submit-luxury:hover {
        background: #0a1740;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 35, 101, 0.3);
        color: #fff;
    }

    .upload-zone {
        background: #f1f5f9;
        border-radius: 16px;
        border: 2px dashed #cbd5e1;
        transition: 0.3s;
    }

    .assets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .asset-item {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        aspect-ratio: 1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: 2px solid #fff;
    }

    .asset-item img, .asset-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .remove-asset {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(220, 38, 38, 0.9);
        color: #fff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        backdrop-filter: blur(4px);
        transition: 0.3s;
    }

    .remove-asset:hover {
        background: #dc2626;
        transform: scale(1.1);
    }
</style>
@endsection

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="premium-create-container">
                
                <div class="create-card-header">
                    <div class="create-title">
                        <h2>Edit Offer: {{ $offer->title }} ✏️</h2>
                    </div>
                    <a href="{{ route('admin.offers.index') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left"></i> Back to Offers
                    </a>
                </div>

                <div class="create-form-card">
                    <form method="POST" enctype="multipart/form-data" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" name="edit_value" value="{{ $offer->id }}">
                        
                        <div class="row">
                            <!-- Title -->
                            <div class="col-md-12 mb-4">
                                <label class="form-label-luxury">Offer Title</label>
                                <input type="text" class="form-control luxury-input" name="title"
                                    value="{{ $offer->title }}" placeholder="Enter offer title" required>
                            </div>

                            <!-- Position -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label-luxury">Display Position</label>
                                <select name="position" class="form-control luxury-input" required>
                                    <option value="top_header" {{ $offer->position == 'top_header' ? 'selected' : '' }}>Top Header Slider</option>
                                    <option value="footer" {{ $offer->position == 'footer' ? 'selected' : '' }}>Footer Banner</option>
                                    <option value="other" {{ $offer->position == 'other' ? 'selected' : '' }}>Other Page</option>
                                </select>
                            </div>

                            <!-- Media Type -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label-luxury">Media Type</label>
                                <select name="media_type" id="media_type" class="form-control luxury-input" required>
                                    <option value="image" {{ $offer->media_type == 'image' ? 'selected' : '' }}>Images (Multiple)</option>
                                    <option value="video" {{ $offer->media_type == 'video' ? 'selected' : '' }}>Video (Single)</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="col-md-4 mb-4">
                                <label class="form-label-luxury">Status</label>
                                <select name="status" class="form-control luxury-input" required>
                                    <option value="1" {{ $offer->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $offer->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <!-- Link & Priority -->
                            <div class="col-md-8 mb-4">
                                <label class="form-label-luxury">Redirect Link (Optional)</label>
                                <input type="url" class="form-control luxury-input" name="link"
                                    value="{{ $offer->link }}" placeholder="https://example.com/promo">
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label-luxury">Priority Order</label>
                                <input type="number" class="form-control luxury-input" name="priority" value="{{ $offer->priority }}">
                            </div>

                            <!-- Existing Media -->
                            <div class="col-12 mb-4">
                                <label class="form-label-luxury">Current Assets</label>
                                <div class="assets-grid" id="existing-assets">
                                    @if($offer->media)
                                        @foreach($offer->media as $item)
                                            <div class="asset-item" id="media-{{ md5($item) }}">
                                                @if($offer->media_type == 'image')
                                                    <img src="{{ asset('uploads/offers/images/'.$item) }}" alt="Offer Media">
                                                @else
                                                    <video src="{{ asset('uploads/offers/videos/'.$item) }}" muted></video>
                                                @endif
                                                <button type="button" class="remove-asset" onclick="removeMedia('{{ $item }}')">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No media uploaded yet.</p>
                                    @endif
                                </div>
                            </div>

                            <!-- New Media Upload -->
                            <div class="col-12 mb-4">
                                <label class="form-label-luxury">Add More Media</label>
                                <div class="upload-zone p-5 text-center">
                                    <i class="bi bi-cloud-arrow-up-fill mb-3" style="font-size: 3rem; color: var(--mst-indigo);"></i>
                                    <h5 class="mb-3" id="upload_title">Drag & Drop new images here</h5>
                                    <p class="text-muted mb-4 small" id="upload_desc">Or click to browse from your device</p>
                                    
                                    <input type="file" id="media_input" class="form-control" name="{{ $offer->media_type == 'image' ? 'media[]' : 'media' }}" {{ $offer->media_type == 'image' ? 'multiple' : '' }} accept="{{ $offer->media_type == 'image' ? 'image/*' : 'video/*' }}">
                                </div>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-submit-luxury">
                                    Update Offer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        var form_url = 'offers/store';
        var redirect_url = 'offers';
        
        $('#media_type').on('change', function() {
            const type = $(this).val();
            if (type === 'video') {
                $('#upload_title').text('Drag & Drop video here');
                $('#upload_desc').text('Or click to browse video');
                $('#media_input').attr('name', 'media').removeAttr('multiple').attr('accept', 'video/*');
            } else {
                $('#upload_title').text('Drag & Drop images here');
                $('#upload_desc').text('Or click to browse images');
                $('#media_input').attr('name', 'media[]').attr('multiple', 'multiple').attr('accept', 'image/*');
            }
        });

        function removeMedia(mediaName) {
            if(confirm('Are you sure you want to remove this media?')) {
                $.ajax({
                    url: "{{ route('admin.offers.removeMedia') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: "{{ $offer->id }}",
                        media: mediaName
                    },
                    success: function(response) {
                        if(response.success) {
                            // Find element and remove
                            location.reload(); // Simple way to refresh assets
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        }
    </script>
@endsection
