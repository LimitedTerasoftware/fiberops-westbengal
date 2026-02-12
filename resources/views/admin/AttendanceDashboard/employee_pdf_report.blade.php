<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Performance Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background-color: #f8f9fa;
        }
        
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .header-content {
            width: 100%;
        }
        
        .header-content td {
            vertical-align: top;
        }
        
        .logo {
            width: 40px;
            height: 40px;
            background-color: #2563eb;
            border-radius: 8px;
            text-align: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
            padding-top: 8px;
        }
        
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .subtitle {
            color: #6b7280;
            font-size: 12px;
        }
        
        .meta-info {
            text-align: right;
            color: #6b7280;
            font-size: 11px;
        }
        
        .section {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .section-header {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
        }
        
        /* .section-header:before {
            content: "ℹ";
            color: #2563eb;
            margin-right: 8px;
        }
         */
        .info-grid {
            width: 100%;
        }
        
        .info-grid td {
            padding: 8px 0;
            vertical-align: top;
        }
        
        .info-label {
            color: #6b7280;
            font-size: 11px;
            font-weight: normal;
        }
        
        .info-value {
            color: #1f2937;
            font-weight: bold;
            padding-top: 2px;
        }
        
        .stats-grid {
            width: 100%;
        }
        
        .stats-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 0 5px;
            width: 18%;
        }
        
        .stats-card.blue {
            background-color: #dbeafe;
        }
        
        .stats-card.green {
            background-color: #dcfce7;
        }
        
        .stats-card.orange {
            background-color: #fed7aa;
        }
        
        .stats-card.purple {
            background-color: #e9d5ff;
        }
        
        .stats-number {
            font-size: 28px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .stats-label {
            color: #6b7280;
            font-size: 11px;
        }
        
        .performance-grid {
            width: 100%;
        }
        
        .perf-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin: 0 3px;
            width: 19%;
        }
        
        .perf-card.blue { background-color: #dbeafe; }
        .perf-card.green { background-color: #dcfce7; }
        .perf-card.yellow { background-color: #fef3c7; }
        .perf-card.red { background-color: #fee2e2; }
        .perf-card.indigo { background-color: #e0e7ff; }
        
        .perf-number {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .perf-label {
            color: #6b7280;
            font-size: 10px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .table th {
            background-color: #f8f9fa;
            color: #374151;
            font-weight: bold;
            padding: 12px 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        
        .table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 11px;
        }
        
        .status-present {
            background-color: #dcfce7;
            color: #166534;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
          .badge-warning {
            background-color: #f38181ff;
            color: #f31717ff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-active {
            background-color: #dcfce7;
            color: #166534;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .map-placeholder {
            width: 100%;
            height: 200px;
            background-color: #e5e7eb;
            border-radius: 8px;
            position: relative;
            margin-top: 15px;
        }
        
        .map-legend {
            position: absolute;
            top: 15px;
            left: 15px;
            background-color: white;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 10px;
        }
        
        .legend-item {
            margin-bottom: 5px;
        }
        
        .legend-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }
        
        .dot-green { background-color: #10b981; }
        .dot-orange { background-color: #f59e0b; }
        .dot-blue { background-color: #3b82f6; }
        
        .end-point {
            position: absolute;
            bottom: 30px;
            right: 30px;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 11px;
        }
        
        .footer-note {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            background-color: #f0fdf4;
            border-radius: 8px;
            font-size: 11px;
            color: #166534;
        }
        
      
         .map-container {
            text-align: center;
            margin-top: 20px;
        }

        .map-image {
            width: 100%;
            max-width: 600px;
            height: 300px;
            border: 1px solid #e2e8f0;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table class="header-content">
                <tr>
                    <td style="width: 50px;">
                            <img src="{{ Setting::get('site_logo', asset('logo-black.png')) }}" alt="Fiber ops" class="logo">
                    </td>
                    <td style="padding-left: 15px;">
                        <div class="title">Employee Performance Report</div>
                        <div class="subtitle">Comprehensive Analytics & Insights</div>
                    </td>
                    <td style="width: 200px;">
                        <div class="meta-info">
                            Generated: {{ $reportDate }}<br>
                            Period: {{ date('d M Y', strtotime($fromDate)) }} - {{ date('d M Y', strtotime($toDate)) }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Employee Information -->
        <div class="section">
            <div class="section-header">Employee Information</div>
            <table class="info-grid">
                <tr>
                    <td style="width: 25%;">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $provider->first_name }} {{ $provider->last_name }}</div>
                    </td>
                  
                    <td style="width: 25%;">
                        <div class="info-label">Role</div>
                        <div class="info-value">{{ $roleNames[$provider->type] ? $roleNames[$provider->type] :  'Unknown' }}</div>
                    </td>
                    <td style="width: 25%;">
                        <div class="info-label">Mobile</div>
                        <div class="info-value">{{ $provider->mobile }}</div>
                    </td>
                    <td style="width: 25%;">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $provider->email }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="info-label">District</div>
                        <div class="info-value">{{ $provider->district_name }}</div>
                    </td>
                    <td>
                        <div class="info-label">Block</div>
                        <div class="info-value">{{ $provider->block_name ? $provider->block_name  :'Not Assigned' }}</div>
                    </td>
                    <td>
                        <div class="info-label">Zone</div>
                        <div class="info-value">{{ $provider->zone_name ? $provider->zone_name :'Not Assigned' }}</div>
                    </td>
                    <td>
                        <div class="info-label">Version</div>
                        <div class="info-value"><span class="status-active">{{ $provider->version}}</span></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Attendance Statistics -->
         <!-- @php
            $ThisDate = \Carbon\Carbon::now()->format('F');
        @endphp -->
        <div class="section">
            <div class="section-header" style="margin-bottom: 20px;">Attendance Statistics </div>
            <table class="stats-grid">
                <tr>
                    <td class="stats-card blue">
                        <div class="stats-number">{{ $attendanceStats['present_days']}}</div>
                        <div class="stats-label">Present Days</div>
                    </td>
                    <td class="stats-card orange">
                        <div class="stats-number">{{ $attendanceStats['total_days'] }}</div>
                        <div class="stats-label">Total Days</div>
                    </td>
                    <td class="stats-card green">
                        <div class="stats-number">{{ $attendanceStats['attendance_percentage'] }}%</div>
                        <div class="stats-label">Attendance Rate</div>
                    </td>
                    
                    <td class="stats-card purple">
                        <div class="stats-number">{{ $attendanceStats['avg_duration'] }}</div>
                        <div class="stats-label">Average Duration</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Work Performance -->
        <div class="section">
            <div class="section-header" style="margin-bottom: 20px;"> Work Performance</div>
            <table class="performance-grid">
                <tr>
                    <td class="perf-card blue">
                        <div class="perf-number">{{ $requestStats['total'] }}</div>
                        <div class="perf-label">Total Requests</div>
                    </td>
                    <td class="perf-card green">
                        <div class="perf-number">{{ $requestStats['completed'] }}</div>
                        <div class="perf-label">Completed</div>
                    </td>
                    <td class="perf-card yellow">
                        <div class="perf-number">{{ $requestStats['pending'] }}</div>
                        <div class="perf-label">Pending</div>
                    </td>
                    <td class="perf-card red">
                        <div class="perf-number">{{ $requestStats['cancelled'] }}</div>
                        <div class="perf-label">Cancelled</div>
                    </td>
                    <td class="perf-card indigo">
                        <div class="perf-number">{{ $requestStats['completion_rate'] }}%</div>
                        <div class="perf-label">Success Rate</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Daily Activity Summary -->
        <div class="section">
            <div class="section-header"> Daily Activity Summary</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Tickets</th>
                        <th>Completed</th>
                        <th>Distance</th>
                        <th>Images</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentRequests as $request)

                    <tr>
                        <td>{{ date('d M Y', strtotime($request->attendance_date)) }}</td>
                        <td>{{ date('h:i A', strtotime($request->check_in)) }}</td>
                        <td>{{ $request->onlinestatus != 'active' ? date('h:i A', strtotime($request->check_out)): '-' }}</td>
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

                        <td>{{$duration}}</td>
                        <td><span class="{{ $request->onlinestatus == 'active' ? 'status-present' : 'badge-warning' }}"> {{ ucfirst($request->onlinestatus) }}</span></td>
                        <td>{{ $request->total_tickets }}</td>
                        <td>{{ $request->completed_tickets }}</td>
                        <td>{{ round($request->total_distance, 2) }} km</td>
                        <td>{{ $request->images }}</td>
                       
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Location Tracking -->
        <div class="section">
            <div class="section-header">Location Tracking & Movement Analysis</div>
                <div class="map-container">
                        @if($staticMapUrl)
                        <img src="{{ $staticMapUrl }}" alt="Employee Movement Map" class="map-image">
                        @else
                        <div class="map-placeholder">
                            <div class="placeholder-content">
                                <strong>Location Tracking Data Available</strong><br>
                                {{ count($trackingData) }} GPS points recorded<br>
                            </div>
                        </div>
                        @endif
             </div>
        </div>

        <!-- Footer -->
        <div class="footer-note">
            Shop No-8-2-293/82/A, 1107, Road Number 55, CBI Colony, Jubilee Hills, Hyderabad, Telangana 500033 | 04023547447        </div>
    </div>
</body>
</html>