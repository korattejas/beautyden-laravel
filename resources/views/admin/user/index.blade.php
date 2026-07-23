@extends('admin.layouts.app')
@section('content')

<style>
    /* Stat Filter Cards */
    .stat-filter-card {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border: 2px solid transparent !important;
    }
    .stat-filter-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
        border-color: rgba(115,103,240,0.3) !important;
    }
    .stat-filter-card.active-stat {
        background-color: #f0edff !important;
        border-color: #7367f0 !important;
        box-shadow: 0 4px 15px rgba(115,103,240,0.15) !important;
    }
    .stat-filter-card.active-stat h4 {
        color: #7367f0 !important;
    }

    /* Premium Detail Modal Enhancements - Based on Ref Image */
    #c-viewUserModal .c-modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        overflow: hidden;
    }

    #c-viewUserModal .c-modal-header {
        background: #5568f5;
        padding: 16px 24px;
        color: #fff;
    }

    #c-viewUserModal .c-modal-title {
        color: #fff;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #c-viewUserModal .c-close-btn {
        background: rgba(255,255,255,0.2);
        color: #fff;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s;
    }
    #c-viewUserModal .c-close-btn:hover {
        background: rgba(255,255,255,0.3);
    }

    .detail-section-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #5568f5;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .detail-info-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px;
        height: 100%;
        border: 1px solid #e4e6ea;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01);
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f2f4;
    }

    .info-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .info-icon {
        width: 44px;
        height: 44px;
        background: #f3f4fd;
        color: #5568f5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .info-content label {
        display: block;
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .info-content p {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }

    .stats-footer-card {
        background: #f9f9fb;
        border: 1px solid #e4e6ea;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .stat-block {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-right: 20px;
        border-right: 1px solid #e4e6ea;
    }
    .stat-block:last-child {
        border-right: none;
        padding-right: 0;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .premium-table {
        width: 100%;
        border-collapse: collapse;
    }
</style>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Registered Users</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                <li class="breadcrumb-item active">Users</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            
            <!-- Stats Row -->
            <div class="row g-2 mb-2">
                <div class="col-md col-sm-4 col-6">
                    <div class="card h-100 mb-0 stat-filter-card active-stat" data-type="total">
                        <div class="card-body d-flex align-items-center p-2">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #f3e8ff !important; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-people" style="font-size: 1.5rem; color: #7c3aed;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b; font-size: 1.25rem;">{{ $totalUsers }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 600; font-size: 0.9rem;">Total</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-4 col-6">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="active">
                        <div class="card-body d-flex align-items-center p-2">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #f0fdf4 !important; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-check" style="font-size: 1.5rem; color: #16a34a;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b; font-size: 1.25rem;">{{ $activeUsers }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 600; font-size: 0.9rem;">Active</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-4 col-6">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="suspended">
                        <div class="card-body d-flex align-items-center p-2">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #fff1f2 !important; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-x" style="font-size: 1.5rem; color: #e11d48;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b; font-size: 1.25rem;">{{ $suspendedUsers }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 600; font-size: 0.9rem;">Suspended</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-6 col-6">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="app">
                        <div class="card-body d-flex align-items-center p-2">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #eff6ff !important; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person" style="font-size: 1.5rem; color: #3b82f6;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b; font-size: 1.25rem;">{{ $appUsers }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 600; font-size: 0.9rem;">Users</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-6 col-12">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="web">
                        <div class="card-body d-flex align-items-center p-2">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #fefce8 !important; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-workspace" style="font-size: 1.5rem; color: #ca8a04;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b; font-size: 1.25rem;">{{ $webUsers }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 600; font-size: 0.9rem;">Beauticians</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <input type="hidden" id="filter-type" value="total">
            
            <section id="column-search-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-datatable table-responsive premium-table-container">
                                <table class="dt-column-search premium-table table w-100 dataTable" id="user-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>User Info</th>
                                            <th>Contact</th>
                                            <th>Joined On</th>
                                            <th>Wallet</th>
                                            <th>Ref. Code</th>
                                            <th data-search="false">Appointments</th>
                                            <th data-stuff="User,Beautician">Role</th>
                                            <th data-stuff="Active,InActive">Status</th>
                                            <th data-search="false">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Custom View Modal -->
<div id="c-viewUserModal" class="c-modal">
    <div class="c-modal-dialog" style="max-width: 950px;">
        <div class="c-modal-content">
            
            <!-- Header -->
            <div class="c-modal-header">
                <h5 class="c-modal-title">
                    <i class="bi bi-person-circle"></i> <span id="modal-user-name">User Profile</span>
                </h5>
                <button class="c-close-btn" data-c-close>&times;</button>
            </div>

            <!-- Body -->
            <div class="c-modal-body" style="background: #fcfcfd;">
                <div id="c-user-details" style="min-height: 200px;">
                    <!-- Content injected via JS -->
                    <div class="c-loader" style="text-align: center; padding: 50px 0; color: #a0aec0;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Loading data...</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="c-modal-footer" style="text-align: left; padding: 16px 24px; background: #fff; border-top: 1px solid #eaecf0; border-radius: 0 0 16px 16px;">
                <button class="c-btn" data-c-close style="background: #f3f4fd; color: #5568f5; border-radius: 20px; padding: 8px 24px; font-weight: 600;">
                    Close View
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('footer_script_content')
<script>
    var datatable_url = '/getDataUser';
    var sweetalert_delete_title = "Delete User?";
    var sweetalert_change_status = "Change Status of User";
    var form_url = '/user';

    $(document).on('click', '.stat-filter-card', function() {
        $('.stat-filter-card').removeClass('active-stat');
        $(this).addClass('active-stat');
        let type = $(this).data('type');
        $('#filter-type').val(type);
        $('#user-table').DataTable().ajax.reload();
    });

    $.extend(true, $.fn.dataTable.defaults, {
        pageLength: 25,
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        ajax: {
            url: APP_URL + datatable_url,
            data: function(d) {
                d.status = $('#filter-status').val();
                d.filter_type = $('#filter-type').val();
            }
        },
        columns: [
            {
                data: null,
                name: 'id',
                render: function (data, type, row, meta) { return meta.row + 1; }
            },
            { 
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    let name = data || 'User';
                    return `<div class="d-flex flex-column">
                                <span class="user_name text-truncate fw-bolder" style="color: #000; font-size: 0.95rem;">${name}</span>
                                <small class="text-muted fw-bold">ID: #${row.id}</small>
                            </div>`;
                }
            },
            { 
                data: 'mobile_number', 
                name: 'mobile_number',
                render: function(data, type, row) {
                    let phoneStr = data || '';
                    return `<div class="d-flex flex-column">
                                <span class="fw-bold">${phoneStr}</span>
                                <small class="text-muted">${row.email || '-'}</small>
                            </div>`;
                }
            },
            { data: 'created_at', name: 'created_at' },
            { 
                data: 'wallet_balance', 
                name: 'wallet_balance', 
                render: function(data) { 
                    return '<span class="fw-bold text-success">₹' + (data || '0.00') + '</span>'; 
                } 
            },
            { 
                data: 'referral_code', 
                name: 'referral_code', 
                render: function(data) { 
                    return data ? '<span class="badge bg-light-info text-dark">' + data + '</span>' : '-'; 
                } 
            },
            {
                data: 'total_appointments',
                name: 'total_appointments',
                searchable: false,
                render: function(data, type, row) {
                    let phoneStr = row.mobile_number || '';
                    let count = data || 0;
                    if (count > 0) {
                        return `<a href="/admin/appointments?phone=${phoneStr}" class="badge bg-light-primary text-primary" style="font-size: 0.9rem; text-decoration: none;">
                                    <i class="bi bi-calendar-check me-50"></i> ${count}
                                </a>`;
                    }
                    return `<span class="badge bg-light-secondary text-secondary" style="font-size: 0.9rem;">0</span>`;
                }
            },
            {
                data: 'role',
                name: 'role',
                render: function (data) {
                    if (data == 2) {
                        return `<span class="badge bg-warning text-white fw-bolder" style="padding: 6px 12px; font-size: 0.85rem; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(255, 159, 67, 0.4);"><i class="bi bi-star-fill me-50"></i> Beautician</span>`;
                    } else {
                        return `<span class="badge bg-info text-white fw-bolder" style="padding: 6px 12px; font-size: 0.85rem; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0, 207, 232, 0.4);"><i class="bi bi-person-fill me-50"></i> User</span>`;
                    }
                }
            },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[0, 'DESC']],
    });

    // Row click to open view modal
    $('#user-table tbody').on('click', 'tr', function (e) {
        // Don't trigger if clicking on action items or inputs
        if ($(e.target).closest('.dropdown, a, button, .dropdown-item').length) {
            return;
        }
        
        let viewBtn = $(this).find('.btn-view');
        if (viewBtn.length) {
            viewBtn.click();
        }
    });

    $(document).on("click", "[data-c-close]", function() {
        $("#c-viewUserModal").removeClass("show");
    });

    $(document).on('click', '.btn-view', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        $("#c-viewUserModal").addClass('show');
        $("#c-user-details").html(
            `<div class="c-loader" style="text-align: center; padding: 50px 0; color: #a0aec0;">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Loading data...</p>
            </div>`
        );
        $("#modal-user-name").text("Loading...");

        $.ajax({
            url: '/admin/user-view/' + id,
            type: 'GET',
            success: function(response) {
                let data = response.data;
                let user = data.user || {};
                
                $("#modal-user-name").text((user.name || "User") + " Profile");

                let joinedDate = user.created_at ? new Date(user.created_at).toLocaleDateString() : '-';
                let mobileVerified = user.mobile_verified_at ? '<span class="badge bg-light-success text-success"><i class="bi bi-check-circle"></i> Verified</span>' : '<span class="badge bg-light-warning text-warning"><i class="bi bi-exclamation-circle"></i> Pending</span>';
                let emailVerified = user.email_verified_at ? '<span class="badge bg-light-success text-success"><i class="bi bi-check-circle"></i> Verified</span>' : '<span class="badge bg-light-warning text-warning"><i class="bi bi-exclamation-circle"></i> Pending</span>';
                let lastLoginDate = user.last_login_at ? new Date(user.last_login_at).toLocaleString() : '-';
                
                let addressesHtml = '';
                if (data.addresses && data.addresses.length > 0) {
                    data.addresses.forEach((addr, index) => {
                        let isDefault = addr.is_default ? '<i class="bi bi-star-fill text-warning ms-2" style="font-size:1.1rem; filter: drop-shadow(0 2px 4px rgba(255,193,7,0.4));" title="Default Address"></i>' : '';
                        let typeBadge = addr.type ? `<span class="badge bg-light-secondary text-secondary me-2" style="font-size: 0.75rem; text-transform: uppercase;">${addr.type}</span>` : '';
                        addressesHtml += `
                            <div class="px-3 py-2 mb-2 d-flex justify-content-between align-items-center" style="background:#fff; border:1px solid ${addr.is_default ? '#9da6f9' : '#e2e8f0'}; border-radius:12px; position: relative; overflow: hidden;">
                                ${addr.is_default ? '<div style="position:absolute; top:0; left:0; width:4px; height:100%; background:#5568f5;"></div>' : ''}
                                
                                <div class="d-flex align-items-center" style="padding-left: ${addr.is_default ? '12px' : '0'};">
                                    <div class="info-icon" style="width:40px; height:40px; background:#fef0f1; color:#f04958; border-radius:50%; margin-right:16px; font-size: 1.1rem; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bolder d-flex align-items-center" style="font-size: 0.9rem; color: #1e293b; margin: 0;">
                                            ${typeBadge || '<span style="color: #475569;">Address ' + (index + 1) + '</span>'}
                                            ${isDefault}
                                        </h6>
                                        <p class="mb-0 text-muted" style="font-size:0.85rem; line-height:1.4; color:#64748b;">
                                            ${addr.address || 'No address line'}
                                        </p>
                                    </div>
                                </div>
                                

                            </div>
                        `;
                    });
                } else {
                    addressesHtml = `<p class="text-muted" style="font-size:0.85rem;">No saved addresses found.</p>`;
                }

                $("#c-user-details").html(`
                    <div class="row align-items-stretch">
                        <!-- Client Contact Card -->
                        <div class="col-md-6 mb-3 d-flex flex-column">
                            <div class="detail-info-card" style="flex: 1;">
                                <div class="detail-section-label">
                                    <i class="bi bi-person"></i> PERSONAL INFORMATION
                                </div>
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-person"></i></div>
                                    <div class="info-content">
                                        <label>Full Name</label>
                                        <p>${user.name ?? '-'}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-envelope"></i></div>
                                    <div class="info-content w-100 d-flex justify-content-between align-items-center">
                                        <div>
                                            <label>Email Address</label>
                                            <p>${user.email ?? 'Not provided'}</p>
                                        </div>
                                        ${user.email_verified_at ? '<span class="badge bg-light-success text-success rounded-pill px-3 py-2">Verified</span>' : '<span class="badge bg-light-warning text-warning rounded-pill px-3 py-2">Pending</span>'}
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-telephone"></i></div>
                                    <div class="info-content w-100 d-flex justify-content-between align-items-center">
                                        <div>
                                            <label>Phone Number</label>
                                            <p>${user.mobile_number ?? 'Not provided'}</p>
                                        </div>
                                        ${user.mobile_verified_at ? '<span class="badge bg-light-success text-success rounded-pill px-3 py-2"><i class="bi bi-check2"></i> Verified</span>' : '<span class="badge bg-light-warning text-warning rounded-pill px-3 py-2">Pending</span>'}
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-calendar-event"></i></div>
                                    <div class="info-content">
                                        <label>Date of Birth</label>
                                        <p>${user.dob ?? 'Not provided'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Information Card -->
                        <div class="col-md-6 mb-3 d-flex flex-column">
                            <div class="detail-info-card" style="flex: 1;">
                                <div class="detail-section-label">
                                    <i class="bi bi-laptop"></i> SYSTEM INFORMATION
                                </div>
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-calendar-check"></i></div>
                                    <div class="info-content">
                                        <label>Joined On</label>
                                        <p>${joinedDate}</p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-clock-history"></i></div>
                                    <div class="info-content">
                                        <label>Last Login</label>
                                        <p>${lastLoginDate}</p>
                                        <small class="text-muted" style="font-size:0.75rem; font-weight:500;">IP: ${user.last_login_ip || user.ip_address || 'Unknown'}</small>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                                    <div class="info-content">
                                        <label>Primary City / Base Address</label>
                                        <p>${user.city_name ?? '-'}</p>
                                        <small class="text-muted" style="font-size:0.75rem; font-weight:500;">${user.address ?? ''}</small>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon" style="background:#fff4e5; color:#f5a623;"><i class="bi bi-person-hearts"></i></div>
                                    <div class="info-content w-100 d-flex justify-content-between align-items-center">
                                        <div>
                                            <label>Referred By</label>
                                            <p>${data.referred_by_user ? data.referred_by_user.name : 'Direct / Organic'}</p>
                                            ${data.referred_by_user ? `<small class="text-muted" style="font-size:0.75rem; font-weight:500;">Code: ${data.referred_by_user.referral_code}</small>` : ''}
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                                    <div class="info-icon" style="background:#e6f6ff; color:#0099ff;"><i class="bi bi-people"></i></div>
                                    <div class="info-content">
                                        <label>Total Referrals Made</label>
                                        <p>${data.total_referrals_made || 0}</p>
                                        <small class="text-muted" style="font-size:0.75rem; font-weight:500;">Users joined using their code</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Saved Addresses -->
                    <div class="detail-info-card mb-3">
                        <div class="detail-section-label">
                            <i class="bi bi-map"></i> SAVED ADDRESSES
                        </div>
                        <div class="addresses-list-container">
                            ${addressesHtml}
                        </div>
                    </div>

                    <!-- Recent Appointments History -->
                    <div class="detail-info-card mb-3">
                        <div class="detail-section-label">
                            <i class="bi bi-calendar-check"></i> RECENT APPOINTMENTS (LAST 5)
                        </div>
                        <div class="table-responsive" style="border: 1px solid #f1f2f4; border-radius: 8px;">
                            <table class="table table-sm mb-0" style="font-size: 0.85rem;">
                                <thead style="background: #f9f9fb;">
                                    <tr>
                                        <th style="padding: 10px 16px; color:#64748b; font-weight:600;">DATE</th>
                                        <th style="padding: 10px 16px; color:#64748b; font-weight:600;">ORDER NO</th>
                                        <th style="padding: 10px 16px; color:#64748b; font-weight:600;">AMOUNT</th>
                                        <th style="padding: 10px 16px; color:#64748b; font-weight:600;">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${(() => {
                                        if (data.recent_appointments && data.recent_appointments.length > 0) {
                                            return data.recent_appointments.map(appt => {
                                                let date = new Date(appt.appointment_date).toLocaleDateString() + ' ' + (appt.appointment_time || '');
                                                let amt = appt.services_data && appt.services_data.summary ? appt.services_data.summary.grand_total : 0;
                                                let statusBadge = '';
                                                if (appt.status == 0) statusBadge = '<span class="badge bg-light-warning text-warning">Pending</span>';
                                                else if (appt.status == 1) statusBadge = '<span class="badge bg-light-primary text-primary">Accepted</span>';
                                                else if (appt.status == 2) statusBadge = '<span class="badge bg-light-info text-info">Ongoing</span>';
                                                else if (appt.status == 3) statusBadge = '<span class="badge bg-light-success text-success">Completed</span>';
                                                else if (appt.status == 4) statusBadge = '<span class="badge bg-light-danger text-danger">Cancelled</span>';
                                                else statusBadge = '<span class="badge bg-light-secondary text-secondary">Unknown</span>';
                                                
                                                return `
                                                    <tr>
                                                        <td style="padding: 10px 16px;">${date}</td>
                                                        <td style="padding: 10px 16px;"><a href="/admin/appointments?order_no=${encodeURIComponent(appt.order_number)}" class="fw-bold text-primary">${appt.order_number || appt.id}</a></td>
                                                        <td class="fw-bold" style="padding: 10px 16px;">₹${parseFloat(amt).toFixed(2)}</td>
                                                        <td style="padding: 10px 16px;">${statusBadge}</td>
                                                    </tr>
                                                `;
                                            }).join('');
                                        } else {
                                            return `
                                                <tr>
                                                    <td colspan="4" class="text-center" style="padding: 30px 0;">
                                                        <div style="opacity: 0.5;">
                                                            <i class="bi bi-calendar-x" style="font-size: 2.5rem; color: #5568f5;"></i>
                                                            <p class="mt-2 text-muted fw-bold">No appointments found</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            `;
                                        }
                                    })()}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Wallet Transactions History -->
                    <div class="detail-info-card mb-3">
                        <div class="detail-section-label">
                            <i class="bi bi-wallet2"></i> WALLET TRANSACTIONS HISTORY
                        </div>
                        <div class="table-responsive" style="border: 1px solid #f1f2f4; border-radius: 8px;">
                            <table class="table table-sm mb-0" style="font-size: 0.85rem;">
                                <thead style="background: #f9f9fb;">
                                    <tr>
                                        <th style="padding: 10px 16px; color:#64748b; font-weight:600;">DATE</th>
                                        <th style="padding: 10px 16px; color:#64748b; font-weight:600;">TYPE</th>
                                        <th style="padding: 10px 16px; color:#64748b; font-weight:600;">AMOUNT</th>
                                        <th style="padding: 10px 16px; color:#64748b; font-weight:600;">DESCRIPTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${(() => {
                                        if (data.wallet_transactions && data.wallet_transactions.length > 0) {
                                            return data.wallet_transactions.map(txn => {
                                                let date = new Date(txn.created_at).toLocaleDateString();
                                                let color = txn.type === 'credit' ? 'text-success' : 'text-danger';
                                                let sign = txn.type === 'credit' ? '+' : '-';
                                                return `
                                                    <tr>
                                                        <td style="padding: 10px 16px;">${date}</td>
                                                        <td style="padding: 10px 16px;"><span class="badge bg-light-${txn.type === 'credit' ? 'success' : 'danger'}">${txn.type}</span></td>
                                                        <td class="${color} fw-bold" style="padding: 10px 16px;">${sign}₹${txn.amount}</td>
                                                        <td style="padding: 10px 16px; color: #475569;">${txn.description || '-'}</td>
                                                    </tr>
                                                `;
                                            }).join('');
                                        } else {
                                            return `
                                                <tr>
                                                    <td colspan="4" class="text-center" style="padding: 30px 0;">
                                                        <div style="opacity: 0.5;">
                                                            <i class="bi bi-wallet" style="font-size: 2.5rem; color: #5568f5;"></i>
                                                            <p class="mt-2 text-muted fw-bold">No transactions found</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            `;
                                        }
                                    })()}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer Stats Row -->
                    <div class="stats-footer-card">
                        <div class="stat-block">
                            <div class="stat-icon" style="background: #f3f4fd; color: #5568f5;">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Total Appointments Booked</div>
                                <div style="font-size: 1.25rem; font-weight: 800; color: #1e293b;">${data.total_appointments || 0}</div>
                            </div>
                        </div>
                        <div class="stat-block">
                            <div class="stat-icon" style="background: #fff4e5; color: #f5a623;">
                                <i class="bi bi-tags"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Coupons Claimed</div>
                                <div style="font-size: 1.25rem; font-weight: 800; color: #1e293b;">${data.total_coupons || 0}</div>
                            </div>
                        </div>
                        <div class="stat-block">
                            <div class="stat-icon" style="background: #e6f6ff; color: #0099ff;">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Wallet Balance</div>
                                <div style="font-size: 1.25rem; font-weight: 800; color: #28c76f;">₹${parseFloat(user.wallet_balance || 0).toFixed(2)}</div>
                            </div>
                        </div>
                        <div class="stat-block" style="border: none;">
                            <div style="text-align: right;">
                                <div style="font-size: 0.85rem; color: #1e293b; font-weight: 700;">Total Lifetime Value</div>
                                <div style="font-size: 1.5rem; font-weight: 800; color: #5568f5;">₹${parseFloat(data.total_spent || 0).toFixed(2)}</div>
                            </div>
                        </div>
                    </div>
                `);
            },
            error: function() {
                $("#c-user-details").html(
                    `<div class="text-center py-5 text-danger"><i class="bi bi-exclamation-triangle fs-1"></i><p>Failed to load data</p></div>`
                );
            }
        });
    });

</script>
<script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{time()}}"></script>
@endsection
