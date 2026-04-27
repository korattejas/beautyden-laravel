@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root { --mst-primary: #1a237e; --mst-bg: #f8fafc; }
    body { font-family: 'Poppins', sans-serif; background-color: var(--mst-bg); }
    .card { border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
    .form-label { font-weight: 700; color: #1e293b; margin-bottom: 8px; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    .form-control, .form-select { border-radius: 8px; padding: 10px 15px; border-color: #e2e8f0; }
    .btn-save { background: var(--mst-primary); color: #fff; font-weight: 700; border-radius: 8px; padding: 10px 30px; border: none; }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Edit Membership Plan</h2>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <form id="membership-form">
                        @csrf
                        <input type="hidden" name="edit_value" value="{{ $plan->id }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Plan Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $plan->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (₹)</label>
                                <input type="number" name="price" class="form-control" value="{{ $plan->price }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Discount Percentage (%)</label>
                                <input type="number" name="discount_percentage" class="form-control" min="0" max="100" value="{{ $plan->discount_percentage }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Duration</label>
                                <select name="duration_months" class="form-select" required>
                                    <option value="1" {{ $plan->duration_months == 1 ? 'selected' : '' }}>1 Month</option>
                                    <option value="3" {{ $plan->duration_months == 3 ? 'selected' : '' }}>3 Months</option>
                                    <option value="6" {{ $plan->duration_months == 6 ? 'selected' : '' }}>6 Months</option>
                                    <option value="12" {{ $plan->duration_months == 12 ? 'selected' : '' }}>12 Months</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="1" {{ $plan->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $plan->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Description / Benefits</label>
                                <textarea name="description" class="form-control" rows="4">{{ $plan->description }}</textarea>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-save">Update Plan</button>
                                <a href="{{ route('admin.membership.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px; padding: 10px 30px;">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    $('#membership-form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.membership.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                if(res.success) {
                    toastr.success(res.message);
                    setTimeout(() => window.location.href = "{{ route('admin.membership.index') }}", 1000);
                } else {
                    toastr.error(res.message);
                }
            }
        });
    });
</script>
@endsection
