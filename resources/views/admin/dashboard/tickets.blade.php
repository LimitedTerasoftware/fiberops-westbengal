@extends('admin.layout.base')

@section('title', 'Tickets Dashboard ')

@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
@endsection


@section('content')

@php
    $user = Session::get('user');
    $DistId = null; 
    if ($user && isset($user->district_id)) {
        $DistId = $user->district_id;
    }
@endphp
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
  overflow-x: auto; 

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

<div class="content-area dashboard-page py-1" id="main_content">
    <div class="container-fluid">
        <div class="box box-block ">

            <!-- Header Row -->
            <div class="header-row">
                <!-- Status Tabs -->
                <ul class="nav nav-pills nav-cstm mb-0">
                    <li class="nav-item">
                        <a href="{{ route('admin.tickets1') }}" 
                           class="nav-link nav-link-cstm {{ @Request::get('status') == ''? 'active' : '' }}">
                           All
                        </a>
                    </li>
                    @foreach ($ticket_status as $status)
                        <li class="nav-item">
                            <a href="{{ route('admin.tickets1',['status' => $status]) }}" 
                               class="nav-link nav-link-cstm {{ $status == @Request::get('status') ? 'active' : '' }}">
                               {{ $status }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Action Buttons -->
                  @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin' || auth()->user()->role=='district_incharge')
                <div class="mt-2 mt-md-0">
                    <a href="{{ route('admin.import') }}" class="btn btn-success mr-2">
                        <i class="fa fa-upload"></i> Upload CSV
                    </a>
                    <a href="{{ route('admin.tickets.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Add Ticket
                    </a>
                </div>
                @endif
            </div>


            <!-- Filter Row -->
            <div class="filter-card">
                <form action="{{route('admin.tickets1', $query_params)}}" method="GET">
                    <div class="filter-row">
                        <div class="filter-pill">
                            <i class="bi bi-search text-muted"></i>
                            <input type="text" name="searchinfo" placeholder="Search..." value="{{ @Request::get('searchinfo') }}">
                        </div>
                          <div class="filter-pill">
                            <i class="bi bi-globe-central-south-asia text-info"></i>
                            <select name="zone_id">
                                <option value="">Zonal</option>
                                @foreach($zonals as $zone)
                                    <option value="{{$zone->id}}" {{ Request::get('zone_id') == $zone->id ? 'selected' : '' }}>
                                        {{$zone->Name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-pill">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <select name="district_id">
                                <option value="">All Districts</option>
                                @foreach($districts as $district)
                                    <option value="{{$district->id}}" 
                                    {{ (request('district_id') == $district->id) || ($DistId && $DistId == $district->id) ? 'selected' : '' }}>

                                        {{$district->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                      
                        <div class="filter-pill">
                            <i class="bi bi-dice-4-fill text-primary"></i>
                            <select name="block_id">
                                <option value="">Block</option>
                                @foreach($blocks as $block)
                                    <option value="{{$block->name}}" {{ Request::get('block_id') == $block->name ? 'selected' : '' }}>
                                        {{$block->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-pill">
                            <i class="bi bi-bucket-fill text-success"></i>
                            <select name="category">
                                <option value="">Category</option>
                                @foreach($services as $service)
                                    <option value="{{$service->name}}" {{ Request::get('category') == $service->name ? 'selected' : '' }}>
                                        {{$service->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-pill">
                                <i class="bi bi-calendar-week-fill text-warning"></i>

                                <input type="date" name="from_date" value="{{ @Request::get('from_date') }}">
                        </div>
                        <div class="filter-pill">
                            <i class="bi bi-calendar-week-fill text-warning"></i>

                            <input type="date" name="to_date" value="{{ @Request::get('to_date') }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-apply">Apply</button>

                    </div>
                    <input type="hidden" value="{{ @Request::get('status') }}" name="status">
                </form>

            </div>

                    <!-- Summary Cards -->
            <div class="stats-row">
                <div class="stat-card stat-total">
                    <h3>{{$tickets->total()}}</h3>
                    <p>Total Tickets</p>
                </div>
                <div class="stat-card stat-permanent-down">
                     <h3>{{ $permanentDownCount }}</h3>
                    <p>Permanent Down</p>
                </div>
                <div class="stat-card stat-notstarted">
                     <h3>{{ isset($statusCounts['INCOMING']) ? $statusCounts['INCOMING'] : 0 }}</h3>
                    <p>Open</p>
                </div>
                <div class="stat-card stat-ongoing">
                    <h3>{{ isset($statusCounts['PICKEDUP']) ? $statusCounts['PICKEDUP'] : 0 }}</h3>
                    <p>Ongoing</p>
                </div>
                <div class="stat-card stat-onhold">
                    <h3>{{ (isset($statusCounts['ONHOLD']) ? $statusCounts['ONHOLD'] : 0) - $permanentDownCount }}</h3>
                    <p>On Hold</p>
                </div>
                <div class="stat-card stat-completed">
                    <h3>{{ isset($statusCounts['COMPLETED']) ? $statusCounts['COMPLETED'] : 0 }}</h3>
                    <p>Completed</p>
                </div>
            </div>
  

         @if(count($tickets) != 0)
           <div class="table-wrapper">
            <table  id="table-5" class="new-table nowrap display" style="width:100%">
                <thead>
                    <tr>
                        <th>Ticket Id</th>
                        <th>GP Details   </th>
                        <th>Assigned To</th>
                        <th>Down Details</th>
                        <th>Ticket Time</th>
                        <th>During Hours</th>
                       <th>Type</th>
                       
                        <th>Created By</th>
                      
                        <th>Status</th>

                        <th>Action</th>
                       
                        
                    </tr>
                </thead>
                <tbody> 
                 @foreach($tickets as $index => $request)
             
                    <tr>
                        <td class="font-weight-bold @if($request->status == 'INCOMING') bleft-notstarted @elseif($request->status == 'PICKEDUP') bleft-ongoing @elseif($request->status == 'ONHOLD') bleft-onhold @elseif($request->status == 'COMPLETED') bleft-completed @endif">
                          {{ $request->ticketid }}
                        </td>
                        <td>
                        <div>
                       {{ $request->gpname }}<small> ({{ $request->lgd_code}})</small><br>
                      <small><i class="bi bi-globe-central-south-asia text-info"></i> {{ $request->zone_name}} 
                             <i class="bi bi-geo-alt-fill text-danger"></i> {{ $request->district }} 
                             <i class="bi bi-dice-4-fill text-primary"></i> {{ $request->mandal }}</small> 
                        </div> 
                       </td>
                        <td>
                        <div>
                        {{ $request->first_name}} {{ $request->last_name}} <br>
                        @if(auth()->user()->role != 'client')

                        <small>{{ $request->mobile}} </small>
                        @endif
                        </div>
                        </td>
                         
                        <td class="d-size">
                          <div>
                               @if(!empty($request->downreason))
                               <i class="bi bi-record-fill text-danger"></i> {{ $request->downreason }} <br>
                               @endif
                               @if(!empty($request->subcategory))
                               <i class="bi bi-record-fill text-warning"></i> {{ $request->subcategory }} <br>
                                @endif
                               @if(!empty($request->downreasonindetailed))
                               <i class="bi bi-record-fill text-warning"></i>{{ $request->downreasonindetailed}}<br>
                                @endif
                               @if(!empty($request->description))
                               <i class="bi bi-record-fill text-warning"></i> {{ $request->description}}<br>
                                @endif
                               @if(!empty($request->issue_type))
                              <i class="bi bi-record-fill text-warning"></i> {{ $request->issue_type }} <br>
                               @endif
                               @if(!empty($request->purpose))
                              <i class="bi bi-record-fill text-primary"></i> {{ $request->purpose }} <br>
                               @endif

                          </div>
                        
                         </td>
                        
                                               
                       <td>
  <div class="d-size">
    <i class="bi bi-record-fill text-danger"></i><span>Down :</span>
    <span class="text-muted bld">
      {{ $request->downdate }} {{ $request->downtime ? \Carbon\Carbon::parse($request->downtime)->format('h:i A') : '-' }}
    </span><br>

    <i class="bi bi-record-fill text-primary"></i><span>Assign :</span>
    <span class="text-muted bld">
      {{ $request->assigned_at ? \Carbon\Carbon::parse($request->assigned_at)->format('Y-m-d h:i A') : '-' }}
    </span><br>

    <i class="bi bi-record-fill text-warning"></i><span>Started :</span>
    <span class="text-muted bld">
      {{ $request->started_at ? \Carbon\Carbon::parse($request->started_at)->format('Y-m-d h:i A') : '-' }}
    </span><br>

    <i class="bi bi-record-fill text-success"></i><span>Closed :</span>
    <span class="text-muted bld">
      {{ $request->finished_at ? \Carbon\Carbon::parse($request->finished_at)->format('Y-m-d h:i A') : '-' }}
    </span>
  </div>
</td>
                                               
                        <?php
$downdate = $request->downdate;
$downtime = $request->downtime;
$downdatetime = date('Y-m-d H:i:s', strtotime("$downdate $downtime"));
$todaydatetime = date('Y-m-d H:i:s');

if(!empty($request->finished_at)) {
    $seconds = strtotime($request->finished_at) - strtotime($downdatetime);
} else {
    $seconds = strtotime($todaydatetime) - strtotime($downdatetime);
}

// Calculate hours, minutes properly
$hours = floor($seconds / 3600);
$minutes = floor(($seconds / 60) % 60);

// Format as hh:mm
$formattedTime = sprintf("%02d:%02d", $hours, $minutes);
?>
                        <td><i class="bi bi-stopwatch-fill text-info"></i> {{ $formattedTime }}</td>
                        <td>
                           <span class="font-weight-bold @if($request->default_autoclose == 'Auto') text-success @elseif($request->default_autoclose == 'Manual') text-danger @endif">{{ $request->default_autoclose }}</span> - 
                           <span class="font-weight-bold @if($request->autoclose == 'Auto') text-primary @elseif($request->autoclose == 'Manual') text-success  @endif">{{ $request->autoclose}}</span>
                           
                        </td>
                        @php
                            $admin = \App\Admin::find($request->created_by);
                            $name = $admin->name ?? '-';

                            if (in_array($name, ['WestBengal Tracking', 'Andaman Tracking'])) {
                                $name = 'Admin';
                            }
                        @endphp


                        <td>{{ $name }}</td>
                       
                        




                        <?php if($request->status != ''){?>
                        <td>
                            @if($request->status == 'COMPLETED')
                                <span class="tag tag-success tag-brp"> {{ $request->status }} </span>
                            @elseif($request->status == 'INCOMING')
                                <span class="tag tag-notstarted  tag-brp"> OPEN </span>
                            @elseif($request->status == 'PICKEDUP')
                                <span class="tag tag-ongoing tag-brp"> ONGOING </span>
                            @elseif($request->status == 'ONHOLD')
                                <span class="tag tag-danger tag-brp"> {{ $request->status }} </span>
                            @else 
                                <span class="tag tag-info tag-brp"> {{ $request->status }} </span>
                            @endif
                        </td>
                        <?php } else {?>
                          <td><span class="tag tag-info tag-brp">Not Assigned</span></td>
                         <?php } ?> 
                       

                         <?php if($request->status != ''){?>
                           <td>
                                <div class="input-group-btn">
                                  <button type="button" class="btn btn-info b-a-radius-0-5 dropdown-toggle pull-left" data-toggle="dropdown">Action
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('admin.requests.show', $request->request_id) }}" class="btn btn-default"><i class="fa fa-search"></i> More Details</a>
                                    </li>
                                    @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin' ||  auth()->user()->role == 'zone_admin' || auth()->user()->role=='district_incharge')
                                    <li>
                                        <a href="{{ route('admin.tickets.edit', $request->master_id) }}" class="btn btn-default"><i class="fa fa-pencil"></i> @lang('admin.edit')</a>
                                    </li>
                                    <!--<button class="btn btn-default"   onclick="deleteLocation({{ $request->master_id }}, '{{ $request->ticketid }}')">
                                        <i class="ti-trash"></i> Delete
                                      
                                    </button>--->
                                     <?php if($request->status == 'SEARCHING'){ ?>
                                     <li>
                                        <a href="{{ route('admin.dispatcher.assignform', $request->request_id) }}" class="btn btn-default"><i class="fa fa-arrows"></i> Assign</a>
                                    </li>
                                   <?php } ?>
                                                                 
                                 
                                    
                                   @endif
                                    @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin')
                                     <?php if($request->status != 'COMPLETED'){ ?>
                                    <li>
                                        <a href="{{ route('admin.dispatcher.completeform', $request->request_id) }}" class="btn btn-default"><i class="fa fa-arrows"></i> Request Close </a>
                                    </li>
                                     <?php } ?>
                                    @endif
                              
                                                                     
                                     @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin')

                                      <?php if($request->status == 'INCOMING' || $request->status == 'ONHOLD'){ ?>
                                     <li>
                                        <a href="{{ route('admin.dispatcher.assignform', $request->request_id) }}" class="btn btn-default"><i class="fa fa-arrows"></i> Re-Assign</a>
                                    </li>
                                   <?php } ?>
                                     @endif
                                    @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin' || auth()->user()->role == 'district_incharge')
                                    <?php if($request->status == 'INCOMING' ||  $request->status == 'PICKEDUP'){ ?>
                                    <li>
                                        <a href="{{ route('admin.dispatcher.onholdform', $request->request_id) }}" class="btn btn-default"><i class="fa fa-arrows"></i> On Hold </a>
                                    </li>
                                     <?php } ?>
                                    @endif
                                   

                                  </ul>
                                </div>
                            </td>
                           <?php } else{?>
                            <td>
                             <div class="input-group-btn">
                                  <button type="button" class="btn btn-info dropdown-toggle b-a-radius-0-5 pull-left" data-toggle="dropdown">Action
                                    <span class="caret"></span>
                                  </button>
                              </div>
                              </td>    
                           <?php } ?> 
                          
                    </tr>
                @endforeach
                </tbody>
              </table>
            @else
            <h6 class="no-result">No results found</h6>
            @endif 
        </div>
         Showing {{$tickets->currentPage() != 1 ? $tickets->currentPage() * 10 - 9 : $tickets->currentPage()}} to {{$tickets->currentPage() * $tickets->perPage()}} of {{$tickets->total()}} entries
    </div>
      {{ $tickets->appends(['status' => @$status_get,'district_id'=>@$district_id_get,'interval'=>@$interval_get,'searchinfo'=>@$serch_term_get,'zone_id'=>@$zone_id_get,'team_id'=>@$team_id_get,'block_id'=>@$block_id_get,'from_date'=>@$from_date_get,'to_date'=>@$to_date_get,'autoclose'=>@$autoclose_get,'default_autoclose'=>@$default_autoclose_get,'provider_id'=>@$provider_id_get,'category'=>@$category_get,'newfrom_date'=>@$newfrom_date_get,'newto_date'=>@$newto_date_get,'range'=>@$range_get])->links()  }}
   </div>
</div>
          

        </div>
    </div>
</div>
{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="terrasoft-modal" data-location-id="">
    <div class="terrasoft-modal-content">
        <div class="terrasoft-modal-header">
            <h3>Confirm Delete</h3>
            <button class="terrasoft-modal-close" onclick="closeDeleteModal()">
                <i class="ti-x"></i>
            </button>
        </div>
        <div class="terrasoft-modal-body">
            <div class="terrasoft-delete-warning">
                <i class="ti-alert-triangle text-red-500"></i>
                <p>Are you sure you want to delete this <strong id="locationName"></strong>? This action cannot be undone.</p>
            </div>
        </div>
        <div class="terrasoft-modal-footer">
            <button class="terrasoft-btn terrasoft-btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <button class="terrasoft-btn terrasoft-btn-danger" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>
@endsection
<link rel="stylesheet" href="{{ asset('/css/olt.css')}}">

@section('scripts')
 <script type="text/javascript">
    /*$('#table-5').DataTable( {
        scrollX: true,
        searching: false,
        responsive: true,
        paging: false,
        info:false,
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
    } );*/

$('#searchblocklist').change(function(){
        var nid = $(this).find('option:selected').attr('rel');
        if(nid){
        $.ajax({
           type:"get",
            url: 'https://fleet.terasoftware.com/public/westbengal/public/admin/getSearchblocklist/'+ nid,
            success:function(res)
           {       
                if(res)
                {
                    $("#getblock").empty();
                    $("#getblock").append('<option value="">Select block</option>');
                    $.each(res,function(key,value){
                        $("#getblock").append('<option value="'+value+'">'+value+'</option>');
                    });
                }
           }

        });
        }
});


$(document).ready(function() {
    var buttonsArray = [
        @if(auth()->user()->role != 'client')
        'copyHtml5',
        'excelHtml5',
        'csvHtml5',
        'pdfHtml5'
        @endif
    ];

    var table = $('#table-5').DataTable({
        scrollX: true,
        searching: false,
        responsive: false,
        paging: false,
        info: false,
        dom: 'Bfrtip',
        buttons: buttonsArray
    });



  $('#searchInput').on('keyup', function() {
   
    // table.search(this.value, true, false).draw();
    // var srch_info = this.value;
    // if(srch_info.length > 3){
    //     var csrf_tokenn = '{{ csrf_token() }}';
    //     $.ajax({
    //         url: "{{route('admin.tickets1', $query_params)}}",
    //         method: 'GET',
    //         data: {'_token': csrf_tokenn, 'searchinfo':srch_info },
    //         success: function(response) {
    //             $('#main_content').html(response);
    //             // table.clear().draw();
    //             table.rows.add($('#table-5 tbody tr')).draw();
    //         }
    //     });
    // }
  });
});

jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
   if ( this.context.length ) {

     var string = window.location.search;
            if(string == ''){
                string = '?page=all';                         
            }
     var jsonResult = $.ajax({
       url: "{{url('admin/tickets')}}"+string,
       data: {},
       success: function (result) {                       
         p = new Array();
		
         var current = 1;
         $.each(result.data, function (i, d)
         {
            let downdate = d.downdate;
            let downtime = d.downtime;

            let downdatetime = new Date(downdate + " " + downtime);
            let currentDatetime = new Date();

            let finishedAt = d.finished_at ? new Date(d.finished_at) : null;

            let seconds;
            if (finishedAt) {
                seconds = (finishedAt - downdatetime) / 1000;
            } else {
                seconds = (currentDatetime - downdatetime) / 1000;
            }

            let hours = Math.floor(seconds / 3600);
            let minutes = Math.floor((seconds / 60) % 60);

            let formattedTime = 
                String(hours).padStart(2, "0") + ":" + 
                String(minutes).padStart(2, "0");


           var item = [
           current,
		   d.ticketid,
           d.zone_name,
           d.district,
           d.mandal,
           d.gpname,
           d.lgd_code,
           d.subsategory,
           d.downreason,
           d.downreasonindetailed,
           d.downdate,
		   d.downtime,
		   d.first_name,
		   d.last_name,
		   d.mobile,
		   d.s_address,
		   d.s_latitude,
		   d.s_longitude,
		   d.d_address,
		   d.d_latitude,
		   d.d_longitude,
		   d.assigned_at,
		   d.started_at,
		   d.started_location,
		   d.reached_at,
		   d.reached_location,
		   d.finished_at,
           formattedTime,
            d.autoclose,
            d.materials['24F CABLE'] || '',
            d.materials['48F CABLE'] || '',
            d.materials['4F CABLE'] || '',
            d.materials['6F CABLE'] || '',
            d.materials['8F CABLE'] || '',
            d.materials['12F CABLE'] || '',
            d.materials['PATCH CORD'] || '',
            d.materials['JOINT ENCLOSURE'] || '',
            d.materials['Other Joint BOX'] || '',
            d.materials['SS STRIP'] || '',
            d.materials['TENSION CLAMP'] || '',
            d.materials['BUCKLES'] || '',
            d.materials['FRAMES'] || '',
            d.materials['ENCLOSURES'] || '',
            d.materials['Joint chamber'] || '',
            d.joint_enclosure_before_latlong || '',
            d.joint_enclosure_after_latlong || '',
               
           d.status
           ];
           p.push(item);
           current++;
         });
       },
       async: false
     });
     var head=new Array();
     head.push(
       "ID",
       "Ticket ID",
       "Zonal",
       "District Name",
       "Block Name",
       "GP Name",
       "LGD Code",
       "Category",
       "Down Reason",
       "Description",
       "Ticket Down Date",
       "Ticket Down Time",
	   "First Name",
	   "Last Name",
	   "Mobile",
	   "Source Address",
	   "Source Latitude",
	   "Source Longitude",
	   "Destination Address",
	   "Destination Latitude",
	   "Destination Longitude",
	   "Ticket Assigned Time",
	   "Ticket Started Time",
	   "Ticket Started Location",
	   "Ticket Reached Time",
	   "Ticket Reached Location",
	   "Ticket Closed Time",
       "Duration",
        "Ticket Assigned",
        "24F Cable",
        "48F Cable",
        "4F Cable",
        "6F Cable",
        "8F Cable",
        "12F Cable",
        "Patch Cord",
        "Joint Enclosure",
        "Other Joint Box",
        "SS Strip",
        "Tension Clamp",
        "Buckles",
        "Frames",
        "Enclosures",
        "Joint Chamber",
        "Before LatLong",
        "After LatLong",
       "Status"
       );            
     return {body: p, header: head};
   }
 } );


function deleteLocation(id,name) {
    const modal = document.getElementById('deleteModal');
    modal.dataset.locationId = id;
    document.getElementById('locationName').textContent = name;
    modal.classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}
function confirmDelete() {
    const modal = document.getElementById('deleteModal');
    const id = modal.dataset.locationId;
    
    fetch(`/admin/delticket/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error deleting ticket');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting ticket');
    });
    
    closeDeleteModal();
}
</script>
@endsection
