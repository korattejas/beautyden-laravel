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
    .filepond--root {
        margin-bottom: 0;
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
                    <h2 class="content-header-title float-start mb-0">Create New Offer</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.offers.index') }}">Offers</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <form method="POST" enctype="multipart/form-data" data-parsley-validate="" id="addEditForm" role="form">
                    @csrf
                    <input type="hidden" name="edit_value" value="0">
                    <input type="hidden" id="form-method" value="add">

                    <div class="row">
                        <!-- Left Side: Basic Info -->
                        <div class="col-lg-8">
                            <div class="card premium-card">
                                <div class="card-body p-4">
                                    <h5 class="form-section-title">General Information</h5>
                                    
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold">Offer Title</label>
                                                <input type="text" class="form-control form-control-lg border-2" name="title"
                                                    placeholder="e.g. Summer Special 50% Off" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold">Display Position</label>
                                                <select name="position" class="form-select border-2" required>
                                                    <option value="top_header" selected>Top Header Slider</option>
                                                    <option value="footer">Footer Banner</option>
                                                    <option value="other">Other Page</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold">Priority Order</label>
                                                <input type="number" class="form-control border-2" name="priority" value="0">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold">Redirect Link <small class="text-muted text-capitalize">(Optional)</small></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-2 border-end-0"><i class="bi bi-link-45deg"></i></span>
                                                    <input type="url" class="form-control border-2 border-start-0" name="link"
                                                        placeholder="https://beautyden.com/offers/summer-special">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card premium-card mt-3">
                                <div class="card-body p-4">
                                    <h5 class="form-section-title">Visibility Settings</h5>
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <p class="text-muted small mb-0">Switch off to keep this offer as a draft.</p>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input h4 mb-0" type="checkbox" name="status" value="1" checked id="statusSwitch">
                                                <label class="form-check-label fw-bold ms-1" for="statusSwitch">Active Status</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Media -->
                        <div class="col-lg-4">
                            <div class="card premium-card">
                                <div class="card-body p-4">
                                    <h5 class="form-section-title">Media Assets</h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold d-block mb-1">Select Media Type</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="media_type" id="type_image" value="image" checked>
                                            <label class="btn btn-outline-primary" for="type_image">
                                                <i class="bi bi-images me-1"></i> Images
                                            </label>

                                            <input type="radio" class="btn-check" name="media_type" id="type_video" value="video">
                                            <label class="btn btn-outline-primary" for="type_video">
                                                <i class="bi bi-play-circle me-1"></i> Video
                                            </label>
                                        </div>
                                    </div>

                                    <div id="image-upload-wrapper">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Upload Multiple Images</label>
                                        <input type="file" class="filepond-multiple" name="media[]" multiple data-allow-reorder="true">
                                        <p class="text-muted x-small mt-1 mb-0"><i class="bi bi-info-circle me-1"></i> Recommended: 1200x600px for banners.</p>
                                    </div>

                                    <div id="video-upload-wrapper" style="display: none;">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Upload Single Video</label>
                                        <input type="file" class="filepond-single" name="media">
                                        <p class="text-muted x-small mt-1 mb-0"><i class="bi bi-info-circle me-1"></i> Max 20MB. MP4 format recommended.</p>
                                    </div>

                                    <div class="mt-4 pt-2 border-top">
                                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm rounded-pill">
                                            <i class="bi bi-check-circle me-2"></i> Create Offer
                                        </button>
                                        <a href="{{ route('admin.offers.index') }}" class="btn btn-outline-secondary w-100 rounded-pill mt-2">
                                            Cancel
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
        
        // Handle Media Type Toggle
        $('input[name="media_type"]').on('change', function() {
            if ($(this).val() === 'video') {
                $('#image-upload-wrapper').hide();
                $('#video-upload-wrapper').show();
            } else {
                $('#image-upload-wrapper').show();
                $('#video-upload-wrapper').hide();
            }
        });

        // Initialize FilePond
        document.addEventListener('DOMContentLoaded', function() {
            FilePond.registerPlugin(FilePondPluginImagePreview);
            
            // Multiple Images
            FilePond.create(document.querySelector('.filepond-multiple'), {
                allowMultiple: true,
                instantUpload: false,
                storeAsFile: true,
                acceptedFileTypes: ['image/*'],
                labelIdle: 'Drag & Drop your images or <span class="filepond--label-action">Browse</span>'
            });

            // Single Video
            FilePond.create(document.querySelector('.filepond-single'), {
                allowMultiple: false,
                instantUpload: false,
                storeAsFile: true,
                acceptedFileTypes: ['video/*'],
                labelIdle: 'Drag & Drop your video or <span class="filepond--label-action">Browse</span>'
            });
        });
    </script>
@endsection
