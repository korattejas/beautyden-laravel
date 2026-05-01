@extends('admin.layouts.app')

@section('header_style_content')
<style>
    :root {
        --premium-blue: #1a237e;
        --premium-gradient: linear-gradient(135deg, #1a237e 0%, #3f51b5 100%);
        --soft-blue: rgba(26, 35, 126, 0.05);
    }

    .app-content { background: #f8fafc; min-height: 100vh; }
    
    .profile-banner {
        background: var(--premium-gradient);
        height: 200px;
        border-radius: 0 0 40px 40px;
        position: relative;
        margin-bottom: 80px;
    }

    .profile-header-card {
        position: absolute;
        top: 100px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 1000px;
        background: #fff;
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 30px;
    }

    .user-avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 30px;
        background: var(--soft-blue);
        color: var(--premium-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 800;
        border: 4px solid #fff;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }

    .user-info-main h2 {
        margin: 0;
        font-weight: 800;
        color: #1e293b;
        font-size: 2.2rem;
    }

    .user-info-main p {
        margin: 5px 0 0;
        color: #64748b;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .status-badge-premium {
        padding: 8px 20px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stat-card-premium {
        background: #fff;
        border-radius: 24px;
        padding: 25px;
        height: 100%;
        border: 1px solid #eef2f7;
        transition: all 0.3s ease;
    }

    .stat-card-premium:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.05);
    }

    .stat-icon-box {
        width: 50px;
        height: 50px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1e293b;
        display: block;
    }

    .stat-label {
        color: #64748b;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .premium-table-card {
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }

    .premium-table thead th {
        background: #f8fafc;
        padding: 20px;
        color: #475569;
        font-weight: 800;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
    }

    .premium-table tbody td {
        padding: 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 700;
        color: #1e293b;
    }

    .section-header-premium {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
    }

    .section-header-premium i {
        font-size: 1.5rem;
        color: var(--premium-blue);
    }

    .section-header-premium h4 {
        margin: 0;
        font-weight: 800;
        color: #1e293b;
    }

    .address-card {
        background: #f8fafc;
        padding: 15px;
        border-radius: 16px;
        border: 1px solid #eef2f7;
        margin-bottom: 12px;
        display: flex;
        gap: 15px;
    }

    .address-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--premium-blue);
        flex-shrink: 0;
    }

    /* Mobile adjustments */
    @media (max-width: 768px) {
        .profile-header-card { flex-direction: column; text-align: center; top: 50px; }
        .profile-banner { height: 150px; margin-bottom: 200px; }
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="profile-banner shadow-sm">
        <div class="profile-header-card">
            <div class="user-avatar-large">
                @php $initials = strtoupper(substr($user->first_name ?? 'U', 0, 1) . substr($user->last_name ?? '', 0, 1)); @endphp
                {{ $initials }}
            </div>
            <div class="user-info-main flex-grow-1 text-start">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h2>{{ ($user->first_name ?? 'Registered') . ' ' . ($user->last_name ?? 'User') }}</h2>
                    <span class="status-badge-premium {{ $user->status == 1 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}" style="background: {{ $user->status == 1 ? 'rgba(40, 199, 111, 0.1)' : 'rgba(234, 84, 85, 0.1)' }}">
                        {{ $user->status == 1 ? 'Active Member' : 'Suspended' }}
                    </span>
                </div>
                <p><i class="bi bi-envelope-at me-2"></i>{{ $user->email ?? 'N/A' }} | <i class="bi bi-phone me-2"></i>{{ $user->mobile_number ?? 'N/A' }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.user.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4">
        <!-- Stats Row -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card-premium">
                    <div class="stat-icon-box bg-soft-primary text-primary" style="background: rgba(26, 35, 126, 0.1)">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <span class="stat-value">{{ $total_appointments }}</span>
                    <span class="stat-label">Total Appointments</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-premium">
                    <div class="stat-icon-box bg-soft-success text-success" style="background: rgba(40, 199, 111, 0.1)">
                        <i class="bi bi-gem"></i>
                    </div>
                    <span class="stat-value">{{ $user->activeSubscription() ? 'ACTIVE' : 'NONE' }}</span>
                    <span class="stat-label">Membership Status</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-premium">
                    <div class="stat-icon-box bg-soft-warning text-warning" style="background: rgba(255, 159, 67, 0.1)">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                    <span class="stat-value">{{ $total_coupons }}</span>
                    <span class="stat-label">Coupons Applied</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-premium">
                    <div class="stat-icon-box bg-soft-info text-info" style="background: rgba(0, 207, 232, 0.1)">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <span class="stat-value" style="font-size: 1.2rem; padding: 6px 0;">{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never' }}</span>
                    <span class="stat-label">Last Activity</span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left: Appointment History -->
            <div class="col-lg-8">
                <div class="section-header-premium">
                    <i class="bi bi-journal-text"></i>
                    <h4>Appointment History</h4>
                </div>
                <div class="premium-table-card shadow-sm">
                    <div class="table-responsive">
                        <table class="premium-table table w-100 mb-0">
                            <thead>
                                <tr>
                                    <th>Order Details</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $app)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-light rounded-3 p-2 text-center" style="min-width: 65px;">
                                                <div class="small text-muted">{{ date('M', strtotime($app->appointment_date)) }}</div>
                                                <div class="fs-5 fw-800">{{ date('d', strtotime($app->appointment_date)) }}</div>
                                            </div>
                                            <div>
                                                <div class="fw-800 text-dark">#{{ $app->order_number }}</div>
                                                <div class="small text-muted">{{ $app->appointment_time }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $badges = [1=>'bg-warning', 2=>'bg-info', 3=>'bg-success', 4=>'bg-danger'];
                                            $texts = [1=>'Pending', 2=>'Assigned', 3=>'Completed', 4=>'Rejected'];
                                        @endphp
                                        <span class="badge rounded-pill {{ $badges[$app->status] ?? 'bg-secondary' }}" style="padding: 6px 12px; font-weight: 700;">
                                            {{ $texts[$app->status] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-800 text-primary" style="font-size: 1.1rem;">₹{{ number_format($app->price, 2) }}</div>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-soft-primary rounded-pill btn-view-appointment px-3" data-id="{{ $app->id }}" style="background: rgba(26, 35, 126, 0.08); color: var(--premium-blue); font-weight: 700; border: none;">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-5 text-muted">No appointment records found for this user.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right: Membership & Addresses -->
            <div class="col-lg-4">
                <div class="section-header-premium">
                    <i class="bi bi-shield-check"></i>
                    <h4>Plan Details</h4>
                </div>
                <div class="premium-table-card p-4 mb-4">
                    @if($active_plan = $user->activeSubscription())
                        <div class="p-3 rounded-4 mb-3" style="background: linear-gradient(135deg, rgba(40, 199, 111, 0.1) 0%, rgba(40, 199, 111, 0.05) 100%); border: 1px solid rgba(40, 199, 111, 0.1);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success text-white rounded-3 p-2 shadow-sm">
                                    <i class="bi bi-award fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-800 text-dark">{{ $active_plan->plan->name }}</h6>
                                    <span class="small text-success fw-700">Expires: {{ date('d M Y', strtotime($active_plan->end_date)) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-2 border rounded-3 bg-light">
                                    <label class="d-block small text-muted">Started</label>
                                    <span class="fw-700 small">{{ date('d M Y', strtotime($active_plan->start_date)) }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded-3 bg-light">
                                    <label class="d-block small text-muted">Paid</label>
                                    <span class="fw-700 small">₹{{ number_format($active_plan->price_paid, 0) }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-slash-circle text-muted fs-3"></i>
                            </div>
                            <p class="text-muted fw-600">No active subscription found.</p>
                        </div>
                    @endif
                </div>

                <div class="section-header-premium">
                    <i class="bi bi-geo-alt"></i>
                    <h4>Saved Locations</h4>
                </div>
                <div class="premium-table-card p-4">
                    @forelse($user->addresses as $addr)
                        <div class="address-card">
                            <div class="address-icon shadow-sm">
                                <i class="bi bi-{{ $addr->address_type == 'home' ? 'house' : 'building' }}"></i>
                            </div>
                            <div>
                                <span class="badge bg-soft-primary text-primary mb-1" style="font-size: 0.65rem; text-transform: uppercase;">{{ $addr->address_type ?? 'Home' }}</span>
                                <p class="small text-dark mb-0 fw-600" style="line-height: 1.4;">{{ $addr->address }}, {{ $addr->area }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-map text-muted fs-3 opacity-25"></i>
                            <p class="small text-muted mb-0 mt-2">No addresses saved.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Detail Offcanvas -->
<div class="offcanvas offcanvas-end" style="width: 550px !important; border-left: none; box-shadow: -20px 0 60px rgba(0,0,0,0.1);" tabindex="-1" id="appointmentDetailOffcanvas">
    <div class="offcanvas-header" style="background: var(--premium-gradient); color: #fff; padding: 25px;">
        <h5 class="offcanvas-title fw-800" id="appointmentDetailOffcanvasLabel">
            <i class="bi bi-calendar-event me-2"></i> Appointment Insights
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0" id="offcanvas-content">
        <!-- Ajax Content -->
    </div>
</div>

@endsection

@section('footer_script_content')
<script>
$(document).ready(function() {
    $('.btn-view-appointment').on('click', function() {
        const id = $(this).data('id');
        const offcanvasElement = document.getElementById('appointmentDetailOffcanvas');
        const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
        const contentArea = $('#offcanvas-content');

        contentArea.html(`
            <div class="d-flex align-items-center justify-content-center h-100 py-5">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="text-muted fw-600">Generating appointment details...</p>
                </div>
            </div>
        `);
        offcanvas.show();

        $.ajax({
            url: `/admin/appointments-view/${id}`,
            method: 'GET',
            success: function(response) {
                const data = response.data;
                const summ = data.summary || {};
                
                let servicesHtml = '';
                (data.services || []).forEach(service => {
                    servicesHtml += `
                        <div class="p-3 bg-white rounded-3 border mb-2 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-800 text-dark">${service.name}</div>
                                <div class="text-muted small">${service.qty || 1} x ₹${parseFloat(service.price).toFixed(2)}</div>
                            </div>
                            <div class="fw-800 text-primary">₹${(parseFloat(service.price) * (service.qty || 1)).toFixed(2)}</div>
                        </div>
                    `;
                });

                let teamHtml = '';
                (data.team_members || []).forEach(member => {
                    teamHtml += `<span class="badge bg-soft-info text-info me-2 mb-2 p-2 px-3 rounded-pill fw-700">${member}</span>`;
                });

                const html = `
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded-4" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                            <div>
                                <span class="text-muted small fw-800 text-uppercase letter-spacing-1">Order Number</span>
                                <h4 class="mb-0 fw-800 text-primary">#${data.order_number}</h4>
                            </div>
                            <span class="badge rounded-pill ${data.status == 3 ? 'bg-success' : 'bg-warning'} px-4 py-2 fw-800 shadow-sm">
                                ${data.status == 1 ? 'Pending' : (data.status == 2 ? 'Assigned' : (data.status == 3 ? 'Completed' : 'Rejected'))}
                            </span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="p-3 rounded-4 border bg-white">
                                    <label class="text-muted small d-block fw-700 mb-1">DATE</label>
                                    <div class="fw-800 text-dark">${data.appointment_date}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-4 border bg-white">
                                    <label class="text-muted small d-block fw-700 mb-1">TIME</label>
                                    <div class="fw-800 text-dark">${data.appointment_time}</div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 rounded-4 border bg-white mb-4">
                            <label class="text-muted small d-block fw-700 mb-1">SERVICE ADDRESS</label>
                            <div class="fw-700 text-dark" style="line-height: 1.6;">${data.service_address}</div>
                        </div>

                        <h6 class="fw-800 mb-3 text-dark text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Services Booked</h6>
                        ${servicesHtml}

                        <h6 class="fw-800 mt-4 mb-3 text-dark text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Assigned Professional</h6>
                        <div>${teamHtml || '<span class="text-muted">No professional assigned yet</span>'}</div>

                        <div class="mt-5 p-4 rounded-4 text-white" style="background: #1e293b; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
                            <div class="d-flex justify-content-between mb-2 opacity-75">
                                <span>Subtotal</span>
                                <span>₹${parseFloat(summ.sub_total || 0).toFixed(2)}</span>
                            </div>
                             ${summ.discount_amount > 0 ? `
                                <div class="d-flex justify-content-between mb-2 text-success fw-700">
                                    <span>Discount (${summ.discount_percent}%)</span>
                                    <span>-₹${parseFloat(summ.discount_amount).toFixed(2)}</span>
                                </div>
                            ` : ''}
                            <div class="d-flex justify-content-between mb-4 opacity-75 border-bottom border-white border-opacity-10 pb-3">
                                <span>Logistics Fee</span>
                                <span>₹${parseFloat(summ.travel_charges || 0).toFixed(2)}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="fs-5 fw-800">Grand Total</span>
                                <span class="fs-2 fw-800">₹${parseFloat(summ.grand_total || 0).toFixed(2)}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="/admin/appointments/${data.id}/pdf" class="btn btn-primary w-100 rounded-pill py-2 fw-800 shadow-sm">
                                    <i class="bi bi-file-earmark-pdf me-2"></i> Download Invoice
                                </a>
                            </div>
                        </div>
                    </div>
                `;
                contentArea.html(html);
            }
        });
    });
});
</script>
@endsection
