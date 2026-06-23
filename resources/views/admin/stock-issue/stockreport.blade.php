@extends('admin.layout.base')

@section('title', 'Employee Stock Report')

@section('content')
<style>
    :root {
        --primary: #2196F3;
        --success: #4CAF50;
        --danger: #f44336;
        --warning: #FF9800;
        --light: #f8f9fa;
        --border: #e0e0e0;
        --text-primary: #333;
        --text-secondary: #666;
        --text-muted: #999;
    }

    .sr-container {
        padding: 24px;
        background: #f8fafc;
        min-height: 100vh;
    }

    /* ── Header ── */
    .sr-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .sr-page-header-left h1 {
        font-size: 25px;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0 0 4px 0;
        letter-spacing: -0.5px;
    }

    .sr-page-header-left .sr-subtitle {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sr-page-header-left .sr-subtitle .sr-last-updated {
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
    }

    .sr-btn-export {
        background: var(--primary);
        color: white;
        border: none;
        padding: 10px 22px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: background 0.2s ease, box-shadow 0.2s ease;
        white-space: nowrap;
    }

    .sr-btn-export:hover {
        background: #1976D2;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
        color: white;
        text-decoration: none;
    }

    /* ── Stat Cards ── */
    .sr-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        background: white;
        border-radius: 10px;
        border: 1px solid var(--border);
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .sr-stat-card {
        padding: 22px 24px;
        border: 1px solid var(--border);
        border-radius: 10px;
        position: relative;
        background: #fafafa;
    }

    .sr-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        border-radius: 10px 10px 0 0;
    }

    .sr-stat-card:nth-child(1)::before { background: var(--primary); }
    .sr-stat-card:nth-child(2)::before { background: var(--success); }
    .sr-stat-card:nth-child(3)::before { background: var(--danger); }
    .sr-stat-card:nth-child(4)::before { background: var(--warning); }

    .sr-stat-card-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 10px;
    }

    .sr-stat-card-value {
        font-size: 32px;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
        margin-bottom: 4px;
    }

    .sr-stat-card-value span {
        font-size: 14px;
        font-weight: 400;
        color: var(--text-muted);
        margin-left: 4px;
    }

    .sr-stat-card-sub {
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .sr-stat-card-sub .sr-stat-highlight {
        font-weight: 700;
    }

    .sr-stat-card-sub .sr-stat-highlight.blue  { color: var(--primary); }
    .sr-stat-card-sub .sr-stat-highlight.green { color: var(--success); }
    .sr-stat-card-sub .sr-stat-highlight.red   { color: var(--danger); }
    .sr-stat-card-sub .sr-stat-highlight.amber { color: var(--warning); }

    /* ── Filters ── */
    .sr-filters {
        background: white;
        border-radius: 8px;
        padding: 18px 20px;
        margin-bottom: 20px;
        border: 1px solid var(--border);
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }

    .sr-filters form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }

    .sr-filter-group {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 160px;
    }

    .sr-filter-group label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: 0.6px;
    }

    .sr-filter-group select,
    .sr-filter-group input {
        padding: 9px 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 13px;
        background: white;
        color: var(--text-primary);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        height: 38px;
    }

    .sr-filter-group select:focus,
    .sr-filter-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
    }

    .sr-filter-actions {
        display: flex;
        gap: 8px;
        align-items: flex-end;
    }

    .sr-btn-filter {
        background: var(--primary);
        color: white;
        border: none;
        padding: 9px 20px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: background 0.2s ease, box-shadow 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 38px;
        white-space: nowrap;
    }

    .sr-btn-filter:hover {
        background: #1976D2;
        box-shadow: 0 4px 10px rgba(33, 150, 243, 0.25);
    }

    .sr-btn-reset {
        background: #e9ecef;
        color: #4a5568;
        border: none;
        padding: 9px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        height: 38px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: background 0.2s ease;
        white-space: nowrap;
    }

    .sr-btn-reset:hover {
        background: #dee2e6;
        color: #2d3748;
        text-decoration: none;
    }

    /* ── Table ── */
    .sr-table-wrapper {
        background: white;
        border-radius: 10px;
        border: 1px solid var(--border);
        overflow-x: auto;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .sr-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
        background: white;
    }

    .sr-table thead {
        background: #f4f6f9;
        border-bottom: 2px solid var(--border);
    }

    .sr-table th {
        padding: 13px 16px;
        color: var(--text-muted);
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        text-align: left;
        white-space: nowrap;
    }

    .sr-table th.center,
    .sr-table td.center {
        text-align: center;
    }

    .sr-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.15s ease;
    }

    .sr-table tbody tr:hover {
        background: #f8fafc;
    }

    .sr-table td {
        padding: 14px 16px;
        color: var(--text-primary);
        font-size: 13px;
        vertical-align: middle;
    }

    /* Employee cell */
    .sr-employee-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sr-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        color: white;
        flex-shrink: 0;
        text-transform: uppercase;
    }

    .sr-employee-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 13px;
        line-height: 1.3;
    }

    .sr-district-text {
        color: var(--text-secondary);
        font-size: 13px;
    }

    .sr-material-name {
        color: var(--text-primary);
        font-size: 13px;
        font-weight: 500;
    }

    /* Type badges */
    .sr-badge {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .sr-badge-serial {
        background: rgba(33, 150, 243, 0.12);
        color: var(--primary);
    }

    .sr-badge-bulk {
        background: #f0f0f0;
        color: #555;
    }

    /* Amounts */
    .sr-issued {
        color: var(--text-primary);
        font-weight: 500;
    }

    .sr-used {
        color: var(--text-primary);
        font-weight: 500;
    }

    .sr-balance {
        font-weight: 800;
        font-size: 14px;
    }

    .sr-balance.low    { color: var(--danger); }
    .sr-balance.medium { color: var(--warning); }
    .sr-balance.good   { color: var(--text-primary); }

    /* Status badges */
    .sr-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .sr-status-badge .sr-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .sr-status-low {
        background: #fef2f2;
        color: #c53030;
    }

    .sr-status-low .sr-status-dot { background: #f44336; }

    .sr-status-idle {
        background: #fff8f0;
        color: #c05621;
    }

    .sr-status-idle .sr-status-dot { background: var(--warning); }

    .sr-status-healthy {
        background: #f0fdf4;
        color: #276749;
    }

    .sr-status-healthy .sr-status-dot { background: var(--success); }

    .sr-last-used-text {
        color: var(--text-muted);
        font-size: 12px;
        white-space: nowrap;
    }

    /* Action */
    .sr-action-link {
        color: var(--primary);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border: none;
        background: none;
        padding: 0;
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .sr-action-link:hover {
        color: #1565C0;
        text-decoration: underline;
    }

    /* Empty */
    .sr-empty {
        text-align: center;
        padding: 64px 20px;
        color: var(--text-muted);
    }

    .sr-empty i {
        font-size: 48px;
        color: #ddd;
        margin-bottom: 16px;
        display: block;
    }

    .sr-empty p {
        font-size: 14px;
        margin: 0;
    }

    /* Table footer */
    .sr-table-footer {
        padding: 14px 18px;
        background: #fafafa;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .sr-showing-text {
        font-size: 13px;
        color: var(--text-muted);
    }

    /* Pagination */
    .sr-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
    }

    .sr-pagination a,
    .sr-pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        border: 1px solid var(--border);
        border-radius: 5px;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.15s ease;
        cursor: pointer;
        padding: 0 8px;
    }

    .sr-pagination a:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(33, 150, 243, 0.05);
    }

    .sr-pagination .active {
        background: var(--primary);
        color: white !important;
        border-color: var(--primary);
    }

    .sr-pagination .disabled {
        color: #ddd;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Alert */
    .sr-alert-popup {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        border: 1px solid #fecaca;
        padding: 18px 20px;
        max-width: 380px;
        z-index: 9999;
        display: flex;
        gap: 14px;
        animation: slideInUp 0.3s ease;
    }

    @keyframes slideInUp {
        from { transform: translateY(20px); opacity: 0; }
        to   { transform: translateY(0);   opacity: 1; }
    }

    .sr-alert-icon {
        width: 40px;
        height: 40px;
        background: #fef2f2;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sr-alert-icon i {
        color: var(--danger);
        font-size: 18px;
    }

    .sr-alert-body {}

    .sr-alert-title {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 14px;
        margin-bottom: 4px;
    }

    .sr-alert-desc {
        font-size: 12px;
        color: var(--text-secondary);
        line-height: 1.5;
        margin-bottom: 12px;
    }

    .sr-alert-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .sr-alert-btn-primary {
        font-size: 11px;
        font-weight: 700;
        color: var(--danger);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        text-decoration: none;
    }

    .sr-alert-btn-primary:hover {
        text-decoration: underline;
        color: #c53030;
    }

    .sr-alert-btn-dismiss {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }

    .sr-alert-btn-dismiss:hover {
        color: var(--text-secondary);
    }

    /* Modal */
    .sr-modal-header {
        background: var(--light);
        border-bottom: 2px solid var(--border);
        padding: 20px !important;
    }

    .sr-modal-title {
        color: var(--text-primary);
        font-weight: 700;
        font-size: 16px;
        margin: 0 0 4px 0;
    }

    .sr-modal-subtitle {
        color: var(--text-muted);
        font-size: 12px;
        margin: 0;
    }

    .sr-serial-item {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s ease;
    }

    .sr-serial-item:hover {
        background: var(--light);
    }

    .sr-serial-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .sr-serial-number {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sr-serial-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 16px;
        padding: 12px 0;
    }

    .sr-stat-box {
        display: flex;
        flex-direction: column;
    }

    .sr-stat-label {
        font-size: 10px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }

    .sr-stat-value {
        font-weight: 700;
        font-size: 16px;
    }

    .sr-ticket-list {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--border);
    }

    .sr-ticket-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin-bottom: 8px;
        display: block;
        letter-spacing: 0.5px;
    }

    .sr-ticket-item {
        font-size: 12px;
        color: var(--text-secondary);
        padding: 4px 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sr-modal-body {
        max-height: 70vh;
        overflow-y: auto;
        padding: 0 !important;
    }

    .sr-modal-body::-webkit-scrollbar { width: 6px; }
    .sr-modal-body::-webkit-scrollbar-track { background: #f1f1f1; }
    .sr-modal-body::-webkit-scrollbar-thumb { background: #bbb; border-radius: 3px; }
    .sr-modal-body::-webkit-scrollbar-thumb:hover { background: #888; }

    .sr-modal-footer {
        background: var(--light);
        border-top: 1px solid var(--border);
        padding: 15px 20px !important;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .sr-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .sr-stats-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .sr-filters form {
            flex-direction: column;
        }

        .sr-filter-group {
            min-width: 100%;
            width: 100%;
        }

        .sr-page-header h1 {
            font-size: 22px;
        }

        .sr-table th,
        .sr-table td {
            padding: 10px 8px;
            font-size: 11px;
        }

        .sr-table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .sr-alert-popup {
            left: 16px;
            right: 16px;
            bottom: 16px;
            max-width: 100%;
        }
    }

    @media (max-width: 480px) {
        .sr-filters {
            padding: 12px;
        }

        .sr-filter-group label {
            font-size: 10px;
        }

        .sr-filter-group select,
        .sr-filter-group input {
            padding: 8px 10px;
            font-size: 12px;
        }

        .sr-stat-card {
            padding: 16px;
        }

        .sr-stat-card-value {
            font-size: 22px;
        }

        .sr-stat-card-label {
            font-size: 10px;
        }
    }
</style>

<div class="sr-container">

    {{-- ── Page Header ── --}}
    <div class="sr-page-header">
        <div class="sr-page-header-left">
            <h1>Employee Stock Report</h1>
            <p class="sr-subtitle">
                Track material distribution and usage across employees
                &nbsp;•&nbsp;
                <span class="sr-last-updated">Last Updated: {{ \Carbon\Carbon::now()->format('d M Y, g:i A') }}</span>
            </p>
        </div>
        <div>
            <a href="{{ route('admin.stock-report') }}?{{ http_build_query(request()->except('page')) }}&export=1"
               class="sr-btn-export">
                <i class="fa fa-download"></i> Export
            </a>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="sr-stats-grid">
        <div class="sr-stat-card">
            <div class="sr-stat-card-label">Total Employees</div>
            <div class="sr-stat-card-value">
                {{ $statCards['totalEmployees'] ?? 0 }}
            </div>
            <div class="sr-stat-card-sub">
                <span class="sr-stat-highlight blue">Active Staff</span>
            </div>
        </div>
        <div class="sr-stat-card">
            <div class="sr-stat-card-label">Materials Issued</div>
            <div class="sr-stat-card-value">
                {{ number_format($statCards['totalIssued'] ?? 0) }}
            </div>
            <div class="sr-stat-card-sub">Units Total</div>
        </div>
        <div class="sr-stat-card">
            <div class="sr-stat-card-label">Materials Used</div>
            <div class="sr-stat-card-value">
                {{ number_format($statCards['totalUsed'] ?? 0) }}
            </div>
            <div class="sr-stat-card-sub">
                @php
                    $utilPct = ($statCards['totalIssued'] ?? 0) > 0
                        ? round((($statCards['totalUsed'] ?? 0) / ($statCards['totalIssued'] ?? 1)) * 100, 1)
                        : 0;
                @endphp
                <span class="sr-stat-highlight red">{{ $utilPct }}% Utilization</span>
            </div>
        </div>
        <div class="sr-stat-card">
            <div class="sr-stat-card-label">Total Balance</div>
            <div class="sr-stat-card-value">
                {{ number_format($statCards['totalBalance'] ?? 0) }}
            </div>
            <div class="sr-stat-card-sub">In-Stock</div>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <div class="sr-filters">
        <form method="GET" action="{{ route('admin.stock-report') }}">
            <div class="sr-filter-group">
                <label>District</label>
                <select name="district" id="district_id">
                    <option value="">All Districts</option>
                    @foreach($districts ?? [] as $district)
                        <option value="{{ $district->id }}"
                            {{ request('district') == $district->id ? 'selected' : '' }}>
                            {{ $district->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sr-filter-group">
                <label>Employee</label>
                <select name="employee_id" id="emp">
                    <option value="">Select Employee...</option>
                    @foreach($employees ?? [] as $employee)
                        <option value="{{ $employee->id }}"
                            {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sr-filter-group">
                <label>Material</label>
                <select name="material_id">
                    <option value="">All Materials</option>
                    @foreach($materials ?? [] as $material)
                        <option value="{{ $material->id }}"
                            {{ request('material_id') == $material->id ? 'selected' : '' }}>
                            {{ $material->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- <div class="sr-filter-group">
                <label>Type</label>
                <select name="type">
                    <option value="">Serial / Non-Serial</option>
                    <option value="serial"     {{ request('type') == 'serial'     ? 'selected' : '' }}>Serial</option>
                    <option value="non_serial" {{ request('type') == 'non_serial' ? 'selected' : '' }}>Non-Serial</option>
                </select>
            </div> -->

            <div class="sr-filter-group">
                <label>Search</label>
                <input type="text" name="search"
                       placeholder="Search ..." value="{{ request('search') }}">
            </div>

            <div class="sr-filter-actions">
                <button type="submit" class="sr-btn-filter">
                    <i class="fa fa-filter"></i> Apply Filter
                </button>
                <a href="{{ route('admin.stock-report') }}" class="sr-btn-reset">Reset</a>
            </div>
        </form>
    </div>

    {{-- ── Table ── --}}
    <div class="sr-table-wrapper">
        @if($report->count() > 0)
            <table class="sr-table">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>District</th>
                        <th>Material Name</th>
                        <th class="center">Type</th>
                        <th class="center">Issued</th>
                        <th class="center">Used</th>
                        <th class="center">Balance</th>
                       <!-- <th class="center">Transfer In</th> -->
                        <th class="center">Transfer Out</th>
                        <th class="center">Status</th>
                        <th class="center">Last Used</th>
                        <th class="center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report as $item)
                        @php
                            $initials = collect(explode(' ', $item['employee']))
                                ->map(function($w) {
                                    return strtoupper(substr($w, 0, 1));
                                })
                                ->take(2)
                                ->implode('');
                            $avatarColors = ['#2196F3','#4CAF50','#FF9800','#E91E63','#9C27B0','#00BCD4','#FF5722','#607D8B'];
                            $avatarBg = $avatarColors[crc32($item['employee']) % count($avatarColors)];

                            $issued  = floatval($item['issued']);
                            $used    = floatval($item['used']);
                            $balance = floatval($item['balance']);

                            $balancePct = $issued > 0 ? ($balance / $issued) * 100 : 0;
                            $balanceClass = $balancePct <= 10 ? 'low' : ($balancePct <= 30 ? 'medium' : 'good');

                            if ($used == 0) {
                                $statusClass = 'sr-status-idle';
                                $statusDot   = 'sr-status-dot';
                                $statusLabel = 'IDLE STOCK';
                            } elseif ($balancePct <= 10) {
                                $statusClass = 'sr-status-low';
                                $statusDot   = 'sr-status-dot';
                                $statusLabel = 'LOW STOCK';
                            } else {
                                $statusClass = 'sr-status-healthy';
                                $statusDot   = 'sr-status-dot';
                                $statusLabel = 'HEALTHY';
                            }

                            $lastUsed = $item['last_used'] ?? null;
                        @endphp
                        <tr>
                            <td>
                                <div class="sr-employee-cell">
                                    <div class="sr-avatar" style="background: {{ $avatarBg }};">
                                        {{ $initials }}
                                    </div>
                                    <span class="sr-employee-name">{{ $item['employee'] }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="sr-district-text">{{ $item['district'] ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="sr-material-name">{{ $item['material'] }}-{{ $item['material_code'] }}</span>
                            </td>
                            <td class="center">
                                @if($item['is_serial'])
                                    <span class="sr-badge sr-badge-serial">Serial</span>
                                @else
                                    <span class="sr-badge sr-badge-bulk">Non-Serial</span>
                                @endif
                            </td>
                            <td class="center">
                                <span class="sr-issued">{{ number_format($issued, 2) }} {{ $item['baseunit'] }}</span>
                            </td>
                            <td class="center">
                                <span class="sr-used">{{ number_format($used, 2) }} {{ $item['baseunit'] }}</span>
                            </td>
                            <td class="center">
                                <span class="sr-balance {{ $balanceClass }}">
                                    {{ number_format($balance, 2) }} {{ $item['baseunit'] }}
                                </span>
                            </td>
                             <!-- <td class="center">
                                <span class="sr-balance">
                                    {{ (floatval($item["transfer_in"])) }} {{ $item['baseunit'] }}
                                </span>
                            </td> -->
                               <td class="center">
                                <span class="sr-balance">
                                    {{ (floatval($item["transfer_out"])) }} {{ $item['baseunit'] }}
                                </span>
                            </td>
                            <td class="center">
                                <span class="sr-status-badge {{ $statusClass }}">
                                    <span class="{{ $statusDot }}"></span>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="center">
                                <span class="sr-last-used-text">
                                    {{ $lastUsed ? \Carbon\Carbon::parse($lastUsed)->diffForHumans() : '—' }}
                                </span>
                            </td>
                            <td class="center">
                                <button type="button" class="sr-action-link"
                                        data-toggle="modal" data-target="#detailsModal"
                                        onclick="loadSerialDetails('{{ addslashes(json_encode($item)) }}')">
                                    <i class="fa fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="sr-table-footer">
                <span class="sr-showing-text">
                    Showing {{ $report->firstItem() }} to {{ $report->lastItem() }} of {{ $report->total() }} entries
                </span>
                @if($report->hasPages())
                    <div class="sr-pagination">
                        {{ $report->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="sr-empty">
                <i class="fa fa-inbox"></i>
                <p>No stock records found. Try adjusting your filters.</p>
            </div>
        @endif
    </div>
</div>

{{-- ── Stock Alert Popup ── --}}
@php
    $lowStockCount = isset($report) ? $report->filter(function($i) {
        $issued = floatval($i['issued']);
        $balance = floatval($i['balance']);
        return $issued > 0 && ($balance / $issued) * 100 <= 10;
    })->count() : 0;
@endphp

@if($lowStockCount > 0)
<div class="sr-alert-popup" id="srAlertPopup">
    <div class="sr-alert-icon">
        <i class="fa fa-exclamation-triangle"></i>
    </div>
    <div class="sr-alert-body">
        <div class="sr-alert-title">Critical Stock Alert</div>
        <div class="sr-alert-desc">
            {{ $lowStockCount }} employee{{ $lowStockCount > 1 ? 's are' : ' is' }} below 5% safety stock.
            Re-allocation suggested.
        </div>
        <div class="sr-alert-actions">
            <!-- <a href="{{ route('admin.stock-issue.index') }}" class="sr-alert-btn-primary">Re-Allocate Stock</a> -->
            <button class="sr-alert-btn-dismiss" onclick="document.getElementById('srAlertPopup').style.display='none'">
                Dismiss
            </button>
        </div>
    </div>
</div>
@endif

{{-- ── Details Modal ── --}}
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0">
            <div class="sr-modal-header">
                <h5 class="sr-modal-title">Stock Details</h5>
                <p class="sr-modal-subtitle">
                    <span id="modalEmployee"></span> &bull; <span id="modalMaterial"></span>
                </p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="sr-modal-body" id="serialDetailsContainer"></div>
            <div class="sr-modal-footer text-right">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('/css/materials.css') }}">

<script>
document.getElementById('district_id').addEventListener('change', function () {
    const districtId = this.value;
    const empSelect  = document.getElementById('emp');

    fetch(`{{ route('admin.get-employees') }}?district_id=${districtId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                empSelect.innerHTML = '<option value="">All Employees</option>';
                data.employees.filter(emp => [2, 4, 5].includes(emp.type))
                .forEach(emp => {
                    empSelect.innerHTML += `<option value="${emp.id}">${emp.first_name} ${emp.last_name}</option>`;
                });
            } else {
                showToast('Error:', data.message);
            }
        });
});

function loadSerialDetails(itemJson) {
    const item = JSON.parse(itemJson);

    document.getElementById('modalEmployee').textContent = item.employee;
    document.getElementById('modalMaterial').textContent = item.material;

    let html = '';

    if (item.is_serial) {
        if (!item.serials || item.serials.length === 0) {
            document.getElementById('serialDetailsContainer').innerHTML =
                '<div class="sr-empty" style="padding:40px 20px;"><i class="fa fa-inbox"></i><p>No serial details available</p></div>';
            return;
        }

        item.serials.forEach(serial => {
            html += `
                <div class="sr-serial-item">
                    <div class="sr-serial-header">
                        <span class="sr-serial-number">
                            <i class="fa fa-barcode"></i>${serial.serial_number}
                        </span>
                    </div>
                    <div class="sr-serial-stats">
                        <div class="sr-stat-box">
                            <span class="sr-stat-label">Issued</span>
                            <span class="sr-stat-value" style="color:var(--success);">${parseFloat(serial.issued).toFixed(2)}</span>
                        </div>
                        <div class="sr-stat-box">
                            <span class="sr-stat-label">Used</span>
                            <span class="sr-stat-value" style="color:var(--danger);">${parseFloat(serial.used).toFixed(2)}</span>
                        </div>
                        <div class="sr-stat-box">
                            <span class="sr-stat-label">Balance</span>
                            <span class="sr-stat-value" style="color:var(--warning);">${parseFloat(serial.balance).toFixed(2)}</span>
                        </div>
                    </div>`;

            if (serial.issued_indents && serial.issued_indents.length > 0) {
                html += `<div class="sr-ticket-list"><span class="sr-ticket-label">Issued (Indent-wise)</span>`;
                serial.issued_indents.forEach(ind => {
                    html += `<div class="sr-ticket-item"><i class="fa fa-file-text"></i>Indent ${ind.indent_no}: <strong>${Number(ind.qty).toFixed(2)}</strong> <strong>
                (${new Date(ind.issue_date).toLocaleDateString('en-GB')})
                </strong></div>`;
                });
                html += `</div>`;
            }

            if (serial.tickets && serial.tickets.length > 0) {
                html += `<div class="sr-ticket-list"><span class="sr-ticket-label">Usage by Ticket</span>`;
                serial.tickets.forEach(ticket => {
                    html += `<div class="sr-ticket-item"><i class="fa fa-ticket"></i><strong>Ticket ${ticket.ticket_id || 'N/A'}</strong>: <span style="color:var(--danger);font-weight:600;">${parseFloat(ticket.used).toFixed(2)}</span>  <strong>
                (${new Date(ticket.issue_date).toLocaleDateString('en-GB')})
                </strong></div>`;
                });
                html += `</div>`;
            }

            html += `</div>`;
        });
    } else {
        html += `
            <div class="sr-serial-item">
                <div class="sr-serial-stats">
                    <div class="sr-stat-box">
                        <span class="sr-stat-label">Issued</span>
                        <span class="sr-stat-value" style="color:var(--success);">${parseFloat(item.issued).toFixed(2)}</span>
                    </div>
                    <div class="sr-stat-box">
                        <span class="sr-stat-label">Used</span>
                        <span class="sr-stat-value" style="color:var(--danger);">${parseFloat(item.used).toFixed(2)}</span>
                    </div>
                    <div class="sr-stat-box">
                        <span class="sr-stat-label">Balance</span>
                        <span class="sr-stat-value" style="color:var(--warning);">${parseFloat(item.balance).toFixed(2)}</span>
                    </div>
                </div>`;

        if (item.issued_indents && item.issued_indents.length > 0) {
            html += `<div class="sr-ticket-list"><span class="sr-ticket-label">Issued (Indent-wise)</span>`;
            item.issued_indents.forEach(ind => {
                html += `<div class="sr-ticket-item"><i class="fa fa-file-text"></i>Indent ${ind.indent_no}: <strong>${Number(ind.qty).toFixed(2)}</strong> <strong>
                (${new Date(ind.issue_date).toLocaleDateString('en-GB')})
                </strong></div>`;
            });
            html += `</div>`;
        }

        if (item.tickets && item.tickets.length > 0) {
            html += `<div class="sr-ticket-list"><span class="sr-ticket-label">Usage by Ticket</span>`;
            item.tickets.forEach(ticket => {
                html += `<div class="sr-ticket-item"><i class="fa fa-ticket"></i><strong>Ticket ${ticket.ticket_id || 'N/A'}</strong>: <span style="color:var(--danger);font-weight:600;">${parseFloat(ticket.used).toFixed(2)}</span>  <strong>
                (${new Date(ticket.issue_date).toLocaleDateString('en-GB')})
                </strong></div>`;
            });
            html += `</div>`;
        } else {
            html += `<p style="padding:12px 16px;color:var(--text-muted);font-size:13px;">No ticket usage found</p>`;
        }

        html += `</div>`;
    }

    document.getElementById('serialDetailsContainer').innerHTML = html;
}
</script>

@endsection
