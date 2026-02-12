@extends('admin.layout.base')

@section('title', 'Attendance ')

@section('content')
@php
    $user = Session::get('user');
    $DistId = null; 
    if ($user && isset($user->district_id)) {
        $DistId = $user->district_id;
    }
@endphp
<style type="text/css">
    table.dataTable thead th {
        background-color: #d9d9d9f5 !important;
        border-bottom: none !important;
    }
    .buttons-html5{
        border-radius: 10px;
/*        margin-right: 6px;*/
    }
    table.display tbody tr:hover td{
        background-color: #f1eeeef5 !important;
    }
    .dataTables_scrollBody thead {
        visibility: hidden;
    }
    select.filter-box:not([size]):not([multiple]), input.filter{
        height: 30px;
        background-color: #f6f7f7 !important;
        border:none;
        color:#000;
        padding-top: 4px !important;
    }

    .nav-cstm .nav-link-cstm:not(.active):hover{
        color: #333333 !important;
        border-bottom:3px solid #edf1f2;
        transition: none !important;
    }

    .nav-cstm .nav-link-cstm{
        font-weight: 600;
        color: #636f73 !important;
    }

    .nav-link-cstm.active{
        background-color: transparent !important;
        color: #2b3eb1 !important;
        border-bottom: 3px solid #2b3eb1;        
    }
    .filter-box{
        border-radius: 25px;
        height: 30px !important;
    }
    #table-5_filter label{
        display: none !important;
    }
    .br-10{
        border-radius: 10px;
    }
    .dropdown-menu{
        left: -50px !important;
    }

</style>
<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white"> 
        <form action="{{route('admin.attendance')}}" method="GET">
            <div class="row">
                <div class="col-xs-4">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="district_id" id="district_id">
                   	<option value="">Please Select District</option>
                    @foreach($districts as $district)
                    <option value="{{$district->id}}"
                    {{ (request('district_id') == $district->id) || ($DistId && $DistId == $district->id) ? 'selected' : '' }}>

                     {{$district->name}} </option> 
                   @endforeach 
                  </select>
                </div>

                <div class="form-group row col-md-3">
                            <label for="name" class="col-xs-4 col-form-label">Date From</label>
                            <div class="col-xs-8">
                                <input class="form-control" type="date" name="from_date" value="{{ @Request::get('from_date') }}" placeholder="From Date" id="from_date">
                            </div>
                </div>
                            
                <div class="form-group row col-md-3">
                            <label for="email" class="col-xs-4 col-form-label">Date To</label>
                            <div class="col-xs-8">
                                <input class="form-control" type="date" name="to_date" value="{{ @Request::get('to_date') }}" placeholder="To Date" id="to_date">
                            </div>
                  </div>
                
                <div class="col-xs-2">
                    <button type="submit" class="form-control btn btn-primary" onclick="return validate_reqst()">Fetch</button>
                </div>  
            </div>
        </form>
        </div> 
        <div class="box box-block bg-white">
            @if(Setting::get('demo_mode') == 1)
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : @lang('admin.demomode')
                </div>
                @endif
            <h5 class="mb-1" style="color:#0275d8;">
                @if((Request::get('from_date') != '') && (Request::get('to_date') != ''))
                Attendance Report From {{Request::get('from_date')}} To {{Request::get('to_date')}}
			    @else
			    Today Attendance Report 
                @endif				
                @if(Setting::get('demo_mode', 0) == 1)
                <span class="pull-right">(*personal information hidden in demo)</span>
                @endif
            </h5>
            <table class="table row-bordered dataTable nowrap display" id="table-4" style="width:100%">
                <thead>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('admin.name')</th>
                        <th>@lang('admin.district')</th>
                        <th>@lang('admin.designation')</th>
                        @if(auth()->user()->role != 'client')
                        <th>@lang('admin.mobile')</th> 
                        @endif                      
                        <th>@lang('admin.onlocation')</th>
                        <th>@lang('admin.offlocation')</th>
						<th>@lang('admin.report')</th>
						<th>@lang('admin.off')</th>
                        <th>@lang('admin.duration')</th>
                        <th>Version</th>
						<th>@lang('admin.status')</th>
                    </tr>
                </thead>
                <tbody>
                	@if(count($providers) > 0)
				  @php($i=0)
                @foreach($providers as $index => $provider)
                   @php($i++)
				  
                    <tr>
                        <td>{{$i}}</td>
                        <td class="font-weight-bold">{{ $provider->first_name }} {{ $provider->last_name }}</td>
                        <td>{{$provider->district_name}}</td> 
                        @if($provider->type == 1)
                        <td>POP Engineer</td>
                        @else 
                         <td>FRT Engineer</td>
                        @endif
                        @if(auth()->user()->role != 'client')
                        <td>{{ $provider->mobile }}</td>
                        @endif
                        <td>
						{{ $provider->address?$provider->address:'N/A' }}
                        </td>
                         <td>
						{{ $provider->offaddress?$provider->offaddress:'N/A' }}
                        </td>
						@php($mytime = Carbon\Carbon::now())
						<td>{{ $provider->created_at }}</td>
						@if($provider->status == 'active')
						<td>--</td>
					    @else
						<td>{{ $provider->updated_at }}</td>				
						@endif
						 <?php
						 $startTime = Carbon\Carbon::parse($provider->created_at);
						 $currenttime = Carbon\Carbon::now();
						 $currentdate =$currenttime->toDateTimeString();
						 if($provider->status == 'active'){
                         $finishTime = Carbon\Carbon::parse($currentdate);
						 }
						 else {
						 $finishTime = Carbon\Carbon::parse($provider->updated_at);	 
						 }
                         $totalDuration = $finishTime->diffInSeconds($startTime);
                          $duration =gmdate('H:i:s', $totalDuration);						 
                         ?>						 
                        <td>{{ $duration  }}</td>
                        <td>{{ $provider->version}}</td>
  
						<td>
                           
                                @if($provider->status == 'active')
                                    <label class="btn btn-block btn-success br-10 mb-0">Online</label>
                                @else
                                    <label class="btn btn-block btn-danger br-10 mb-0">Offline</label>
                                @endif
                           
                        </td>
                    </tr>
                @endforeach
                @else
                 <tr><td colspan="10">No Records Found in this District  !..</td></tr>
                @endif
                </tbody>
                <!--<tfoot>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('admin.provides.full_name')</th>
                        <th>@lang('admin.email')</th>
                        <th>@lang('admin.mobile')</th>
                        <th>@lang('admin.provides.total_requests')</th>
                        <th>@lang('admin.provides.accepted_requests')</th>
                        <th>@lang('admin.provides.cancelled_requests')</th> 
                        <th>@lang('admin.provides.service_type')</th>
                        <th>@lang('admin.provides.online')</th>
                        <th>@lang('admin.action')</th>
                    </tr>
                </tfoot>-->
            </table>
           
        </div>
    </div>
</div>
<script>
function validate_reqst(){

        var district_dropdwn = document.getElementById('district_id');
        var district_id = district_dropdwn.options[district_dropdwn.selectedIndex].value;
        var from_date = document.getElementById('from_date').value;
        var to_date = document.getElementById('to_date').value;

        if(!district_id && !from_date && !to_date){
            document.getElementById('district_id').style.border = "1px solid red";
            document.getElementById('from_date').style.border = "1px solid red";
            document.getElementById('to_date').style.border = "1px solid red";
            return false;    // in failure case
        }
        else if(!from_date || !to_date){
            document.getElementById('district_id').style.border = "1px solid rgba(0,0,0,.15)";
            if(from_date)
                document.getElementById('from_date').style.border = "1px solid rgba(0,0,0,.15)";
            else
                document.getElementById('from_date').style.border = "1px solid red";
            if(to_date)
                document.getElementById('to_date').style.border = "1px solid rgba(0,0,0,.15)";
            else
                document.getElementById('to_date').style.border = "1px solid red";
            return false;    // in failure case
        }        
        return true;    // in success case
    }
$('#table-56').DataTable( {
        scrollX: true,    
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

