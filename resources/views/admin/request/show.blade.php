@extends('admin.layout.base')

@section('title', 'Request details')

@section('content')

<?php
  $data_array = array();
    $i=0;
    foreach ($maptrackdata as $rr) 
    { 
    $i++;
     $lat = str_replace('""', '', $rr->latitude);
     $log = str_replace('""', '', $rr->longitude); 
     $mobile = str_replace('""', '', $rr->ticket_id);
     $data_array[] =Array($mobile,$lat, $log ,$i);
    }

    $firstmap = reset($data_array);

    $lastmap  = end($data_array);
 //echo "<pre>";
//print_r( json_encode($data_array));    exit();
 
  //print_r($request->provider->latitude); exit;
  ?>
<?php
$latitudeFrom = $request->s_latitude;
$longitudeFrom = $request->s_longitude;

$latitudeTo = $request->d_latitude;
$longitudeTo = $request->d_longitude;
//Calculate distance from latitude and longitude
$theta = $longitudeFrom - $longitudeTo;
$dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
$dist = acos($dist);
$dist = rad2deg($dist);
$miles = $dist * 60 * 1.1515;

$distance = ($miles * 1.609344).' km';
//print_r($$request);exit;
if (!function_exists('getDuration')) {
    function getDuration($start, $end) {
        if (!$start || !$end) return '-';

        $startTime = \Carbon\Carbon::parse($start);
        $endTime   = \Carbon\Carbon::parse($end);

        if ($endTime->lessThan($startTime)) {
            return '-';
        }

        $diff = $startTime->diff($endTime);

        $parts = [];
        if ($diff->d > 0) {
            $parts[] = $diff->d . 'd';
        }
        if ($diff->h > 0) {
            $parts[] = $diff->h . 'h';
        }
        if ($diff->i > 0) {
            $parts[] = $diff->i . 'm';
        }
        if ($diff->s > 0) {
            $parts[] = $diff->s . 's';
        }

        return implode(' ', $parts);
    }
}


?>
<div class="content-area py-4 mt-5">
    <div class="container-fluid">
        <!-- Header Section -->

        <div class="ticket-header bg-white rounded-lg shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start flex-nowrap">

                <!-- Left side -->
                <div class="ticket-info flex-grow-1 pe-3">
                    <div class="d-flex align-items-center mb-2 flex-wrap">
                        <h2 class="ticket-id mb-0 me-3">{{ $request->booking_id }}</h2>
                        <a href="{{ route('admin.requests.index') }}" class="btn btn-default pull-right mt-0">
                            <i class="fa fa-angle-left"></i> @lang('admin.back')
                        </a>
                        
                    </div>
                    <div class="ticket-meta text-muted">
                        <i class="ti-location-pin" style="color: #FF0000;"></i>

                        <span>{{ isset($ticket->district) ? $ticket->district : '' }} /
                              {{ isset($ticket->mandal) ? $ticket->mandal : '' }} /
                              {{ isset($request->gpname) ? $request->gpname : '' }}
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

                                <div > <i class="ti-bolt-alt" style="color:#FFDF21;"></i>{{ isset($request->downreason)?$request->downreason:'N/A'}}</div>
                            </div>
                        </div>
                        <div class="header-step completed">
                            <div class="timeline-content">
                                <div class="step-title">DISTANCE</div>
                                <div>{{$distance}}</div>
                            </div>
                        </div>
                        <div class="header-step completed">
                            <div class="timeline-content">
                                <div class="step-title">ASSIGNED TEAM</div>
                                @if($request->provider)
                                <div>
                                <i class="ti-user" style="color: #007bff;"></i>
                                {{ $request->provider->first_name }}</div>
                                @else
                                <div>@lang('admin.request.provider_not_assigned')</div>
                                @endif
                            </div>
                        </div>
                        <div class="header-step completed">
                            <div class="timeline-content">
                                <div class="step-title">CURRENT STATUS</div>
                                    @if($request->status == 'COMPLETED')
                                        <span class="tag tag-success tag-brp"> {{ $request->status }} </span>
                                    @elseif($request->status == 'CANCELLED')
                                        <span class="tag tag-danger tag-brp"> {{ $request->status }} </span>
                                    @elseif($request->status == 'SEARCHING')
                                        <span class="tag tag-warning tag-brp"> {{ $request->status }} </span>
                                    @elseif($request->status == 'SCHEDULED')
                                        <span class="tag tag-primary tag-brp"> {{ $request->status }} </span>
                                    @else 
                                        <span class="tag tag-info tag-brp"> {{ $request->status }} </span>
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
                    <h5 class="mb-0">Status Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline-container">
                        <div class="timeline-step completed">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="step-title">Created</div>
                                <div class="step-time">
                                    @if(isset($ticket))
                                    {{ date('d-m-Y', strtotime($ticket->downdate)) }} {{ $ticket->downtime }}
                                    @else
                                        - 
                                    @endif                                   
                                </div>
                               
                            </div>
                        </div>
                        @if($request->assigned_at)
                        <div class="timeline-connector">
                            <div class="duration">
                                {{ getDuration($ticket->downdate . ' ' . $ticket->downtime, $request->assigned_at) }}

                            </div>
                            <div class="icon">
                                <i class="bi bi-bicycle"></i>
                            </div>
                        </div>
                         @endif      

                        
                        @if($request->assigned_at)
                        <div class="timeline-step completed">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="step-title">Assigned</div>
                                <div class="step-time">
                                       @if($request->assigned_at != "")
                                        {{ date('d-m-Y h:i:s A', strtotime($request->assigned_at)) }} 
                                        @else
                                            - 
                                        @endif
                                </div>
                            </div>
                        </div>
                        @endif
                           @if($request->started_at)
                        <div class="timeline-connector">
                            <div class="duration">
                                {{ getDuration($request->assigned_at, $request->started_at) }}

                            </div>
                            <div class="icon">
                                <i class="bi bi-bicycle"></i>
                            </div>
                        </div>
                         @endif      

                        @if($request->started_at)
                        <div class="timeline-step completed">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="step-title">Started</div>
                                <div class="step-time">
                                    @if($request->started_at != "")
                                    {{ date('d-m-Y h:i:s A', strtotime($request->started_at)) }} 
                                    @else
                                        - 
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                         @if($request->finished_at)
                        <div class="timeline-connector">
                            <div class="duration">
                                {{ getDuration($request->started_at, $request->finished_at) }}

                            </div>
                            <div class="icon">
                                <i class="bi bi-bicycle"></i>
                            </div>
                        </div>
                         @endif     
                        @if($request->finished_at)
                        <div class="timeline-step completed">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="step-title">Completed</div>
                                <div class="step-time">
                                     @if($request->finished_at != "") 
                                    {{ date('d-m-Y h:i:s A', strtotime($request->finished_at)) }}
                                    @else
                                        - 
                                    @endif
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="timeline-step pending">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="step-title">Completed</div>
                                <div class="step-time">Pending</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-white rounded-lg shadow-sm mb-4">
            <!-- Ticket Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">@lang('admin.request.ticket_details')</h5>a_type
                    <small class="text-muted">ID: {{ $request->booking_id }}</small>
                </div>
                <div class="card-body p-1">
                    <div class="row">
                        <!-- Column 1 -->
                        <div class="col-md-3">
                            <div class="detail-group mb-3">
                                <label class="detail-label">Ticket Status</label>
                                <div class="detail-value">
                                    @if($request->status == 'COMPLETED')
                                        <span class="badge badge-success">{{ $request->status }}</span>
                                    @elseif($request->status == 'CANCELLED')
                                        <span class="badge badge-danger">{{ $request->status }}</span>
                                    @elseif($request->status == 'SEARCHING')
                                        <span class="badge badge-warning">{{ $request->status }}</span>
                                    @elseif($request->status == 'SCHEDULED')
                                        <span class="badge badge-primary">{{ $request->status }}</span>
                                    @else 
                                        <span class="badge badge-info">{{ $request->status }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="detail-group mb-3">
                                <label class="detail-label">Distance</label>
                                <div class="detail-value">{{ $distance }}</div>
                            </div>

                            <div class="detail-group mb-3">
                                <label class="detail-label">District</label>
                                <div class="detail-value">{{ $ticket->district }}</div>
                            </div>

                            <div class="detail-group mb-3">
                                <label class="detail-label">Block</label>
                                <div class="detail-value">{{ $ticket->mandal }}</div>
                            </div>
                            <div class="detail-group mb-3">
                                <label class="detail-label">GP Name</label>
                                <div class="detail-value">{{ $request->gpname }}</div>
                            </div>
                             <div class="detail-group mb-3">
                                <label class="detail-label">Joint Enclousre LatLong 1</label>
                                <div class="detail-value">{{ isset($documents->joint_enclosurebefore_latlong ) ?$documents->joint_enclosurebefore_latlong  : ''}}</div>
                            </div>
                             <div class="detail-group mb-3">
                                <label class="detail-label">Joint Enclousre LatLong 2</label>
                                <div class="detail-value">{{isset($documents->joint_enclosureafter_latlong ) ? $documents->joint_enclosureafter_latlong : '' }}</div>
                            </div>
                             <div class="detail-group mb-3">
                                <label class="detail-label">Before Image LatLong</label>
                                <div class="detail-value">{{isset($documents->before_img_latlong ) ? $documents->before_img_latlong : '' }}</div>
                            </div> 
                            <div class="detail-group mb-3">
                                <label class="detail-label">After Image LatLong</label>
                                <div class="detail-value">{{isset($documents->after_img_latlong ) ? $documents->after_img_latlong : '' }}</div>
                            </div>
                              <div class="detail-group mb-3">
                                <label class="detail-label">OTDR Image LatLong</label>
                                <div class="detail-value">{{isset($documents->otdr_img_latlong ) ? $documents->otdr_img_latlong : '' }}</div>
                            </div>
                              @if($request->downreason ==='Fiber')
                              <div class="detail-group mb-3">
                                <label class="detail-label">Owner Ship</label>
                                <div class="detail-value">{{ isset($request->ownership) ? $request->ownership : '' }}</div>
                            </div>
                            @endif
                            
                        
                        </div>
                        

                        <!-- Column 2 -->
                        <div class="col-md-3">
                             <div class="detail-group mb-3">
                                <label class="detail-label">Issue Type</label>
                                <div class="detail-value">{{ isset($request->issue_type) ? $request->issue_type : '' }}</div>
                            </div>
                           
                            <div class="detail-group mb-3">
                                <label class="detail-label">Issue Reason</label>
                                <div class="detail-value">{{ isset($request->downreason) ? $request->downreason : '' }}</div>
                            </div>

                            <div class="detail-group mb-3">
                                <label class="detail-label">Issue Overview</label>
                                <div class="detail-value">{{ isset($request->downreasonindetailed)  ? $request->downreasonindetailed : '' }}</div>
                            </div>
                           @if($request->downreason ==='Fiber')
                            <div class="detail-group mb-3">
                                <label class="detail-label">Construction Type</label>
                                <div class="detail-value">{{ isset($documents->construction_type)  ? $documents->construction_type : '' }}</div>
                            </div>
                           
   
                              <div class="detail-group mb-3">
                                <label class="detail-label">Fiber Type</label>
                                <div class="detail-value">{{ isset($documents->fiber_type)  ? $documents->fiber_type : '' }}</div>
                            </div>
                              <div class="detail-group mb-3">
                                <label class="detail-label">Construction Type - RESTORATION</label>
                                <div class="detail-value">{{ isset($documents->consttype_restoration)  ? $documents->consttype_restoration : '' }}</div>
                            </div>
                           @endif
                            <div class="detail-group mb-3">
                                <label class="detail-label">Assigned Team</label>
                                <div class="detail-value text-primary">
                                    @if($request->provider)
                                        {{ $request->provider->first_name }}
                                    @else
                                        @lang('admin.request.provider_not_assigned')
                                    @endif
                                </div>
                            </div>
                            @if($request->status == 'SCHEDULED')
                                <div class="detail-group mb-3">
                                    <label class="detail-label">Scheduled Time</label>
                                    <div class="detail-value">
                                        {{ $request->schedule_at ? date('d-m-Y h:i:s A', strtotime($request->schedule_at)) : '-' }}
                                    </div>
                                </div>
                            @else
                                <div class="detail-group mb-3">
                                    <label class="detail-label">Ticket Down Time</label>
                                    <div class="detail-value">
                                        {{ isset($ticket) ? date('d-m-Y', strtotime($ticket->downdate)) . ' ' . $ticket->downtime : '-' }}
                                    </div>
                                </div>

                               
                            @endif
                            <div class="detail-group mb-3">
                                <label class="detail-label">Assigned Time</label>
                                <div class="detail-value">
                                    {{ $request->assigned_at ? date('d-m-Y h:i:s A', strtotime($request->assigned_at)) : '-' }}
                                </div>
                            </div>
                            <div class="detail-group mb-3">
                                <label class="detail-label">Estimated Time</label>
                                <div class="detail-value">4 Hours</div>
                            </div>
                        </div>
                        

                        <!-- Column 3 -->
                        <div class="col-md-3">
                      
                            
                      
                            <div class="detail-group mb-3">
                                <label class="detail-label">Started Time</label>
                                <div class="detail-value">
                                    {{ $request->started_at ? date('d-m-Y h:i:s A', strtotime($request->started_at)) : '-' }}
                                </div>
                            </div>
                            <div class="detail-group mb-3">
                                <label class="detail-label">Ticket Started Location</label>
                                <div class="detail-value">{{ $request->started_location ?: '-' }}</div>
                            </div>
                            <div class="detail-group mb-3">
                                <label class="detail-label">Reached Time</label>
                                <div class="detail-value">
                                    {{ $request->reached_at ? date('d-m-Y h:i:s A', strtotime($request->reached_at)) : '-' }}
                                </div>
                            </div>
                            <div class="detail-group mb-3">
                                <label class="detail-label">Reached Location</label>
                                <div class="detail-value">
                                    @if($request->reached_location != "")
                                        {{ $request->reached_location }} 
                                    @else
                                        - 
                                    @endif
                                </div>
                            </div>
                              <div class="detail-group mb-3">
                                <label class="detail-label">Closed Time</label>
                                <div class="detail-value">
                                    {{ $request->finished_at ? date('d-m-Y h:i:s A', strtotime($request->finished_at)) : '-' }}
                                </div>
                            </div>
                                 <?php 
                            if(!empty($request->finished_at) && !empty($ticket->downdate)){
                            if($request->status == 'COMPLETED'){ ?>   
                            <?php 
                            $downdate = $ticket->downdate;
                            $downtime = $ticket->downtime;
                            $downdatetime = date('Y-m-d H:i:s', strtotime("$downdate $downtime "));
                            $seconds = strtotime($request->finished_at) - strtotime($downdatetime);
                            $hours = $seconds/3600;
                            ?>
                                <div class="detail-group mb-3">
                                <label class="detail-label">Ticket Closed Hours :</label>
                                <div class="detail-value">
                                    {{ $hours }} hrs
                                </div>
                            </div>
                            
                                <?php } }?>
                                      <?php 
                            if(!empty($request->started_latitude)){ ?>
                            <?php 
                            $latitudeFrom = $request->started_latitude;
                            $longitudeFrom = $request->started_longitude;

                            $latitudeTo = $request->d_latitude;
                            $longitudeTo = $request->d_longitude;
                            //Calculate distance from latitude and longitude
                            $theta = $longitudeFrom - $longitudeTo;
                            $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
                            $dist = acos($dist);
                            $dist = rad2deg($dist);
                            $miles = $dist * 60 * 1.1515;

                            $traveldistance = ($miles * 1.609344).' km';
                            ?>
                                <div class="detail-group mb-3">
                                <label class="detail-label">Distance Travelled </label>
                                <div class="detail-value">
                                    {{ $traveldistance }}
                                </div>
                            </div>
                                
                                <?php } ?>

                                <?php 
                                if($request->status == "COMPLETED"){ 
                                    if(isset($documents) &&  $documents != ''){
                                        
                                if($documents->materials != ''){ ?>
                                <div class="detail-group mb-3">
                                <label class="detail-label"> Used Materials</label>
                                <div class="detail-value">
                                    {{ $documents->materials}}
                                </div>
                            </div>
                                
                            <?php } ?>
                             @if(auth()->user()->role == 'admin')
                             <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#uploadImageModal">
                            <i class="fa fa-upload"></i> Upload Images
                            </button>
                            @endif
                        </div>

                        <!-- Column 4 -->
                        <div class="col-md-3">
                            
                             
                       
                             <?php if($documents->before_image != ''){  
                                        ?>
                            <div class="detail-group mb-3">
                                <label class="detail-label">Issue Before Images</label>
                                <div class="detail-value" id="beforeuploadimages">
                                    <?php if ((is_array(json_decode($documents->before_image, true))) == 1) { ?>   
                                    <?php 
                                        $beforedata = json_decode($documents->before_image);
                                        foreach ($beforedata as $beforeimage) {
                                
                                            ?>
                                        <a data-magnify="gallery" data-group="a">  
                                    <!--<img src="data:image/png;base64, {{$beforeimage }}" alt="Red dot" style="width:100px;height:70px;"/></a>-->
                                        <img src="{{asset('/uploads/SubmitFiles/'.$beforeimage)}}" alt="Red dot" style="width:100px;height:70px;"/></a> 
                                    <?php } ?>
                                <?php } else { ?>

                                <?php 
                                    $beforedata = explode(',',$documents->before_image);
                                    foreach ($beforedata as $beforeimage) {
                                
                                    ?>
                                <a data-magnify="gallery" data-group="a">  
                                <img src="data:image/png;base64, {{$beforeimage }}" alt="Red dot" style="width:100px;height:70px;"/></a> 
                                <?php } }?>
                                </div>
                            </div>
                                
                                <?php }?>

                                <?php 
                                if($documents->after_image != ''){ ?>
                                <div class="detail-group mb-3">
                                <label class="detail-label">Issue After Images</label>
                                <div class="detail-value"  id="afteruploadimages">
                                    <?php if ((is_array(json_decode($documents->after_image, true))) == 1) { ?>   
                                    <?php 
                                        $afterdata = json_decode($documents->after_image);
                                        foreach ($afterdata as $afterimage) {
                                
                                            ?>
                                        <a data-magnify="gallery" data-group="a">  
                                    <!--<img src="data:image/png;base64, {{$afterimage }}" alt="Red dot" style="width:100px;height:70px;"/></a>-->
                                        <img src="{{asset('/uploads/SubmitFiles/'.$afterimage)}}" alt="Red dot" style="width:100px;height:70px;"/></a> 
                                    <?php } ?>
                                <?php } else { ?>

                                <?php 
                                    $afterdata = explode(',',$documents->after_image);
                                    foreach ($afterdata as $afterimage) {
                                
                                    ?>
                                <a data-magnify="gallery" data-group="a">  
                                <img src="data:image/png;base64, {{$afterimage }}" alt="Red dot" style="width:100px;height:70px;"/></a> 
                                <?php } }?>
                                </div>
                                <?php } } }?>
                                

                              @php
                                    // Helper function to get images array from a field
                                    function getImages($documents, $field) {
                                        if(empty($documents) || !isset($documents->$field) || $documents->$field == '') {
                                            return [];
                                        }

                                        $data = json_decode($documents->$field, true);
                                        if(is_array($data)) {
                                            return $data;
                                        }

                                        // Fallback for comma-separated values
                                        return explode(',', $documents->$field);
                                    }
                                @endphp

                                {{-- Joint Enclouser Before Images --}}
                                @php $beforeImages = getImages($documents ?? null, 'joint_enclouser_beforeimg'); @endphp
                                @if(count($beforeImages))
                                    <div class="detail-group mb-3">
                                        <label class="detail-label">Joint Enclouser Before Images</label>
                                        <div class="detail-value" id="Jointbeforeuploadimages">
                                            @foreach($beforeImages as $image)
                                                <a data-magnify="gallery" data-group="before">
                                                    <img src="{{ asset('/uploads/SubmitFiles/'.$image) }}" style="width:100px;height:70px;" alt="Image"/>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Joint Enclouser After Images --}}
                                @php $afterImages = getImages($documents ?? null, 'joint_enclouser_afterimg'); @endphp
                                @if(count($afterImages))
                                    <div class="detail-group mb-3">
                                        <label class="detail-label">Joint Enclouser After Images</label>
                                        <div class="detail-value" id="JointAfteruploadimages">
                                            @foreach($afterImages as $image)
                                                <a data-magnify="gallery" data-group="after">
                                                    <img src="{{ asset('/uploads/SubmitFiles/'.$image) }}" style="width:100px;height:70px;" alt="Image"/>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- OTDR Images --}}
                                @php $otdrImages = getImages($documents ?? null, 'otdr_img'); @endphp
                                @if(count($otdrImages))
                                    <div class="detail-group mb-3">
                                        <label class="detail-label">OTDR Images</label>
                                        <div class="detail-value" id="otdrimg">
                                            @foreach($otdrImages as $image)
                                                <a data-magnify="gallery" data-group="otdr">
                                                    <img src="{{ asset('/uploads/SubmitFiles/'.$image) }}" style="width:100px;height:70px;" alt="Image"/>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if(!empty($documents->video))
                                    <div class="detail-group mb-3">
                                        <label class="detail-label">Video</label>
                                        <div class="detail-value">
                                            <video width="200" height="200" controls>
                                                <source src="{{ asset('uploads/SubmitFiles/videos/'.$documents->video) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    </div>
                                @endif


                                                        



                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-white rounded-lg shadow-sm mb-4">
          <!-- Location & Route -->
                <div class="card mb-4">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Location & Route</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="map"></div>
                    </div>
                </div>
        </div>
    </div>
</div>


<!-- Upload Image Modal -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Upload Images</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="uploadImageForm" enctype="multipart/form-data" method="POST" action="{{ route('admin.upload-otdr-images') }}">
                {{csrf_field()}}
                <input type="hidden" name="request_id" value="{{ $request->booking_id}}">

                <div class="modal-body">

                    <label class="font-weight-bold">Select Images</label>
                    <input type="file" name="images[]" id="imagesInput" class="form-control" multiple accept="image/*">

                    <div class="row mt-3" id="previewContainer"></div>

                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
               
            </form>

        </div>
    </div>
</div>


<!-- Image Modal -->
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">              
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <img src="" class="imagepreview" style="width: 100%;" >
      </div>
    </div>
  </div>
</div>

</div>

@endsection
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
@section('styles')
<style>
/* Modern Card Styles */
.timeline-connector {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.timeline-connector::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    height: 2px;
    width: 100%;
    background: #d1d5db;
    z-index: 0;
}

.timeline-connector .duration {
    font-size: 0.85rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.25rem;
    background: #f9fafb;
    padding: 2px 6px;
    border-radius: 4px;
    z-index: 1;
}

.timeline-connector .icon {
    font-size: 1.4rem;
    color: #3b82f6;
    background: #fff;
    padding: 4px;
    border-radius: 50%;
    z-index: 1;
}

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

/* Modal Background */
.modal.fade .modal-dialog {
    transform: translateY(-30px);
    transition: all 0.3s ease-in-out;
}

.modal.show .modal-dialog {
    transform: translateY(0);
}

/* Modal Content */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0px 10px 25px rgba(0,0,0,0.15);
}

/* Header Styling */
.modal-header {
    background: #f8f9fc;
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 1.25rem;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.modal-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #374151;
}

.modal-header .close span {
    font-size: 1.8rem;
    color: #6b7280;
}

.modal-header .close:hover span {
    color: #ef4444;
}

/* Modal Body */
.modal-body {
    padding: 1.4rem;
}

/* File Input */
#imagesInput {
    border: 1px solid #d1d5db;
    padding: 8px;
    border-radius: 6px;
    cursor: pointer;
}

/* Preview Container */
#previewContainer img {
    border-radius: 10px;
    border: 2px solid #e5e7eb;
    transition: 0.2s;
}

#previewContainer img:hover {
    transform: scale(1.05);
    border-color: #3b82f6;
}

/* Modal Footer */
.modal-footer {
    border-top: 1px solid #e5e7eb;
    padding: 0.75rem 1.25rem;
}

/* Button Styling */
.modal-footer .btn {
    font-weight: 500;
    padding: 0.55rem 1.1rem;
    border-radius: 8px;
}

.btn-success {
    background-color: #10b981;
    border-color: #10b981;
}

.btn-success:hover {
    background-color: #059669;
}

.btn-secondary {
    background-color: #6b7280;
    color: #fff;
}

.btn-secondary:hover {
    background-color: #4b5563;
}

</style>
@endsection

@section('scripts')

@if($request->status != 'COMPLETED')
<script type="text/javascript">
    var map;
    var zoomLevel = 11;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            styles: [
                {
                    featureType: 'all',
                    elementType: 'geometry.fill',
                    stylers: [{ color: '#f8f9fc' }]
                },
                {
                    featureType: 'water',
                    elementType: 'geometry',
                    stylers: [{ color: '#e3f2fd' }]
                }
            ]
        });

        var marker = new google.maps.Marker({
            map: map,
            icon: '/asset/img/marker-start.png',
            anchorPoint: new google.maps.Point(0, -29)
        });

        var markerSecond = new google.maps.Marker({
            map: map,
            icon: '/asset/img/marker-end.png',
            anchorPoint: new google.maps.Point(0, -29)
        });

        var bounds = new google.maps.LatLngBounds();
        @if(!empty($request->started_latitude))
        source = new google.maps.LatLng({{ $request->started_latitude }}, {{ $request->started_longitude }});
        @else
        source = new google.maps.LatLng({{ $request->s_latitude }}, {{ $request->s_longitude }});
        @endif
        destination = new google.maps.LatLng({{ $request->d_latitude }}, {{ $request->d_longitude }});

        marker.setPosition(source);
        markerSecond.setPosition(destination);

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer({
            suppressMarkers: true, 
            preserveViewport: true,
            polylineOptions: {
                strokeColor: '#3b82f6',
                strokeWeight: 4
            }
        });
        directionsDisplay.setMap(map);

        directionsService.route({
            origin: source,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING
        }, function(result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);
                marker.setPosition(result.routes[0].legs[0].start_location);
                markerSecond.setPosition(result.routes[0].legs[0].end_location);
            }
        });

        @if($request->provider && $request->status != 'COMPLETED')
        var markerProvider = new google.maps.Marker({
            map: map,
            icon: "/asset/img/marker-car.png",
            anchorPoint: new google.maps.Point(0, -29)
        });

        provider = new google.maps.LatLng({{ $request->provider->latitude }}, {{ $request->provider->longitude }});
        markerProvider.setVisible(true);
        markerProvider.setPosition(provider);
        bounds.extend(markerProvider.getPosition());
        @endif

        bounds.extend(marker.getPosition());
        bounds.extend(markerSecond.getPosition());
        map.fitBounds(bounds);
    }
    if (typeof google !== 'undefined') {
        initMap();
    } else {
        window.addEventListener('load', initMap);
    }

</script>
@else
<script type="text/javascript">
    var map;
    var zoomLevel = 11;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            styles: [
                {
                    featureType: 'all',
                    elementType: 'geometry.fill',
                    stylers: [{ color: '#f8f9fc' }]
                },
                {
                    featureType: 'water',
                    elementType: 'geometry',
                    stylers: [{ color: '#e3f2fd' }]
                }
            ]
        });

        var marker = new google.maps.Marker({
            map: map,
            icon: '/asset/img/marker-start.png',
            anchorPoint: new google.maps.Point(0, -29)
        });

        var markerSecond = new google.maps.Marker({
            map: map,
            icon: '/asset/img/marker-end.png',
            anchorPoint: new google.maps.Point(0, -29)
        });

        var bounds = new google.maps.LatLngBounds();

        @if(!empty($firstmap) && !empty($lastmap))
        source = new google.maps.LatLng({{ $firstmap[1] }}, {{ $firstmap[2] }});
        destination = new google.maps.LatLng({{ $lastmap[1] }}, {{ $lastmap[2] }});
        @else
        source = new google.maps.LatLng({{ $request->s_latitude }}, {{ $request->s_longitude }});
        destination = new google.maps.LatLng({{ $request->d_latitude }}, {{ $request->d_longitude }});
        @endif
        
        marker.setPosition(source);
        markerSecond.setPosition(destination);

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer({
            suppressMarkers: true, 
            preserveViewport: true,
            polylineOptions: {
                strokeColor: '#10b981',
                strokeWeight: 4
            }
        });
        directionsDisplay.setMap(map);

        directionsService.route({
            origin: source,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING
        }, function(result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);
                marker.setPosition(result.routes[0].legs[0].start_location);
                markerSecond.setPosition(result.routes[0].legs[0].end_location);
            }
        });

        bounds.extend(marker.getPosition());
        bounds.extend(markerSecond.getPosition());
        map.fitBounds(bounds);
    }

    google.maps.event.addDomListener(window, "load", initMap);
</script>
@endif

<script type="text/javascript">
// Image modal functionality
$(document).ready(function() {
    $('.image-item img').on('click', function() {
        $('.imagepreview').attr('src', $(this).attr('src'));
        $('#imagemodal').modal('show');
    });

    // Initialize zoom functionality
    if (typeof ezoom !== 'undefined') {
        ezoom.onInit($('#beforeuploadimages img'), {
            hideControlBtn: false,
            onClose: function (result) {
            },
            onRotate: function (result) {
            },
        });

        ezoom.onInit($('#afteruploadimages img'), {
            hideControlBtn: false,
            onClose: function (result) {
            },
            onRotate: function (result) {
            },
        });
          ezoom.onInit($('#Jointbeforeuploadimages img'), {
            hideControlBtn: false,
            onClose: function (result) {
            },
            onRotate: function (result) {
            },
        });
          ezoom.onInit($('#JointAfteruploadimages img'), {
            hideControlBtn: false,
            onClose: function (result) {
            },
            onRotate: function (result) {
            },
        });
             ezoom.onInit($('#otdrimg img'), {
            hideControlBtn: false,
            onClose: function (result) {
            },
            onRotate: function (result) {
            },
        });
       
    }
});
</script>

<script>
document.getElementById('imagesInput').addEventListener('change', function (event) {

    let preview = document.getElementById('previewContainer');
    preview.innerHTML = ""; 

    [...event.target.files].forEach(file => {
        let reader = new FileReader();
        reader.onload = function (e) {

            preview.innerHTML += `
                <div class="col-md-3 mb-3">
                    <img src="${e.target.result}" class="img-thumbnail" style="height:120px; object-fit:cover;">
                </div>`;
        };
        reader.readAsDataURL(file);
    });

});
</script>

@endsection