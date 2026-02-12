@extends('admin.layout.base')

@section('title', 'Dashboard ')

@section('styles')
	<!--<link rel="stylesheet" href="{{asset('main/vendor/jvectormap/jquery-jvectormap-2.0.3.css')}}">-->
@endsection

@section('content')
<style>
.box {border-radius: 20px;box-shadow: 0px 5px 10px 0px rgba(0, 0, 0, 0.5);}
.bg-chocolate{background-color:#d2691e;}
.bg-darkmagenta	{background-color:#8b008b;}
.bg-olivedrab	{background-color:#6b8e23;}
.bg-teal	{background-color:#008080;}
.bg-yellowgreen	{background-color:#9acd32;}
.bg-peru	{background-color:#cd853f;}
.filter-box{border-radius: 25px;height: 30px !important;}

</style>

<?php
use \Carbon\Carbon;
$todaydate = Carbon::today();
$today = $todaydate->toDateString();

$yesterdaydate = Carbon::yesterday();
$yesterday= $yesterdaydate->toDateString();

?>
<div class="content-area py-1">
<div class="container-fluid">
    <div class="row row-md">
		
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets-ongoing-intervals') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.assigned')</h6>
					<h1 class="mb-1">{{$ongoing_tickets}}</h1>
				</div>
			</div></a>
		</div>

                <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets-ongoing-history') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.ongoing')</h6>
                                        <h1 class="mb-1">{{$totalongoing_tickets}}</h1>
                                        <span class="text-muted font-180">Today &nbsp;&nbsp;<a href="/public/westbengal/public/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=OnGoing">{{$todayongoing_tickets}}</a></span><br/>
                                        <span class="text-success font-180"><b>Yesterday :</b> &nbsp;&nbsp;<a href="/public/westbengal/public/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=OnGoing">{{$yesterdayongoing_tickets}}</a></span><br/>

				</div>
			</div></a>
		</div>

                <a href="{{ url('/admin/tickets') }}"><div class="col-lg-3 col-md-6 col-xs-12">
			<div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-danger"></span><i class="ti-archive"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.Tickets')</h6>
					<h1 class="mb-1">{{$master_tickets}}</h1>
				</div>
			</div>
		</div></a>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets-completed-history') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-success"></span><i class="ti-thumb-up"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.resolved')</h6>
					<h1 class="mb-1">{{$completed_tickets}}</h1>
                                        <span class="text-muted font-180">Today &nbsp;&nbsp;<a href="/public/westbengal/public/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=Completed">{{$todayclosed_tickets}}</a></span><br/>
                                        <span class="text-success font-180"><b>Yesterday</b> &nbsp;&nbsp;<a href="/public/westbengal/public/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Completed">{{$yesterdayclosed_tickets}}</a></span><br/>
                                       </div>
			</div></a>
		</div>

		 <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?status=Onhold') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-warning"></span><i class="ti-control-pause"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">On hold Tickets</h6>
					<h1 class="mb-1">{{$onhold_tickets}}</h1>
                                        <table>
                                       <tr>
                                        <td><span class="text-muted font-180">Today : &nbsp;&nbsp;<a href="/public/westbengal/public/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=Onhold">{{$todayonhold_tickets}}</a></span></td>
                                        <td><span class="text-muted font-90">Power :&nbsp;&nbsp; <a href="{{ url('/admin/tickets?category=Power&status=Onhold') }}">{{$holdups}}</a></span></td>
                                          </tr>
                                       <tr> 
                                        <td><span class="text-success font-90"><b>Yesterday :</b> &nbsp;&nbsp;<a href="/public/westbengal/public/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Onhold">{{$yesterdayonhold_tickets}}</a></span></td>
                                        <td><span class="text-muted font-90">Fiber :&nbsp;&nbsp; <a href="{{ url('/admin/tickets?category=Fiber&status=Onhold') }}">{{$holdfiber}}</a></span></td>
                                        </tr>
                                        <tr>
                                        <td><span class="text-muted font-90">@lang('admin.dashboard.software_hardware'):&nbsp;&nbsp; <a href="{{ url('/admin/tickets?category=Software/Hardware&status=Onhold') }}">{{$holdelectronics}}</a>&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                                        <td><span class="text-muted font-90">Solar :&nbsp;&nbsp; <a href="{{ url('/admin/tickets?category=Solar&status=Onhold') }}">{{$holdsolar}}</a></span></td>
                                       </tr>
                                         <tr>
                                          <td><span class="text-muted font-90">@lang('admin.dashboard.ccu_battery') :&nbsp;&nbsp; <a href="{{ url('/admin/tickets?category=CCU/Battery&status=Onhold') }}">{{$holdccu}}</a></span></td>
                                          <td><span class="text-muted font-90">Olt :&nbsp;&nbsp; <a href="{{ url('/admin/tickets?category=OLT&status=Onhold') }}">{{$holdolt}}</a></span></td>
                                        </tr>
                                        <tr>
                                        <td><span class="text-muted font-90">Others :&nbsp;&nbsp; <a href="{{ url('/admin/tickets?category=Others&status=Onhold') }}">{{$holdothers}}</a></span></td>
                                        <td>
                                        </td>
                                        </tr>
                                        </table>
                                                                                

				</div>
			</div></a>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?category=Power') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-success"></span><i class="ti-plug"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">Power</h6>
					<h1 class="mb-1">{{$ups}}</h1>
                                         <table>
                                        <tr><td><span class="text-muted font-90">Not Started <a href="{{ url('/admin/tickets?category=Power&status=NotStarted') }}">{{$notstartedups}}</a></span></td></tr>
                                        <tr><td><span class="text-muted font-90">Ongoing <a href="{{ url('/admin/tickets?category=Power&status=OnGoing') }}">{{$ongoingups}}</a></span></td></tr>
                                        <tr>
                                         <td><span class="text-muted font-90">Hold <a href="{{ url('/admin/tickets?category=Power&status=Onhold') }}">{{$holdups}}</a></span></td>
                                         <td><span class="text-success font-90"><b>Today :</b>  <a href="/public/westbengal/public/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=Power&status=Completed">{{$completedups_today}}</a></span></td>
                                        </tr>
                                        <tr>
                                         <td><span class="text-muted font-90">Completed <a href="{{ url('/admin/tickets?category=Power&status=Completed') }}">{{$completedups}} </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                                         <td><span class="text-success font-90"><b>Yesterday :</b>  <a href="/public/westbengal/public/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=Power&status=Completed">{{$completedups_yesterday}}</a></span></td>
                                        </tr>
                                        <tr><td>&nbsp;</td><td>&nbsp;</td>
                                       </tr>
                                       
                                        </table>
                                        
				</div>
			</div></a>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?category=Fiber') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-success"></span><i class="ti-pulse"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.fiber')</h6>
					<h1 class="mb-1">{{$fiber}}</h1>
                                        <table>
                                        <tr><td><span class="text-muted font-90">Not Started <a href="{{ url('/admin/tickets?category=Fiber&status=NotStarted') }}">{{$notstartedfiber}}</a></span></td></tr>
                                        <tr><td><span class="text-muted font-90">Ongoing <a href="{{ url('/admin/tickets?category=Fiber&status=OnGoing') }}">{{$ongoingfiber}}</a></span></td></tr>
                                        <tr>
                                          <td><span class="text-muted font-90">Hold <a href="{{ url('/admin/tickets?category=Fiber&status=Onhold') }}">{{$holdfiber}}</a></span></td>
                                          <td><span class="text-success font-90"><b>Today:</b>&nbsp;&nbsp;<a href="/public/westbengal/public/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=Fiber&status=Completed">{{$completedfiber_today}}</a></span></td>
                                        </tr>
                                        <tr>
                                           <td><span class="text-muted font-90">Completed <a href="{{ url('/admin/tickets?category=Fiber&status=Completed') }}">{{$completedfiber}}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                                           <td><span class="text-success font-90"><b>Yesterday :</b>&nbsp;&nbsp;<a href="/public/westbengal/public/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=Fiber&status=Completed">{{$completedfiber_yesterday}}</a></span></td>
                                        </tr>
                                        <tr><td>&nbsp;</td><td>&nbsp;</td>
                                        </table>
					</div>
			</div></a>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?category=Software/Hardware') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-warning"></span><i class="ti-android"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.software_hardware')</h6>
					<h1 class="mb-1">{{$electronics}}</h1>
                                        <table>
                                        <tr><td><span class="text-muted font-90">Not Started <a href="{{ url('/admin/tickets?category=Software/Hardware&status=NotStarted') }}">{{$notstartedelectronics}}</a></span></td></tr>
                                        <tr><td><span class="text-muted font-90">Ongoing <a href="{{ url('/admin/tickets?category=Software/Hardware&status=OnGoing') }}">{{$ongoingelectronics}}</a></span></td></tr>
                                        <tr>
                                          <td><span class="text-muted font-90">Hold <a href="{{ url('/admin/tickets?category=Software/Hardware&status=Onhold') }}">{{$holdelectronics}}</a></span></td>
                                          <td><span class="text-success font-90"><b>Today :</b> <a href="/public/westbengal/public/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=Software/Hardware&status=Completed">{{$completedelectronics_today}}</a></span></td>
                                        </tr>
                                        <tr>
                                          <td><span class="text-muted font-90">Completed <a href="{{ url('/admin/tickets?category=Software/Hardware&status=Completed') }}">{{$completedelectronics}}</a></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                          <td><span class="text-success font-90"><b>Yesterday :</b> <a href="/public/westbengal/public/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=Software/Hardware&status=Completed">{{$completedelectronics_yesterday}}</a></span></td>
                                        </tr>
                                        <tr><td>&nbsp;</td></tr>
                                        </table>
					
				</div>
			</div></a>
		</div>
		
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?category=Solar') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-dropbox"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.solar')</h6>
					<h1 class="mb-1">{{$solar}}</h1>
					<span class="text-muted font-90">Not Started <a href="{{ url('/admin/tickets?category=Solar&status=NotStarted') }}">{{$notstartedsolar }}</a></span><br/>
					<span class="text-muted font-90">Ongoing <a href="{{ url('/admin/tickets?category=Solar&status=OnGoing') }}">{{$ongoingsolar}}</a></span><br/>
					
                                        <table>
                                        <tr>
                                           <td> <span class="text-muted font-90">Hold <a href="{{ url('/admin/tickets?category=Solar&status=Onhold') }}">{{$holdsolar}}</a></span></td>
                                           <td> <span class="text-success font-90"><b>Today :</b> <a href="/public/westbengal/public/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=Solar&status=Completed">{{$completedsolar_today}}</a></span></td>
                                        </tr>
                                        <tr>
                                           <td> <span class="text-muted font-90">Completed <a href="{{ url('/admin/tickets?category=Solar&status=Completed') }}">{{$completedsolar}}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                                           <td> <span class="text-success font-90"><b>Yesterday :</b> <a href="/public/westbengal/public/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=Solar&status=Completed">{{$completedsolar_yesterday}}</a></span></td>
                                        </tr>
                                        </table>
                                       
                                        

				</div>
			</div></a>
		</div>

                <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?category=OLT') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-layout-grid2-thumb"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.olt')</h6>
					<h1 class="mb-1">{{$olt}}</h1>
					<span class="text-muted font-90">Not Started <a href="{{ url('/admin/tickets?category=OLT&status=NotStarted') }}">{{$notstartedolt}}</a></span><br/>
					<span class="text-muted font-90">Ongoing <a href="{{ url('/admin/tickets?category=OLT&status=OnGoing') }}">{{$ongoingolt}}</a></span><br/>
					
                                        <table>
                                         <tr>
                                           <td><span class="text-muted font-90">Hold <a href="{{ url('/admin/tickets?category=OLT&status=Onhold') }}">{{$completedsolar_yesterday}}</a></span></td>
                                           <td><span class="text-success font-90"><b>Today :</b> <a href="/public/westbengal/public/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=OLT&status=Completed">{{$completedolt_today}}</a></span></td>
                                        </tr>

                                        <tr>
                                           <td><span class="text-muted font-90">Completed <a href="{{ url('/admin/tickets?category=OLT&status=Completed') }}">{{$completedolt}}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                                           <td><span class="text-success font-90"><b>Yesterday :</b> <a href="/public/westbengal/public/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=OLT&status=Completed">{{$completedolt_yesterday}}</a></span></td>
                                        </tr>
                                        </table>

                                        

				</div>
			</div></a>
		</div>
  
                <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?category=CCU/Battery') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.ccu_battery')</h6>
					<h1 class="mb-1">{{$ccu}}</h1>
					<span class="text-muted font-90">Not Started <a href="{{ url('/admin/tickets?category=CCU/Battery&status=NotStarted') }}">{{$notstartedccu}}</a></span><br/>
					<span class="text-muted font-90">Ongoing <a href="{{ url('/admin/tickets?category=CCU/Battery&status=OnGoing') }}">{{$ongoingccu}}</a></span><br/>
					
                                        <table>
                                          <td><span class="text-muted font-90">Hold <a href="{{ url('/admin/tickets?category=CCU/Battery&status=Onhold') }}">{{$holdccu}}</a></span></td>
                                          <td><span class="text-success font-90"><b>Today:</b> <a href="/public/westbengal/public/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=CCU/Battery&status=Completed">{{$completedccu_today}}</a></span></td>
                                        <tr>
                                           <td><span class="text-muted font-90">Completed <a href="{{ url('/admin/tickets?category=CCU/Battery&status=Completed') }}">{{$completedccu}}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                                           <td><span class="text-success font-90"><b>Yesterday :</b> <a href="/public/westbengal/public/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=CCU/Battery&status=Completed">{{$completedccu_yesterday}}</a></span></td>
                                        </tr>
                                        </table>

                                        

				</div>
			</div></a>
		</div>



               <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?category=Others') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">Others</h6>
					<h1 class="mb-1">{{$others}}</h1>
					<span class="text-muted font-90">Not Started <a href="{{ url('/admin/tickets?category=Others&status=NotStarted') }}">{{$notstartedothers}}</a></span><br/>
					<span class="text-muted font-90">Ongoing <a href="{{ url('/admin/tickets?category=Others&status=OnGoing') }}">{{$ongoingothers}}</a></span><br/>
                                        <table>
                                        <tr>
                                         <td><span class="text-muted font-90">Hold <a href="{{ url('/admin/tickets?category=Others&status=Onhold') }}">{{$holdothers}}</a></span></td>
                                         <td><span class="text-success font-90"><b>Today :</b> <a href="/public/westbengal/public/admin/tickets?from_date={{$today}}&to_date={{$today}}&category=Others&status=Completed">{{$completedothers_today}}</a></span></td>
                                        </tr>					  
                                        <tr>
                                           <td><span class="text-muted font-90">Completed <a href="{{ url('/admin/tickets?category=Others&status=Completed') }}">{{$completedothers}}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                                           <td><span class="text-success font-90"><b>Yesterday :</b> <a href="/public/westbengal/public/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&category=Others&status=Completed">{{$completedothers_yesterday}}</a></span></td>
                                        </tr>
                                        </table>

                                        

				</div>
			</div></a>
		</div>
	
		
	</div>
	<div class="row row-md">
		
	</div>

	<div class="row row-md mb-2">
		<div class="col-md-12">
			<div class="box bg-white">
					<div class="box-block clearfix">
						<h5 class="float-xs-left">Unique Teams Work</h5>
						<div class="float-xs-right">
 					     </div>
					<table class="table mb-md-0">
					   <tr>
                                             <th>No Of Teams</th>
                                             <th>Completed Teams</th>
                                             <th>Hold Teams Running</th>
                                             <th>Unique Teams Running</th>
                                             <th>Not Started Teams</th>
                                             <th>Today Not Started</th>
                                          </tr>
                                          <tr>
                                           <td><a href="{{ url('/admin/totalteams') }}">{{$teamcount}}</a></td>
                                           <td><a href="{{ url('/admin/completedteams') }}">{{$completedteams}}</a></td>
                                           <td><a href="{{ url('/admin/holdteams') }}">{{$holdteams}}</a></td>
                                           <td><a href="{{ url('/admin/uniqueteams') }}">{{$runningteams}}</a></td>
                                           <td><a href="{{ url('/admin/notstartedteams') }}">{{$notrunningteams}}</a></td>
                                           <td><a href="{{ url('/admin/todaynotstartedteams') }}">{{$notworkedteamscount }}</a></td>
                                           </tr>
					</table>
				</div>
			</div>

		
		
		</div>

	</div>
</div>
@endsection
