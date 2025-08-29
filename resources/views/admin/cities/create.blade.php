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
                        <h2 class="content-header-title float-start mb-0">Add City</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.city.index') }}">Cities</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    <a href="#">Add City</a>
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
                                <form method="POST" data-parsley-validate="" id="addEditForm" role="form" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="0">
                                    <input type="hidden" id="form-method" value="add">

                                    <div class="row row-sm">

                                        <!-- Name -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>City Name</label>
                                                <input type="text" class="form-control" name="name" placeholder="Enter city name" required>
                                            </div>
                                        </div>

                                        <!-- State -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>State</label>
                                                <input type="text" class="form-control" name="state" placeholder="Enter state name" required>
                                            </div>
                                        </div>

                                        <!-- City -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Area</label>
                                                <input type="text" class="form-control" name="area" placeholder="Enter area">
                                            </div>
                                        </div>

                                        <!-- Slug -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Slug</label>
                                                <input type="text" class="form-control" name="slug" placeholder="Auto-generated or enter manually">
                                            </div>
                                        </div>

                                        <!-- Icon -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Icon</label>
                                                <input type="file" class="form-control filepond" name="icon">
                                            </div>
                                        </div>

                                        <!-- Launch Quarter -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Launch Quarter</label>
                                                <input type="text" class="form-control" name="launch_quarter" placeholder="e.g. Q1 2025">
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="1" selected>Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Priority -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Priority Status</label>
                                                <select name="is_popular" class="form-control" required>
                                                    <option value="1">High Priority</option>
                                                    <option value="0" selected>Low Priority</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Submit -->
                                        <div class="col-12">
                                            <div class="form-group mb-0 mt-3 justify-content-end">
                                                <button type="submit" class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                <a href="{{ route('admin.city.index') }}" class="btn btn-secondary">{{ trans('admin_string.cancel') }}</a>
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
</script>
@endsection
