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
    <div class="container-fluid">
        	<div class="box box-block bg-white"> 
        <form action="{{route('admin.map.index')}}" method="GET">
            <div class="row">
                <div class="col-xs-4">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="district_id" required>
                   	<option value="">Please Select District</option>
                    @foreach($districts as $district)
                    <option value="{{$district->id}}" 
                    {{ (request('district_id') == $district->id) || ($DistId && $DistId == $district->id) ? 'selected' : '' }}>

                        {{$district->name}} </option> 
                   @endforeach 
                  </select>
                </div>
                
                <div class="col-xs-2">
                    <button type="submit" class="form-control btn btn-primary">Fetch</button>
                </div>  
            </div>
        </form>
        </div>
       
        <div class="box box-block bg-white">
            <h5 class="mb-1">Tracking View</h5>
            <div class="row">
            	 <input type="hidden" name="districtdata" value="{{$district_id}}" id="districtdata" />
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
     var map;
        var users;
        var districtdata = document.getElementById('districtdata').value;
        var providers;
        var googleMarkers = {};
        var mapIcons = {
            user: '{{ asset("asset/img/marker-user.png") }}',
            active: '{{ asset("asset/img/marker-user.png") }}',
            riding: '{{ asset("asset/img/marker-user.png") }}',
            offline: '{{ asset("asset/img/map-marker-red.png") }}',
            unactivated: '{{ asset("asset/img/marker-plus.png") }}'
        }

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 20.8444, lng: 85.1511 },
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
                url: 'https://westbengal.terasoftware.com/admin/map/ajax?district_id=' + districtdata,
                dataType: "JSON",
                headers: { 'X-CSRF-TOKEN': window.Laravel.csrfToken },
                type: "GET",
                success: function (data) {
                    updateMarkers(data);
                }
            });
        }

        function updateMarkers(data) {
            var currentIds = [];

            data.forEach(function (element) {
                currentIds.push(element.id);
                var lat = parseFloat(element.latitude);
                var lng = parseFloat(element.longitude);
                var position = { lat: lat, lng: lng };
                var status = element.service ? element.service.status : element.status;
                var icon = mapIcons[status] || mapIcons['user'];

                if (googleMarkers[element.id]) {
                    // Update existing marker
                    var marker = googleMarkers[element.id];
                    marker.setPosition(position);
                    marker.setIcon(icon);
                } else {
                    // Add new marker
                    var baddress = element.service && element.service.address ? element.service.address : (element.latitude + "," + element.longitude);
                    var marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: element.first_name + " " + element.last_name + "\n" + baddress,
                        icon: icon,
                        id: element.id
                    });

                    // Add click listener
                    google.maps.event.addListener(marker, 'click', function () {
                        window.location.href = '/admin/currentlocation/' + element.id;
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

        if (typeof google !== 'undefined') {
            initMap();
        } else {
            window.addEventListener('load', initMap);
        }
</script>
@endsection