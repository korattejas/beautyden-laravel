@extends('admin.layouts.app')
@section('content')
<style>
    .premium-file-input { 
        position: relative; 
        border: 2px dashed #d1d5db; 
        border-radius: 12px; 
        padding: 20px; 
        text-align: center; 
        background: #fff; 
        cursor: pointer; 
        transition: all 0.3s; 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center;
        min-height: 150px;
    }
    .premium-file-input:hover { border-color: #6366f1; background: #f5f3ff; }
    .premium-file-input input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10; }
    .premium-file-input .placeholder-content { transition: all 0.3s; }
    .premium-file-input.has-preview .placeholder-content { display: none; }
    .preview-media { max-width: 100%; max-height: 200px; border-radius: 12px; object-fit: contain; }
</style>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Add Essential</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.service-essential.index') }}">Essentials</a></li>
                                <li class="breadcrumb-item active"><a href="#">Add Essential</a></li>
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
                                <form method="POST" data-parsley-validate="" id="addEditForm" role="form" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="0">
                                    <input type="hidden" id="form-method" value="add">
                                    <div class="row row-sm">
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" class="form-control" name="title" placeholder="Enter title (e.g. Waxing Kit)" required>
                                            </div>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Type / Category</label>
                                                <input type="text" class="form-control" name="type" placeholder="e.g. Overview, Protocol">
                                            </div>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Icon / Image</label>
                                                <input type="file" class="form-control filepond" name="icon">
                                            </div>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="1" selected>Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" class="btn btn-primary">Submit</button>
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
    var is_one_image_and_multiple_image_status = 'is_one_image';
</script>
@endsection
