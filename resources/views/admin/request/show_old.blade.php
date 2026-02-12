@extends('admin.layout.base')

@section('title', 'Request details ')

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
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block">
                 <a href="{{ route('admin.requests.index') }}" class="btn btn-default pull-right">
                <i class="fa fa-angle-left"></i> @lang('admin.back')
            </a>
             <div class="row cs-card">
                <div class="col-md-6 tkt-dtls">
                    <dl class="row">
                        <dd class="col-sm-12 bg-white ">
                            <h4>@lang('admin.request.ticket_details')</h4>
                            <p class="fw-4">{{ $request->booking_id }}</p>
                        </dd>
                        <!-- <dd class="col-sm-8">{{ $request->booking_id }}</dd> -->

                        <dt class="col-sm-5">@lang('admin.request.ticket_status') : </dt>
                        <dd class="col-sm-7 fw-4">
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
                        </dd>

                        <dt class="col-sm-5">Distance :</dt>
                        <dd class="col-sm-7 fw-4">{{ $distance}}</dd>

                        <dt class="col-sm-5">District:</dt>
                        <dd class="col-sm-7 fw-4">{{ $ticket->district}}</dd>

                        <dt class="col-sm-5">Block :</dt>
                        <dd class="col-sm-7 fw-4">{{ $ticket->mandal}}</dd>

                        <dt class="col-sm-5">GP Name :</dt>
                        <dd class="col-sm-7 fw-4">{{ $request->gpname }}</dd>

                        
                        <dt class="col-sm-5">Issue Type :</dt>
                        <dd class="col-sm-7 fw-4">{{ isset($request->downreason)?$request->downreason:'N/A'}}</dd>

                        <dt class="col-sm-5">Issue Overview :</dt>
                        <dd class="col-sm-7 fw-4">{{ isset($request->downreasonindetailed)?$request->downreasonindetailed:'N/A'}}</dd>
                        
                        <dt class="col-sm-5">Estimated Time :</dt>
                        <dd class="col-sm-7 fw-4">4 Hours</dd>

                        <dt class="col-sm-5"> Name :</dt>
                        @if($request->provider)
                        <dd class="col-sm-7 fw-4">{{ $request->provider->first_name }}</dd>
                        @else
                        <dd class="col-sm-7 fw-4">@lang('admin.request.provider_not_assigned')</dd>
                        @endif

                        <!--<dt class="col-sm-5">@lang('admin.request.total_distance') :</dt>
                        <dd class="col-sm-7">{{ $request->distance ? $request->distance : '-' }}{{$request->unit}}</dd>-->

                        @if($request->status == 'SCHEDULED')
                        <dt class="col-sm-5">@lang('admin.request.ticket_scheduled_time') :</dt>
                        <dd class="col-sm-7 fw-4">
                            @if($request->schedule_at != "")
                                {{ date('d-m-Y h:i:s A', strtotime($request->schedule_at)) }} 
                            @else
                                - 
                            @endif
                        </dd>
                        @else

                        <dt class="col-sm-5">Ticket Down Time :</dt>
                        <dd class="col-sm-7 fw-4">
                            @if(isset($ticket))
                                {{ date('d-m-Y', strtotime($ticket->downdate)) }} {{ $ticket->downtime }}
                            @else
                                - 
                            @endif
                         </dd>

                        <dt class="col-sm-5">Ticket Assigned Time :</dt>
                        <dd class="col-sm-7 fw-4">
                            @if($request->assigned_at != "")
                                {{ date('d-m-Y h:i:s A', strtotime($request->assigned_at)) }} 
                            @else
                                - 
                            @endif
                         </dd>

                        <dt class="col-sm-5">Ticket Started Time :</dt>
                        <dd class="col-sm-7 fw-4">
                            @if($request->started_at != "")
                                {{ date('d-m-Y h:i:s A', strtotime($request->started_at)) }} 
                            @else
                                - 
                            @endif
                         </dd>

                         <dt class="col-sm-5">Ticket Started Location :</dt>
                        <dd class="col-sm-7 fw-4">
                            @if($request->started_location != "")
                                {{ $request->started_location }} 
                            @else
                                - 
                            @endif
                         </dd>

                         <dt class="col-sm-5">Ticket Reached Time :</dt>
                        <dd class="col-sm-7 fw-4">
                            @if($request->reached_at != "")
                                {{ date('d-m-Y h:i:s A', strtotime($request->reached_at)) }} 
                            @else
                                - 
                            @endif
                         </dd>

                         <dt class="col-sm-5">Ticket Reached Location :</dt>
                        <dd class="col-sm-7 fw-4">
                            @if($request->reached_location != "")
                                {{ $request->reached_location }} 
                            @else
                                - 
                            @endif
                         </dd>

                        <dt class="col-sm-5">Ticket Closed Time :</dt>
                        <dd class="col-sm-7 fw-4">
                            @if($request->finished_at != "") 
                                {{ date('d-m-Y h:i:s A', strtotime($request->finished_at)) }}
                            @else
                                - 
                            @endif
                        </dd>
                        @endif

                        <!--<dt class="col-sm-4">@lang('admin.request.pickup_address') :</dt>
                        <dd class="col-sm-8">{{ $request->s_address ? $request->s_address : '-' }}</dd>

                        <dt class="col-sm-4">@lang('admin.request.drop_address') :</dt>
                        <dd class="col-sm-8">{{ $request->d_address ? $request->d_address : '-' }}</dd>-->

                        @if($request->payment)
                        <!--<dt class="col-sm-4">@lang('admin.request.base_price') :</dt>
                        <dd class="col-sm-8">{{ currency($request->payment->fixed) }}</dd>
                        @if($request->service_type->calculator=='MIN')
                            <dt class="col-sm-4">@lang('admin.request.minutes_price') :</dt>
                            <dd class="col-sm-8">{{ currency($request->payment->minute) }}</dd>
                        @endif
                        @if($request->service_type->calculator=='HOUR')
                            <dt class="col-sm-4">@lang('admin.request.hours_price') :</dt>
                            <dd class="col-sm-8">{{ currency($request->payment->hour) }}</dd>
                        @endif
                        @if($request->service_type->calculator=='DISTANCE')
                            <dt class="col-sm-4">@lang('admin.request.distance_price') :</dt>
                            <dd class="col-sm-8">{{ currency($request->payment->distance) }}</dd>
                        @endif
                        @if($request->service_type->calculator=='DISTANCEMIN')
                            <dt class="col-sm-4">@lang('admin.request.minutes_price') :</dt>
                            <dd class="col-sm-8">{{ currency($request->payment->minute) }}</dd>
                            <dt class="col-sm-4">@lang('admin.request.distance_price') :</dt>
                            <dd class="col-sm-8">{{ currency($request->payment->distance) }}</dd>
                        @endif
                        @if($request->service_type->calculator=='DISTANCEHOUR')
                            <dt class="col-sm-4">@lang('admin.request.hours_price') :</dt>
                            <dd class="col-sm-8">{{ currency($request->payment->hour) }}</dd>
                            <dt class="col-sm-4">@lang('admin.request.distance_price') :</dt>
                            <dd class="col-sm-8">{{ currency($request->payment->distance) }}</dd>
                        @endif                           
                        <dt class="col-sm-4">@lang('admin.request.commission') :</dt>
                        <dd class="col-sm-8">{{ currency($request->payment->commision) }}</dd>

                        <dt class="col-sm-4">@lang('admin.request.fleet_commission') :</dt>
                        <dd class="col-sm-8">{{ currency($request->payment->fleet) }}</dd>

                        <dt class="col-sm-4">@lang('admin.request.discount_price') :</dt>
                        <dd class="col-sm-8">{{ currency($request->payment->discount) }}</dd>

                        <dt class="col-sm-4">@lang('admin.request.tax_price') :</dt>
                        <dd class="col-sm-8">{{ currency($request->payment->tax) }}</dd>

                        <dt class="col-sm-4">@lang('admin.request.surge_price') :</dt>
                        <dd class="col-sm-8">{{ currency($request->payment->surge) }}</dd>

                        <dt class="col-sm-4">@lang('admin.request.tips') :</dt>
                        <dd class="col-sm-8">{{ currency($request->payment->tips) }}</dd>

                        <dt class="col-sm-4">@lang('admin.request.total_amount') :</dt>
                        <dd class="col-sm-8">{{ currency($request->payment->total+$request->payment->tips) }}</dd>

                        <dt class="col-sm-4">@lang('admin.request.wallet_deduction') :</dt>
                        <dd class="col-sm-8">{{ currency($request->payment->wallet) }}</dd>-->

                        <!-- <dt class="col-sm-4">@lang('admin.request.paid_amount') :</dt>
                        <dd class="col-sm-8">{{ currency($request->payment->payable) }}</dd> -->

                        <!--<dt class="col-sm-4">@lang('admin.request.payment_mode') :</dt>
                        <dd class="col-sm-8">{{ $request->payment->payment_mode }}</dd>
                        @if($request->payment->payment_mode=='CASH')
                            <dt class="col-sm-4">@lang('admin.request.cash_amount') :</dt>
                            <dd class="col-sm-8">{{ currency($request->payment->cash) }}</dd>
                        @else
                            <dt class="col-sm-4">@lang('admin.request.card_amount') :</dt>
                            <dd class="col-sm-8">{{ currency($request->payment->card) }}</dd>
                        @endif-->
                         <!--<dt class="col-sm-4">@lang('admin.request.provider_earnings'):</dt>
                        <dd class="col-sm-8">{{ currency($request->payment->provider_pay) }}</dd> -->

                       <!--  <dt class="col-sm-4">Provider Admin Commission :</dt>
                        <dd class="col-sm-8">{{ currency($request->payment->provider_commission) }}</dd> -->
                        @endif
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
                         <dt class="col-sm-5">Ticket Closed Hours : </dt>
                        <dd class="col-sm-7 fw-4">
                            {{ $hours }} hrs
                        </dd>
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
                         <dt class="col-sm-5">Distance Travelled : </dt>
                        <dd class="col-sm-7 fw-4">
                            {{ $traveldistance }}
                        </dd>
                        <?php } ?>


                        <?php 
                        if($request->status == "COMPLETED"){ 
                             if(isset($documents) &&  $documents != ''){
                                 
                        if($documents->materials != ''){ ?>
                        
                         <dt class="col-sm-5">Used Materials</dt>
                         <dd class="col-sm-7 fw-4">
                           <?php echo $documents->materials; ?>
                         </dd>
                        
                        <?php } ?>
                                 
                         
                         <?php if($documents->before_image != ''){  
                                ?>
                         <dt class="col-sm-5">Issue Before Images</dt>
                         <dd class="col-sm-7" id="beforeuploadimages">
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

                        </dd>
                        <?php }?>

                        <?php 
                         if($documents->after_image != ''){ ?>
                         <dt class="col-sm-5">Issue After Images</dt>
                         <dd class="col-sm-7" id="afteruploadimages">
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
                        </dd>
                        <?php } } }?>
                    </dl>
                </div>
                <div class="col-md-6">
                    <div id="map"></div>
                </div>
            </div>
            <!-- Custom 2 -->
        </div>
    </div>
</div>
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
@endsection

@section('styles')
<style type="text/css">
    #map {
        height: 450px;
    }
</style>
@endsection

@section('scripts')

<script src="https://maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&libraries=places"></script>
<?php if($request->status != 'COMPLETED'){?>
<script type="text/javascript">
    var map;
    var zoomLevel = 11;

    function initMap() {

        map = new google.maps.Map(document.getElementById('map'));

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
        <?php if(!empty($request->started_latitude)) { ?>
        source = new google.maps.LatLng({{ $request->started_latitude }}, {{ $request->started_longitude }});
        <?php } else { ?>
         source = new google.maps.LatLng({{ $request->s_latitude }}, {{ $request->s_longitude }});
        <?php } ?>
        destination = new google.maps.LatLng({{ $request->d_latitude }}, {{ $request->d_longitude }});
      

        marker.setPosition(source);
        markerSecond.setPosition(destination);

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true, preserveViewport: true});
        directionsDisplay.setMap(map);

        directionsService.route({
            origin: source,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING
        }, function(result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                console.log(result);
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
        console.log('Provider Bounds', markerProvider.getPosition());
        bounds.extend(markerProvider.getPosition());
        @endif

        bounds.extend(marker.getPosition());
        bounds.extend(markerSecond.getPosition());
        map.fitBounds(bounds);
    }
    google.maps.event.addDomListener(window, "load", initMap);
</script>
<?php } else {?>

<script type="text/javascript">
    var map;
    var zoomLevel = 11;

    function initMap() {

        map = new google.maps.Map(document.getElementById('map'));

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
       
        <?php if(!empty($firstmap) && !empty($lastmap) ){ ?>
        source = new google.maps.LatLng({{ $firstmap[1] }}, {{ $firstmap[2] }});
        destination = new google.maps.LatLng({{ $lastmap[1] }}, {{ $lastmap[2] }});
        <?php } else { ?>
        source = new google.maps.LatLng({{ $request->s_latitude }}, {{ $request->s_longitude }});
        destination = new google.maps.LatLng({{ $request->d_latitude }}, {{ $request->d_longitude }});
        <?php } ?>    
        marker.setPosition(source);
        markerSecond.setPosition(destination);

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true, preserveViewport: true});
        directionsDisplay.setMap(map);

        directionsService.route({
            origin: source,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING
        }, function(result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                console.log(result);
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
        console.log('Provider Bounds', markerProvider.getPosition());
        bounds.extend(markerProvider.getPosition());
        @endif

        bounds.extend(marker.getPosition());
        bounds.extend(markerSecond.getPosition());
        map.fitBounds(bounds);
    }
    google.maps.event.addDomListener(window, "load", initMap);
</script>
<?php } ?>
<script type="text/javascript">
$(function() {
        $('.pop').on('click', function() {
            $('.imagepreview').attr('src', $(this).find('img').attr('src'));
            $('#imagemodal').modal('show');   
        });     
});     
</script>
<script type="text/javascript">
		$(document).ready(function(){

			ezoom.onInit($('#beforeuploadimages img'), {
				hideControlBtn: false,
				onClose: function (result) {
					console.log(result);
				},
				onRotate: function (result) {
					console.log(result);
				},
			});


                     ezoom.onInit($('#afteruploadimages img'), {
				hideControlBtn: false,
				onClose: function (result) {
					console.log(result);
				},
				onRotate: function (result) {
					console.log(result);
				},
			});


		});
	</script>
@endsection