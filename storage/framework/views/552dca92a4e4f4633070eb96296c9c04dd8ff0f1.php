<?php $__env->startSection('title', 'Attendance Summary - '); ?>

<?php $__env->startSection('content'); ?>
<?php 
    $roles = [
        1 => 'OFC',
        2 => 'FRT',
        5 => 'Patroller',
        3 => 'Zonal incharge',
        4 => 'District incharge'
    ];
 ?>
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
           <a href="<?php echo e(route('admin.attendance-export', [
                    'from_date' => request('from_date'),
                    'to_date' => request('to_date'),
                    'date_range' => request('date_range'),
                    'district_id' => request('district_id'),
                    'block_id' => request('block_id'),
                    'zone_id' => request('zone_id'),
                    'role' => request('role'),
                    'status' => request('status'),
                    'search' => request('search')
                ])); ?>" 
            class="btn btn-export">
                <i class="fa fa-download me-2"></i>Export
            </a>


        </div>

        <!-- Filters Section -->
         <div class="filters-section mb-4">
    <form method="GET" action="<?php echo e(route('admin.attendance_list')); ?>">

        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Zone</label>
                <select name="zone_id" class="filter-select">
                    <option value="">Select Zone</option>
                    <?php $__currentLoopData = $zonals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zon): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <option value="<?php echo e($zon->id); ?>" <?php echo e(request('zone_id') == $zon->id ? 'selected' : ''); ?>>
                            <?php echo e($zon->Name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">District</label>
                <select name="district_id" class="filter-select">
                    <option value="">Select District</option>
                    <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <option value="<?php echo e($district->id); ?>" <?php echo e(request('district_id') == $district->id ? 'selected' : ''); ?>>
                            <?php echo e($district->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Block</label>
                <select name="block_id" class="filter-select">
                    <option value="">Select Block</option>
                    <?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <option value="<?php echo e($block->id); ?>" <?php echo e(request('block_id') == $block->id ? 'selected' : ''); ?>>
                            <?php echo e($block->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Role</label>
                <select name="role" class="filter-select">
                    <option value="">All Roles</option>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $roleName): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <option value="<?php echo e($id); ?>" <?php echo e(request('role') == $id ? 'selected' : ''); ?>>
                            <?php echo e($roleName); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Date Range</label>
                <select name="date_range" class="filter-select">
                    <option value="today" <?php echo e(request('date_range') == 'today' ? 'selected' : ''); ?>>Today</option>
                    <option value="yesterday" <?php echo e(request('date_range') == 'yesterday' ? 'selected' : ''); ?>>Yesterday</option>
                    <option value="week" <?php echo e(request('date_range') == 'week' ? 'selected' : ''); ?>>This Week</option>
                    <option value="month" <?php echo e(request('date_range') == 'month' ? 'selected' : ''); ?>>This Month</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="filter-input" placeholder="Name, ID, GP...">
            </div>
        </div>

                <!-- New Row: From Date & To Date -->
                <div class="filters-grid mt-1">
                    <div class="filter-group">
                        <label class="filter-label">From Date</label>
                        <input type="date" name="from_date" value="<?php echo e(request('from_date')); ?>" class="filter-input">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">To Date</label>
                        <input type="date" name="to_date" value="<?php echo e(request('to_date')); ?>" class="filter-input">
                    </div>
               

                    <!-- Buttons Row -->
                    <div class="filter-group" style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="action-btn action-btn-primary">Apply</button>
                        <a href="<?php echo e(route('admin.attendance_list')); ?>" class="action-btn action-btn-secondary">Clear</a>
                    </div>
                </div>

            </form>
        </div>


        <!-- Statistics Cards -->
        <!-- <div class="stats-grid mb-5">
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
        </div> -->

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
                            <th class="th-actions">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>

                            <tr class="table-row">
                                <td class="td-staff">
                                    <div class="staff-info">
                                        <div class="staff-avatar">
                                            <img src="https://images.pexels.com/photos/2379004/pexels-photo-2379004.jpeg?auto=compress&cs=tinysrgb&w=50&h=50&fit=crop" alt="Ravi Kumar">
                                        </div>
                                        <div class="staff-details">
                                            <div class="staff-name"><?php echo e($provider->first_name); ?> <?php echo e($provider->last_name); ?></div>
                                            <div class="staff-id"><?php echo e($provider->mobile); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="td-role">
                                    <?php if(isset($roles[$provider->type])): ?>
                                        <span class="role-badge role-<?php echo e(strtolower(str_replace(' ', '-', $roles[$provider->type]))); ?>">
                                            <?php echo e($roles[$provider->type]); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="role-badge role-unknown">Unknown</span>
                                    <?php endif; ?>
                                </td>
                                <td class="td-date"> <?php echo e($provider->check_in ? \Carbon\Carbon::parse($provider->check_in)->format('dd/mm/yyyy') : '—'); ?></td>
                                <td class="td-zone"><?php echo e(isset($provider->zone_name) ? $provider->zone_name : 'N/A'); ?></td>
                                <td class="td-punch"><?php echo e($provider->check_in ? \Carbon\Carbon::parse($provider->check_in)->format('h:i A') : '—'); ?></td>
                                <td class="td-punch"> 
                                    <?php if($provider->attendance_status == 'active'): ?>
                                    -
                                    <?php else: ?>
                                    <?php echo e($provider->check_out ? \Carbon\Carbon::parse($provider->check_out)->format('h:i A') : '—'); ?>

                                    <?php endif; ?>
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
                                    <?php echo e($duration? $duration . ' hrs' : $provider->duration); ?>

                                    
                                </td>
                                <td class="td-zone"><?php echo e(isset($provider->version) ? $provider->version : 'N/A'); ?></td>

                                <!-- <td class="td-km">22.4</td> -->
                                <td class="td-status">
                                      <?php if($provider->attendance_status == 'active'): ?>
                                            <span class="status-badge status-done">Online</span>
                                        <?php else: ?>
                                            <span class="status-badge status-late">Offline</span>
                                        <?php endif; ?>
                                </td>
                                <td class="td-actions">
                                    <button class="action-btn action-btn-primary">View Route</button>
                                    <button class="action-btn action-btn-secondary">Calendar</button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>

                     
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-section">
            <div class="pagination-info">
                Showing 
                <?php echo e(($providers->currentPage() - 1) * $providers->perPage() + 1); ?> 
                to 
                <?php echo e(($providers->currentPage() * $providers->perPage()) > $providers->total() 
                    ? $providers->total() 
                    : $providers->currentPage() * $providers->perPage()); ?> 
                of <?php echo e($providers->total()); ?> entries
            </div>
            <nav class="pagination-nav">
                <?php echo e($providers->links('pagination::bootstrap-4')); ?>

            </nav>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->

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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>