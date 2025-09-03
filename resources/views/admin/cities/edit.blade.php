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
                            <h2 class="content-header-title float-start mb-0">Edit City</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.city.index') }}">Cities</a>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        <a href="#">Edit City</a>
                                    </li>
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
                                        <input type="hidden" name="edit_value" value="{{ $city->id }}">
                                        <input type="hidden" id="form-method" value="edit">
                                        <div class="row row-sm">

                                            <!-- Name -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>City Name</label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{ old('name', $city->name) }}" required>
                                                </div>
                                            </div>

                                            <!-- State -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>State</label>
                                                    <input type="text" class="form-control" name="state"
                                                        value="{{ old('state', $city->state) }}" required>
                                                </div>
                                            </div>

                                            <!-- City -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Area</label>
                                                    <input type="text" class="form-control" name="area"
                                                        value="{{ old('city', $city->area) }}">
                                                </div>
                                            </div>

                                            <!-- Slug -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Slug</label>
                                                    <input type="text" class="form-control" name="slug"
                                                        value="{{ old('slug', $city->slug) }}">
                                                </div>
                                            </div>

                                            <!-- Icon -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Icon</label>
                                                    @if (isset($city->icon) && !empty($city->icon))
                                                        <div class="mb-3">
                                                            <img src="{{ asset('uploads/city/' . $city->icon) }}"
                                                                alt="City Icon" style="width: 120px; height: auto;" />
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control filepond" name="icon">
                                                </div>
                                            </div>

                                            <!-- Launch Quarter -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Launch Quarter</label>
                                                    <input type="text" class="form-control" name="launch_quarter"
                                                        value="{{ old('launch_quarter', $city->launch_quarter) }}">
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="1" {{ $city->status == 1 ? 'selected' : '' }}>
                                                            Active</option>
                                                        <option value="0" {{ $city->status == 0 ? 'selected' : '' }}>
                                                            Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Priority -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Priority Status</label>
                                                    <select name="is_popular" class="form-control" required>
                                                        <option value="1"
                                                            {{ $city->is_popular == 1 ? 'selected' : '' }}>High Priority
                                                        </option>
                                                        <option value="0"
                                                            {{ $city->is_popular == 0 ? 'selected' : '' }}>Low Priority
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Submit -->
                                            <div class="col-12">
                                                <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                    <a href="{{ route('admin.city.index') }}"
                                                        class="btn btn-secondary">{{ trans('admin_string.cancel') }}</a>
                                                </div>
                                            </div>

                                        </div> <!-- row end -->
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
        var form_url = 'city/store';
        var redirect_url = 'city';
        var is_one_image_and_multiple_image_status = 'is_one_image';
    </script>
@endsection
