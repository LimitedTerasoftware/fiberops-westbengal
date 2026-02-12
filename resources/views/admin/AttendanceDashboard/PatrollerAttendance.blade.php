@extends('admin.layout.base')

@section('title', 'Attendance Summary - ')

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
@endphp
<div class="content-area py-4">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="header-section mb-4">
            <div class="header-content">
                <h1 class="page-title">Attendance List</h1>
                <p class="page-subtitle">Field Team Management</p>
            </div>
            <!-- <button class="btn btn-export">
                <i class="fa fa-download me-2"></i>Export
            </button> -->
        @if(auth()->user()->role != 'client')

         <button id="export-btn" class="btn btn-export">
            <span id="btn-text"><i class="fa fa-download me-2"></i>Export</span>
            <span id="btn-loading" style="display:none;"><i class="fa fa-spinner fa-spin me-2"></i>Loading...</span>
        </button>
        @endif

        </div>

        <!-- Filters Section -->
         <div class="filters-section mb-4">
    <form method="GET" action="{{ route('admin.attendance_list') }}">

        <div class="filters-grid">
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
             <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select">
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>obsent</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late check in</option>
                </select>
            </div>
        </div>

                <!-- New Row: From Date & To Date -->
                <div class="filters-grid mt-1">
                    <div class="filter-group">
                        <label class="filter-label">From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-input">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-input">
                    </div>
                  <div class="filter-group">
                    <label class="filter-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="filter-input" placeholder="Name, ID, GP...">
                </div>

                    <!-- Buttons Row -->
                    <div class="filter-group" style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="action-btn action-btn-primary">Apply</button>
                        <a href="{{ route('admin.attendance_list') }}" class="action-btn action-btn-secondary">Clear</a>
                    </div>
                </div>

            </form>
        </div>
        <!-- Table Section -->
        <div class="table-section">
            <h2 class="table-title">Detailed Attendance Records</h2>
            
            <div class="table-container">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th class="th-staff">STAFF</th>
                            <th class="th-role">ROLE</th>
                            <th class="th-date">DATE</th>
                            <th class="th-zone">ZONE</th>
                            <th class="th-punch">PUNCH IN</th>
                            <th class="th-punch">PUNCH OUT</th>
                            <th class="th-duration">DURATION</th>
                             <th class="th-zone">Version</th>
                            <!-- <th class="th-km">KM</th> -->
                            <th class="th-status">STATUS</th>
                            <th class="th-status">LEAVE STATUS</th>

                            <th class="th-actions">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($providers as $provider)

                            <tr class="table-row">
                                <td class="td-staff">
                                    <div class="staff-info">
                                        <div class="staff-avatar">
                                         <img 
                                            src="{{ $provider->online_image 
                                                ? asset('uploads/attendance_images/' . $provider->online_image) 
                                                : 'https://cdn-icons-png.flaticon.com/512/847/847969.png' }}" 
                                           alt="{{ $provider->first_name }} {{ $provider->last_name }}"
                                            width="50" 
                                            height="50">
                                        </div>
                                        <div class="staff-details">
                                            <div class="staff-name">{{ $provider->first_name }} {{ $provider->last_name }}</div>
                                            @if(auth()->user()->role != 'client')

                                            <div class="staff-id">{{ $provider->mobile }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="td-role">
                                    @if(isset($roles[$provider->type]))
                                        <span class="role-badge role-{{ strtolower(str_replace(' ', '-', $roles[$provider->type])) }}">
                                            {{ $roles[$provider->type] }}
                                        </span>
                                    @else
                                        <span class="role-badge role-unknown">Unknown</span>
                                    @endif
                                </td>
                                <td class="td-date">
                                    {{ $provider->check_in ? \Carbon\Carbon::parse($provider->check_in)->format('d/m/Y') :  \Carbon\Carbon::parse($provider->display_attendance_date)->format('d/m/Y')  ?? 'N/A' }}
                                </td>
                                <td class="td-zone">{{ isset($provider->zone_name) ? $provider->zone_name : 'N/A' }}</td>
                                <td class="td-punch">{{ $provider->check_in ? \Carbon\Carbon::parse($provider->check_in)->format('h:i A') : '—' }}</td>
                                <td class="td-punch"> 
                                    @if($provider->attendance_status == 'active')
                                    -
                                    @else
                                    {{ $provider->check_out ? \Carbon\Carbon::parse($provider->check_out)->format('h:i A') : '—' }}
                                    @endif
                                </td>
                                    <?php
                                        $startTime = Carbon\Carbon::parse($provider->check_in);
                                        $currenttime = Carbon\Carbon::now();
                                        $currentdate =$currenttime->toDateTimeString();
                                        if($provider->attendance_status == 'active'){
                                        $finishTime = Carbon\Carbon::parse($currentdate);
                                        }
                                        else {
                                        $finishTime = Carbon\Carbon::parse($provider->check_out);	 
                                        }
                                        $totalDuration = $finishTime->diffInSeconds($startTime);
                                        $duration =gmdate('H:i:s', $totalDuration);						 
                                    ?>	
                                <td class="td-duration">
                                    {{ $duration? $duration . ' hrs' : $provider->duration  }}
                                    
                                </td>
                                <td class="td-zone">{{ isset($provider->version) ? $provider->version : 'N/A' }}</td>

                                <!-- <td class="td-km">22.4</td> -->
                                <td class="td-status">
                                      @if($provider->attendance_status == 'active')
                                            <span class="status-badge status-done">Online</span>
                                        @else
                                            <span class="status-badge status-late">Offline</span>
                                        @endif
                                </td>
                                 <td class="td-status">
                                      @if($provider->leave_status)
                                            <span class="status-badge status-late">{{$provider->leave_status}}</span>

                                        @else
                                            <span class="status-badge status-done">Available</span>

                                        @endif
                                </td>
                               

                                <td class="td-actions">
                                   
                                     <a href="{{ route('admin.staff_livetrack', $provider->id) }}?date={{ date('Y-m-d') }}" class="action-btn action-btn-secondary" title="View Route">
                                        <i class="fas fa-route"></i> 
                                    </a>
                                    <a href="{{ route('admin.staff_details', $provider->id) }}" class="action-btn action-btn-secondary" title="View Profile">
                                        <i class="fas fa-calendar-alt"></i> 
                                    </a>
                                    @if(auth()->user()->role != 'client' )
                                  
                                    
                                        @if($provider->leave_id)
                                            {{-- EDIT --}}
                                          <button
                                            type="button"
                                            class="action-btn action-btn-primary"
                                            data-provider-id="{{ $provider->id }}"
                                            data-name="{{ $provider->first_name }} {{ $provider->last_name }}"
                                            data-leave='{!! json_encode([
                                                "id"     => $provider->leave_id,
                                                "type"   => $provider->leave_type,
                                                "reason" => $provider->leave_reason,
                                            ]) !!}'
                                            onclick="AddLeaveFromBtn(this)">
                                            <i class="ti-plus"></i>
                                        </button>



                                        @else
                                            {{-- ADD --}}
                                            <button
                                                type="button"
                                                class="action-btn action-btn-danger"
                                                onclick="AddLeave({{ $provider->id }}, '{{ $provider->first_name }} {{ $provider->last_name }}')">
                                                <i class="ti-plus"></i>
                                            </button>
                                        @endif
                                    

                                    @endif
                                   </td>

                            </tr>
                        @endforeach

                     
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-section">
            <div class="pagination-info">
                Showing 
                {{ ($providers->currentPage() - 1) * $providers->perPage() + 1 }} 
                to 
                {{ ($providers->currentPage() * $providers->perPage()) > $providers->total() 
                    ? $providers->total() 
                    : $providers->currentPage() * $providers->perPage() 
                }} 
                of {{ $providers->total() }} entries
            </div>
            <nav class="pagination-nav">
                {{ $providers->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    </div>
      {{-- Add New Leave Modal --}}
    <div id="addLeaveModal" class="terrasoft-modal">
    <div class="terrasoft-modal-overlay" onclick="closeAddLeaveModal()">
        <div class="terrasoft-modal-container" onclick="event.stopPropagation()">

            <!-- Header -->
            <div class="terrasoft-modal-header">
                <div class="terrasoft-modal-title-section">
                    <div class="terrasoft-modal-icon">
                        <i class="ti-calendar"></i>
                    </div>
                    <div>
                        <h3 class="terrasoft-modal-title">Mark Today’s Status</h3>
                        <span class="terrasoft-form-label" id="EmpName"></span>
                    </div>
                </div>
                <button class="terrasoft-modal-close-btn" onclick="closeAddLeaveModal()" type="button">
                    <i class="ti-x"></i>
                </button>
            </div>

            <!-- Form -->
            <form id="addLeaveForm" class="terrasoft-modal-form" onsubmit="submitLeaveForm(event)">
                {{ csrf_field() }}

                <div class="terrasoft-modal-body">

                    <!-- Emp ID -->
                    <input type="hidden" id="provider_id" name="provider_id">
                     <input type="hidden" id="leave_id" name="leave_id">

                    <!-- Today's date display -->
                    <div class="terrasoft-form-group">
                        <label class="terrasoft-form-label">
                            <i class="ti-time"></i> Date
                        </label>
                        <input 
                            type="text" 
                            id="today_date_display"
                            class="terrasoft-form-input"
                            value=""
                            readonly
                        >
                    </div>

                    <!-- Status -->
                    <div class="terrasoft-form-group">
                        <label class="terrasoft-form-label">
                            <i class="ti-check"></i> Mark As
                        </label>
                        <select id="type" name="type" class="terrasoft-form-select">
                            <option value="leave" selected>On Leave (Today)</option>
                            <option value="late_login">Late Login (Today)</option>
                        </select>
                    </div>

                    <!-- Reason -->
                    <div class="terrasoft-form-group">
                        <label class="terrasoft-form-label">
                            <i class="ti-file-text"></i> Reason
                        </label>
                        <textarea 
                            id="reason"
                            name="reason"
                            class="terrasoft-form-textarea"
                            placeholder="Reason (optional)"
                            rows="3"
                        ></textarea>
                    </div>

                </div>

                <!-- Footer -->
                <div class="terrasoft-modal-footer">
                    <button type="button" class="terrasoft-btn terrasoft-btn-secondary" onclick="closeAddLeaveModal()">
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="terrasoft-btn terrasoft-btn-danger"
                        id="deleteLeaveBtn"
                        style="display:none"
                        onclick="deleteLeave()">
                        <i class="ti-trash"></i> Delete
                    </button>
                    <button type="submit" class="terrasoft-btn terrasoft-btn-primary" id="submitLeaveBtn">
                        <i class="ti-save"></i> Save
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
    {{-- Success/Error Toast Notifications --}}
    <div id="leaveToast" class="terrasoft-toast">
        <div class="terrasoft-toast-content">
            <div class="terrasoft-toast-icon" id="leaveToastIcon">
                <i class="ti-check"></i>
            </div>
            <div class="terrasoft-toast-message" id="leaveToastMessage">
                Leave added successfully!
            </div>
            <button class="terrasoft-toast-close" onclick="closeLeaveToast()">
                <i class="ti-x"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="{{ asset('/css/filter.css')}}">

<style>
/* Reset and Base Styles */
* {
    box-sizing: border-box;
}


.content-area {
    background-color: #f8fafc;
    min-height: 100vh;
    padding: 2rem 0;
}

.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Header Section */
.header-section {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
}

.header-content {
    flex: 1;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0 0 0.25rem 0;
    line-height: 1.2;
}

.page-subtitle {
    font-size: 1rem;
    color: #718096;
    margin: 0;
    font-weight: 400;
}

.btn-export {
    background-color: #38a169;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-export:hover {
    background-color: #2f855a;
    transform: translateY(-1px);
    color:white;
}



/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1.5rem;
    margin-bottom: 3rem;
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
    font-size: 2.25rem;
    font-weight: 700;
    color: #1a202c;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #718096;
    font-weight: 500;
    line-height: 1.2;
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

.stat-icon-blue { background-color: #4299e1; }
.stat-icon-green { background-color: #38a169; }
.stat-icon-orange { background-color: #ed8936; }
.stat-icon-purple { background-color: #9f7aea; }
.stat-icon-indigo { background-color: #667eea; }

/* Table Section */
.table-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.table-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a202c;
    padding: 1.5rem 1.5rem 0;
    margin: 0 0 1.5rem 0;
}

.table-container {
    overflow-x: auto;
}

.attendance-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.attendance-table thead th {
    background-color: #f7fafc;
    padding: 1rem 1.5rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border: none;
    white-space: nowrap;
}

.attendance-table tbody td {
    padding: 1.25rem 1.5rem;
    border-top: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: 0.875rem;
}

.table-row:hover {
    background-color: #f8fafc;
}

/* Staff Info */
.staff-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 180px;
}

.staff-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.staff-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.staff-details {
    flex: 1;
}

.staff-name {
    font-weight: 500;
    color: #1a202c;
    margin: 0 0 0.125rem 0;
    line-height: 1.2;
}

.staff-id {
    font-size: 0.75rem;
    color: #718096;
    margin: 0;
    line-height: 1.2;
}

/* Role Badges */
.role-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    display: inline-block;
}

.role-ofc { background: #e0f2fe; color: #0369a1; }
.role-frt { background: #f0fdf4; color: #166534; }
.role-patroller { background: #fef2f2; color: #b91c1c; }
.role-zonal-incharge { background: #fdf4ff; color: #7e22ce; }
.role-district-incharge { background: #fff7ed; color: #c2410c; }
.role-unknown { background: #e2e8f0; color: #475569; }


/* Status Badges */
.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    white-space: nowrap;
}
.status-done {
    background: #f0fdf4;
    color: #166534;
}

.status-late {
    background: #fef3c7;
    color: #92400e;
}

.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}

.status-synced {
    background-color: #f0fff4;
    color: #2f855a;
}

.status-synced .status-dot {
    background-color: #38a169;
}

.status-pending {
    background-color: #fffbeb;
    color: #b45309;
}

.status-pending .status-dot {
    background-color: #ed8936;
}

.status-absent {
    background-color: #fed7d7;
    color: #c53030;
}

.status-absent .status-dot {
    background-color: #e53e3e;
}

/* Action Buttons */
.td-actions {
    white-space: nowrap;
}


/* Pagination */
.pagination-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 0.5rem;
}

.pagination-info {
    font-size: 0.875rem;
    color: #718096;
}

.pagination {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 0.25rem;
}

.page-item {
    display: block;
}

.page-link {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    color: #4a5568;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.page-item.active .page-link {
    background-color: #4299e1;
    border-color: #4299e1;
    color: white;
}

.page-item.disabled .page-link {
    color: #a0aec0;
    cursor: not-allowed;
}

.page-link:hover:not(.disabled) {
    background-color: #f7fafc;
    border-color: #cbd5e0;
}

/* Column Widths */
.th-staff, .td-staff { width: 200px; }
.th-role, .td-role { width: 100px; }
.th-date, .td-date { width: 120px; }
.th-zone, .td-zone { width: 180px; }
.th-punch, .td-punch { width: 100px; }
.th-duration, .td-duration { width: 100px; }
.th-km, .td-km { width: 80px; }
.th-status, .td-status { width: 120px; }
.th-actions, .td-actions { width: 180px; }

/* Responsive Design */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
  
}

@media (max-width: 768px) {
    .header-section {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
   
    
    .pagination-section {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .table-container {
        overflow-x: scroll;
    }
    
    .attendance-table {
        min-width: 800px;
    }
}

@media (max-width: 576px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .container-fluid {
        padding: 0 1rem;
    }
}

/* Utility Classes */
.me-2 {
    margin-right: 0.5rem;
}

/* Modal Overlay */
.terrasoft-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000;
}

.terrasoft-modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.terrasoft-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Modal Container */
.terrasoft-modal-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow: hidden;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Modal Header */
.terrasoft-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px 24px 20px 24px;
    border-bottom: 1px solid #e2e8f0;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.terrasoft-modal-title-section {
    display: flex;
    align-items: center;
    gap: 12px;
}

.terrasoft-modal-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.terrasoft-modal-title {
    font-size: 20px;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.terrasoft-modal-close-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: #f1f5f9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}

.terrasoft-modal-close-btn:hover {
    background: #e2e8f0;
    color: #475569;
}

/* Modal Body */
.terrasoft-modal-body {
    padding: 24px;
    max-height: 60vh;
    overflow-y: auto;
}

.terrasoft-modal-form {
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Form Styles */
.terrasoft-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 20px;
}

.terrasoft-form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 20px;
}

.terrasoft-form-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

.terrasoft-required {
    color: #ef4444;
}

.terrasoft-form-input,
.terrasoft-form-select,
.terrasoft-form-textarea {
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    transition: all 0.2s ease;
    font-family: inherit;
}

.terrasoft-form-input:focus,
.terrasoft-form-select:focus,
.terrasoft-form-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.terrasoft-form-textarea {
    resize: vertical;
    min-height: 80px;
}

.terrasoft-form-textarea::placeholder {
    color: #9ca3af;
}

/* Modal Footer */
.terrasoft-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 20px 24px 24px 24px;
    border-top: 1px solid #f1f5f9;
    background: #fafbfc;
}

/* Buttons */
.terrasoft-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 120px;
    justify-content: center;
}

.terrasoft-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.terrasoft-btn-secondary {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}

.terrasoft-btn-secondary:hover:not(:disabled) {
    background: #e2e8f0;
}

.terrasoft-btn-danger {
    background: #f91111ff;
    color: white;
    border: 1px solid #cbd5e1;
}

.terrasoft-btn-danger:hover:not(:disabled) {
    background: #e95656ff;
}


.terrasoft-btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.terrasoft-btn-primary:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.terrasoft-btn-primary.loading {
    opacity: 0.7;
    cursor: not-allowed;
}

.terrasoft-btn-primary.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Toast Notifications */
.terrasoft-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1100;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}

.terrasoft-toast.show {
    opacity: 1;
    transform: translateX(0);
}

.terrasoft-toast-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 300px;
}

.terrasoft-toast-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.terrasoft-toast.success .terrasoft-toast-icon {
    background: #22c55e;
}

.terrasoft-toast.error .terrasoft-toast-icon {
    background: #ef4444;
}

.terrasoft-toast-message {
    flex: 1;
    font-size: 14px;
    color: #1e293b;
    font-weight: 500;
}

.terrasoft-toast-close {
    background: none;
    border: none;
    color: #64748b;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

.terrasoft-toast-close:hover {
    background: #f1f5f9;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .terrasoft-modal-container {
        margin: 10px;
        max-width: calc(100vw - 20px);
    }

    .terrasoft-form-row {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .terrasoft-modal-header {
        padding: 20px 16px 16px 16px;
    }

    .terrasoft-modal-body {
        padding: 20px 16px;
    }

    .terrasoft-modal-footer {
        padding: 16px;
        flex-direction: column;
    }

    .terrasoft-btn {
        width: 100%;
    }

    .terrasoft-toast-content {
        min-width: calc(100vw - 40px);
    }
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
  

    $('#export-btn').on('click', function() {
        const btnText = $('#btn-text');
        const btnLoading = $('#btn-loading');

        // Show loader
        btnText.hide();
        btnLoading.show();

        fetch(`{{ route('admin.attendance-export', [
            'from_date' => request('from_date'),
            'to_date' => request('to_date'),
            'date_range' => request('date_range'),
            'district_id' => request('district_id'),
            'block_id' => request('block_id'),
            'zone_id' => request('zone_id'),
            'role' => request('role'),
            'status' => request('status'),
            'search' => request('search')
        ]) }}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = "attendance.xlsx";
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Export failed:', error);
            alert("Export failed! Please try again.");
        })
        .finally(() => {
            btnText.show();
            btnLoading.hide();
        });
    });
});
function AddLeaveFromBtn(btn) {
    const providerId = btn.dataset.providerId;
    const name = btn.dataset.name;

    let leaveData = null;
    if (btn.dataset.leave) {
        try {
            leaveData = JSON.parse(btn.dataset.leave);
        } catch (e) {
            console.error('Invalid leave data', btn.dataset.leave);
        }
    }

    AddLeave(providerId, name, leaveData);
}


function AddLeave(employeeId, name,leaveData = null) {
    document.getElementById('EmpName').textContent = name;
    document.getElementById('provider_id').value = employeeId;
    
    document.getElementById('addLeaveModal').classList.add('show');
    document.getElementById('addLeaveForm').reset();

    const today = new Date().toISOString().split('T')[0];
    document.getElementById("today_date_display").value = today;
    const deleteBtn = document.getElementById('deleteLeaveBtn');
    const submitBtn = document.getElementById('submitLeaveBtn');


     if (leaveData) {
        document.getElementById('leave_id').value = leaveData.id;
        document.getElementById('type').value = leaveData.type;
        document.getElementById('reason').value = leaveData.reason ?? '';
        submitBtn.innerHTML = '<i class="ti-save"></i> Update';
        deleteBtn.style.display = 'inline-block';
        // document.getElementById("today_date_display").value = leaveData.start_date;
    } 
    else {
        document.getElementById('leave_id').value = '';
         submitBtn.innerHTML = '<i class="ti-save"></i> Save';
        deleteBtn.style.display = 'none';
    }
    document.getElementById('provider_id').value = employeeId;


}
function closeAddLeaveModal() {
    document.getElementById('addLeaveModal').classList.remove('show');
    document.getElementById('addLeaveForm').reset();
}
//  document.addEventListener("DOMContentLoaded", () => {
//         let today = new Date().toISOString().split("T")[0];
       
//         document.getElementById("today_date_display").value = today;
//     });
async function submitLeaveForm(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('submitLeaveBtn');
    const originalContent = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.classList.add('loading');
    submitBtn.innerHTML = '<i class="ti-loader"></i> Adding Leave...';
    submitBtn.disabled = true;
    
    try {
        const formData = new FormData(event.target);
        const today = new Date().toISOString().split("T")[0];

        formData.set("start_date", today);
        formData.set("end_date", today);
        formData.set("status", 'approved');
        const data = Object.fromEntries(formData.entries());

       
        const response = await fetch('/admin/provider/leave', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            // Success
            showLeaveToast('success', result.message || 'Leave added successfully!');
            closeAddLeaveModal();
            
            // Refresh the page or update the table
            if (typeof refreshLeaveData === 'function') {
                refreshLeaveData();
            } else {
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        } else {
            // Error from server
            throw new Error(result.message || 'Failed to add leave');
        }
        
    } catch (error) {
        showLeaveToast('error', error.message || 'Failed to add leave. Please try again.');
    } finally {
        // Reset button state
        submitBtn.classList.remove('loading');
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
    }
}
function deleteLeave() {
    const leaveId = document.getElementById('leave_id').value;
    if (!leaveId) return;

    if (!confirm('Are you sure you want to delete this record?')) return;

    fetch(`/admin/provider/del-leaves/${leaveId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeAddLeaveModal();
            location.reload();
        }
    });
}

function showLeaveToast(type, message) {
    const toast = document.getElementById('leaveToast');
    const icon = document.getElementById('leaveToastIcon');
    const messageEl = document.getElementById('leaveToastMessage');
    
    // Set message
    messageEl.textContent = message;
    
    // Set type
    toast.className = `terrasoft-toast ${type}`;
    
    // Set icon
    if (type === 'success') {
        icon.innerHTML = '<i class="ti-check"></i>';
    } else {
        icon.innerHTML = '<i class="ti-alert-circle"></i>';
    }
    
    // Show toast
    toast.classList.add('show');
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        closeLeaveToast();
    }, 5000);
}
document.addEventListener('click', function(event) {
    const modal = document.getElementById('addLeaveModal');
    const modalContainer = modal.querySelector('.terrasoft-modal-container');
    
    if (event.target === modal.querySelector('.terrasoft-modal-overlay')) {
        closeAddLeaveModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAddLeaveModal();
    }
});
</script>

@endsection