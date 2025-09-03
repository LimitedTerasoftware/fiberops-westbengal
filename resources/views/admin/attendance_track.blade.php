@extends('admin.layout.base')

@section('title', 'Patroller Attendance & Activity - ')

@section('content')
<div class="content-area">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">Patroller Attendance & Activity</h1>
                <p class="page-subtitle">Daily sheet view of patroller activities</p>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="main-card">
            <div class="table-container">
                <table class="patroller-table">
                    <thead>
                        <tr>
                            <th class="th-patroller">Patroller</th>
                            <th class="th-login">Login Time</th>
                            <th class="th-start">Start Patrol Time</th>
                            <th class="th-km">Patrol KM Covered</th>
                            <th class="th-sync">Sync Status</th>
                            <th class="th-exceptions">Exceptions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-row">
                            <td class="td-patroller">
                                <div class="patroller-info">
                                    <div class="patroller-name">Ethan Carter</div>
                                </div>
                            </td>
                            <td class="td-login">
                                <span class="time-text">08:00 AM</span>
                            </td>
                            <td class="td-start">
                                <span class="time-text">08:30 AM</span>
                            </td>
                            <td class="td-km">
                                <span class="km-text">15 KM</span>
                            </td>
                            <td class="td-sync">
                                <span class="status-badge status-online">Online</span>
                            </td>
                            <td class="td-exceptions">
                                <span class="exception-text">None</span>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td class="td-patroller">
                                <div class="patroller-info">
                                    <div class="patroller-name">Olivia Bennett</div>
                                </div>
                            </td>
                            <td class="td-login">
                                <span class="time-text">08:15 AM</span>
                            </td>
                            <td class="td-start">
                                <span class="time-text">08:45 AM</span>
                            </td>
                            <td class="td-km">
                                <span class="km-text">12 KM</span>
                            </td>
                            <td class="td-sync">
                                <span class="status-badge status-online">Online</span>
                            </td>
                            <td class="td-exceptions">
                                <span class="exception-text">None</span>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td class="td-patroller">
                                <div class="patroller-info">
                                    <div class="patroller-name">Noah Thompson</div>
                                </div>
                            </td>
                            <td class="td-login">
                                <span class="time-text">08:30 AM</span>
                            </td>
                            <td class="td-start">
                                <span class="time-text">09:00 AM</span>
                            </td>
                            <td class="td-km">
                                <span class="km-text">10 KM</span>
                            </td>
                            <td class="td-sync">
                                <span class="status-badge status-pending">Pending</span>
                            </td>
                            <td class="td-exceptions">
                                <span class="exception-text">None</span>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td class="td-patroller">
                                <div class="patroller-info">
                                    <div class="patroller-name">Ava Martinez</div>
                                </div>
                            </td>
                            <td class="td-login">
                                <span class="time-text">08:45 AM</span>
                            </td>
                            <td class="td-start">
                                <span class="time-text">09:15 AM</span>
                            </td>
                            <td class="td-km">
                                <span class="km-text">8 KM</span>
                            </td>
                            <td class="td-sync">
                                <span class="status-badge status-online">Online</span>
                            </td>
                            <td class="td-exceptions">
                                <span class="exception-text">None</span>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td class="td-patroller">
                                <div class="patroller-info">
                                    <div class="patroller-name">Liam Harris</div>
                                </div>
                            </td>
                            <td class="td-login">
                                <span class="time-text">09:00 AM</span>
                            </td>
                            <td class="td-start">
                                <span class="time-text">09:30 AM</span>
                            </td>
                            <td class="td-km">
                                <span class="km-text">5 KM</span>
                            </td>
                            <td class="td-sync">
                                <span class="status-badge status-online">Online</span>
                            </td>
                            <td class="td-exceptions">
                                <span class="exception-text exception-alert">Inactivity Alert (1 hour)</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
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
    padding: 0 2rem;
}

/* Header Section */
.page-header {
    margin-bottom: 2rem;
}

.header-content {
    text-align: left;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
}

.page-subtitle {
    font-size: 1rem;
    color: #10b981;
    margin: 0;
    font-weight: 500;
}

/* Main Card */
.main-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: 1px solid #f1f5f9;
}

/* Table Container */
.table-container {
    overflow-x: auto;
}

/* Table Styles */
.patroller-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    background: white;
}

.patroller-table thead th {
    background-color: #f8fafc;
    padding: 1.25rem 1.5rem;
    text-align: left;
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748b;
    text-transform: none;
    letter-spacing: 0;
    border: none;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.patroller-table tbody td {
    padding: 1.5rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: 0.875rem;
}

.table-row {
    transition: background-color 0.2s ease;
}

.table-row:hover {
    background-color: #f8fafc;
}

.table-row:last-child td {
    border-bottom: none;
}

/* Column Specific Styles */
.th-patroller, .td-patroller { 
    width: 200px; 
    min-width: 200px;
}

.th-login, .td-login { 
    width: 140px; 
    min-width: 140px;
}

.th-start, .td-start { 
    width: 160px; 
    min-width: 160px;
}

.th-km, .td-km { 
    width: 140px; 
    min-width: 140px;
}

.th-sync, .td-sync { 
    width: 120px; 
    min-width: 120px;
}

.th-exceptions, .td-exceptions { 
    width: 200px; 
    min-width: 200px;
}

/* Patroller Info */
.patroller-info {
    display: flex;
    align-items: center;
}

.patroller-name {
    font-weight: 500;
    color: #1a202c;
    margin: 0;
    line-height: 1.4;
}

/* Time Text */
.time-text {
    color: #10b981;
    font-weight: 500;
    font-size: 0.875rem;
}

/* KM Text */
.km-text {
    color: #10b981;
    font-weight: 500;
    font-size: 0.875rem;
}

/* Status Badges */
.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-block;
    text-align: center;
    min-width: 70px;
}

.status-online {
    background-color: #f0f9ff;
    color: #0369a1;
    border: 1px solid #e0f2fe;
}

.status-pending {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}

.status-offline {
    background-color: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

/* Exception Text */
.exception-text {
    color: #10b981;
    font-weight: 400;
    font-size: 0.875rem;
}

.exception-alert {
    color: #f59e0b;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .container-fluid {
        padding: 0 1.5rem;
    }
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 0 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .table-container {
        overflow-x: scroll;
        -webkit-overflow-scrolling: touch;
    }
    
    .patroller-table {
        min-width: 800px;
    }
    
    .patroller-table thead th,
    .patroller-table tbody td {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .content-area {
        padding: 1rem 0;
    }
    
    .page-header {
        margin-bottom: 1.5rem;
    }
    
    .page-title {
        font-size: 1.25rem;
    }
    
    .page-subtitle {
        font-size: 0.875rem;
    }
}

/* Table Hover Effects */
.table-row:hover .patroller-name {
    color: #3b82f6;
}

.table-row:hover .time-text,
.table-row:hover .km-text {
    color: #059669;
}

/* Focus States */
.patroller-table:focus-within {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Print Styles */
@media print {
    .content-area {
        background: white;
        padding: 0;
    }
    
    .main-card {
        box-shadow: none;
        border: 1px solid #e2e8f0;
    }
    
    .table-row:hover {
        background-color: transparent;
    }
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Add any interactive functionality here
    console.log('Patroller Attendance page loaded');
    
    // Example: Add click handlers for rows
    $('.table-row').on('click', function() {
        const patrollerName = $(this).find('.patroller-name').text();
        console.log('Clicked on patroller:', patrollerName);
    });
});
</script>
@endsection