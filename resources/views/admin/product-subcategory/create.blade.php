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
                            <h2 class="content-header-title float-start mb-0">Create Product Sub-Category</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.product-subcategory.index') }}">Product Sub-Categories</a></li>
                                    <li class="breadcrumb-item active"><a href="#">Create Product Sub-Category</a></li>
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
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="0">
                                        <input type="hidden" id="form-method" value="add">
                                        
                                        <div class="row row-sm">
                                            <div class="col-12">
                                                <div class="form-group mb-2">
                                                    <label class="form-label">Category</label>
                                                    <select name="category_id" class="form-select select2" required>
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group mb-2">
                                                    <label class="form-label">Sub-Category Name</label>
                                                    <input type="text" name="name" class="form-control" placeholder="e.g. Lipstick" required>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="">{{ trans('admin_string.select_status') }}</option>
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label class="form-label">Featured</label>
                                                    <div class="form-check form-switch mt-50">
                                                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured">
                                                        <label class="form-check-label" for="is_featured">Is Featured?</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label class="form-label">New</label>
                                                    <div class="form-check form-switch mt-50">
                                                        <input class="form-check-input" type="checkbox" name="is_new" value="1" id="is_new">
                                                        <label class="form-check-label" for="is_new">Is New?</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <div class="form-group mb-0 justify-content-end" style="text-align: right;">
                                                    <button type="submit" class="btn btn-primary px-3 shadow">{{ trans('admin_string.submit') }}</button>
                                                    <a href="{{ route('admin.product-subcategory.index') }}" class="btn btn-secondary">{{ trans('admin_string.cancel') }}</a>
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
    var form_url = 'product-subcategory/store';
    var redirect_url = 'product-subcategory';

    $(function() {
        $('.select2').select2({ width: '100%' });
    });
</script>
@endsection
