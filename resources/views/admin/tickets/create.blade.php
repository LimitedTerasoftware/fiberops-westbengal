@extends('admin.layout.base')

@section('title', 'Add Ticket')

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
            <a href="{{ route('admin.tickets') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">Add New Ticket</h5>

      <form class="form-horizontal" action="{{route('admin.tickets.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
				<div class="form-group row">
					<label for="ticketid" class="col-xs-12 col-form-label">@lang('admin.request.Ticket_ID')</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ old('ticketid') }}" name="ticketid" required id="ticketid" placeholder=" Enter Ticket ID Start with TKIT000000 format">
					</div>
				</div>

				@if(count($districts) > 0)
				<div class="form-group row">
					<label for="district" class="col-xs-12 col-form-label">Districts</label>
					<div class="col-xs-10">
						<select class="form-control" name="district" id="district">
							<option value="">Please Select District</option>
							@foreach($districts as $dist)
							<option value="{{$dist->id}}" rel="{{$dist->id}}"
							 {{ ($DistId && $DistId == $dist->id) ? 'selected' : '' }}
							>{{$dist->name}}</option>
							@endforeach
							
						</select>
					</div>
				</div>
        @endif

        @if(count($blocks) > 0)
				<div class="form-group row">
					<label for="mandal" class="col-xs-12 col-form-label">Mandal</label>
					<div class="col-xs-10">
						<select class="form-control" name="mandal" id="mandal">
							<option value="">Please Select Mandal</option>
							@foreach($blocks as $block)
							<option value="{{$block->name}}" rel="{{$block->id}}" >{{$block->name}}</option>
							@endforeach
							
						</select>
					</div>
				</div>
        @endif


       @if(count($gplist) > 0)
				<div class="form-group row">
					<label for="mandal" class="col-xs-12 col-form-label">Gp Name</label>
					<div class="col-xs-10">
						<select class="form-control" name="gpname" id="gpname">
							<option value="">Please Select GP</option>
							@foreach($gplist as $list)
							<option value="{{$list->gp_name}}" rel="{{$list->latitude}}"  rel1="{{$list->longitude}}">{{$list->gp_name}}</option>
							@endforeach
							
						</select>
					</div>
				</div>
        @endif


				<div class="form-group row">
					<label for="lat" class="col-xs-12 col-form-label">Latitude</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" required name="lat" value="{{old('lat')}}" id="lat" placeholder="Latitude">
					</div>
				</div>

				<div class="form-group row">
					<label for="log" class="col-xs-12 col-form-label">Longitude</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" name="log" id="log" placeholder="Longitude">
					</div>
				</div>

				<div class="form-group row">					
					<div class="col-xs-5">
						<label for="downdate" class="col-form-label">Down Date</label>
						<input class="form-control" type="date" name="downdate" id="downdate" placeholder="Down Date">
					</div>					
					<div class="col-xs-5">
						<label for="downtime" class="col-form-label">Down Time</label>
						<input class="form-control" type="time" value="{{ old('downtime') }}" name="downtime" required id="downtime" placeholder="Down Time">
					</div>
				</div>

				<div class="form-group row">
					<label for="downreason" class="col-xs-12 col-form-label">Down Reason</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" name="downreason" id="downreason" placeholder="Down Reason">
					</div>
				</div>

				<div class="form-group row">
					<label for="downreasonindetailed" class="col-xs-12 col-form-label">Down Reason In Detailed</label>
					<div class="col-xs-10">
						<textarea class="form-control" type="textarea" name="downreasonindetailed" id="downreasonindetailed" placeholder="Down Reason In Detailed"></textarea>
					</div>
				</div>

				<div class="form-group row">
					<label for="zipcode" class="col-xs-12 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">Add Ticket</button>
						<a href="{{route('admin.tickets')}}" class="btn btn-default">@lang('admin.cancel')</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>
@endsection
@section('scripts')
<script>
$("select[id='district']").change(function(){
  var district_id = $('#district option:selected').attr('rel');
  $.get('{{url("admin/ajax-blocks")}}/'+district_id,function(data) {
    $("#mandal").empty().append(data);      
  });
});

$("select[id='mandal']").change(function(){
  var block_id = $('#mandal option:selected').attr('rel');
  $.get('{{url("admin/ajax-gps")}}/'+block_id,function(data) {
    $("#gpname").empty().append(data);      
  });
});


$("select[id='gpname']").change(function() { 
 var lat= $('#gpname option:selected').attr('rel');
 var long = $('#gpname option:selected').attr('rel1');
  
$("#lat").val(lat);
$("#log").val(long);
});
</script>
@endsection
