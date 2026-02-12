@extends('admin.layout.base')

@section('title', 'Update Provider ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <a href="{{ route('admin.provider.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

            <h5 style="margin-bottom: 2em;">@lang('admin.provides.update_provider')</h5>

            <form class="form-horizontal" action="{{route('admin.provider.update', $provider->id )}}" method="POST" enctype="multipart/form-data" role="form">
                {{csrf_field()}}
                <input type="hidden" name="_method" value="PATCH">
                <div class="form-group row">
                    <label for="first_name" class="col-xs-2 col-form-label">@lang('admin.first_name')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="{{ $provider->first_name }}" name="first_name" required id="first_name" placeholder="First Name">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="last_name" class="col-xs-2 col-form-label">@lang('admin.last_name')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="{{ $provider->last_name }}" name="last_name" required id="last_name" placeholder="Last Name">
                    </div>
                </div>


                <div class="form-group row">
                    
                    <label for="picture" class="col-xs-2 col-form-label">@lang('admin.picture')</label>
                    <div class="col-xs-10">
                    @if(isset($provider->avatar))
                        <img style="height: 90px; margin-bottom: 15px; border-radius:2em;" src="{{img($provider->avatar)}}">
                    @endif
                        <input type="file" accept="image/*" name="avatar" class="dropify form-control-file" id="picture" aria-describedby="fileHelp">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="mobile" class="col-xs-2 col-form-label">@lang('admin.mobile')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="number" value="{{ $provider->mobile }}" name="mobile" required id="mobile" placeholder="Mobile">
                    </div>
                </div>

               <div class="form-group row">
                    <label for="mobile" class="col-xs-2 col-form-label">@lang('admin.email')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="email" value="{{ $provider->email}}" name="email" required id="email" placeholder="Email">
                    </div>
                </div>

              @if(count($zonalmanagers) > 0)
				<div class="form-group row">
					<label for="email" class="col-xs-2 col-form-label">Zonal Manager</label>
					<div class="col-xs-10">
						<select class="form-control" name="zone_id" id="zone_id">
							<option value="">Please Select Zonalmanager</option>
							@foreach($zonalmanagers as $zonal)
							<option value="{{$zonal->id}}" {{ $zonal->id== $provider->zone_id ? 'selected' : '' }}>{{$zonal->Name}}</option>
							@endforeach
							
						</select>
					</div>
				</div>
                @endif

               @if(count($teams) > 0)
				<div class="form-group row">
					<label for="email" class="col-xs-2 col-form-label">Teams</label>
					<div class="col-xs-10">
						<select class="form-control" name="team_id" id="team_id">
							<option value="">Please Select Teams</option>
							@foreach($teams as $team)
							<option value="{{$team->id}}" {{ $team->id== $provider->team_id ? 'selected' : '' }}>{{$team->name}}</option>
							@endforeach
							
						</select>
					</div>
				</div>
                @endif




               @if(count($districts) > 0)
				<div class="form-group row">
					<label for="email" class="col-xs-2 col-form-label">Districts</label>
					<div class="col-xs-10">
						<select class="form-control" name="district_id" id="district_id">
							<option value="">Please Select District</option>
							@foreach($districts as $dist)
							<option value="{{$dist->id}}" {{ $dist->id== $provider->district_id ? 'selected' : '' }}>{{$dist->name}}</option>
							@endforeach
							
						</select>
					</div>
				</div>
                @endif


             @if(count($blocks) > 0)
				<div class="form-group row">
					<label for="email" class="col-xs-2 col-form-label">Blocks</label>
					<div class="col-xs-10">
						<select class="form-control" name="block_id" id="block_id">
							<option value="">Please Select Block</option>
							@foreach($blocks as $block)
							<option value="{{$block->id}}" {{ $block->id== $provider->block_id ? 'selected' : '' }}>{{$block->name}}</option>
							@endforeach
							
						</select>
					</div>
				</div>
                @endif


               
               <div class="form-group row">
					<label for="email" class="col-xs-2 col-form-label">Designation Type</label>
					<div class="col-xs-10">
						<select class="form-control" name="type">
							<option value="">Please Select Designation</option>
							<option value="1" {{ $provider->type == 1 ? 'selected' : '' }}>OFC</option>
							<option value="2" {{ $provider->type == 2 ? 'selected' : '' }}>FRT</option>
                                                        <option value="5" {{ $provider->type == 5 ? 'selected' : '' }}>Patroller</option>
                                                        <option value="3" {{ $provider->type == 3 ? 'selected' : '' }}>Zonal incharge</option>
                                                        <option value="4" {{ $provider->type == 4 ? 'selected' : '' }}>District incharge</option>

						</select>
					</div>
				</div>





               <div class="form-group row">
                    <label for="date" class="col-xs-2 col-form-label">Joining Date</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="date" value="" name="joindate" >
                    </div>
                </div>
                <div class="form-group row">
                    <label for="zipcode" class="col-xs-2 col-form-label"></label>
                    <div class="col-xs-10">
                        <button type="submit" class="btn btn-primary">@lang('admin.provides.update_provider')</button>
                        <a href="{{route('admin.provider.index')}}" class="btn btn-default">@lang('admin.cancel')</a>
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

