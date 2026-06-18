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
                                        <div class="fw-bold fs-5 company-owed-display" id="display-company-{{ $member->id }}" data-val="{{ $member->settlement->company_to_beautician }}" style="color: {{ $member->settlement->company_to_beautician > 0 ? 'red' : 'inherit' }}">
                                            ₹ {{ number_format($member->settlement->company_to_beautician, 2) }}
                                        </div>
                                        <small class="text-muted">Company to Beautician</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold fs-5 beautician-owed-display" id="display-beautician-{{ $member->id }}" data-val="{{ $member->settlement->beautician_to_company }}" style="color: {{ $member->settlement->beautician_to_company > 0 ? 'green' : 'inherit' }}">
                                            ₹ {{ number_format($member->settlement->beautician_to_company, 2) }}
                                        </div>
                                        <small class="text-muted">Beautician to Company</small>
                                    </td>
                                    <td class="last-updated-{{ $member->id }} last-updated-text">
                                        {{ $member->settlement->updated_at->format('d M Y, h:i A') }}
                                    </td>
                                    <td>
                                        <button class="btn btn-update btn-sm btn-open-modal" 
                                            data-id="{{ $member->id }}" 
                                            data-name="{{ $member->name }}"
                                            data-company="{{ $member->settlement->company_to_beautician }}"
                                            data-beautician="{{ $member->settlement->beautician_to_company }}">
                                            <i class="bi bi-pencil-square me-25"></i> Manage
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th><strong>Total</strong></th>
                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text fw-bold">₹</span>
                                            <input type="text" class="form-control form-control-sm fw-bold" id="total_company_owed" readonly value="0.00" style="background-color: #f8f9fa;">
                                        </div>
                                    </th>
                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text fw-bold">₹</span>
                                            <input type="text" class="form-control form-control-sm fw-bold" id="total_beautician_owed" readonly value="0.00" style="background-color: #f8f9fa;">
                                        </div>
                                    </th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom View Modal -->
<div id="settlementModal" class="c-modal">
    <div class="c-modal-dialog" style="max-width: 600px;">
        <div class="c-modal-content">
            
            <!-- Header -->
            <div class="c-modal-header">
                <h5 class="c-modal-title">
                    <i class="bi bi-wallet2"></i> <span id="modal-beautician-name">Beautician Name</span>
                </h5>
                <button class="c-close-btn" data-c-close>&times;</button>
            </div>

            <!-- Body -->
            <div class="c-modal-body" style="padding: 24px;">
                <input type="hidden" id="modal-member-id">
                
                <div style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; color: #1a4a7a; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-arrow-left-right"></i> Account Balances
                </div>
                
                <div style="background: #f8f9fa; border-radius: 12px; padding: 20px; border: 1px solid #edf2f7;">
                    
                    <div class="form-group mb-3">
                        <label style="font-size: 0.85rem; color: #475569; font-weight: 600; margin-bottom: 5px; text-transform: uppercase;">Company to Beautician (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white" style="font-weight: bold;">₹</span>
                            <input type="number" id="modal-company-owed" class="form-control" style="font-weight: bold; font-size: 1.1rem;" step="0.01">
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Amount the company owes to the beautician.</small>
                    </div>

                    <div class="form-group mb-0">
                        <label style="font-size: 0.85rem; color: #475569; font-weight: 600; margin-bottom: 5px; text-transform: uppercase;">Beautician to Company (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white" style="font-weight: bold;">₹</span>
                            <input type="number" id="modal-beautician-owed" class="form-control" style="font-weight: bold; font-size: 1.1rem;" step="0.01">
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Amount the beautician owes to the company.</small>
                    </div>

                </div>
            </div>

            <!-- Footer -->
            <div class="c-modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
                <button class="c-btn" data-c-close style="background: #e2e8f0; color: #475569;">
                    Cancel
                </button>
                <button class="c-btn" id="btn-save-modal">
                    <i class="bi bi-check2-circle"></i> Save Changes
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('footer_script_content')
<script>
    function calculateTotalsAndColors() {
        let totalCompany = 0;
        let totalBeautician = 0;

        $('.company-owed-display').each(function() {
            let val = parseFloat($(this).attr('data-val')) || 0;
            totalCompany += val;
        });

        $('.beautician-owed-display').each(function() {
            let val = parseFloat($(this).attr('data-val')) || 0;
            totalBeautician += val;
        });

        $('#total_company_owed').val(totalCompany.toFixed(2));
        if (totalCompany > 0) {
            $('#total_company_owed').css('color', 'red');
        } else {
            $('#total_company_owed').css('color', 'inherit');
        }

        $('#total_beautician_owed').val(totalBeautician.toFixed(2));
        if (totalBeautician > 0) {
            $('#total_beautician_owed').css('color', 'green');
        } else {
            $('#total_beautician_owed').css('color', 'inherit');
        }
    }

    $(document).ready(function() {
        calculateTotalsAndColors();

        // Color input text based on value in modal
        $(document).on('input', '#modal-company-owed', function() {
            let val = parseFloat($(this).val()) || 0;
            if (val > 0) {
                $(this).css('color', 'red');
                $(this).prev('.input-group-text').css('color', 'red');
            } else {
                $(this).css('color', 'inherit');
                $(this).prev('.input-group-text').css('color', 'inherit');
            }
        });

        $(document).on('input', '#modal-beautician-owed', function() {
            let val = parseFloat($(this).val()) || 0;
            if (val > 0) {
                $(this).css('color', 'green');
                $(this).prev('.input-group-text').css('color', 'green');
            } else {
                $(this).css('color', 'inherit');
                $(this).prev('.input-group-text').css('color', 'inherit');
            }
        });
    });

    $(document).on('click', '.btn-open-modal', function() {
        var btn = $(this);
        var id = btn.attr('data-id');
        var name = btn.attr('data-name');
        var companyOwed = btn.attr('data-company');
        var beauticianOwed = btn.attr('data-beautician');

        $('#modal-member-id').val(id);
        $('#modal-beautician-name').text(name);
        $('#modal-company-owed').val(companyOwed).trigger('input');
        $('#modal-beautician-owed').val(beauticianOwed).trigger('input');

        $('#settlementModal').addClass('show');
    });

    $(document).on("click", "[data-c-close]", function() {
        $("#settlementModal").removeClass("show");
    });

    $(document).on('click', '#btn-save-modal', function() {
        var btn = $(this);
        var id = $('#modal-member-id').val();
        var company_to_beautician = $('#modal-company-owed').val() || 0;
        var beautician_to_company = $('#modal-beautician-owed').val() || 0;

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

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
                btn.prop('disabled', false).html('<i class="bi bi-check2-circle"></i> Save Changes');
                if(response.success) {
                    toastr.success(response.message);
                    
                    // Update table display
                    let compDis = $('#display-company-' + id);
                    compDis.attr('data-val', company_to_beautician);
                    compDis.html('₹ ' + parseFloat(company_to_beautician).toFixed(2));
                    compDis.css('color', company_to_beautician > 0 ? 'red' : 'inherit');

                    let beautDis = $('#display-beautician-' + id);
                    beautDis.attr('data-val', beautician_to_company);
                    beautDis.html('₹ ' + parseFloat(beautician_to_company).toFixed(2));
                    beautDis.css('color', beautician_to_company > 0 ? 'green' : 'inherit');

                    // Update button data attributes
                    let updateBtn = $('.btn-open-modal[data-id="' + id + '"]');
                    updateBtn.attr('data-company', company_to_beautician);
                    updateBtn.attr('data-beautician', beautician_to_company);

                    $('.last-updated-' + id).text(response.updated_at);
                    
                    calculateTotalsAndColors();
                    $('#settlementModal').removeClass('show');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="bi bi-check2-circle"></i> Save Changes');
                toastr.error('Something went wrong. Please try again.');
            }
        });
    });
</script>
@endsection
