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
                            <h2 class="content-header-title float-start mb-0">Add Coupon Code</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.coupon-codes.index') }}">Coupon Codes</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a
                                            href="#">Add Coupon Code</a>
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
                                        <input type="hidden" name="edit_value" value="0">
                                        <input type="hidden" id="form-method" value="add">
                                        <div class="row row-sm">

                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label>Coupon Code</label>
                                                    <input type="text" class="form-control" name="code"
                                                        placeholder="e.g. WELCOME10" required>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label>Discount Type</label>
                                                    <select class="form-control" name="discount_type" required>
                                                        <option value="percentage">Percentage (%)</option>
                                                        <option value="fixed">Fixed Amount (₹)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label>Discount Value</label>
                                                    <input type="number" class="form-control" name="discount_value"
                                                        placeholder="Value" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>Min Purchase Amount</label>
                                                    <input type="number" class="form-control" name="min_purchase_amount"
                                                        value="0">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>Max Discount Amount (For %)</label>
                                                    <input type="number" class="form-control" name="max_discount_amount"
                                                        placeholder="Max Discount">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>Usage Limit (Total)</label>
                                                    <input type="number" class="form-control" name="usage_limit"
                                                        placeholder="Total Uses">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>Usage Limit (Per User)</label>
                                                    <input type="number" class="form-control" name="usage_per_user"
                                                        value="1">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>Start Date</label>
                                                    <input type="date" class="form-control" name="start_date" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>End Date</label>
                                                    <input type="date" class="form-control" name="end_date" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="is_first_order_only" id="is_first_order_only">
                                                    <label class="form-check-label" for="is_first_order_only">For New Users Only (First Appointment)</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="1" selected>Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea class="form-control" name="description" rows="2" placeholder="Brief description of the offer"></textarea>
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
