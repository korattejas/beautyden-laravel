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
                        <h2 class="content-header-title float-start mb-0">Edit Service Category</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{trans('admin_string.home')}}</a>
                                </li>
                                 <li class="breadcrumb-item">
                                    <a href="{{ route('admin.service-category.index') }}">Service Category</a>
                                </li>
                                <li class="breadcrumb-item active"><a
                                        href="#">Edit Service Category</a>
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
                                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="{{$category->id}}">
                                        <input type="hidden" id="form-method" value="edit">
                                        <div class="row row-sm">

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Service Type <span class="text-danger">*</span></label>
                                                    <select name="service_type_id" class="form-control" required>
                                                        <option value="">Select Service Type</option>
                                                        @foreach($serviceTypes as $type)
                                                            <option value="{{ $type->id }}" {{ $category->service_type_id == $type->id ? 'selected' : '' }}>
                                                                {{ $type->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>{{trans('admin_string.name')}}</label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{$category->name}}"
                                                        placeholder="{{trans('admin_string.name')}}" required>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Image</label>
                                                    @if(isset($category->icon) && !empty($category->icon))
                                                        <div class="mb-3">
                                                            <img src="{{ asset('uploads/service-category/' . $category->icon) }}"
                                                                alt="Category Image" style="width: 250px; height: auto;" />
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control filepond" name="icon">
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea class="form-control" name="description" rows="4"
                                                        placeholder="{{trans('admin_string.description')}}">{{($category->description ?? '')}}</textarea>

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
                                                            @if(isset($category->media_json['images']))
                                                                @foreach($category->media_json['images'] as $img)
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
                                                            @if(isset($category->media_json['videos']))
                                                                @foreach($category->media_json['videos'] as $vid)
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
                                                    <select id="status" name="status" class="form-control" required>
                                                        <option value="">{{trans('admin_string.select_status')}}
                                                        </option>
                                                        <option value="1" @if($category->status == '1') selected @endif>
                                                            Active</option>
                                                        <option value="0" @if($category->status == '0') selected @endif>
                                                            Inactive</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Priority Status</label>
                                                    <select id="is_popular" name="is_popular" class="form-control" required>
                                                        <option value="">
                                                            {{trans('admin_string.select_priority_status')}}
                                                        </option>
                                                        <option value="1" @if($category->is_popular == '1') selected @endif>
                                                            High Priority</option>
                                                        <option value="0" @if($category->is_popular == '0') selected @endif>
                                                            Low Priority</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Is New</label>
                                                    <select id="is_new" name="is_new" class="form-control" required>
                                                        <option value="">Is New</option>
                                                        <option value="1" @if($category->is_new == '1') selected @endif>
                                                            New</option>
                                                        <option value="0" @if($category->is_new == '0') selected @endif>
                                                            No</option>
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