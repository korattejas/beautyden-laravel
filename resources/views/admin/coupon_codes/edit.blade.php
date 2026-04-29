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
                        <h2 class="content-header-title float-start mb-0">Edit Coupon Code</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.coupon-codes.index') }}">Coupon Codes</a>
                                </li>
                                <li class="breadcrumb-item active">Edit Coupon Code</li>
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
                                <form id="addEditForm" enctype="multipart/form-data" data-parsley-validate="" role="form">
                                    @csrf
                                    <input type="hidden" name="edit_value" value="{{ $coupon->id }}">
                                    <input type="hidden" id="form-method" value="edit">
                                    <div class="row row-sm">

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Coupon Code</label>
                                                <input type="text" name="code" class="form-control" value="{{ $coupon->code }}" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label>Discount Type</label>
                                                <select name="discount_type" class="form-control" required>
                                                    <option value="percentage" {{ $coupon->discount_type == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                                    <option value="fixed" {{ $coupon->discount_type == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                                                </select>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <label>Discount Value</label>
                                                <input type="number" name="discount_value" class="form-control" value="{{ $coupon->discount_value }}" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label>Min Purchase Amount</label>
                                                <input type="number" name="min_purchase_amount" class="form-control" value="{{ $coupon->min_purchase_amount }}">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label>Max Discount Amount (For %)</label>
                                                <input type="number" name="max_discount_amount" class="form-control" value="{{ $coupon->max_discount_amount }}">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label>Usage Limit (Total)</label>
                                                <input type="number" name="usage_limit" class="form-control" value="{{ $coupon->usage_limit }}">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label>Usage Limit (Per User)</label>
                                                <input type="number" name="usage_per_user" class="form-control" value="{{ $coupon->usage_per_user }}">
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label>Start Date</label>
                                                <input type="date" name="start_date" class="form-control" value="{{ $coupon->start_date ? $coupon->start_date->format('Y-m-d') : '' }}" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label>End Date</label>
                                                <input type="date" name="end_date" class="form-control" value="{{ $coupon->end_date ? $coupon->end_date->format('Y-m-d') : '' }}" required>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="is_first_order_only" id="is_first_order_only" {{ $coupon->is_first_order_only ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_first_order_only">For New Users Only (First Appointment)</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" {{ $coupon->status == 1 ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ $coupon->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="description" class="form-control" rows="2" placeholder="Brief description of the offer">{{ $coupon->description }}</textarea>
                                                <div class="valid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-0 mt-3 justify-content-end" style="text-align: right;">
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <a href="{{ route('admin.coupon-codes.index') }}" class="btn btn-secondary">Cancel</a>
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
    var form_url = 'coupon-codes/store';
    var redirect_url = 'coupon-codes';
</script>
@endsection
