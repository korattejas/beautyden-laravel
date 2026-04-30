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
                            <h2 class="content-header-title float-start mb-0">Edit Service Sub Category</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a></li>
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('admin.service-subcategory.index') }}">Service Sub Category</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit Service Sub Category</li>
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
                                        <input type="hidden" name="edit_value" value="{{ $subcategory->id }}">
                                        <input type="hidden" id="form-method" value="edit">
                                        <div class="row row-sm">

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Category</label>
                                                    <select name="service_category_id" class="form-control select2">
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}" {{ $subcategory->service_category_id == $cat->id ? 'selected' : '' }}>
                                                                {{ $cat->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{ old('name', $subcategory->name) }}"
                                                        placeholder="Sub Category Name" required>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Starting Price (₹)</label>
                                                    <input type="number" step="0.01" class="form-control" name="starting_at_price"
                                                        value="{{ old('starting_at_price', $subcategory->starting_at_price) }}"
                                                        placeholder="0.00" required>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Icon</label>
                                                    @if(!empty($subcategory->icon))
                                                        <div class="mb-3">
                                                            <img src="{{ asset('uploads/service-subcategory/' . $subcategory->icon) }}"
                                                                alt="SubCategory Image" style="width: 250px; height: auto;" />
                                                        </div>
                                                    @endif
                                                    <input type="file" class="filepond" name="icon">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea class="form-control" name="description"
                                                        rows="4">{{ old('description', $subcategory->description) }}</textarea>
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
                                                            @if(isset($subcategory->media_json['images']))
                                                                @foreach($subcategory->media_json['images'] as $img)
                                                                    <div class="col-md-3 mb-2 media-row">
                                                                        <div class="border rounded p-1 position-relative">
                                                                            <button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-25 remove-existing-media" data-name="{{ $img }}">
                                                                                <i data-feather="x"></i>
                                                                            </button>
                                                                            <div class="text-center">
                                                                                <img src="{{ asset('uploads/service-media/' . $img) }}" style="max-width: 100%; border-radius: 4px; max-height: 100px;">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                            @if(isset($subcategory->media_json['videos']))
                                                                @foreach($subcategory->media_json['videos'] as $vid)
                                                                    <div class="col-md-3 mb-2 media-row">
                                                                        <div class="border rounded p-1 position-relative">
                                                                            <button type="button" class="btn btn-sm btn-icon btn-flat-danger position-absolute top-0 end-0 m-25 remove-existing-media" data-name="{{ $vid }}">
                                                                                <i data-feather="x"></i>
                                                                            </button>
                                                                            <div class="text-center">
                                                                                <video src="{{ asset('uploads/service-media/' . $vid) }}" style="max-width: 100%; border-radius: 4px; max-height: 100px;" controls></video>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                        <div id="removed-media-container"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="">Select Status</option>
                                                        <option value="1" {{ $subcategory->status == '1' ? 'selected' : '' }}>
                                                            Active</option>
                                                        <option value="0" {{ $subcategory->status == '0' ? 'selected' : '' }}>
                                                            Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Priority Status</label>
                                                    <select name="is_popular" class="form-control" required>
                                                        <option value="">Select Priority</option>
                                                        <option value="1" {{ $subcategory->is_popular == '1' ? 'selected' : '' }}>High Priority</option>
                                                        <option value="0" {{ $subcategory->is_popular == '0' ? 'selected' : '' }}>Low Priority</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12 text-end mt-3">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                                <a href="{{ route('admin.service-subcategory.index') }}"
                                                    class="btn btn-secondary">Cancel</a>
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

        $(document).on('click', '.remove-existing-media', function() {
            var name = $(this).data('name');
            $('#removed-media-container').append(`<input type="hidden" name="removed_media[]" value="${name}">`);
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