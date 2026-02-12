@extends('admin.layout.base')

@section('title', 'Attendance Reports ')

@section('content')
@php
    $user = Session::get('user');
    $DistId = null; 
    if ($user && isset($user->district_id)) {
        $DistId = $user->district_id;
    }
@endphp

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white"> 
        <form action="{{route('admin.reportattendance')}}" method="GET">
            <div class="row">
                <div class="col-xs-3">
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
                            <label for="name" class="col-xs-4 col-form-label">FromDate</label>
                            <div class="col-xs-8">
                                <input class="form-control" value="{{ @Request::get('from_date') }}" type="date" name="from_date" placeholder="From Date" id="from_date">
                            </div>
                </div>
                            
                <div class="form-group row col-md-3">
                            <label for="email" class="col-xs-4 col-form-label">ToDate</label>
                            <div class="col-xs-8">
                                <input class="form-control" value="{{ @Request::get('to_date') }}" type="date" name="to_date" placeholder="To Date" id="to_date">
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
			    Attendance Report From {{ date('Y-m-d', strtotime('first day of this month'))}} To {{date('Y-m-d', strtotime('last day of this month'))}}
                @endif				
                @if(Setting::get('demo_mode', 0) == 1)
                <span class="pull-right">(*personal information hidden in demo)</span>
                @endif
            </h5>
            <table class="table table-striped table-bordered dataTable" id="table-4">
                <thead>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('admin.district')</th>
                        <th>@lang('admin.name')</th>
                        <th>@lang('admin.designation')</th>
                        <th>@lang('admin.mobile')</th> 
                        <th>Date of Joining</th>                       
                        <th>@lang('admin.totalwork')</th>
                        <th>Absent</th>
                        <th>@lang('admin.leaves')</th>
						<th>@lang('admin.presentday')</th>
                      
                    </tr>
                </thead>
                <tbody>
				
				
                	@if(count($results ) > 0)
				  @php($i=0)
                @foreach($results  as $index => $r)
                   @php($i++)
				
                    <tr>
                       
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $r['provider']->district_name }}</td>
                        <td>{{ $r['provider']->first_name }} {{ $r['provider']->last_name }}</td>
                          @if ($r['provider']->type == 1)
                                <td>POP Engineer</td>
                            @endif

                            @if ($r['provider']->type == 2)
                                <td>Patroller Engineer</td>
                            @endif

                            @if ($r['provider']->type == 5)
                                <td>FRT Engineer</td>
                            @endif

                            @if (!in_array($r['provider']->type, [1, 2, 5]))
                                <td>Other Engineer</td>
                            @endif

                        <td>{{ $r['provider']->mobile }}</td>
                        <td>{{ $r['provider']->joiningdate ? \Carbon\Carbon::parse($r['provider']->joiningdate)->format('d-m-Y') : '' }}</td>
                        <td>{{ $r['total_working_days'] }}</td>
                        <td>{{ $r['absent_days'] }}</td>
                        <td>{{ $r['leave_days'] }}</td>
                        <td>{{ $r['present_days'] }}</td>
                        
                 
						
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
        else if(!district_id && (!from_date || !to_date)){
        	if(district_id)
            	document.getElementById('district_id').style.border = "1px solid rgba(0,0,0,.15)";
            else
            	document.getElementById('district_id').style.border = "1px solid red";
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
    
</script>
@endsection

