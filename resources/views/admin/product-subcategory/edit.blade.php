@extends('admin.layouts.app')
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <form method="POST" id="subcategoryForm">
                @csrf
                <input type="hidden" name="edit_value" value="{{ $subcategory->id }}">
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h2 class="fw-bold mb-0">Edit Product Sub-Category</h2>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.product-subcategory.index') }}" class="btn btn-outline-secondary">Discard</a>
                        <button type="submit" class="btn btn-primary px-3 shadow">Update Sub-Category</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-select select2" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $subcategory->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Sub-Category Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $subcategory->name }}" placeholder="e.g. Lipstick" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="1" {{ $subcategory->status == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ $subcategory->status == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label">Featured</label>
                                        <div class="form-check form-switch mt-50">
                                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ $subcategory->is_featured ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">Is Featured?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label">New</label>
                                        <div class="form-check form-switch mt-50">
                                            <input class="form-check-input" type="checkbox" name="is_new" value="1" id="is_new" {{ $subcategory->is_new ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_new">Is New?</label>
                                        </div>
                                    </div>
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
    var form_url = 'product-subcategory/store';
    var redirect_url = 'product-subcategory';

    $(function() {
        $('.select2').select2({ width: '100%' });

        $('#subcategoryForm').on('submit', function(e) {
            e.preventDefault();
            loaderView();
            let formData = new FormData(this);
            axios.post(APP_URL + '/admin/' + form_url, formData)
                .then(res => {
                    notificationToast(res.data.message, 'success');
                    setTimeout(() => window.location.href = APP_URL + '/admin/' + redirect_url, 1000);
                })
                .catch(err => {
                    loaderHide();
                    notificationToast(err.response?.data?.message || 'Something went wrong', 'warning');
                });
        });
    });
</script>
@endsection
