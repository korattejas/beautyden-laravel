@extends('admin.layouts.app')

@section('header_style_content')
<style>
    .profile-header {
        background: linear-gradient(135deg, #102365 0%, #1a4a7a 100%);
        padding: 60px 0; border-radius: 0 0 50px 50px; position: relative; margin-bottom: 40px;
    }
    .profile-avatar-wrapper {
        position: relative; width: 140px; height: 140px; margin: 0 auto;
    }
    .profile-avatar {
        width: 100%; height: 100%; border-radius: 40px; object-fit: cover;
        border: 4px solid rgba(255,255,255,0.2); box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    .user-meta { color: rgba(255,255,255,0.8); font-size: 0.9rem; }
    .user-meta i { color: #00d2ff; }
    
    .stat-card {
        border: none; border-radius: 24px; transition: all 0.3s ease;
        background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        height: 100%; overflow: hidden; position: relative; padding: 20px; text-align: center;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
    .stat-icon {
        width: 50px; height: 50px; border-radius: 16px; margin: 0 auto 15px auto;
        display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background: #f1f5f9; color: #1a4a7a;
    }
    .stat-card h3 { font-weight: 800; margin: 0; }
    .stat-card p { color: #64748b; margin: 0; font-size: 0.9rem; }
    
    .section-title { font-weight: 800; color: #1e293b; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .table thead th { background: #f8fafc; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; border: none; }
</style>
@endsection

@section('content')
<div class="app-content content" style="padding: 0;">
    <div class="profile-header shadow-lg">
        <div class="container-fluid px-5">
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
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill btn-view-appointment" data-id="{{ $app->id }}">
                                                View Details
                                            </button>
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

@section('header_style_content')
<style>
    .offcanvas-appointment { width: 550px !important; border-left: none; box-shadow: -20px 0 60px rgba(0,0,0,0.1); }
    .detail-section { padding: 24px; border-bottom: 1px solid #f1f5f9; }
    .detail-section:last-child { border-bottom: none; }
    .detail-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 8px; display: block; }
    .detail-value { font-size: 1.1rem; font-weight: 700; color: #1e293b; line-height: 1.4; }
    .service-item { padding: 16px; background: #ffffff; border-radius: 16px; margin-bottom: 12px; border: 1px solid #f1f5f9; transition: all 0.2s; }
    .service-item:hover { border-color: #7367f0; box-shadow: 0 4px 12px rgba(115,103,240,0.05); }
    .summary-box-premium { background: #1e293b; color: white; border-radius: 24px; padding: 24px; position: relative; overflow: hidden; }
    .summary-box-premium::before { content: ''; position: absolute; top: -50%; right: -20%; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 1rem; opacity: 0.8; }
    .summary-total { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 16px; margin-top: 16px; font-weight: 800; font-size: 1.6rem; opacity: 1; color: #fff; }
    .loader-wrapper { display: flex; align-items: center; justify-content: center; height: 100%; background: white; }
    .section-icon { width: 32px; height: 32px; border-radius: 8px; background: rgba(115,103,240,0.1); color: #7367f0; display: flex; align-items: center; justify-content: center; margin-right: 12px; }
</style>
@endsection

@section('footer_script_content')
<!-- Appointment Detail Offcanvas -->
<div class="offcanvas offcanvas-end offcanvas-appointment" tabindex="-1" id="appointmentDetailOffcanvas" aria-labelledby="appointmentDetailOffcanvasLabel">
    <div class="offcanvas-header bg-light-primary border-bottom py-3">
        <h5 class="offcanvas-title fw-bold" id="appointmentDetailOffcanvasLabel">
            <i class="bi bi-calendar-event text-primary me-1"></i> Appointment Details
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0" id="offcanvas-content">
        <!-- Content will be loaded here -->
    </div>
</div>

<script>
$(document).ready(function() {
    $('.btn-view-appointment').on('click', function() {
        const id = $(this).data('id');
        const offcanvasElement = document.getElementById('appointmentDetailOffcanvas');
        const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
        const contentArea = $('#offcanvas-content');

        // Show loading state
        contentArea.html(`
            <div class="loader-wrapper">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <p class="text-muted small">Fetching appointment insights...</p>
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
                        <div class="service-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small text-dark">${service.name}</div>
                                <div class="text-muted" style="font-size: 0.65rem;">${service.qty || 1} x ₹${parseFloat(service.price).toFixed(2)}</div>
                            </div>
                            <div class="fw-bold text-dark">₹${(parseFloat(service.price) * (service.qty || 1)).toFixed(2)}</div>
                        </div>
                    `;
                });

                let teamHtml = '';
                (data.team_members || []).forEach(member => {
                    teamHtml += `<span class="badge bg-light-info text-info me-1 mb-1">${member}</span>`;
                });

                const html = `
                    <div class="p-0">
                        <div class="detail-section d-flex justify-content-between align-items-center bg-light-primary border-0">
                            <div>
                                <span class="detail-label">Booking Reference</span>
                                <div class="fw-bold fs-3 text-primary">${data.order_number}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge ${data.status == 3 ? 'bg-success' : (data.status == 4 ? 'bg-danger' : 'bg-warning')} px-3 py-1 rounded-pill shadow-sm fs-6">
                                    ${data.status == 1 ? 'Pending' : (data.status == 2 ? 'Assigned' : (data.status == 3 ? 'Completed' : 'Rejected'))}
                                </span>
                            </div>
                        </div>

                        <div class="detail-section">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="section-icon"><i class="bi bi-calendar3"></i></div>
                                        <span class="detail-label mb-0">Date</span>
                                    </div>
                                    <div class="detail-value ps-1">${data.appointment_date}</div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="section-icon"><i class="bi bi-clock"></i></div>
                                        <span class="detail-label mb-0">Time</span>
                                    </div>
                                    <div class="detail-value ps-1">${data.appointment_time}</div>
                                </div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <div class="d-flex align-items-center mb-2">
                                <div class="section-icon"><i class="bi bi-geo-alt"></i></div>
                                <span class="detail-label mb-0">Service Address</span>
                            </div>
                            <div class="detail-value ps-1" style="font-size: 1rem;">${data.service_address}</div>
                        </div>

                        <div class="detail-section bg-light-secondary border-0">
                            <div class="d-flex align-items-center mb-3">
                                <div class="section-icon bg-white shadow-sm"><i class="bi bi-scissors text-secondary"></i></div>
                                <h6 class="mb-0 fw-bolder">Services Rendered</h6>
                            </div>
                            ${servicesHtml}
                        </div>

                        <div class="detail-section">
                            <div class="d-flex align-items-center mb-2">
                                <div class="section-icon"><i class="bi bi-people"></i></div>
                                <span class="detail-label mb-0">Assigned Professionals</span>
                            </div>
                            <div class="ps-1 mt-2">${teamHtml || '<span class="text-muted small">No team members assigned yet.</span>'}</div>
                        </div>

                        <div class="detail-section">
                            <div class="summary-box-premium shadow-lg">
                                <div class="summary-row">
                                    <span>Subtotal Amount</span>
                                    <span>₹${parseFloat(summ.sub_total || 0).toFixed(2)}</span>
                                </div>
                                ${summ.discount_amount > 0 ? `
                                <div class="summary-row" style="color: #4ade80;">
                                    <span>Offer Applied (${summ.discount_percent}%)</span>
                                    <span>-₹${parseFloat(summ.discount_amount).toFixed(2)}</span>
                                </div>
                                ` : ''}
                                <div class="summary-row">
                                    <span>Travel & Logistics</span>
                                    <span>₹${parseFloat(summ.travel_charges || 0).toFixed(2)}</span>
                                </div>
                                <div class="summary-row summary-total">
                                    <span>Grand Total</span>
                                    <span>₹${parseFloat(summ.grand_total || 0).toFixed(2)}</span>
                                </div>
                                
                                <div class="mt-4 pt-2 d-flex justify-content-between align-items-center border-top border-white border-opacity-10">
                                    <div>
                                        <span class="detail-label text-white-50 mb-0">Method</span>
                                        <div class="fw-bold">${data.payment_type ? data.payment_type.toUpperCase() : 'CASH'}</div>
                                    </div>
                                    <a href="/admin/appointments/${data.id}/pdf" class="btn btn-primary btn-lg shadow-sm px-4 rounded-3">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Get Invoice
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 text-center">
                            <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Prices are inclusive of all taxes.</p>
                        </div>
                    </div>
                `;
                contentArea.html(html);
            },
            error: function() {
                contentArea.html(`
                    <div class="p-5 text-center">
                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                        <p class="mt-2">Failed to load appointment data.</p>
                        <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="offcanvas">Close</button>
                    </div>
                `);
            }
        });
    });
});
</script>
@endsection
