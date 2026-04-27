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
                            <h2 class="content-header-title float-start mb-0">Add App Setting</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.app-setting.index') }}">App Setting</a>
                                    </li>
                                    <li class="breadcrumb-item active">Add App Setting</li>
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
                                    <h4 class="card-title">App Setting Details</h4>
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
                                                    <input type="file" class="filepond" name="image" accept="image/*">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2 text-end">
                                                <button type="submit" class="btn btn-primary px-3">
                                                    <i class="bi bi-check-lg"></i> {{ trans('admin_string.submit') }}
                                                </button>
                                                <a href="{{ route('admin.app-setting.index') }}" class="btn btn-secondary px-3">
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
        var form_url = 'app-setting/store';
        var redirect_url = 'app-setting';

        $(function() {
            FilePond.registerPlugin(FilePondPluginImagePreview);
            FilePond.create(document.querySelector('.filepond'), {
                allowMultiple: false,
                allowImagePreview: true,
                imagePreviewHeight: 150,
                credits: false
            });
        });
    </script>
@endsection
