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
                        <h2 class="content-header-title float-start mb-0">Edit App Setting</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.app-setting.index') }}">App Setting</a>
                                </li>
                                <li class="breadcrumb-item active">Edit App Setting</li>
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
                                <form id="addEditForm" enctype="multipart/form-data" data-parsley-validate="" role="form">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="{{ $setting->id }}">
                                    <input type="hidden" id="form-method" value="edit">
                                    <div class="row row-sm">

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>{{ trans('admin_string.screen_name') }}</label>
                                                <input type="text" name="screen_name" class="form-control" value="{{ $setting->screen_name }}" placeholder="{{ trans('admin_string.screen_name') }}">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>{{ trans('admin_string.key') }}</label>
                                                <input type="text" name="key" class="form-control" value="{{ $setting->key }}" placeholder="{{ trans('admin_string.key') }}" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>{{ trans('admin_string.value') }}</label>
                                                <textarea name="value" class="form-control" rows="3" placeholder="Write a value" required>{{ $setting->value }}</textarea>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>{{ trans('admin_string.status') }}</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" {{ $setting->status == '1' ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ $setting->status == '0' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>{{ trans('admin_string.image') }}</label>
                                                @if(!empty($setting->image))
                                                    <div class="mb-2">
                                                        <img src="{{ asset('uploads/app-settings/' . $setting->image) }}" alt="Current Setting Image" class="rounded shadow-sm border" style="max-width: 150px; height: auto;">
                                                    </div>
                                                @endif
                                                <input type="file" class="filepond" name="image" accept="image/*">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <a href="{{ route('admin.app-setting.index') }}" class="btn btn-secondary">Cancel</a>
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
@endsection

@section('footer_script_content')
<script>
    var form_url = 'app-setting/store';
    var redirect_url = 'app-setting';
    var is_one_image_and_multiple_image_status = 'is_one_image';
</script>
@endsection
