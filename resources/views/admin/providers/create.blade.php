@extends('admin.layout.base')

@section('title', 'Add Provider ')

@section('content')

<style type="text/css">
	.shadow-gray {
    box-shadow: 0 0 5px 1px #3333332e !important;
	}
	.col-form-label{
		font-size: 13px !important;
		font-weight: 600;
	}
	.p-2-5{
		padding:2.5rem;
	}
</style>
<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{ route('admin.provider.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.contacts.new_contact')</h5>

      <form class="form-horizontal" action="{{route('admin.provider.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <div class="top-cs box-block shadow-gray">
        	<h5 class="mb-2">Contact Details</h5>
        	<div class="box-block">
					<div class="form-group row">
						<div class="col-sm-12 col-md-6">
							<label for="first_name" class="col-form-label  ">@lang('admin.first_name')
							<span class="look-a-like">*</span></label>
							<input class="form-control select-box" type="text" value="{{ old('first_name') }}" name="first_name" required id="first_name" placeholder="First Name">
						</div>
						<div class="col-sm-12 col-md-6">
							<label for="last_name" class="col-form-label  ">@lang('admin.last_name')
							<span class="look-a-like">*</span></label>
							<input class="form-control select-box" type="text" value="{{ old('last_name') }}" name="last_name" required id="last_name" placeholder="Last Name">
						</div>
					</div>

					<div class="form-group row">
						<div class="col-sm-12 col-md-6">
							<label for="email" class="col-form-label  ">Designation Type</label>
							<select class="form-control select-box" name="type">
								<option value="">Please Select Designation</option>
								<option value="1">OFC</option>
								<option value="2">FRT</option>
                                                                <option value="5">Patroller</option>
                                                                <option value="3">Zonal incharge</option>
                                                                <option value="4">District incharge</option>
                                                               
							</select>
						</div>

                                                @if(count($zonalmanagers) > 0)
						<div class="col-sm-12 col-md-6">
							<label for="email" class="col-form-label  ">Zonal Manager</label>
							<select class="form-control select-box" name="zone_id" id="zone_id">
								<option value="0">Please Select Zonal Manager</option>
								@foreach($zonalmanagers as $zonal)
								<option value="{{$zonal->id}}">{{$zonal->Name}}</option>
								@endforeach
								
							</select>
						</div>
						@endif


                                                @if(count($teams) > 0)
						<div class="col-sm-12">
							<label for="email" class="col-form-label  ">Teams</label>
							<select class="form-control select-box" name="team_id" id="team_id">
								<option value="0">Please Select Team</option>
								@foreach($teams as $team)
								<option value="{{$team->id}}">{{$team->name}}</option>
								@endforeach
								
							</select>
						</div>
						@endif


					</div>

					<div class="form-group row">
						@if(count($districts) > 0)
						<div class="col-sm-12 col-md-6">
							<label for="email" class="col-form-label  ">Districts</label>
							<select class="form-control select-box" name="district_id" id="district_id">
								<option value="">Please Select District</option>
								@foreach($districts as $dist)
								<option value="{{$dist->id}}">{{$dist->name}}</option>
								@endforeach
								
							</select>
						</div>
						@endif
						@if(count($blocks) > 0)
						<div class="col-sm-12 col-md-6">
							<label for="email" class="col-form-label  ">Blocks</label>
							<select class="form-control select-box" name="block_id" id="block_id">
								<option value="">Please Select Block</option>
								@foreach($blocks as $block)
								<option value="{{$block->id}}">{{$block->name}}</option>
								@endforeach
								
							</select>
						</div>
						@endif
					</div>

					<div class="form-group row">
						<div class="col-sm-12 col-md-6">
							<label for="email" class="col-form-label  ">@lang('admin.email')
							<span class="look-a-like">*</span></label>
							<input class="form-control select-box" type="email" required name="email" value="{{old('email')}}" id="email" placeholder="Email">
						</div>
						<div class="col-sm-12 col-md-6">
							<label for="mobile" class="col-form-label  ">@lang('admin.mobile')
							<span class="look-a-like">*</span></label>
							<input class="form-control select-box" type="number" value="{{ old('mobile') }}" name="mobile" required id="mobile" placeholder="Mobile">
						</div>
					</div>

					<div class="form-group row">
						<div class="col-sm-12 col-md-6">
							<label for="password" class="col-form-label  ">@lang('admin.password')</label>
							<input class="form-control select-box" type="password" name="password" id="password" placeholder="Password">
						</div>
						<div class="col-sm-12 col-md-6">
							<label for="password_confirmation" class="col-form-label  ">@lang('admin.provides.password_confirmation')</label>
							<input class="form-control select-box" type="password" name="password_confirmation" id="password_confirmation" placeholder="Re-type Password">
						</div>
					</div>
					 <div class="form-group row">
                    <label for="date" class="col-xs-2 col-form-label">Joining Date</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="date" value="" name="joiningdate" >
                    </div>
                </div>

					<div class="form-group row">
						<label for="picture" class="col-xs-12 col-form-label  ">@lang('admin.profile_picture') </label>
						<div class="col-xs-10">
							<input type="file" accept="image/*" name="avatar" class="dropify form-control-file" id="picture" aria-describedby="fileHelp">
						</div>
					</div>
					 

					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.contacts.add_contact')</button>
							<a href="{{route('admin.provider.index')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
						</div>
					</div>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>
@endsection
@section('scripts')
<script>
$("select[id='district_id']").change(function(){
  var district_id = $(this).val();
  $.get('{{url("admin/ajax-blocks-providers")}}/'+district_id,function(data) {
    $("#block_id").empty().append(data);      
  });
});
</script>
@endsection
