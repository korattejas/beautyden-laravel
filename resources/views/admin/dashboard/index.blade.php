@extends('admin.layouts.app')
@section('header_style_content')
<!-- <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/css/plugins/charts/chart-apex.css') }}"> -->
@endsection
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- Dashboard Analytics Start -->
            <section id="dashboard-analytics">
                <div class="row match-height">
                    <!-- Greetings Card starts -->
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="card card-congratulations">
                            <div class="card-body text-center">
                                <div class="avatar avatar-xl bg-primary shadow">
                                    <div class="avatar-content">
                                        <i data-feather="award" class="font-large-1"></i>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h1 class="mb-1 text-white">
                                        Hi, {{ Auth::guard('admin')->user()->name }},</h1>
                                    <p class="card-text m-auto w-75">
                                        You have done <strong>57.6%</strong> more sales today. Check your new badge
                                        in
                                        your profile.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row match-height">
                    <div class="col-xl-6 col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title fw-bolder">Total AWS Space</h4>
                            </div>
                            <div class="card-body">
                                <div id="user-donut-chart">{{$awsTotalSpace . ' GB' ?? 0}}</div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title fw-bolder">Total AWS Space Remaining</h4>
                            </div>
                            <div class="card-body">
                                <div id="user-donut-chart">{{$remainingSpace ?? 0}}</div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title fw-bolder">Total AWS Space Use</h4>
                            </div>
                            <div class="card-body">
                                <div id="user-donut-chart">{{$totalUsedSpace ?? 0}}</div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title fw-bolder">AWS Plan Expiry Date:</h4>
                            </div>
                            <div class="card-body">
                                <div id="user-donut-chart">{{ $awsPlanExpire ?? 0}}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title fw-bolder">Donations</h4>
                            </div>
                            <div class="card-body">
                                <div id="horses-donut-chart"></div>
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
<!-- <script src="{{ URL::asset('panel-assets/vendors/js/charts/apexcharts.min.js') }}"></script> -->
<!-- <script src="{{ URL::asset('panel-assets/vendors/js/charts/chart.min.js') }}"></script> -->
<!-- <script src="{{ URL::asset('panel-assets/js/scripts/pages/admin/dashboard.js') }}"></script> -->
@endsection