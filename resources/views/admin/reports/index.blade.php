@extends('admin.layout.base')

@section('title', 'Teams Reports ')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">

              <form action="{{route('admin.teams_status')}}" method="GET">
            <ul class="nav nav-pills mb-2 pb-1 b-b">
                 
                  <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="from_date" name="from_date" placeholder="From Date" value="{{ @Request::get('from_date') }}"  onclick="this.showPicker()">
                
                <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="to_date" name="to_date" placeholder="To Date" value="{{ @Request::get('to_date') }}"  onclick="this.showPicker()">
                </li>

                <li class="nav-item mr-0-75 pull-right mt">
                    <button type="submit" class="form-control btn btn-primary btn-cstm" style="height:30px">Apply</button>
                </li>
            </ul>
            </form>



           @if(Setting::get('demo_mode') == 1)
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : @lang('admin.demomode')
                </div>
                @endif
            <h5 class="mb-1">
                 Teams Report
                @if(Setting::get('demo_mode', 0) == 1)
                <span class="pull-right">(*personal information hidden in demo)</span>
                @endif               
            </h5>
            <table class="table table-striped table-bordered dataTable" id="table-5">
    <thead>
        <tr>
            <th>@lang('admin.id')</th>
            <th>Zonal</th>
            <th>District</th>
            <th>Team</th>
            <th>Name</th>
            <th>New Tickets</th>
            <th>Auto Close</th>
            <th>Manual Close</th>
            <th>Hold</th>
            <th>OnGoing</th>
            <th>NotStarted</th>
            <th>>24Hr</th>
        </tr>
    </thead>
    <tbody>
        @php
            $page = 0;
            $totalTickets = 0;
            $totalCompletedAuto = 0;
            $totalCompletedManual = 0;
            $totalHold = 0;
            $totalOngoing = 0;
            $totalPending = 0;
            $pending24hr = 0;
        @endphp
        @foreach($teams as $index => $user)
        @php
            $page++;
            $totalTickets += $user->total_tickets;
            $totalCompletedAuto += $user->completed_tickets_auto;
            $totalCompletedManual += $user->completed_tickets_manual;
            $totalHold += $user->hold_tickets;
            $totalOngoing += $user->pickup_tickets;
            $totalPending += $user->pending_tickets;
            $pending24hr += $user->pending_tickets_morethen_24;
        @endphp
        <tr>
            <td>{{ $page }}</td>
            <td>{{ $user->zone_name }}</td>
            <td>{{ $user->district }}</td>
            <td>{{ $user->team_name }}</td>
            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
            <td>{{ $user->total_tickets }}</td>
            <td><a href="/admin/tickets?zone_id={{ $user->zone_id }}&team_id={{ $user->team_id }}&status=Completed&autoclose=Auto&newfrom_date={{$fromDate}}&newto_date={{$toDate}}">{{ $user->completed_tickets_auto }}</a></td>
            <td><a href="/admin/tickets?zone_id={{ $user->zone_id }}&team_id={{ $user->team_id }}&status=Completed&autoclose=Manual&newfrom_date={{$fromDate}}&newto_date={{$toDate}}">{{ $user->completed_tickets_manual }}</a></td>
            <td><a href="/admin/tickets?zone_id={{ $user->zone_id }}&team_id={{ $user->team_id }}&status=Onhold&newfrom_date={{$fromDate}}&newto_date={{$toDate}}">{{ $user->hold_tickets }}</a></td>
            <td><a href="/admin/tickets?zone_id={{ $user->zone_id }}&team_id={{ $user->team_id }}&status=OnGoing">{{ $user->pickup_tickets }}</a></td>
            <td>
              @if(request()->has('from_date') && request()->has('to_date'))
            <a href="/admin/tickets?zone_id={{ $user->zone_id }}&team_id={{ $user->team_id }}&status=Open&from_date={{ $fromDate }}&to_date={{ $toDate }}">
                {{ $user->pending_tickets }}
            </a>
        @else
            <a href="/admin/tickets?zone_id={{ $user->zone_id }}&team_id={{ $user->team_id }}&status=Open">
                {{ $user->pending_tickets }}
            </a>
        @endif
            </td>
            <td>
              @if(request()->has('from_date') && request()->has('to_date'))
            <a href="/admin/tickets?zone_id={{ $user->zone_id }}&team_id={{ $user->team_id }}&status=Open&from_date={{ $fromDate }}&to_date={{ $toDate }}&range=24hr">
                {{ $user->pending_tickets_morethen_24 }}
            </a>
        @else
            <a href="/admin/tickets?zone_id={{ $user->zone_id }}&team_id={{ $user->team_id }}&status=Open&range=24hr">
                {{ $user->pending_tickets_morethen_24 }}
            </a>
        @endif
            </td>

        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">Total</th>
            <th>{{ $totalTickets }}</th>
            <th>{{ $totalCompletedAuto }}</th>
            <th>{{ $totalCompletedManual }}</th>
            <th>{{ $totalHold }}</th>
            <th>{{ $totalOngoing }}</th>
            <th>{{ $totalPending }}</th>
            <th>{{ $pending24hr }}</th>

        </tr>
    </tfoot>
</table>
  </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
       $('#table-5').DataTable( {
        responsive: true,
        paging:false,
            info:false,
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
    } );
</script>
@endsection