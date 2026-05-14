@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --mst-primary: #1a237e;
        --mst-bg: #f8fafc;
        --mst-card-bg: #ffffff;
        --mst-text-main: #1e293b;
        --mst-text-muted: #64748b;
        --mst-radius: 12px;
        --mst-shadow: 0 4px 15px rgba(0,0,0,0.04);
    }

    body {
        background-color: var(--mst-bg);
        font-family: 'Poppins', sans-serif;
    }

    .settlement-card {
        background: var(--mst-card-bg);
        border-radius: var(--mst-radius);
        box-shadow: var(--mst-shadow);
        border: 1px solid #eef2f7;
        margin-top: 20px;
    }

    .table thead th {
        background-color: #f8f9fa;
        color: var(--mst-text-main);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #eef2f7;
    }

    .table tbody td {
        vertical-align: middle;
        padding: 1rem;
        color: var(--mst-text-main);
        font-weight: 500;
    }

    .form-control-sm {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0.5rem;
        font-weight: 600;
    }

    .form-control-sm:focus {
        border-color: var(--mst-primary);
        box-shadow: 0 0 0 2px rgba(26, 35, 126, 0.1);
    }

    .btn-update {
        background-color: var(--mst-primary);
        color: #fff;
        border-radius: 8px;
        font-weight: 600;
        padding: 0.5rem 1rem;
        transition: all 0.2s;
    }

    .btn-update:hover {
        background-color: #0d1440;
        transform: translateY(-1px);
        color: #fff;
    }

    .last-updated-text {
        font-size: 0.8rem;
        color: var(--mst-text-muted);
        font-weight: 600;
    }

    .content-header-title {
        color: var(--mst-text-main);
        font-weight: 800;
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Settlements</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">Beautician Settlements</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="card settlement-card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Active Beauticians Settlement Ledger</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="30%">Beautician Name</th>
                                    <th width="20%">Company Owed (₹)</th>
                                    <th width="20%">Beautician Owed (₹)</th>
                                    <th width="20%">Last Entry Date</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bolder">{{ $member->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" class="form-control form-control-sm company-to-beautician" 
                                                value="{{ $member->settlement->company_to_beautician }}" 
                                                data-id="{{ $member->id }}" step="0.01">
                                        </div>
                                        <small class="text-muted">Company to Beautician</small>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" class="form-control form-control-sm beautician-to-company" 
                                                value="{{ $member->settlement->beautician_to_company }}" 
                                                data-id="{{ $member->id }}" step="0.01">
                                        </div>
                                        <small class="text-muted">Beautician to Company</small>
                                    </td>
                                    <td class="last-updated-{{ $member->id }} last-updated-text">
                                        {{ $member->settlement->updated_at->format('d M Y, h:i A') }}
                                    </td>
                                    <td>
                                        <button class="btn btn-update btn-sm btn-update-settlement" data-id="{{ $member->id }}">
                                            <i class="bi bi-check2-circle me-25"></i> Update
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    $(document).on('click', '.btn-update-settlement', function() {
        var btn = $(this);
        var id = btn.data('id');
        var company_to_beautician = $('.company-to-beautician[data-id="' + id + '"]').val();
        var beautician_to_company = $('.beautician-to-company[data-id="' + id + '"]').val();

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

        $.ajax({
            url: "{{ route('admin.settlement.update') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                team_member_id: id,
                company_to_beautician: company_to_beautician,
                beautician_to_company: beautician_to_company
            },
            success: function(response) {
                btn.prop('disabled', false).html('<i class="bi bi-check2-circle me-25"></i> Update');
                if(response.success) {
                    toastr.success(response.message);
                    $('.last-updated-' + id).text(response.updated_at);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="bi bi-check2-circle me-25"></i> Update');
                toastr.error('Something went wrong. Please try again.');
            }
        });
    });
</script>
@endsection
