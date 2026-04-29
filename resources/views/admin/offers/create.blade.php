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
                        <h2 class="content-header-title float-start mb-0">Add Offer</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.offers.index') }}">Offers</a>
                                </li>
                                <li class="breadcrumb-item active">Add Offer</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section class="horizontal-wizard">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="addEditForm" enctype="multipart/form-data" data-parsley-validate="" role="form">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="0">
                                    <input type="hidden" id="form-method" value="add">
                                    <div class="row row-sm">

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Offer Title</label>
                                                <input type="text" name="title" class="form-control" placeholder="e.g. Summer Special 50% Off" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label>Display Position</label>
                                                <select name="position" class="form-control" required>
                                                    <option value="top_header" selected>Top Header Slider</option>
                                                    <option value="footer">Footer Banner</option>
                                                    <option value="other">Other Page</option>
                                                </select>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label>Priority Order</label>
                                                <input type="number" name="priority" class="form-control" value="0">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Redirect Link (Optional)</label>
                                                <input type="url" name="link" class="form-control" placeholder="https://beautyden.com/promo">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Media Type</label>
                                                <div class="mt-1">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="media_type" id="type_image" value="image" checked>
                                                        <label class="form-check-label" for="type_image">Images</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="media_type" id="type_video" value="video">
                                                        <label class="form-check-label" for="type_video">Video</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2" id="image-upload-wrapper">
                                            <div class="form-group">
                                                <label>Upload Multiple Images</label>
                                                <input type="file" class="filepond-multiple" multiple data-allow-reorder="true">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2" id="video-upload-wrapper" style="display: none;">
                                            <div class="form-group">
                                                <label>Upload Single Video</label>
                                                <input type="file" class="filepond-single">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                    <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">Cancel</a>
                                                </div>
                                            </div>
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
    var media_field_name = 'media[]';
    var is_one_image_and_multiple_image_status = 'is_multiple_image';

    $(document).ready(function() {
        FilePond.registerPlugin(FilePondPluginImagePreview);
        
        let imagePond = FilePond.create(document.querySelector('.filepond-multiple'), {
            allowMultiple: true,
            instantUpload: false,
            allowProcess: false,
        });

        let videoPond = FilePond.create(document.querySelector('.filepond-single'), {
            allowMultiple: false,
            instantUpload: false,
            allowProcess: false,
        });

        // Initially pond is imagePond
        pond = imagePond;

        $('input[name="media_type"]').on('change', function() {
            if ($(this).val() === 'video') {
                $('#image-upload-wrapper').hide();
                $('#video-upload-wrapper').show();
                pond = videoPond;
                media_field_name = 'media';
                is_one_image_and_multiple_image_status = 'is_one_image';
            } else {
                $('#image-upload-wrapper').show();
                $('#video-upload-wrapper').hide();
                pond = imagePond;
                media_field_name = 'media[]';
                is_one_image_and_multiple_image_status = 'is_multiple_image';
            }
        });
    });
</script>
@endsection
