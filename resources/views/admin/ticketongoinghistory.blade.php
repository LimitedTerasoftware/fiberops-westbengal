@extends('admin.layout.base')

@section('title', 'Ongoing History')

@section('content')
  <?php
  use \Carbon\Carbon;
$todaydate = Carbon::today();
$today = $todaydate->toDateString();

$yesterdaydate = Carbon::yesterday();
$yesterday= $yesterdaydate->toDateString();

  ?>
    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">Ongoing History</h5>
                   
                <table class="table row-bordered dataTable nowrap display" style="width:100%">
				  <thead>
						<tr>
						  <th scope="col">#</th>
						  <th scope="col">Tickets</th>
						  <th scope="col">Manually Created Tickets</th>
						  <th scope="col">Regular Tickets</th>
						</tr>
                   				  </thead>
				  <tbody>
						<tr>
						  <th scope="row">Tickets Ongoing</th>
						  <td><a href="{{ url('/admin/tickets?status=OnGoing') }}">{{$totalongoing_tickets}}</a></td>
						  <td><a href="{{ url('/admin/tickets?status=OnGoing&autoclose=Manual') }}">{{$manual_tickets}}</a></td>
                                                  <td><a href="{{ url('/admin/tickets?status=OnGoing&autoclose=Auto') }}">{{$regular_tickets}}</a></td>
						  </tr>
						<tr>
						  <th scope="row">Today</th>
							<td>
								<a href="{{ url('/admin/tickets?status=OnGoing&from_date=' . $today . '&to_date=' . $today) }}">
									{{ $todayongoing_tickets }}
								</a>
							</td>

							<td>
								<a href="{{ url('/admin/tickets?status=OnGoing&autoclose=Manual&from_date=' . $today . '&to_date=' . $today) }}">
									{{ $todaymanual_tickets }}
								</a>
							</td>

							<td>
								<a href="{{ url('/admin/tickets?status=OnGoing&autoclose=Auto&from_date=' . $today . '&to_date=' . $today) }}">
									{{ $todayregular_tickets }}
								</a>
							</td> 
                        </tr>
						<tr>
						  <th scope="row">Yesterday</th>
							<td>
								<a href="{{ url('/admin/tickets') . '?status=OnGoing&from_date=' . $yesterday . '&to_date=' . $yesterday }}">
									{{ $yesterdayongoing_tickets }}
								</a>
							</td>

							<td>
								<a href="{{ url('/admin/tickets') . '?status=OnGoing&autoclose=Manual&from_date=' . $yesterday . '&to_date=' . $yesterday }}">
									{{ $yesterdaymanual_tickets }}
								</a>
							</td>

							<td>
								<a href="{{ url('/admin/tickets') . '?status=OnGoing&autoclose=Auto&from_date=' . $yesterday . '&to_date=' . $yesterday }}">
									{{ $yesterdayregular_tickets }}
								</a>
							</td>                                                
						</tr>
				  </tbody>
              </table>                
			  </div>
            
        </div>
    </div>
@endsection