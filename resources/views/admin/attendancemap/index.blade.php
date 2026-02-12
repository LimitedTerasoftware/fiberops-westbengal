@extends('admin.layout.base')

@section('title', 'Map View ')

@section('content')
@php
    $user = Session::get('user');
    $DistId = null; 
    if ($user && isset($user->district_id)) {
        $DistId = $user->district_id;
    }
@endphp

<div class="content-area py-1">
        <div class="container-fluid ">
        	<div class="box box-block bg-white"> 
        <form action="{{route('admin.trackattendance')}}" method="GET">
            <div class="row">
                <div class="col-xs-2">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="district_id" id="district" required>
                   	<option value="">Please Select District</option>
                    @foreach($districts as $district)
                    <option value="{{$district->id}}" 
                        {{ (request('district_id') == $district->id) || ($DistId && $DistId == $district->id) ? 'selected' : '' }}>

                    {{$district->name}} </option> 
                   @endforeach 
                  </select>
                </div>
                    @if(count($blocks) > 0)
                  <div class="col-xs-2">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="block_id" id="block" required>
                   	<option value="">Please Select Block</option>
                    @foreach($blocks as $block)
                    <option value="{{$block->id}}" @if(Request::get('block_id')) @if(@Request::get('block_id') == $block->id) selected @endif @endif>{{$block->name}} </option> 
                   @endforeach 
                  </select>
                </div>
                @endif
 
				
				
				 <div class="col-xs-2">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="provider_id" id="person" required>
                   	<option value="">Please Select Person</option>
                    @foreach($providers as $provider)
                    <option value="{{$provider->id}}" @if(Request::get('provider_id')) @if(@Request::get('provider_id') == $provider->id) selected @endif @endif>{{$provider->first_name}} {{$provider->last_name}}</option> 
                   @endforeach 
                  </select>
                </div>
				
				<div class="form-group row col-md-3">
                            <label for="name" class="col-xs-4 col-form-label">Date From</label>
                            <div class="col-xs-8">
                                <input class="form-control" type="date" name="from_date" required placeholder="From Date">
                            </div>
                </div>
                            
                <div class="form-group row col-md-3">
                            <label for="email" class="col-xs-4 col-form-label">Date To</label>
                            <div class="col-xs-8">
                                <input class="form-control" type="date" required name="to_date" placeholder="To Date">
                            </div>
                  </div>
                
                <div class="col-xs-2">
                    <button type="submit" class="form-control btn btn-primary">Fetch</button>
                </div>  
            </div>
        </form>
        </div>       
            <div class="box box-block bg-white">
		     @if((Request::get('from_date') != '') && (Request::get('to_date') != ''))
            <h5 class="mb-1" style="color:#0275d8;"> Attendance Map View From {{Request::get('from_date')}} To {{Request::get('to_date')}}</h5>
		     @else
			 <h5 class="mb-1" style="color:#0275d8;">Today Attendance Map View</h5>	 
			 @endif
            <div class="row">
            	 <input type="hidden" name="districtdata" value="{{$district_id}}" id="districtdata" />
                 <input type="hidden" name="blockdata" value="{{$block_id}}" id="blockdata" />
  
				 <input type="hidden" name="fromdate" value="{{$from_date}}" id="fromdate" />
				 <input type="hidden" name="todate" value="{{$to_date}}" id="todate" />
				 <input type="hidden" name="providerid" value="{{$provider_id}}" id="providerid" />
				 
                <div class="col-xs-12">
                    <div id="map"></div>
                    <div id="legend"><h3>Note: </h3></div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('styles')
<style type="text/css">
    #map {
        height: 100%;
        min-height: 500px;
    }
    
    #legend {
        font-family: Arial, sans-serif;
        background: rgba(255,255,255,0.8);
        padding: 10px;
        margin: 10px;
        border: 2px solid #f3f3f3;
    }

    #legend h3 {
        margin-top: 0;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }

    #legend img {
        vertical-align: middle;
        margin-bottom: 5px;
    }
</style>
@endsection

@section('scripts')
<script>
$("select[id='district']").change(function(){
  var district = $(this).val();
  $.get('{{url("admin/ajax-blocks-id")}}/'+district,function(data) {
    $("#block").empty().append(data);      
  });
});

$("select[id='block']").change(function(){
  var block= $(this).val();
  $.get('{{url("admin/ajax-blockwise-providers")}}/'+block,function(data) {
    $("#person").empty().append(data);      
  });
});

</script>
<script>
        var map;
        var users;
        var districtdata = document.getElementById('districtdata').value;
        var blockdata = document.getElementById('blockdata').value;
        var fromdate = document.getElementById('fromdate').value;
        var todate = document.getElementById('todate').value;
        var providerid = document.getElementById('providerid').value;
        var providers;
        var googleMarkers = {}; // Changed to object for easier updating by ID

        var mapIcons = {
        user: '{{ asset("asset/img/marker-user.png") }}',
        active: '{{ asset("asset/img/marker-user.png") }}',
        riding: '{{ asset("asset/img/marker-user.png") }}',
        offline: '{{ asset("asset/img/map-marker-red.png") }}',
        unactivated: '{{ asset("asset/img/marker-plus.png") }}'
        }

        function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 20.8444, lng: 85.1511},
        zoom: 8,
        minZoom: 1
        });

        // Load initial data
        ajaxMapData();

        // Auto-update every 3 seconds
        setInterval(ajaxMapData, 3000);

        var legend = document.getElementById('legend');


        var div = document.createElement('div');
        div.innerHTML = '<img src="' + mapIcons['offline'] + '"> ' + 'offline';
        legend.appendChild(div);

        var div = document.createElement('div');
        div.innerHTML = '<img src="' + mapIcons['active'] + '"> ' + 'online';
        legend.appendChild(div);

        map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);
        }

        function ajaxMapData() {
        $.ajax({
        url: 'https://westbengal.terasoftware.com/admin/map/ajax?district_id='+
        districtdata+'&block_id='+blockdata+'&provider_id='+providerid +'&from_date='+fromdate+'&to_date='+todate,
        dataType: "JSON",
        headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken },
        type: "GET",
        success: function(data) {
        updateMarkers(data);
        }
        });
        }

        function updateMarkers(data) {
        var currentIds = [];

        data.forEach(function(element) {
        currentIds.push(element.id);
        var lat = parseFloat(element.latitude);
        var lng = parseFloat(element.longitude);
        var position = { lat: lat, lng: lng };
        var status = element.service ? element.service.status : element.status;
        var icon = mapIcons[status] || mapIcons['user'];

        // Handle address
        var baddress = (element.service && element.service.address)
        ? element.service.address
        : (element.latitude + "," + element.longitude);

        if (googleMarkers[element.id]) {
        // Update existing marker
        var marker = googleMarkers[element.id];
        marker.setPosition(position);
        marker.setIcon(icon);
        marker.setTitle(element.first_name + " " + element.last_name + "\n" + baddress);
        } else {
        // Add new marker
        var marker = new google.maps.Marker({
        position: position,
        map: map,
        title: element.first_name + " " +element.last_name + "\n" + baddress,
        icon: icon,
        id: element.id
        });

        // Add click listener
        google.maps.event.addListener(marker, 'click', function() {
        window.location.href = 'https://fleet.terasoftware.com/public/westbengal/public/admin/currentlocation/'+element.id;
        });

        googleMarkers[element.id] = marker;
        }
        });

        // Remove markers that are no longer in the data
        for (var id in googleMarkers) {
        if (!currentIds.includes(parseInt(id))) {
        googleMarkers[id].setMap(null);
        delete googleMarkers[id];
        }
        }
        }

        // Call initMap explicitly since the script is now in base
        if(typeof google !== 'undefined') {
        initMap();
        } else {
        window.addEventListener('load', initMap);
        }
        </script>

@endsection