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
                        <h2 class="content-header-title float-start mb-0">Add Service Category</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{trans('admin_string.home')}}</a>
                                </li>
                                 <li class="breadcrumb-item">
                                    <a href="{{ route('admin.service-category.index') }}">Service Category</a>
                                </li>
                                <li class="breadcrumb-item active"><a
                                        href="#">Add Service Category</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="content-body">
                <section class="horizontal-wizard">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data" data-parsley-validate="" id="addEditForm" role="form">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="0">
                                        <input type="hidden" id="form-method" value="add">
                                        <div class="row row-sm">
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Service Type <span class="text-danger">*</span></label>
                                                    <select name="service_type_id" class="form-control" required>
                                                        <option value="">Select Service Type</option>
                                                        @foreach($serviceTypes as $type)
                                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <input type="text" class="form-control" name="name"
                                                        placeholder="Name" required>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Icon</label>
                                                    <input type="file" class="filepond" name="icon">
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea class="form-control" name="description" rows="4"
                                                        placeholder="Description"></textarea>

                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <!-- Gallery Media -->
                                            <div class="col-12 mt-2">
                                                <div class="card border shadow-none">
                                                    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                                        <h4 class="card-title">Gallery Media (Images & Videos)</h4>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-image-row">+ Add Image</button>
                                                            <button type="button" class="btn btn-sm btn-outline-info" id="add-video-row">+ Add Video</button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body pt-2">
                                                        <div id="media-container" class="row">
                                                            <!-- Media rows will be added here -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select id="status" name="status" class="form-control" required>
                                                        <option value="">Status</option>
                                                        <option value="1" selected>Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Priority Status</label>
                                                    <select id="is_popular" name="is_popular" class="form-control" required>
                                                        <option value="">Priority Status</option>
                                                        <option value="1">High Priority</option>
                                                        <option value="0" selected>Low Priority</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Is New</label>
                                                    <select id="is_new" name="is_new" class="form-control" required>
                                                        <option value="">Is New</option>
                                                        <option value="1">New</option>
                                                        <option value="0" selected>No</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                    <div>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                        <a href="{{ route('admin.service-category.index') }}"
                                                            class="btn btn-secondary">{{ trans('admin_string.cancel') }}</a>
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
</div>
@endsection
@section('footer_script_content')
<script>
    var form_url = 'service-category/store';
    var redirect_url = 'service-category';
    var is_one_image_and_multiple_image_status = 'is_one_image';

        $(document).on('click', '#add-image-row', function() {
            var html = `
                <div class="col-md-3 mb-2 media-row animate__animated animate__fadeIn">
                    <div class="border rounded p-1 position-relative">
                        <button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-25 remove-media" style="z-index:10">
                            <i data-feather="x"></i>
                        </button>
                        <div class="text-center mb-1">
                            <i data-feather="image" class="text-primary" style="width: 48px; height: 48px;"></i>
                        </div>
                        <input type="file" name="gallery_images[]" class="form-control form-control-sm media-input" accept="image/*">
                        <div class="preview-container mt-1 text-center" style="display:none"></div>
                    </div>
                </div>`;
            $('#media-container').append(html);
            feather.replace();
        });

        $(document).on('click', '#add-video-row', function() {
            var html = `
                <div class="col-md-3 mb-2 media-row animate__animated animate__fadeIn">
                    <div class="border rounded p-1 position-relative">
                        <button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-25 remove-media" style="z-index:10">
                            <i data-feather="x"></i>
                        </button>
                        <div class="text-center mb-1">
                            <i data-feather="video" class="text-info" style="width: 48px; height: 48px;"></i>
                        </div>
                        <input type="file" name="gallery_videos[]" class="form-control form-control-sm media-input" accept="video/*">
                        <div class="preview-container mt-1 text-center" style="display:none"></div>
                    </div>
                </div>`;
            $('#media-container').append(html);
            feather.replace();
        });

        $(document).on('click', '.remove-media', function() {
            $(this).closest('.media-row').remove();
        });

        $(document).on('change', '.media-input', function() {
            var input = this;
            var container = $(this).siblings('.preview-container');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var html = '';
                    if (input.accept.includes('image')) {
                        html = `<img src="${e.target.result}" style="max-width: 100%; border-radius: 4px; max-height: 100px;">`;
                    } else {
                        html = `<video src="${e.target.result}" style="max-width: 100%; border-radius: 4px; max-height: 100px;" controls></video>`;
                    }
                    container.html(html).fadeIn();
                    $(input).siblings('.text-center').hide();
                }
                reader.readAsDataURL(input.files[0]);
            }
        });

    //   document.addEventListener('DOMContentLoaded', function () {
    //     // Select all file inputs with class 'filepond'
    //     FilePond.parse(document.body);

    //     // OR explicitly register
    //     const inputElement = document.querySelector('input[name="icon"]');
    //     FilePond.create(inputElement, {
    //         allowMultiple: false,
    //         instantUpload: false,
    //         storeAsFile: false,
    //         acceptedFileTypes: ['image/*'],
    //     });
    // });

</script>
@endsection
