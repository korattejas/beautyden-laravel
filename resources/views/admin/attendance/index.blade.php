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
                                            <div class="leave-block type-{{ $leave->type }}" 
                                                 title="{{ $leave->reason ?? $leave->type_text }}"
                                                 onclick="deleteLeave({{ $leave->id }})">
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
                <div class="legend-item"><div class="legend-box type-1"></div> Leave / Absent</div>
                <div class="legend-item"><div class="legend-box type-2"></div> Personal Work</div>
                <div class="legend-item"><div class="legend-box type-3"></div> Sick</div>
                <div class="legend-item"><div class="legend-box type-4"></div> Holiday</div>
                <div class="legend-item" style="margin-left: auto; color: #ea5455;"><i class="bi bi-info-circle"></i> Click on a block to delete, or click any cell to mark leave.</div>
            </div>
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
                            <option value="1">Leave / Absent</option>
                            <option value="2">Personal Work</option>
                            <option value="3">Sick</option>
                            <option value="4">Holiday</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Reason / Notes (Optional)</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Explain why..."></textarea>
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

    function deleteLeave(id) {
        if(confirm("Confirm: Mark this staff back to Available for these dates?")) {
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
</script>
@endsection
