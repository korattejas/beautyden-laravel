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
                        <h2 class="content-header-title float-start mb-0">Edit App Service City Pricing</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.service-city-master.index') }}">App Service City Master</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">Edit App Pricing</a>
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
                                    <form id="addEditForm" enctype="multipart/form-data" data-parsley-validate="" role="form">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="{{ $data->id }}">
                                        <input type="hidden" id="form-method" value="edit">
                                        <div class="row row-sm">

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <select name="city_id" class="form-control select2" required>
                                                        <option value="">Select City</option>
                                                        @foreach($cities as $city)
                                                            <option value="{{ $city->id }}" {{ $data->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Category</label>
                                                    <select name="category_id" id="category_id" class="form-control select2" required>
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}" {{ $data->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Sub Category</label>
                                                    <select name="sub_category_id" id="sub_category_id" class="form-control select2">
                                                        <option value="">Select Sub Category</option>
                                                        @foreach($subcategories as $sub)
                                                            <option value="{{ $sub->id }}" {{ $data->sub_category_id == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Service (Master)</label>
                                                    <select name="service_master_id" id="service_master_id" class="form-control select2" required>
                                                        <option value="">Select Service</option>
                                                        @foreach($services as $service)
                                                            <option value="{{ $service->id }}" {{ $data->service_master_id == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Base Price (App)</label>
                                                    <input type="number" name="price" class="form-control" value="{{ $data->price }}" placeholder="0.00" required>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Discount Price (App)</label>
                                                    <input type="number" name="discount_price" class="form-control" value="{{ $data->discount_price }}" placeholder="0.00">
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>App Discount %</label>
                                                    <input type="number" name="app_discount_percentage" class="form-control" value="{{ $data->app_discount_percentage }}" placeholder="e.g. 10.00">
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Beautician Commission (₹)</label>
                                                    <input type="number" name="beautician_commission" class="form-control" value="{{ $data->beautician_commission }}" placeholder="e.g. 100.00">
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Is Available in this city?</label>
                                                    <select name="is_available" class="form-control">
                                                        <option value="1" {{ $data->is_available == 1 ? 'selected' : '' }}>Yes, Available</option>
                                                        <option value="0" {{ $data->is_available == 0 ? 'selected' : '' }}>No, Disable for App</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>Active</option>
                                                        <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                    <div class="valid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                    <div>
                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                        <a href="{{ route('admin.service-city-master.index') }}" class="btn btn-secondary">Cancel</a>
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
    var form_url = 'service-city-master/store';
    var redirect_url = 'service-city-master';

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
                url: "{{ url('admin/service-city-price/get-serviceCityPriceSubCategories') }}/" + categoryId,
                type: 'GET',
                success: function(data) {
                    $.each(data, function(key, subCategory) {
                        $('#sub_category_id').append('<option value="' + subCategory.id + '">' + subCategory.name + '</option>');
                    });
                    $('#sub_category_id').trigger('change');
                }
            });

            // Fetch Services
            var $serviceSelect = $('#service_master_id');
            $serviceSelect.empty().append('<option value="">Loading...</option>');
            $.ajax({
                url: "{{ route('admin.service-city-master.services-by-category') }}",
                type: "GET",
                data: { category_id: categoryId },
                success: function(response) {
                    $serviceSelect.empty().append('<option value="">Select Service</option>');
                    $.each(response, function(key, service) {
                        $serviceSelect.append('<option value="' + service.id + '">' + service.name + '</option>');
                    });
                }
            });
        } else {
            $('#service_master_id').empty().append('<option value="">Select Service</option>');
        }
    });
</script>
@endsection

