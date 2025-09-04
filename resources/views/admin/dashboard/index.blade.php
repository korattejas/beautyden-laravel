@extends('admin.layouts.app')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <section id="beautyden-dashboard">
                    <div class="row match-height">

                        {{-- ================== APPOINTMENTS ================== --}}
                        <!-- Total Appointments -->
                        <div class="col-md-3 col-sm-6 mb-1">
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

                        <!-- Pending -->
                        <div class="col-md-3 col-sm-6 mb-1">
                            <div class="card text-center bg-gradient-warning text-white shadow">
                                <a href="{{ route('admin.appointments.index', ['status' => 1]) }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="clock" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalAppointmentsPending }}</h2>
                                        <h5>Appointments Pending</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Assigned -->
                        <div class="col-md-3 col-sm-6 mb-1">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.appointments.index', ['status' => 2]) }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="user-check" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalAppointmentsAssigned }}</h2>
                                        <h5>Appointments Assigned</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Completed -->
                        <div class="col-md-3 col-sm-6 mb-1">
                            <div class="card text-center bg-gradient-success text-white shadow">
                                <a href="{{ route('admin.appointments.index', ['status' => 3]) }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="check-circle" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalAppointmentsCompleted }}</h2>
                                        <h5>Appointments Completed</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Rejected -->
                        <div class="col-md-3 col-sm-6 mb-1">
                            <div class="card text-center bg-gradient-danger text-white shadow">
                                <a href="{{ route('admin.appointments.index', ['status' => 4]) }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="x-circle" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalAppointmentsRejected }}</h2>
                                        <h5>Appointments Rejected</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        {{-- ================== OTHER MODULES ================== --}}
                        <!-- Contact Submissions -->
                        <div class="col-md-3 col-sm-6 mb-1">
                            <div class="card text-center bg-gradient-secondary text-white shadow">
                                <a href="{{ route('admin.contact-submissions.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="mail" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalContacts }}</h2>
                                        <h5>Contact Submissions</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Service Category -->
                        <div class="col-md-3 col-sm-6 mb-1">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.service-category.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="layers" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalServiceCategory }}</h2>
                                        <h5>Total Service Categories</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Services -->
                        <div class="col-md-3 col-sm-6 mb-1">
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

                        <!-- Blogs -->
                        <div class="col-md-3 col-sm-6 mb-1">
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

                        <!-- Blog Categories -->
                        <div class="col-md-3 col-sm-6 mb-1">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.blog-category.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="folder" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalBlogCategory }}</h2>
                                        <h5>Total Blog Categories</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Team Members -->
                        <div class="col-md-3 col-sm-6 mb-1">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.team.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="users" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalTeamMember }}</h2>
                                        <h5>Total Team Members</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Hirings -->
                        <div class="col-md-3 col-sm-6 mb-1">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.hirings.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="briefcase" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalHirings }}</h2>
                                        <h5>Total Hirings</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Reviews -->
                        <div class="col-md-3 col-sm-6 mb-1">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.reviews.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="star" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalCustomerReviews }}</h2>
                                        <h5>Total Reviews</h5>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Cities -->
                        <div class="col-md-3 col-sm-6 mb-1">
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

                        <!-- Product Brand -->
                        <div class="col-md-3 col-sm-6 mb-1">
                            <div class="card text-center bg-gradient-info text-white shadow">
                                <a href="{{ route('admin.product-brand.index') }}" class="dashboard-card">
                                    <div class="card-body" style="color: #102365;">
                                        <i data-feather="tag" class="font-large-2 mb-1"></i>
                                        <h2 class="fw-bolder">{{ $totalProductBrand }}</h2>
                                        <h5>Total Product Brand</h5>
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
