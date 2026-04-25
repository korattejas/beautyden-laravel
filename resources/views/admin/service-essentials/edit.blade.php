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
                        <h2 class="content-header-title float-start mb-0">Edit Essential</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.service-essential.index') }}">Essentials</a></li>
                                <li class="breadcrumb-item active"><a href="#">Edit Essential</a></li>
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
                                <form method="POST" id="essentialForm" role="form" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="{{ $essential->id }}">
                                    <input type="hidden" id="form-method" value="edit">
                                    <div class="row row-sm">
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" class="form-control" name="title" value="{{ $essential->title }}" placeholder="Enter title" required>
                                            </div>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Type / Category</label>
                                                <input type="text" class="form-control" name="type" value="{{ $essential->type }}" placeholder="e.g. Overview, Protocol">
                                            </div>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Icon / Image</label>
                                                <input type="file" class="form-control filepond" name="icon">
                                                @if($essential->icon)
                                                    <img src="{{ asset('uploads/essential/' . $essential->icon) }}" class="mt-1" style="max-width: 100px;">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="1" {{ $essential->status == 1 ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ $essential->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <a href="{{ route('admin.service-essential.index') }}" class="btn btn-secondary">Cancel</a>
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
    var form_url = 'service-essential/store';
    var redirect_url = 'service-essential';

    $(function() {
        FilePond.registerPlugin(FilePondPluginImagePreview);
        $('.filepond').each(function() {
            FilePond.create(this, {
                allowMultiple: false,
                instantUpload: false,
                allowProcess: false,
                storeAsFile: true,
                labelIdle: 'Drag & Drop or <span class="filepond--label-action">Browse</span>'
            });
        });

        $('#essentialForm').on('submit', function(e) {
            e.preventDefault();
            loaderView();
            let formData = new FormData(this);
            axios.post(APP_URL + '/' + form_url, formData)
                .then(res => {
                    notificationToast(res.data.message, 'success');
                    setTimeout(() => window.location.href = APP_URL + '/' + redirect_url, 1000);
                })
                .catch(err => {
                    loaderHide();
                    notificationToast(err.response?.data?.message || 'Something went wrong', 'warning');
                });
        });
    });
</script>
@endsection
