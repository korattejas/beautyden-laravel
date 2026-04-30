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
                            <h2 class="content-header-title float-start mb-0">Add Service Subcategory</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.service-subcategory.index') }}">Service Subcategory</a>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        <a href="#">Add Service Subcategory</a>
                                    </li>
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
                                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="0">
                                        <input type="hidden" id="form-method" value="add">

                                        <div class="row row-sm">

                                            <!-- Parent Category -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Parent Category</label>
                                                    <select name="service_category_id" class="form-control select2">
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <!-- Subcategory Name -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <input type="text" class="form-control" name="name"
                                                        placeholder="Subcategory Name" required>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <!-- Starting Price -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Starting Price (₹)</label>
                                                    <input type="number" step="0.01" class="form-control" name="starting_at_price"
                                                        placeholder="0.00" required>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Icon</label>
                                                    <input type="file" class="form-control filepond" name="icon">
                                                </div>
                                            </div>

                                            <!-- Description -->
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

                                            <!-- Status -->
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

                                            <!-- Priority Status -->
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

                                           

                                            <!-- Submit -->
                                            <div class="col-12">
                                                <div class="form-group mb-0 mt-3 justify-content-end"
                                                    style="text-align: right;">
                                                    <div>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                        <a href="{{ route('admin.service-subcategory.index') }}"
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
@endsection

@section('footer_script_content')
    <script>
        var form_url = 'service-subcategory/store';
        var redirect_url = 'service-subcategory';
        var is_one_image_and_multiple_image_status = 'is_one_image';

        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true,
            width: '100%'
        });

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
    </script>
@endsection