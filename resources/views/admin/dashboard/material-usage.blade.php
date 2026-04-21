@extends('admin.layout.base')

@section('title', 'Material Usage Dashboard')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  :root {
    --primary: #2196F3;
    --success: #4CAF50;
    --danger: #f44336;
    --warning: #FF9800;
    --info: #00BCD4;
    --purple: #9C27B0;
    --light: #f8f9fa;
    --border: #e0e0e0;
    --text-primary: #333;
    --text-secondary: #666;
    --text-muted: #999;
  }

  * {
    box-sizing: border-box;
  }

  .dashboard-container {
    padding: 24px;
    background: #f8fafc;
    min-height: 100vh;
  }

  /* ── Page Header ── */
  .dashboard-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
  }

  .dashboard-header-left {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .dashboard-logo {
    width: 40px;
    height: 40px;
    background: var(--primary);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
  }

  .dashboard-header-title {
    margin: 0;
  }

  .dashboard-header-title h1 {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    letter-spacing: -0.3px;
  }

  .dashboard-header-title p {
    font-size: 12px;
    color: var(--text-muted);
    margin: 2px 0 0 0;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
  }

  .dashboard-header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .date-range-picker {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: white;
    border: 1px solid var(--border);
    border-radius: 6px;
    font-size: 12px;
    color: var(--text-secondary);
  }

  .date-range-picker i {
    color: var(--text-muted);
  }

  .btn-refresh {
    background: white;
    border: 1px solid var(--border);
    width: 36px;
    height: 36px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    transition: all 0.2s ease;
  }

  .btn-refresh:hover {
    border-color: var(--primary);
    color: var(--primary);
  }

  .btn-export {
    background: white;
    border: 1px solid var(--border);
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
  }

  .btn-export:hover {
    border-color: var(--primary);
    color: var(--primary);
  }

  .notification-bell {
    width: 36px;
    height: 36px;
    background: #fef3f2;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--danger);
    font-size: 16px;
    position: relative;
    cursor: pointer;
  }

  .notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 20px;
    height: 20px;
    background: var(--danger);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
  }

  /* ── Filter Bar ── */
  .filter-bar {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 14px 16px;
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
  }

  .filter-bar-section {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
  }

  .filter-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #e3f2fd;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    color: var(--primary);
    font-weight: 500;
  }

  .filter-item-close {
    cursor: pointer;
    color: var(--primary);
    font-weight: 700;
  }

  .filter-item-close:hover {
    opacity: 0.7;
  }

  .filter-input {
    padding: 6px 10px;
    border: 1px solid var(--border);
    border-radius: 4px;
    font-size: 12px;
    width: 120px;
  }

  .filter-input::placeholder {
    color: var(--text-muted);
  }

  .btn-apply-filters {
    background: var(--primary);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 24px;
    transition: background 0.2s ease;
  }

  .btn-apply-filters:hover {
    background: #1976D2;
  }

  /* ── Stat Cards ── */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 18px;
    position: relative;
    overflow: hidden;
  }

  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
  }

  .stat-card.blue::before   { background: var(--primary); }
  .stat-card.green::before  { background: var(--success); }
  .stat-card.pink::before   { background: #E91E63; }
  .stat-card.cyan::before   { background: var(--info); }
  .stat-card.green2::before { background: #4CAF50; }

  .stat-label {
    font-size: 11px;
    color: var(--text-muted);
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
  }

  .stat-value {
    font-size: 28px;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 6px;
  }

  .stat-value-unit {
    font-size: 12px;
    font-weight: 400;
    color: var(--text-muted);
    margin-left: 4px;
  }

  .stat-subtitle {
    font-size: 12px;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .stat-trend {
    font-weight: 600;
    margin-right: 4px;
  }

  .stat-trend.up   { color: var(--success); }
  .stat-trend.down { color: var(--danger); }

  /* ── Charts Section ── */
  .charts-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
    align-items: start;
  }

  .chart-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 20px;
    overflow: hidden;
  }
  
  .chart-scroll-container {
    overflow-x: auto;
    overflow-y: visible;
    padding-bottom: 10px;
    max-height: 450px;
  }

  .chart-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 3px;
  }

  .chart-subtitle {
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 16px;
  }



  .chart-tab {
    font-size: 12px;
    color: var(--text-muted);
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    font-weight: 600;
    transition: all 0.2s ease;
  }

  .chart-tab.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
  }

  .chart-bar-container {
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    height: 180px;
    gap: 8px;
  }

  .chart-bar-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    flex: 1;
  }

  .chart-bar-pair {
    display: flex;
    gap: 4px;
    height: 100%;
    align-items: flex-end;
  }

  .chart-bar {
    flex: 1;
    border-radius: 3px 3px 0 0;
    min-width: 12px;
  }

  .chart-bar.issued { background: #bbdefb; }
  .chart-bar.used   { background: var(--primary); }

  .chart-bar-label {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 600;
  }

  .chart-legend {
    display: flex;
    gap: 16px;
    margin-top: 12px;
    font-size: 12px;
  }

  .chart-legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .chart-legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 2px;
  }

  .chart-legend-dot.issued { background: #bbdefb; }
  .chart-legend-dot.used   { background: var(--primary); }

  /* ── Donut Chart ── */
  .donut-container {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    width: 200px;
    height: 200px;
    margin: 0 auto 20px;
  }

  .donut-center {
    position: absolute;
    text-align: center;
  }

  .donut-value {
    font-size: 28px;
    font-weight: 800;
    color: var(--text-primary);
  }

  .donut-label {
    font-size: 11px;
    color: var(--text-muted);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
  }

  .donut-legend {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .donut-legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
  }

  .donut-legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 2px;
  }

  .donut-legend-pct {
    font-weight: 700;
    margin-left: auto;
    color: var(--text-secondary);
  }

  /* ── Tables ── */
  .table-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0;
    overflow: hidden;
    margin-bottom: 20px;
  }

  .table-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .table-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
  }

  .table-header-actions {
    display: flex;
    gap: 8px;
  }

  .table-filter-btn {
    background: none;
    border: none;
    font-size: 12px;
    color: var(--primary);
    cursor: pointer;
    font-weight: 600;
  }

  .table-filter-btn:hover {
    text-decoration: underline;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  table thead {
    background: #f8f9fa;
    border-bottom: 1px solid var(--border);
  }

  table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 12px;
    color: var(--text-primary);
  }

  table tbody tr:hover {
    background: #f8fafc;
  }

  .badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap;
  }

  .badge-used {
    background: #e8f5e9;
    color: var(--success);
  }

  .badge-hold {
    background: #e3f2fd;
    color: var(--primary);
  }

  .badge-sent {
    background: #fce4ec;
    color: var(--purple);
  }

  .badge-idle {
    background: #fff3e0;
    color: var(--warning);
  }

  .badge-critical {
    background: #ffebee;
    color: var(--danger);
  }

  .badge-optimal {
    background: #e8f5e9;
    color: var(--success);
  }

  .badge-stocked {
    background: #e3f2fd;
    color: var(--primary);
  }

  .trending-chart {
    width: 40px;
    height: 24px;
    display: inline-block;
  }

  .status-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 4px;
  }

  .status-dot.critical { background: var(--danger); }
  .status-dot.warning  { background: var(--warning); }
  .status-dot.info     { background: var(--primary); }

  .alert-item {
    padding: 12px 16px;
    border-left: 3px solid var(--danger);
    background: #fef5f5;
    display: flex;
    gap: 10px;
  }

  .alert-item.high {
    border-left-color: var(--danger);
    background: #fef5f5;
  }

  .alert-item.warning {
    border-left-color: var(--warning);
    background: #fff8f0;
  }

  .alert-item.info {
    border-left-color: var(--primary);
    background: #e3f2fd;
  }

  .alert-icon {
    font-size: 14px;
    flex-shrink: 0;
    margin-top: 2px;
  }

  .alert-icon.critical { color: var(--danger); }
  .alert-icon.warning  { color: var(--warning); }
  .alert-icon.info     { color: var(--primary); }

  .alert-body {
    flex: 1;
  }

  .alert-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 2px;
  }

  .alert-desc {
    font-size: 11px;
    color: var(--text-secondary);
    line-height: 1.4;
  }

  .alert-severity {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-top: 4px;
    color: var(--text-muted);
  }

  .alert-time {
    font-size: 10px;
    color: var(--text-muted);
    white-space: nowrap;
    margin-left: 12px;
    flex-shrink: 0;
  }

  .view-all-link {
    color: var(--primary);
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    float: right;
  }

  .view-all-link:hover {
    text-decoration: underline;
  }

  .tab-nav {
    display: flex;
    gap: 0;
    border-bottom: 2px solid var(--border);
    margin-bottom: 16px;
  }

  .tab-nav button {
    background: none;
    border: none;
    padding: 10px 0;
    margin-right: 20px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
  }

  .tab-nav button.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
  }

  .tabs-container {
    padding: 16px 20px;
  }

  /* ── Responsive ── */
  @media (max-width: 1024px) {
    .charts-grid {
      grid-template-columns: 1fr;
    }

    .stats-grid {
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .chart-scroll-container {
      overflow-x: auto;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
    }
  }

  @media (max-width: 768px) {
    .dashboard-container {
      padding: 16px;
    }

    .dashboard-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .dashboard-header-actions {
      width: 100%;
      justify-content: flex-start;
    }

    .filter-bar {
      flex-direction: column;
      align-items: flex-start;
    }

    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }

    table {
      font-size: 11px;
    }

    table th,
    table td {
      padding: 10px 8px;
    }
    
    .chart-scroll-container {
      overflow-x: auto;
      overflow-y: auto;
      max-height: 350px;
      -webkit-overflow-scrolling: touch;
    }
    
    .chart-scroll-container::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    
    .chart-scroll-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }
    
    .chart-scroll-container::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 4px;
    }
    
    .chart-scroll-container::-webkit-scrollbar-thumb:hover {
      background: #999;
    }
  }
</style>

<div class="dashboard-container">
  <!-- Header -->
  <div class="dashboard-header">
    <div class="dashboard-header-left">
      <div class="dashboard-logo">
        <span style="font-size: 20px;">📦</span>
      </div>
      <div class="dashboard-header-title">
        <h1>Material Usage Dashboard</h1>
        <p>Track material usage and monitor stock levels in real time.</p>
      </div>
    </div>
    <div class="dashboard-header-actions">
      <div class="date-range-picker">
        <i class="fa fa-calendar"></i>
        <span>{{ request('from_date', 'All Time') }} - {{ request('to_date', '') }}</span>
      </div>
      <button class="btn-refresh" title="Refresh">
        <i class="fa fa-refresh"></i>
      </button>
      <!-- <button class="btn-export">
        <i class="fa fa-download"></i>
        Export
      </button> -->
      <!-- <div class="notification-bell">
        <i class="fa fa-bell"></i>
        <div class="notification-badge">2</div>
      </div> -->
    </div>
  </div>

  <!-- Filter Bar -->
  <div class="filter-bar">
    <form method="GET" action="{{ route('admin.material-usage-dashboard') }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; width: 100%;">
      <div class="filter-bar-section">
        <span style="font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-right: 8px;">Filters:</span>
        <div class="filter-group" style="display: inline-flex; flex-direction: column; gap: 4px;">
          <label style="font-size: 10px; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">District</label>
          <select class="filter-input" name="district" id="district_id" style="width: 150px;">
            <option value="">All Districts</option>
            @if(isset($districts) && $districts->count() > 0)
              @foreach($districts as $district)
                <option value="{{ $district->id }}" {{ request('district') == $district->id ? 'selected' : '' }}>
                  {{ $district->name ?? 'N/A' }}
                </option>
              @endforeach
            @endif
          </select>
        </div>
        <div class="filter-group" style="display: inline-flex; flex-direction: column; gap: 4px;">
          <label style="font-size: 10px; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Employee</label>
          <select class="filter-input" name="employee_id" id="emp" style="width: 180px;">
            <option value="">All Employees</option>
            @if(isset($employees) && $employees->count() > 0)
              @foreach($employees as $employee)
                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                  {{ $employee->first_name }} {{ $employee->last_name }}
                </option>
              @endforeach
            @endif

          </select>
        </div>
        <div class="filter-group" style="display: inline-flex; flex-direction: column; gap: 4px;">
          <label style="font-size: 10px; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Material</label>
          <select class="filter-input" name="material_id" style="width: 180px;">
            <option value="">All Materials</option>
            @if(isset($materials) && $materials->count() > 0)
              @foreach($materials as $material)
                <option value="{{ $material->id }}" {{ request('material_id') == $material->id ? 'selected' : '' }}>
                  {{ $material->name ?? 'N/A' }}
                </option>
              @endforeach
            @endif
          </select>
        </div>
      </div>
      <div class="filter-bar-section" style="margin-left: auto; gap: 10px;">
        <div class="filter-group" style="display: inline-flex; flex-direction: column; gap: 4px;">
          <label style="font-size: 10px; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">From Date</label>
          <input type="date" class="filter-input" name="from_date" value="{{ request('from_date') }}" style="width: 140px;">
        </div>
        <div class="filter-group" style="display: inline-flex; flex-direction: column; gap: 4px; ">
          <label style="font-size: 10px; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">To Date</label>
          <input type="date" class="filter-input" name="to_date" value="{{ request('to_date') }}" style="width: 140px;">
        </div>
        <button type="submit" class="btn-apply-filters">APPLY</button>
        <a href="{{ request()->url() }}" class="btn-apply-filters" style="background: #e9ecef; color: #4a5568;">RESET</a>
      </div>
    </form>
  </div>

  <!-- Stats Grid -->
  <div class="stats-grid">
    <div class="stat-card blue">
      <div class="stat-label">Total Issued</div>
      <div class="stat-value">{{ number_format($statCards['totalIssued'] ?? 0) }}</div>
      <div class="stat-subtitle">
        <span class="stat-trend up">▲ {{ $statCards['efficiency'] ?? 0 }}%</span>
        <span>Efficiency</span>
      </div>
    </div>
    <div class="stat-card green">
      <div class="stat-label">Total Used</div>
      <div class="stat-value" style="color: var(--success);">{{ number_format($statCards['totalUsed'] ?? 0) }}</div>
      <div class="stat-subtitle">
        <span class="stat-trend up" style="color: var(--success);">{{ $statCards['efficiency'] ?? 0 }}%</span>
        <span>Efficiency</span>
      </div>
    </div>
    <div class="stat-card pink">
      <div class="stat-label">Unused Balance</div>
      <div class="stat-value" style="color: #E91E63;">{{ number_format($statCards['unusedBalance'] ?? 0) }}</div>
      <div class="stat-subtitle">
        <span class="stat-trend down" style="color: var(--danger);">Action</span>
        <span>Required</span>
      </div>
    </div>
    <div class="stat-card cyan">
      <div class="stat-label">Serial Assets</div>
      <div class="stat-value" style="color: var(--info);">{{ number_format($statCards['serialAssets'] ?? 0) }}</div>
      <div class="stat-subtitle">
        <span style="color: var(--text-muted);">Issued Inventory</span>
      </div>
    </div>
    <div class="stat-card green2">
      <div class="stat-label">Serial Assets Active</div>
      <div class="stat-value" style="color: var(--success);">{{ number_format($statCards['assetsActive'] ?? 0) }}</div>
      <div class="stat-subtitle">
        <span style="color: var(--text-muted);">Deployment</span>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="charts-grid">
    <!-- Issue vs Used Chart -->
    <div class="chart-card">
      <div class="chart-title">Issue vs Used Overview</div>
      <div class="chart-subtitle">Material-wise Issued vs Used volume</div>
      
      <!-- Unit Tabs -->
      <div class="chart-tabs" id="unitTabs" style="display: flex; gap: 8px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
        <button class="chart-tab active" data-unit="all" onclick="filterChartByUnit('all')" style="padding: 6px 16px; border-radius: 4px; border: 1px solid #ddd; background: #2196F3; color: white; font-size: 11px; font-weight: 600; cursor: pointer;">ALL</button>
        @php
          $uniqueUnits = collect($chartData ?? [])->pluck('base_unit')->filter()->unique()->values();
        @endphp
        @foreach($uniqueUnits as $unit)
          <button class="chart-tab" data-unit="{{ $unit }}" onclick="filterChartByUnit('{{ $unit }}')" style="padding: 6px 16px; border-radius: 4px; border: 1px solid #ddd; background: white; color: #666; font-size: 11px; font-weight: 600; cursor: pointer;">{{ $unit }}</button>
        @endforeach
      </div>
      
      @if(!empty($chartData) && count($chartData) > 0)
      <div class="chart-scroll-container" style="min-height: 400px; display: flex; justify-content: center; align-items: center;">
        <canvas id="barChart" style="max-width: 100%; min-width: 600px; max-height: 350px;"></canvas>
      </div>
      <div class="chart-legend" style="margin-top: 15px; display: flex; gap: 20px;">
        <div class="chart-legend-item">
          <div class="chart-legend-dot" style="background: #2196F3;"></div>
          <span>Issued Qty</span>
        </div>
        <div class="chart-legend-item">
          <div class="chart-legend-dot" style="background: #4CAF50;"></div>
          <span>Used Qty</span>
        </div>
      </div>
      @else
      <div style="text-align: center; padding: 40px; color: var(--text-muted);">
        <i class="fa fa-chart-bar" style="font-size: 48px; margin-bottom: 12px; opacity: 0.3;"></i>
        <p>No data available for the selected filters</p>
      </div>
      @endif
    </div>

    <!-- Category Consumption Chart -->
    <div class="chart-card">
      <div class="chart-title">Category-wise Consumption</div>
      <div class="chart-subtitle">Material consumption by category from tickets</div>
      <div class="chart-tabs" id="categoryUnitTabs" style="display: flex; gap: 8px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
        <button class="chart-tab active" data-unit="all" onclick="filterCategoryByUnit('all')" style="padding: 6px 16px; border-radius: 4px; border: 1px solid #ddd; background: #2196F3; color: white; font-size: 11px; font-weight: 600; cursor: pointer;">ALL</button>
        @php
          $uniqueUnits = collect($chartData ?? [])->pluck('base_unit')->filter()->unique()->values();
        @endphp
        @foreach($uniqueUnits as $unit)
          <button class="chart-tab" data-unit="{{ $unit }}" onclick="filterCategoryByUnit('{{ $unit }}')" style="padding: 6px 16px; border-radius: 4px; border: 1px solid #ddd; background: white; color: #666; font-size: 11px; font-weight: 600; cursor: pointer;">{{ $unit }}</button>
        @endforeach
      </div>
      <div class="chart-scroll-container" style="min-height: 280px; display: flex; justify-content: center;">
        @if(!empty($categoryChartData) && count($categoryChartData) > 0)
          <canvas id="categoryPieChart" style="max-width: 320px; max-height: 450px; min-height: 355px;"></canvas>
        @else
          <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fa fa-chart-pie" style="font-size: 48px; margin-bottom: 12px; opacity: 0.3;"></i>
            <p>No category data available</p>
          </div>
        @endif
      </div>
      <div class="donut-legend" id="categoryLegend">
  
      </div>
    </div>
  </div>

  <!-- High Consumption Materials Table -->
  <div class="table-card">
    <div class="table-header">
      <div class="table-title">Material-wise Usage Summary</div>
      <div class="table-header-actions">
        <span style="font-size: 11px; color: var(--text-muted);">@if(isset($materialWiseData)){{ count($materialWiseData) }} materials @endif</span>
      </div>
    </div>
    <div style="overflow-x: auto; overflow-y: visible; max-height: 400px;">
    <table>
      <thead style="position: sticky; top: 0; z-index: 10;">
        <tr>
          <th style="min-width: 80px;">Code</th>
          <th style="min-width: 200px;">Material Name</th>
          <th style="min-width: 100px;">Issued</th>
          <th style="min-width: 100px;">Used</th>
          <th style="min-width: 100px;">Balance</th>
          <th style="min-width: 120px;">Usage %</th>
          <th style="min-width: 100px;">Status</th>
        </tr>
      </thead>
      <tbody>
        @if(!empty($materialWiseData) && count($materialWiseData) > 0)
          @foreach($materialWiseData as $item)
            @php
              $balance = $item['issued'] - $item['used'];
              $usagePct = $item['issued'] > 0 ? round(($item['used'] / $item['issued']) * 100, 1) : 0;
              if($balance < 0) {
                $statusClass = 'badge-critical';
                $statusText = 'NEGATIVE BALANCE';

              }
              elseif ($usagePct >= 80) {
                $statusClass = 'badge-optimal';
                $statusText = 'OPTIMAL';
              } elseif ($usagePct >= 50) {
                $statusClass = 'badge-stocked';
                $statusText = 'STOCKED';
              }
               elseif ($usagePct > 0) {
                $statusClass = 'badge-critical';
                $statusText = 'LOW USAGE';
              } else {
                $statusClass = 'badge-idle';
                $statusText = 'IDLE';
              }
            @endphp
            <tr>
              <td><strong>{{ $item['material_code'] ?: 'N/A' }}</strong></td>
              <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item['material_name'] }}">{{ $item['material_name'] }}</td>
              <td>{{ number_format($item['issued']) }}</td>
              <td style="color: var(--success); font-weight: 600;">{{ number_format($item['used']) }}</td>
              <td style="color: @if($balance > 0) #E91E63 @else var(--text-muted) @endif; font-weight: 600;">{{ number_format($balance) }}</td>
              <td>
                <div style="display: flex; align-items: center; gap: 8px;">
                  <div style="width: 60px; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;">
                    <div style="width: {{ sprintf('%.1f', $usagePct) }}%; height: 100%; background: @if($usagePct >= 50) var(--success) @else var(--warning) @endif;"></div>
                  </div>
                  <span style="font-size: 11px; font-weight: 600;">{{ $usagePct }}%</span>
                </div>
              </td>
              <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">
              No material usage data available for the selected filters
            </td>
          </tr>
        @endif
      </tbody>
    </table>
    </div>
  </div>

  <!-- Alerts and Exceptions -->
  <!-- <div class="table-card">
    <div class="table-header">
      <div class="table-title">Critical Alerts & Exceptions</div>
      <span style="color: #f44336; font-weight: 700; font-size: 11px; text-transform: uppercase; background: #fef5f5; padding: 4px 8px; border-radius: 3px;">2 ACTIVE</span>
    </div>
    <div class="tabs-container">
      <div class="alert-item high">
        <div class="alert-icon critical">
          <i class="fa fa-exclamation-triangle"></i>
        </div>
        <div class="alert-body">
          <div class="alert-title">Critical Stock Alert: High usage balance</div>
          <div class="alert-desc">Material Reserve in Fiber Optic cables dropped 20% off standard buffer, loss of material storage.</div>
          <div class="alert-severity">SEVERITY: CRITICAL</div>
        </div>
        <div class="alert-time">2h ago</div>
      </div>
      <div class="alert-item warning" style="margin-top: 12px;">
        <div class="alert-icon warning">
          <i class="fa fa-exclamation-circle"></i>
        </div>
        <div class="alert-body">
          <div class="alert-title">Negative Inventory Detected: Hub-042</div>
          <div class="alert-desc">Consumption logged without issuance record for 'Optical Splitter'. Buffer lost. Audit required.</div>
          <div class="alert-severity">SEVERITY: HIGH</div>
        </div>
        <div class="alert-time">5h ago</div>
      </div>
      <div class="alert-item info" style="margin-top: 12px;">
        <div class="alert-icon info">
          <i class="fa fa-info-circle"></i>
        </div>
        <div class="alert-body">
          <div class="alert-title">Ticket Delay: TKT-9821</div>
          <div class="alert-desc">Asset return pending from Employee (EMP-0802) for over 72 hours.</div>
          <div class="alert-severity">SEVERITY: INFO</div>
        </div>
        <div class="alert-time">1d ago</div>
      </div>
    </div>
  </div> -->

  <!-- District & Employee Usage -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
    <div class="table-card">
      <div class="table-header">
        <div class="table-title">District-wise Usage</div>
      </div>
      <div style="overflow-x: auto; max-height: 300px; overflow-y: auto;">
      <table>
        <thead style="position: sticky; top: 0; z-index: 10; background: #f8f9fa;">
          <tr>
            <th style="min-width: 150px;">District Name</th>
            <th style="min-width: 100px;">Total Issued</th>
            <th style="min-width: 100px;">Total Used</th>
            <th style="min-width: 90px;">Scrap Rate</th>
            <th style="min-width: 90px;">Status</th>
          </tr>
        </thead>
        <tbody>
          @if(!empty($districtWiseData) && count($districtWiseData) > 0)
            @foreach($districtWiseData as $district)
              @php
                $usagePct = $district['issued'] > 0 ? round(($district['used'] / $district['issued']) * 100, 1) : 0;
                $scrapRate = $district['issued'] > 0 ? round(($district['wastage'] / $district['issued']) * 100, 1) : 0;
                if ($usagePct >= 80) {
                  $statusClass = 'badge-optimal';
                  $statusText = 'OPTIMAL';
                } elseif ($usagePct >= 50) {
                  $statusClass = 'badge-stocked';
                  $statusText = 'STOCKED';
                } elseif ($usagePct > 0) {
                  $statusClass = 'badge-critical';
                  $statusText = 'LOW USAGE';
                }elseif($district['issued'] == 0 && $district['used'] > 0) {

                  $statusClass = 'badge-critical';

                  $statusText = 'USAGE WITHOUT ISSUE';

                }
                else {
                  $statusClass = 'badge-idle';
                  $statusText = 'IDLE';
                }
              @endphp
              <tr>
                <td><strong>{{ $district['district_name'] }}</strong></td>
                <td>{{ number_format($district['issued']) }}</td>
                <td style="color: var(--success); font-weight: 600;">{{ number_format($district['used']) }}</td>
                <td>{{ $scrapRate }}%</td>
                <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5" style="text-align: center; color: var(--text-muted);">No district data available</td>
            </tr>
          @endif
        </tbody>
      </table>
      </div>
    </div>

  <!-- Recent Transaction Logs -->
  <div class="table-card">
    <div class="table-header">
      <div class="table-title">Recent Transaction Logs</div>
      <!-- <div class="table-header-actions">
        <button class="table-filter-btn">↓ Filter Log</button>
      </div> -->
    </div>
    <div style="overflow-x: auto; max-height: 400px; overflow-y: auto;">
    <table>
      <thead style="position: sticky; top: 0; z-index: 10; background: #f8f9fa;">
        <tr>
          <th style="min-width: 130px;">Date & Time</th>
          <th style="min-width: 80px;">Type</th>
          <th style="min-width: 180px;">Material</th>
          <th style="min-width: 80px;">Qty</th>
          <th style="min-width: 150px;">Employee</th>
          <th style="min-width: 130px;">District</th>
          <th style="min-width: 100px;">Ticket ID</th>
        </tr>
      </thead>
      <tbody>
        @if(!empty($transactionLogs) && count($transactionLogs) > 0)
          @foreach($transactionLogs as $log)
            @php
              if ($log['type'] == 'ISSUE') {
                $typeClass = 'badge-hold';
              } elseif ($log['type'] == 'USED') {
                $typeClass = 'badge-used';
              } elseif ($log['type'] == 'RETURN') {
                $typeClass = 'badge-sent';
              } else {
                $typeClass = 'badge-idle';
              }
              $initials = strtoupper(substr($log['employee_name'], 0, 1));
              $date = \Carbon\Carbon::parse($log['date']);
            @endphp
            <tr>
              <td>{{ $date->format('M d, Y') }}<br><span style="font-size: 11px; color: var(--text-muted);">{{ $date->format('h:i A') }}</span></td>
              <td><span class="badge {{ $typeClass }}">{{ $log['type'] }}</span></td>
              <td>{{ $log['material_name'] }} ({{ $log['material_code'] }})</td>
              <td>{{ number_format($log['quantity']) }} {{ $log['base_unit'] }}</td>
              <td>
                <span style="display: inline-flex; align-items: center; gap: 6px;">
                  <span style="width: 24px; height: 24px; background: #2196F3; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 11px; font-weight: 700;">{{ $initials }}</span>
                  {{ $log['employee_name'] }}
                </span>
              </td>
              <td>{{ $log['district_name'] }}</td>
              <td>{{ $log['ticket_id'] ?: '-' }}</td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="7" style="text-align: center; color: var(--text-muted);">No transaction logs available</td>
          </tr>
        @endif
      </tbody>
    </table>
    </div>
  </div>

<script>
  // Chart.js Bar Chart - Global scope functions
  function formatQuantity(val) {
    if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
    if (val >= 1000) return (val / 1000).toFixed(1) + 'K';
    return val.toLocaleString('en-IN', { maximumFractionDigits: 0 });
  }
  
  let barChart = null;
  let categoryPieChart = null;

  const allChartData = {!! json_encode($chartData ?? []) !!};
  const categoryChartData = {!! json_encode($categoryChartData ?? []) !!};
  const allCategoryData = {!! json_encode($categoryChartData ?? []) !!};
  const categoryDataByUnit = {!! json_encode($categoryChartDataByUnit ?? []) !!};


  function filterChartByUnit(unit) {
    // Update tab buttons
    document.querySelectorAll('#unitTabs .chart-tab').forEach(btn => {
      if (btn.dataset.unit === unit) {
        btn.style.background = '#2196F3';
        btn.style.color = 'white';
        btn.classList.add('active');
      } else {
        btn.style.background = 'white';
        btn.style.color = '#666';
        btn.classList.remove('active');
      }
    });
    
    // Filter data by unit
    let filteredData = allChartData;
    if (unit !== 'all') {
      filteredData = allChartData.filter(d => d.base_unit === unit);
    }
    
    renderBarChartWithData(filteredData);
  }
  
  function renderBarChartWithData(chartData) {
    const ctx = document.getElementById('barChart');
    if (!ctx) return;
    
    // Destroy existing chart
    if (barChart) {
      barChart.destroy();
    }
    
    if (!chartData || chartData.length === 0) {
      ctx.parentElement.innerHTML = '<div style="text-align: center; padding: 60px; color: #999;">No data available for this unit</div>';
      return;
    }
    
    const labels = chartData.map(d => d.material_name.length > 20 ? d.material_name.substring(0, 17) + '...' : d.material_name);
    const issuedData = chartData.map(d => d.issued);
    const usedData = chartData.map(d => d.used);
    
    // Get unique units in filtered data
    const units = [...new Set(chartData.map(d => d.base_unit))];
    const unitLabel = units.length === 1 ? units[0] : 'Units';
    
    barChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Issued (' + unitLabel + ')',
            data: issuedData,
            backgroundColor: 'rgba(33, 150, 243, 0.8)',
            borderColor: 'rgba(25, 118, 210, 1)',
            borderWidth: 1,
            borderRadius: 4,
            barPercentage: 0.6,
            categoryPercentage: 0.7
          },
          {
            label: 'Used (' + unitLabel + ')',
            data: usedData,
            backgroundColor: 'rgba(76, 175, 80, 0.8)',
            borderColor: 'rgba(56, 142, 60, 1)',
            borderWidth: 1,
            borderRadius: 4,
            barPercentage: 0.6,
            categoryPercentage: 0.7
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              padding: 20,
              font: { size: 12 }
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                let value = context.parsed.y;
                if (label) {
                  label += ': ';
                }
                label += formatQuantity(value);
                return label;
              },
              title: function(context) {
                const item = chartData[context[0].dataIndex];
                return item.material_name + ' (' + (item.base_unit || '') + ')';
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return formatQuantity(value);
              },
              font: { size: 11 }
            },
            grid: {
              color: 'rgba(0,0,0,0.05)'
            }
          },
          x: {
            ticks: {
              autoSkip: false,
              maxRotation: 45,
              minRotation: 0,
              font: { size: 10 }
            },
            grid: {
              display: false
            }
          }
        }
      }
    });
  }
  
  function renderChartJSBarChart() {
    renderBarChartWithData(allChartData);
  }
  
 let categoryChartInstance = null;

 function renderCategoryPieChart() {
    const ctx = document.getElementById('categoryPieChart');
    if (!ctx) return;
    
    renderCategoryPieChartWithData(categoryChartData);
  }
    function renderCategoryPieChartWithData(catData) {
    const ctx = document.getElementById('categoryPieChart');
    if (!ctx) return;
    
    if (!catData || catData.length === 0) {
      ctx.parentElement.innerHTML = '<div style="text-align: center; padding: 40px; color: #999;">No category data for this unit</div>';
      return;
    }
    
    if (categoryPieChart) {
      categoryPieChart.destroy();
    }
    
    const labels = catData.map(d => d.category || 'Unknown');
    const data = catData.map(d => d.value);
    const colors = [
      '#2196F3', '#4CAF50', '#9C27B0', '#FF9800', 
      '#E91E63', '#00BCD4', '#795548', '#607D8B'
    ];
    const backgroundColors = catData.map((_, i) => colors[i % colors.length]);
    
    categoryPieChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: backgroundColors,
          borderWidth: 2,
          borderColor: '#fff'
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
            callbacks: {
              label: function(context) {
                const value = context.parsed;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const pct = ((value / total) * 100).toFixed(1);
                return context.label + ': ' + formatQuantity(value) + ' (' + pct + '%)';
              }
            }
          }
        }
      }
    });
    
    updateCategoryLegend(catData);
  }
   function updateCategoryLegend(catData) {
    const legendContainer = document.getElementById('categoryLegend');
    if (!legendContainer || !catData) return;
    
    const total = catData.reduce((sum, d) => sum + d.value, 0);
    const colors = ['#2196F3', '#4CAF50', '#9C27B0', '#FF9800', '#E91E63', '#00BCD4', '#795548', '#607D8B'];
    
    let html = '';
    catData.forEach((cat, i) => {
      const pct = total > 0 ? ((cat.value / total) * 100).toFixed(1) : 0;
      const color = colors[i % colors.length];
      html += `
        <div class="donut-legend-item">
          <div class="donut-legend-dot" style="background: ${color};"></div>
          <span>${cat.category || 'Unknown'}</span>
          <span class="donut-legend-pct">${pct}%</span>
        </div>
      `;
    });
    legendContainer.innerHTML = html;
  }
  function filterCategoryByUnit(unit) {
    document.querySelectorAll('#categoryUnitTabs .chart-tab').forEach(btn => {
      if (btn.dataset.unit === unit) {
        btn.style.background = '#2196F3';
        btn.style.color = 'white';
        btn.classList.add('active');
      } else {
        btn.style.background = 'white';
        btn.style.color = '#666';
        btn.classList.remove('active');
      }
    });
    
    let filteredData = allCategoryData;
    if (unit !== 'all' && categoryDataByUnit[unit]) {
      filteredData = categoryDataByUnit[unit];
    }
    
    renderCategoryPieChartWithData(filteredData);
  }


  // Initialize chart when DOM is ready
  document.addEventListener('DOMContentLoaded', function() {
    // Filter close buttons
    document.querySelectorAll('.filter-item-close').forEach(btn => {
      btn.addEventListener('click', function() {
        this.parentElement.remove();
      });
    });

    // Refresh button
    document.querySelector('.btn-refresh').addEventListener('click', function() {
      location.reload();
    });

    // Tab navigation
    document.querySelectorAll('.chart-tab').forEach(tab => {
      tab.addEventListener('click', function() {
        document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
      });
    });

    // Tab nav buttons
    document.querySelectorAll('.tab-nav button').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-nav button').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
      });
    });

    // District-Employee cascading dropdown
    const districtSelect = document.getElementById('district_id');
    if (districtSelect) {
      districtSelect.addEventListener('change', function() {
        const districtId = this.value;
        const empSelect = document.getElementById('emp');

        fetch(`/admin/get-employees?district_id=${districtId}`)
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              empSelect.innerHTML = '<option value="">All Employees</option>';
              data.employees.filter(emp => emp.type === 2).forEach(emp => {
                empSelect.innerHTML += `<option value="${emp.id}">${emp.first_name} ${emp.last_name}</option>`;
              });
            }
          });
      });
    }
    
    // Render the chart
    renderChartJSBarChart();
    renderCategoryPieChart();
  });
</script>

@endsection
