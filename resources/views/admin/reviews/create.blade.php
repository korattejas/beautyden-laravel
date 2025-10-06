@extends('admin.layouts.app')
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <h2 class="content-header-title float-start mb-0">Add Customer Review</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Customer Reviews</a>
                            </li>
                            <li class="breadcrumb-item active">Add Review</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <section class="horizontal-wizard">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" data-parsley-validate id="addEditForm" role="form"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="0">
                                        <input type="hidden" id="form-method" value="add">
                                        <div class="row">

                                            <!-- Category -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Category</label>
                                                    <select name="category_id" class="form-control select2">
                                                        <option value="">Select Category</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Service -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Service</label>
                                                    <select name="service_id" class="form-control select2">
                                                        <option value="">Select Service</option>
                                                        @foreach ($services as $service)
                                                            <option value="{{ $service->id }}">{{ $service->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Customer Name -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Customer Name</label>
                                                    <input type="text" class="form-control" name="customer_name"
                                                        placeholder="Full Name" required>
                                                </div>
                                            </div>

                                            <!-- Customer Photo -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Customer Photo</label>
                                                    <input type="file" class="form-control filepond"
                                                        name="customer_photo">
                                                </div>
                                            </div>

                                            <!-- Rating -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Rating (0-5)</label>
                                                    <input type="number" class="form-control" name="rating" min="0"
                                                        max="5" step="0.1" placeholder="e.g. 4.5">
                                                </div>
                                            </div>

                                            <!-- Review -->
                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Review</label>
                                                    <textarea class="form-control" name="review" rows="3" placeholder="Write a review"></textarea>
                                                </div>
                                            </div>

                                            <!-- Photos -->
                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Photos (multiple)</label>
                                                    <input type="file" class="form-control filepond" name="photos[]"
                                                        multiple>
                                                </div>
                                            </div>

                                            <!-- Video -->
                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Video</label>
                                                    <input type="file" class="form-control filepond" name="video">
                                                </div>
                                            </div>

                                            <!-- Review Date -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Review Date</label>
                                                    <input type="date" class="form-control" name="review_date">
                                                </div>
                                            </div>

                                            <!-- Popular -->
                                            <div class="col-md-3 mt-2">
                                                <div class="form-group">
                                                    <label>Popular</label>
                                                    <select name="is_popular" class="form-control">
                                                        <option value="0" selected>No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-3 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="1" selected>Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Submit -->
                                            <div class="col-12 mt-3" style="text-align: right;">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                <a href="{{ route('admin.reviews.index') }}"
                                                    class="btn btn-secondary">Cancel</a>
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
        var form_url = 'reviews/store';
        var redirect_url = 'reviews';
        var is_one_image_and_multiple_image_status = 'is_multiple_image';

        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true,
            width: '100%'
        });
    </script>
@endsection
