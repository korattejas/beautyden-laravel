@extends('admin.layouts.app')

@section('header_style_content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    .form-section-title {
        color: #1a237e;
        font-weight: 800;
        letter-spacing: 0.5px;
        position: relative;
        padding-left: 15px;
        margin-bottom: 1.5rem;
    }
    .form-section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 20px;
        background: #1a237e;
        border-radius: 10px;
    }
    .premium-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }
    .asset-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: 2px solid #fff;
        background: #eee;
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
        background: #ef4444;
        color: #fff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px solid #fff;
        font-size: 14px;
        transition: all 0.2s;
        z-index: 5;
    }
    .remove-asset:hover {
        background: #b91c1c;
        transform: scale(1.15);
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

            <div class="content-body">
                <form method="POST" enctype="multipart/form-data" data-parsley-validate="" id="addEditForm" role="form">
                    @csrf
                    <input type="hidden" name="edit_value" value="{{ $offer->id }}">
                    <input type="hidden" id="form-method" value="edit">

                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Basic Info -->
                            <div class="card premium-card">
                                <div class="card-body p-4">
                                    <h5 class="form-section-title">Offer Details</h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold">Offer Title</label>
                                                <input type="text" class="form-control form-control-lg border-2" name="title"
                                                    value="{{ $offer->title }}" placeholder="e.g. Summer Special" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold">Display Position</label>
                                                <select name="position" class="form-select border-2" required>
                                                    <option value="top_header" {{ $offer->position == 'top_header' ? 'selected' : '' }}>Top Header Slider</option>
                                                    <option value="footer" {{ $offer->position == 'footer' ? 'selected' : '' }}>Footer Banner</option>
                                                    <option value="other" {{ $offer->position == 'other' ? 'selected' : '' }}>Other Page</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold">Priority Order</label>
                                                <input type="number" class="form-control border-2" name="priority" value="{{ $offer->priority }}">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold">Redirect Link</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-2 border-end-0"><i class="bi bi-link-45deg"></i></span>
                                                    <input type="url" class="form-control border-2 border-start-0" name="link"
                                                        value="{{ $offer->link }}" placeholder="https://beautyden.com/promo">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Visibility -->
                            <div class="card premium-card mt-3">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h5 class="mb-0 fw-bold">Visibility Status</h5>
                                            <p class="text-muted small mb-0">Manage if this banner is visible to users.</p>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input h4 mb-0" type="checkbox" name="status" value="1" {{ $offer->status == 1 ? 'checked' : '' }} id="statusSwitch">
                                                <label class="form-check-label fw-bold ms-1" for="statusSwitch">{{ $offer->status == 1 ? 'Active' : 'Inactive' }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Existing Media Gallery -->
                            <div class="card premium-card mt-3">
                                <div class="card-body p-4">
                                    <h5 class="form-section-title">Current Assets <small class="text-muted fw-normal ms-1">({{ count($offer->media ?? []) }} files)</small></h5>
                                    
                                    <div class="row g-3">
                                        @if($offer->media)
                                            @foreach($offer->media as $item)
                                                <div class="col-md-3 col-6" id="media-{{ md5($item) }}">
                                                    <div class="asset-item">
                                                        @if($offer->media_type == 'image')
                                                            <img src="{{ asset('uploads/offers/images/'.$item) }}" alt="Offer Media">
                                                        @else
                                                            <video src="{{ asset('uploads/offers/videos/'.$item) }}" muted loop onmouseover="this.play()" onmouseout="this.pause()"></video>
                                                            <div class="position-absolute bottom-0 end-0 p-1 bg-dark bg-opacity-50 text-white rounded-start" style="font-size: 10px;">VIDEO</div>
                                                        @endif
                                                        <button type="button" class="remove-asset" onclick="removeMedia('{{ $item }}')">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-12 text-center py-4 bg-light rounded-3 border-dashed">
                                                <i class="bi bi-images text-muted fs-2"></i>
                                                <p class="text-muted mb-0">No media assets found for this offer.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card premium-card sticky-top" style="top: 100px; z-index: 100;">
                                <div class="card-body p-4">
                                    <h5 class="form-section-title">Update Media</h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold d-block mb-1">Media Type</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="media_type" id="type_image" value="image" {{ $offer->media_type == 'image' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-primary" for="type_image">
                                                <i class="bi bi-images me-1"></i> Images
                                            </label>

                                            <input type="radio" class="btn-check" name="media_type" id="type_video" value="video" {{ $offer->media_type == 'video' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-primary" for="type_video">
                                                <i class="bi bi-play-circle me-1"></i> Video
                                            </label>
                                        </div>
                                    </div>

                                    <div id="image-upload-wrapper" style="{{ $offer->media_type == 'image' ? '' : 'display:none' }}">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Add More Images</label>
                                        <input type="file" class="filepond-multiple" name="media[]" multiple data-allow-reorder="true">
                                    </div>

                                    <div id="video-upload-wrapper" style="{{ $offer->media_type == 'video' ? '' : 'display:none' }}">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Replace Video</label>
                                        <input type="file" class="filepond-single" name="media">
                                    </div>

                                    <div class="mt-4 pt-2 border-top">
                                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm rounded-pill">
                                            <i class="bi bi-cloud-upload me-2"></i> Update Offer
                                        </button>
                                        <a href="{{ route('admin.offers.index') }}" class="btn btn-outline-secondary w-100 rounded-pill mt-2">
                                            Back to List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        var form_url = 'offers/store';
        var redirect_url = 'offers';
        
        $('input[name="media_type"]').on('change', function() {
            if ($(this).val() === 'video') {
                $('#image-upload-wrapper').hide();
                $('#video-upload-wrapper').show();
            } else {
                $('#image-upload-wrapper').show();
                $('#video-upload-wrapper').hide();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            FilePond.registerPlugin(FilePondPluginImagePreview);
            
            FilePond.create(document.querySelector('.filepond-multiple'), {
                allowMultiple: true,
                instantUpload: false,
                storeAsFile: true,
                acceptedFileTypes: ['image/*'],
                labelIdle: 'Drop new images here...'
            });

            FilePond.create(document.querySelector('.filepond-single'), {
                allowMultiple: false,
                instantUpload: false,
                storeAsFile: true,
                acceptedFileTypes: ['video/*'],
                labelIdle: 'Drop new video here...'
            });
        });

        function removeMedia(mediaName) {
            Swal.fire({
                title: 'Remove this media?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-outline-secondary ms-1' },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    loaderView();
                    $.ajax({
                        url: "{{ route('admin.offers.removeMedia') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: "{{ $offer->id }}",
                            media: mediaName
                        },
                        success: function(response) {
                            loaderHide();
                            if(response.success) {
                                notificationToast(response.message, 'success');
                                location.reload();
                            } else {
                                notificationToast(response.message, 'error');
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection
