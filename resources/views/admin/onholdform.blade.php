@extends('admin.layout.base')

@section('title', 'On Hold Form')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
			<a href="javascript:void(0)" onclick="history.back()" class="btn btn-default pull-right">
				<i class="fa fa-angle-left"></i> Back
			</a>

			<h5>On Hold Form</h5>

            <form class="form-horizontal" action="{{route('admin.dispatcher.onholdrequest')}}" method="POST" enctype="multipart/form-data" role="form">
            	{!! csrf_field() !!}
				<div class="form-group row">
					<label for="first_name" class="col-xs-12 col-form-label">Ticket Id</label>
					<div class="col-xs-10">
						<p>{{$userrequest->booking_id}}</p>
					</div>
				</div>
				<div class="form-group row">
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
				</div>
				<div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Select Category</label>
					<div class="col-xs-10">
						<select class="form-control" name="downreason" id="category"  required>
							<option value="">Please Select</option>
							<?php foreach($service_types as $types) { ?>
							<option value="{{ $types->id }}" data-name="{{ $types->name }}">{{$types->name}}</option>
						    <?php } ?>
						</select>
						<input type="hidden" name="downreason_name" id="downreason_name">

					</div>
				</div>
				<div class="form-group row">
                    <label for="sub_category" class="col-xs-12 col-form-label">Sub Category</label>
					<div class="col-xs-10">
                        <select class="form-control " name="sub_category" id="sub_category" required>
							<option value="">Sub Category</option>
						</select>
                         <input type="hidden" name="sub_category_name" id="sub_category_name">
					</div>
                </div>
				<div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Close Reason</label>
					<div class="col-xs-10">
						 <input class="form-control" type="textarea" name="downreasonindetailed" required placeholder="Description">
					</div>
				</div>
				<input type ="hidden" value="{{$userrequest->id}}" name="request_id">
                                <input type ="hidden" value="{{$userrequest->booking_id}}" name="booking_id">

				
				<div class="form-group row">
					<label for="zipcode" class="col-xs-12 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">On Hold</button>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>
@endsection

@section('scripts')

<script type="text/javascript">

 $('#category').on('change', function () {
      var categoryId = $(this).val();
      var categoryName = $('#category option:selected').data('name');

      // Save category name to hidden input
      $('#downreason_name').val(categoryName || '');

      if (categoryId) {
          $.ajax({
              url: "{{ url('admin/get_sub_categories') }}/" + categoryId,
              type: "GET",
              success: function (data) {
                  $('#sub_category').empty();
                  $('#sub_category').append('<option value="">Select Sub Category</option>');
                  
                  $.each(data, function (key, value) {
                      $('#sub_category').append(
                          '<option value="' + value.id + '" data-name="' + value.name + '">' + value.name + '</option>'
                      );
                  });

                  // Reset hidden subcategory name when category changes
                  $('#sub_category_name').val('');
              },
              error: function () {
                  alert('Something went wrong while loading sub categories.');
              }
          });
      } else {
          $('#sub_category').empty();
          $('#sub_category').append('<option value="">Sub Category</option>');
          $('#sub_category_name').val('');
      }
  });

  // When subcategory changes
  $('#sub_category').on('change', function () {
      var subCategoryName = $('#sub_category option:selected').data('name');
      $('#sub_category_name').val(subCategoryName || '');
  });
</script>
@endsection

