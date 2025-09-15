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
    </script>
@endsection