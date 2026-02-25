@extends('admin.layouts.app')

@section('header_style_content')
<style>
    /* Premium Member Grid */
    .member-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 15px;
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
        margin-top: 15px;
    }

    .member-card {
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 15px 10px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .member-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        background: #fff;
    }

    .member-card.selected {
        border-color: #1a4a7a;
        background: #eff6ff;
        box-shadow: 0 4px 12px rgba(26, 74, 122, 0.12);
    }

    .member-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        margin: 0 auto 10px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e2e8f0;
        font-weight: bold;
        color: #64748b;
        font-size: 1.2rem;
        background-size: cover;
        background-position: center;
    }

    .member-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #1e293b;
        margin-bottom: 2px;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .member-role {
        font-size: 0.75rem;
        color: #64748b;
        display: block;
    }

    .selection-indicator {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 22px;
        height: 22px;
        background: #1a4a7a;
        color: #fff;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .member-card.selected .selection-indicator {
        display: flex;
    }

    /* Modal Styling */
    #c-assignModal .c-modal-dialog {
        max-width: 650px;
    }

    .member-search-wrap {
        position: relative;
        margin-bottom: 10px;
    }

    .member-search-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .member-search-wrap input#memberSearch {
        padding-left: 35px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding-top: 10px;
        padding-bottom: 10px;
        background: #f8fafc;
    }

    .member-search-wrap input#memberSearch:focus {
        background: #fff;
        border-color: #1a4a7a;
        box-shadow: 0 0 0 3px rgba(26, 74, 122, 0.1);
    }

    /* Premium Detail Modal Enhancements */
    #c-viewAppointmentModal .c-modal-content {
        border: none;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }

    #c-viewAppointmentModal .c-modal-header {
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
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }

    .premium-table-container {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #edf2f7;
        margin-top: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    .premium-table {
        width: 100%;
        border-collapse: collapse;
    }

    .premium-table thead th {
        background: #f8f9fa;
        color: #475569;
        font-weight: 800;
        font-size: 0.8rem;
        padding: 14px 16px;
        text-transform: uppercase;
        text-align: left;
        letter-spacing: 0.8px;
    }

    .premium-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.9rem;
        color: #1e293b;
    }

    .premium-table tbody tr:last-child td {
        border-bottom: none;
    }

    .summary-box {
        background: #fdfdfd;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
        width: 100%;
        max-width: 350px;
        margin-left: auto;
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
        font-size: 1.4rem;
        color: #7367f0;
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Appointments</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">Appointments</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary">
                    Add Appointments
                </a>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 300px;">
                        <div class="mb-2">
                            <label class="form-label">Status</label>
                            <select id="filter-status" class="form-select">
                                <option value="">All</option>
                                <option value="1">Pending</option>
                                <option value="2">Assigned</option>
                                <option value="3">Completed</option>
                                <option value="4">Rejected</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Appointment Date</label>
                            <input type="date" id="filter-appointment-date" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Appointment Time</label>
                            <input type="time" id="filter-appointment-time" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Created Date</label>
                            <input type="date" id="filter-created-date" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">City</label>
                            <select id="filter-city" class="form-select">
                                <option value="">All Cities</option>
                                @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button id="btn-apply-filters" class="btn btn-sm btn-primary">
                                Apply
                            </button>
                            <button id="btn-reset-filters" class="btn btn-sm btn-secondary">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Summary Boxes -->
            <div class="row g-1 mb-2">
                <div class="col">
                    <div class="card h-100 mb-0" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s ease;">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #f3e8ff !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-calendar-check" style="font-size: 1.2rem; color: #7c3aed;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $totalAppointments }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 1rem;">Total</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 mb-0" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s ease;">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #ecfdf5 !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-currency-rupee" style="font-size: 1.2rem; color: #059669;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">₹{{ number_format($totalRevenue, 2) }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 1rem;">Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 mb-0" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s ease;">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #fff7ed !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-clock-history" style="font-size: 1.2rem; color: #ea580c;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $pendingAppointments }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 1rem;">Pending</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 mb-0" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s ease;">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #e0f2fe !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-check" style="font-size: 1.2rem; color: #0284c7;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $assignedAppointments }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 1rem;">Assigned</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 mb-0" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s ease;">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #dcfce7 !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-check2-circle" style="font-size: 1.2rem; color: #16a34a;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $completedAppointments }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 1rem;">Completed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column Search -->
            <section id="column-search-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-datatable">
                                <table class="dt-column-search table w-100 dataTable" id="table-appointments">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            {{-- <th>Service Category</th> --}}
                                            {{-- <th>Service</th> --}}
                                            <th>Order Number</th>
                                            <th>First Name</th>
                                            {{-- <th>Last Name</th> --}}
                                            <th>Phone</th>
                                            {{-- <th>Quantity</th> --}}
                                            {{-- <th>Price</th> --}}
                                            {{-- <th>Discount Price</th> --}}
                                            {{-- <th>Service Address</th> --}}
                                            <th>Appointment Date</th>
                                            <th>Appointment Time</th>
                                            <th data-stuff="Pending,Assigned,Completed,Rejected">Status</th>
                                            <th data-search="false">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--/ Column Search -->
        </div>
    </div>
</div>
<!-- Assign Team Members Modal -->
<div id="c-assignModal" class="c-modal">
    <div class="c-modal-dialog">
        <div class="c-modal-content">

            <!-- Header -->
            <div class="c-modal-header">
                <h5 class="c-modal-title"><i class="bi bi-people-fill"></i> Assign Team Members</h5>
                <button class="c-close-btn" data-c-close>&times;</button>
            </div>

            <!-- Body -->
            <div class="c-modal-body">
                <form id="assignForm">
                    <input type="hidden" id="value_id" name="value_id">
                    
                    <div class="member-search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" id="memberSearch" class="form-control shadow-none" placeholder="Search team members...">
                    </div>

                    <div class="member-grid" id="memberGrid">
                        @foreach ($teamMembers as $member)
                        <div class="member-card" data-id="{{ $member->id }}" data-name="{{ strtolower($member->name) }}">
                            <div class="selection-indicator"><i class="bi bi-check"></i></div>
                            <div class="member-avatar" 
                                style="{{ $member->icon && file_exists(public_path('uploads/team-member/' . $member->icon)) 
                                    ? 'background-image: url(' . asset('uploads/team-member/' . $member->icon) . ')' 
                                    : '' }}">
                                @if(!($member->icon && file_exists(public_path('uploads/team-member/' . $member->icon))))
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                @endif
                            </div>
                            <span class="member-name">{{ $member->name }}</span>
                            <span class="member-role">{{ $member->role ?? 'Professional' }}</span>
                        </div>
                        @endforeach
                    </div>

                    <select id="team_members" name="team_members[]" class="d-none" multiple>
                        @foreach ($teamMembers as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Footer -->
            <div class="c-modal-footer">
                <button class="c-btn c-btn-secondary" data-c-close>
                    <i class="bi bi-x-circle"></i> Close
                </button>
                <button type="button" id="saveMembers" class="c-btn c-btn-primary">
                    <i class="bi bi-check-circle"></i> Save
                </button>
            </div>

        </div>
    </div>
</div>

<div id="c-viewAppointmentModal" class="c-modal">
    <div class="c-modal-dialog" style="max-width: 850px;">
        <div class="c-modal-content">

            <!-- Header -->
            <div class="c-modal-header">
                <h5 class="c-modal-title" style="margin:0;">
                    <i class="bi bi-stars"></i> Appointment Insights
                </h5>

                <div style="display:flex; align-items:center; gap:12px;">

                    <!-- 🔥 Copy Button -->
                    <button id="copyAppointmentData"
                        style="
                            background: rgba(255,255,255,0.15);
                            color:#fff;
                            border: 1px solid rgba(255,255,255,0.3);
                            padding:8px 16px;
                            border-radius:8px;
                            font-size:13px;
                            font-weight: 600;
                            cursor:pointer;
                            display:flex;
                            align-items:center;
                            gap:8px;
                            transition:all 0.3s ease;
                            backdrop-filter: blur(5px);
                        "
                        onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.15)'">

                        <i class="bi bi-clipboard2-check"></i> <span>Copy Details</span>
                    </button>

                    <!-- Close Button -->
                    <button class="c-close-btn" data-c-close 
                        style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2); border-radius: 50%; font-size: 20px; transition: all 0.3s;">
                        &times;
                    </button>

                </div>
            </div>

            <!-- Body -->
            <div class="c-modal-body" id="c-appointment-details" style="background: #fff; padding: 24px;">
                <div class="c-loader">
                    <div class="c-spinner"></div>
                    <span>Revealing information...</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="c-modal-footer" style="background: #f8f9fa; border-top: 1px solid #edf2f7; padding: 16px 24px;">
                <div style="display: flex; align-items: center; gap: 8px; color: #82868b; font-size: 0.85rem;">
                    <i class="bi bi-shield-check text-success"></i>
                    <span>Verified Appointment Record</span>
                </div>
                <button class="c-btn" data-c-close style="background: #444050; border-radius: 8px; padding: 10px 20px;">
                    <i class="bi bi-x-lg me-1"></i> Close View
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('footer_script_content')
<script>
    const sweetalert_delete_title = "Delete Appointment?";
    const sweetalert_change_status = "Change Status of Appointment";
    const form_url = '/appointments';
    datatable_url = '/getDataAppointments';
    
    // Member Selection Logic
    $(document).on('click', '.member-card', function() {
        let card = $(this);
        let id = card.data('id');
        let select = $('#team_members');
        
        card.toggleClass('selected');
        
        // Sync with hidden select
        let option = select.find(`option[value="${id}"]`);
        if (card.hasClass('selected')) {
            option.prop('selected', true);
        } else {
            option.prop('selected', false);
        }
    });

    // Search Filtering
    $(document).on('keyup', '#memberSearch', function() {
        let value = $(this).val().toLowerCase();
        $('.member-card').each(function() {
            let name = $(this).data('name');
            if (name.includes(value)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $(document).on('click', '.assign-member', function() {
        // Reset selections when opening modal
        $('.member-card').removeClass('selected');
        $('#team_members').val([]);
        $('#memberSearch').val('');
        $('.member-card').show();
        
        const value_id = $(this).data('id');
        const currentMembers = $(this).data('members'); // Comma-separated IDs

        if (currentMembers) {
            const memberIds = currentMembers.toString().split(',');
            memberIds.forEach(id => {
                $(`.member-card[data-id="${id}"]`).addClass('selected');
                $(`#team_members option[value="${id}"]`).prop('selected', true);
            });
        }

        $('#value_id').val(value_id);
        $("#c-assignModal").addClass("show");
    });

    $.extend(true, $.fn.dataTable.defaults, {
        pageLength: 100,
        lengthMenu: [
            [10, 25, 50, 100, 200, -1],
            [10, 25, 50, 100, 200, "All"]
        ],
        columns: [{
                data: null,
                name: 'id',
                render: function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            // {
            //     data: 'service_category_name',
            //     name: 'service_category_name'
            // },
            // {
            //     data: 'service_name',
            //     name: 'service_name'
            // },
            {
                data: 'order_number',
                name: 'order_number'
            },
            {
                data: 'first_name',
                name: 'first_name'
            },
            // {
            //     data: 'last_name',
            //     name: 'last_name'
            // },
            {
                data: 'phone',
                name: 'phone'
            },
            // { data: 'quantity', name: 'quantity' },
            // { data: 'price', name: 'price' },
            // { data: 'discount_price', name: 'discount_price' },
            // { data: 'service_address', name: 'service_address' },
            {
                data: 'appointment_date',
                name: 'appointment_date'
            },
            {
                data: 'appointment_time',
                name: 'appointment_time'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false
            },
        ],
        order: [
            [4, 'DESC']
        ],
    });

    $(document).on('click', '#saveMembers', function() {
        let selected = $('#team_members').val();
        let value_id = $('#value_id').val();

        $.ajax({
            url: 'appointments/assign_member',
            method: 'POST',
            data: {
                value_id: value_id,
                members: selected
            },
            success: function(res) {
                location.reload();
                $('#c-assignModal').removeClass("show");
            }
        });
    });

    $(document).on("click", "[data-c-close]", function() {
        $("#c-assignModal").removeClass("show");
    });

    let currentAppointmentData = null

    $(document).on('click', '.btn-view', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        $("#c-viewAppointmentModal").addClass("show");
        $("#c-appointment-details").html(
            `<div class="c-loader"><div class="c-spinner"></div><span>Loading...</span></div>`
        );

        $.ajax({
            url: '/admin/appointments-view/' + id,
            type: 'GET',
            success: function(response) {

                let data = response.data;
                currentAppointmentData = data;

                let client = data.client || {};
                let appointment = data.appointment || {};
                let services = data.services || [];
                let summary = data.summary || {};

                let servicesHtml = '';

                if (services.length > 0) {
                    servicesHtml = `
                        <div class="detail-section-label mt-4">
                            <i class="bi bi-layers-half"></i> Service Inventory
                        </div>
                        <div class="premium-table-container">
                            <table class="premium-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Name</th>
                                        <th style="text-align: right;">Price</th>
                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    services.forEach((s, index) => {
                        servicesHtml += `
                            <tr>
                                <td style="text-align: center; color: #82868b;">${index + 1}</td>
                                <td style="font-weight: 700; color: #1e293b;">
                                    ${s.name ?? '-'}
                                    <div style="font-size: 0.75rem; color: #82868b; text-transform: capitalize; font-weight: 500;">${s.type ?? 'Standard'}</div>
                                </td>
                                <td style="text-align: right; font-weight: 600;">₹${parseFloat(s.price).toFixed(2)}</td>
                                <td style="text-align: center; font-weight: 600;">${s.qty}</td>
                                <td style="text-align: right; font-weight: 800; color: #7367f0; font-size: 1rem;">
                                    ₹${parseFloat(s.total).toFixed(2)}
                                </td>
                            </tr>
                        `;
                    });
                    servicesHtml += `</tbody></table></div>`;
                }

                $("#c-appointment-details").html(`
                    <div class="row">
                        <!-- Client Contact Card -->
                        <div class="col-md-6 mb-3">
                            <div class="detail-section-label">
                                <i class="bi bi-person-circle"></i> Client Information
                            </div>
                            <div class="detail-info-card">
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-person"></i></div>
                                    <div class="info-content">
                                        <label>Full Name</label>
                                        <p>${client.first_name ?? '-'} ${client.last_name ?? ''}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-envelope"></i></div>
                                    <div class="info-content">
                                        <label>Email Address</label>
                                        <p>${client.email ?? 'Not provided'}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-telephone"></i></div>
                                    <div class="info-content">
                                        <label>Phone Number</label>
                                        <p>${client.phone ?? 'Not provided'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule & Location Card -->
                        <div class="col-md-6 mb-3">
                            <div class="detail-section-label">
                                <i class="bi bi-geo-alt-fill"></i> Schedule & Logistics
                            </div>
                            <div class="detail-info-card" style="border-left: 4px solid #7367f0;">
                                <div class="info-item">
                                    <div class="info-icon" style="background: rgba(115, 103, 240, 0.2);"><i class="bi bi-calendar-check"></i></div>
                                    <div class="info-content">
                                        <label>Appointment Date</label>
                                        <p>${appointment.date ?? '-'}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon" style="background: rgba(115, 103, 240, 0.2);"><i class="bi bi-clock-history"></i></div>
                                    <div class="info-content">
                                        <label>Reserved Time</label>
                                        <p>${appointment.time ?? '-'}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon" style="background: rgba(115, 103, 240, 0.2);"><i class="bi bi-geo"></i></div>
                                    <div class="info-content">
                                        <label>Service Location</label>
                                        <p>${appointment.address ?? 'On-site'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    ${servicesHtml}

                    <div class="summary-box">
                        <div class="summary-line">
                            <span>Subtotal</span>
                            <span style="font-weight: 700; color: #1e293b;">₹${parseFloat(summary.sub_total || 0).toFixed(2)}</span>
                        </div>
                        <div class="summary-line">
                            <span>Traveling Charges</span>
                            <span style="font-weight: 700; color: #1e293b;">+ ₹${parseFloat(summary.travel_charges || 0).toFixed(2)}</span>
                        </div>
                        <div class="summary-line text-danger">
                            <span style="font-weight: 600;">Discount (${summary.discount_percent || 0}%)</span>
                            <span style="font-weight: 700;">- ₹${parseFloat(summary.discount_amount || 0).toFixed(2)}</span>
                        </div>
                        <div class="summary-line summary-total">
                            <span>Grand Total</span>
                            <span>₹${parseFloat(summary.grand_total || 0).toFixed(2)}</span>
                        </div>
                    </div>

                    <div class="mt-4 p-3" style="background: #fff8eb; border-radius: 12px; border: 1px solid #ffe5b4; box-shadow: 0 4px 12px rgba(255, 159, 67, 0.08);">
                        <div class="detail-section-label" style="color: #ff9f43; margin-bottom: 8px;">
                            <i class="bi bi-sticky"></i> Special Instructions
                        </div>
                        <p style="margin:0; font-size: 0.95rem; color: #1e293b; font-weight: 600; font-style: italic; line-height: 1.5;">
                            "${appointment.special_notes ?? 'No special instructions provided for this appointment.'}"
                        </p>
                    </div>
                `);

                // Update copy button text
                $("#copyAppointmentData").html('<i class="bi bi-clipboard2-check"></i> <span>Copy Details</span>');
            },
            error: function() {
                $("#c-appointment-details").html(
                    `<div class="text-center py-5 text-danger"><i class="bi bi-exclamation-triangle fs-1"></i><p>Failed to load data</p></div>`
                );
            }
        });
    });

    $(document).on('click', '#copyAppointmentData', function() {
        if (!currentAppointmentData) return;

        let d = currentAppointmentData;
        let client = d.client || {};
        let appointment = d.appointment || {};
        let summary = d.summary || {};
        let services = d.services || [];

        let text = `🌟 APPOINTMENT DETAILS 🌟\n\n`;
        text += `👤 CLIENT INFORMATION\n`;
        text += `Name: ${client.first_name} ${client.last_name}\n`;
        text += `Phone: ${client.phone}\n`;
        text += `Email: ${client.email}\n\n`;

        text += `📅 SCHEDULE & LOCATION\n`;
        text += `Date: ${appointment.date}\n`;
        text += `Time: ${appointment.time}\n`;
        text += `Address: ${appointment.address}\n\n`;

        text += `🛠️ SERVICES\n`;
        services.forEach((s, idx) => {
            text += `${idx + 1}. ${s.name} (x${s.qty}) - ₹${s.total}\n`;
        });
        text += `\n💰 FINANCIAL SUMMARY\n`;
        text += `Subtotal: ₹${summary.sub_total}\n`;
        text += `Travel Charges: ₹${summary.travel_charges}\n`;

        text += `Grand Total: ₹${summary.grand_total}\n\n`;

        text += `We’ll review your booking and confirm shortly.\n`;
        text += `Thank you for choosing BeautyDen 💖\n\n`;
        text += `📞 Support: +91 95747 58282`;

        navigator.clipboard.writeText(text).then(() => {

            let btn = $('#copyAppointmentData');
            btn.html('<i class="bi bi-check-circle"></i> Copied');
            btn.css('background', '#28a745');

            setTimeout(() => {
                btn.html('<i class="bi bi-clipboard"></i> Copy');
                btn.css('background', '#1a4a7a');
            }, 2000);

        });

    });




    $(document).on("click", "[data-c-close]", function() {
        $("#c-viewAppointmentModal").removeClass("show");
    });
</script>
<script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection