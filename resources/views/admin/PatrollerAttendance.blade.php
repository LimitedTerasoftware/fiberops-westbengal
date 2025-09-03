@extends('admin.layout.base')

@section('title', 'Attendance Summary - ')

@section('content')
<div class="content-area py-4">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="header-section mb-4">
            <div class="header-content">
                <h1 class="page-title">Attendance Summary</h1>
                <p class="page-subtitle">Field Team Management</p>
            </div>
            <button class="btn btn-export">
                <i class="fa fa-download me-2"></i>Export
            </button>
        </div>

        <!-- Filters Section -->
        <div class="filters-section mb-4">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Date Range</label>
                    <input type="date" class="filter-input" placeholder="dd-mm-yyyy">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Quick Filter</label>
                    <select class="filter-select">
                        <option>Today</option>
                        <option>Yesterday</option>
                        <option>This Week</option>
                        <option>This Month</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Search</label>
                    <input type="text" class="filter-input" placeholder="Name or ID">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Role</label>
                    <select class="filter-select">
                        <option>All</option>
                        <option>FRT</option>
                        <option>Patroller</option>
                        <option>Supervisor</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Zone</label>
                    <select class="filter-select">
                        <option>All Zones</option>
                        <option>Zone A</option>
                        <option>Zone B</option>
                        <option>Zone C</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select class="filter-select">
                        <option>All</option>
                        <option>Synced</option>
                        <option>Pending</option>
                        <option>Absent</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid mb-5">
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number">124</div>
                    <div class="stat-label">Total Field Staff</div>
                </div>
                <div class="stat-icon stat-icon-blue">
                    <i class="fa fa-users"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number">98</div>
                    <div class="stat-label">Active Today</div>
                </div>
                <div class="stat-icon stat-icon-green">
                    <i class="fa fa-user"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number">7.5h</div>
                    <div class="stat-label">Avg Duration</div>
                </div>
                <div class="stat-icon stat-icon-orange">
                    <i class="fa fa-clock-o"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number">1,847</div>
                    <div class="stat-label">Total KM</div>
                </div>
                <div class="stat-icon stat-icon-purple">
                    <i class="fa fa-road"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number">89</div>
                    <div class="stat-label">Complete Records</div>
                </div>
                <div class="stat-icon stat-icon-indigo">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
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
                            <th class="th-zone">ZONE/GP</th>
                            <th class="th-punch">PUNCH IN</th>
                            <th class="th-punch">PUNCH OUT</th>
                            <th class="th-duration">DURATION</th>
                            <th class="th-km">KM</th>
                            <th class="th-status">STATUS</th>
                            <th class="th-actions">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-row">
                            <td class="td-staff">
                                <div class="staff-info">
                                    <div class="staff-avatar">
                                        <img src="https://images.pexels.com/photos/2379004/pexels-photo-2379004.jpeg?auto=compress&cs=tinysrgb&w=50&h=50&fit=crop" alt="Ravi Kumar">
                                    </div>
                                    <div class="staff-details">
                                        <div class="staff-name">Ravi Kumar</div>
                                        <div class="staff-id">FRT_023</div>
                                    </div>
                                </div>
                            </td>
                            <td class="td-role">
                                <span class="role-badge role-frt">FRT</span>
                            </td>
                            <td class="td-date">19-07-2025</td>
                            <td class="td-zone">Zone B / Chakulia GP</td>
                            <td class="td-punch">08:15</td>
                            <td class="td-punch">16:35</td>
                            <td class="td-duration">8h 20m</td>
                            <td class="td-km">22.4</td>
                            <td class="td-status">
                                <span class="status-badge status-synced">
                                    <span class="status-dot"></span>
                                    Synced
                                </span>
                            </td>
                            <td class="td-actions">
                                <button class="action-btn action-btn-primary">View Route</button>
                                <button class="action-btn action-btn-secondary">Calendar</button>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td class="td-staff">
                                <div class="staff-info">
                                    <div class="staff-avatar">
                                        <img src="https://images.pexels.com/photos/1239291/pexels-photo-1239291.jpeg?auto=compress&cs=tinysrgb&w=50&h=50&fit=crop" alt="Priya Singh">
                                    </div>
                                    <div class="staff-details">
                                        <div class="staff-name">Priya Singh</div>
                                        <div class="staff-id">PAT_045</div>
                                    </div>
                                </div>
                            </td>
                            <td class="td-role">
                                <span class="role-badge role-patroller">Patroller</span>
                            </td>
                            <td class="td-date">19-07-2025</td>
                            <td class="td-zone">Zone A / Ranchi GP</td>
                            <td class="td-punch">07:45</td>
                            <td class="td-punch">15:30</td>
                            <td class="td-duration">7h 45m</td>
                            <td class="td-km">18.2</td>
                            <td class="td-status">
                                <span class="status-badge status-pending">
                                    <span class="status-dot"></span>
                                    Pending
                                </span>
                            </td>
                            <td class="td-actions">
                                <button class="action-btn action-btn-primary">View Route</button>
                                <button class="action-btn action-btn-secondary">Calendar</button>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td class="td-staff">
                                <div class="staff-info">
                                    <div class="staff-avatar">
                                        <img src="https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?auto=compress&cs=tinysrgb&w=50&h=50&fit=crop" alt="Amit Sharma">
                                    </div>
                                    <div class="staff-details">
                                        <div class="staff-name">Amit Sharma</div>
                                        <div class="staff-id">FRT_011</div>
                                    </div>
                                </div>
                            </td>
                            <td class="td-role">
                                <span class="role-badge role-frt">FRT</span>
                            </td>
                            <td class="td-date">19-07-2025</td>
                            <td class="td-zone">Zone C / Dhanbad GP</td>
                            <td class="td-punch">-</td>
                            <td class="td-punch">-</td>
                            <td class="td-duration">-</td>
                            <td class="td-km">0</td>
                            <td class="td-status">
                                <span class="status-badge status-absent">
                                    <span class="status-dot"></span>
                                    Absent
                                </span>
                            </td>
                            <td class="td-actions">
                                <button class="action-btn action-btn-primary">View Route</button>
                                <button class="action-btn action-btn-secondary">Calendar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-section">
            <div class="pagination-info">
                Showing 1 to 10 of 124 entries
            </div>
            <nav class="pagination-nav">
                <ul class="pagination">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
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
}

/* Filters Section */
.filters-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.filters-grid {
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
    color: #4a5568;
    margin-bottom: 0.5rem;
}

.filter-input,
.filter-select {
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.875rem;
    background: white;
    transition: border-color 0.2s ease;
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.filter-input::placeholder {
    color: #a0aec0;
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

.role-frt {
    background-color: #ebf4ff;
    color: #2b6cb0;
}

.role-patroller {
    background-color: #f0fff4;
    color: #2f855a;
}

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
    
    .filters-grid {
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
    
    .filters-grid {
        grid-template-columns: 1fr;
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
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Filter functionality
    $('.filter-input, .filter-select').on('change input', function() {
        // Add your filter logic here
        console.log('Filter changed:', $(this).val());
    });

    // Export functionality
    $('.btn-export').on('click', function() {
        // Add your export logic here
        console.log('Export clicked');
    });

    // Action button functionality
    $('.action-btn').on('click', function() {
        const action = $(this).text().trim();
        console.log('Action clicked:', action);
    });
});
</script>
@endsection