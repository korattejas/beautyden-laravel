@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root { --mst-primary: #1a237e; --mst-bg: #f8fafc; }
    body { font-family: 'Poppins', sans-serif; background-color: var(--mst-bg); }
    .card { border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
    .form-label { font-weight: 700; color: #1e293b; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    .btn-save { background: var(--mst-primary); color: #fff; font-weight: 700; border-radius: 8px; padding: 12px 40px; border: none; }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">
            <form id="master-form">
                @csrf
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="fw-bold mb-0">Add App Service City Pricing</h2>
                    <button type="submit" class="btn btn-save shadow">Save Pricing</button>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-1">
                                    <label class="form-label">City</label>
                                    <select name="city_id" class="form-select select2" required>
                                        <option value="">Select City</option>
                                        @foreach($cities as $city) <option value="{{ $city->id }}">{{ $city->name }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" id="category_id" class="form-select select2" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Sub Category</label>
                                    <select name="sub_category_id" id="sub_category_id" class="form-select select2">
                                        <option value="">Select Sub Category</option>
                                    </select>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label">Service (Master)</label>
                                    <select name="service_master_id" id="service_master_id" class="form-select select2" required>
                                        <option value="">Select Service</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">Base Price (App)</label>
                                        <input type="number" name="price" class="form-control" placeholder="0.00" required>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">Discount Price (App)</label>
                                        <input type="number" name="discount_price" class="form-control" placeholder="0.00">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">App Discount %</label>
                                        <input type="number" name="app_discount_percentage" class="form-control" placeholder="e.g. 10.00">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">Beautician Commission (₹)</label>
                                        <input type="number" name="beautician_commission" class="form-control" placeholder="e.g. 100.00">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">Is Available in this city?</label>
                                        <select name="is_available" class="form-select">
                                            <option value="1">Yes, Available</option>
                                            <option value="0">No, Disable for App</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });

        $('#category_id').on('change', function() {
            let catId = $(this).val();
            // Fetch Subcategories
            $.get("{{ url('admin/service-city-master/subcategories') }}/" + catId, function(res) {
                let html = '<option value="">Select Sub Category</option>';
                res.forEach(item => html += `<option value="${item.id}">${item.name}</option>`);
                $('#sub_category_id').html(html);
            });
            // Fetch Services
            $.get("{{ url('admin/service-city-price/get-services-by-category') }}", { category_id: catId }, function(res) {
                let html = '<option value="">Select Service</option>';
                res.forEach(item => html += `<option value="${item.id}">${item.name}</option>`);
                $('#service_master_id').html(html);
            });
        });

        $('#master-form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.service-city-master.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    if(res.success) {
                        toastr.success(res.message);
                        setTimeout(() => window.location.href = "{{ route('admin.service-city-master.index') }}", 1000);
                    } else {
                        toastr.error(res.message);
                    }
                }
            });
        });
    });
</script>
@endsection
