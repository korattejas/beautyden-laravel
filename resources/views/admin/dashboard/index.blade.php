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
