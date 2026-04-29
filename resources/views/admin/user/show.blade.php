@extends('admin.layouts.app')

@section('header_style_content')
<style>
    .profile-header {
        background: linear-gradient(135deg, #1a237e 0%, #311b92 100%);
        color: white; padding: 40px 0; border-radius: 0 0 40px 40px; margin-bottom: 30px;
    }
    .stats-card {
        background: white; border-radius: 15px; padding: 20px; text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04); transition: transform 0.3s;
    }
    .stats-card:hover { transform: translateY(-5px); }
    .stats-card i { font-size: 2rem; color: #1a237e; margin-bottom: 10px; }
    .stats-card h3 { font-weight: 800; margin: 0; }
    .stats-card p { color: #64748b; margin: 0; font-size: 0.9rem; }
    
    .section-title { font-weight: 800; color: #1e293b; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .table thead th { background: #f8fafc; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; border: none; }
</style>
@endsection

@section('content')
<div class="app-content content" style="padding: 0;">
    <div class="profile-header">
        <div class="container-fluid px-5">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <div class="avatar avatar-xl bg-white p-1" style="width: 120px; height: 120px; border-radius: 50%;">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=1a237e&color=fff&size=120" class="rounded-circle w-100 h-100">
                    </div>
                </div>
                <div class="col-md-7">
                    <h1 class="fw-bold mb-1 text-white">{{ $user->name }}</h1>
                    <div class="d-flex flex-wrap gap-3 mt-2">
                        <span><i class="bi bi-phone"></i> {{ $user->mobile_number }}</span>
                        <span><i class="bi bi-envelope"></i> {{ $user->email ?? 'N/A' }}</span>
                        <span><i class="bi bi-calendar-event"></i> DOB: {{ $user->dob ?? 'Not Set' }}</span>
                    </div>
                </div>
                <div class="col-md-3 text-md-end">
                    <span class="badge {{ $user->status == 1 ? 'bg-success' : 'bg-danger' }} fs-5 px-3 py-2 rounded-pill shadow-sm">
                        {{ $user->status == 1 ? 'Active Account' : 'Suspended' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-5 pb-5">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="bi bi-calendar-check"></i>
                    <h3>{{ $total_appointments }}</h3>
                    <p>Total Appointments</p>
                </div>
            </div>
            <div class="col-md-3 mt-2 mt-md-0">
                <div class="stats-card">
                    <i class="bi bi-gem"></i>
                    <h3>{{ $user->activeSubscription() ? 'YES' : 'NO' }}</h3>
                    <p>Active Membership</p>
                </div>
            </div>
            <div class="col-md-3 mt-2 mt-md-0">
                <div class="stats-card">
                    <i class="bi bi-ticket-perforated"></i>
                    <h3>{{ $total_coupons }}</h3>
                    <p>Coupons Used</p>
                </div>
            </div>
            <div class="col-md-3 mt-2 mt-md-0">
                <div class="stats-card">
                    <i class="bi bi-clock-history"></i>
                    <h6>{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never' }}</h6>
                    <p>Last Activity</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <h4 class="section-title"><i class="bi bi-list-task"></i> Appointment History</h4>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Date/Time</th>
                                        <th>Order #</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($appointments as $app)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $app->appointment_date }}</div>
                                            <small class="text-muted">{{ $app->appointment_time }}</small>
                                        </td>
                                        <td class="fw-bold">#{{ $app->order_number }}</td>
                                        <td>
                                            @php
                                                $badges = [1=>'bg-warning', 2=>'bg-info', 3=>'bg-success', 4=>'bg-danger'];
                                                $texts = [1=>'Pending', 2=>'Assigned', 3=>'Completed', 4=>'Rejected'];
                                            @endphp
                                            <span class="badge {{ $badges[$app->status] ?? 'bg-secondary' }}">
                                                {{ $texts[$app->status] ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="fw-bold text-success">₹{{ number_format($app->price, 2) }}</td>
                                        <td>
                                            <a href="{{ route('admin.appointments.view', $app->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">View Details</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-4">No appointments found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mt-4 mt-md-0">
                <h4 class="section-title"><i class="bi bi-shield-check"></i> Membership Details</h4>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        @if($active_plan = $user->activeSubscription())
                            <div class="alert alert-success d-flex align-items-center rounded-3 border-0">
                                <i class="bi bi-star-fill me-2 fs-4"></i>
                                <div>
                                    <h6 class="alert-heading mb-0 fw-bold">{{ $active_plan->plan->name }}</h6>
                                    <small>Valid until {{ $active_plan->end_date }}</small>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="mb-1 text-muted small"><i class="bi bi-calendar-event me-1"></i> Started: {{ $active_plan->start_date }}</p>
                                <p class="mb-0 text-muted small"><i class="bi bi-cash-stack me-1"></i> Paid: ₹{{ $active_plan->price_paid }}</p>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-x-circle text-muted fs-1 opacity-25"></i>
                                <p class="mt-2 text-muted small mb-0">No active membership plan.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <h4 class="section-title"><i class="bi bi-geo-alt"></i> Saved Addresses</h4>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($user->addresses as $addr)
                            <li class="list-group-item border-0 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <span class="badge bg-light-primary text-primary mb-2">{{ ucfirst($addr->address_type ?? 'Home') }}</span>
                                <p class="small mb-0 text-dark">{{ $addr->address }}, {{ $addr->area }}</p>
                            </li>
                            @empty
                            <li class="list-group-item text-center text-muted py-4 border-0">No addresses saved.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('admin.user.index') }}" class="btn btn-secondary w-100 rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i> Back to Users List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
