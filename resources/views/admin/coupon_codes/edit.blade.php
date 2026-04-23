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
                                    <li class="breadcrumb-item active"><a
                                            href="#">Edit Coupon Code</a>
                                    </li>
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
                                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="{{ $coupon->id }}">
                                        <input type="hidden" id="form-method" value="edit">
                                        <div class="row row-sm">

                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label>Coupon Code</label>
                                                    <input type="text" class="form-control" name="code"
                                                        value="{{ $coupon->code }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label>Discount Type</label>
                                                    <select class="form-control" name="discount_type" required>
                                                        <option value="percentage" @if($coupon->discount_type == 'percentage') selected @endif>Percentage (%)</option>
                                                        <option value="fixed" @if($coupon->discount_type == 'fixed') selected @endif>Fixed Amount (₹)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label>Discount Value</label>
                                                    <input type="number" class="form-control" name="discount_value"
                                                        value="{{ $coupon->discount_value }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>Min Purchase Amount</label>
                                                    <input type="number" class="form-control" name="min_purchase_amount"
                                                        value="{{ $coupon->min_purchase_amount }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>Max Discount Amount (For %)</label>
                                                    <input type="number" class="form-control" name="max_discount_amount"
                                                        value="{{ $coupon->max_discount_amount }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>Usage Limit (Total)</label>
                                                    <input type="number" class="form-control" name="usage_limit"
                                                        value="{{ $coupon->usage_limit }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>Usage Limit (Per User)</label>
                                                    <input type="number" class="form-control" name="usage_per_user"
                                                        value="{{ $coupon->usage_per_user }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>Start Date</label>
                                                    <input type="date" class="form-control" name="start_date"
                                                        value="{{ $coupon->start_date ? $coupon->start_date->format('Y-m-d') : '' }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>End Date</label>
                                                    <input type="date" class="form-control" name="end_date"
                                                        value="{{ $coupon->end_date ? $coupon->end_date->format('Y-m-d') : '' }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="is_first_order_only" id="is_first_order_only" @if($coupon->is_first_order_only) checked @endif>
                                                    <label class="form-check-label" for="is_first_order_only">For New Users Only (First Appointment)</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="1" @if($coupon->status == 1) selected @endif>Active</option>
                                                        <option value="0" @if($coupon->status == 0) selected @endif>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea class="form-control" name="description" rows="2">{{ $coupon->description }}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <div class="form-group text-end">
                                                    <button type="submit" class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                    <a href="{{ route('admin.coupon-codes.index') }}" class="btn btn-secondary">{{ trans('admin_string.cancel') }}</a>
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
