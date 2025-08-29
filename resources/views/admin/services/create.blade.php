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
                        <h2 class="content-header-title float-start mb-0">Add Service</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                 <li class="breadcrumb-item">
                                    <a href="{{ route('admin.service.index') }}">Services</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    <a href="#">Add Service</a>
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

                                        <!-- Category -->
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Category</label>
                                                <select name="category_id" class="form-control" required>
                                                    <option value="">Select Category</option>
                                                    {{-- @dd($categories) --}}
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Name -->
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" class="form-control" name="name" placeholder="Service Name" required>
                                            </div>
                                        </div>

                                        <!-- Price -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Price</label>
                                                <input type="number" step="0.01" class="form-control" name="price" placeholder="Price" required>
                                            </div>
                                        </div>

                                        <!-- Discount Price -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Discount Price</label>
                                                <input type="number" step="0.01" class="form-control" name="discount_price" placeholder="Discount Price">
                                            </div>
                                        </div>

                                        <!-- Duration -->
                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Duration</label>
                                                <input type="text" class="form-control" name="duration" placeholder="e.g. 45 min, 1 hr" required>
                                            </div>
                                        </div>

                                        <!-- Rating -->
                                        <div class="col-3 mt-2">
                                            <div class="form-group">
                                                <label>Rating</label>
                                                <input type="number" step="0.1" max="5" class="form-control" name="rating" placeholder="e.g. 4.5">
                                            </div>
                                        </div>

                                        <!-- Reviews -->
                                        <div class="col-3 mt-2">
                                            <div class="form-group">
                                                <label>Reviews</label>
                                                <input type="number" class="form-control" name="reviews" placeholder="e.g. 150">
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea class="form-control" name="description" rows="4" placeholder="Service Description" required></textarea>
                                            </div>
                                        </div>

                                        <!-- Includes -->
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Includes (comma separated)</label>
                                                <textarea class="form-control" name="includes" rows="3" placeholder="e.g. Haircut, Coloring, Styling"></textarea>
                                            </div>
                                        </div>

                                        <!-- Icon -->
                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Icon</label>
                                                <input type="file" class="form-control filepond" name="icon">
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
                                                <a href="{{ route('admin.service.index') }}" class="btn btn-secondary">{{ trans('admin_string.cancel') }}</a>
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
    var form_url = 'service/store';
    var redirect_url = 'service';
</script>
@endsection
