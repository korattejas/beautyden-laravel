@extends('admin.layouts.app')

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Add New Offer</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.offers.index') }}">Offers</a></li>
                                    <li class="breadcrumb-item active">Create</li>
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
                                        <input type="hidden" name="edit_value" value="0">
                                        
                                        <div class="row row-sm">
                                            <!-- Title -->
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Offer Title</label>
                                                    <input type="text" class="form-control" name="title"
                                                        placeholder="Enter offer title (e.g. Summer Special 50% Off)" required>
                                                </div>
                                            </div>

                                            <!-- Position -->
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Display Position</label>
                                                    <select name="position" class="form-control" required>
                                                        <option value="top_header" selected>Top Header Slider</option>
                                                        <option value="footer">Footer Banner</option>
                                                        <option value="other">Other Page</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Media Type -->
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Media Type</label>
                                                    <select name="media_type" id="media_type" class="form-control" required>
                                                        <option value="image" selected>Images (Multiple)</option>
                                                        <option value="video">Video (Single)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="1" selected>Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Link & Priority -->
                                            <div class="col-md-8 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Redirect Link (Optional)</label>
                                                    <input type="url" class="form-control" name="link"
                                                        placeholder="https://example.com/promo">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Priority Order</label>
                                                    <input type="number" class="form-control" name="priority" value="0">
                                                </div>
                                            </div>

                                            <!-- Media Upload -->
                                            <div class="col-12 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold" id="media_label">Upload Media Assets</label>
                                                    <div class="upload-zone p-2 border rounded text-center bg-light">
                                                        <i class="bi bi-cloud-arrow-up text-primary fs-3"></i>
                                                        <h6 class="mt-1" id="upload_title">Select Multiple Images</h6>
                                                        <p class="text-muted small mb-1" id="upload_desc">Drag & drop or browse</p>
                                                        <input type="file" id="media_input" class="form-control" name="media[]" multiple accept="image/*" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2 text-end">
                                                <button type="submit" class="btn btn-primary px-3">
                                                    <i class="bi bi-check-lg"></i> Create Offer
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
                $('#upload_title').text('Select Single Video');
                $('#upload_desc').text('Supports MP4, MOV, AVI');
                $('#media_input').attr('name', 'media').removeAttr('multiple').attr('accept', 'video/*');
            } else {
                $('#upload_title').text('Select Multiple Images');
                $('#upload_desc').text('Drag & drop or browse');
                $('#media_input').attr('name', 'media[]').attr('multiple', 'multiple').attr('accept', 'image/*');
            }
        });
    </script>
@endsection

