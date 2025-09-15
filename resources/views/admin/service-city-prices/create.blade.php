@extends('admin.layouts.app')
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">

            <!-- Page Header -->
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Add Service City Price</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.service-city-price.index') }}">Service City Prices</a>
                                    </li>
                                    <li class="breadcrumb-item active">Add</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Body -->
            <div class="content-body">
                <section class="horizontal-wizard">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="0">
                                        <input type="hidden" id="form-method" value="add">
                                        <div class="row row-sm">

                                            <!-- City Dropdown -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label for="city_id">City</label>
                                                    <select name="city_id" id="city_id" class="form-control select2">
                                                        <option value="">Select City</option>
                                                        @foreach ($cities as $city)
                                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Category -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Category</label>
                                                    <select name="category_id" class="form-control select2"
                                                        id="category_id">
                                                        <option value="">Select Category</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Sub Category -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Sub Category</label>
                                                    <select name="sub_category_id" class="form-control select2"
                                                        id="sub_category_id">
                                                        <option value="">Select Sub Category</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Service Dropdown -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label for="service_id">Service</label>
                                                    <select name="service_id" id="service_id" class="form-control select2">
                                                        <option value="">Select Service</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Price -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label for="price">Price</label>
                                                    <input type="text" name="price" id="price" class="form-control"
                                                        placeholder="Enter Price" required>
                                                </div>
                                            </div>

                                            <!-- Discount Price -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label for="discount_price">Discount Price</label>
                                                    <input type="text" name="discount_price" id="discount_price"
                                                        class="form-control" placeholder="Enter Discount Price">
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label for="status">Status</label>
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="1" selected>Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Submit Buttons -->
                                            <div class="col-12">
                                                <div class="form-group mb-0 mt-3 text-end">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                    <a href="{{ route('admin.service-city-price.index') }}"
                                                        class="btn btn-secondary">Cancel</a>
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
        var form_url = 'service-city-price/store';
        var redirect_url = 'service-city-price';
        var is_one_image_and_multiple_image_status = 'is_one_image';

        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true,
            width: '100%'
        });

        $('#category_id').on('change', function() {
            var categoryId = $(this).val();

            $('#sub_category_id').empty().append('<option value="">Select Sub Category</option>');

            if (categoryId) {
                $.ajax({
                    url: 'get-serviceCityPriceSubCategories/' + categoryId,
                    type: 'GET',
                    success: function(data) {
                        $.each(data, function(key, subCategory) {
                            $('#sub_category_id').append('<option value="' + subCategory.id +
                                '">' + subCategory.name + '</option>');
                        });

                        $('#sub_category_id').trigger('change');
                    }
                });
            }
        });

        $('#category_id').on('change', function() {
            var categoryId = $(this).val();
            var $serviceSelect = $('#service_id');

            $serviceSelect.empty().append('<option value="">Loading...</option>');

            if (categoryId) {
                $.ajax({
                    url: "{{ route('admin.services.by-category') }}",
                    type: "GET",
                    data: {
                        category_id: categoryId
                    },
                    success: function(response) {
                        $serviceSelect.empty().append('<option value="">Select Service</option>');
                        $.each(response, function(key, service) {
                            $serviceSelect.append('<option value="' + service.id + '">' +
                                service.name + '</option>');
                        });
                    }
                });
            } else {
                $serviceSelect.empty().append('<option value="">Select Service</option>');
            }
        });
    </script>
@endsection
