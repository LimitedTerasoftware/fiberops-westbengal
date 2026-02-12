@extends('admin.layout.base')

@section('title', 'Route Map View - ')

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
<div class="route-track-container">
    <!-- Header Section -->
    <div class="route-header">
        <div class="header-left">
            <button class="back-btn" onclick="history.back()">
                <i class="fa fa-arrow-left"></i>
            </button>
            <div class="header-info">
                <h1 class="route-title">Route Map View</h1>
                <p class="route-subtitle">{{ $provider->first_name }} {{ $provider->last_name }} •  @if(isset($roles[$provider->type]))
                                            {{ $roles[$provider->type] }}
                                         
                                        @else
                                            Unknown
                                        @endif • Zone {{ $provider->zone_name ?  $provider->zone_name:'-' }}</p>
            </div>
        </div>
        <div class="header-actions">
            <button class="action-btn btn-export-pdf">
                <i class="fa fa-download"></i>
                Export PDF
            </button>
            <button class="action-btn btn-share">
                <i class="fa fa-share"></i>
                Share
            </button>
        </div>
    </div>

    <!-- Controls Section -->
    <div class="controls-section">
        <div class="controls-left">
            <div class="control-group">
                <i class="fa fa-calendar"></i>
                <input type="date" class="control-input" id="date-picker" value="{{ request('date', date('Y-m-d')) }}">
            </div>
            <!-- <div class="control-group">
                <i class="fa fa-clock"></i>
                <select class="control-select">
                    <option>All Day</option>
                    <option>Morning</option>
                    <option>Afternoon</option>
                    <option>Evening</option>
                </select>
            </div> -->
            <div class="control-group">
                <i class="fa fa-map-marker"></i>
                <select class="control-select">
                   <option value="">Select Zone</option>
                    @foreach($zonals as $zon)
                        <option value="{{ $zon->id }}" {{ request('zone_id') == $zon->id ? 'selected' : '' }}>
                            {{ $zon->Name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="controls-right">
            <div class="view-toggles">
                <button class="toggle-btn active" data-view="tracking">
                    <i class="fa fa-route"></i>
                    GPS Trail
                </button>
                <button class="toggle-btn" data-view="tickets">
                    <i class="fa fa-ticket-alt"></i>
                    Tickets
                </button>
                <label class="checkbox-label">
                    <input type="checkbox" id="show-markers" checked>
                    <span class="checkmark"></span>
                    Show Markers
                </label>
            </div>
        </div>
        <!-- <div class="controls-right">
            <div class="view-toggles">
                <button class="toggle-btn active">
                    <i class="fa fa-eye"></i>
                    AI View
                </button>
                <button class="toggle-btn">Raw GPS</button>
                <label class="checkbox-label">
                    <input type="checkbox" checked>
                    <span class="checkmark"></span>
                    Show Planned GPs
                </label>
            </div>
        </div> -->
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Left Sidebar -->
        <!-- <div class="left-sidebar"> -->
            <!-- Auto Insights -->
            <!-- <div class="insights-card">
                <h3 class="card-title">
                    <i class="fa fa-lightbulb"></i>
                    Auto Insights
                </h3>
                <div class="insight-item insight-success">
                    <i class="fa fa-check-circle"></i>
                    <span>Patrolled 5/6 GPs</span>
                </div>
                <div class="insight-item insight-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>Missed GP: GP-403</span>
                </div>
            </div> -->

            <!-- Statistics -->
            <!-- <div class="stats-card">
                <div class="stat-item">
                    <span class="stat-label">Distance walked</span>
                    <span class="stat-value">1.2 km</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Longest idle time</span>
                    <span class="stat-value stat-warning">18 mins</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Photos taken</span>
                    <span class="stat-value">4</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Weak signal zones</span>
                    <span class="stat-value stat-warning">2</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">GPS disabled</span>
                    <span class="stat-value stat-danger">1 instance</span>
                </div>
            </div> -->

            <!-- Legend -->
            <!-- <div class="legend-card">
                <h4 class="legend-title">Legend</h4>
                <div class="legend-items">
                    <div class="legend-item">
                        <span class="legend-dot legend-checkin"></span>
                        <span>Check-in</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot legend-checkout"></span>
                        <span>Check-out</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot legend-trail"></span>
                        <span>GPS Trail</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot legend-visited"></span>
                        <span>GP Visited</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot legend-idle"></span>
                        <span>Idle Zone</span>
                    </div>
                </div>
            </div> -->
        <!-- </div> -->

        <!-- Map Container -->
        <div class="map-container">
            <div id="map" style="height: 100%; width: 100%;"></div>

            <!-- <div class="map-placeholder">
                <div class="map-icon">
                    <i class="fa fa-map"></i>
                </div>
                <h3>Interactive Map View</h3>
                <p>Route visualization with GPS trails and checkpoints</p>
            </div> -->
            
           
        </div>

        <!-- Right Sidebar -->
        <div class="right-sidebar">
            <!-- Patrol Summary -->
            <div class="summary-card">
                <h3 class="card-title">
                    <i class="fa fa-clipboard-list"></i>
                    Patrol Summary
                </h3>
                
                <div class="patroller-profile">
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
                        <h4 class="profile-name">{{ $provider->first_name }} {{ $provider->last_name }}</h4>
                        <p class="profile-role"> 
                                       @if(isset($roles[$provider->type]))
                                            {{ $roles[$provider->type] }}
                                         
                                        @else
                                            Unknown
                                        @endif</p>
                        <p class="profile-zone">Zone {{ $provider->zone_name ?  $provider->zone_name:'-' }}</p>
                    </div>
                </div>
                @if($recentRequests->isNotEmpty())
                <div class="summary-details">
                    <div class="detail-row">
                        <span class="detail-label">Patrol Date</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($recentRequests[0]->attendance_date)->format('M d, Y') }} </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Check-in</span>
                        <span class="detail-value detail-success">{{ $recentRequests[0]->check_in ? \Carbon\Carbon::parse($recentRequests[0]->check_in)->format('h:i A') : '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Check-out</span>
                        <span class="detail-value detail-danger">{{ $recentRequests[0]->onlinestatus != 'active' ? \Carbon\Carbon::parse($recentRequests[0]->check_out)->format('h:i A') : '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Duration</span>
                        <span class="detail-value">
                             <?php
                                        $startTime = Carbon\Carbon::parse($recentRequests[0]->check_in);
                                        $currenttime = Carbon\Carbon::now();

                                        if ($recentRequests[0]->onlinestatus == 'active') {
                                            $finishTime = $currenttime;
                                        } else {
                                            $finishTime = Carbon\Carbon::parse($recentRequests[0]->check_out);
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
                          
                                    {{ $duration? $duration : '-'}}
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tickets Assigned</span>
                        <span class="detail-value">{{ $recentRequests[0]->total_tickets }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tickets Covered</span>
                        <span class="detail-value">{{$recentRequests[0]->completed_tickets }}</span>
                    </div>
                    
                    <div class="completion-section">
                        <div class="completion-header">
                            <span class="completion-label">Checklist Completion</span>
                            @php
                                $total = $recentRequests[0]->total_tickets;
                                $completed = $recentRequests[0]->completed_tickets;
                                $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
                            @endphp
                            <span class="completion-percentage">{{ $percentage }}%</span>
                        </div>
                        <div class="completion-bar">
                            <div class="completion-progress" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Distance Covered</span>
                        <span class="detail-value">{{ number_format($recentRequests[0]->total_distance,2) }} km</span>
                    </div>
                     <div class="detail-row">
                    <div id="tickets-list"></div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Images</span>
                        <span class="detail-value gallery">
                            @foreach($recentRequests[0]->images as $img)

                                {{-- Before Images --}}
                                @if($img->before_image != '')
                                    @php
                                        $beforedata = json_decode($img->before_image, true);
                                        if (!$beforedata) {
                                            $beforedata = explode(',', $img->before_image);
                                        }
                                    @endphp
                                    @foreach($beforedata as $beforeimage)
                                        <a href="{{ asset('/uploads/SubmitFiles/'.$beforeimage) }}" title="Before">
                                            <img src="{{ asset('/uploads/SubmitFiles/'.$beforeimage) }}" 
                                                alt="Before" style="width:80px;height:60px;">
                                        </a>
                                    @endforeach
                                @endif

                                {{-- After Images --}}
                                @if($img->after_image != '')
                                    @php
                                        $afterdata = json_decode($img->after_image, true);
                                        if (!$afterdata) {
                                            $afterdata = explode(',', $img->after_image);
                                        }
                                    @endphp
                                    @foreach($afterdata as $afterimage)
                                        <a href="{{ asset('/uploads/SubmitFiles/'.$afterimage) }}" title="After">
                                            <img src="{{ asset('/uploads/SubmitFiles/'.$afterimage) }}" 
                                                alt="After" style="width:80px;height:60px;">
                                        </a>
                                    @endforeach
                                @endif

                            @endforeach
                        </span>
                    </div>

                    <!-- <div class="detail-row">
                        <span class="detail-label">SLA Status</span>
                        <span class="detail-value detail-success">
                            <i class="fa fa-check-circle"></i>
                            OK
                        </span>
                    </div> -->
                </div>
                @else
                    <span class="detail-value">No Records</span>
                @endif

                <!-- AI Tags -->
                <div class="ai-tags-section">
                    <h4 class="tags-title">AI Tags</h4>
                    <div class="tags-container">
                        <span class="ai-tag tag-warning">Long Idle</span>
                        <span class="ai-tag tag-danger">Missed GP</span>
                    </div>
                </div>

                <!-- Supervisor Feedback -->
                <div class="feedback-section">
                    <h4 class="feedback-title">Supervisor Feedback</h4>
                    <textarea class="feedback-textarea" placeholder="Add your remarks..."></textarea>
                    <button class="feedback-save-btn">Save</button>
                </div>
                  <div class="legend-card">
                <h4 class="legend-title">Map Legend</h4>
                <div class="legend-items">
                    <div class="legend-item">
                        <span class="legend-dot legend-start"></span>
                        <span>Start Point</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot legend-end"></span>
                        <span>End Point</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot legend-trail"></span>
                        <span>GPS Trail</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot legend-completed"></span>
                        <span>Completed</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot legend-pending"></span>
                        <span>Pending</span>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" />

<style>
/* Reset and Base Styles */
* {
    box-sizing: border-box;
}

/* Container styling */
#tickets-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 10px;
  max-height: 70vh; /* adjust as needed */
  overflow-y: auto;
  scrollbar-width: thin; /* for Firefox */
  scrollbar-color: #ccc #f9f9f9; /* for Firefox */
}

/* Chrome, Edge, Safari scrollbar styling */
#tickets-list::-webkit-scrollbar {
  width: 8px;
}

#tickets-list::-webkit-scrollbar-track {
  background: #f9f9f9;
  border-radius: 10px;
}

#tickets-list::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 10px;
}

#tickets-list::-webkit-scrollbar-thumb:hover {
  background-color: #999;
}


/* Ticket card */
.ticket-item {
  background: #fff;
  border: 1px solid #e2e2e2;
  border-radius: 10px;
  padding: 15px 18px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease-in-out;
  cursor: pointer;
}

/* Hover effect */
.ticket-item:hover {
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

/* Header layout */
.ticket-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

/* Ticket ID */
.ticket-id {
  font-weight: 600;
  color: #333;
}

/* Ticket status styles */
.ticket-status {
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 500;
  text-transform: capitalize;
}

.status-open {
  background-color: #e8f5e9;
  color: #2e7d32;
}

.status-pending {
  background-color: #fff3cd;
  color: #856404;
}

.status-closed {
  background-color: #fdecea;
  color: #c62828;
}

/* Ticket details */
.ticket-details {
  font-size: 14px;
  color: #555;
  line-height: 1.5;
}

.ticket-details div {
  margin-bottom: 4px;
}

/* No data message */
.no-data {
  text-align: center;
  color: #777;
  font-style: italic;
  padding: 20px;
}

/* Selected ticket */
.ticket-item.selected {
  border-color: #007bff;
  background-color: #f0f7ff;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.15);
}

.route-track-container {
    background-color: #f8fafc;
    min-height: 100vh;
    padding: 1.5rem;
}

/* Header Section */
.route-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.back-btn {
    background: #f1f5f9;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.back-btn:hover {
    background: #e2e8f0;
}

.header-info {
    flex: 1;
}

.route-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 0.25rem 0;
}

.route-subtitle {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0;
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

.btn-export-pdf {
    background: #3b82f6;
    color: white;
}

.btn-export-pdf:hover {
    background: #2563eb;
}

.btn-share {
    background: #10b981;
    color: white;
}

.btn-share:hover {
    background: #059669;
}

/* Controls Section */
.controls-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    background: white;
    padding: 1.25rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.controls-left {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.control-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.control-group i {
    color: #64748b;
    font-size: 0.875rem;
}

.control-input,
.control-select {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.875rem;
    background: white;
    color: #1e293b;
}

.control-input:focus,
.control-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.controls-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.view-toggles {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.toggle-btn {
    padding: 0.5rem 1rem;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.toggle-btn.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.toggle-btn:hover:not(.active) {
    background: #f8fafc;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #64748b;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    margin: 0;
}

/* Main Content Layout */
.main-content {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 1.5rem;
    min-height: calc(100vh - 200px);
}

/* Left Sidebar */
.left-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.insights-card,
.stats-card,
.legend-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-title i {
    color: #3b82f6;
}

/* Insights */
.insight-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.insight-success {
    background: #f0fdf4;
    color: #166534;
}

.insight-warning {
    background: #fefce8;
    color: #ca8a04;
}

.insight-item i {
    font-size: 1rem;
}

/* Statistics */
.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-label {
    font-size: 0.875rem;
    color: #64748b;
}

.stat-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
}

.stat-warning {
    color: #f59e0b;
}

.stat-danger {
    color: #ef4444;
}

/* Legend */
.legend-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 1rem 0;
}

.legend-items {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
    color: #64748b;
}

.legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.legend-checkin { background: #10b981; }
.legend-checkout { background: #ef4444; }
.legend-trail { background: #3b82f6; }
.legend-visited { background: #f59e0b; }
.legend-idle { background: #8b5cf6; }

/* Map Container */
.map-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.map-placeholder {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    color: #64748b;
    text-align: center;
}

.map-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #cbd5e1;
}

.map-placeholder h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #475569;
    margin: 0 0 0.5rem 0;
}

.map-placeholder p {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0;
}

.map-controls {
    position: absolute;
    bottom: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    padding: 0.75rem 1.25rem;
    border-radius: 25px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.map-control-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: #3b82f6;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.map-control-btn:hover {
    background: #2563eb;
}

.speed-control {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #64748b;
}

.speed-select {
    padding: 0.25rem 0.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 0.875rem;
}

.time-display {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1e293b;
}

/* Right Sidebar */
.right-sidebar {
    display: flex;
    flex-direction: column;
}

.summary-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    height: 100%;
}

.patroller-profile {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.profile-avatar {
    width: 48px;
    height: 48px;
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
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 0.25rem 0;
}

.profile-role {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0 0 0.125rem 0;
}

.profile-zone {
    font-size: 0.75rem;
    color: #94a3b8;
    margin: 0;
}

/* Summary Details */
.summary-details {
    margin-bottom: 1.5rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f8fafc;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-size: 0.875rem;
    color: #64748b;
}

.detail-value {
    display: flex;
    gap: 10px;             
    overflow-x: auto;      
    padding-bottom: 8px;    }

.detail-value a {
    flex: 0 0 auto;        
}

.detail-value img {
    width: 100px;
    height: 80px;
    border-radius: 6px;
    border: 1px solid #ccc;
    object-fit: cover;
}

.detail-success {
    color: #10b981;
}

.detail-danger {
    color: #ef4444;
}

/* Completion Section */
.completion-section {
    margin: 1rem 0;
    padding: 1rem 0;
    border-top: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
}

.completion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.completion-label {
    font-size: 0.875rem;
    color: #64748b;
}

.completion-percentage {
    font-size: 0.875rem;
    font-weight: 600;
    color: #3b82f6;
}

.completion-bar {
    height: 8px;
    background: #f1f5f9;
    border-radius: 4px;
    overflow: hidden;
}

.completion-progress {
    height: 100%;
    background: #3b82f6;
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* AI Tags */
.ai-tags-section {
    margin-bottom: 1.5rem;
}

.tags-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 0.75rem 0;
}

.tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.ai-tag {
    padding: 0.375rem 0.75rem;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 500;
}

.tag-warning {
    background: #fef3c7;
    color: #92400e;
}

.tag-danger {
    background: #fee2e2;
    color: #dc2626;
}

/* Feedback Section */
.feedback-section {
    margin-top: auto;
}

.feedback-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 0.75rem 0;
}

.feedback-textarea {
    width: 100%;
    min-height: 80px;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.875rem;
    resize: vertical;
    margin-bottom: 0.75rem;
}

.feedback-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.feedback-save-btn {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.feedback-save-btn:hover {
    background: #2563eb;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .main-content {
        grid-template-columns: 280px 1fr 300px;
    }
}

@media (max-width: 992px) {
    .main-content {
        grid-template-columns: 1fr;
        grid-template-rows: auto 1fr auto;
    }
    
    .left-sidebar {
        order: 2;
        flex-direction: row;
        overflow-x: auto;
    }
    
    .map-container {
        order: 1;
        height: 400px;
    }
    
    .right-sidebar {
        order: 3;
    }
}

@media (max-width: 768px) {
    .route-track-container {
        padding: 1rem;
    }
    
    .route-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .header-actions {
        justify-content: center;
    }
    
    .controls-section {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .controls-left {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .view-toggles {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .left-sidebar {
        flex-direction: column;
    }
    
    .main-content {
        grid-template-columns: 1fr;
        height: auto;
    }
    
    .map-container {
        height: 300px;
    }
}

@media (max-width: 576px) {
    .route-title {
        font-size: 1.25rem;
    }
    
    .controls-left {
        gap: 1rem;
    }
    
    .control-group {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .action-btn {
        padding: 0.625rem 1rem;
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
    // Back button functionality
    $('.back-btn').on('click', function() {
        window.history.back();
    });

    // Export PDF functionality
    $('.btn-export-pdf').on('click', function() {
        // Add your PDF export logic here
        console.log('Export PDF clicked');
    });

    // Share functionality
    $('.btn-share').on('click', function() {
        // Add your share logic here
        console.log('Share clicked');
    });

    // Toggle button functionality
    $('.toggle-btn').on('click', function() {
        $('.toggle-btn').removeClass('active');
        $(this).addClass('active');
    });

    // Control change handlers
    $('.control-input, .control-select').on('change', function() {
        // Add your filter logic here
        console.log('Control changed:', $(this).val());
    });

    // Feedback save functionality
    $('.feedback-save-btn').on('click', function() {
        const feedback = $('.feedback-textarea').val();
        // Add your save logic here
        console.log('Feedback saved:', feedback);
    });

    // Map control functionality
    $('.map-control-btn').on('click', function() {
        // Add your map control logic here
        console.log('Map control clicked');
    });
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&callback=initMap" async defer></script>
<script>
let map;
let markers = [];
let polylines = [];
let trackingData = [];
let userRequests = [];
let animationInterval;
let currentStep = 0;

// Initialize map
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: { lat: 20.8444, lng: 85.1511 },
        styles: [
            {
                featureType: 'all',
                elementType: 'geometry.fill',
                stylers: [{ color: '#f8f9fc' }]
            },
            {
                featureType: 'water',
                elementType: 'geometry',
                stylers: [{ color: '#e3f2fd' }]
            }
        ]
    });

    loadMapData();
}

// Load map data
function loadMapData() {
    const date = document.getElementById('date-picker').value;
    const providerId = {{ $provider->id }};
    userRequests= {!! json_encode($requests) !!}; 
    trackingData={!! json_encode($trackingData) !!};
    displayTicketRoutes();
    displayTrackingData();
    loadTicketsList();
}

// Display ticket routes
function displayTicketRoutes() {
    clearMap();
    
    userRequests.forEach((request, index) => {
        if (request.s_latitude && request.s_longitude && request.d_latitude && request.d_longitude) {
            const startPoint = new google.maps.LatLng(request.s_latitude, request.s_longitude);
            const endPoint = new google.maps.LatLng(request.d_latitude, request.d_longitude);
            
            // Start marker
            const startMarker = new google.maps.Marker({
                position: startPoint,
                map: map,
                icon: {
                    url: '/asset/img/marker-start.png',
                    scaledSize: new google.maps.Size(30, 30)
                },
                title: `Start: ${request.booking_id}`
            });
            
            // End marker
            const endMarker = new google.maps.Marker({
                position: endPoint,
                map: map,
                icon: {
                    url: '/asset/img/marker-end.png',
                    scaledSize: new google.maps.Size(30, 30)
                },
                title: `End: ${request.booking_id}`
            });
            
            markers.push(startMarker, endMarker);
            
            // Route line
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer({
                suppressMarkers: true,
                preserveViewport: true,
                polylineOptions: {
                    strokeColor: request.status === 'COMPLETED' ? '#10b981' : '#f59e0b',
                    strokeWeight: 3,
                    strokeOpacity: 0.8
                }
            });
            
            directionsRenderer.setMap(map);
            
            directionsService.route({
                origin: startPoint,
                destination: endPoint,
                travelMode: google.maps.TravelMode.DRIVING
            }, (result, status) => {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(result);
                }
            });
            
            // Info windows
            const startInfoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px;">
                        <h6>${request.booking_id}</h6>
                        <p><strong>Category:</strong> ${request.category || 'N/A'}</p>
                        <p><strong>GP:</strong> ${request.gpname || 'N/A'}</p>
                        <p><strong>Status:</strong> ${request.status}</p>
                        <p><strong>Start:</strong> ${request.s_address || 'N/A'}</p>
                    </div>
                `
            });
            
            const endInfoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px;">
                        <h6>${request.booking_id}</h6>
                        <p><strong>Destination:</strong> ${request.d_address || 'N/A'}</p>
                        <p><strong>Status:</strong> ${request.status}</p>
                    </div>
                `
            });
            
            startMarker.addListener('click', () => {
                startInfoWindow.open(map, startMarker);
            });
            
            endMarker.addListener('click', () => {
                endInfoWindow.open(map, endMarker);
            });
        }
    });
    
    // Fit map to show all markers
    if (markers.length > 0) {
        const bounds = new google.maps.LatLngBounds();
        markers.forEach(marker => bounds.extend(marker.getPosition()));
        map.fitBounds(bounds);
    }
}

// Display tracking data with directions
function displayTrackingData() {
    if (trackingData.length < 2) return; // need at least 2 points
    
    const startPoint = new google.maps.LatLng(
        parseFloat(trackingData[0].latitude),
        parseFloat(trackingData[0].longitude)
    );
    const endPoint = new google.maps.LatLng(
        parseFloat(trackingData[trackingData.length - 1].latitude),
        parseFloat(trackingData[trackingData.length - 1].longitude)
    );

    // Add start and end markers
    const startMarker = new google.maps.Marker({
        position: startPoint,
        map: map,
        icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png",
        title: "Tracking Start"
    });
    const endMarker = new google.maps.Marker({
        position: endPoint,
        map: map,
        icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
        title: "Tracking End"
    });
    markers.push(startMarker, endMarker);

    // Directions service + renderer
    const directionsService = new google.maps.DirectionsService();
    const directionsRenderer = new google.maps.DirectionsRenderer({
        suppressMarkers: true,
        preserveViewport: true,
        polylineOptions: {
            strokeColor: '#3b82f6',
            strokeWeight: 4,
            strokeOpacity: 0.9
        }
    });
    directionsRenderer.setMap(map);

    // Waypoints (optional) → you can feed intermediate tracking points
    const waypoints = trackingData.slice(1, trackingData.length - 1).map(point => ({
        location: new google.maps.LatLng(parseFloat(point.latitude), parseFloat(point.longitude)),
        stopover: false
    }));

    directionsService.route({
        origin: startPoint,
        destination: endPoint,
        waypoints: waypoints.length > 20 ? [] : waypoints, // Google allows max 23 waypoints
        travelMode: google.maps.TravelMode.DRIVING
    }, (result, status) => {
        if (status === google.maps.DirectionsStatus.OK) {
            directionsRenderer.setDirections(result);
        } else {
            console.warn("Directions failed, fallback to raw polyline:", status);
            
            // fallback → draw raw tracking path
            const trackingPath = trackingData.map(point => ({
                lat: parseFloat(point.latitude),
                lng: parseFloat(point.longitude)
            }));
            const trackingPolyline = new google.maps.Polyline({
                path: trackingPath,
                geodesic: true,
                strokeColor: '#3b82f6',
                strokeOpacity: 1.0,
                strokeWeight: 4
            });
            trackingPolyline.setMap(map);
            polylines.push(trackingPolyline);
        }
    });
}


// Load tickets list
function loadTicketsList() {
    const ticketsList = document.getElementById('tickets-list');
    ticketsList.innerHTML = '';
    
    if (userRequests.length === 0) {
        ticketsList.innerHTML = '<p class="no-data">No tickets found for selected date</p>';
        return;
    }
    
    userRequests.forEach(request => {
        const ticketItem = document.createElement('div');
        ticketItem.className = 'ticket-item';
        ticketItem.innerHTML = `
            <div class="ticket-header">
                <span class="ticket-id">${request.booking_id}</span>
                <span class="ticket-status status-${request.status.toLowerCase()}">${request.status}</span>
            </div>
            <div class="ticket-details">
                <div><strong>Reason:</strong> ${request.downreason || 'N/A'}</div>
                <div><strong>GP:</strong> ${request.gpname || 'N/A'}</div>
                <div><strong>Time:</strong> ${request.created_at ? new Date(request.created_at).toLocaleTimeString() : 'N/A'}</div>
            </div>
        `;
        
        ticketItem.addEventListener('click', () => {
            // Add highlight
            document.querySelectorAll('.ticket-item').forEach(item => item.classList.remove('selected'));
            ticketItem.classList.add('selected');
            
            // Redirect to Laravel route
            window.location.href = `/admin/requests/${request.id}`;
        });
        
        ticketsList.appendChild(ticketItem);
    });
}


// Focus on specific ticket
function focusOnTicket(request) {
    if (request.s_latitude && request.s_longitude) {
        const center = new google.maps.LatLng(request.s_latitude, request.s_longitude);
        map.setCenter(center);
        map.setZoom(15);
    }
}

// Clear map
function clearMap() {
    markers.forEach(marker => marker.setMap(null));
    polylines.forEach(polyline => polyline.setMap(null));
    markers = [];
    polylines = [];
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Date picker change
    document.getElementById('date-picker').addEventListener('change', function() {
        const newDate = this.value;
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('date', newDate);
        window.location.href = currentUrl.toString();
    });
    
    // Toggle buttons
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.dataset.view;
            if (view === 'tracking') {
                displayTrackingData();
            } else if (view === 'tickets') {
                displayTicketRoutes();
            }
        });
    });
    
    // Show/hide markers
    document.getElementById('show-markers').addEventListener('change', function() {
        const showMarkers = this.checked;
        markers.forEach(marker => {
            marker.setVisible(showMarkers);
        });
    });
    
});

 $('.gallery').magnificPopup({
        delegate: 'a', 
        type: 'image',
        gallery: {
            enabled: true
        },
        zoom: {
            enabled: true, 
            duration: 300 
        }
    });



// Initialize map when page loads
google.maps.event.addDomListener(window, 'load', initMap);
</script>

@endsection