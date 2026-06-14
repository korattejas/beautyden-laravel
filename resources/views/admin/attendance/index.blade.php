@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root {
        --mst-primary: #1a237e;
        --mst-primary-soft: rgba(26, 35, 126, 0.08);
        --mst-bg: #f8fafc;
        --mst-card-bg: #ffffff;
        --leave-color: #ea5455;
        --personal-color: #7367f0;
        --sick-color: #ff9f43;
        --holiday-color: #28c76f;
        --mst-radius: 12px;
        --mst-shadow: 0 4px 15px rgba(0,0,0,0.04);
    }

    body {
        background-color: var(--mst-bg);
        font-family: 'Poppins', sans-serif;
    }

    .timeline-container {
        background: #fff;
        border-radius: var(--mst-radius);
        box-shadow: var(--mst-shadow);
        border: 1px solid #eef2f7;
        overflow-x: auto;
        margin-top: 1.5rem;
    }

    .timeline-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1200px;
    }

    .timeline-table th, .timeline-table td {
        border: 1px solid #f1f5f9;
        text-align: center;
        padding: 0;
        height: 60px;
    }

    .timeline-table th {
        background: #f8fafc;
        font-weight: 700;
        font-size: 0.85rem;
        color: #64748b;
        padding: 10px 5px;
    }

    .staff-col {
        width: 250px;
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 10;
        text-align: left !important;
        padding-left: 15px !important;
        font-weight: 600;
        color: var(--mst-primary);
        box-shadow: 5px 0 10px rgba(0,0,0,0.02);
    }

    .day-col {
        width: 40px;
    }

    .today-highlight {
        background: rgba(26, 35, 126, 0.03);
    }

    .leave-block {
        height: 40px;
        margin: 10px 2px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s;
        overflow: hidden;
        white-space: nowrap;
        padding: 0 5px;
    }

    .leave-block:hover {
        transform: scale(1.05);
        z-index: 5;
    }

    .type-1 { background-color: var(--leave-color); }
    .type-2 { background-color: var(--personal-color); }
    .pending-leave { opacity: 0.8; border: 2px dashed #1a237e; color: #1a237e !important; background-color: #fff !important; }
    .type-3 { background-color: var(--sick-color); }
    .type-4 { background-color: var(--holiday-color); }

    .add-leave-cell {
        cursor: pointer;
        transition: background 0.2s;
    }

    .add-leave-cell:hover {
        background: #f0f4ff;
    }

    .legend {
        display: flex;
        gap: 20px;
        margin-top: 15px;
        flex-wrap: wrap;
        padding: 10px;
        background: #fff;
        border-radius: 8px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
    }

    .legend-box {
        width: 15px;
        height: 15px;
        border-radius: 3px;
    }

    .header-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .calendar-filters {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .filter-select {
        padding: 8px 15px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        font-weight: 600;
        color: #1e293b;
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Staff Attendance & Availability</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Attendance</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="header-controls">
                <div class="calendar-filters">
                    <select id="jump-month" class="filter-select">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m, 1)) }}</option>
                        @endforeach
                    </select>
                    <select id="jump-year" class="filter-select">
                        @foreach(range(date('Y')-1, date('Y')+2) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <button id="btn-refresh" class="btn btn-primary">
                        <i class="bi bi-arrow-repeat"></i> Go
                    </button>
                </div>
                <div>
                     <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeaveModal">
                        <i class="bi bi-plus-lg"></i> Mark Manual Leave
                    </button>
                </div>
            </div>

            <div class="timeline-container shadow-sm">
                <table class="timeline-table">
                    <thead>
                        <tr>
                            <th class="staff-col">Beautician Name</th>
                            @for($i = 1; $i <= $daysInMonth; $i++)
                                @php 
                                    $currentDate = Carbon\Carbon::create($year, $month, $i);
                                    $isToday = $currentDate->isToday();
                                    $isWeekend = $currentDate->isWeekend();
                                @endphp
                                <th class="day-col {{ $isToday ? 'today-highlight' : '' }}" style="{{ $isWeekend ? 'color: #ea5455;' : '' }}">
                                    {{ $i }}<br>
                                    <small>{{ $currentDate->format('D') }}</small>
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teamMembers as $member)
                            <tr>
                                <td class="staff-col">{{ $member->name }}</td>
                                @for($i = 1; $i <= $daysInMonth; $i++)
                                    @php 
                                        $currentDateStr = Carbon\Carbon::create($year, $month, $i)->format('Y-m-d');
                                        $leave = $unavailabilities->filter(function($u) use ($member, $currentDateStr) {
                                            return $u->team_member_id == $member->id && 
                                                   $currentDateStr >= $u->start_date && 
                                                   $currentDateStr <= $u->end_date;
                                        })->first();
                                    @endphp
                                    <td class="day-col {{ Carbon\Carbon::create($year, $month, $i)->isToday() ? 'today-highlight' : '' }} add-leave-cell" 
                                        data-staff-id="{{ $member->id }}" 
                                        data-staff-name="{{ $member->name }}"
                                        data-date="{{ $currentDateStr }}">
                                        @if($leave)
                                            <div class="leave-block type-{{ $leave->type }} {{ $leave->status == 0 ? 'pending-leave' : '' }}" 
                                                 title="{{ $leave->reason ?? $leave->type_text }} {{ $leave->status == 0 ? '(Pending)' : '' }}"
                                                 onclick="showLeaveDetails({{ $leave->id }}, '{{ addslashes($member->name) }}', '{{ Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} to {{ Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}', '{{ $leave->type_text }}', '{{ addslashes(str_replace(\"\n\", \" \", $leave->reason)) }}', {{ $leave->status }})">
                                                {{ $leave->start_date == $currentDateStr ? $leave->type_text : '' }}
                                            </div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="legend border shadow-sm">
                <div class="legend-item"><div class="legend-box type-1"></div> Full Day</div>
                <div class="legend-item"><div class="legend-box type-2"></div> Half Day</div>
                <div class="legend-item"><div class="legend-box pending-leave"></div> Pending Request</div>
                <div class="legend-item" style="margin-left: auto; color: #ea5455;"><i class="bi bi-info-circle"></i> Click on a block to view details and update status, or click any cell to mark leave.</div>
            </div>

            @if(isset($pendingRequests) && $pendingRequests->count() > 0)
            <div class="mt-4">
                <h4 class="mb-3" style="color: var(--mst-primary);">Pending Leave Requests</h4>
                <div class="card shadow-sm border-0" style="border-radius: var(--mst-radius);">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: var(--mst-bg);">
                                <tr>
                                    <th>Beautician</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Type</th>
                                    <th>Reason</th>
                                    <th>Requested On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingRequests as $req)
                                <tr>
                                    <td class="font-weight-bold" style="color: var(--mst-primary);">{{ $req->teamMember->name ?? 'N/A' }}</td>
                                    <td>{{ Carbon\Carbon::parse($req->start_date)->format('d M Y') }}</td>
                                    <td>{{ Carbon\Carbon::parse($req->end_date)->format('d M Y') }}</td>
                                    <td><span class="badge bg-secondary">{{ $req->type_text }}</span></td>
                                    <td>{{ $req->reason }}</td>
                                    <td>{{ $req->created_at->format('d M Y h:i A') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="updateLeaveStatus({{ $req->id }}, 1)"><i class="bi bi-check-circle"></i> Approve</button>
                                        <button class="btn btn-sm btn-danger ms-1" onclick="updateLeaveStatus({{ $req->id }}, 2)"><i class="bi bi-x-circle"></i> Reject</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Leave Modal -->
<div class="modal fade" id="addLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">Mark Staff Unavailability</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="leave-form">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Select Beautician</label>
                        <select name="team_member_id" id="modal-staff-id" class="form-select" required>
                            @foreach($teamMembers as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Start Date</label>
                            <input type="date" name="start_date" id="modal-start-date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">End Date</label>
                            <input type="date" name="end_date" id="modal-end-date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Reason Type</label>
                        <select name="type" class="form-select" required>
                            <option value="1">Full Day</option>
                            <option value="2">Half Day</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Explain why..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Leave Details & Action Modal -->
<div class="modal fade" id="leaveDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header text-white" style="background-color: var(--mst-primary);">
                <h5 class="modal-title text-white">Leave Request Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-0">
                <table class="table table-borderless mb-0">
                    <tr><th style="width: 130px; color: #64748b;">Beautician:</th><td><span id="dtl-name" class="font-weight-bold" style="color: #1e293b;"></span></td></tr>
                    <tr><th style="color: #64748b;">Duration:</th><td><span id="dtl-dates" style="color: #1e293b;"></span></td></tr>
                    <tr><th style="color: #64748b;">Type:</th><td><span id="dtl-type" class="badge bg-secondary"></span></td></tr>
                    <tr><th style="color: #64748b;">Status:</th><td><span id="dtl-status" class="badge"></span></td></tr>
                    <tr><th style="color: #64748b; vertical-align: top;">Reason:</th><td><div id="dtl-reason" class="text-dark p-2 rounded" style="background-color: #f8fafc; font-size: 0.9rem; min-height: 50px;"></div></td></tr>
                </table>
            </div>
            <div class="modal-footer justify-content-between mt-3 border-top pt-3">
                <button type="button" class="btn btn-outline-danger btn-sm" id="btn-delete-leave"><i class="bi bi-trash"></i> Delete Leave</button>
                <div>
                    <button type="button" class="btn btn-success btn-sm me-1" id="btn-approve-leave"><i class="bi bi-check-circle"></i> Approve</button>
                    <button type="button" class="btn btn-danger btn-sm" id="btn-reject-leave"><i class="bi bi-x-circle"></i> Reject</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer_script_content')
<script>
    $(document).ready(function() {
        // Refresh based on month/year selection
        $('#btn-refresh').on('click', function() {
            let m = $('#jump-month').val();
            let y = $('#jump-year').val();
            window.location.href = `{{ route('admin.attendance.index') }}?month=${m}&year=${y}`;
        });

        // Click cell to open modal with pre-filled data
        $('.add-leave-cell').on('click', function(e) {
            if($(e.target).hasClass('leave-block')) return; // ignore click on leave block itself

            let staffId = $(this).data('staff-id');
            let date = $(this).data('date');

            $('#modal-staff-id').val(staffId);
            $('#modal-start-date').val(date);
            $('#modal-end-date').val(date);
            $('#addLeaveModal').modal('show');
        });

        // Submit form
        $('#leave-form').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            
            $.ajax({
                url: "{{ route('admin.attendance.store') }}",
                type: "POST",
                data: formData,
                success: function(res) {
                    if(res.success) {
                        toastr.success(res.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        toastr.error(res.message || "Error updating attendance");
                    }
                },
                error: function(err) {
                    toastr.error("Something went wrong!");
                }
            });
        });
    });

    let currentSelectedLeaveId = null;

    function showLeaveDetails(id, name, dates, typeText, reason, status) {
        currentSelectedLeaveId = id;
        $('#dtl-name').text(name);
        $('#dtl-dates').text(dates);
        $('#dtl-type').text(typeText);
        $('#dtl-reason').text(reason || 'No reason provided by beautician.');
        
        let statusText = status === 0 ? 'Pending' : (status === 1 ? 'Approved' : 'Rejected');
        let statusClass = status === 0 ? 'bg-warning text-dark' : (status === 1 ? 'bg-success' : 'bg-danger');
        
        $('#dtl-status').text(statusText).removeClass().addClass('badge ' + statusClass);

        // Show/Hide buttons based on current status
        if(status === 1) {
            $('#btn-approve-leave').hide();
            $('#btn-reject-leave').show();
        } else if(status === 2) {
            $('#btn-approve-leave').show();
            $('#btn-reject-leave').hide();
        } else {
            // Pending
            $('#btn-approve-leave').show();
            $('#btn-reject-leave').show();
        }

        $('#leaveDetailsModal').modal('show');
    }

    $('#btn-approve-leave').click(function() {
        if(currentSelectedLeaveId) updateLeaveStatus(currentSelectedLeaveId, 1);
    });
    
    $('#btn-reject-leave').click(function() {
        if(currentSelectedLeaveId) updateLeaveStatus(currentSelectedLeaveId, 2);
    });
    
    $('#btn-delete-leave').click(function() {
        if(currentSelectedLeaveId) deleteLeave(currentSelectedLeaveId);
    });

    function deleteLeave(id) {
        if(confirm("Confirm: Delete this leave record entirely?")) {
            $.ajax({
                url: "{{ url('admin/attendance') }}/" + id,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    toastr.success(res.message);
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }
    }

    function updateLeaveStatus(id, status) {
        let actionStr = status === 1 ? 'Approve' : 'Reject';
        if(confirm(`Are you sure you want to ${actionStr} this leave request?`)) {
            $.ajax({
                url: "{{ route('admin.attendance.updateStatus') }}",
                type: "POST",
                data: { 
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                },
                success: function(res) {
                    if(res.success) {
                        toastr.success(res.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        toastr.error(res.message || "Error updating status");
                    }
                },
                error: function(err) {
                    toastr.error("Something went wrong!");
                }
            });
        }
    }
</script>
@endsection
