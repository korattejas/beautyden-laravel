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
                        <h2 class="content-header-title float-start mb-0">Edit Service</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.service.index') }}">Services</a>
                                </li>
                                <li class="breadcrumb-item active">Edit Service</li>
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
                                    <input type="hidden" name="edit_value" value="{{ $service->id }}">
                                    <input type="hidden" id="form-method" value="edit">

                                    <div class="row row-sm">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>Category</label>
                                                <select name="category_id" class="form-control" required>
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" 
                                                            @if($service->category_id == $category->id) selected @endif>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>{{ trans('admin_string.name') }}</label>
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ $service->name }}"
                                                    placeholder="{{ trans('admin_string.name') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Price</label>
                                                <input type="number" step="0.01" class="form-control" name="price"
                                                    value="{{ $service->price }}" placeholder="Price" required>
                                            </div>
                                        </div>

                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Discount Price</label>
                                                <input type="number" step="0.01" class="form-control" name="discount_price"
                                                    value="{{ $service->discount_price }}" placeholder="Discount Price">
                                            </div>
                                        </div>

                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Duration</label>
                                                <input type="text" class="form-control" name="duration"
                                                    value="{{ $service->duration }}" placeholder="e.g. 45 min" required>
                                            </div>
                                        </div>

                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Rating</label>
                                                <input type="number" step="0.1" class="form-control" name="rating"
                                                    value="{{ $service->rating }}" placeholder="Rating (0-5)">
                                            </div>
                                        </div>

                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Reviews</label>
                                                <input type="number" class="form-control" name="reviews"
                                                    value="{{ $service->reviews }}" placeholder="No. of Reviews">
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea class="form-control" name="description" rows="4"
                                                    placeholder="{{ trans('admin_string.description') }}">{{ $service->description }}</textarea>
                                            </div>
                                        </div>

                                       <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Includes (comma separated)</label>
                                                <textarea class="form-control" name="includes" rows="3" placeholder="Hair wash, Cutting, Styling">
                                                    {{
                                                        is_array($service->includes)
                                                            ? implode(',', $service->includes)
                                                            : ($service->includes
                                                                ? implode(',', json_decode($service->includes, true))  {{-- string hoy to decode --}}
                                                                : '')
                                                    }}
                                                </textarea>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Icon</label>
                                                @if(isset($service->icon) && !empty($service->icon))
                                                    <div class="mb-3">
                                                        <img src="{{ asset('uploads/service/' . $service->icon) }}"
                                                            alt="Service Icon" style="width: 120px; height: auto;" />
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control filepond" name="icon">
                                            </div>
                                        </div>

                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="1" @if($service->status == 1) selected @endif>Active</option>
                                                    <option value="0" @if($service->status == 0) selected @endif>Inactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6 mt-2">
                                            <div class="form-group">
                                                <label>Priority</label>
                                                <select name="is_popular" class="form-control" required>
                                                    <option value="1" @if($service->is_popular == 1) selected @endif>High Priority</option>
                                                    <option value="0" @if($service->is_popular == 0) selected @endif>Low Priority</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                <div>
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                    <a href="{{ route('admin.service.index') }}"
                                                        class="btn btn-secondary">{{ trans('admin_string.cancel') }}</a>
                                                </div>
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
    var is_one_image_and_multiple_image_status = 'is_one_image';
</script>
@endsection
