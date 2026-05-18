@extends('admin.layouts.app')
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-body">
            <form method="POST" id="categoryForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="edit_value" value="0">
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h2 class="fw-bold mb-0">Create Product Category</h2>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.product-category.index') }}" class="btn btn-outline-secondary">Discard</a>
                        <button type="submit" class="btn btn-primary px-3 shadow">Save Category</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label">Category Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Skincare" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Category Image</label>
                                    <input type="file" class="form-control filepond" name="image">
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label">Featured</label>
                                        <div class="form-check form-switch mt-50">
                                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured">
                                            <label class="form-check-label" for="is_featured">Is Featured?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label">New</label>
                                        <div class="form-check form-switch mt-50">
                                            <input class="form-check-input" type="checkbox" name="is_new" value="1" id="is_new">
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
    var form_url = 'product-category/store';
    var redirect_url = 'product-category';

    $(function() {
        FilePond.registerPlugin(FilePondPluginImagePreview);
        FilePond.create($('.filepond')[0], {
            allowMultiple: false,
            instantUpload: false,
            allowProcess: false,
            storeAsFile: true,
            labelIdle: 'Drag & Drop or <span class="filepond--label-action">Browse</span>'
        });

        $('#categoryForm').on('submit', function(e) {
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
