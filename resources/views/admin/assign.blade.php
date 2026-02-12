@extends('admin.layout.base')

@section('title', 'Assign Form')

@section('content')
@php
    $user = Session::get('user');
    $DistId = null; 
    if ($user && isset($user->district_id)) {
        $DistId = $user->district_id;
    }
@endphp
<style>
	.custom_card{
		background-color: #2B3EB1;
		border-radius: 10px;
		color: #F7F2FB;
	}
	.cfs{
		font-size:16px;
		font-weight: 600;
		color: #9A93EA;
		padding-top: 1.5rem !important;
	}
	.c_fs{
		font-size:28px;
		font-weight: 600;
		margin-bottom: 0.5rem;
	}
	.col-form-label{
		color: #A299BB;
	}
</style>

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block" style="background-color: #F2F3F7">
            <a href="#" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5 style="margin-bottom: 1rem;">Assign</h5>

             <form class="form-horizontal" action="{{route('admin.dispatcher.sendassignrequest')}}" method="POST" enctype="multipart/form-data" role="form">
            	{!! csrf_field() !!}
				<div class="form-group bg-white row top-cs">
					<div class="col-md-3 mt-3 mb-3 ml-3 mr-2 custom_card">
						<label for="first_name" class="col-xs-10 col-form-label cfs">
						<i class="ti-ticket icon-cs"></i>Ticket Id</label>
						<div class="col-xs-10">
							@if($userrequest->booking_id)
							<p class="c_fs">{{$userrequest->booking_id}}</p>
							@else
							<p class="c_fs">-</p>
							@endif
						</div>
					</div>
					<!-- NIhtin -->
					<div class="col-md-3 mt-3 mb-3 ml-3 mr-2 custom_card">
						<label for="issue_type" class="col-xs-12 col-form-label cfs">
							<i class="ti-layout-grid2-alt  icon-cs"></i>Issue Type</label>
						<div class="col-xs-10">
							@if($userrequest->downreason)
							<p class="c_fs">{{$userrequest->downreason}}</p>
							@else
							<p class="c_fs">-</p>
							@endif
						</div>
					</div>
					<div class="col-md-3 mt-3 mb-3 ml-3 mr-2 custom_card">
						<label for="description" class="col-xs-12 col-form-label cfs">
						<i class="ti-file  icon-cs"></i>Description</label>
						<div class="col-xs-10">							
							@if($userrequest->downreasonindetailed)
							<p class="c_fs"><p>{{$userrequest->downreasonindetailed}}</p></p>
							@else
							<p class="c_fs">-</p>
							@endif
						</div>
					</div>
					<!-- End Nithin -->
				</div>
				<!-- <div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Issue Type</label>
					<div class="col-xs-10">
						<p>{{$userrequest->downreason}}</p>
					</div>
				</div>
				<div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Description</label>
					<div class="col-xs-10">
						<p>{{$userrequest->downreasonindetailed}}</p>
					</div>
				</div> -->
				<div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Select Category</label>
					<div class="col-xs-10">
						<select class="form-control select-box" name="downreason" required>
							<option value="">Please Select</option>
							<?php foreach($service_types as $types) { ?>
							<option value="{{$types->name}}">{{$types->name}}</option>
						    <?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Description</label>
					<div class="col-xs-10">
						 <input class="form-control select-box" type="textarea" name="downreasonindetailed" required placeholder="Description">
					</div>
				</div>
				<input type ="hidden" value="{{$userrequest->id}}" name="request_id">
                                <div class="form-group row">
					<label for="email" class="col-xs-12 col-form-label">District</label>
					<div class="col-xs-10">
						<select class="form-control select-box" name="district_id" required id="district_id">
							<option value="">Please Select</option>
							<?php foreach($districts as $district) { ?>
							<option value="{{$district->id}}" {{ ($DistId && $DistId == $district->id) ? 'selected' : ''}}>{{$district->name}}</option>
						    <?php } ?>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label for="email" class="col-xs-12 col-form-label">Assign Member</label>
					<div class="col-xs-10">
						<select class="form-control select-box" name="provider_id" required id ="provider_id">
							<option value="">Please Select</option>
							<?php foreach($providers as $provider) { ?>
							<option value="{{$provider->id}}">{{$provider->first_name}}{{$provider->last_name}}</option>
						    <?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<label for="zipcode" class="col-xs-12 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary btn-cstm">Assign</button>
					</div>
				</div>
			</form>


		</div>
    </div>
</div>

@endsection
@section('scripts')
 <script type="text/javascript">
$('#district_id').change(function(){
        var nid = $(this).val();
        if(nid){
        $.ajax({
           type:"get",
              url: "{{ url('/admin/getSearchproviderlist') }}/" + nid,

            success:function(res)
           {       
                if(res)
                {
                    $("#provider_id").empty();
                    $("#provider_id").append('<option>Select Provider</option>');
                    $.each(res,function(key,value){
                        $("#provider_id").append('<option value="'+key+'">'+value+'</option>');
                    });
                }
           }

        });
        }
});
</script>
@endsection

