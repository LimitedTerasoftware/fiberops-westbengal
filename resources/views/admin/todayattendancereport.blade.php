@extends('admin.layout.base')

@section('title', 'Today Attendance Report')

@section('content')

<div class="content-area py-1"> 
        <div class="box box-block bg-white">
            @if(Setting::get('demo_mode') == 1)
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : @lang('admin.demomode')
                </div>
                @endif
            <h5 class="mb-1" style="color:#0275d8;">
			 Today Attendance Report
                @if(Setting::get('demo_mode', 0) == 1)
                <span class="pull-right">(*personal information hidden in demo)</span>
                @endif
            </h5>
            <table class="table table-striped table-bordered dataTable" id="table-4">
                <thead>
                    <tr>
                        <th>Total No of Users</th>
                        <th>Total No of Users Logged in</th>
                        <th>Total No of Users Not logged</th>
                    </tr>
                </thead>
                <tbody>		
                     <tr>
                       <td><a href="{{ route('admin.provider.index') }}" target="_blank">{{$totalusers}}</a></td> 
                       <td><a href="{{ route('admin.attendance') }}" target="_blank">{{$loggedinusers}}</a></td>
                       <td><a href="{{ route('admin.attendance_list') }}?status=absent" target="_blank">{{$totalusers - $loggedinusers}}</a></td> 			
                    </tr>
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
@endsection

