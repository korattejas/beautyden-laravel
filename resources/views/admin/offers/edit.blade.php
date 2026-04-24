@extends('admin.layouts.app')

@section('header_style_content')
<style>
    .assets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .asset-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 1;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border: 2px solid #fff;
    }

    .asset-item img, .asset-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .remove-asset {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 38, 38, 0.9);
        color: #fff;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        font-size: 12px;
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
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Edit Offer</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.offers.index') }}">Offers</a></li>
                                    <li class="breadcrumb-item active">Edit</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <section id="basic-input">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h4 class="card-title">Offer Details</h4>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data" data-parsley-validate="" id="addEditForm" role="form">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="{{ $offer->id }}">
                                        
                                        <div class="row row-sm">
                                            <!-- Title -->
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Offer Title</label>
                                                    <input type="text" class="form-control" name="title"
                                                        value="{{ $offer->title }}" placeholder="Enter offer title" required>
                                                </div>
                                            </div>

                                            <!-- Position -->
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Display Position</label>
                                                    <select name="position" class="form-control" required>
                                                        <option value="top_header" {{ $offer->position == 'top_header' ? 'selected' : '' }}>Top Header Slider</option>
                                                        <option value="footer" {{ $offer->position == 'footer' ? 'selected' : '' }}>Footer Banner</option>
                                                        <option value="other" {{ $offer->position == 'other' ? 'selected' : '' }}>Other Page</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Media Type -->
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Media Type</label>
                                                    <select name="media_type" id="media_type" class="form-control" required>
                                                        <option value="image" {{ $offer->media_type == 'image' ? 'selected' : '' }}>Images (Multiple)</option>
                                                        <option value="video" {{ $offer->media_type == 'video' ? 'selected' : '' }}>Video (Single)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="1" {{ $offer->status == 1 ? 'selected' : '' }}>Active</option>
                                                        <option value="0" {{ $offer->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Link & Priority -->
                                            <div class="col-md-8 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Redirect Link (Optional)</label>
                                                    <input type="url" class="form-control" name="link"
                                                        value="{{ $offer->link }}" placeholder="https://example.com/promo">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Priority Order</label>
                                                    <input type="number" class="form-control" name="priority" value="{{ $offer->priority }}">
                                                </div>
                                            </div>

                                            <!-- Existing Media -->
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Current Assets</label>
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
                                                        <p class="text-muted small">No media uploaded yet.</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- New Media Upload -->
                                            <div class="col-12 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold" id="media_label">Add More Media</label>
                                                    <div class="upload-zone p-2 border rounded text-center bg-light">
                                                        <i class="bi bi-cloud-arrow-up text-primary fs-3"></i>
                                                        <h6 class="mt-1" id="upload_title">Select @if($offer->media_type == 'image') More Images @else New Video @endif</h6>
                                                        <p class="text-muted small mb-1" id="upload_desc">Drag & drop or browse</p>
                                                        <input type="file" id="media_input" class="form-control" name="{{ $offer->media_type == 'image' ? 'media[]' : 'media' }}" {{ $offer->media_type == 'image' ? 'multiple' : '' }} accept="{{ $offer->media_type == 'image' ? 'image/*' : 'video/*' }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2 text-end">
                                                <button type="submit" class="btn btn-primary px-3">
                                                    <i class="bi bi-check-lg"></i> Update Offer
                                                </button>
                                                <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary px-3">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
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
