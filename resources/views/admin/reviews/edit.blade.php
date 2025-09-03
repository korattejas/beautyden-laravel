@extends('admin.layouts.app')
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Edit Customer Review</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Customer Reviews</a></li>
                        <li class="breadcrumb-item active">Edit Review</li>
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
                                <form method="POST" data-parsley-validate id="addEditForm" role="form" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="{{ $review->id }}">
                                    <input type="hidden" id="form-method" value="edit">

                                    <div class="row">

                                        <!-- Service -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Service</label>
                                                <select name="service_id" class="form-control" required>
                                                    <option value="">Select Service</option>
                                                    @foreach($services as $service)
                                                        <option value="{{ $service->id }}" 
                                                            @if($review->service_id == $service->id) selected @endif>
                                                            {{ $service->name }}
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
                                                       value="{{ $review->customer_name }}" required>
                                            </div>
                                        </div>

                                        <!-- Customer Photo -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Customer Photo</label>
                                                @if($review->customer_photo)
                                                    <div class="mb-2">
                                                        <img src="{{ asset('uploads/review/customer-photos/' . $review->customer_photo) }}" 
                                                             style="max-width:120px;" alt="Customer Photo">
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control filepond" name="customer_photo">
                                            </div>
                                        </div>

                                        <!-- Rating -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Rating (0-5)</label>
                                                <input type="number" class="form-control" name="rating" min="0" max="5" step="0.1"
                                                       value="{{ $review->rating }}">
                                            </div>
                                        </div>

                                        <!-- Review -->
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label>Review</label>
                                                <textarea class="form-control" name="review" rows="3">{{ $review->review }}</textarea>
                                            </div>
                                        </div>

                                        <!-- Photos -->
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label>Photos (multiple)</label>
                                                @if($review->photos)
                                                    <div class="mb-2">
                                                        @foreach(json_decode($review->photos) as $photo)
                                                            <img src="{{ asset('uploads/review/photos/' . $photo) }}" 
                                                                 style="max-width:100px; margin-right:5px;" alt="Review Photo">
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control filepond" name="photos[]" multiple>
                                            </div>
                                        </div>

                                        <!-- Video -->
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label>Video</label>
                                                @if($review->video)
                                                    <div class="mb-2">
                                                        <video src="{{ asset('uploads/review/videos/' . $review->video) }}" controls
                                                               style="max-width:200px;"></video>
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control filepond" name="video">
                                            </div>
                                        </div>

                                        <!-- Review Date -->
                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Review Date</label>
                                                <input type="date" class="form-control" name="review_date"
                                                       value="{{ $review->review_date }}" required>
                                            </div>
                                        </div>

                                        <!-- Popular -->
                                        <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label>Popular</label>
                                                <select name="is_popular" class="form-control">
                                                    <option value="0" @if($review->is_popular == 0) selected @endif>No</option>
                                                    <option value="1" @if($review->is_popular == 1) selected @endif>Yes</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" @if($review->status == 1) selected @endif>Active</option>
                                                    <option value="0" @if($review->status == 0) selected @endif>Inactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Submit -->
                                        <div class="col-12 mt-3" style="text-align: right;">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">Cancel</a>
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
</script>
@endsection
