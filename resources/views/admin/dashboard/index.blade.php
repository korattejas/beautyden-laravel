@extends('admin.layouts.app')

@section('content')
    <style>
        /* Dashboard Custom Styles */

        /* .text-white {
                                        color: #fff !important;
                                    }

                                    .text-white [data-feather],
                                    .text-white .feather {
                                        stroke: #fff !important;
                                        color: #fff !important;
                                    }

                                    #beautyden-dashboard .card {
                                        border-radius: 1rem;
                                        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
                                    }

                                    #beautyden-dashboard .card-body {
                                        padding: 2rem 1rem;
                                    }

                                    #beautyden-dashboard i,
                                    #beautyden-dashboard [data-feather] {
                                        width: 40px;
                                        height: 40px;
                                        stroke-width: 2.5;
                                        margin-bottom: 0.5rem;
                                    }

                                    #beautyden-dashboard h2 {
                                        font-weight: 700;
                                        margin: 0;
                                    }

                                    #beautyden-dashboard p {
                                        margin: 0;
                                        font-size: 0.9rem;
                                        opacity: 0.9;
                                    } */
    </style>

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section id="beautyden-dashboard">
                    <div class="row match-height">

                        <!-- Total Appointments -->
                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center bg-gradient-primary text-white shadow">
                                <a href="{{ route('admin.appointments.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="calendar" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalAppointments }}</h2>
                                        <h5>Total Appointments</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Success Appointments -->
                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center bg-gradient-success text-white shadow">
                                <a href="{{ route('admin.appointments.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="check-circle" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalAppoinmentSuccess }}</h2>
                                        <h5>Appointments Completed</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Pending Appointments -->
                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center bg-gradient-warning text-white shadow">
                                <a href="{{ route('admin.appointments.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="clock" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalAppoinmentPending }}</h2>
                                        <h5>Appointments Pending</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Contact Submissions -->
                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.contact-submissions.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="mail" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalContacts }}</h2>
                                        <h5>Contact Submissions</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.service-category.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="layers" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalServiceCategory }}</h2>
                                        <h5>Total Service Category</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.service.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="briefcase" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalServices }}</h2>
                                        <h5>Total Services</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.blogs.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="file-text" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalBlogs }}</h2>
                                        <h5>Total Blogs</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.blog-category.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="folder" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalBlogCategory }}</h2>
                                        <h5>Total Blog Category</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.team.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="users" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalTeamMember }}</h2>
                                        <h5>Total Team Member</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.hirings.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="briefcase" class="font-large-2 mb-1"></i> 
                                        <h2 class="fw-bolder">{{ $totalHirings }}</h2>
                                        <h5>Total Hiring</h5>
                                    </div>
                                </a>
                            </div>
                        </div>


                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.reviews.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="star" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalCustomerReviews }}</h2>
                                        <h5>Total Customer Reviews</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.city.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="map-pin" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalCity }}</h2>
                                        <h5>Total Cities</h5>
                                    </div>
                                </a>
                            </div>
                        </div>


                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
@endsection
