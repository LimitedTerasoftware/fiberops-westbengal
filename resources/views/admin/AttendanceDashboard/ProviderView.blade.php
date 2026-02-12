@extends('admin.layout.base')

@section('title', 'Team Member Details - ' . ($provider->first_name ?? 'Staff'))

@section('content')
 @php
    $roles = [
        1 => 'OFC',
        2 => 'FRT',
        5 => 'Patroller',
        3 => 'Zonal incharge',
        4 => 'District incharge'
    ];
@endphp
<div class="member-details-container">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="details-header">
            <div class="header-left">
                <!-- <button class="back-btn">
                    <i class="fa fa-arrow-left"></i>
                    <span>Team Member Details</span>
                </button> -->
                 <button class="back-btn">
                    <i class="fa fa-arrow-left"></i>
                    <span>Team Member Details</span>
                </button>
            </div>
            <div class="header-actions">
                <!-- <button class="action-btn btn-notification">
                    <i class="fa fa-bell"></i>
                    Send Notification
                </button>
                <button class="action-btn btn-approve">
                    <i class="fa fa-check"></i>
                    Approve Field
                </button> -->
                
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="main-content-grid">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Member Profile Card -->
                <div class="profile-cards">
                    <div class="profile-header">
                        <div class="profile-avatar">
                             <img 
                                src="{{ $provider->online_image 
                                    ? asset('uploads/attendance_images/' . $provider->online_image) 
                                    : 'https://cdn-icons-png.flaticon.com/512/847/847969.png' }}" 
                                alt="{{ $provider->first_name }} {{ $provider->last_name }}"
                                width="50" 
                                height="50">

                        </div>
                        <div class="profile-info">
                            <h2 class="profile-name">{{ $provider->first_name }} {{ $provider->last_name }}</h2>
                            <div class="profile-details">
                                <span class="profile-role">
                                     @if(isset($roles[$provider->type]))
                                            {{ $roles[$provider->type] }}
                                         
                                        @else
                                            Unknown
                                        @endif
                                </span>
                                <span class="profile-separator">•</span>
                                <span class="profile-emp-id">EMP ID: -</span>
                                <span class="profile-separator">•</span>
                                <span class="profile-zone">Zone: {{ $provider->zone_name ?  $provider->zone_name:'-' }}</span>
                            </div>
                            @if(auth()->user()->role != 'client')

                            <div class="profile-contact">
                                <span>Mobile: {{ $provider->mobile ? $provider->mobile : '-' }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="profile-status">
                            <div class="status-badges">
                                <span class="status-badge {{ $provider->rating == '5.00' ? 'status-good' : 'status-pending' }}">
                                    {{ $provider->rating == '5.00' ? 'Good' : 'Average' }}</span>
                                <span class="streak-badge">{{ $attendanceStats['streak'] ?  $attendanceStats['streak']:  0 }}-Day Streak</span>
                            </div>
                            <div class="status-details">
                                <span class="status-text">Total Requests: {{ $totalRequests }}</span>
                                <span class="status-time">{{$provider->status == 'Active' ? 'Online' : 'Offline'}}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date Range Filters -->
              <div class="date-filters-card">
                <h3 class="card-title">Date Range</h3>
                <div class="date-filter-buttons">
                    <a href="{{ route('admin.staff_details', ['id' => $provider->id, 'filter' => 'today']) }}"
                    class="date-btn {{ $filter == 'today' ? 'active' : '' }}">Today</a>

                    <a href="{{ route('admin.staff_details', ['id' => $provider->id, 'filter' => 'yesterday']) }}"
                    class="date-btn {{ $filter == 'yesterday' ? 'active' : '' }}">Yesterday</a>

                    <a href="{{ route('admin.staff_details', ['id' => $provider->id, 'filter' => 'last7']) }}"
                    class="date-btn {{ $filter == 'last7' ? 'active' : '' }}">Last 7 Days</a>
                   <form action="{{ route('admin.staff_details', ['id' => $provider->id]) }}" method="GET" style="display:inline;">
                      <input type="month" 
                            class="date-btn {{ $filter == 'monthyear' ? 'active' : '' }}" 
                            name="monthyear" 
                            value="{{ $monthyear ?? date('Y-m') }}" 
                            onchange="this.form.submit()">
                             <input type="hidden" name="filter" value="monthyear">
                    </form>
                    <!-- Custom Range (popup or form) -->
                   <!-- <form action="{{ route('admin.staff_details', ['id' => $provider->id]) }}" method="get" style="display:inline;">
                        <input type="hidden" name="filter" value="custom">
                         <input type="date" 
                            class="date-btn"
                            name="start_date"  
                            value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}" 
                            required>

                        <input type="date" 
                            class="date-btn"
                            name="end_date" 
                            value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}" 
                            required>

                        <button type="submit" class="date-btn {{ $filter == 'custom' ? 'active' : '' }}">
                            Custom Range
                        </button>
                    </form> -->

                </div>
            </div>


                <!-- Performance Scorecard -->
                <div class="scorecard-card">
                    <div class="scorecard-header">
                        <h3 class="card-title">
                            <i class="fa fa-trophy"></i>
                            Performance Scorecard
                        </h3>
                    </div>
                    <div class="scorecard-metrics">
                        <div class="metric-item">
                            <div class="metric-value">{{ $attendanceStats['present_days'] }}/{{ $attendanceStats['total_days'] }}</div>
                            <div class="metric-label">Attendance Days</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-value">{{ $attendanceStats['avg_duration'] }}</div>
                            <div class="metric-label">Avg Patrol Duration</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-value">{{ $attendanceStats['completion_rate'] }}%</div>
                            <div class="metric-label">Completion Rate</div>
                        </div>
                         
                    </div>
                    <div class="scorecard-details">
                        <div class="detail-item">
                            <span class="detail-icon"><i class="fa fa-list"></i></span>
                            <span class="detail-text">{{ $requestStats['total'] }} Total</span>
                        </div></br>
                        <div class="detail-item">
                            <span class="detail-icon text-success"><i class="fa fa-check-circle"></i></span>
                            <span class="detail-text">{{ $requestStats['completed'] }} Completed</span>
                        </div></br>
                        <div class="detail-item">
                            <span class="detail-icon text-warning"><i class="fa fa-times-circle"></i></span>
                            <span class="detail-text">{{ $requestStats['cancelled'] }} Cancelled</span>
                        </div></br>
                        <div class="detail-item">
                            <span class="detail-icon text-info"><i class="fa fa-hourglass-half"></i></span>
                            <span class="detail-text">{{ $requestStats['pending'] }} Pending</span>
                        </div>
                      
                    </div></br>
                      <div class="detail-item">
                            <span class="detail-icon text-warning"><i class="fa fa-star"></i></span>
                            <span class="detail-text">Rating: {{ number_format($provider->rating ?? 0, 1) }}/5</span>
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa fa-star{{ ($provider->rating ?? 0) >= $i ? '' : '-o' }}"></i>
                                @endfor
                            </div>
                        </div>

                </div>
            </div>

            <!-- Right Column -->
            <div class="right-column">
              
                <!-- Monthly Calendar -->
                <div class="calendar-card">
                    <div class="calendar-header">
                        <h3 class="card-title"><i class="fa fa-calendar-day"></i> Monthly Calendar View</h3>
                        @php
                            $prevMonth = $month == 1 ? 12 : $month - 1;
                            $prevYear = $month == 1 ? $year - 1 : $year;

                            $nextMonth = $month == 12 ? 1 : $month + 1;
                            $nextYear = $month == 12 ? $year + 1 : $year;
                        @endphp

                        <div class="calendar-nav">
                            <a href="{{ route('admin.staff_details', ['id' => $provider->id, 'month' => $prevMonth, 'year' => $prevYear]) }}" class="nav-btn">
                                <i class="fa fa-chevron-left"></i>
                            </a>
                            <span class="calendar-month">{{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</span>
                            <a href="{{ route('admin.staff_details', ['id' => $provider->id, 'month' => $nextMonth, 'year' => $nextYear]) }}" class="nav-btn">
                                <i class="fa fa-chevron-right"></i>
                            </a>
                        </div>

                       
                    </div>
                    <div class="calendar-grid">
                        <div class="calendar-weekdays">
                            <div class="weekday">Sun</div>
                            <div class="weekday">Mon</div>
                            <div class="weekday">Tue</div>
                            <div class="weekday">Wed</div>
                            <div class="weekday">Thu</div>
                            <div class="weekday">Fri</div>
                            <div class="weekday">Sat</div>
                        </div>
                        <div class="calendar-days">
                                @php
                                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                                $firstDayOfWeek = date('w', mktime(0, 0, 0, $month, 1, $year));
                                $prevMonth = $month == 1 ? 12 : $month - 1;
                                $prevYear = $month == 1 ? $year - 1 : $year;
                                $daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);
                            @endphp
                            
                            {{-- Previous month trailing days --}}
                            @for($i = $firstDayOfWeek - 1; $i >= 0; $i--)
                                <div class="calendar-day inactive">{{ $daysInPrevMonth - $i }}</div>
                            @endfor
                            @php $today = Carbon\Carbon::today(); @endphp

                            {{-- Current month days --}}
                            @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $currentDate = Carbon\Carbon::create($year, $month, $day);

                                    $dayStatus = 'present'; // default
                                     if ($currentDate->isFuture()) {
                                        $dayStatus = 'future';
                                    } else {
                                    if (isset($attendanceMap[$day])) {
                                        $attendance = $attendanceMap[$day];
                                        if ($attendance['present'] == false) {
                                            $dayStatus = 'absent';
                                        } elseif (isset($attendance['partial']) && $attendance['partial']) {
                                            $dayStatus = 'partial';
                                        }
                                    } else {
                                        $dayStatus = 'absent';
                                    }
                                   }
                                @endphp
                                <div class="calendar-day {{ $dayStatus }}">{{ $day }}</div>
                            @endfor
                            
                            {{-- Next month leading days to fill the grid --}}
                            @php
                                $totalCells = $firstDayOfWeek + $daysInMonth;
                                $remainingCells = 42 - $totalCells; // 6 rows × 7 days = 42 cells
                            @endphp
                            @for($day = 1; $day <= $remainingCells && $totalCells < 42; $day++)
                                <div class="calendar-day inactive">{{ $day }}</div>
                                @php $totalCells++; @endphp
                            @endfor
                            <!-- @php $today = Carbon\Carbon::now(); @endphp
                            <div class="calendar-day {{ $dayStatus }} {{ ($day == $today->day && $month == $today->month && $year == $today->year) ? 'today' : '' }}">
                                {{ $day }}
                            </div> -->

                           
                        </div>
                    </div>
                    <div class="calendar-legend">
                        <div class="legend-item">
                            <span class="legend-dot legend-present"></span>
                            <span>Present + Complete</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot legend-partial"></span>
                            <span>Partial Patrol</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot legend-absent"></span>
                            <span>Absent</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot legend-sync"></span>
                            <span>Sync Failure</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot legend-manual"></span>
                            <span>Manual Check-in</span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
          <!-- Daily Activity Details -->
                <div class="activity-card">
                    <div class="activity-header">
                        <h3 class="card-title"><i class="fa fa-clipboard-list"></i> Daily Activity Details</h3>
                        <button class="export-btn">
                            <i class="fa fa-download"></i>
                            Export
                        </button>
                    </div>
                    <div class="activity-table" style="max-height: 500px; overflow-y: auto;">

                         <table class="details-table">
                            <thead>
                                <tr>
                                    <th>DATE</th>
                                    <th>CHECK-IN</th>
                                    <th>CHECK-OUT</th>
                                    <th>DURATION</th>
                                    <th>GP COVERAGE</th>
                                    <!-- <th>Distance</th> -->
                                    <th>PHOTOS</th>
                                    <th>Status</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            @forelse($recentRequests as $request)
                                <tr class="activity-row">
                                    <td class="date-cell">{{ \Carbon\Carbon::parse($request->attendance_date)->format('M d, Y') }} 
                                     </td>
                                    <td class="date-cell">{{ $request->check_in ? \Carbon\Carbon::parse($request->check_in)->format('h:i A') : '-' }}</td>
                                    <td class="date-cell">{{ $request->onlinestatus != 'active' ? \Carbon\Carbon::parse($request->check_out)->format('h:i A') : '-' }}</td>
                                    <td class="duration-cell">
                                             <?php
                                        $startTime = Carbon\Carbon::parse($request->check_in);
                                        $currenttime = Carbon\Carbon::now();

                                        if ($request->onlinestatus == 'active') {
                                            $finishTime = $currenttime;
                                        } else {
                                            $finishTime = Carbon\Carbon::parse($request->check_out);
                                        }

                                        // Get total duration in seconds
                                        $totalDuration = $finishTime->diffInSeconds($startTime);

                                        // Convert to days, hours, minutes, seconds
                                        $days = floor($totalDuration / 86400);
                                        $hours = floor(($totalDuration % 86400) / 3600);
                                        $minutes = floor(($totalDuration % 3600) / 60);
                                        $seconds = $totalDuration % 60;

                                        // Format nicely
                                        if ($days > 0) {
                                            $duration = sprintf('%dd %02dh %02dm %02ds', $days, $hours, $minutes, $seconds);
                                        } else {
                                            $duration = sprintf('%02dh %02dm %02ds', $hours, $minutes, $seconds);
                                        }
                                        ?>

                                        
                                         {{ $duration? $duration  : '-'}}
                                    </td>
                                    <td class="coverage-cell">{{ $request->completed_tickets }}/{{ $request->total_tickets }}</td>
                                    <td class="coverage-cell">{{ number_format($request->total_distance,2) }} km</td>
                                    <td class="photos-cell">
                                    <span class="anomaly-badge anomaly-{{$request->onlinestatus}}">
                                            {{ is_array($request->images) ? count($request->images) : 0 }} Images
                                        </span>

                                                                                    
                                    </td>
                                     <td class="anomalies-cell">
                                           
                                            <span class="anomaly-badge anomaly-{{$request->onlinestatus}}">{{ $request->onlinestatus }}</span>
                                    </td>
                                            <td class="actions-cell">
                                             <a href="{{ url('admin/staff_livetrack/' . $request->provider_id) }}?date={{ $request->attendance_date }}" class="action-link">
                                             <i class="fa fa-location-arrow"></i> Live Track
                                             </a>
                                            <!-- <button class="action-link" onclick="viewRequest('{{ $request->attendance_id }}')">📍 View Details</button> -->
                                             <!-- <a href="{{ url('admin/staff_track/' . $request->provider_id) }}?date={{ $request->attendance_date }}" class="action-link">
                                              <i class="fa fa-eye"></i> View Details
                                             </a> -->
                                        </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No data found</td>
                                </tr>
                            @endforelse

                        </table>


                    </div>
                </div>

                <!-- Charts Section -->
                <!-- <div class="charts-section">
                    <div class="chart-card">
                        <h4 class="chart-title">GP Coverage Trend</h4>
                        <div class="chart-placeholder">
                            <div class="chart-visual">
                                <i class="fa fa-chart-line"></i>
                                <p>Coverage trend visualization</p>
                            </div>
                        </div>
                    </div>
                    <div class="chart-card">
                        <h4 class="chart-title"> Patrol Duration</h4>
                        <div class="chart-placeholder">
                            <div class="chart-visual">
                                <i class="fa fa-chart-bar"></i>
                                <p>Duration analysis</p>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Supervisor Remarks -->
                <!-- <div class="remarks-card">
                    <div class="remarks-header">
                        <h3 class="card-title"> Supervisor Remarks</h3>
                        <button class="add-remark-btn">
                            <i class="fa fa-plus"></i>
                            Add Remark
                        </button>
                    </div>
                    <div class="remarks-list">
                        <div class="remark-item">
                            <div class="remark-header">
                                <span class="remark-date">{{ \Carbon\Carbon::now()->format('M d, Y') }}</span>
                                <div class="remark-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star{{ ($provider->rating ?? 0) >= $i ? '' : '-o' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="remark-text">{{'No remarks available.' }}</p>
                            <div class="remark-footer">
                                <span class="remark-author">By: Admin</span>
                                <button class="remark-action">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div> -->

                     <!-- Export Options -->
                <!-- <div class="export-card">
                    <h3 class="card-title">Export Options</h3>
                    <div class="export-buttons">
                        <button class="export-option export-excel">
                            <i class="fa fa-file-excel"></i>
                            Export to Excel
                        </button>
                        <button class="export-option export-pdf">
                            <i class="fa fa-file-pdf"></i>
                            Export to PDF
                        </button>
                        <button class="export-option export-images">
                            <i class="fa fa-download"></i>
                            Bulk Download Images
                        </button>
                    </div>
                </div> -->
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
.calendar-day.future {
    background-color: #f2f2f2;
    color: #aaa;
    cursor: not-allowed;
    pointer-events: none;

}

.member-details-container {
    background-color: #f8fafc;
    min-height: 100vh;
    padding: 0.5rem 0;
}

.container-fluid {
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Header Section */
.details-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    /* margin-bottom: 10rem; */
}

.header-left {
    display: flex;
    align-items: center;
}

.back-btn {
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.125rem;
    font-weight: 500;
    color: #1e293b;
    cursor: pointer;
    padding: 0.5rem 0;
}

.back-btn:hover {
    color: #3b82f6;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
}

.action-btn {
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-notification {
    background: #3b82f6;
    color: white;
}

.btn-notification:hover {
    background: #2563eb;
}

.btn-approve {
    background: #10b981;
    color: white;
}

.btn-approve:hover {
    background: #059669;
}

/* Main Content Grid */
.main-content-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
    margin-bottom:20px;
}

/* Left Column */
.left-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Profile Card */
.profile-cards {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.profile-header {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info {
    flex: 1;
}

.profile-name {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
}

.profile-details {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    color: #64748b;
}

.profile-separator {
    color: #cbd5e1;
}

.profile-contact {
    font-size: 0.875rem;
    color: #64748b;
}

.profile-status {
    text-align: right;
}

.status-badges {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-end;
    margin-bottom: 0.5rem;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-good {
    background: #f0fdf4;
    color: #166534;
}
.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.streak-badge {
    background: #fef3c7;
    color: #92400e;
}

.status-details {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
    font-size: 0.875rem;
    color: #64748b;
}

/* Date Filters Card */
.date-filters-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.date-filter-buttons {
    display: flex;
    gap: 0.5rem;
}

.date-btn {
    padding: 0.5rem 1rem;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.date-btn.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.date-btn:hover:not(.active) {
    background: #f8fafc;
}

/* Scorecard Card */
.scorecard-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.scorecard-metrics {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.metric-large {
    text-align: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.metric-label {
    font-size: 0.875rem;
    color: #64748b;
}

.scorecard-details {
    display: flex;
    flex-direction: row;
    gap: 0.75rem;
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
}

.detail-icon {
    font-size: 1rem;
}

.detail-text {
    color: #64748b;
    flex: 1;
}

.rating-stars {
    display: flex;
    gap: 0.125rem;
}

.rating-stars .fa-star {
    color: #fbbf24;
}

.rating-stars .fa-star-o {
    color: #d1d5db;
}

/* Calendar Card */
.calendar-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.calendar-nav {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.nav-btn {
    background: transparent;
    border: 1px solid #e2e8f0;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.nav-btn:hover {
    background: #f8fafc;
}

.calendar-month {
    font-weight: 500;
    color: #1e293b;
}

.calendar-grid {
    /* margin-bottom: 6rem; */
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    margin-bottom: 0.5rem;
}

.weekday {
    padding: 0.5rem;
    text-align: center;
    font-size: 0.75rem;
    font-weight: 500;
    color: #64748b;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.calendar-day.inactive {
    color: #cbd5e1;
    background: #f8fafc;
}

.calendar-day.present {
    background: #10b981;
    color: white;
}

.calendar-day.partial {
    background: #f59e0b;
    color: white;
}

.calendar-day.absent {
    background: #ef4444;
    color: white;
}

.calendar-day.manual {
    background: #8b5cf6;
    color: white;
}

.calendar-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.75rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.legend-present { background: #10b981; }
.legend-partial { background: #f59e0b; }
.legend-absent { background: #ef4444; }
.legend-sync { background: #ef4444; }
.legend-manual { background: #8b5cf6; }

/* Activity Card */
.activity-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom:20px;
}

.activity-header {
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
    border-radius: 6px;
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

.activity-table {
    overflow-x: auto;
}

.details-table {
    width: 100%;
    border-collapse: collapse;
}

.details-table thead th {
    background: #f8fafc;
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    border-bottom: 1px solid #e2e8f0;
}

.details-table tbody td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.activity-row:hover {
    background: #f8fafc;
}

.date-cell {
    font-weight: 500;
    color: #1e293b;
}

.time-cell {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.time-value {
    font-weight: 500;
    color: #10b981;
}

.time-note {
    font-size: 0.75rem;
    color: #64748b;
}

.duration-cell {
    font-weight: 500;
    color: #1e293b;
}

.coverage-cell {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.coverage-value {
    font-weight: 500;
    color: #1e293b;
}

.coverage-status {
    font-size: 0.875rem;
    color: #64748b;
}

.photo-count {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    color: #64748b;
}

.anomaly-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.anomaly-offline {
    background: #fef3c7;
    color: #92400e;
}

.anomaly-active {
    background: #f0fdf4;
    color: #166534;
}

.action-link {
    background: transparent;
    border: none;
    color: #3b82f6;
    font-size: 0.875rem;
    cursor: pointer;
    text-decoration: none;
}

.action-link:hover {
    text-decoration: underline;
}

/* Charts Section */
.charts-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom:20px;
}

.chart-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chart-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 1rem 0;
}

.chart-placeholder {
    height: 150px;
    background: #f8fafc;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chart-visual {
    text-align: center;
    color: #64748b;
}

.chart-visual i {
    font-size: 2rem;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
}

.chart-visual p {
    margin: 0;
    font-size: 0.875rem;
}

/* Remarks Card */
.remarks-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom:20px;
}

.remarks-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.add-remark-btn {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.add-remark-btn:hover {
    background: #2563eb;
}

.remark-item {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.remark-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.remark-date {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1e293b;
}

.remark-rating {
    display: flex;
    gap: 0.125rem;
}

.remark-rating .fa-star {
    color: #fbbf24;
    font-size: 0.875rem;
}

.remark-rating .fa-star-o {
    color: #d1d5db;
    font-size: 0.875rem;
}

.remark-text {
    font-size: 0.875rem;
    color: #475569;
    line-height: 1.5;
    margin: 0 0 0.75rem 0;
}

.remark-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.remark-author {
    font-size: 0.75rem;
    color: #64748b;
}

.remark-action {
    background: transparent;
    border: none;
    color: #64748b;
    cursor: pointer;
    padding: 0.25rem;
}

.remark-action:hover {
    color: #3b82f6;
}

/* Export Card */
.export-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.export-buttons {
    display: flex;
    flex-direction: row;
    gap: 0.75rem;
}

.export-option {
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-align: left;
}

.export-excel {
    color: #059669;
    border-color: #d1fae5;
}

.export-excel:hover {
    background: #f0fdf4;
}

.export-pdf {
    color: #dc2626;
    border-color: #fecaca;
}

.export-pdf:hover {
    background: #fef2f2;
}

.export-images {
    color: #3b82f6;
    border-color: #dbeafe;
}

.export-images:hover {
    background: #eff6ff;
}

/* Right Column */
.right-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .main-content-grid {
        grid-template-columns: 1fr 350px;
    }
}

@media (max-width: 992px) {
    .main-content-grid {
        grid-template-columns: 1fr;
    }
    
    .charts-section {
        grid-template-columns: 1fr;
    }
    
    .scorecard-metrics {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .details-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .profile-header {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-status {
        text-align: center;
    }
    
    .status-badges {
        align-items: center;
    }
    
    .status-details {
        align-items: center;
    }
    
    .date-filter-buttons {
        flex-wrap: wrap;
    }
    
    .container-fluid {
        padding: 0 1rem;
    }
}

@media (max-width: 576px) {
    .member-details-container {
        padding: 1rem 0;
    }
    
    .profile-name {
        font-size: 1.25rem;
    }
    
    .export-buttons {
        gap: 0.5rem;
    }
    
    .export-option {
        padding: 0.625rem 0.875rem;
        font-size: 0.8125rem;
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
    // Date filter functionality
    $('.date-btn').on('click', function() {
        $('.date-btn').removeClass('active');
        $(this).addClass('active');
        
        const filterType = $(this).text();
        console.log('Date filter changed:', filterType);
        // Add your date filter logic here
    });

    // Back button functionality
    $('.back-btn').on('click', function() {
        window.history.back();
    });

    // Action button functionality
    $('.action-btn').on('click', function() {
        const action = $(this).find('span').text() || $(this).text();
        console.log('Action clicked:', action);
        // Add your action logic here
    });

    // Export functionality
    $('.export-btn, .export-option').on('click', function() {
        const exportType = $(this).text().trim();
        console.log('Export clicked:', exportType);
        // Add your export logic here
    });

    // Calendar navigation
    $('.nav-btn').on('click', function() {
        const direction = $(this).find('i').hasClass('fa-chevron-left') ? 'prev' : 'next';
        console.log('Calendar navigation:', direction);
        // Add your calendar navigation logic here
    });

    // Calendar day click
    $('.calendar-day').on('click', function() {
        if (!$(this).hasClass('inactive')) {
            const day = $(this).text();
            console.log('Calendar day clicked:', day);
            // Add your day selection logic here
        }
    });

    // Add remark functionality
    $('.add-remark-btn').on('click', function() {
        console.log('Add remark clicked');
        // Add your remark modal or form logic here
    });

    // Remark edit functionality
    $('.remark-action').on('click', function() {
        console.log('Edit remark clicked');
        // Add your edit remark logic here
    });
});
</script>
@endsection