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
                            <h2 class="content-header-title float-start mb-0">Add Portfolio</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.portfolio.index') }}">Portfolio</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Add Portfolio</a>
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
                                        <form method="POST" enctype="multipart/form-data" data-parsley-validate=""
                                            id="addEditForm" role="form">
                                            @csrf
                                            <input type="hidden" name="edit_value" value="0">
                                            <input type="hidden" id="form-method" value="add">
                                            <div class="row row-sm">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>Name</label>
                                                        <input type="text" class="form-control" name="name"
                                                            placeholder="Name" required>
                                                        <div class="valid-feedback"></div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mt-2">
                                                    <div class="form-group">
                                                        <label>Profile Photos</label>
                                                        <input 
                                                            type="file"
                                                            class="form-control filepond"
                                                            name="photos[]"
                                                            multiple
                                                            data-allow-reorder="true"
                                                            data-max-files="10"
                                                        >
                                                    </div>
                                                </div>

                                                <div class="col-12 mt-2">
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select id="status" name="status" class="form-control" required>
                                                            <option value="">Status</option>
                                                            <option value="1" selected>Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                        <div class="valid-feedback"></div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                        <div>
                                                            <button type="submit"
                                                                class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                            <a href="{{ route('admin.portfolio.index') }}"
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
        var form_url = 'portfolio/store';
        var redirect_url = 'portfolio';
        var is_one_image_and_multiple_image_status = 'is_multiple_image';
    </script>
@endsection
