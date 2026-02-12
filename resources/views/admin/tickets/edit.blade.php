@extends('admin.layout.base')

@section('title', 'Request details ')

@section('content')

<div class="content-area py-4 mt-5">
    <div class="container-fluid">
        <!-- Header Section -->

        <div class="ticket-header bg-white rounded-lg shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start flex-nowrap">

                <!-- Left side -->
                <div class="ticket-info flex-grow-1 pe-3">
                    <div class="d-flex align-items-center mb-2 flex-wrap">
                        <h2 class="ticket-id mb-0 me-3">{{ $userrequest->booking_id }}</h2>
                        <a href="{{ route('admin.requests.index') }}" class="btn btn-default pull-right mt-0">
                            <i class="fa fa-angle-left"></i> @lang('admin.back')
                        </a>
                        
                    </div>
                    <div class="ticket-meta text-muted">
                        <i class="ti-location-pin" style="color: #FF0000;"></i>

                        <span>{{ isset($ticket->district) ? $ticket->district : '' }} /
                              {{ isset($ticket->mandal) ? $ticket->mandal : '' }} /
                              {{ isset($ticket->gpname) ? $ticket->gpname : '' }}
                            </span>
                        &nbsp;
                        <i class="ti-time" style="color:#FF0000;"></i>
                        <span>
                        {{ $ticket ? date('M d, Y h:i A', strtotime($ticket->downdate.' '.$ticket->downtime)) : date('M d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="heade-container">
                        <div class="header-step completed">
                            <div class="timeline-content">
                                <div class="step-title">CATEGORY</div>

                                <div >
                                     <i class="bi bi-record-fill text-danger"></i>{{ isset($userrequest->downreason)?$userrequest->downreason:'N/A'}}<br>
                                       <small>{{ isset($userrequest->downreasonindetailed)?$userrequest->downreasonindetailed:'N/A'}}</small>

                                     </div>
                            </div>
                        </div>
                        <div class="header-step completed">
                            <div class="timeline-content">
                                <div class="step-title">ASSIGNED TEAM</div>
                                @if($userrequest->provider)
                                <div>
                                <i class="ti-user" style="color: #007bff;"></i>
                                {{ $userrequest->provider->first_name }}</div>
                                @else
                                <div>@lang('admin.request.provider_not_assigned')</div>
                                @endif
                            </div>
                        </div>
                        <div class="header-step completed">
                            <div class="timeline-content">
                                <div class="step-title">CURRENT STATUS</div>
                                    @if($userrequest->status == 'COMPLETED')
                                        <span class="tag tag-success tag-brp"> {{ $userrequest->status }} </span>
                                    @elseif($userrequest->status == 'CANCELLED')
                                        <span class="tag tag-danger tag-brp"> {{ $userrequest->status }} </span>
                                    @elseif($userrequest->status == 'SEARCHING')
                                        <span class="tag tag-warning tag-brp"> {{ $userrequest->status }} </span>
                                    @elseif($userrequest->status == 'SCHEDULED')
                                        <span class="tag tag-primary tag-brp"> {{ $userrequest->status }} </span>
                                    @else 
                                        <span class="tag tag-info tag-brp"> {{ $userrequest->status }} </span>
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<div class="p-4  bg-white rounded-lg shadow-sm mb-4">
            <!-- Status Timeline -->
            <div class="card bg-white mb-4">
                <div class="card-header  border-bottom">
                    <h5 class="mb-0">Issue Type</h5>
                </div>
              <div class="card-body p-1">
                  <form class="form-horizontal" action="{{route('admin.dispatcher.sendassignrequest')}}" method="POST" enctype="multipart/form-data" role="form">
                    {!! csrf_field() !!}
                     <input type ="hidden" value="{{$userrequest->id}}" name="request_id">
                     <input type ="hidden" value="{{$userrequest->booking_id}}" name="booking_id">
                    <div class="row filter-pill">
                        <!-- Column 1 -->
                        <div class="col-md-4 filter-pill">
                          <label for="category" class="col-form-label">Category</label>
                           <select class="form-control select-box" name="downreason" id="category" required>
				<option value="">Category</option>
				<?php foreach($service_types as $types) { ?>
					<option value="{{ $types->id }}" data-name="{{ $types->name }}">{{$types->name}}</option>
		                <?php } ?>
			  </select>
                           <input type="hidden" name="downreason_name" id="downreason_name">
                        </div>
                        <div class="col-md-4 filter-pill">
                          <label for="sub_category" class="col-form-label">Sub Category</label>
                           <select class="form-control select-box" name="sub_category" id="sub_category" required>
				<option value="">Sub Category</option>
			  </select>
                         <input type="hidden" name="sub_category_name" id="sub_category_name">
                        </div>
                        <div class="col-md-4 filter-pill">
                          <label for="last_name" class="col-form-label">Details</label>
                           <input type="text" class="form-control select-box" name="downreasonindetailed" value="{{ isset($userrequest->downreasonindetailed)?$userrequest->downreasonindetailed:'N/A'}}" required>
                        </div>
                        <div class="col-md-4 filter-pill">
                          <label for="district" class="col-form-label">District</label>
                            <select class="form-control select-box" name="district_id" required id="district_id">
							<option value="">Select District</option>
							<?php foreach($districts as $district) { ?>
							<option value="{{$district->id}}">{{$district->name}}</option>
						    <?php } ?>
			    </select>

                        </div>
                        <div class="col-md-4 filter-pill">
                          <label for="provider" class="col-form-label">Assign Member</label>
                             <select class="form-control select-box" name="provider_id" required id ="provider_id">
							<option value="">Select Member</option>
							<?php foreach($providers as $provider) { ?>
							<option value="{{$provider->id}}">{{$provider->first_name}}{{$provider->last_name}}</option>
						    <?php } ?>
			    </select>


                        </div>
                 
                            <div class="col-md-2 filter-pill">
                          <label for="provider" class="col-form-label">&nbsp;</label>
                             <button type="submit" class="btn btn-primary btn-cstm form-control">Assign</button>
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
            url: 'https://fleet.terasoftware.com/public/westbengal/public/admin/getSearchproviderlist/'+ nid,
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

<script>
  // When category changes
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

@section('styles')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* Modern Card Styles */
.card {
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border-radius: 0.5rem;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    padding: 1rem 1.25rem;
}
.status-header {
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    margin-bottom:10px;
   
}

 /* Filter Pills */



/* Ticket Header Styles */
.ticket-header {
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    margin-bottom:10px;
    padding:20px;
}
.ticket-header .ticket-info {
  flex: 1 1 auto;  /* take remaining space */
  min-width: 0; /* prevent pushing SLA block down */
}
.ticket-header .sla-timer {
  flex: 0 0 auto;   /* fixed width, never wrap to new row */
  white-space: nowrap;
  margin-left: 1rem;
}
.ticket-id {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
}

.ticket-meta {
    font-size: 0.875rem;
    margin-top:5px;
}

/* SLA Timer Styles */
.sla-timer {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border-radius: 0.5rem;
    padding: 1rem;
    min-width: 120px;
}

.time-display {
    font-size: 1.5rem;
    font-weight: bold;
    color: #dc2626;
}

.sla-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #dc2626;
    margin-top: 0.25rem;
}

.due-time {
    font-size: 0.7rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

/* Detail Groups */
.detail-group {
    margin-bottom: 1rem;
}

.detail-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    margin-bottom: 0.25rem;
    display: block;
}

.detail-value {
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
}

.heade-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin: 1rem 0;
    padding: 0 1rem;
}



/* Timeline Styles */
.timeline-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    margin: 1rem 0;
    padding: 0 1rem;
}

/* .timeline-container::before {
   content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e5e7eb;
    z-index: 0;
} */

.timeline-step {
    position: relative;
    text-align: center;
    flex: 1;
    z-index: 1;
}

.header-step {
    position: relative;
    flex: 1;
    z-index: 1;
}
.timeline-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin: 0 auto 0.5rem;
    position: relative;
}

.timeline-step.completed .timeline-dot {
    background: #3b82f6;
    border: 3px solid #dbeafe;
}

.timeline-step.pending .timeline-dot {
    background: #e5e7eb;
    border: 3px solid #f3f4f6;
}

.step-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
}

.step-time {
    font-size: 0.75rem;
    color: #3b82f6;
    margin-bottom: 0.125rem;
}

.step-user {
    font-size: 0.75rem;
    color: #6b7280;
}

/* Map Styles */
#map {
    height: 350px;
    width: 100%;
}

.map-info {
    background-color: #f8f9fc !important;
}



/* Badge Styles */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-weight: 500;
}

.badge-warning {
    background-color: #f59e0b;
    color: white;
}

.badge-info {
    background-color: #3b82f6;
    color: white;
}

.badge-success {
    background-color: #10b981;
    color: white;
}

.badge-danger {
    background-color: #ef4444;
    color: white;
}

.badge-primary {
    background-color: #6366f1;
    color: white;
}

/* Button Styles */
.btn {
    border-radius: 0.375rem;
    font-weight: 500;
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
    transition: all 0.15s ease-in-out;
}

.btn-block {
    width: 100%;
}

.btn-success {
    background-color: #10b981;
    border-color: #10b981;
    color: white;
}

.btn-success:hover {
    background-color: #059669;
    border-color: #059669;
}

.btn-warning {
    background-color: #f59e0b;
    border-color: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background-color: #d97706;
    border-color: #d97706;
}

.btn-primary {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.btn-primary:hover {
    background-color: #2563eb;
    border-color: #2563eb;
}

.btn-danger {
    background-color: #ef4444;
    border-color: #ef4444;
}

.btn-danger:hover {
    background-color: #dc2626;
    border-color: #dc2626;
}

/* Image Gallery */
.image-gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.image-item {
    width: 80px;
    height: 80px;
    overflow: hidden;
    border-radius: 0.375rem;
}

.image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.image-item img:hover {
    transform: scale(1.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .timeline-container {
        flex-direction: column;
        gap: 1rem;
    }
    
    .timeline-container::before {
        display: none;
    }
    
    .ticket-header .d-flex {
    display: flex;
    justify-content: space-between;
    align-items: center; /* vertically aligns SLA timer with ticket info */
    flex-wrap: wrap;
    }
    
    .sla-timer {
          background: #fee2e2;
    border: 1px solid #fecaca;
    border-radius: 0.5rem;
    padding: 1rem;
    width: 160px;   /* fixes alignment */
    text-align: center;
    }
}

/* Content Area */
.content-area {
    /* background-color: #f8f9fc; */
    min-height: 100vh;
    margin-top:10px;
}

/* Utility Classes */
.fw-4 {
    font-weight: 400;
}

.fw-5 {
    font-weight: 500;
}

.fw-6 {
    font-weight: 600;
}

.rounded-lg {
    border-radius: 0.5rem;
}

.shadow-sm {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
</style>
@endsection

