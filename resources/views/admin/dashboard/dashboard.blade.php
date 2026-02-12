@extends('admin.layout.base')

@section('title', 'Dashboard ')

@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')

<style>
   html, body {
  background: white;
  height: 100%;
  overflow-y: auto !important;
}

/* Sidebar specifically */
.sidebar {
  max-height: 100vh;
  overflow-y: auto !important;
}

  .dashboard-page {background-color: #f8fafc;}
  /* General card styles */
  .dashboard-page .card { 
    background-color: #ffffff !important; 
    border-radius: 16px; 
    box-shadow: 0 2px 6px rgba(0,0,0,0.05); 
  }

  /* Stat cards (top row) */
  .stat-card { 
    text-align: center; 
    padding: 1rem; 
  }
  .stat-value { 
    font-size: 1.6rem; 
    font-weight: 600; 
  }
  .badge-sub { 
    font-size: 0.8rem; 
    font-weight: 800; 
  }

  /* Section title */
  .section-title { 
    font-size: 1rem; 
    font-weight: 600; 
    margin: 1rem 0; 
  }

  /* Ticket breakdown cards */
  .ticket-card {
    border-radius: 16px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    background: #fff;
    text-align: center;
    padding: 0.5rem;
    min-height: 260px;
  }
  .ticket-card h6 { 
   font-weight: 600;
    margin: 0;          /* remove extra spacing */
    text-align: unset;  /* let flexbox handle alignment */
  }
  .ticket-icon {
  font-size: 1.4rem;
}
    .stat-number { 
    font-size: 1.3rem; 
    font-weight: 600; 
  }

   .progress {
  height: 8px;
  border-radius: 10px;
}

.view-btn {
  background-color: #f0f6ff;
  border-radius: 10px;
  padding: 6px;
  margin-top: 10px;
  font-weight: 500;
  color: #0d6efd;
  text-decoration: none;
  display: block;
}

.view-btn:hover {
  background-color: #e1ecff;
  color: #0a58ca;
}

/* Force the header to flex left/right */
.header-fix {
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  width: 100%;
}
.header-fix h6 {
  margin: 0; /* remove default margin */
}
.dashboard-page .card canvas {
  max-height: 280px !important;
  padding:1rem;
}

  #dashboardMap {
        height: 50%;
        min-height: 380px;
    }

    #legend {
        font-family: Arial, sans-serif;
        background: rgba(255, 255, 255, 0.8);
        padding: 10px;
        margin: 10px;
        border: 2px solid #f3f3f3;
    }

    #legend h3 {
        margin-top: 0;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }

    #legend img {
        vertical-align: middle;
        margin-bottom: 5px;
    }

.loader {
  border: 4px solid #f3f3f3;   /* Light gray background */
  border-top: 4px solid #007bff; /* Blue color */
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin: auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}


.loader-square {
  width: 40px;
  height: 40px;
  background: #007bff;
  animation: loader-rotate 1.2s infinite ease-in-out;
}
@keyframes loader-rotate {
  0%   { transform: perspective(120px) rotateX(0deg) rotateY(0deg); }
  50%  { transform: perspective(120px) rotateX(-180deg) rotateY(0deg); }
  100% { transform: perspective(120px) rotateX(-180deg) rotateY(-180deg); }
}



.ripple-loader {
  display: inline-block;
  position: relative;
  width: 64px;
  height: 64px;
}
.ripple-loader div {
  position: absolute;
  border: 4px solid #007bff;
  border-radius: 50%;
  animation: ripple 1.2s cubic-bezier(0, 0.2, 0.8, 1) infinite;
}
.ripple-loader div:nth-child(2) {
  animation-delay: -0.6s;
}
@keyframes ripple {
  0% { top: 28px; left: 28px; width: 0; height: 0; opacity: 1; }
  100% { top: -1px; left: -1px; width: 58px; height: 58px; opacity: 0; }
}

/* GRID */
.grid-3 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

@media (max-width: 992px) {
    .grid-3 {
        grid-template-columns: 1fr;
    }
}

/* CARD */
.new-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    min-height: 320px;
    position: relative;
}

.new-card h6 {
    font-weight: 600;
    font-size: 14px;
    color: #2c2c2c;
    margin-bottom: 12px;
}


/* CANVAS FIX */
canvas {
    width: 100% !important;
    height: 240px !important;
}

/* ALERTS */
.alert {
    padding: 10px 12px;
    border-radius: 8px;
    margin-bottom: 10px;
    font-size: 13px;
    font-weight: 500;
}
.alert.danger { background:#fdecea; color:#d32f2f; }
.alert.warning { background:#fff4e5; color:#ef6c00; }
.alert.info { background:#e3f2fd; color:#1976d2; }

/* CARD HEADER FIX */
.card-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.card-header-custom h6 {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
}

/* FILTER GROUP */
.header-filters {
    display: flex;
    gap: 8px;
}

.header-filters select {
    min-width: 110px;
}


/* TABLE */
.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.table th {
    background: #f1f3f6;
    padding: 10px;
    text-align: left;
    font-weight: 600;
}

.table td {
    padding: 10px;
    border-bottom: 1px solid #e6e6e6;
}

/* BADGES */
.badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    color: #fff;
}
.badge.green { background:#43a047; }
.badge.red { background:#e53935; }
.badge.orange { background:#fb8c00; }

.green { color:#43a047; font-weight:600; }
.red { color:#e53935; font-weight:600; }
.orange { color:#fb8c00; font-weight:600; }

/* TEAM */
.team-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.team-list li {
    padding: 12px 14px;
    border-radius: 10px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    font-weight: 500;
}

.team-list .good { background:#e8f5e9; }
.team-list .neutral { background:#f5f5f5; }
.team-list .bad { background:#fdecea; }
/* SPACING */
.mt-20 { margin-top: 20px; }

/* MINI DROPDOWN */
.mini-select {
    height: 28px;
    padding: 2px 26px 2px 8px;
    font-size: 12px;
    font-weight: 500;
    color: #1f2937;
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    outline: none;
    cursor: pointer;

    /* Remove default arrow */
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;

    /* Custom arrow */
    background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 8px center;
    background-size: 14px;
}

.mini-select:hover {
    background-color: #f3f4f6;
}

.mini-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37,99,235,0.15);
}
/* ================= HEATMAP COLORS ================= */
.hm-good {
    background: #e8f5e9 !important;
    color: #2e7d32 !important;
    font-weight: 600;
}
.hm-warning {
    background: #fff8e1 !important;
    color: #ef6c00 !important;
    font-weight: 600;
}
.hm-bad {
    background: #fdecea !important;
    color: #c62828 !important;
    font-weight: 600;
}

/* ===== Table Modern Look ===== */
.heatmap-table th {
    cursor: pointer;
    user-select: none;
    white-space: nowrap;
}
.heatmap-table th.sortable::after {
    content: "\21C5";
    font-size: 11px;
    color: #9e9e9e;
}
.heatmap-table th.sorted-asc::after {
    content: "\25B2";
    color: #1e88e5;
}
.heatmap-table th.sorted-desc::after {
    content: "\25BC";
    color: #1e88e5;
}
.heatmap-table td,
.heatmap-table th {
    padding: 10px 12px;
    vertical-align: middle;
}
.heatmap-table tbody tr:hover {
    background: #f8fafc;
}

</style>

<?php
use \Carbon\Carbon;
$todaydate = Carbon::today();
$today = $todaydate->toDateString();

$yesterdaydate = Carbon::yesterday();
$yesterday= $yesterdaydate->toDateString();

?>

<div class="dashboard-page">
<div class="container-fluid py-3">

  <!-- Top Stats -->
  <div class="row mb-1">
     <div class="col-md-2 mb-1">
      <div class="card stat-card">
        <div class="stat-value text-primary" id="gp-total">0</div>
        <div>GP Summary</div>
        <div class="badge-sub">
            <a href="{{ url('/admin/tickets?Gpstatus=DownGP') }}"> <i class="bi bi-caret-down-fill text-danger"></i> <span class="text-danger" id="unique-lgd">0</span>(<span class="text-danger" id="unique-lgd-percent">0</span>)</a>
            | <i class="bi bi-caret-up-fill text-success"></i> <span class="text-success" id="down-gps">0</span>(<span class="text-success" id="down-gps-percent">0</span>)
           </div>
      </div>
    </div>

   
         <div class="col-md-2 mb-1">
            <div class="card stat-card p-1">
              <div class="row align-items-center text-center g-1" >
                <div class="col-6" style="margin-top:-13px;">
                  <a href="{{ url('/admin/tickets?category=Permanent Down&status=Onhold') }}">
                    <div class="stat-value text-danger mb-1" id="totalpermanent_down">0</div>
                  </a>
                  <div class="small"  style="margin-top:-15px;">Permanent Down</div>
                </div>

                <div class="col-6" style="margin-bottom:-13px;">
                  <a href="{{ url('/admin/tickets?category=BSNL Scope&status=Onhold') }}">
                    <div class="stat-value text-primary mb-1" id="etr_fiber_cut_olt">0</div>
                  </a>
                  <div class="small" style="margin-top:-13px;">BSNL Scope</div>
                </div>
              </div>
            </div>
          </div>
         
      <div class="col-md-2 mb-1">
      <div class="card stat-card">
        <a href="{{ url('/admin/tickets-ongoing-intervals') }}">
        <div class="stat-value text-danger" id="notstarted-tickets">0</div>
        </a>
        <div>Open Tickets</div>
      <div class="badge-sub">
        <a href="/admin/tickets?autoclose=Auto&status=Open">
            <span class="text-success">Auto : <span id="open_auto">0</span></span>
        </a>
        |
        <a href="/admin/tickets?autoclose=Manual&status=Open">
            <span class="text-primary">Manual : <span id="open_manual">0</span></span>
        </a>
    </div>

      </div>
    </div>

  

        <div class="col-md-2 mb-1">
      <div class="card stat-card">
        <a href="{{ url('/admin/tickets-ongoing-history') }}">
        <div class="stat-value" id="ongoing-tickets">0</div>
        </a>
        <div>Ongoing</div>
        <div class="badge-sub">
           <a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=OnGoing"><span class="text-success">Today : <span id="today-ongoing">0</span></span></a>  
         | <a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=OnGoing"><span class="text-primary"> Yesterday : <span id="yesterday-ongoing">0</span></span></a>
        </div>
      </div>
    </div>

      <div class="col-md-2 mb-1">
      <div class="card stat-card">
        <a href="{{ url('/admin/tickets?status=Onhold') }}"><div class="stat-value text-warning" id="r-onhold-tickets">0</div></a>
        <div>Hold</div>
        <div class="badge-sub">
              <a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=Onhold"><span class="text-success">Today : <span id="today-onhold">0</span></span></a>  
            | <a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Onhold"><span class="text-primary"> Yesterday : <span id="yesterday-onhold">0</span></span></a>
      </div>
       </div>
    </div>

<!---
    <div class="col-md-2 mb-1">
      <div class="card stat-card">
        <a href="{{ url('/admin/tickets') }}"><div class="stat-value" id="total-tickets">0</div></a>
        <div>Total Tickets</div>
        <div class="text-success badge-sub">
          <span class="text-success">Auto : <span id="total-auto-tickets">...</span></span> 
          | <span class="text-primary"> Manual : <span id="total-manual-tickets">...</span></span>
         
       </div>
      </div>
    </div> 
--->
    <div class="col-md-2 mb-1">
      <div class="card stat-card">
        <a href="{{ url('/admin/tickets-completed-history') }}"><div class="stat-value text-success" id="complete-ticket-percent">0%</div></a>
        <div>Completed</div>
        <div class="badge-sub">
           <a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=Completed"><span class="text-success">Today : <span id="today-completed-tickets">0</span></span></a> 
         | <a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Completed"><span class="text-primary"> Yesterday : <span id="yesterday-completed-tickets">0</span></span></a>
       </div>
      </div>
    </div>
   
  
  </div>






  <div class="row">

<div class="col-md-6 mb-2">
  <div class="card p-3">
     <h6>Category-wise Tickets (Power, Fiber, etc.)</h6>
  
     <div id="categoryLoader" class="text-center p-4">
        <div class="spinner-border text-primary" role="status">
         <div id="loader" class="ripple-loader"><div></div><div></div></div>
        </div>
      </div>

    <canvas id="categoryChart"></canvas>
  </div>
</div>

<div class="col-md-6 mb-2">
  <div class="card p-3">
     <h6>Overall Tickets (Open Tickets, Ongoing, Hold)</h6>
     
     
       <div id="overallLoader" class="text-center p-4">
        <div class="spinner-border text-primary" role="status">
         <div id="loader" class="ripple-loader"><div></div><div></div></div>
        </div>
      </div>

    <canvas id="overallChart"></canvas>
  </div>
</div>

</div>

<!-- ROW 1 -->
    <div class="grid-3">
        <div class="new-card">

        <!-- HEADER -->
<div class="card-header-custom">
    <h6 class="m-0">Velocity Trend</h6>

    <div class="header-filters">
        <select id="velocityRange" class="mini-select">
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="7days" selected>Last 7 Days</option>
            <option value="15days" >Last 15 Days</option>
            <option value="30days" >Last 30 Days</option>

        </select>

       <select id="velocityGType" class="mini-select">
                <option value="" selected>Generated</option>
                <option value="all">All</option>
                <option value="auto">Auto</option>
                <option value="manual">Manual</option>
            </select>


        <select id="velocityType" class="mini-select">
            <option value="" selected>Completed</option>
            <option value="all">All</option>
            <option value="auto">Auto</option>
            <option value="manual">Manual</option>
        </select>
    </div>
</div>
        <!-- CHART -->
        <canvas id="velocityChart"></canvas>

    </div>

        <div class="new-card">
            <h6>Backlog Burndown & Forecast</h6>
            <canvas id="burndownChart"></canvas>
        </div>
        <!---
        <div class="new-card alert-card">
            <h6> Alerts & Exceptions</h6>
            <div class="alert danger">SLA Breach Spike in Krishna - 15 tickets</div>
            <div class="alert warning">Zero Progress in Block "Kankipadu"</div>
            <div class="alert warning">Ticket Stuck &gt; 5 Days</div>
            <div class="alert info">Missing Photos / GPS</div>
        </div>--->
    </div>

<div class="new-card mt-20">

    <!-- HEADER -->
    <div class="card-header-custom">
        <h6 class="m-0">District Performance Heatmap</h6>

        <div class="header-filters">
            <select id="districtRange" class="mini-select">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="7days" selected>Last 7 Days</option>
            </select>

            <select id="districtGType" class="mini-select">
                <option value="all" selected>Generated: All</option>
                <option value="auto">Generated: Auto</option>
                <option value="manual">Generated: Manual</option>
            </select>

            <select id="districtType" class="mini-select">
                <option value="all" selected>Completed: All</option>
                <option value="auto">Completed: Auto</option>
                <option value="manual">Completed: Manual</option>
            </select>
        </div>
    </div>

    <!-- TABLE -->
    <table class="table heatmap-table">
        <thead>
            <tr>
                <th class="sortable" data-key="district">District</th>
                <th class="sortable" data-key="assigned">Assigned</th>
                <th class="sortable" data-key="closed">Closed</th>
                <th class="sortable" data-key="hold">Hold</th>
                <th class="sortable" data-key="ongoing">Ongoing</th>
                <th class="sortable" data-key="not_started">Open</th>
                <th class="sortable" data-key="sla_pass">SLA (Reached)</th>
                <th class="sortable" data-key="sla_percent">SLA %</th>
                <th class="sortable" data-key="net_velocity">Net Velocity</th>
            </tr>
        </thead>
        <tbody id="districtHeatmapBody">
            <tr>
                <td colspan="9" class="text-center text-muted">Loading...</td>
            </tr>
        </tbody>
    </table>
</div>



<div class="row mt-20">
 <!-- Team Activity Summary -->
<div class="col-md-6 mb-2">
  <div class="card p-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-1 header-fix">
      <h6 class="mb-0 font-weight-bold">Today Team Activity Summary</h6>
      <a href="{{ url('/admin/teams_status') }}" class="btn btn-sm font-weight-bold" style="background-color:#f0f6ff; color:#0d6efd; border-radius:8px; padding:4px;">More View</a>
    </div>

    <!-- Body -->
    <ul class="list-unstyled mb-0">
       <li class="d-flex justify-content-between py-1 header-fix">
        <span><i class="bi bi-people-fill"></i> Total Teams</span>
        <a href="{{ url('/admin/dailyrepots') }}"><span class="font-weight-bold" id="total-teams">0</span></a>
      </li>

      <li class="d-flex justify-content-between py-1 header-fix">
        <span><i class="bi bi-pin-angle-fill text-info"></i> Tickets Assigned Today Teams</span>
        <span class="font-weight-bold text-info" id="asign-teams">0</span>
      </li>
      <li class="d-flex justify-content-between py-1 header-fix">
        <span><i class="bi bi-arrow-repeat text-primary"></i> Ongoing Teams</span>
        <a href="{{ url('/admin/workforce_details?stage=working&type=frt') }}"><span class="font-weight-bold text-primary" id="ongoing-teams">0</span></a>
      </li>
      <li class="d-flex justify-content-between py-1 header-fix">
        <span><i class="bi bi-check-square-fill text-success"></i> Completed Teams</span>
        <a href="{{ url('/admin/workforce_details?stage=completed&type=frt') }}"><span class="font-weight-bold text-success" id="t-completed-teams">0</span></a>
      </li>
      <li class="d-flex justify-content-between py-1 header-fix">
        <span><i class="bi bi-hourglass-split text-warning"></i> Yet to Start Teams</span>
        <a href="{{ url('/admin/workforce_details?stage=not_started&type=frt') }}"><span class="font-weight-bold text-warning" id="notstarted-teams">0</span></a>
      </li>
       <li class="d-flex justify-content-between py-1 header-fix">
        <span><i class="bi bi-hypnotize text-danger"></i> Tickets On Hold Teams</span>
        <a href="{{ url('/admin/workforce_details?stage=only_hold&type=frt') }}"><span class="font-weight-bold text-danger" id="t-hold-teams">0</span></a>
      </li>
       <li class="d-flex justify-content-between py-1 header-fix">
        <span><i class="bi bi-ban text-danger"></i> No Tickets Assigned Teams</span>
        <a href="{{ url('/admin/workforce_details?stage=no_ticket&type=frt') }}"><span class="font-weight-bold text-danger" id="no-t-teams">0</span></a>
      </li>
       <li class="d-flex justify-content-between py-1 header-fix">
        <span><i class="bi bi-graph-up-arrow text-danger"></i> Overloaded Teams(>2 tickets)</span>
        <a href="{{ url('/admin/workforce_details?stage=open_morethen2&type=frt') }}"><span class="font-weight-bold text-danger" id="more-then-2">0</span></a>
      </li>
       <li class="d-flex justify-content-between py-1 header-fix">
        <span><i class="bi bi-clock-fill text-danger"></i> Old Tickets Pending Teams</span>
        <a href="{{ url('/admin/workforce_details?stage=old_ongoing&type=frt') }}"><span class="font-weight-bold text-danger" id="old-tickes-teams">0</span></a>
      </li>



    </ul>
  </div>
</div>

<!-- Attendance & Patroller Summary -->
<div class="col-md-6 mb-3">
  <div class="card p-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-1 header-fix">
      <h6><span class="mb-0 font-weight-bold">Team Live Location</span>   &nbsp;&nbsp;&nbsp;&nbsp;  <span class="text-success">Online : <span id="online-count">0</span></span> | <span class="text-danger">Offline : <span id="offline-count">0</span></span></h6>
      <a href="{{ url('/admin/trackattendance') }}" class="btn btn-sm font-weight-bold " style="background-color:#e9f9f0; color:#198754; border-radius:8px; padding:4px;">View Map</a>
    </div>
      <div class="d-flex justify-content-between align-items-center  header-fix">
      <p>FRT :<span class="text-success"> Online : <span id="frt-online">0</span></span> | <span class="text-danger">Offline : <span id="frt-offline">0</span></span></p>
      <p>Patroller :<span class="text-success"> Online : <span id="patroller-online">0</span></span> | <span class="text-danger">Offline : <span id="patroller-offline">0</span></span></p>
      </div>
    <!-- Body -->
      <div class="card-body">
                <div id="dashboardMap"></div>
                <div id="legend"><h3>Legend</h3></div>
      </div>
   </div>
</div>

</div>



  <!-- Category-wise Tickets -->
  <div class="section-title">Category-wise Ticket Breakdown</div>
  <div class="row">

  <!-- Hold -->
  <div class="col-md-3 mb-1">
    <div class="card ticket-card p-2">
      <div class="d-flex justify-content-between align-items-center mb-2 header-fix">
        <h6>Hold</h6>
        <i class="bi bi-ticket-detailed-fill text-danger ticket-icon"></i>
      </div>
      <div class="ticket-stats mt-2">
        <div class="row text-center">
          <div class="col-md-4 text-primary">
             <a href="{{ url('/admin/tickets?category=Power&status=Onhold') }}">
             <div class="stat-number text-primary" id="power-hold1">0</div>
             </a>
            <small>Power</small>
          </div>
           <div class="col-md-4 text-success">
            <a href="{{ url('/admin/tickets?category=CCU/Battery&status=Onhold') }}">
            <div class="stat-number text-success" id="battery-hold1">0</div>          
            </a>
            <small>CCU/Battery</small>
          </div>

          <div class="col-md-4 text-warning">
            <a href="{{ url('/admin/tickets?category=Fiber&status=Onhold') }}">
            <div class="stat-number text-warning" id="fiber-hold1">0</div>
            </a>
            <small>Fiber</small>
          </div>
          <div class="col-md-4 text-danger mt-2">
            <a href="{{ url('/admin/tickets?category=Software/Hardware&status=Onhold') }}">
            <div class="stat-number text-danger" id="electronics-hold1">0</div>
            </a>
            <small>Soft/Hard</small>
          </div>
           <div class="col-md-4 text-danger mt-2">
             <a href="{{ url('/admin/tickets?category=BSNL Scope&status=Onhold') }}">
            <div class="stat-number text-danger" id="etr_fiber_cut_olttot">0</div> 
            </a>
            <small>BSNL </small>
          </div>

          <div class="col-md-4 text-danger mt-2">
             <a href="{{ url('/admin/tickets?category=Permanent Down&status=Onhold') }}">
            <div class="stat-number text-danger" id="permanent_down">0</div> 
            </a>
            <small>Prmt Down</small>
          </div>
         

          <div class="col-md-4 text-danger mt-2">
            <a href="{{ url('/admin/tickets?category=OLT&status=Onhold') }}">
            <div class="stat-number text-danger" id="olt-hold1">0</div>
            </a>
            <small>OLT</small>
          </div>
          <div class="col-md-4 text-success mt-2">
            <a href="{{ url('/admin/tickets?category=Solar&status=Onhold') }}">
            <div class="stat-number text-success" id="solar-hold1">0</div>
            </a>
            <small>Solar</small>
          </div>
          <div class="col-md-4 text-success mt-2">
            <a href="{{ url('/admin/tickets?category=Others&status=Onhold') }}">
            <div class="stat-number text-success" id="other-hold1">0</div>
            </a>
            <small>Other</small>
          </div>

        </div>
      </div>
    <!-- View Button -->
    <a href="{{ url('/admin/tickets?status=Onhold') }}" class="view-btn">View All</a>

    </div>
  </div>


  <!-- Power -->
  <div class="col-md-3 mb-2">
    <div class="card ticket-card p-2">
      <div class="d-flex justify-content-between align-items-center mb-2 header-fix">
        <h6>Power</h6>
        <i class="bi bi-lightning-charge-fill text-warning ticket-icon"></i>
      </div>
      <div class="ticket-stats mt-2">
        <div class="row text-center">
          <div class="col-md-6 text-primary">
            <a href="{{ url('/admin/tickets?category=Power&status=Open') }}">
            <div class="stat-number text-primary" id="power-not-started">0</div>
            </a>
            <small>Open Tickets</small>
          </div>
          <div class="col-md-6 text-warning">
            <a href="{{ url('/admin/tickets?category=Power&status=OnGoing') }}">
            <div class="stat-number text-warning" id="power-ongoing">0</div>
            </a>
            <small>Ongoing</small>
          </div>
          <div class="col-md-6 text-danger mt-2">
            <a href="{{ url('/admin/tickets?category=Power&status=Onhold') }}">
            <div class="stat-number text-danger" id="power-hold">0</div>
            </a>
            <small>Hold</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="{{ url('/admin/tickets?category=Power&status=Completed') }}">
            <div class="stat-number text-success" id="power-completed">0</div>
            </a>
            <small>Completed</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=Power&status=Completed">
            <div class="stat-number text-success" id="power-today-completed">0</div>
            </a>
            <small>Today</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=Power&status=Completed">
            <div class="stat-number text-success" id="power-yesterday-completed">0</div>
            </a>
            <small>Yesterday</small>
          </div>

        </div>
      </div>
    <!-- View Button -->
    <a href="{{ url('/admin/tickets?category=Power') }}" class="view-btn">View All</a>

    </div>
  </div>

  <!-- Fiber -->
  <div class="col-md-3 mb-2">
    <div class="card ticket-card p-2">
    <div class="d-flex justify-content-between align-items-center mb-2 header-fix">
  <h6 class="mb-0">Fiber</h6>
  <i class="bi bi-router-fill text-info ticket-icon"></i>
</div>
      <div class="ticket-stats mt-2">
        <div class="row text-center">
          <div class="col-md-6 text-primary">
            <a href="{{ url('/admin/tickets?category=Fiber&status=Open') }}">
            <div class="stat-number text-primary" id="fiber-not-started">0</div>
            </a>
            <small>Open Tickets</small>
          </div>
          <div class="col-md-6 text-warning">
            <a href="{{ url('/admin/tickets?category=Fiber&status=OnGoing') }}">
            <div class="stat-number text-warning" id="fiber-ongoing">0</div>
            </a>
            <small>Ongoing</small>
          </div>
          <div class="col-md-6 text-danger mt-2">
            <a href="{{ url('/admin/tickets?category=Fiber&status=Onhold') }}">
            <div class="stat-number text-danger" id="fiber-hold">0</div>
            </a>
            <small>Hold</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="{{ url('/admin/tickets?category=Fiber&status=Completed') }}">
            <div class="stat-number text-success" id="fiber-completed">0</div>
            </a>
            <small>Completed</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=Fiber&status=Completed">
            <div class="stat-number text-success" id="fiber-today-completed">0</div>
            </a>
            <small>Today</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=Fiber&status=Completed">
            <div class="stat-number text-success" id="fiber-yesterday-completed">0</div>
            </a>
            <small>Yesterday</small>
          </div>

        </div>  
      </div>

    <!-- View Button -->
    <a href="{{ url('/admin/tickets?category=Fiber') }}" class="view-btn">View All</a>
    </div>
  </div>

  <!-- OLT -->
  <div class="col-md-3 mb-2">
    <div class="card ticket-card p-2">
      <div class="d-flex justify-content-between align-items-center mb-2 header-fix">
        <h6>OLT</h6>
        <i class="bi bi-hdd-network-fill text-success ticket-icon"></i>
      </div>
      <div class="ticket-stats mt-2">
        <div class="row text-center">
          <div class="col-md-6 text-primary">
            <a href="{{ url('/admin/tickets?category=OLT&status=Open') }}">
            <div class="stat-number text-primary" id="olt-not-started">0</div>
            </a>
            <small>Open Tickets</small>
          </div>
          <div class="col-md-6 text-warning">
            <a href="{{ url('/admin/tickets?category=OLT&status=OnGoing') }}">
            <div class="stat-number text-warning" id="olt-ongoing">0</div>
            </a>
            <small>Ongoing</small>
          </div>
          <div class="col-md-6 text-danger mt-2">
            <a href="{{ url('/admin/tickets?category=OLT&status=Onhold') }}">
            <div class="stat-number text-danger" id="olt-hold">0</div>
            </a>
            <small>Hold</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="{{ url('/admin/tickets?category=OLT&status=Completed') }}">
            <div class="stat-number text-success" id="olt-completed">0</div>
            </a>
            <small>Completed</small>
          </div>
          
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=OLT&status=Completed">
            <div class="stat-number text-success" id="olt-today-completed">0</div>
            </a>
            <small>Today</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=OLT&status=Completed">
            <div class="stat-number text-success" id="olt-yesterday-completed">0</div>
            </a>
            <small>Yesterday</small>
          </div>

        </div>
      </div>
     
        <!-- View Button -->
    <a href="{{ url('/admin/tickets?category=OLT') }}" class="view-btn">View All</a>

    </div>
  </div>

  <!-- Solar -->
  <div class="col-md-3 mb-2">
    <div class="card ticket-card p-2">
      <div class="d-flex justify-content-between align-items-center mb-2 header-fix">
  <h6>Solar</h6>
  <i class="bi bi-brightness-high-fill text-warning ticket-icon"></i>
</div>
      <div class="ticket-stats mt-2">
        <div class="row text-center">
          <div class="col-md-6 text-primary">
            <a href="{{ url('/admin/tickets?category=Solar&status=Open') }}">
            <div class="stat-number text-primary" id="solar-not-started">0</div>
            </a>
            <small>Open Tickets</small>
          </div>
          <div class="col-md-6 text-warning">
            <a href="{{ url('/admin/tickets?category=Solar&status=OnGoing') }}">
            <div class="stat-number text-warning" id="solar-ongoing">0</div>
            </a>
            <small>Ongoing</small>
          </div>
          <div class="col-md-6 text-danger mt-2">
            <a href="{{ url('/admin/tickets?category=Solar&status=Onhold') }}">
            <div class="stat-number text-danger" id="solar-hold">0</div>
            </a>
            <small>Hold</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="{{ url('/admin/tickets?category=Solar&status=Completed') }}">
            <div class="stat-number text-success" id="solar-completed">0</div>
            </a>
            <small>Completed</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=Solar&status=Completed">
            <div class="stat-number text-success" id="solar-today-completed">0</div>
            </a>
            <small>Today</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=Solar&status=Completed">
            <div class="stat-number text-success" id="solar-yesterday-completed">0</div>
            </a>
            <small>Yesterday</small>
          </div>

        </div>
      </div>
    <!-- View Button -->
    <a href="{{ url('/admin/tickets?category=Solar') }}" class="view-btn">View All</a>

    </div>
  </div>

  <!-- Software -->
  <div class="col-md-3 mb-2">
    <div class="card ticket-card p-2">
      <div class="d-flex justify-content-between align-items-center mb-2 header-fix">
  <h6>Software/Hardware</h6>
  <i class="bi bi-motherboard-fill text-info ticket-icon"></i>
</div>
      <div class="ticket-stats mt-2">
        <div class="row text-center">
          <div class="col-md-6 text-primary">
            <a href="{{ url('/admin/tickets?category=Software/Hardware&status=Open') }}">
            <div class="stat-number" id="electronics-not-started">0</div>
            </a>
            <small>Open Tickets</small>
          </div>
          <div class="col-md-6 text-warning">
            <a href="{{ url('/admin/tickets?category=Software/Hardware&status=OnGoing') }}">
            <div class="stat-number text-warning" id="electronics-ongoing">0</div>
            </a>
            <small>Ongoing</small>
          </div>
          <div class="col-md-6 text-danger mt-2">
            <a href="{{ url('/admin/tickets?category=Software/Hardware&status=Onhold') }}">
            <div class="stat-number text-danger" id="electronics-hold">0</div>
            </a>
            <small>Hold</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="{{ url('/admin/tickets?category=Software/Hardware&status=Completed') }}">
             <div class="stat-number text-success" id="electronics-completed">0</div>
             </a>
            <small>Completed</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=Software/Hardware&status=Completed">
            <div class="stat-number text-success" id="electronics-today-completed">0</div>
            </a>
            <small>Today</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=Software/Hardware&status=Completed">
            <div class="stat-number text-success" id="electronics-yesterday-completed">0</div>
            </a>
            <small>Yesterday</small>
          </div>
          

        </div>
      </div>
    <!-- View Button -->
    <a href="{{ url('/admin/tickets?category=Software/Hardware') }}" class="view-btn">View All</a>

    </div>
  </div>

    <!-- battery -->
  <div class="col-md-3 mb-2">
    <div class="card ticket-card p-2">
      <div class="d-flex justify-content-between align-items-center mb-2 header-fix">
  <h6>CCU/Battery</h6>
  <i class="bi bi-battery-half text-warning ticket-icon"></i>
</div>
      <div class="ticket-stats mt-2">
        <div class="row text-center">
          <div class="col-md-6 text-primary">
            <a href="{{ url('/admin/tickets?category=CCU/Battery&status=Open') }}">
            <div class="stat-number text-primary" id="battery-not-started">0</div>
            </a>
            <small>Open Tickets</small>
          </div>
          <div class="col-md-6 text-warning">
            <a href="{{ url('/admin/tickets?category=CCU/Battery&status=OnGoing') }}">
            <div class="stat-number text-warning" id="battery-ongoing">0</div>
            </a>
            <small>Ongoing</small>
          </div>
          <div class="col-md-6 text-danger mt-2">
            <a href="{{ url('/admin/tickets?category=CCU/Battery&status=Onhold') }}">
            <div class="stat-number text-danger " id="battery-hold">0</div>
            </a>
            <small>Hold</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="{{ url('/admin/tickets?category=CCU/Battery&status=Completed') }}">
            <div class="stat-number text-success" id="battery-completed">0</div>
            </a>
            <small>Completed</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=CCU/Battery&status=Completed">
            <div class="stat-number text-success" id="battery-today-completed">0</div>
            </a>
            <small>Today</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=CCU/Battery&status=Completed">
            <div class="stat-number text-success" id="battery-yesterday-completed">0</div>
            </a>
            <small>Yesterday</small>
          </div>

        </div>
      </div>
    <!-- View Button -->
    <a href="{{ url('/admin/tickets?category=CCU/Battery') }}" class="view-btn">View All</a>

    </div>
  </div>


    <!-- Others -->
  <div class="col-md-3 mb-2">
    <div class="card ticket-card p-2">
      <div class="d-flex justify-content-between align-items-center mb-2 header-fix">
  <h6>Others</h6>
  <i class="bi bi-exclamation-triangle-fill text-danger ticket-icon"></i>
</div>
      <div class="ticket-stats mt-2">
        <div class="row text-center">
          <div class="col-md-6 text-primary">
            <a href="{{ url('/admin/tickets?category=CCU/Battery&status=Open') }}">
            <div class="stat-number text-primary" id="other-not-started">0</div>
            </a>
            <small>Open Tickets</small>
          </div>
          <div class="col-md-6 text-warning">
            <a href="{{ url('/admin/tickets?category=CCU/Battery&status=OnGoing') }}">
            <div class="stat-number text-warning" id="other-ongoing">0</div>
            </a>
            <small>Ongoing</small>
          </div>
          <div class="col-md-6 text-danger mt-2">
            <a href="{{ url('/admin/tickets?category=CCU/Battery&status=Onhold') }}">
            <div class="stat-number text-danger" id="other-hold">0</div>
            </a>
            <small>Hold</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="{{ url('/admin/tickets?category=CCU/Battery&status=Completed') }}">
            <div class="stat-number text-success" id="other-completed">0</div>
            </a>
            <small>Completed</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=Others&status=Completed">
            <div class="stat-number text-success" id="other-today-completed">0</div>
            </a>
            <small>Today</small>
          </div>
          <div class="col-md-6 text-success mt-2">
            <a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=Others&status=Completed">
            <div class="stat-number text-success" id="other-yesterday-completed">0</div>
            </a>
            <small>Yesterday</small>
          </div>

        </div>
      </div>
    <!-- View Button -->
    <a href="{{ url('/admin/tickets?category=Others') }}" class="view-btn">View All</a>

    </div>
  </div>


</div>


</div>




@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
   let categoryChart, overallChart;
   let firstLoad = true;
  function loadDashboardData() {
   
     if (firstLoad) {
      // Show spinners only for the first load
      $("#categoryLoader").show();
      $("#categoryChart").hide();
      $("#overallLoader").show();
      $("#overallChart").hide();
    }
   
    $.ajax({
      url: "{{ url('/admin/get_dashboard_data') }}",
      //url: "{{ url('/admin/dashboard-test') }}",
      method: "GET",
      success: function(res) {

        // ---------------- Power ----------------
        //$('#power-total').text(res.categories.power.total);
        $('#power-not-started').text(res.categories.power.notstarted);
        $('#power-ongoing').text(res.categories.power.ongoing);
        $('#power-hold').text(res.categories.power.hold);
        $('#power-hold1').text(res.categories.power.hold);
        $('#power-completed').text(res.categories.power.completed);
        $('#power-today-completed').text(res.categories.power.completed_today);
        $('#power-yesterday-completed').text(res.categories.power.completed_yesterday);

 

        let powerProgress = Math.round((res.categories.power.completed / res.categories.power.total) * 100);
        $('#power-progress').css('width', powerProgress + '%');
        $('#power-progress-text').text(powerProgress + '% Complete');

        // ---------------- Electronics ----------------
        $('#electronics-not-started').text(res.categories.electronics.notstarted);
        $('#electronics-ongoing').text(res.categories.electronics.ongoing);
        $('#electronics-hold').text(res.categories.electronics.hold);
        $('#electronics-hold1').text(res.categories.electronics.hold);
        $('#electronics-completed').text(res.categories.electronics.completed);
        $('#electronics-today-completed').text(res.categories.electronics.completed_today);
        $('#electronics-yesterday-completed').text(res.categories.electronics.completed_yesterday);

        let electronicsProgress = Math.round((res.categories.electronics.completed / res.categories.electronics.total) * 100);
        $('#electronics-progress').css('width', electronicsProgress + '%');
        $('#electronics-progress-text').text(electronicsProgress + '% Complete');

        // ---------------- Solar ----------------
        $('#solar-not-started').text(res.categories.solar.notstarted);
        $('#solar-ongoing').text(res.categories.solar.ongoing);
        $('#solar-hold').text(res.categories.solar.hold);
        $('#solar-hold1').text(res.categories.solar.hold);
        $('#solar-completed').text(res.categories.solar.completed);
        $('#solar-today-completed').text(res.categories.solar.completed_today);
        $('#solar-yesterday-completed').text(res.categories.solar.completed_yesterday);

        let solarProgress = Math.round((res.categories.solar.completed / res.categories.solar.total) * 100);
        $('#solar-progress').css('width', solarProgress + '%');
        $('#solar-progress-text').text(solarProgress + '% Complete');

        // ---------------- Other ----------------
        $('#other-not-started').text(res.categories.other.notstarted);
        $('#other-ongoing').text(res.categories.other.ongoing);
        $('#other-hold').text(res.categories.other.hold);
        $('#other-hold1').text(res.categories.other.hold);
        $('#other-completed').text(res.categories.other.completed);
        $('#other-today-completed').text(res.categories.other.completed_today);
        $('#other-yesterday-completed').text(res.categories.other.completed_yesterday);



        let otherProgress = Math.round((res.categories.other.completed / res.categories.other.total) * 100);
        $('#other-progress').css('width', otherProgress + '%');
        $('#other-progress-text').text(otherProgress + '% Complete');

                // ---------------- olt ----------------
        $('#olt-not-started').text(res.categories.olt.notstarted);
        $('#olt-ongoing').text(res.categories.olt.ongoing);
        $('#olt-hold').text(res.categories.olt.hold);
        $('#olt-hold1').text(res.categories.olt.hold);
        $('#olt-completed').text(res.categories.olt.completed);
        $('#olt-today-completed').text(res.categories.olt.completed_today);
        $('#olt-yesterday-completed').text(res.categories.olt.completed_yesterday);


        let oltProgress = Math.round((res.categories.olt.completed / res.categories.olt.total) * 100);
        $('#olt-progress').css('width', oltProgress + '%');
        $('#olt-progress-text').text(oltProgress + '% Complete');

                // ---------------- battery ----------------
        $('#battery-not-started').text(res.categories.battery.notstarted);
        $('#battery-ongoing').text(res.categories.battery.ongoing);
        $('#battery-hold').text(res.categories.battery.hold);
        $('#battery-hold1').text(res.categories.battery.hold);
        $('#battery-completed').text(res.categories.battery.completed);
        $('#battery-today-completed').text(res.categories.battery.completed_today);
        $('#battery-yesterday-completed').text(res.categories.battery.completed_yesterday);


        let batteryProgress = Math.round((res.categories.battery.completed / res.categories.battery.total) * 100);
        $('#battery-progress').css('width', batteryProgress + '%');
        $('#battery-progress-text').text(batteryProgress + '% Complete');

         // ---------------- fiber ----------------
        $('#fiber-not-started').text(res.categories.fiber.notstarted);
        $('#fiber-ongoing').text(res.categories.fiber.ongoing);
        $('#fiber-hold').text(res.categories.fiber.hold);
        $('#fiber-hold1').text(res.categories.fiber.hold);
        $('#fiber-completed').text(res.categories.fiber.completed);
        $('#fiber-today-completed').text(res.categories.fiber.completed_today);
        $('#fiber-yesterday-completed').text(res.categories.fiber.completed_yesterday);

        let fiberProgress = Math.round((res.categories.fiber.completed / res.categories.fiber.total) * 100);
        $('#fiber-progress').css('width', fiberProgress + '%');
        $('#fiber-progress-text').text(fiberProgress + '% Complete');

        // ---------------- Totals / Extra Stats ----------------
        $('#total-tickets').text(res.tickets.total);
        $('#total-auto-tickets').text(res.tickets.total_auto);
        $('#total-manual-tickets').text(res.tickets.total_manual);
        $('#total-completed').text(res.tickets.completed);
        $('#ongoing-tickets').text(res.tickets.ongoing);
        $('#notstarted-tickets').text(res.tickets.notstarted);
        $('#open_auto').text(res.tickets.auto_notstarted);
        $('#open_manual').text(res.tickets.manual_notstarted);
         $('#onhold-tickets').text(res.tickets.onhold);
        $('#today-onhold').text(res.tickets.today_onhold);
        $('#yesterday-onhold').text(res.tickets.yesterday_onhold);
        $('#today-ongoing').text(res.tickets.today_ongoing);
        $('#yesterday-ongoing').text(res.tickets.yesterday_ongoing);
        $('#today-completed-tickets').text(res.tickets.today_completed);
        $('#yesterday-completed-tickets').text(res.tickets.yesterday_completed);
        $('#permanent_down').text(res.tickets.permanent_down);
         $('#etr_fiber_cut_olt').text(res.tickets.etr_fiber_cut_olt);
        $('#totalpermanent_down').text(res.tickets.permanent_down);
        let totalHold = res.tickets.onhold - res.tickets.permanent_down - res.tickets.etr_fiber_cut_olt;
        $('#r-onhold-tickets').text(totalHold);
        $('#etr_fiber_cut_olttot').text(res.tickets.etr_fiber_cut_olt);


     
        $('#unique-lgd').text(res.unique_lgd);
        $('#gp-total').text(res.gp_total);

      
        let percent_t_completed = res.tickets.total > 0 ? Math.round((res.tickets.completed / res.tickets.total) * 100) : 0;

        $('#complete-ticket-percent').text(percent_t_completed + '%');
   
        // Calculate Down GPs
        let down_gps = res.gp_total - res.unique_lgd;
        $('#down-gps').text(down_gps);

        // Calculate Percentages
       let percent_unique = res.gp_total > 0 ? Math.round((res.unique_lgd / res.gp_total) * 100) : 0;
       let percent_down   = res.gp_total > 0 ? Math.round((down_gps / res.gp_total) * 100) : 0;

       $('#unique-lgd-percent').text(percent_unique + '%');
       $('#down-gps-percent').text(percent_down + '%');


       
       // ---------------- Overall Chart ----------------
        let overallNotStarted = Number(res.tickets.notstarted);
        let overallOngoing   = Number(res.tickets.ongoing);
        let overallHold1     = Number(res.tickets.onhold);
        let overapwn         = Number(res.tickets.permanent_down);
        let overbsnl         = Number(res.tickets.etr_fiber_cut_olt);

        let overallHold = overallHold1 - overapwn - overbsnl;
        let overallTotal = overallNotStarted + overallOngoing + overallHold + overapwn + overbsnl;

        if (firstLoad) {
          let overallCtx = document.getElementById("overallChart").getContext("2d");
          overallChart = new Chart(overallCtx, {
            type: "doughnut",
            data: {
              labels: ["Open Tickets", "Ongoing", "Hold", "Permanent Down","BSNL Scope"],
              datasets: [{
                data: [overallNotStarted, overallOngoing, overallHold, overapwn,overbsnl],
                backgroundColor: ["#007bff", "#ffc107", "#dc3545", "#dc7935",'#6E260E'],
              }],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: "right",
                  labels: {
                    generateLabels: function(chart) {
                      const data = chart.data;
                      const dataset = data.datasets[0];
                      let defaultLabels = data.labels.map((label, i) => {
                        const value = dataset.data[i];
                        return {
                          text: `${label} (${value})`,
                          fillStyle: dataset.backgroundColor[i],
                          strokeStyle: dataset.backgroundColor[i],
                          hidden: !chart.getDataVisibility(i),
                          index: i,
                        };
                      });
                      defaultLabels.push({
                        text: `Total (${overallTotal})`,
                        fillStyle: "#6c757d",
                        strokeStyle: "#6c757d",
                        hidden: false,
                        index: null,
                      });
                      return defaultLabels;
                    },
                  },
                  onClick: function(e, legendItem, legend) {
                    if (legendItem.index === null) return;
                    const ci = legend.chart;
                    ci.toggleDataVisibility(legendItem.index);
                    ci.update();
                  },
                },
              },
            },
          });
        } else {
          // Update only
          overallChart.data.datasets[0].data = [
            overallNotStarted,
            overallOngoing,
            overallHold,
            overapwn,
            overbsnl
          ];
          overallChart.update();
        }

        // ---------------- Category Chart ----------------
        let categories = ["Power", "Fiber", "Solar", "Software", "Other", "OLT", "Battery", "Permanent Down", "BSNL Scope","Administrative"];
        let notStartedData = [
          Number(res.categories.power.notstarted),
          Number(res.categories.fiber.notstarted),
          Number(res.categories.solar.notstarted),
          Number(res.categories.electronics.notstarted),
          Number(res.categories.other.notstarted),
          Number(res.categories.olt.notstarted),
          Number(res.categories.battery.notstarted),
          Number(res.categories.p_down.notstarted),
          Number(res.categories.etr_fiber_cut_olt.notstarted),
          Number(res.categories.administrative.notstarted),
        ];
        let ongoingData = [
          Number(res.categories.power.ongoing),
          Number(res.categories.fiber.ongoing),
          Number(res.categories.solar.ongoing),
          Number(res.categories.electronics.ongoing),
          Number(res.categories.other.ongoing),
          Number(res.categories.olt.ongoing),
          Number(res.categories.battery.ongoing),
          Number(res.categories.p_down.ongoing),
          Number(res.categories.etr_fiber_cut_olt.ongoing),
          Number(res.categories.administrative.ongoing),
        ];
        let holdData = [
          Number(res.categories.power.hold),
          Number(res.categories.fiber.hold),
          Number(res.categories.solar.hold),
          Number(res.categories.electronics.hold),
          Number(res.categories.other.hold),
          Number(res.categories.olt.hold),
          Number(res.categories.battery.hold),
          Number(res.categories.p_down.hold),
          Number(res.categories.etr_fiber_cut_olt.hold),
          Number(res.categories.administrative.hold),
        ];

        let totals = categories.map((cat, i) => notStartedData[i] + ongoingData[i] + holdData[i]);
        let categoryLabels = categories.map((cat, i) => `${cat} (${totals[i]})`);

        if (firstLoad) {
          let categoryCtx = document.getElementById("categoryChart").getContext("2d");
          categoryChart = new Chart(categoryCtx, {
            type: "bar",
            data: {
              labels: categoryLabels,
              datasets: [
                { label: "Open Tickets", data: notStartedData, backgroundColor: "#007bff" },
                { label: "Ongoing", data: ongoingData, backgroundColor: "#ffc107" },
                { label: "Hold", data: holdData, backgroundColor: "#dc3545" },
              ],
            },
            options: {
              responsive: true,
              plugins: { legend: { position: "bottom" } },
              scales: { y: { beginAtZero: true } },
            },
          });
        } else {
          // Update only
          categoryChart.data.labels = categoryLabels;
          categoryChart.data.datasets[0].data = notStartedData;
          categoryChart.data.datasets[1].data = ongoingData;
          categoryChart.data.datasets[2].data = holdData;
          categoryChart.update();
        }

        // ---------------- Hide spinners after first load ----------------
        if (firstLoad) {
          $("#categoryLoader").hide();
          $("#categoryChart").show();
          $("#overallLoader").hide();
          $("#overallChart").show();
          firstLoad = false;
        }
      },
      error: function() {
        console.error("Failed to load dashboard data");
        $("#categoryLoader, #overallLoader").html(
          "<span class='text-danger'>Failed to load data</span>"
        );
      },
    });
  }

  // Initial load
  loadDashboardData();

  // Auto-refresh every 120s
  setInterval(loadDashboardData, 120000);
});
</script>

<script>
$(document).ready(function() {
  function loadDashboardData() {
    $.ajax({
      url: "{{ url('/admin/get_teams_data') }}",
      method: "GET",
      success: function(res) {

        // ---------------- Teams ----------------
        $('#total-teams').text(res.total_teams);
        $('#ongoing-teams').text(res.working_teams);
        $('#t-completed-teams').text(res.completed_teams);
        $('#notstarted-teams').text(res.not_started);
        $('#t-hold-teams').text(res.only_hold_teams);
        $('#no-t-teams').text(res.no_ticket_teams);
        $('#more-then-2').text(res.not_started_morethan2);
        $('#old-tickes-teams').text(res.teams_working_on_old_tickets);


        let ticket_asign_teams = res.total_teams - res.no_ticket_teams;
        $('#asign-teams').text(ticket_asign_teams);


        
        
      },
      error: function() {
        console.error("Failed to load dashboard data");
      }
    });
  }

  // Load data on page load
  loadDashboardData();

  // Auto-refresh every 60 seconds
  setInterval(loadDashboardData, 60000);
});
</script>


<script>
let dashboardMap;
let dashboardMarkers = {};
// let mapMarkers = [];
let activeInfoWindow = null;

const mapIcons = {
    active: '{{ asset("asset/img/marker-user.png") }}',
    offline: '{{ asset("asset/img/map-marker-red.png") }}',
    user: '{{ asset("asset/img/marker-user.png") }}',
};

// Google Maps callback
window.initDashboardMap = function () {
    dashboardMap = new google.maps.Map(document.getElementById('dashboardMap'), {
        center: { lat: 20.8444, lng: 85.1511 },
        zoom: 7,
        minZoom: 3
    });

    // Create legend container
    let legend = document.getElementById('legend');
    legend.innerHTML = `
        <div id="legend-online"><img src="${mapIcons['active']}"> Online: 0</div>
        <div id="legend-offline"><img src="${mapIcons['offline']}"> Offline: 0</div>
    `;
    dashboardMap.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);

    // Initial load + auto-refresh (every 60s)
    loadDashboardMapData();
    setInterval(loadDashboardMapData, 60000);
};

function loadDashboardMapData() {
    // Remove old markers
    // mapMarkers.forEach(m => m.setMap(null));
    // mapMarkers = [];

    $.ajax({
        url: "{{ url('/admin/get_dashboard_map') }}",
        dataType: "json",
        type: "GET",
        success: function (data) {
            let bounds = new google.maps.LatLngBounds();
            let onlineCount = 0, offlineCount = 0;
            let frtOnline = 0, frtOffline = 0;
            let patrollerOnline = 0, patrollerOffline = 0;
            let currentIds = [];

            data.forEach(element => {
                let status = element.service ? element.service.status : element.status;
                let type   = element.type;

                if (status === 'active') onlineCount++;
                else offlineCount++;

                if (type === 2) {
                if (status === 'active') frtOnline++;
                else frtOffline++;
                } else if (type === 5) {
                if (status === 'active') patrollerOnline++;
                else patrollerOffline++;
                }

                let baddress = (element.service && element.service.address)
                    ? element.service.address
                    : `${element.latitude},${element.longitude}`;

                let position = { lat: parseFloat(element.latitude), lng: parseFloat(element.longitude) };
                let icon = mapIcons[status] || mapIcons['user'];
                currentIds.push(element.id);

                if (dashboardMarkers[element.id]) {
                  // Update existing marker
                  let marker = dashboardMarkers[element.id];
                  marker.setPosition(position);
                  marker.setIcon(icon);
                  marker.setTitle(`${element.first_name} ${element.last_name}\n${baddress}`);
                } else {
                  let marker = new google.maps.Marker({
                    position,
                    map: dashboardMap,
                    title: `${element.first_name} ${element.last_name}\n${baddress}`,
                    icon: icon
                  });


              

                let info = new google.maps.InfoWindow({
                    content: `
                        <div style="min-width:200px">
                            <strong>${element.first_name} ${element.last_name}</strong><br>
                            <small>${baddress}</small><br>
                            <span>Status: <b>${status}</b></span>
                        </div>
                    `
                });

                marker.addListener('click', () => {
                    if (activeInfoWindow) activeInfoWindow.close();
                    info.open(dashboardMap, marker);
                    activeInfoWindow = info;
                });
                dashboardMarkers[element.id] = marker;
              }


                bounds.extend(position);
            });
            for (var id in dashboardMarkers) {
                if (!currentIds.includes(parseInt(id))) {
                  dashboardMarkers[id].setMap(null);
                  delete dashboardMarkers[id];
                }
              }
            // Update counts in legend
            document.getElementById('legend-online').innerHTML =
                `<img src="${mapIcons['active']}"> Online: ${onlineCount}`;
            document.getElementById('legend-offline').innerHTML =
                `<img src="${mapIcons['offline']}"> Offline: ${offlineCount}`;

            // ?? Update external counters
            document.getElementById('online-count').textContent = onlineCount;
            document.getElementById('offline-count').textContent = offlineCount;

             document.getElementById('frt-online').textContent = frtOnline;
             document.getElementById('frt-offline').textContent = frtOffline;

             document.getElementById('patroller-online').textContent = patrollerOnline;
             document.getElementById('patroller-offline').textContent = patrollerOffline;

            // Fit map to all markers
            if (!bounds.isEmpty()) {
                dashboardMap.fitBounds(bounds);
            }
        }
    });
}
</script>

<script>
let velocityChart;
let backlogChart;

/* ================= VELOCITY CHART ================= */
function loadVelocity() {

    const range = document.getElementById('velocityRange').value;
    const type  = document.getElementById('velocityType').value;
    const g_type  = document.getElementById('velocityGType').value;

    fetch(`/admin/velocity?range=${range}&type=${type}&g_type=${g_type}`)
        .then(res => res.json())
        .then(data => {
            renderVelocityChart(data);
            renderBacklogChart(data.labels, data.not_started);
        });
}

function renderVelocityChart(data) {

    const ctx = document.getElementById('velocityChart').getContext('2d');
    if (velocityChart) velocityChart.destroy();

    velocityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Assigned',
                    data: data.assigned,
                    borderColor: '#1e88e5',
                    backgroundColor: 'rgba(30,136,229,0.08)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    tension: 0.4
                },
                {
                    label: 'Closed',
                    data: data.closed,
                    borderColor: '#43a047',
                    backgroundColor: 'rgba(67,160,71,0.08)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    tension: 0.4
                },
                {
                    label: 'Hold',
                    data: data.hold,
                    borderColor: '#fb8c00',
                    borderDash: [6,6],
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    tension: 0.4
                },
                {
                    label: 'Not Started',
                    data: data.not_started,
                    borderColor: '#e53935',
                    borderDash: [4,4],
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    tension: 0.4
                },
                 {
                    label: 'On Going',
                    data: data.on_going,
                    borderColor: '#67023d',
                    borderDash: [4,4],
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    tension: 0.4
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            animation: {
                duration: 1200,
                easing: 'easeOutQuart'
            },

            interaction: {
                mode: 'index',
                intersect: false
            },

            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },

                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 10,
                    cornerRadius: 6,
                    callbacks: {
                        title: function (items) {
                            return 'Time : ' + items[0].label;
                        },
                        label: function (item) {
                            return item.dataset.label + ' : ' + item.formattedValue;
                        }
                    }
                }
            },

            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}

/* ================= BACKLOG CHART ================= */
function renderBacklogChart(labels, notStarted) {

    const ctx = document.getElementById('burndownChart').getContext('2d');
    if (backlogChart) backlogChart.destroy();

    backlogChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Actual Backlog (Not Started)',
                    data: notStarted,
                    borderColor: '#e53935',
                    backgroundColor: 'rgba(229,57,53,0.15)',
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            animation: {
                duration: 1000,
                easing: 'easeOutCubic'
            },

            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            },

            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });
}

/* ================= INIT ================= */
loadVelocity();

/* ================= FILTER EVENTS ================= */
document.getElementById('velocityRange').addEventListener('change', loadVelocity);
document.getElementById('velocityType').addEventListener('change', loadVelocity);
document.getElementById('velocityGType').addEventListener('change', loadVelocity);

</script>

<script>
let districtData = [];
let sortKey = null;
let sortDir = 'asc';

function loadDistrictHeatmap() {

    const range  = document.getElementById('districtRange').value;
    const type   = document.getElementById('districtType').value;
    const g_type = document.getElementById('districtGType').value;

    fetch(`/admin/district-heatmap?range=${range}&type=${type}&g_type=${g_type}`)
        .then(res => res.json())
        .then(rows => {
            districtData = rows;
            renderDistrictTable(rows);
        });
}

function renderDistrictTable(rows) {

    let html = '';

    if (!rows.length) {
        html = `<tr><td colspan="9" class="text-center text-muted">No data</td></tr>`;
    }

    rows.forEach(r => {

        let openClass =
            r.not_started > 4 ? 'hm-bad' :
            r.not_started > 1 ? 'hm-warning' :
                                'hm-good';

       let slaClass =
          r.sla_percent <= 25 ? 'hm-good' :
          r.sla_percent <= 50 ? 'hm-warning' :
                                'hm-bad';


        let velocityClass =
            r.net_velocity < 0 ? 'hm-bad' : 'hm-good';

        html += `
            <tr>
                <td><strong>${r.district}</strong></td>
                <td>${r.assigned}</td>
                <td>${r.closed}</td>
                <td>${r.hold}</td>
                <td>${r.ongoing}</td>
                <td class="${openClass}">${r.not_started}</td>
                <td>
                    <span class="text-success">${r.sla_pass}</span> /
                    <span class="text-danger">${r.sla_fail}</span>
                </td>
                <td class="${slaClass}">
                    ${r.closed > 0 ? r.sla_percent + '%' : '�'}
                </td>
                <td class="${velocityClass}">
                    ${r.net_velocity > 0 ? '+' : ''}${r.net_velocity}
                </td>
            </tr>
        `;
    });

    document.getElementById('districtHeatmapBody').innerHTML = html;
}

/* ===== SORTING ===== */
document.querySelectorAll('.heatmap-table th.sortable').forEach(th => {

    th.addEventListener('click', function () {

        const key = this.dataset.key;

        if (sortKey === key) {
            sortDir = sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            sortKey = key;
            sortDir = 'asc';
        }

        document.querySelectorAll('.heatmap-table th')
            .forEach(h => h.classList.remove('sorted-asc', 'sorted-desc'));

        this.classList.add(sortDir === 'asc' ? 'sorted-asc' : 'sorted-desc');

        const sorted = [...districtData].sort((a, b) => {

            let A = a[key];
            let B = b[key];

            if (typeof A === 'string') {
                return sortDir === 'asc'
                    ? A.localeCompare(B)
                    : B.localeCompare(A);
            }
            return sortDir === 'asc' ? A - B : B - A;
        });

        renderDistrictTable(sorted);
    });
});

/* INIT */
loadDistrictHeatmap();

/* FILTER EVENTS */
document.getElementById('districtRange').addEventListener('change', loadDistrictHeatmap);
document.getElementById('districtType').addEventListener('change', loadDistrictHeatmap);
document.getElementById('districtGType').addEventListener('change', loadDistrictHeatmap);
</script>



<!-- Google Maps -->
<!-- <script src="https://maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&callback=initDashboardMap" async defer></script> -->
<script>
    if (typeof google === 'object' && typeof google.maps === 'object') {
        window.initDashboardMap();
    } else {
        window.addEventListener('load', function() {
            if (window.initDashboardMap) {
               window.initDashboardMap();
            }
        });
    }
</script>


@endsection


@endsection
