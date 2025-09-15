@extends('admin.layouts.app')
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <h2 class="content-header-title float-start mb-0">Add Appointment</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.appointments.index') }}">Appointments</a>
                            </li>
                            <li class="breadcrumb-item active">Add Appointment</li>
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

                                            <!-- Service Category -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Service Category</label>
                                                    <select name="service_category_id" class="form-control select2"
                                                        id="category_id">
                                                        <option value="">Select Category</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Service Sub Category -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Service Sub Category</label>
                                                    <select name="service_sub_category_id" class="form-control select2"
                                                        id="sub_category_id">
                                                        <option value="">Select Sub Category</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Services (Multiple Select) -->
                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Services</label>
                                                    <select name="service_id[]" id="service_id" class="form-control select2"
                                                        multiple>
                                                        @foreach ($services as $service)
                                                            <option value="{{ $service->id }}">{{ $service->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">Hold Ctrl (Windows) / Cmd (Mac) or use
                                                        multi-select dropdown to choose multiple services.</small>
                                                </div>
                                            </div>

                                            <!-- First Name -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>First Name</label>
                                                    <input type="text" class="form-control" name="first_name"
                                                        placeholder="First Name" required>
                                                </div>
                                            </div>

                                            <!-- Last Name -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Last Name</label>
                                                    <input type="text" class="form-control" name="last_name"
                                                        placeholder="Last Name">
                                                </div>
                                            </div>

                                            <!-- Email -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" class="form-control" name="email"
                                                        placeholder="Email">
                                                </div>
                                            </div>

                                            <!-- Phone -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Phone</label>
                                                    <input type="number" class="form-control" name="phone"
                                                        placeholder="Phone">
                                                </div>
                                            </div>

                                            <!-- Quantity -->
                                            <div class="col-md-3 mt-2">
                                                <div class="form-group">
                                                    <label>Quantity</label>
                                                    <input type="number" class="form-control" name="quantity"
                                                        min="1" value="1">
                                                </div>
                                            </div>

                                            <!-- Price -->
                                            <div class="col-md-3 mt-2">
                                                <div class="form-group">
                                                    <label>Price</label>
                                                    <input type="number" step="0.01" class="form-control" name="price"
                                                        placeholder="0.00">
                                                </div>
                                            </div>

                                            <!-- Discount Price -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Discount Price</label>
                                                    <input type="number" step="0.01" class="form-control"
                                                        name="discount_price" placeholder="0.00">
                                                </div>
                                            </div>


                                            <!-- Appointment Date -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Appointment Date</label>
                                                    <input type="date" class="form-control" name="appointment_date">
                                                </div>
                                            </div>

                                            <!-- Appointment Time -->
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Appointment Time</label>
                                                    <input type="time" class="form-control" name="appointment_time">
                                                </div>
                                            </div>

                                            <!-- Service Address -->
                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Service Address</label>
                                                    <textarea class="form-control" name="service_address" rows="3" placeholder="Full Address"></textarea>
                                                </div>
                                            </div>

                                            <!-- Special Notes -->
                                            <div class="col-md-12 mt-2">
                                                <div class="form-group">
                                                    <label>Special Notes</label>
                                                    <textarea class="form-control" name="special_notes" rows="3" placeholder="Special instructions"></textarea>
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-4 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="1" selected>Pending</option>
                                                        <option value="2">Assigned</option>
                                                        <option value="3">Completed</option>
                                                        <option value="4">Rejected</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Submit -->
                                            <div class="col-12 mt-3" style="text-align: right;">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                <a href="{{ route('admin.appointments.index') }}"
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
        var form_url = 'appointments/store';
        var redirect_url = 'appointments';

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
                    url: 'get-appoinmentSubcategories/' + categoryId,
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
    </script>
@endsection
