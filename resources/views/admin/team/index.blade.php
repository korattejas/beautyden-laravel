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
        --mst-text-main: #1e293b;
        --mst-text-muted: #64748b;
        --mst-radius: 12px;
        --mst-shadow: 0 4px 15px rgba(0,0,0,0.04);
        --mst-shadow-hover: 0 10px 25px rgba(0,0,0,0.08);
    }

    body {
        background-color: var(--mst-bg);
    }

    .team-members-page {
        font-family: 'Poppins', sans-serif;
    }

    .team-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem 0;
    }

    .team-member-card {
        background: var(--mst-card-bg);
        border-radius: var(--mst-radius);
        padding: 2rem 1.5rem; /* Increased vertical padding */
        box-shadow: var(--mst-shadow);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        border: 1px solid #eef2f7;
    }

    .team-member-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--mst-shadow-hover);
        border-color: var(--mst-primary-soft);
    }

    .card-profile-header {
        text-align: center;
        margin-bottom: 1.25rem;
    }

    .avatar-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 12px;
    }

    .circle-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .initials-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: var(--mst-primary-soft);
        color: var(--mst-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.85rem;
        font-weight: 700;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .status-badge-absolute {
        position: absolute;
        top: 0;
        right: 0;
        transform: translate(25%, -25%);
    }

    .card-info h5 {
        margin-bottom: 8px; /* Added spacing */
        font-weight: 700;
        color: var(--mst-text-main);
        font-size: 1.35rem; /* Slightly larger */
    }

    .card-info .role-label {
        font-size: 0.85rem; /* Increased font size */
        font-weight: 600;
        color: #7367f0;
        background: rgba(115, 103, 240, 0.08);
        padding: 4px 14px;
        border-radius: 50px;
        margin-top: 6px;
        display: inline-block;
    }

    .member-address {
        font-size: 0.95rem;
        color: #5e6d82;
        margin-top: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.8em;
        line-height: 1.4;
        font-weight: 500;
    }

    .all-time-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin: 1.5rem 0;
        padding: 12px 0;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
        row-gap: 15px;
    }

    .stat-box-mini {
        text-align: center;
    }

    .stat-box-mini label {
        display: block;
        font-size: 0.75rem; /* Increased font size */
        font-weight: 700;
        color: var(--mst-text-muted);
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .stat-box-mini span {
        font-size: 1.15rem; /* Increased font size */
        font-weight: 700;
        color: var(--mst-text-main);
    }

    .revenue-text { color: #059669 !important; } /* Deeper emerald for better contrast */

    .card-actions-strip {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin-top: auto;
    }

    .btn-action-pill {
        width: 42px; /* Slightly larger */
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: all 0.2s;
        border: 1px solid transparent;
        background: #f8fafc;
        color: #64748b;
    }

    .btn-action-pill:hover {
        transform: scale(1.1);
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .btn-edit:hover { color: #1a237e; border-color: #1a237e; }
    .btn-delete:hover { color: #ea5455; border-color: #ea5455; }
    .btn-status:hover { color: #28c76f; border-color: #28c76f; }
    .btn-priority:hover { color: #7367f0; border-color: #7367f0; }
    .btn-view-card:hover { color: #1e293b; border-color: #1e293b; }

    .search-input-group {
        flex: 1 1 0;
        min-width: 0;
        width: auto;
        max-width: none;
        display: flex;
        flex-wrap: nowrap;
        align-items: stretch;
    }

    .search-input-group .input-group-text {
        background: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        color: var(--mst-text-muted);
        padding: 0 0.75rem 0 0.9rem !important;
    }

    .search-input-group .form-control,
    .search-input-group .form-control:focus {
        border: none !important;
        padding: 0 0.9rem 0 0.25rem !important;
        font-size: 0.9rem;
        box-shadow: none !important;
        background: transparent !important;
        min-height: 44px !important;
        height: 44px !important;
        border-radius: 0 !important;
        outline: none !important;
    }


    /* Pagination Styling */
    .pagination-wrapper {
        margin-top: 2.5rem;
        display: flex;
        justify-content: center;
    }

    .pagination-wrapper .pagination {
        gap: 5px;
    }

    .pagination-wrapper .page-link {
        border-radius: 8px !important;
        border: none;
        padding: 8px 16px;
        font-weight: 600;
        color: var(--mst-text-main);
        transition: all 0.2s;
        box-shadow: var(--mst-shadow);
    }

    .pagination-wrapper .page-item.active .page-link {
        background-color: var(--mst-primary);
        color: #fff;
    }

    .pagination-wrapper .page-link:hover {
        background-color: var(--mst-primary-soft);
        color: var(--mst-primary);
    }

    /* Premium Stat Cards (Appointment Style) */
    .stat-filter-card {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border: 2px solid transparent !important;
        border-radius: 20px !important;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
    }
    .stat-filter-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.1) !important;
        border-color: rgba(26, 35, 126, 0.2) !important;
    }
    .stat-filter-card.active-stat {
        background-color: #f0f4ff !important;
        border-color: var(--mst-primary) !important;
    }

    .summary-info h3 {
        margin: 0;
        font-weight: 800;
        font-size: 1.6rem;
        color: var(--mst-text-main);
        line-height: 1.2;
    }

    .summary-info span {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--mst-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    /* Report Table */
    .report-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    .report-table th {
        background: var(--mst-bg);
        padding: 15px 18px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.95rem; /* Increased from 0.8rem */
        color: var(--mst-text-muted);
        text-align: left;
        border-radius: 8px;
    }
    .report-table td {
        padding: 20px 18px;
        font-size: 1.05rem; 
        font-weight: 700;
        color: var(--mst-text-main);
        background: #fff;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }
    .report-table tr td:first-child { border-left: 1px solid #f1f5f9; border-radius: 10px 0 0 10px; }
    .report-table tr td:last-child { border-right: 1px solid #f1f5f9; border-radius: 0 10px 10px 0; }
    .report-table tr:nth-child(even) td { background: #fcfdfe; }
    .report-table tr:hover td { background: #f8faff; }
    
    .report-total-text {
        font-size: 1.35rem; 
        font-weight: 800;
        color: #1a237e;
    }

    .chart-container,
    .pa-chart-panel {
        background: var(--mst-card-bg);
        border: 1px solid #eef2f7;
        border-radius: var(--mst-radius);
        box-shadow: var(--mst-shadow);
        padding: 1.35rem 1.5rem;
    }

    .report-modal-header-title {
        font-size: 1.4rem;
        font-weight: 700;
    }

    /* Premium Location Alert - Glassmorphism Edit */
    .location-banner {
        background: rgba(14, 165, 233, 0.05);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(14, 165, 233, 0.2);
        border-radius: 14px;
        padding: 0.8rem 1.2rem;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1.5rem;
        max-width: fit-content; /* Make it fit content instead of full width */
    }

    .location-icon-box {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 4px 10px rgba(14, 165, 233, 0.2);
    }

    .location-text span {
        display: block;
        font-size: 0.7rem;
        color: #0284c7;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        line-height: 1;
        margin-bottom: 4px;
    }

    .location-text h6 {
        margin: 0;
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
        line-height: 1.2;
    }

    .location-badge {
        background: #0ea5e9;
        color: #fff;
        padding: 4px 12px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-left: 10px;
    }

    /* Distance Badge Refinement */
    .distance-pill {
        background: #f0f9ff;
        color: #0284c7;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.9rem; /* Increased font size */
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1.5px solid #e0f2fe;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .distance-pill i {
        font-size: 1rem;
    }

    .role-label {
        font-size: 0.9rem !important;
        padding: 5px 15px !important;
    }

    /* Custom Premium Modal Styles */
    .c-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(8px);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .c-modal.show {
        display: flex;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .c-modal-dialog {
        width: 95%;
        max-width: 900px;
        position: relative;
    }

    .c-modal-content {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        overflow: hidden;
    }

    .c-modal-header {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .c-modal-title {
        margin: 0;
        font-weight: 700;
        font-size: 1.25rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .c-close-btn {
        background: #f8fafc;
        border: none;
        color: #64748b;
        font-size: 1.5rem;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .c-close-btn:hover { background: #f1f5f9; color: #ef4444; }

    .c-modal-body { padding: 1.5rem; max-height: calc(100vh - 140px); overflow-y: auto; }

    .c-profile-section {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .c-profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: 4px solid #fff;
    }

    .c-profile-initials {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(115, 103, 240, 0.1);
        color: #7367f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: 4px solid #fff;
    }

    .c-profile-info h4 { margin: 0; font-weight: 800; color: #1e293b; font-size: 1.6rem; }
    .c-profile-info p { margin: 5px 0 0; color: #64748b; font-weight: 600; font-size: 1.1rem; }

    .c-row { display: flex; flex-wrap: wrap; margin: 0 -10px; }
    .c-col-6 { width: 50%; padding: 0 10px; margin-bottom: 20px; }
    .c-col-12 { width: 100%; padding: 0 10px; margin-bottom: 20px; }

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

    .c-include-badge {
        display: inline-block;
        background: #fff;
        border: 1px solid #e2e8f0;
        padding: 4px 12px;
        border-radius: 8px;
        margin-right: 5px;
        margin-bottom: 5px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #475569;
    }

    .c-modal-footer {
        padding: 1.25rem 1.5rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .c-btn {
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        background: #fff;
        border: 1px solid #e2e8f0;
        color: #1a237e;
    }

    .c-btn:hover { background: #f1f5f9; transform: translateY(-1px); }

    .c-loader { text-align: center; padding: 40px 0; color: #64748b; font-weight: 600; }
    .c-spinner {
        width: 32px;
        height: 32px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #1a237e;
        border-radius: 50%;
        margin: 0 auto 15px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    /* Mobile Responsive Card Details */
    @media (max-width: 576px) {
        .c-col-6 { width: 100%; }
        .c-profile-section { flex-direction: column; text-align: center; }
    }

    .fw-800 { font-weight: 800 !important; }

    /* Platform Retention Modal */
    .pa-retention-modal .pa-retention-modal-dialog {
        width: min(920px, 94vw);
        max-width: 920px;
    }

    .pa-retention-modal-content {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18);
    }

    .pa-retention-modal-header {
        background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
        color: #fff;
        padding: 1.1rem 1.35rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: none;
    }

    .pa-retention-modal-header .c-modal-title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #fff;
    }

    .pa-retention-modal-close {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .pa-retention-modal-close:hover {
        background: rgba(255, 255, 255, 0.25);
    }

    .pa-retention-modal-body {
        padding: 1.25rem 1.35rem 1.5rem;
        background: #fff;
        max-height: 75vh;
        overflow-y: auto;
    }

    .pa-modal-search-row {
        display: flex;
        align-items: stretch;
        gap: 0.65rem;
        margin-bottom: 1.25rem;
    }

    .pa-modal-search-input {
        flex: 1;
        min-width: 0;
    }

    .pa-retention-modal .search-input-group {
        display: flex;
        flex-wrap: nowrap;
        align-items: stretch;
        border: 1.5px solid var(--pa-border);
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .pa-retention-modal .search-input-group:focus-within {
        border-color: var(--pa-primary);
        box-shadow: 0 0 0 3px var(--pa-primary-glow);
    }

    .pa-retention-modal .search-input-group .input-group-text {
        background: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        color: var(--pa-text-muted);
        padding: 0 0.75rem 0 0.9rem !important;
        display: flex;
        align-items: center;
        min-height: 44px;
    }

    .pa-retention-modal .search-input-group .form-control,
    .pa-retention-modal .search-input-group .form-control:focus {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        min-height: 44px !important;
        height: 44px !important;
        padding: 0 0.9rem 0 0.25rem !important;
        border-radius: 0 !important;
        flex: 1 1 auto;
        min-width: 0;
        outline: none !important;
    }

    .pa-modal-search-row .pa-btn {
        flex-shrink: 0;
        min-width: 110px;
    }

    .pa-retention-customer-card {
        background: var(--pa-surface-2);
        border: 1px solid var(--pa-border);
        border-radius: 14px;
        padding: 1rem 1.15rem;
        margin-bottom: 1rem;
    }

    .pa-retention-customer-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding-bottom: 0.75rem;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid var(--pa-border);
    }

    .pa-retention-customer-head h6 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--pa-text);
        display: flex;
        align-items: center;
        gap: 0.35rem;
        flex-wrap: wrap;
    }

    .pa-retention-rank {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        padding: 0.15rem 0.45rem;
        border-radius: 8px;
        background: var(--pa-primary-light);
        color: var(--pa-primary);
        font-size: 0.75rem;
        font-weight: 800;
    }

    .pa-retention-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }

    .pa-retention-table {
        width: 100%;
        border-collapse: collapse;
    }

    .pa-retention-table thead th {
        background: #e2e8f0;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        padding: 0.55rem 0.75rem;
    }

    .pa-retention-table thead th:first-child { border-radius: 8px 0 0 8px; }
    .pa-retention-table thead th:last-child { border-radius: 0 8px 8px 0; }

    .pa-retention-table tbody td {
        padding: 0.65rem 0.75rem;
        font-size: 0.85rem;
        color: var(--pa-text-secondary);
        border-bottom: 1px dashed #cbd5e1;
        vertical-align: middle;
    }

    .pa-retention-table tbody tr:last-child td {
        border-bottom: none;
    }

    @media (max-width: 575px) {
        .pa-modal-search-row {
            flex-wrap: wrap;
        }

        .pa-modal-search-row .pa-btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('page_title', 'Team Members')
@section('page_heading', 'Team Members')

@section('content')
    <div class="pa-list-page team-members-page">
            @include('admin.layouts.crud-header', [
                'title' => 'Team Members',
                'items' => [
                    ['label' => 'Home', 'url' => route('admin.dashboard')],
                    ['label' => 'Team Members'],
                ],
                'actions' => view('admin.team.partials.header-actions')->render(),
            ])

            <div class="pa-list-toolbar pa-list-search-bar">
                <div class="input-group search-input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="search-member" class="form-control border-0 shadow-none" placeholder="Search name..." value="{{ request('search') }}">
                </div>
                <div class="input-group search-input-group">
                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" id="search-address" class="form-control border-0 shadow-none" placeholder="Search address (15km)..." value="{{ request('address_search') }}">
                </div>
            </div>

            <div class="pa-list-content">
                @if(request('address_search'))
                    <div class="location-banner">
                        <div class="location-icon-box">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="location-text">
                            <span>Searching Radius: 2000 k.m.</span>
                            <h6>Proximity results for: {{ request('address_search') }}</h6>
                        </div>
                        <div class="location-badge">
                            Active filter
                        </div>
                    </div>
                @endif

                <!-- Summary Stats (Premium Style) -->
                <div class="row g-2 mb-4">
                    <div class="col-md-4">
                        <div class="card stat-filter-card h-100 mb-0">
                            <div class="card-body p-2 d-flex align-items-center">
                                <div class="avatar p-1 me-2" style="background: rgba(40, 199, 111, 0.1); border-radius: 15px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-person-check-fill" style="font-size: 1.8rem; color: #28c76f;"></i>
                                </div>
                                <div class="summary-info">
                                    <h3>{{ $active_count }}</h3>
                                    <span>Active Members</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-filter-card h-100 mb-0">
                            <div class="card-body p-2 d-flex align-items-center">
                                <div class="avatar p-1 me-2" style="background: rgba(234, 84, 85, 0.1); border-radius: 15px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-person-x-fill" style="font-size: 1.8rem; color: #ea5455;"></i>
                                </div>
                                <div class="summary-info">
                                    <h3>{{ $inactive_count }}</h3>
                                    <span>Inactive Members</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-filter-card h-100 mb-0">
                            <div class="card-body p-2 d-flex align-items-center">
                                <div class="avatar p-1 me-2" style="background: rgba(115, 103, 240, 0.1); border-radius: 15px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-person-heart" style="font-size: 1.8rem; color: #7367f0;"></i>
                                </div>
                                <div class="summary-info">
                                    <h3>{{ $total_return_customers }}</h3>
                                    <span>Return Customers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Return Customers Chart -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="chart-container pa-chart-panel">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                                <div>
                                    <h4 class="mb-1 fw-bold">Beautician Return Performance</h4>
                                    <p class="text-muted small mb-0">Total customers who returned after being served by each beautician</p>
                                </div>
                                <button type="button" id="btn-platform-retention" class="pa-btn pa-btn-primary pa-btn-sm">
                                    <i class="bi bi-people-fill"></i> View Platform Journey
                                </button>
                            </div>
                            <div style="position:relative;width:100%;height:320px;">
                                <canvas id="returnPerformanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="team-card-grid">
                    @forelse($stats as $id => $stat)
                        <div class="team-member-card">
                            <div class="card-profile-header">
                                <div class="avatar-wrapper">
                                    @if($stat['member']->icon && file_exists(public_path('uploads/team-member/' . $stat['member']->icon)))
                                        <img src="{{ asset('uploads/team-member/' . $stat['member']->icon) }}" class="circle-avatar" alt="{{ $stat['member']->name }}">
                                    @else
                                        <div class="initials-avatar">
                                            {{ strtoupper(substr($stat['member']->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    
                                    @if($stat['member']->status == 1)
                                        <div class="status-badge-absolute badge bg-success rounded-pill">Active</div>
                                    @else
                                        <div class="status-badge-absolute badge bg-danger rounded-pill">InActive</div>
                                    @endif
                                </div>
                                
                                <div class="card-info">
                                    <h5>{{ $stat['member']->name }}</h5>
                                    <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 mt-2">
                                        <span class="role-label">{{ $stat['member']->role ?? 'Team Member' }}</span>
                                        <span class="badge bg-light-primary text-primary" style="font-weight: 700; border: 1px solid rgba(115, 103, 240, 0.2);">ID: {{ $stat['member']->id_number ?? '0' }}</span>
                                        @if(isset($stat['distance']))
                                            <div class="distance-pill" title="Distance from searched location">
                                                <i class="bi bi-cursor-fill"></i>
                                                {{ $stat['distance'] }} km away
                                            </div>
                                        @endif
                                    </div>
                                    <p class="member-address" title="{{ $stat['member']->address }}">
                                        <i class="bi bi-geo-alt"></i> {{ $stat['member']->address ?: 'No address provided' }}
                                    </p>
                                </div>
                            </div>

                            <div class="all-time-stats">
                                <div class="stat-box-mini">
                                    <label>Bookings</label>
                                    <span>{{ $stat['total_appointments'] }}</span>
                                </div>
                                <div class="stat-box-mini">
                                    <label>Revenue</label>
                                    <span class="revenue-text">₹{{ number_format($stat['total_revenue'], 0) }}</span>
                                </div>
                                <div class="stat-box-mini">
                                    <label>Returns</label>
                                    <span class="text-primary">{{ $stat['return_count'] }}</span>
                                </div>
                                <div class="stat-box-mini">
                                    <label>Exp.</label>
                                    <span>{{ $stat['member']->experience_years ?? 0 }}y</span>
                                </div>
                            </div>

                            <div class="card-actions-strip">
                                <!-- View -->
                                <button type="button" class="btn-action-pill btn-view-card btn-view" data-id="{{ $stat['member']->id }}" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                
                                <!-- Edit -->
                                <a href="{{ route('admin.team.edit', encryptId($stat['member']->id)) }}" class="btn-action-pill btn-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Status Toggle -->
                                <button data-id="{{ $stat['member']->id }}" data-change-status="{{ $stat['member']->status == 1 ? 0 : 1 }}" 
                                    class="btn-action-pill btn-status status-change" title="{{ $stat['member']->status == 1 ? 'Make InActive' : 'Make Active' }}">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>

                                <!-- Report -->
                                <button type="button" class="btn-action-pill btn-report btn-report-view" data-id="{{ $stat['member']->id }}" title="Appointments Report">
                                    <i class="bi bi-file-earmark-text"></i>
                                </button>

                                <!-- Return Report -->
                                <button type="button" class="btn-action-pill btn-return-report btn-return-report-view" data-id="{{ $stat['member']->id }}" title="Return Customers Detail">
                                    <i class="bi bi-person-check"></i>
                                </button>

                                <!-- Popularity Toggle -->
                                <button data-id="{{ $stat['member']->id }}" data-priority-change-status="{{ $stat['member']->is_popular == 1 ? 0 : 1 }}" 
                                    class="btn-action-pill btn-priority priority-status-change" title="{{ $stat['member']->is_popular == 1 ? 'Remove from Popular' : 'Mark as Popular' }}">
                                    <i class="bi {{ $stat['member']->is_popular == 1 ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
                                </button>

                                <!-- Delete -->
                                <button data-id="{{ $stat['member']->id }}" class="btn-action-pill btn-delete delete-single" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="text-muted">No team members found.</div>
                        </div>
                    @endforelse
                </div>

                @if(isset($members) && $members->hasPages())
                    <div class="pagination-wrapper">
                        {{ $members->links('pagination::bootstrap-5') }}
                    </div>
                @endif

                <!-- DataTable Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="pa-dt-panel">
                            <div class="pa-dt-panel-header">
                                <h4 class="card-title"><i class="bi bi-table me-2 text-primary"></i> Detailed Team List</h4>
                            </div>
                            <div class="card-datatable table-responsive">
                                <table class="dt-column-search table table-hover w-100" id="table-team-members">
                                    <thead>
                                        <tr>
                                            <th data-search="false">#</th>
                                            <th data-search="false">Photo</th>
                                            <th>Name</th>
                                            <th>Role</th>
                                            <th>Phone</th>
                                            <th>Experience</th>
                                            <th>Address</th>
                                            <th data-search="false">Status</th>
                                            <th data-search="false">Popularity</th>
                                            <th data-search="false">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <div id="c-viewTeamModal" class="c-modal">
        <div class="c-modal-dialog">
            <div class="c-modal-content">
                <!-- Header -->
                <div class="c-modal-header">
                    <h5 class="c-modal-title"><i class="bi bi-person-badge"></i> Team Member Details</h5>
                    <button class="c-close-btn" data-c-close>&times;</button>
                </div>
                <!-- Body -->
                <div class="c-modal-body" id="c-team-details">
                    <div class="c-loader">
                        <div class="c-spinner"></div>
                        <span>Fetching details...</span>
                    </div>
                </div>
                <!-- Footer -->
                <div class="c-modal-footer">
                    <small><i class="bi bi-clock"></i> Updated just now</small>
                    <button class="c-btn" data-c-close>
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments Report Modal -->
    <div id="c-reportModal" class="c-modal">
        <div class="c-modal-dialog" style="max-width: 850px;">
            <div class="c-modal-content">
                <div class="c-modal-header" style="background: linear-gradient(135deg, #1a237e 0%, #3f51b5 100%);">
                    <h5 class="c-modal-title report-modal-header-title text-white"><i class="bi bi-file-earmark-bar-graph"></i> Appointments Report</h5>
                    <button class="c-close-btn" data-c-close-report>&times;</button>
                </div>
                <div class="c-modal-body" id="report-modal-body">
                    <!-- Report Filters -->
                    <div class="report-filters mb-3 p-3 bg-white rounded shadow-sm border">
                        <div class="row g-2 align-items-end mb-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Start Date</label>
                                <input type="date" id="report-filter-start-date" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">End Date</label>
                                <input type="date" id="report-filter-end-date" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Month</label>
                                <select id="report-filter-month" class="form-select form-select-sm">
                                    <option value="">Month</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ sprintf('%02d', $m) }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Year</label>
                                <select id="report-filter-year" class="form-select form-select-sm">
                                    <option value="">Year</option>
                                    @php $currentYear = date('Y'); @endphp
                                    @for ($y = $currentYear - 2; $y <= $currentYear + 1; $y++)
                                        <option value="{{ $y }}" {{ $y == 2026 ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-primary flex-grow-1" id="btn-filter-report" title="Filter">
                                    <i class="bi bi-funnel"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-success flex-grow-1" id="btn-download-report" title="Download">
                                    <i class="bi bi-download"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Summary Section (Premium Large Cards) -->
                        <div class="row g-3 text-center mb-4" id="report-summary-container">
                            <div class="col-md-4">
                                <div class="p-3 border-0 rounded-4 shadow-sm" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(14, 165, 233, 0.05) 100%); border: 1px solid rgba(14, 165, 233, 0.1) !important;">
                                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                        <i class="bi bi-credit-card-2-front text-info fs-4"></i>
                                        <label class="small fw-800 text-info text-uppercase mb-0" style="letter-spacing:1.5px; font-size:0.75rem;">Online Revenue</label>
                                    </div>
                                    <h3 class="fw-800 mb-0 text-dark" id="summary-online" style="font-size: 1.85rem;">₹0</h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border-0 rounded-4 shadow-sm" style="background: linear-gradient(135deg, rgba(40, 199, 111, 0.1) 0%, rgba(40, 199, 111, 0.05) 100%); border: 1px solid rgba(40, 199, 111, 0.1) !important;">
                                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                        <i class="bi bi-cash-stack text-success fs-4"></i>
                                        <label class="small fw-800 text-success text-uppercase mb-0" style="letter-spacing:1.5px; font-size:0.75rem;">Cash Revenue</label>
                                    </div>
                                    <h3 class="fw-800 mb-0 text-dark" id="summary-cash" style="font-size: 1.85rem;">₹0</h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border-0 rounded-4 shadow-sm" style="background: linear-gradient(135deg, rgba(26, 35, 126, 0.1) 0%, rgba(26, 35, 126, 0.05) 100%); border: 1px solid rgba(26, 35, 126, 0.1) !important;">
                                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                        <i class="bi bi-wallet2 text-primary fs-4"></i>
                                        <label class="small fw-800 text-primary text-uppercase mb-0" style="letter-spacing:1.5px; font-size:0.75rem;">Total Business</label>
                                    </div>
                                    <h3 class="fw-800 mb-0 text-dark" id="summary-total" style="font-size: 1.85rem;">₹0</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="report-table-container">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Fetching appointments...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Customers Report Modal -->
    <div id="c-returnReportModal" class="c-modal">
        <div class="c-modal-dialog" style="max-width: 850px;">
            <div class="c-modal-content">
                <div class="c-modal-header" style="background: linear-gradient(135deg, #7367f0 0%, #a889f4 100%);">
                    <h5 class="c-modal-title report-modal-header-title text-white"><i class="bi bi-person-heart"></i> Return Customers Detail</h5>
                    <button class="c-close-btn" data-c-close-return-report>&times;</button>
                </div>
                <div class="c-modal-body" id="return-report-modal-body">
                    <div id="return-report-table-container">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Fetching return customers...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Platform Retention Report Modal -->
    <div id="c-platformRetentionModal" class="c-modal pa-retention-modal">
        <div class="c-modal-dialog pa-retention-modal-dialog">
            <div class="c-modal-content pa-retention-modal-content">
                <div class="c-modal-header pa-retention-modal-header">
                    <h5 class="c-modal-title"><i class="bi bi-people-fill me-2"></i> Platform Retention Report</h5>
                    <button type="button" class="pa-retention-modal-close" data-c-close-platform-retention aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="c-modal-body pa-retention-modal-body">
                    <div class="pa-modal-search-row">
                        <div class="input-group search-input-group pa-modal-search-input">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="platform-retention-search" class="form-control border-0 shadow-none" placeholder="Search by name or mobile number..." autocomplete="off">
                        </div>
                        <button type="button" class="pa-btn pa-btn-primary" id="btn-search-platform-retention">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                    <div id="platform-retention-table-container" class="pa-retention-results">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Fetching platform retention data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('footer_script_content')
    <script>
        // Set datatable_url globally before datatable.js runs
        datatable_url = '/getDataTeamMembers';

        $(document).ready(function() {
            // DataTable configuration
            
            $.extend(true, $.fn.dataTable.defaults, {
                columns: [
                    { 
                        data: null, 
                        name: 'id',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { 
                        data: 'icon', 
                        name: 'icon', 
                        orderable: false, 
                        searchable: false,
                        render: function(data) {
                            if (data) {
                                return `<div class="avatar-wrapper"><div class="avatar shadow-sm" style="width:45px; height:45px; border-radius:10px; overflow:hidden; border:2px solid #fff;">${data}</div></div>`;
                            }
                            return '';
                        }
                    },
                    { data: 'name', name: 'name' },
                    { data: 'role', name: 'role' },
                    { data: 'phone', name: 'phone' },
                    { 
                        data: 'experience_years', 
                        name: 'experience_years',
                        render: function(data) {
                            return (data || 0) + ' Years';
                        }
                    },
                    { data: 'address', name: 'address' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'is_popular', name: 'is_popular', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                serverSide: true,
                order: [[0, 'desc']],
                drawCallback: function() {
                    // Custom adjustments after draw
                    $('#table-team-members img').css({
                        'width': '100%',
                        'height': '100%',
                        'object-fit': 'cover'
                    });
                }
            });

            // Update DataTable on filter change
            $('#btn-apply-card-filters').on('click', function() {
                if ($.fn.DataTable.isDataTable('#table-team-members')) {
                    $('#table-team-members').DataTable().ajax.reload();
                }
            });

            $('#btn-reset-card-filters').on('click', function() {
                setTimeout(() => {
                    if ($.fn.DataTable.isDataTable('#table-team-members')) {
                        $('#table-team-members').DataTable().ajax.reload();
                    }
                }, 100);
            });

            // Row click to open view modal (Premium Appointment Style)
            $('#table-team-members tbody').on('click', 'tr', function (e) {
                // Don't trigger if clicking on action items or buttons
                if ($(e.target).closest('.dropdown, button, a, .status-change, .priority-status-change').length) {
                    return;
                }
                
                let viewBtn = $(this).find('.btn-view');
                if (viewBtn.length) {
                    viewBtn.click();
                } else {
                    // Fallback: If btn-view is not found (e.g. inside a dropdown), find by data-id
                    let data = $('#table-team-members').DataTable().row(this).data();
                    if (data && data.id) {
                        $('.btn-view[data-id="' + data.id + '"]').first().click();
                    }
                }
            });
        });

        // Card Filtering Logic
        $(document).on('click', '#btn-apply-card-filters', function() {
            let search = $('#search-member').val();
            let address_search = $('#search-address').val();
            let status = $('#filter-status').val();
            let popular = $('#filter-popular').val();
            let exp = $('#filter-year-of-experience').val();
            let date = $('#filter-created-date').val();
            let month = $('#filter-month').val();
            let year = $('#filter-year').val();
 
            let url = new URL(window.location.href);
            if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
            if (address_search) url.searchParams.set('address_search', address_search); else url.searchParams.delete('address_search');
            if (status !== "") url.searchParams.set('status', status); else url.searchParams.delete('status');
            if (popular !== "") url.searchParams.set('popular', popular); else url.searchParams.delete('popular');
            if (exp) url.searchParams.set('year_of_experience', exp); else url.searchParams.delete('year_of_experience');
            if (date) url.searchParams.set('created_date', date); else url.searchParams.delete('created_date');
            if (month) url.searchParams.set('month', month); else url.searchParams.delete('month');
            if (year) url.searchParams.set('year', year); else url.searchParams.delete('year');
 
            window.location.href = url.toString();
        });

        $(document).on('click', '#btn-reset-card-filters', function() {
            window.location.href = window.location.pathname;
        });

        // Search on Enter
        $('#search-member, #search-address').on('keypress', function(e) {
            if(e.which == 13) {
                $('#btn-apply-card-filters').click();
            }
        });

        const sweetalert_delete_title = "Delete Team Member?";
        const sweetalert_change_status = "Change Status of Team Member";
        const sweetalert_change_priority_status = "Change Popularity Status of Team Member";

        // Override datatable.js actions for Card View
        $(document).off('click', '.delete-single');
        $(document).on('click', '.delete-single', function (e) {
            e.preventDefault();
            const value_id = $(this).data('id');
            Swal.fire({
                title: sweetalert_delete_title,
                text: sweetalert_delete_text,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: delete_button_text,
                cancelButtonText: cancel_button_text,
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ms-1' },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    loaderView();
                    axios.delete(APP_URL + form_url + '/' + value_id).then(function (response) {
                        loaderHide();
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }).catch(function (error) {
                        notificationToast(error.response.data.message, 'warning');
                        loaderHide();
                    });
                }
            });
        });

        $(document).off('click', '.status-change');
        $(document).on('click', '.status-change', function (e) {
            e.preventDefault();
            const value_id = $(this).data('id');
            const status = $(this).data('change-status');
            Swal.fire({
                title: sweetalert_change_status,
                text: sweetalert_change_status_text,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: yes_change_it,
                cancelButtonText: cancel_button_text,
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ms-1' },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    loaderView();
                    axios.get(APP_URL + form_url + '/status/' + value_id + '/' + status).then(function (response) {
                        loaderHide();
                        Swal.fire({
                            title: 'Updated!',
                            text: response.data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }).catch(function (error) {
                        notificationToast(error.response.data.message, 'warning');
                        loaderHide();
                    });
                }
            });
        });

        $(document).off('click', '.priority-status-change');
        $(document).on('click', '.priority-status-change', function (e) {
            e.preventDefault();
            const value_id = $(this).data('id');
            const status = $(this).data('priority-change-status');
            Swal.fire({
                title: sweetalert_change_priority_status,
                text: sweetalert_change_priority_status_text,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: yes_change_it,
                cancelButtonText: cancel_button_text,
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ms-1' },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    loaderView();
                    axios.get(APP_URL + form_url + '/priority-status/' + value_id + '/' + status).then(function (response) {
                        loaderHide();
                        Swal.fire({
                            title: 'Updated!',
                            text: response.data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }).catch(function (error) {
                        notificationToast(error.response.data.message, 'warning');
                        loaderHide();
                    });
                }
            });
        });

        // base form and data URLs
        const form_url = '/team';

        // CSRF Setup for AJAX/Axios
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Modal View
        $(document).on('click', '.btn-view', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            const baseUrl = "{{ asset('uploads/team-member') }}/";

            $("#c-viewTeamModal").addClass("show");
            $("#c-team-details").html(`
            <div class="c-loader">
                <div class="c-spinner"></div>
                <span>Loading...</span>
            </div>
        `);

            $.ajax({
                url: '/admin/team-view/' + id,
                type: 'GET',
                success: function(response) {
                    let data = response.data;
                    
                    // Helpers for Avatar
                    let avatarHtml = '';
                    if(data.icon) {
                        avatarHtml = `<img src="${baseUrl}${data.icon}" class="c-profile-avatar" alt="profile">`;
                    } else {
                        let initials = data.name ? data.name.split(' ').map(n => n[0]).join('').toUpperCase() : '??';
                        avatarHtml = `<div class="c-profile-initials">${initials}</div>`;
                    }

                    let html = `
                    <div class="c-profile-section">
                        ${avatarHtml}
                        <div class="c-profile-info">
                            <span class="badge bg-soft-primary text-primary mb-2" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:1px; font-weight:800;">Team Member</span>
                            <h4>${data.name ?? '-'}</h4>
                            <p class="text-primary" style="font-weight:700;"><i class="bi bi-briefcase"></i> ${data.role ?? '-'}</p>
                        </div>
                    </div>

                    <div class="c-row">
                        <div class="c-col-6">
                            <div class="detail-info-card">
                                <div class="detail-section-label"><i class="bi bi-person-vcard"></i> Personal Info</div>
                                
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-card-text"></i></div>
                                    <div class="info-content">
                                        <label>ID Number</label>
                                        <p>${data.id_number ?? '0'}</p>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-telephone"></i></div>
                                    <div class="info-content">
                                        <label>Mobile Number</label>
                                        <p><a href="tel:${data.phone}" style="text-decoration:none; color:inherit;">${data.phone ?? '-'}</a></p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-calendar-heart"></i></div>
                                    <div class="info-content">
                                        <label>Date of Birth</label>
                                        <p>${data.dob ?? '-'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="c-col-6">
                            <div class="detail-info-card">
                                <div class="detail-section-label"><i class="bi bi-briefcase"></i> Professional</div>
                                
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-clock-history"></i></div>
                                    <div class="info-content">
                                        <label>Experience</label>
                                        <p>${data.experience_years ?? '-'} Years</p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                                    <div class="info-content">
                                        <label>Location</label>
                                        <p>${data.city ?? '-'}</p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-droplet-half"></i></div>
                                    <div class="info-content">
                                        <label>Blood Group</label>
                                        <p>${data.blood_group ?? '-'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="c-col-12">
                            <div class="detail-info-card">
                                <div class="detail-section-label"><i class="bi bi-journal-text"></i> Professional Bio</div>
                                <p style="font-size:0.95rem; line-height:1.6; color:#475569; margin:0;">${data.bio ?? 'No bio provided.'}</p>
                            </div>
                        </div>

                        <div class="c-col-12">
                            <div class="detail-info-card">
                                <div class="detail-section-label"><i class="bi bi-stars"></i> Core Specialties</div>
                                <div>${
                                    data.specialties 
                                    ? JSON.parse(data.specialties).map(item => `<span class="c-include-badge">${item}</span>`).join("") 
                                    : '<span class="text-muted">None listed</span>'
                                }</div>
                            </div>
                        </div>

                        <div class="c-col-12">
                            <div class="detail-info-card">
                                <div class="detail-section-label"><i class="bi bi-patch-check"></i> Certifications</div>
                                <div>${
                                    data.certifications 
                                    ? JSON.parse(data.certifications).map(item => `<span class="c-include-badge" style="background:#f0fdf4; border-color:#bbf7d0; color:#166534;">${item}</span>`).join("") 
                                    : '<span class="text-muted">None listed</span>'
                                }</div>
                            </div>
                        </div>

                        <div class="c-col-12">
                            <div class="detail-info-card">
                                <div class="detail-section-label"><i class="bi bi-geo"></i> Full Address</div>
                                <p style="margin:0; font-weight:600; color:#1e293b;">${data.address ?? ''}${data.village ? ', ' + data.village : ''}${data.taluko ? ', ' + data.taluko : ''}${data.city ? ', ' + data.city : ''}${data.state ? ', ' + data.state : ''}</p>
                            </div>
                        </div>

                        <div class="c-col-6">
                            <div class="detail-info-card">
                                <div class="detail-section-label"><i class="bi bi-gear"></i> Account Status</div>
                                <p style="margin:0;">${data.status == 1 
                                    ? '<span class="text-success font-weight-bold"><i class="bi bi-check-circle-fill"></i> Active</span>' 
                                    : '<span class="text-danger font-weight-bold"><i class="bi bi-x-circle-fill"></i> Inactive</span>'
                                }</p>
                            </div>
                        </div>

                        <div class="c-col-6">
                            <div class="detail-info-card">
                                <div class="detail-section-label"><i class="bi bi-star"></i> Popularity</div>
                                <p style="margin:0;">${data.is_popular == 1 
                                    ? '<span class="text-warning font-weight-bold"><i class="bi bi-star-fill"></i> Popular</span>' 
                                    : '<span class="text-muted">Standard</span>'
                                }</p>
                            </div>
                        </div>
                        
                        <div class="c-col-6">
                            <div class="detail-info-card">
                                <div class="detail-section-label"><i class="bi bi-calendar-plus"></i> Joined On</div>
                                <p style="margin:0; font-weight:700;">${data.created_at ? new Date(data.created_at).toLocaleDateString() : '-'}</p>
                            </div>
                        </div>

                        <div class="c-col-6">
                            <div class="detail-info-card">
                                <div class="detail-section-label"><i class="bi bi-clock-history"></i> Last Updated</div>
                                <p style="margin:0; font-weight:700;">${data.updated_at ? new Date(data.updated_at).toLocaleDateString() : '-'}</p>
                            </div>
                        </div>
                    </div>
                `;
                    $("#c-team-details").html(html);
                },
                error: function() {
                    $("#c-team-details").html(
                        `<div class="c-detail-card" style="color:red">Failed to load details.</div>`
                    );
                }
            });
        });

        $(document).on("click", "[data-c-close]", function() {
            $("#c-viewTeamModal").removeClass("show");
        });

        // Appointment Report Logic
        let currentReportId = null;

        $(document).on('click', '.btn-report-view', function() {
            currentReportId = $(this).data('id');
            $("#c-reportModal").addClass("show");
            loadReport(currentReportId, 1);
        });

        $(document).on("click", "[data-c-close-report]", function() {
            $("#c-reportModal").removeClass("show");
            $("#report-table-container").html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Fetching appointments...</p>
                </div>
            `);
        });

        $(document).on('click', '#btn-filter-report', function() {
            loadReport(currentReportId, 1);
        });

        $(document).on('click', '#btn-download-report', function() {
            let start_date = $('#report-filter-start-date').val();
            let end_date = $('#report-filter-end-date').val();
            let month = $('#report-filter-month').val();
            let year = $('#report-filter-year').val();
            window.location.href = `/admin/team/appointments-report-download/${currentReportId}?start_date=${start_date}&end_date=${end_date}&month=${month}&year=${year}`;
        });

        function loadReport(id, page) {
            let start_date = $('#report-filter-start-date').val();
            let end_date = $('#report-filter-end-date').val();
            let month = $('#report-filter-month').val();
            let year = $('#report-filter-year').val();
            
            // Show Loader
            $("#report-table-container").html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Filtering report data...</p>
                </div>
            `);

            $.ajax({
                url: `/admin/team/appointments-report/${id}?page=${page}&start_date=${start_date}&end_date=${end_date}&month=${month}&year=${year}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        // Update Summaries
                        $('#summary-online').text(response.total_online || '₹0');
                        $('#summary-cash').text(response.total_cash || '₹0');
                        $('#summary-total').text(response.grand_total || '₹0');

                        let html = `
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Order No.</th>
                                        <th>Customer</th>
                                        <th>Mobile</th>
                                        <th>Date & Time</th>
                                        <th>Payment Type</th>
                                        <th>Grand Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        if (response.data.length > 0) {
                            response.data.forEach(app => {
                                let paymentBadge = app.payment_type === 'online'
                                    ? `<span style="display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:50px;background:linear-gradient(135deg,#00c6ff,#0072ff);color:#fff;font-weight:700;font-size:0.78rem;"><i class="bi bi-credit-card-2-front"></i> Online</span>`
                                    : `<span style="display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:50px;background:linear-gradient(135deg,#28c76f,#20a760);color:#fff;font-weight:700;font-size:0.78rem;"><i class="bi bi-cash-coin"></i> Cash</span>`;
                                html += `
                                    <tr>
                                        <td><strong>${app.order_number}</strong></td>
                                        <td>${app.customer_name}</td>
                                        <td><a href="tel:${app.phone}" class="text-primary font-weight-bold" style="text-decoration: none;">${app.phone}</a></td>
                                        <td>
                                            <div class="font-weight-bold">${app.date}</div>
                                            <small class="text-muted">${app.time}</small>
                                        </td>
                                        <td>${paymentBadge}</td>
                                        <td><span class="report-total-text">${app.total}</span></td>
                                    </tr>
                                `;
                            });
                        } else {
                            html += `<tr><td colspan="6" class="text-center py-4 text-muted">No completed appointments found.</td></tr>`;
                        }

                        html += `</tbody></table>`;
                        
                        // Add Pagination
                        if (response.pagination) {
                            html += `<div class="pagination-wrapper mt-3">${response.pagination}</div>`;
                        }

                        $("#report-table-container").html(html);
                    }
                },
                error: function() {
                    $("#report-table-container").html('<div class="alert alert-danger">Failed to load report.</div>');
                }
            });
        }

        // Handle report modal pagination clicks
        $(document).on('click', '#c-reportModal .pagination a', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            loadReport(currentReportId, page);
        });

        // Return Customer Report Logic
        let currentReturnReportId = null;

        $(document).on('click', '.btn-return-report-view', function() {
            currentReturnReportId = $(this).data('id');
            $("#c-returnReportModal").addClass("show");
            loadReturnReport(currentReturnReportId, 1);
        });

        $(document).on("click", "[data-c-close-return-report]", function() {
            $("#c-returnReportModal").removeClass("show");
            $("#return-report-table-container").html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Fetching return customers...</p>
                </div>
            `);
        });

        function loadReturnReport(id, page) {
            $.ajax({
                url: `/admin/team/return-customers-report/${id}?page=${page}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let html = `
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Return Order</th>
                                        <th>Customer</th>
                                        <th>Mobile</th>
                                        <th>Date & Time</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        if (response.data.length > 0) {
                            response.data.forEach(app => {
                                html += `
                                    <tr>
                                        <td><strong>${app.order_number}</strong></td>
                                        <td>${app.customer_name}</td>
                                        <td><a href="tel:${app.phone}" class="text-primary font-weight-bold" style="text-decoration: none;">${app.phone}</a></td>
                                        <td>
                                            <div class="font-weight-bold">${app.date}</div>
                                            <small class="text-muted">${app.time}</small>
                                        </td>
                                        <td><span class="report-total-text" style="color: #7367f0;">${app.total}</span></td>
                                    </tr>
                                `;
                            });
                        } else {
                            html += `<tr><td colspan="5" class="text-center py-4 text-muted">No return customers found for this beautician's previous service.</td></tr>`;
                        }

                        html += `</tbody></table>`;
                        
                        if (response.pagination) {
                            html += `<div class="pagination-wrapper mt-3">${response.pagination}</div>`;
                        }

                        $("#return-report-table-container").html(html);
                    }
                },
                error: function() {
                    $("#return-report-table-container").html('<div class="alert alert-danger">Failed to load return report.</div>');
                }
            });
        }

        $(document).on('click', '#c-returnReportModal .pagination a', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            loadReturnReport(currentReturnReportId, page);
        });

        // Chart Initialization
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('returnPerformanceChart');
            if (ctx) {
                const labels = [
                    @foreach($stats as $s)
                        @if($s['member']->status == 1)
                            "{{ $s['member']->name }}",
                        @endif
                    @endforeach
                ];
                const data = [
                    @foreach($stats as $s)
                        @if($s['member']->status == 1)
                            {{ $s['return_count'] }},
                        @endif
                    @endforeach
                ];

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Return Customers Brought Back',
                            data: data,
                            backgroundColor: 'rgba(115, 103, 240, 0.7)',
                            borderColor: 'rgba(115, 103, 240, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: 'rgba(115, 103, 240, 0.9)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                titleFont: { size: 14, weight: 'bold' },
                                bodyFont: { size: 13 },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: { weight: '600' }
                                },
                                grid: {
                                    display: true,
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    autoSkip: true,
                                    maxTicksLimit: 12,
                                    font: { size: 11, weight: '600' }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        });

        // Platform Retention Logic
        $(document).on('click', '#btn-platform-retention', function() {
            $('#platform-retention-search').val('');
            $("#c-platformRetentionModal").addClass("show");
            loadPlatformRetention(1);
        });

        $(document).on('click', '#btn-search-platform-retention', function() {
            loadPlatformRetention(1);
        });

        $(document).on('keypress', '#platform-retention-search', function(e) {
            if(e.which == 13) {
                loadPlatformRetention(1);
            }
        });

        $(document).on("click", "[data-c-close-platform-retention]", function() {
            $("#c-platformRetentionModal").removeClass("show");
            $("#platform-retention-table-container").html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Fetching platform retention data...</p>
                </div>
            `);
        });

        function loadPlatformRetention(page) {
            let search = $('#platform-retention-search').val() || '';
            
            $("#platform-retention-table-container").html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Fetching platform retention data...</p>
                </div>
            `);

            $.ajax({
                url: `/admin/team/platform-retention-report?page=${page}&search=${encodeURIComponent(search)}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        if (response.data.length > 0) {
                            const pageStartRank = ((page - 1) * 10) + 1;
                            response.data.forEach((customer, index) => {
                                const rank = pageStartRank + index;
                                const serviceCount = customer.service_count || 0;
                                const apptCount = customer.total_appointments || customer.appointments.length;
                                html += `
                                <div class="pa-retention-customer-card">
                                    <div class="pa-retention-customer-head">
                                        <h6>
                                            <span class="pa-retention-rank">#${rank}</span>
                                            <i class="bi bi-person-circle text-primary me-1"></i>${customer.customer_name || 'Unknown'}
                                        </h6>
                                        <div class="pa-retention-meta">
                                            <span class="pa-badge pa-badge-primary">${serviceCount} Services</span>
                                            <span class="pa-badge pa-badge-neutral">${apptCount} Visits</span>
                                            <a href="tel:${customer.phone}" class="pa-btn pa-btn-sm pa-btn-outline"><i class="bi bi-telephone"></i> ${customer.phone}</a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="pa-retention-table">
                                            <thead>
                                                <tr>
                                                    <th>Order</th>
                                                    <th>Date & Time</th>
                                                    <th>Beautician</th>
                                                    <th>Services</th>
                                                    <th>Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;

                                customer.appointments.forEach((app, visitIndex) => {
                                    const visitBadge = visitIndex === 0
                                        ? '<span class="pa-badge pa-badge-success ms-1">1st Visit</span>'
                                        : '';
                                    html += `
                                        <tr>
                                            <td><strong class="text-secondary">${app.order_number}</strong></td>
                                            <td>${app.date} <span class="text-muted">${app.time || ''}</span>${visitBadge}</td>
                                            <td><span class="fw-semibold">${app.beautician || 'Unassigned'}</span></td>
                                            <td><span class="fw-semibold">${app.services_count || 1}</span></td>
                                            <td><span class="fw-bold text-primary">${app.total}</span></td>
                                        </tr>
                                    `;
                                });
                                
                                html += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                `;
                            });
                        } else {
                            html += `<div class="text-center py-4 text-muted">No repeat customers found on the platform yet.</div>`;
                        }
                        
                        if (response.pagination) {
                            html += `<div class="pagination-wrapper mt-3">${response.pagination}</div>`;
                        }

                        $("#platform-retention-table-container").html(html);
                    }
                },
                error: function() {
                    $("#platform-retention-table-container").html('<div class="alert alert-danger">Failed to load platform retention data.</div>');
                }
            });
        }

        $(document).on('click', '#c-platformRetentionModal .pagination a', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            loadPlatformRetention(page);
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
