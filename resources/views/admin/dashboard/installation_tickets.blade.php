@extends('admin.layout.base')

@section('title', 'Installation Tickets')

@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .dashboard-page {background-color: #f8fafc;}
    .header-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 20px; }
    .nav-cstm .nav-link-cstm { font-weight: 600; color: #636f73 !important; border: none; padding: 6px 15px; border-radius: 20px; transition: 0.2s; }
    .nav-cstm .nav-link-cstm.active { background: #2b3eb1 !important; color: #fff !important; }
    .nav-cstm .nav-link-cstm:hover { background: #edf1f2; }
    .filter-card { background: #fff; padding: 10px; margin-bottom: 15px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .filter-pill { border-radius: 12px; padding: 6px 15px; border: 1px solid #e0e0e0; background: #fff; font-size: 12px; font-weight: 300; display: flex; align-items: center; gap: 6px; }
    .filter-pill select, .filter-pill input { border: none !important; box-shadow: none !important; background: transparent !important; font-size: 12px; padding: 0; height: auto !important; }
    .filter-row { display: flex; flex-wrap: wrap; gap: 10px; }
    .btn-apply { border-radius: 25px; padding: 6px 20px; }
    .stats-row { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; }
    .stat-card { flex: 1; min-width: 180px; background: #fff; border-radius: 12px; padding: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center; }
    .stat-card h3 { font-size: 22px; margin: 0; font-weight: bold; }
    .stat-card p { margin: 5px 0 0; font-size: 14px; color: #6c757d; }
    .stat-total { border-top: 3px solid #8b5cf6; }
    .stat-notstarted { border-top: 3px solid #FFDE00; }
    .stat-ongoing { border-top: 3px solid #02E9FA; }
    .stat-onhold { border-top: 3px solid #FA2602; }
    .stat-completed { border-top: 3px solid #02FA2F; }
    .table-wrapper { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.04); overflow-x: auto; }
    .new-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .new-table thead th { background: #f9fafb; font-weight: 500; padding: 12px; color: #374151; text-align: left; border-bottom: 1px solid #e5e7eb; }
    .new-table tbody tr { font-size: 12px; }
    .new-table tbody td { padding: 14px 12px; color: #111827; vertical-align: middle; border-bottom: 1px solid #e5e7eb !important; }
    .ticket-inst { background: #e8d5ff; color: #6b21a8; border-radius: 6px; padding: 3px 8px; font-weight: 700; font-size: 12px; display: inline-block; }
    .tag-notstarted {background:#FFDE00;}
        .tag-ongoing {background:#04C2A2;}
        .tag-onhold {background:#FA2602;}
        .tag-completed {background:#02FA2F;}
  </style>
@endsection

@section('content')
@php
    $user = Session::get('user');
    $DistId = null;
    if ($user && isset($user->district_id)) {
        $DistId = $user->district_id;
    }
@endphp

<div class="content-area dashboard-page py-1">
    <div class="container-fluid">
        <div class="box box-block">
            <div class="header-row">
                <ul class="nav nav-pills nav-cstm mb-0">
                    <li class="nav-item">
                        <a href="{{ route('admin.installation-tickets') }}"
                           class="nav-link nav-link-cstm {{ @Request::get('status') == '' ? 'active' : '' }}">
                           All
                        </a>
                    </li>
                    @foreach ($ticket_status as $status)
                        <li class="nav-item">
                            <a href="{{ route('admin.installation-tickets',['status' => $status, 'zone_id' => @Request::get('zone_id'), 'district_id' => @Request::get('district_id'), 'block_id' => @Request::get('block_id'), 'category' => @Request::get('category'), 'from_date' => @Request::get('from_date'), 'to_date' => @Request::get('to_date'), 'searchinfo' => @Request::get('searchinfo')]) }}"
                               class="nav-link nav-link-cstm {{ $status == @Request::get('status') ? 'active' : '' }}">
                               {{ $status }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div>
                    <a href="{{ route('admin.import') }}" class="btn btn-success mr-2"><i class="fa fa-upload"></i> Upload CSV</a>
                    <a href="{{ route('admin.tickets.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Ticket</a>
                </div>
            </div>

            <div class="filter-card">
                <form action="{{ route('admin.installation-tickets', $query_params) }}" method="GET">
                    <div class="filter-row">
                        <div class="filter-pill">
                            <i class="bi bi-search text-muted"></i>
                            <input type="text" name="searchinfo" placeholder="Search..." value="{{ @Request::get('searchinfo') }}">
                        </div>
                        <div class="filter-pill">
                            <i class="bi bi-globe-central-south-asia text-info"></i>
                            <select name="zone_id" id="zone_id">
                                <option value="">Zonal</option>
                                @foreach($zonals as $zone)
                                    <option value="{{$zone->id}}" {{ Request::get('zone_id') == $zone->id ? 'selected' : '' }}>{{$zone->Name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-pill">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <select name="district_id" id="district_id">
                                <option value="">All Districts</option>
                                @foreach($districts as $district)
                                    <option value="{{$district->id}}" {{ (request('district_id') == $district->id) || ($DistId && $DistId == $district->id) ? 'selected' : '' }}>{{$district->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-pill">
                            <i class="bi bi-dice-4-fill text-primary"></i>
                            <select name="block_id" id="block_id">
                                <option value="">Block</option>
                                @foreach($blocks as $block)
                                    <option value="{{$block->name}}" {{ Request::get('block_id') == $block->name ? 'selected' : '' }}>{{$block->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-pill">
                            <i class="bi bi-bucket-fill text-success"></i>
                            <select name="category">
                                <option value="">Category</option>
                                @foreach($services as $service)
                                    <option value="{{$service->name}}" {{ Request::get('category') == $service->name ? 'selected' : '' }}>{{$service->name}}</option>
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

            <div class="stats-row">
                <div class="stat-card stat-total">
                    <small style="font-size:10px;font-weight:700;color:#8b5cf6;letter-spacing:.5px;">INSTALLATION TICKETS</small>
                    <h3 style="color:#8b5cf6;">{{ $instTotal }}</h3>
                    <p>Total</p>
                </div>
                <div class="stat-card stat-notstarted">
                    <small style="font-size:10px;font-weight:700;color:#b59500;letter-spacing:.5px;">INSTALLATION TICKETS</small>
                    <h3>{{ $instOpen }}</h3>
                    <p>Open</p>
                </div>
                <div class="stat-card stat-ongoing">
                    <small style="font-size:10px;font-weight:700;color:#0298a8;letter-spacing:.5px;">INSTALLATION TICKETS</small>
                    <h3>{{ $instOngoing }}</h3>
                    <p>Ongoing</p>
                </div>
                <div class="stat-card stat-onhold">
                    <small style="font-size:10px;font-weight:700;color:#a01c01;letter-spacing:.5px;">INSTALLATION TICKETS</small>
                    <h3>{{ $instHold }}</h3>
                    <p>On Hold</p>
                </div>
                <div class="stat-card stat-completed">
                    <small style="font-size:10px;font-weight:700;color:#01a01e;letter-spacing:.5px;">INSTALLATION TICKETS</small>
                    <h3>{{ $instCompleted }}</h3>
                    <p>Completed</p>
                </div>
            </div>

            @if(count($tickets) != 0)
            <div class="table-wrapper">
                <table class="new-table nowrap display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Ticket Id</th>
                            <th>GP Details</th>
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
                            <td class="font-weight-bold">
                                <span class="ticket-inst">{{ $request->ticketid }}</span>
                            </td>
                            <td>
                                {{ $request->gpname }}<small> ({{ $request->lgd_code }})</small><br>
                                <small><i class="bi bi-geo-alt-fill text-danger"></i> {{ $request->district }} <i class="bi bi-dice-4-fill text-primary"></i> {{ $request->mandal }}</small>
                            </td>
                            <td>
                                {{ $request->first_name }} {{ $request->last_name }}<br>
                                <small>{{ $request->mobile }}</small>
                            </td>
                            <td>
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

                            </td>
                            <td>
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
                            </td>
                            <td>
                                @php
                                    $downdatetime = date('Y-m-d H:i:s', strtotime($request->downdate . ' ' . $request->downtime));
                                    $todaydatetime = date('Y-m-d H:i:s');
                                    $seconds = !empty($request->finished_at) ? strtotime($request->finished_at) - strtotime($downdatetime) : strtotime($todaydatetime) - strtotime($downdatetime);
                                    $hours = floor($seconds / 3600);
                                    $minutes = floor(($seconds / 60) % 60);
                                @endphp
                                <i class="bi bi-stopwatch-fill text-info"></i> {{ sprintf("%02d:%02d", $hours, $minutes) }}
                            </td>
                            <td>
                            <span class="font-weight-bold @if($request->default_autoclose == 'Auto') text-success @elseif($request->default_autoclose == 'Manual') text-danger @endif">{{ $request->default_autoclose }}</span> - 
                            <span class="font-weight-bold @if($request->autoclose == 'Auto') text-primary @elseif($request->autoclose == 'Manual') text-success  @endif">{{ $request->autoclose}}</span>
                            
                            </td>
                              @php
                                $name = $request->created_by_name ?? $request->created_by ?? '-';

                                $adminNames = ['WestBengal Tracking', 'Andaman Tracking'];
                                if (in_array($name, $adminNames)) {
                                    $name = 'Admin';
                                }
                            @endphp
                            <td>{{ $name }}</td>
                              <?php if($request->status != ''){?>
                            <td>
                                @if($request->status == 'COMPLETED')
                                    <span class="tag tag-success tag-brp">COMPLETED</span>
                                @elseif($request->status == 'INCOMING')
                                    <span class="tag tag-notstarted tag-brp">OPEN</span>
                                @elseif($request->status == 'PICKEDUP')
                                    <span class="tag tag-ongoing tag-brp">ONGOING</span>
                                @elseif($request->status == 'ONHOLD')
                                    <span class="tag tag-danger tag-brp">{{ $request->status }}</span>
                                @else
                                    <span class="tag tag-info tag-brp">{{ $request->status }}</span>
                                @endif
                            </td>
                            <?php } else {?>
                          <td><span class="tag tag-info tag-brp">Not Assigned</span></td>
                           <?php } ?> 
                            <td>
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-info b-a-radius-0-5 dropdown-toggle pull-left" data-toggle="dropdown">Action <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ route('admin.requests.show', $request->request_id) }}" class="btn btn-default"><i class="fa fa-search"></i> More Details</a></li>
                                        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin' || auth()->user()->role=='district_incharge')
                                        <li><a href="{{ route('admin.tickets.edit', $request->master_id) }}" class="btn btn-default"><i class="fa fa-pencil"></i> Edit</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <h6 class="no-result">No installation tickets found</h6>
            @endif
        </div>
        {{ $tickets->appends(['status' => @$status_get,'zone_id'=>@$zone_id_get,'district_id'=>@$district_id_get,'block_id'=>@$block_id_get,'category'=>@$category_get,'from_date'=>@$from_date_get,'to_date'=>@$to_date_get,'searchinfo'=>@$serch_term_get,'range'=>@$range_get,'provider_id'=>@$provider_id_get])->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
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
                $('#block_id').empty().append('<option value="">All Blocks</option>');
            }
        });
    } else {
        $('#district_id').empty().append('<option value="">All Districts</option>');
        $('#block_id').empty().append('<option value="">All Blocks</option>');
    }
});

$('#district_id').on('change', function () {
    let districtId = $(this).val();
    $('#block_id').html('<option value="">All Blocks</option>');
    if (!districtId) return;
    $.get("{{ url('admin/get_blocks') }}/" + districtId, function (res) {
        let h = '<option value="">All Blocks</option>';
        res.forEach(function(b) {
            h += '<option value="' + b.name + '">' + b.name + '</option>';
        });
        $('#block_id').html(h);
    });
});
</script>
@endsection