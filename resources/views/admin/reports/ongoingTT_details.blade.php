@extends('admin.layout.base')

@section('title', 'Reports ')

@section('content')

<div class="content-area dashboard-page py-1" id="main_content">
    <div class="container-fluid">



            <!-- Filter Row -->
            <div class="filter-card">
               <form id="filterForm">
    <div class="filter-row">
        <div class="filter-pill">
            <i class="bi bi-globe-central-south-asia text-info"></i>
            <select name="zone_id" id="zone_id">
                <option value="">Zonal</option>
                @foreach($zonals as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->Name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-pill">
            <i class="bi bi-geo-alt-fill text-danger"></i>
            <select name="district_id" id="district_id">
                <option value="">All Districts</option>
            </select>
        </div>

          <div class="filter-pill">
            <i class="bi bi-person-walking text-primary"></i>
            <select name="role_id" id="role_id">
                <option value="">All roles</option>
                <option value="2">FRT</option>
                <option value="5">Patroller</option>
            </select>
        </div>
   
          <div class="filter-pill">
            <i class="bi bi-person-walking text-primary"></i>
            <select name="range" id="range">
                <option value="">Range</option>
                <option value="0_4">0 To 4 Hr</option>
                <option value="4_10">4 To 10 Hr</option>
                <option value="10_24">10 To 24 Hr</option>
                <option value="24_48">24 To 48 Hr</option>
                <option value="above_48">Above 48 Hr</option>
            </select>
        </div>



        <div class="filter-pill">
            <i class="bi bi-person-fill text-success"></i>
            <select name="member_id" id="member_id">
                <option value="">Select Employee</option>
            </select>
        </div>
        
        <div class="filter-pill">
            <i class="bi bi-calendar-week-fill text-warning"></i>
            <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}">
        </div>
        <div class="filter-pill">
            <i class="bi bi-calendar-week-fill text-warning"></i>
            <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}">
        </div>
        

      
    </div>

    <input type="hidden" value="{{ request('status') }}" name="status">
</form>
</div>

                    <!-- Summary Cards -->
            <div class="stats-row">
                <div class="stat-card stat-total">
                    <h3></h3>
                    <p>New Tickets</p>
                </div>
                
                <div class="stat-card stat-notstarted">
                     <h3></h3>
                    <p>All Open</p>
                </div>
                <div class="stat-card stat-ongoing">
                    <h3></h3>
                    <p>Today Ongoing</p>
                </div>
                <div class="stat-card stat-onhold">
                    <h3></h3>
                    <p>Today On Hold</p>
                </div>
                <div class="stat-card stat-completed">
                    <h3></h3>
                    <p>Today Completed</p>
                </div>
                <!--<div class="stat-card stat-permanent-down">
                     <h3></h3>
                    <p>Distance</p>
                </div>--->
            </div>



<div class="table-wrapper">
<div class="table-responsive">
            <table id="frtTable" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>Zone</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Role</th>
                        <th>New Tickets</th>
                        <th>Open</th>
                        <th>Ongoing</th>
                        <th>On Hold</th>
                        <th>Completed</th>
                        <th>Track</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

</div>

</div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

    let firstLoad = true; // ? Track if it’s the initial load (from URL params)

    // ===========================
    // Get URL Query Parameters
    // ===========================
    function getQueryParams() {
        const params = {};
        const queryString = window.location.search.substring(1);
        const pairs = queryString.split("&");
        for (const pair of pairs) {
            if (!pair) continue;
            const [key, value] = pair.split("=");
            params[decodeURIComponent(key)] = decodeURIComponent(value || "");
        }
        return params;
    }

    // ===========================
    // Load Employees Dynamically
    // ===========================
    function loadEmployees(districtId, roleId) {
        let url = "{{ url('admin/get_employees') }}";
        if (districtId) url += "/" + districtId;
        if (roleId) url += "?role_id=" + roleId;

        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function(data) {
                $('#member_id').empty().append('<option value="">Select Employee</option>');
                $.each(data, function(key, employee) {
                    let typeLabel = (employee.type == 2)
                        ? 'FRT'
                        : (employee.type == 5)
                        ? 'Patroller'
                        : 'Other';
                    let fullName = employee.first_name + ' ' + employee.last_name + ' (' + typeLabel + ')';
                    $('#member_id').append('<option value="'+ employee.id +'">'+ fullName +'</option>');
                });
            }
        });
    }

    // ===========================
    // Load Districts When Zone Changes
    // ===========================
    $('#zone_id').on('change', function() {
        let zoneId = $(this).val();
        if (zoneId) {
            $.ajax({
                url: "{{ url('admin/get_districts') }}/" + zoneId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#district_id').empty().append('<option value="">All Districts</option>');
                    $.each(data, function(key, district) {
                        $('#district_id').append('<option value="'+ district.id +'">'+ district.name +'</option>');
                    });
                    $('#member_id').empty().append('<option value="">Select Employee</option>');

                    // ?? Auto-load employees for this zone’s first district (optional)
                    const firstDistrict = data.length > 0 ? data[0].id : '';
                    const roleId = $('#role_id').val();
                    if (firstDistrict) loadEmployees(firstDistrict, roleId);
                }
            });
        } else {
            $('#district_id').empty().append('<option value="">All Districts</option>');
            $('#member_id').empty().append('<option value="">Select Employee</option>');
        }
        loadFrtDetails();
    });

    // ===========================
    // Reload Employees and Table on Filter Change
    // ===========================
    $('#district_id, #role_id, #member_id, #from_date, #to_date, #status').on('change', function() {
        if (this.id === 'district_id' || this.id === 'role_id') {
            loadEmployees($('#district_id').val(), $('#role_id').val());
        }
        loadFrtDetails();
    });

    // ===========================
    // Reset All Filters
    // ===========================
    $('#resetFilters').on('click', function() {
        $('#zone_id').val('');
        $('#district_id').empty().append('<option value="">All Districts</option>');
        $('#role_id').val('');
        $('#status').val('');
        $('#member_id').empty().append('<option value="">Select Employee</option>');
        $('#from_date').val('');
        $('#to_date').val('');
        loadFrtDetails();
    });

    // ===========================
    // Load FRT Data Table
    // ===========================
    function loadFrtDetails() {
        const urlParams = getQueryParams();

        // ? On first load, use URL params (for deep linking)
        // After that, use dropdown filters
        let range       = firstLoad ? (urlParams.range || '') : $('#range').val();
        let roleVal     = firstLoad ? (urlParams.type || '') : $('#role_id').val();
        let zone_id     = firstLoad ? (urlParams.zone_id || '') : $('#zone_id').val();
        let district_id = $('#district_id').val() || '';
        let member_id   = $('#member_id').val() || '';
        let from_date   = $('#from_date').val() || '';
        let to_date     = $('#to_date').val() || '';

        if (roleVal == 2) {
          roleVal = 'frt';
             } else if (roleVal == 5) {
              roleVal = 'patroller';
            }

        let filters = {};
        if (range) filters.range = range;
        if (roleVal) filters.type = roleVal;
        if (zone_id) filters.zone_id = zone_id;
        if (district_id) filters.district_id = district_id;
        if (member_id) filters.member_id = member_id;
        if (from_date) filters.from_date = from_date;
        if (to_date) filters.to_date = to_date;

        const apiUrl = "{{ route('admin.ongoing_ticket_range') }}" + '?' + $.param(filters);
        console.log('API URL:', apiUrl);

        $.ajax({
            url: apiUrl,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#frtTable tbody').html('<tr><td colspan="9" class="text-center text-muted">Loading...</td></tr>');
            },
            success: function(res) {
                if ($.fn.DataTable.isDataTable('#frtTable')) {
                    $('#frtTable').DataTable().destroy();
                }

    // ===========================
    //  SUMMARIZE ALL COUNTS
    // ===========================
    let totalTickets = 0,
        openTickets = 0,
        ongoingTickets = 0,
        onHoldTickets = 0,
        completedTickets = 0;

    (res.list || []).forEach(item => {
        totalTickets     += parseInt(item.total_tickets)     || 0;
        openTickets      += parseInt(item.pending_tickets)   || 0;
        ongoingTickets   += parseInt(item.pickup_tickets)    || 0;
        onHoldTickets    += parseInt(item.hold_tickets)      || 0;
        completedTickets += parseInt(item.completed_tickets) || 0;
    });

      $('.stat-total h3').text(totalTickets);
    $('.stat-notstarted h3').text(openTickets);
    $('.stat-ongoing h3').text(ongoingTickets);
    $('.stat-onhold h3').text(onHoldTickets);
    $('.stat-completed h3').text(completedTickets);

                 const fromDate = res.fromDate || '';
                 const toDate   = res.toDate   || '';

                $('#frtTable').DataTable({
                    data: res.list || [],
                    dom: 'Bfrtip',
                    buttons: [
                   { extend: 'excel', text: 'Excel' },
                   { extend: 'csv', text: 'CSV' },
                   { extend: 'pdf', text: 'PDF' },
                   { extend: 'print', text: 'Print' }
                   ],
                    columns: [
                        { data: 'zone_name', title: 'Zone' },
                        { data: null, title: 'Name', render: d => `${d.first_name} ${d.last_name}` },
                        { data: 'mobile', title: 'Mobile' },
                        { data: 'type', title: 'Role', render: d => d == 2 ? 'FRT' : 'Patroller' },
                        { data: 'total_tickets', title: 'New Tickets',
                          render: { _: data => parseInt(data) || 0,
                           display: function (data, type, row) {
                                 let value = parseInt(data) || 0;
                                 let color = value === 0 ? "red" : value <= 1 ? "orange" : "green";
                                 let url = `{{ url('admin/tickets') }}?provider_id=${row.provider_id}&c_from_date=${fromDate}&c_to_date=${toDate}`;
                                        return `<a href="${url}" target="_blank" style="color:${color}; font-weight:bold;">${value}</a>`;
                                 }
                              }
 

                        },
                        { data: 'pending_tickets', title: 'Open',
                          render: { _: data => parseInt(data) || 0,
                           display: function (data, type, row) {
                                 let value = parseInt(data) || 0;
                                 let color = value === 0 ? "green" : value <= 1 ? "orange" : "red";
                                 let url = `{{ url('admin/tickets') }}?provider_id=${row.provider_id}&status=Open`;
                                        return `<a href="${url}" target="_blank" style="color:${color}; font-weight:bold;">${value}</a>`;
                                 }
                              }

                        },
                        {
    data: null,
    title: 'Ongoing',

    render: function (data, type, row) {

        let pickup = parseInt(row.pickup_tickets) || 0;
        let old_ongoing = parseInt(row.old_ongoing) || 0;

        // TOTAL ONGOING
        let totalOngoing = pickup + old_ongoing;

        // COLOR RULES
        let color = totalOngoing === 0 ? "red" :
                    totalOngoing <= 1 ? "orange" : "green";

        // URL
        let url = `{{ url('admin/tickets') }}?provider_id=${row.provider_id}&status=OnGoing`;

        return `<a href="${url}" target="_blank" 
                    style="color:${color}; font-weight:bold;">
                    ${totalOngoing}
                </a>`;
    }
}
,
                        { data: 'hold_tickets', title: 'On Hold',
                            render: { _: data => parseInt(data) || 0,
                           display: function (data, type, row) {
                                 let value = parseInt(data) || 0;
                                 let color = value === 0 ? "green" : value <= 1 ? "orange" : "red";
                                 let url = `{{ url('admin/tickets') }}?provider_id=${row.provider_id}&status=Onhold&from_date=${fromDate}&to_date=${toDate}`;
                                        return `<a href="${url}" target="_blank" style="color:${color}; font-weight:bold;">${value}</a>`;
                                 }
                              }

                         },
                        { data: 'completed_tickets', title: 'Manual Completed',
                            render: { _: data => parseInt(data) || 0,
                           display: function (data, type, row) {
                                 let value = parseInt(data) || 0;
                                 let color = value === 0 ? "red" : value <= 1 ? "orange" : "green";
                                 let url = `{{ url('admin/tickets') }}?provider_id=${row.provider_id}&status=Completed&searchinfo=Manual&from_date=${fromDate}&to_date=${toDate}`;
                                        return `<a href="${url}" target="_blank" style="color:${color}; font-weight:bold;">${value}</a>`;
                                 }
                              }
                        },
                        { data:null, title: 'Track',
                              render: {
                                 display: function (data, type, row) {
                                   let url = `{{ url('admin/track') }}?provider_id=${row.provider_id}`;
                                   return `
                                    <a href="${url}" target="_blank" title="Track">
                                     <i class="fa fa-map-marker" style="font-size:18px; color:#007bff;"></i>
                                   </a>
                                    `;
                                    }
                               }
                            
                        }
                    ],
                    pageLength: 10,
                    scrollX: true
                });

                // ? Switch off URL-based loading after first successful load
                firstLoad = false;
            },
            error: function(err) {
                console.error(err);
                $('#frtTable tbody').html('<tr><td colspan="9" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }

    // ===========================
    // Initial Load
    // ===========================
    loadFrtDetails();
});
</script>
@endsection

@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">



<style>
/* Header row styling */
.dashboard-page {background-color: #f8fafc;}
.header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 20px;
}
.header-row .btn {
    border-radius: 25px;
    font-size: 14px;
    padding: 6px 16px;
}

/* Status Tabs */
.nav-cstm .nav-link-cstm {
    font-weight: 600;
    color: #636f73 !important;
    border: none;
    padding: 6px 15px;
    border-radius: 20px;
    transition: 0.2s;
}
.nav-cstm .nav-link-cstm.active {
    background: #2b3eb1 !important;
    color: #fff !important;
}
.nav-cstm .nav-link-cstm:hover {
    background: #edf1f2;
}

/* Filter card */
  .filter-card {
    background: #fff;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
 /* Filter Pills */
    .filter-pill {
        border-radius: 12px;
        padding: 6px 15px;
        border: 1px solid #e0e0e0;
        background: #fff;
        font-size: 12px;
        font-weight: 300;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .filter-pill select,
    .filter-pill input {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        font-size: 12px;
        padding: 0;
        height: auto !important;
    }
    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        
    }
    .btn-apply {
        border-radius: 25px;
        padding: 6px 20px;
    }

/* Summary Cards */
.stats-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}
.stat-card {
    flex: 1;
    min-width: 180px;
    background: #fff;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    text-align: center;
}
.stat-card h3 {
    font-size: 22px;
    margin: 0;
    font-weight: bold;
}
.stat-card p {
    margin: 5px 0 0;
    font-size: 14px;
    color: #6c757d;
}
.stat-total { border-top: 3px solid #007bff; }
.stat-notstarted { border-top: 3px solid #FFDE00; }
.stat-ongoing { border-top: 3px solid #02E9FA; }
.stat-onhold { border-top: 3px solid #FA2602; }
.stat-completed { border-top: 3px solid #02FA2F; }
.stat-permanent-down { border-top: 3px solid #dc7935; }



/* ===== Modern Inline Table (like 2nd screenshot) ===== */

/* Wrap table inside a container */
/* Table container */
.table-wrapper {
  background: #fff;
  border: 1px solid #e5e7eb;  /* light gray border */
  border-radius: 12px;
  overflow: hidden; /* keeps radius clean */
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}

/* Table reset */
.new-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

/* Table header */
.new-table thead th {
  background: #f9fafb;
  font-weight: 500;
  padding: 12px;
  color: #374151;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.new-table tbody tr{
  font-size:12px;
  border-left: 1px solid red;
  border-radius: 15px;
 }

.new-table tbody tr small{
  font-size:11px;
  color:#636161;
  font-weight:500;
 }



/* Table cells */

.new-table tbody td {
  padding: 14px 12px;
  color: #111827;
  vertical-align: middle;
  border-bottom: 1px solid #e5e7eb !important;
}

/* GP link */
.gp-link {
  font-weight: 600;
  color: #2563eb;
  text-decoration: none;
}
.gp-link:hover {
  text-decoration: underline;
}

/* Tickets badge */
.ticket-badge {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}
.ticket-danger { background: #fee2e2; color: #dc2626; }
.ticket-success { background: #dcfce7; color: #16a34a; }

/* Quick action */
.quick-view {
  color: #2563eb;
  font-weight: 500;
  cursor: pointer;
}


.bleft-notstarted { border-left: 3px solid #FFDE00; }
.bleft-ongoing { border-left: 3px solid #02E9FA; }
.bleft-onhold { border-left: 3px solid #FA2602; }
.bleft-completed { border-left: 3px solid #02FA2F; }
.tag-notstarted {background:#FFDE00;}
.tag-ongoing {background:#04C2A2;}
.d-size i{font-size: 9px;}
.d-size span {font-size: 11px;}
.d-size .bld{font-weight:500;}

</style>

@endsection
