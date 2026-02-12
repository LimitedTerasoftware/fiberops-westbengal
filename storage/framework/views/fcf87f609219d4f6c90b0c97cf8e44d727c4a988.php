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
        
        .section-header:before {
            content: "ℹ";
            color: #2563eb;
            margin-right: 8px;
        }
        
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
        
        .footer-note:before {
            content: "✓";
            color: #10b981;
            margin-right: 8px;
            font-weight: bold;
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
                        <div class="logo">📊</div>
                    </td>
                    <td style="padding-left: 15px;">
                        <div class="title">Employee Performance Report</div>
                        <div class="subtitle">Comprehensive Analytics & Insights</div>
                    </td>
                    <td style="width: 200px;">
                        <div class="meta-info">
                            Generated: Jan 15, 2024 10:30 AM<br>
                            Period: Jan 1 - Jan 15, 2024
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
                        <div class="info-value">John Smith</div>
                    </td>
                    <td style="width: 25%;">
                        <div class="info-label">Employee ID</div>
                        <div class="info-value">EMP-2024-001</div>
                    </td>
                    <td style="width: 25%;">
                        <div class="info-label">Role</div>
                        <div class="info-value">Field Agent</div>
                    </td>
                    <td style="width: 25%;">
                        <div class="info-label">Mobile</div>
                        <div class="info-value">+1 (555) 123-4567</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="info-label">District</div>
                        <div class="info-value">Central District</div>
                    </td>
                    <td>
                        <div class="info-label">Block</div>
                        <div class="info-value">Block A-1</div>
                    </td>
                    <td>
                        <div class="info-label">Zone</div>
                        <div class="info-value">North Zone</div>
                    </td>
                    <td>
                        <div class="info-label">Status</div>
                        <div class="info-value"><span class="status-active">Active</span></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Attendance Statistics -->
        <div class="section">
            <div class="section-header" style="margin-bottom: 20px;">📅 Attendance Statistics</div>
            <table class="stats-grid">
                <tr>
                    <td class="stats-card blue">
                        <div class="stats-number">14</div>
                        <div class="stats-label">Present Days</div>
                    </td>
                    <td class="stats-card green">
                        <div class="stats-number">93.3%</div>
                        <div class="stats-label">Attendance Rate</div>
                    </td>
                    <td class="stats-card orange">
                        <div class="stats-number">7</div>
                        <div class="stats-label">Current Streak</div>
                    </td>
                    <td class="stats-card purple">
                        <div class="stats-number">8.2h</div>
                        <div class="stats-label">Average Duration</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Work Performance -->
        <div class="section">
            <div class="section-header" style="margin-bottom: 20px;">📈 Work Performance</div>
            <table class="performance-grid">
                <tr>
                    <td class="perf-card blue">
                        <div class="perf-number">142</div>
                        <div class="perf-label">Total Requests</div>
                    </td>
                    <td class="perf-card green">
                        <div class="perf-number">128</div>
                        <div class="perf-label">Completed</div>
                    </td>
                    <td class="perf-card yellow">
                        <div class="perf-number">12</div>
                        <div class="perf-label">Pending</div>
                    </td>
                    <td class="perf-card red">
                        <div class="perf-number">2</div>
                        <div class="perf-label">Cancelled</div>
                    </td>
                    <td class="perf-card indigo">
                        <div class="perf-number">90.1%</div>
                        <div class="perf-label">Success Rate</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Daily Activity Summary -->
        <div class="section">
            <div class="section-header">📋 Daily Activity Summary</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Tickets</th>
                        <th>Distance</th>
                        <th>Images</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Jan 15, 2024</td>
                        <td>09:15 AM</td>
                        <td>05:45 PM</td>
                        <td><span class="status-present">Present</span></td>
                        <td>12</td>
                        <td>45.2 km</td>
                        <td>24</td>
                    </tr>
                    <tr>
                        <td>Jan 14, 2024</td>
                        <td>09:00 AM</td>
                        <td>06:00 PM</td>
                        <td><span class="status-present">Present</span></td>
                        <td>8</td>
                        <td>38.7 km</td>
                        <td>16</td>
                    </tr>
                    <tr>
                        <td>Jan 13, 2024</td>
                        <td>08:45 AM</td>
                        <td>05:30 PM</td>
                        <td><span class="status-present">Present</span></td>
                        <td>15</td>
                        <td>52.1 km</td>
                        <td>30</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Location Tracking -->
        <div class="section">
            <div class="section-header">📍 Location Tracking & Movement Analysis</div>
            
            <!-- Tracking Summary -->
            <div style="background-color: #e0f2fe; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <div style="color: #0277bd; font-weight: bold; font-size: 12px; margin-bottom: 15px;">
                    📍 Tracking Summary: 709 location points recorded
                </div>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 33.33%; padding: 8px; vertical-align: top;">
                            <div style="background-color: white; border-radius: 6px; padding: 12px; margin-bottom: 8px;">
                                <div style="font-weight: bold; font-size: 11px; color: #1f2937; margin-bottom: 4px;">01 Sep, 03:29 PM</div>
                                <div style="font-size: 10px; color: #6b7280;">Location: 22.7389997, 88.5584534</div>
                                <div style="font-size: 9px; color: #9ca3af;">22.7390, 88.5585</div>
                            </div>
                            <div style="background-color: white; border-radius: 6px; padding: 12px; margin-bottom: 8px;">
                                <div style="font-weight: bold; font-size: 11px; color: #1f2937; margin-bottom: 4px;">01 Sep, 04:06 PM</div>
                                <div style="font-size: 10px; color: #6b7280;">Location: 22.7396642, 88.5583638</div>
                                <div style="font-size: 9px; color: #9ca3af;">22.7396, 88.5584</div>
                            </div>
                            <div style="background-color: white; border-radius: 6px; padding: 12px;">
                                <div style="font-weight: bold; font-size: 11px; color: #1f2937; margin-bottom: 4px;">01 Sep, 04:47 PM</div>
                                <div style="font-size: 10px; color: #6b7280;">Location: 22.7098812, 88.5343192</div>
                                <div style="font-size: 9px; color: #9ca3af;">22.7099, 88.5343</div>
                            </div>
                        </td>
                        <td style="width: 33.33%; padding: 8px; vertical-align: top;">
                            <div style="background-color: white; border-radius: 6px; padding: 12px; margin-bottom: 8px;">
                                <div style="font-weight: bold; font-size: 11px; color: #1f2937; margin-bottom: 4px;">01 Sep, 03:39 PM</div>
                                <div style="font-size: 10px; color: #6b7280;">Location: 22.7389997, 88.5584534</div>
                                <div style="font-size: 9px; color: #9ca3af;">22.7390, 88.5585</div>
                            </div>
                            <div style="background-color: white; border-radius: 6px; padding: 12px; margin-bottom: 8px;">
                                <div style="font-weight: bold; font-size: 11px; color: #1f2937; margin-bottom: 4px;">01 Sep, 04:21 PM</div>
                                <div style="font-size: 10px; color: #6b7280;">Location: 22.7395662, 88.5583241</div>
                                <div style="font-size: 9px; color: #9ca3af;">22.7396, 88.5583</div>
                            </div>
                            <div style="background-color: white; border-radius: 6px; padding: 12px;">
                                <div style="font-weight: bold; font-size: 11px; color: #1f2937; margin-bottom: 4px;">01 Sep, 04:57 PM</div>
                                <div style="font-size: 10px; color: #6b7280;">Location: 22.7096639, 88.5347718</div>
                                <div style="font-size: 9px; color: #9ca3af;">22.7097, 88.5348</div>
                            </div>
                        </td>
                        <td style="width: 33.33%; padding: 8px; vertical-align: top;">
                            <div style="background-color: white; border-radius: 6px; padding: 12px; margin-bottom: 8px;">
                                <div style="font-weight: bold; font-size: 11px; color: #1f2937; margin-bottom: 4px;">01 Sep, 03:49 PM</div>
                                <div style="font-size: 10px; color: #6b7280;">Location: 22.7389997, 88.5584534</div>
                                <div style="font-size: 9px; color: #9ca3af;">22.7390, 88.5585</div>
                            </div>
                            <div style="background-color: white; border-radius: 6px; padding: 12px; margin-bottom: 8px;">
                                <div style="font-weight: bold; font-size: 11px; color: #1f2937; margin-bottom: 4px;">01 Sep, 04:33 PM</div>
                                <div style="font-size: 10px; color: #6b7280;">Location: 22.7395987, 88.5591213</div>
                                <div style="font-size: 9px; color: #9ca3af;">22.7396, 88.5591</div>
                            </div>
                            <div style="background-color: white; border-radius: 6px; padding: 12px;">
                                <div style="font-weight: bold; font-size: 11px; color: #1f2937; margin-bottom: 4px;">01 Sep, 05:10 PM</div>
                                <div style="font-size: 10px; color: #6b7280;">Location: 22.7096622, 88.5580435</div>
                                <div style="font-size: 9px; color: #9ca3af;">22.7097, 88.5580</div>
                            </div>
                        </td>
                    </tr>
                </table>
                
                <div style="text-align: center; margin-top: 15px; font-style: italic; color: #6b7280; font-size: 11px;">
                    and 700 additional tracking points
                </div>
            </div>
            
            <div class="map-placeholder">
                <div class="map-legend">
                    <div class="legend-item">
                        <span class="legend-dot dot-green"></span>Start Point
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot dot-orange"></span>End Point
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot dot-blue"></span>Movement Path
                    </div>
                </div>
                <div class="end-point">End Roing</div>
            </div>
            
            <div style="text-align: center; margin-top: 15px; font-size: 11px; color: #6b7280;">
                <span style="display: inline-block; width: 8px; height: 8px; background-color: #10b981; border-radius: 50%; margin-right: 6px;"></span>Start Point
                <span style="display: inline-block; width: 8px; height: 8px; background-color: #dc2626; border-radius: 50%; margin: 0 6px 0 15px;"></span>End Point
                <span style="display: inline-block; width: 8px; height: 8px; background-color: #2563eb; border-radius: 50%; margin: 0 6px 0 15px;"></span>Movement Path
            </div>
            <div style="text-align: center; margin-top: 8px; font-size: 11px; color: #6b7280; font-weight: bold;">
                Employee Movement Analysis • 01 Sep 2025 to 30 Sep 2025
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-note">
            Report Generated Successfully using advanced analytics and real-time data collection.
        </div>
    </div>
</body>
</html>