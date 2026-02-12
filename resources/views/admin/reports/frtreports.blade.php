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
                <option value="3">Zonal incharge</option>
                <option value="4">District incharge</option>
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

        <div class="filter-pill">
            <i class="bi bi-bag-fill text-primary"></i>
            <select name="generated_type" id="generated_type">
                <option value="">Generated Type</option>
                <option value="">All</option>
                <option value="Auto">NMS Auto</option>
                <option value="Manual">Manual</option>
            </select>
        </div>

          <div class="filter-pill">
            <i class="bi bi-bag-fill text-primary"></i>
            <select name="purpose" id="purpose">
                <option value="">purpose</option>
                <option value="spare">Spare Fiber Core Rectification</option>
                <option value="otdr">OTDR Trace</option>
                <option value="patrolling">Route Patrolling</option>
                <option value="survey">Field Survey</option>
                <option value="fiber">Fiber Rectification</option>
            </select>
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
                    <div class="badge-sub"><span class="text-success">Auto : <span id="total-auto">0</span></span> | <span class="text-primary"> Manual : <span id="total-manual">0</span></span>

                    </div>
                </div>
                
                <div class="stat-card stat-notstarted">
                     <h3></h3>
                    <p>All Open</p>
                    <div class="badge-sub"><span class="text-success">Auto : <span id="notstarted-auto">0</span></span> | <span class="text-primary"> Manual : <span id="notstarted-manual">0</span></span>

                    </div>

                </div>
                <div class="stat-card stat-ongoing">
                    <h3></h3>
                    <p>Today Ongoing</p>
                     <div class="badge-sub"><span class="text-success">Auto : <span id="ongoing-auto">0</span></span> | <span class="text-primary"> Manual : <span id="ongoing-manual">0</span></span>

                    </div>

                </div>
                <div class="stat-card stat-onhold">
                    <h3></h3>
                    <p>Today On Hold</p>
                     <div class="badge-sub"><span class="text-success">Auto : <span id="onhold-auto">0</span></span> | <span class="text-primary"> Manual : <span id="onhold-manual">0</span></span>

                    </div>

                </div>
                <div class="stat-card stat-completed">
                    <h3></h3>
                    <p>Today Completed</p>
                     <div class="badge-sub"><span class="text-success">Auto : <span id="completed-auto">0</span></span> | <span class="text-primary"> Manual : <span id="completed-manual">0</span></span>

                    </div>

                </div>
                <div class="stat-card stat-permanent-down">
                     <h3></h3>
                    <p>Distance</p>
                </div>
            </div>



<div class="table-wrapper">
  <table  id="table-5" class="new-table nowrap display" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Zonal</th>
                        <th>District</th>
                        <th>Role</th>
                        <th>Name</th>
                        @if(auth()->user()->role != 'client' )
                        <th>Contact No</th>
                        @endif
                        <th>Attendance %</th>
                        <th>Distance Covered (km)</th> 
                        <th>Today Assigned</th>                      
                        <th>Open Tickets</th> 
                        <th>Manual Completed</th>                      
                        <th>Auto Completed</th>
                        <th>Ongoing</th>
                        <th>ON HOLD</th>
                        <th>Selfie Captured (Y/N)</th>
                        <!--<th>Avg TAT (hrs)</th>
                        <th>SLA Breaches</th>-->
        
                        
                    </tr>
                </thead>
                <tbody>
  
                </tbody>
</table>


</div>

</div>
</div>
@endsection

@section('scripts')

<script>
document.getElementById('resetFilters').addEventListener('click', function() {
    // Reset all filters
    document.getElementById('searchzonelist').value = "";
    document.getElementById('searchblocklist').value = "";
    document.getElementById('getblock').value = "";
    document.getElementById('from_date').value = "";
    document.getElementById('to_date').value = "";
    document.getElementById('generated_type').value = "";


    // Reload the page without filters
    //window.location.href = "{{ route('admin.reports') }}";
});
</script>

<script>
$(document).ready(function() {

    function loadEmployees(districtId, roleId) {
        let url = "{{ url('admin/get_employees') }}";

        if (districtId) {
            url += "/" + districtId;
        }

        if (roleId) {
            url += "?role_id=" + roleId;
        }

        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function(data) {
                $('#member_id').empty();
                $('#member_id').append('<option value="">Select Employee</option>');
                $.each(data, function(key, employee) {
                    var typeLabel = '';
                    if (employee.type == 2) {
                        typeLabel = 'FRT';
                    } else if (employee.type == 5) {
                        typeLabel = 'Petroller';
                    } else if (employee.type == 3) {
                        typeLabel = 'Zonal incharge';
                    } else if (employee.type == 4) {
                        typeLabel = 'District incharge';
                    } else {
                        typeLabel = 'Other';
                    }

                    var fullName = employee.first_name + ' ' + employee.last_name + ' (' + typeLabel + ')';
                    $('#member_id').append('<option value="'+ employee.id +'">'+ fullName +'</option>');
                });
            }
        });
    }

    // Zone ? District
    $('#zone_id').on('change', function () {
        var zoneId = $(this).val();

        if(zoneId) {
            $.ajax({
                url: "{{ url('admin/get_districts') }}/" + zoneId, 
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#district_id').empty().append('<option value="">All Districts</option>');
                    $.each(data, function(key, district) {
                        $('#district_id').append('<option value="'+ district.id +'">'+ district.name +'</option>');
                    });

                    // reset employee list
                    $('#member_id').empty().append('<option value="">Select Employee</option>');
                }
            });
        } else {
            $('#district_id').empty().append('<option value="">All Districts</option>');
            $('#member_id').empty().append('<option value="">Select Employee</option>');
        }
    });

    // District change ? load employees (optionally filter by role)
    $('#district_id').on('change', function () {
        var districtId = $(this).val();
        var roleId = $('#role_id').val();

        if(districtId || roleId) {
            loadEmployees(districtId, roleId);
        } else {
            $('#member_id').empty().append('<option value="">Select Employee</option>');
        }
    });

    // Role change ? reload employees based on selected district
    $('#role_id').on('change', function () {
        var districtId = $('#district_id').val();
        var roleId = $(this).val();

        if(districtId || roleId) {
            loadEmployees(districtId, roleId);
        } else {
            $('#member_id').empty().append('<option value="">Select Employee</option>');
        }
    });

});
</script>

<script>
$(document).ready(function () {
      let buttonsArray = [
        @if(auth()->user()->role != 'client')
        'copy', 'csv', 'excel', 'pdf', 'print'
        @endif
    ];
    var table = $('#table-5').DataTable({
        processing: false,
        serverSide: false,
        ajax: {
            url: "{{ route('admin.get_frtreport') }}",
            data: function (d) {
                d.zone_id     = $('#zone_id').val();
                d.district_id = $('#district_id').val();
                d.member_id   = $('#member_id').val();
                d.from_date   = $('input[name="from_date"]').val();
                d.to_date     = $('input[name="to_date"]').val();
                d.status      = $('input[name="status"]').val();
                d.role_id     = $('#role_id').val();
                d.generated_type  = $('#generated_type').val();
                d.purpose  = $('#purpose').val();

            },
            dataSrc: function(json) {
                let totalTickets = 0,
                    totalOpen = 0,
                    totalManual = 0,
                    totalAuto = 0,
                    totalOnHold = 0,
                    totalDistance = 0,
                    totalOngoing = 0;
             
                    totalAutoOngoing = 0;
                    totalManualOngoing = 0;
                    totalAutoOnHold = 0,
                    totalManualOnHold = 0,
                    totalAutoOpen = 0,
                    totalManualOpen = 0,
                    totalAutoTickets = 0,
                    totalManualTickets = 0,

                json.data.forEach(row => {
                    totalTickets += parseInt(row.tickets_assigned) || 0;
                    totalAutoTickets += parseInt(row.tickets_auto_assigned) || 0;
                    totalManualTickets += parseInt(row.tickets_manual_assigned) || 0;

                    totalOpen += parseInt(row.open_tickets) || 0;
                    totalAutoOpen += parseInt(row.open_auto_tickets) || 0;
                    totalManualOpen += parseInt(row.open_manual_tickets) || 0;

                    totalManual += parseInt(row.manual_completed) || 0;
                    totalAuto += parseInt(row.auto_completed) || 0;

                    totalOnHold += parseInt(row.tickets_onhold) || 0;
                    totalAutoOnHold += parseInt(row.tickets_auto_onhold) || 0;
                    totalManualOnHold += parseInt(row.tickets_manaul_onhold) || 0;

                    totalOngoing += parseFloat(row.tickets_accepted) || 0;
                    totalAutoOngoing += parseFloat(row.tickets_auto_accepted) || 0;
                    totalManualOngoing += parseFloat(row.tickets_manual_accepted) || 0;

                    totalDistance += parseFloat(row.distance) || 0;
                    
                });

                $('.stat-total h3').text(totalTickets);
                $('.stat-notstarted h3').text(totalOpen);
                $('.stat-ongoing h3').text(totalOngoing);
                $('.stat-onhold h3').text(totalOnHold);
                $('.stat-completed h3').text(totalManual + totalAuto);
                $('.stat-permanent-down h3').text(totalDistance.toFixed(2) + ' km');

                $('#total-auto').text(totalAutoTickets);
                $('#total-manual').text(totalManualTickets);
                $('#notstarted-auto').text(totalAutoOpen);
                $('#notstarted-manual').text(totalManualOpen);
                $('#ongoing-auto').text(totalAutoOngoing);
                $('#ongoing-manual').text(totalManualOngoing);
                $('#completed-auto').text(totalAuto);
                $('#completed-manual').text(totalManual);
                $('#onhold-auto').text(totalAutoOnHold);
                $('#onhold-manual').text(totalManualOnHold);




                return json.data;
            }
        },
        columns: [
            {
             data: null,
             orderable: false,
             searchable: false,
             render: function (data, type, row, meta) {
             return meta.row + 1;
                }
             },
            { data: 'zone', name: 'zone' },
            { data: 'district', name: 'district' },
            { 
                data: 'role',
                render: {
                    _: data => data, 
                    display: function (data) {
                        if (data == 2) return 'FRT';
                        if (data == 5) return 'Patroller';
                        if (data == 3) return 'Zonal incharge';
                        if (data == 4) return 'District incharge';
                        return 'Others';
                    }
                }
            },
            { data: 'name', name: 'name',
                 render: function (data, type, row) {
                 let url = `{{ url('admin/staff_details/${row.provider_id}') }}`;
                 return `<a href="${url}" target="_blank" style="font-weight:bold;">${data}</a>`;
                }
             },
            @if(auth()->user()->role != 'client')
            { data: 'contact', name: 'contact' },
            @endif
             { 
                data: 'attendance', 
                name: 'attendance',
                render: {
                    _: data => parseFloat(data) || 0,
                    display: function (data, type, row) {
                        let value = parseFloat(data) || 0;
                        let color = value < 50 ? 'red' : value < 80 ? 'orange' : 'green';
                        let url = `{{ url('admin/staff_details/${row.provider_id}') }}`;
                        return `<a href="${url}" target="_blank" style="color:${color}; font-weight:bold;">${value}%</a>`;
                    }
                }
            },
            { 
                data: 'distance', 
                name: 'distance',
                render: {
                    _: data => parseFloat(data) || 0,
                    display: function (data, type, row) {
                        if (!data || data == 0) return '<span style="color:red;font-weight:bold;">0 km</span>';
                        let today = new Date().toISOString().slice(0, 10);
                        let url = `{{ url('admin/staff_livetrack/${row.provider_id}?date=${today}') }}`;
                        return `<a href="${url}" target="_blank" style="font-weight:bold;">${data} km</a>`;
                    }
                }
            },
            { data: 'tickets_assigned', name: 'tickets_assigned' },
            { 
                data: 'open_tickets',
                name: 'open_tickets',
                render: {
                    _: data => parseInt(data) || 0,
                    display: function (data, type, row) {
                        let value = parseInt(data) || 0;
                        let color = value === 0 ? "green" : value <= 1 ? "orange" : "red";
                        
                        let url = `{{ url('admin/tickets?provider_id=${row.provider_id}&status=Open') }}`;
                        return `<a href="${url}" target="_blank" style="color:${color}; font-weight:bold;">${value}</a>`;
                    }
                }
            },
            { data: 'manual_completed', name: 'manual_completed', 

              render: {
                    _: data => parseInt(data) || 0,
                    display: function (data, type, row) {
                        let value = parseInt(data) || 0;
                        let color = value === 0 ? "red" : value <= 1 ? "orange" : "green";
                        
                        let url = `{{ url('admin/tickets?provider_id=${row.provider_id}&status=Completed&searchinfo=Manual&from_date=${row.fromDate}&to_date=${row.toDate}') }}`;
                        return `<a href="${url}" target="_blank" style="color:${color}; font-weight:bold;">${value}</a>`;
                    }
                }
        
            },
            { data: 'auto_completed', name: 'auto_completed', 
             render: {
                    _: data => parseInt(data) || 0,
                    display: function (data, type, row) {
                        let value = parseInt(data) || 0;
                        let color = value === 0 ? "red" : value <= 1 ? "orange" : "green";
                        
                        let url = `{{ url('admin/tickets?provider_id=${row.provider_id}&status=Completed&searchinfo=Auto&from_date=${row.fromDate}&to_date=${row.toDate}') }}`;
                        return `<a href="${url}" target="_blank" style="color:${color}; font-weight:bold;">${value}</a>`;
                    }
                }

            
            },
            { data: 'tickets_accepted', name: 'tickets_accepted',
             render: {
                    _: data => parseInt(data) || 0,
                    display: function (data, type, row) {
                        let value = parseInt(data) || 0;
                        let color = value === 0 ? "red" : value <= 1 ? "orange" : "green";
                        
                        let url = `{{ url('admin/tickets?provider_id=${row.provider_id}&status=OnGoing&from_date=${row.fromDate}&to_date=${row.toDate}') }}`;
                        return `<a href="${url}" target="_blank" style="color:${color}; font-weight:bold;">${value}</a>`;
                    }
                }


            },
            { data: 'tickets_onhold', name: 'tickets_onhold',
             render: {
                    _: data => parseInt(data) || 0,
                    display: function (data, type, row) {
                        let value = parseInt(data) || 0;
                        let color = value === 0 ? "green" : value <= 1 ? "orange" : "red";
                        
                        let url = `{{ url('admin/tickets?provider_id=${row.provider_id}&status=Onhold&from_date=${row.fromDate}&to_date=${row.toDate}') }}`;
                        return `<a href="${url}" target="_blank" style="color:${color}; font-weight:bold;">${value}</a>`;
                    }
                }

            },
            { data: 'selfie', name: 'selfie' },
        ],
        scrollX: true,
        responsive: false,
        searching: false,
        paging: false,
        info: false,
        ordering: true,
        dom: 'Bfrtip',
        buttons: buttonsArray
    });

    // ===== SERIAL NUMBER HANDLER =====
table.on('order.dt search.dt draw.dt', function () {
    table.column(0, { search: 'applied', order: 'applied' })
        .nodes()
        .each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
}).draw();

    $('.btn-apply').on('click', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#zone_id, #district_id, #member_id, #role_id, #generated_type, #purpose, input[name="from_date"], input[name="to_date"]').on('change', function () {
        table.ajax.reload();
    });

});
</script>

@endsection


@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">



<style>
/* Header row styling */

.new-table td:first-child {
    font-weight: 600;
    color: #2563eb;
}
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

 .badge-sub { 
    font-size: 0.9rem; 
    font-weight: 800; 
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
