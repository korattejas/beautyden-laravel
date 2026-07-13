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

    /* Premium Detail Modal Enhancements */
    #c-viewUserModal .c-modal-content {
        border: none;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }

    #c-viewUserModal .c-modal-header {
        background: linear-gradient(135deg, #102365 0%, #1a4a7a 100%);
        padding: 20px 24px;
    }

    .detail-section-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #7367f0;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-info-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 16px;
        height: 100%;
        border: 1px solid #edf2f7;
        transition: all 0.3s ease;
    }

    .detail-info-card:hover {
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-color: #7367f0;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }

    .info-item:last-child {
        margin-bottom: 0;
    }

    .info-icon {
        width: 32px;
        height: 32px;
        background: rgba(115, 103, 240, 0.1);
        color: #7367f0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .info-content label {
        display: block;
        font-size: 0.72rem;
        color: #82868b;
        font-weight: 700;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-content p {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.3;
    }

    .summary-box {
        background: #fdfdfd;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
        width: 100%;
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 0.95rem;
        font-weight: 500;
        color: #475569;
    }

    .summary-line:last-child {
        margin-bottom: 0;
    }

    .summary-total {
        border-top: 2px dashed #dbdade;
        margin-top: 15px;
        padding-top: 15px;
        font-weight: 800;
        font-size: 1.2rem;
        color: #1e293b;
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
            <div class="row g-1 mb-2">
                <div class="col-md col-sm-4 col-6">
                    <div class="card h-100 mb-0 stat-filter-card active-stat" data-type="total">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #f3e8ff !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-people" style="font-size: 1.2rem; color: #7c3aed;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $totalUsers }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 0.85rem;">Total</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-4 col-6">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="active">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #f0fdf4 !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-check" style="font-size: 1.2rem; color: #16a34a;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $activeUsers }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 0.85rem;">Active</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-4 col-6">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="suspended">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #fff1f2 !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-x" style="font-size: 1.2rem; color: #e11d48;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $suspendedUsers }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 0.85rem;">Suspended</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-6 col-6">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="app">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #eff6ff !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-phone" style="font-size: 1.2rem; color: #3b82f6;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $appUsers }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 0.85rem;">App Users</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-6 col-12">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="web">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #fefce8 !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-globe" style="font-size: 1.2rem; color: #ca8a04;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $webUsers }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 0.85rem;">Web Users</p>
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
                            <div class="card-datatable table-responsive p-2">
                                <table class="dt-column-search table w-100 dataTable" id="user-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>User Info</th>
                                            <th>Contact</th>
                                            <th>Joined On</th>
                                            <th>Wallet</th>
                                            <th>Ref. Code</th>
                                            <th data-search="false">Appointments</th>
                                            <th data-stuff="App User,Web User">Role</th>
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
    <div class="c-modal-dialog" style="max-width: 800px;">
        <div class="c-modal-content">
            
            <!-- Header -->
            <div class="c-modal-header">
                <h5 class="c-modal-title">
                    <i class="bi bi-person-circle"></i> <span id="modal-user-name">User Profile</span>
                </h5>
                <button class="c-close-btn" data-c-close>&times;</button>
            </div>

            <!-- Body -->
            <div class="c-modal-body p-4" style="background: #fcfcfd;">
                <div id="c-user-details" style="min-height: 200px;">
                    <!-- Content injected via JS -->
                    <div class="c-loader" style="text-align: center; padding: 50px 0; color: #a0aec0;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Loading data...</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="c-modal-footer" style="text-align: right;">
                <button class="c-btn" data-c-close style="background: #e2e8f0; color: #475569;">
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
                    return `<div class="d-flex align-items-center">
                                <div class="avatar bg-light-primary me-1"><span class="avatar-content">${name.charAt(0)}</span></div>
                                <div class="d-flex flex-column">
                                    <span class="user_name text-truncate fw-bold text-dark">${name}</span>
                                    <small class="emp_post text-muted">ID: #${row.id}</small>
                                </div>
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
                    let color = data == 1 ? 'bg-light-info' : 'bg-light-secondary';
                    let label = data == 1 ? 'App User' : 'Web User';
                    return `<span class="badge ${color} text-dark">${label}</span>`;
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
                        let isDefault = addr.is_default ? '<span class="badge bg-primary ms-1" style="font-size:0.65rem;">Default</span>' : '';
                        let typeBadge = addr.type ? `<span class="badge bg-light-secondary text-secondary me-1">${addr.type}</span>` : '';
                        addressesHtml += `
                            <div class="p-2 mb-2" style="background:#fff; border:1px solid #e2e8f0; border-radius:10px;">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-geo-alt text-danger me-2"></i>
                                    <h6 class="mb-0 fw-bolder">${typeBadge} Address ${index + 1} ${isDefault}</h6>
                                </div>
                                <p class="mb-0 text-muted" style="font-size:0.85rem; padding-left: 24px;">${addr.address || 'No address line'}</p>
                            </div>
                        `;
                    });
                } else {
                    addressesHtml = `<p class="text-muted" style="font-size:0.85rem;">No saved addresses found.</p>`;
                }

                $("#c-user-details").html(`
                    <div class="row">
                        <!-- Client Contact Card -->
                        <div class="col-md-6 mb-3">
                            <div class="detail-section-label">
                                <i class="bi bi-person-circle"></i> Personal Information
                            </div>
                            <div class="detail-info-card">
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-person"></i></div>
                                    <div class="info-content">
                                        <label>Full Name</label>
                                        <p>${user.name ?? '-'}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-envelope"></i></div>
                                    <div class="info-content">
                                        <label>Email Address</label>
                                        <p>${user.email ?? 'Not provided'} ${emailVerified}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-telephone"></i></div>
                                    <div class="info-content">
                                        <label>Phone Number</label>
                                        <p>${user.mobile_number ?? 'Not provided'} ${mobileVerified}</p>
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
                        <div class="col-md-6 mb-3">
                            <div class="detail-section-label">
                                <i class="bi bi-laptop"></i> System Information
                            </div>
                            <div class="detail-info-card" style="border-left: 4px solid #7367f0;">
                                <div class="info-item">
                                    <div class="info-icon" style="background: rgba(115, 103, 240, 0.2);"><i class="bi bi-calendar-check"></i></div>
                                    <div class="info-content">
                                        <label>Joined On</label>
                                        <p>${joinedDate}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon" style="background: rgba(115, 103, 240, 0.2);"><i class="bi bi-shield-check"></i></div>
                                    <div class="info-content">
                                        <label>Role & Status</label>
                                        <p>${user.role == 1 ? 'App User' : 'Web User'} / ${user.status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-danger">Suspended</span>'}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon" style="background: rgba(115, 103, 240, 0.2);"><i class="bi bi-clock-history"></i></div>
                                    <div class="info-content">
                                        <label>Last Login</label>
                                        <p>${lastLoginDate}</p>
                                        <small class="text-muted" style="font-size:0.7rem;">IP: ${user.last_login_ip || user.ip_address || 'Unknown'}</small>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon" style="background: rgba(115, 103, 240, 0.2);"><i class="bi bi-building"></i></div>
                                    <div class="info-content">
                                        <label>Primary City / Base Address</label>
                                        <p>${user.city_name ?? '-'} <br>
                                           <small class="text-muted" style="font-size:0.75rem;">${user.address ?? ''}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Saved Addresses -->
                    <div class="detail-section-label mt-2">
                        <i class="bi bi-map"></i> Saved Addresses
                    </div>
                    <div class="detail-info-card" style="padding: 15px; border-left: 4px solid #00cfe8;">
                        ${addressesHtml}
                    </div>

                    <!-- Wallet Transactions History -->
                    <div class="detail-section-label mt-2">
                        <i class="bi bi-wallet2"></i> Wallet Transactions History
                    </div>
                    <div class="detail-info-card" style="padding: 15px; border-left: 4px solid #28c76f; max-height: 250px; overflow-y: auto;">
                        <table class="table table-sm" style="font-size: 0.85rem;">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
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
                                                    <td>${date}</td>
                                                    <td><span class="badge bg-light-${txn.type === 'credit' ? 'success' : 'danger'}">${txn.type}</span></td>
                                                    <td class="${color} fw-bold">${sign}₹${txn.amount}</td>
                                                    <td>${txn.description || '-'}</td>
                                                </tr>
                                            `;
                                        }).join('');
                                    } else {
                                        return `<tr><td colspan="4" class="text-center text-muted">No transactions found</td></tr>`;
                                    }
                                })()}
                            </tbody>
                        </table>
                    </div>

                    <div class="summary-box">
                        <div class="summary-line">
                            <span>Total Appointments Booked</span>
                            <span style="font-weight: 700; color: #1e293b;">${data.total_appointments || 0}</span>
                        </div>
                        <div class="summary-line">
                            <span>Coupons Claimed</span>
                            <span style="font-weight: 700; color: #1e293b;">${data.total_coupons || 0}</span>
                        </div>
                        <div class="summary-line">
                            <span>Wallet Balance</span>
                            <span style="font-weight: 700; color: #28c76f;">₹${parseFloat(user.wallet_balance || 0).toFixed(2)}</span>
                        </div>
                        <div class="summary-line summary-total">
                            <span>Total Lifetime Value</span>
                            <span class="text-primary">₹${parseFloat(data.total_spent || 0).toFixed(2)}</span>
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