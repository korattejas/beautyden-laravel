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
                                    <li class="breadcrumb-item active"><a
                                            href="#">{{ trans('admin_string.add_setting') }}</a>
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
                                            <input type="hidden" name="edit_value" value="0">
                                            <input type="hidden" id="form-method" value="add">
                                            <div class="row row-sm">

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ trans('admin_string.screen_name') }}</label>
                                                        <input type="text" class="form-control" name="screen_name"
                                                            placeholder="{{ trans('admin_string.screen_name') }}">
                                                        <div class="valid-feedback"></div>
                                                    </div>
                                                </div>

                                                <div class="col-12 mt-2">
                                                    <div class="form-group">
                                                        <label>{{ trans('admin_string.key') }}</label>
                                                        <input type="text" class="form-control" name="key"
                                                            placeholder="{{ trans('admin_string.key') }}" required>
                                                        <div class="valid-feedback"></div>
                                                    </div>
                                                </div>

                                                <div class="col-12 mt-2">
                                                    <div class="form-group">
                                                        <label>{{ trans('admin_string.value') }}</label>
                                                        <textarea class="form-control" name="value" rows="3" placeholder="Write a value" required></textarea>
                                                        <div class="valid-feedback"></div>
                                                    </div>
                                                </div>

                                                <div class="col-12 mt-2">
                                                    <div class="form-group">
                                                        <label>{{ trans('admin_string.status') }}</label>
                                                        <select id="status" name="status" class="form-control" required>
                                                            <option value="">
                                                                {{ trans('admin_string.select_status') }}
                                                            </option>
                                                            <option value="1" selected>Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                        <div class="valid-feedback"></div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group mb-0 mt-3 justify-content-end"
                                                        style="text-align: right;">
                                                        <div>
                                                            <button type="submit"
                                                                class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                            <a href="{{ route('admin.setting.index') }}"
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
        var form_url = 'setting/store';
        var redirect_url = 'setting';
        $(document).ready(function() {
            $('.select2-dropdown').select2({
                allowClear: true
            });
        });
    </script>
@endsection
