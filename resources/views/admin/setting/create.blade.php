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
                            <h2 class="content-header-title float-start mb-0">{{ trans('admin_string.add_setting') }}</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.setting.index') }}">{{ trans('admin_string.setting') }}</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ trans('admin_string.add_setting') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <section id="basic-input">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h4 class="card-title">Setting Details</h4>
                                </div>
                                <div class="card-body">
                                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="0">
                                        <input type="hidden" id="form-method" value="add">
                                        
                                        <div class="row row-sm">
                                            <div class="col-md-6 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">{{ trans('admin_string.screen_name') }}</label>
                                                    <input type="text" class="form-control" name="screen_name"
                                                        placeholder="{{ trans('admin_string.screen_name') }}">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">{{ trans('admin_string.key') }}</label>
                                                    <input type="text" class="form-control" name="key"
                                                        placeholder="{{ trans('admin_string.key') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-12 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">{{ trans('admin_string.value') }}</label>
                                                    <textarea class="form-control" name="value" rows="3" placeholder="Write a value" required></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">{{ trans('admin_string.status') }}</label>
                                                    <select id="status" name="status" class="form-control" required>
                                                        <option value="1" selected>Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Modern Single Image Upload -->
                                            <div class="col-12 mb-2">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">{{ trans('admin_string.image') }}</label>
                                                    <div class="upload-zone p-2 border rounded text-center bg-light">
                                                        <i class="bi bi-cloud-arrow-up text-primary fs-3"></i>
                                                        <h6 class="mt-1">Select Setting Image</h6>
                                                        <p class="text-muted small mb-1">Drag & drop or browse</p>
                                                        <input type="file" id="image_input" class="form-control" name="image" accept="image/*">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2 text-end">
                                                <button type="submit" class="btn btn-primary px-3">
                                                    <i class="bi bi-check-lg"></i> {{ trans('admin_string.submit') }}
                                                </button>
                                                <a href="{{ route('admin.setting.index') }}" class="btn btn-secondary px-3">
                                                    {{ trans('admin_string.cancel') }}
                                                </a>
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
        var form_url = 'setting/store';
        var redirect_url = 'setting';
    </script>
@endsection

