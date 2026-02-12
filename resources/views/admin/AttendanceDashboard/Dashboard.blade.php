@extends('admin.layout.base')

@section('title', 'Attendance Dashboard - ')

@section('content')
 @php
    $roles = [
        1 => 'OFC',
        2 => 'FRT',
        5 => 'Patroller',
        3 => 'Zonal incharge',
        4 => 'District incharge'
    ];

    $user = Session::get('user');
    $DistId = null; 
    if ($user && isset($user->district_id)) {
        $DistId = $user->district_id;
    }
    $queryParams = request()->all();
@endphp

<div class="attendance-dashboard">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="dashboard-header">
            <div class="header-left">
                <h1 class="dashboard-title">Attendance Dashboard</h1>
            </div>
        </div>

       <!-- Filters Section -->
<div class="filters-section">
    <form method="GET" action="{{ route('admin.attendance_dashboard') }}">
         <div class="filters-row">
          
        
            <div class="filter-group">
                <label class="filter-label">District</label>
                <select name="district_id" class="filter-select">
                    <option value="">Select District</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" 
                        {{ (request('district_id') == $district->id) || ($DistId && $DistId == $district->id) ? 'selected' : '' }}>

                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
            </div>
       

            <div class="filter-group">
                <label class="filter-label">Block</label>
                <select name="block_id" class="filter-select">
                    <option value="">Select Block</option>
                    @foreach($blocks as $block)
                        <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>
                            {{ $block->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Zone</label>
                <select name="zone_id" class="filter-select">
                    <option value="">Select Zone</option>
                    @foreach($zonals as $zon)
                        <option value="{{ $zon->id }}" {{ request('zone_id') == $zon->id ? 'selected' : '' }}>
                            {{ $zon->Name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Role</label>
                <select name="role" class="filter-select">
                    <option value="">All Roles</option>
                    @foreach($roles as $id => $roleName)
                        <option value="{{ $id }}" {{ request('role') == $id ? 'selected' : '' }}>
                            {{ $roleName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Date Range</label>
                <select name="date_range" class="filter-select">
                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                </select>
            </div>

            <!-- <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="filter-input" placeholder="Name, ID, GP...">
            </div> -->

            <div class="filter-group" style="display: flex; gap: 0.5rem;">
                <button type="submit" class="action-btn action-btn-primary">Apply</button>
                <a href="{{ route('admin.attendance_dashboard') }}" class="action-btn action-btn-secondary">Clear</a>
            </div>
        </div>
    </form>
</div>


        <!-- Statistics Cards -->
        <div class="stats-grid">
            <a href="{{ route('admin.attendance_list', $queryParams) }}" class="stat-card">

                <div class="stat-content">
                    <div class="stat-number">{{ $totalStaff }}</div>
                    <div class="stat-label">Total Staff</div>
                </div>
                <div class="stat-icon stat-icon-blue">
                    <i class="fa fa-users"></i>
                </div>
            
            </a>
            <a href="{{ route('admin.attendance_list', array_merge(request()->all(), ['status' => 'present']))}}" class="stat-card">

                <div class="stat-content">
                    <div class="stat-number">{{ $present }}</div>
                    <div class="stat-label">Present</div>
                </div>
                <div class="stat-icon stat-icon-green">
                    <i class="fa fa-user"></i>
                </div>
           
             </a>
            <a href="{{ route('admin.attendance_list', array_merge(request()->all(), ['status' => 'absent']))}}" class="stat-card">

                <div class="stat-content">
                    <div class="stat-number">{{ $absent }}</div>
                    <div class="stat-label">Absent</div>
                </div>
                <div class="stat-icon stat-icon-red">
                    <i class="fa fa-user-times"></i>
                </div>
            
            </a>
            <a href="{{ route('admin.attendance_list', array_merge(request()->all(), ['status' => 'late']))}}" class="stat-card">

                <div class="stat-content">
                    <div class="stat-number">{{ $lateCheckins }}</div>
                    <div class="stat-label">Late Check-ins</div>
                </div>
                <div class="stat-icon stat-icon-orange">
                    <i class="fa fa-clock"></i>
                </div>
           
            </a>
           <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number" id="offline-count">0</div>
                    <div class="stat-label">Offline</div>
                </div>
                <div class="stat-icon stat-icon-indigo">
                    <i class="fa fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number" id="online-count">0</div>
                    <div class="stat-label">Live on Map</div>
                </div>
                <div class="stat-icon stat-icon-teal">
                    <i class="fa fa-map-marker-alt"></i>
                </div>
            </div>
            
             <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number">{{ $avgDurationFormatted }}</div>
                    <div class="stat-label">Avg Patrol Duration</div>
                </div>
                <div class="stat-icon stat-icon-purple">
                    <i class="fa fa-stopwatch"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number">78%</div>
                    <div class="stat-label">Checklist Done</div>
                </div>
                <div class="stat-icon stat-icon-cyan">
                    <i class="fa fa-clipboard-check"></i>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="main-content-grid">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Role-Zone Matrix -->
                <div class="matrix-card">
                    <h3 class="card-title">Role-Zone Matrix</h3>
                    <div class="matrix-table">
                       <table class="matrix">
                            <thead>
                                <tr>
                                    <th class="matrix-header">Role</th>
                                       @foreach($zonals as $zone)
                                            <th class="matrix-header">{{ $zone->Name }}</th>
                                        @endforeach
                                    <th class="matrix-header">Total</th>
                                </tr>
                            </thead>
                          <tbody>
                                @foreach($roles as $roleId => $roleName)
                                    <tr>
                                        <td class="role-cell">{{ $roleName }}</td>

                                        @php 
                                            $rowTotalPresent = 0; 
                                            $rowTotalAll = 0;
                                        @endphp

                                        @foreach($zonals as $zone)
                                            @php
                                                $present = $matrix[$roleId][$zone->id]['present'] ?? 0;
                                                $total   = $matrix[$roleId][$zone->id]['total'] ?? 0;
                                                $rowTotalPresent += $present;
                                                $rowTotalAll += $total;
                                            @endphp
                                            <td class="matrix-cell">
                                                <span class="matrix-value">{{ $present }}</span>
                                                <span class="matrix-total">/{{ $total }}</span>
                                            </td>
                                        @endforeach

                                        <td class="matrix-cell total-cell">{{ $rowTotalPresent }}/{{ $rowTotalAll }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>
                </div>

            </div>

            <!-- Right Column -->
            <!-- <div class="right-column">
                <div class="map-card">
                    <div class="map-header">
                        <h3 class="card-title">Live Map</h3>

                        <button class="view-full-btn">
                            <i class="fa fa-expand"></i>
                            <a href="{{ url('/admin/trackattendance') }}"> View Full</a>
                        </button>
                    </div>
                    <div class="map-container">
                        <div class="map-placeholder">
                            <div class="map-content">
                                <div class="active-indicator" id="legend">
                                </div>
                                <div class="map-visual">
                                    <div id="dashboardMap"></div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

              
            </div> -->
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    
/* Reset and Base Styles */
* {
    box-sizing: border-box;
}

.pagination-container {
    margin-top: 12px;
    text-align: center;
}
.attendance-dashboard {
    background-color: #f8fafc;
    min-height: 100vh;
    padding: 1.5rem 0;
}

.container-fluid {
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Header Section */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.dashboard-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}
.td-actions {
    white-space: nowrap;
}
.action-btn {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    border: 1px solid;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-right: 0.5rem;
}

.action-btn-primary {
    background-color: transparent;
    border-color: #4299e1;
    color: #4299e1;
}

.action-btn-primary:hover {
    background-color: #4299e1;
    color: white;
}

.action-btn-secondary {
    background-color: transparent;
    border-color: #a0aec0;
    color: #718096;
}

.action-btn-secondary:hover {
    background-color: #718096;
    color: white;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}





/* Filters Section */
.filters-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filters-row {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    margin-bottom: 0.5rem;
}

.filter-select,
.filter-input {
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.875rem;
    background: white;
    transition: border-color 0.2s ease;
}

.filter-select:focus,
.filter-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.quick-filters {
    grid-column: span 1;
}

.quick-filter-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quick-filter-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #64748b;
    cursor: pointer;
}

.quick-filter-checkbox input[type="checkbox"] {
    margin: 0;
}

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    flex-shrink: 0;
}

.stat-icon-blue { background-color: #3b82f6; }
.stat-icon-green { background-color: #10b981; }
.stat-icon-red { background-color: #ef4444; }
.stat-icon-orange { background-color: #f59e0b; }
.stat-icon-purple { background-color: #8b5cf6; }
.stat-icon-teal { background-color: #14b8a6; }
.stat-icon-indigo { background-color: #6366f1; }
.stat-icon-cyan { background-color: #06b6d4; }

/* Main Content Grid */
.main-content-grid {
    /* display: grid; */
    grid-template-columns: 1fr 400px;
    gap: 2rem;
}

/* Left Column */
.left-column {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* Matrix Card */
.matrix-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 1.5rem 0;
}

.matrix-table {
    overflow-x: auto;
}

.matrix {
    width: 100%;
    border-collapse: collapse;
}

.matrix-header {
    background: #f8fafc;
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
}

.matrix td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
}
.role-cell {
    font-weight: 500;
    color: #1e293b;
}

.matrix-cell {
    /* text-align: center; */
}

.matrix-value {
    font-weight: 600;
    color: #10b981;
}

.matrix-total {
    color: #64748b;
    font-size: 0.875rem;
}

.total-cell {
    font-weight: 600;
    color: #1e293b;
}

/* Records Card */
.records-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    flex: 1;
}

.records-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.export-btn {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.export-btn:hover {
    background: #2563eb;
}

.records-table {
    overflow-x: auto;
    max-height: 800px;   /* adjust height */
    overflow-y: auto;    /* vertical scroll */
    max-width:800px;
    /* border: 1px solid #e5e7eb;
    border-radius: 8px; */
    
}

.attendance-table {
    width: 100%;
    border-collapse: collapse;
}

.attendance-table thead th {
    background: #f8fafc;
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.attendance-table tbody td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.record-row:hover {
    background: #f8fafc;
}

.staff-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.staff-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border-color: #000000;
}

.staff-name {
    font-weight: 500;
    color: #1e293b;
    font-size: 0.875rem;
    margin: 0;
}

.staff-id {
    font-size: 0.75rem;
    color: #64748b;
    margin: 0;
}

.role-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}
.role-ofc { background: #e0f2fe; color: #0369a1; }
.role-frt { background: #f0fdf4; color: #166534; }
.role-patroller { background: #fef2f2; color: #b91c1c; }
.role-zonal-incharge { background: #fdf4ff; color: #7e22ce; }
.role-district-incharge { background: #fff7ed; color: #c2410c; }
.role-unknown { background: #e2e8f0; color: #475569; }

.time-cell {
    color: #10b981;
    font-weight: 500;
}

.patrol-cell {
    color: #10b981;
    font-weight: 500;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-done {
    background: #f0fdf4;
    color: #166534;
}

.status-late {
    background: #fef3c7;
    color: #92400e;
}

.status-present {
    background: #f0fdf4;
    color: #166534;
}

.score-cell {
    font-weight: 500;
    color: #1e293b;
}

/* Right Column */
.right-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Map Card */
.map-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.map-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.view-full-btn {
    background: transparent;
    border: 1px solid #e2e8f0;
    color: #64748b;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.view-full-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e0;
}

.map-container {
    height: 200px;
    background: #f8fafc;
    border-radius: 8px;
    position: relative;
    overflow: hidden;
}
#dashboardMap {
    height: 500px;
    width: 100%;
     position: initial !important;
}
.map-placeholder {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.map-content {
    text-align: center;
    color: #64748b;
}

.active-indicator {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: white;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.active-count {
    font-size: 1.25rem;
    font-weight: 600;
    color: #3b82f6;
    display: block;
}

.active-label {
    font-size: 0.75rem;
    color: #64748b;
}

.map-visual i {
    font-size: 2rem;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
}

.map-visual p {
    margin: 0;
    font-size: 0.875rem;
    color: #64748b;
}

.map-subtitle {
    font-size: 0.75rem !important;
    color: #94a3b8 !important;
}

/* Exceptions Card */
.exceptions-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.exceptions-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.exception-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.exception-warning {
    background: #fef3c7;
    border-left: 4px solid #f59e0b;
}

.exception-info {
    background: #dbeafe;
    border-left: 4px solid #3b82f6;
}

.exception-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.exception-warning .exception-icon {
    background: #f59e0b;
    color: white;
}

.exception-info .exception-icon {
    background: #3b82f6;
    color: white;
}

.exception-content {
    flex: 1;
}

.exception-title {
    font-weight: 500;
    color: #1e293b;
    font-size: 0.875rem;
    margin: 0 0 0.125rem 0;
}

.exception-subtitle {
    font-size: 0.75rem;
    color: #64748b;
    margin: 0;
}

.exception-action {
    background: transparent;
    border: 1px solid #e2e8f0;
    color: #3b82f6;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.exception-action:hover {
    background: #f8fafc;
    border-color: #3b82f6;
}

/* Insights Card */
.insights-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.insights-card .card-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #1e293b;
}

.insights-card .card-title i {
    color: #f59e0b;
}

.insights-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.insight-item {
    background: #f8fafc;
    padding: 0.75rem;
    border-radius: 8px;
    border-left: 3px solid #3b82f6;
}

.insight-item p {
    margin: 0;
    font-size: 0.875rem;
    color: #475569;
    line-height: 1.4;
}

/* Trend Card */
.trend-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.trend-placeholder {
    height: 150px;
    background: #f8fafc;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.trend-chart {
    text-align: center;
    color: #64748b;
}

.trend-chart i {
    font-size: 2rem;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
}

.trend-chart p {
    margin: 0;
    font-size: 0.875rem;
}

.trend-subtitle {
    font-size: 0.75rem !important;
    color: #94a3b8 !important;
}

/* Responsive Design */
@media (max-width: 1400px) {
    .main-content-grid {
        grid-template-columns: 1fr 350px;
    }
}

@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .filters-row {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 992px) {
    .main-content-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filters-row {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .filters-row {
        grid-template-columns: 1fr;
    }
    
    .container-fluid {
        padding: 0 1rem;
    }
    
    .attendance-dashboard {
        padding: 1rem 0;
    }
}

@media (max-width: 576px) {
    .dashboard-title {
        font-size: 1.5rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}

/* Utility Classes */
.fa {
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
function loadDashboardMapData() {

    $.ajax({
        url: "{{ url('/admin/get_dashboard_map') }}",
        dataType: "json",
        type: "GET",
        success: function(data) {
            let onlineCount = 0, offlineCount = 0;
             let currentIds = [];

            data.forEach(function(element) {
               let status = element.service ? element.service.status : element.status;


                if (status === 'active') onlineCount++;
                else offlineCount++;
            });
          
            document.getElementById('online-count').innerText  = onlineCount;
            document.getElementById('offline-count').innerText  = offlineCount;

        }
    });
}
    
});
</script>



@endsection