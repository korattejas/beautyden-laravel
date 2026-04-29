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
                        <h2 class="content-header-title float-start mb-0">Edit Membership Plan</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.membership.index') }}">Membership Plans</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">Edit Plan</a></li>
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
                                <form id="addEditForm" data-parsley-validate="" role="form">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="{{ $plan->id }}">
                                    <div class="row row-sm">

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Plan Name</label>
                                                <input type="text" name="name" class="form-control" value="{{ $plan->name }}" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Price (₹)</label>
                                                <input type="number" name="price" class="form-control" value="{{ $plan->price }}" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label>Discount Percentage (%)</label>
                                                <input type="number" name="discount_percentage" class="form-control" min="0" max="100" value="{{ $plan->discount_percentage }}" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label>Duration</label>
                                                <select name="duration_months" class="form-control" required>
                                                    <option value="1" {{ $plan->duration_months == 1 ? 'selected' : '' }}>1 Month</option>
                                                    <option value="3" {{ $plan->duration_months == 3 ? 'selected' : '' }}>3 Months</option>
                                                    <option value="6" {{ $plan->duration_months == 6 ? 'selected' : '' }}>6 Months</option>
                                                    <option value="12" {{ $plan->duration_months == 12 ? 'selected' : '' }}>12 Months</option>
                                                </select>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" {{ $plan->status == 1 ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ $plan->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Description / Benefits</label>
                                                <textarea name="description" class="form-control" rows="4">{{ $plan->description }}</textarea>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                    <a href="{{ route('admin.membership.index') }}" class="btn btn-secondary">Cancel</a>
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
@endsection

@section('footer_script_content')
<script>
    var form_url = 'membership/store';
    var redirect_url = 'membership';
</script>
@endsection
