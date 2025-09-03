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
                        <h2 class="content-header-title float-start mb-0">Edit Home Counter</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.home-counters.index') }}">Home Counter</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">Edit Home Counter</a></li>
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
                                <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="{{ $homeCounter->id }}">
                                    <input type="hidden" id="form-method" value="edit">

                                    <div class="row row-sm">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" class="form-control" name="label"
                                                    value="{{ old('label', $homeCounter->label) }}"
                                                    placeholder="Title" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Count</label>
                                                <input type="text" class="form-control" name="value"
                                                    value="{{ old('value', $homeCounter->value) }}"
                                                    placeholder="Enter Number" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                       <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Icon</label>
                                                @if(isset($homeCounter->icon) && !empty($homeCounter->icon))
                                                    <div class="mb-3">
                                                        <img src="{{ asset('uploads/home-counters/' . $homeCounter->icon) }}"
                                                            alt="Counter Icon" style="width: 250px; height: auto;" />
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control filepond" name="icon">
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select id="status" name="status" class="form-control" required>
                                                    <option value="">Select Status</option>
                                                    <option value="1" {{ $homeCounter->status == 1 ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ $homeCounter->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                <div>
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                    <a href="{{ route('admin.home-counters.index') }}"
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
@endsection

@section('footer_script_content')
<script>
    var form_url = 'home-counters/store';
    var redirect_url = 'home-counters';
    var is_one_image_and_multiple_image_status = 'is_one_image';
</script>
@endsection
