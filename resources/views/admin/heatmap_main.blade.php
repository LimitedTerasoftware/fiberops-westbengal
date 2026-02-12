@extends('admin.layout.base')

@section('title', 'GPS Map View')

@section('content')
<div class="gps-map-container">
    <!-- Filters -->
    <div class="filter-card">
       
    <form method="GET" id="filterForm">
    <div class="filter-row">
        <select name="district_id[]" class="filter-select" multiple>
            @foreach($districts as $district)
                <option value="{{ $district->id }}" 
                    {{ collect(request('district_id'))->contains($district->id) ? 'selected' : '' }}>
                    {{ $district->name }}
                </option>
            @endforeach
        </select>

        <select name="block_id[]" class="filter-select" multiple>
            @foreach($blocks as $block)
                <option value="{{ $block->id }}" 
                    {{ collect(request('block_id'))->contains($block->id) ? 'selected' : '' }}>
                    {{ $block->name }}
                </option>
            @endforeach
        </select>

        <select name="gp_id[]" class="filter-select" multiple>
            @foreach($allGPs as $gp)
                <option value="{{ $gp->lgd_code }}" 
                    {{ collect(request('gp_id'))->contains($gp->lgd_code) ? 'selected' : '' }}>
                    {{ $gp->gp_name }}
                </option>
            @endforeach
        </select>
         <select name="provider_id[]" class="filter-select" multiple>
            @foreach($providersData as $user)
                <option value="{{ $user->id }}" 
                    {{ collect(request('provider_id'))->contains($user->id) ? 'selected' : '' }}>
                    {{ $user->first_name }}{{ $user->last_name }}
                </option>
            @endforeach
        </select>
    </div>
</form>

    </div>

    <!-- Map -->
    <div id="map" class="map-container"></div>
    <!-- Floating Stats Card -->
    <div id="statsCard" class="card shadow-lg p-3"
        style="position:absolute; top:530px;  z-index:5; width:220px;">
    <h6 class="mb-2">GP Statistics</h6>
    <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between">
        <span>Total</span> <span id="totalGps">0</span>
        </li>
        <li class="list-group-item d-flex justify-content-between text-success">
        <span>UP</span> <span id="upGps">0</span>
        </li>
        <li class="list-group-item d-flex justify-content-between text-danger">
        <span>DOWN</span> <span id="downGps">0</span>
        </li>
    </ul>
    </div>

</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
.select2-container--default .select2-selection--multiple {
        border-radius: 6px;
        border: 1px solid #d1d5db;
        padding: 4px;
    }
.gps-map-container {
    padding: 1.5rem;
}

.filter-card {
    background: #fff;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}

.filter-row {
    display: flex;
    gap: 1rem;
}

.filter-select {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    min-width: 200px;
}

.map-container {
    width: 100%;
    height: 600px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 5px rgba(0,0,0,0.2);
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="//maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&libraries=places&callback=initMap" async defer></script>
<script>
$(document).ready(function() {
        $('.filter-select').select2({
            placeholder: "Select option",
            allowClear: true
        });

        // Auto submit on change
        $('.filter-select').on('change', function() {
            $('#filterForm').submit();
        });
    });
    
function initMap() {
  
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 7,
        center: { lat: 22.9734, lng: 87.8282 } // Center on West Bengal
    });

    const gpsPoints = {!! json_encode($gpsPoints) !!};
    const downGPSCodes ={!! json_encode($downGPSCodes) !!};
    const providers = {!! json_encode($providers) !!};

    const infoWindow = new google.maps.InfoWindow();
    let total = gpsPoints.length;
    let down = 0;
    let up = 0;
   // --- GP Markers ---
       gpsPoints.forEach(point => {
        const isDown = downGPSCodes.includes(point.lgd_code);
        if (isDown) down++; else up++;

        const marker = new google.maps.Marker({
            position: { 
                lat: parseFloat(point.latitude), 
                lng: parseFloat(point.longitude) 
            },
            map,
            title: point.gp_name,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 6,
                fillColor: isDown ? "red" : "green",  
                fillOpacity: 0.9,
                strokeWeight: 1,
                strokeColor: "#333"
            }
        });

        // InfoWindow content
        const content = `
            <div style="font-size:14px; line-height:1.4">
                <strong>GP:</strong> ${point.gp_name}<br>
                <strong>LGD Code:</strong> ${point.lgd_code}<br>
                <strong>District ID:</strong> ${point.district_id}<br>
                <strong>Block ID:</strong> ${point.block_id}<br>
                <strong>Phase:</strong> ${point.phase ?? ''}<br>
                <strong>Provider:</strong> ${point.provider ?? ''}<br>
                <strong>Contact No:</strong> ${point.contact_no ?? ''}<br>
                <strong>Patroller:</strong> ${point.petroller ?? ''}<br>
                <strong>Patroller Contact:</strong> ${point.petroller_contact_no ?? ''}<br>
                <strong>Status:</strong> <span style="color:${isDown ? 'red' : 'green'};font-weight:bold;">
                    ${isDown ? 'DOWN' : 'UP'}
                </span>
            </div>
        `;

        marker.addListener("click", () => {
            infoWindow.setContent(content);
            infoWindow.open(map, marker);
        });
    });

     // --- Provider Markers ---
    providers.forEach(provider => {
        const status = provider.service ? provider.service.status : 'inactive';
        const marker = new google.maps.Marker({
            position: { lat: parseFloat(provider.latitude), lng: parseFloat(provider.longitude) },
            map,
            title: provider.first_name + " " + provider.last_name,
            icon: status === 'active'
                ? "{{ asset('asset/img/marker-user.png') }}"
                : "{{ asset('asset/img/map-marker-red.png') }}"
        });

        const content = `
            <div>
                <strong>Provider:</strong> ${provider.first_name} ${provider.last_name}<br>
                <strong>Status:</strong> 
                <span style="color:${status === 'active' ? 'green' : 'red'};font-weight:bold;">
                    ${status.toUpperCase()}
                </span>
            </div>
        `;

        marker.addListener("click", () => {
            infoWindow.setContent(content);
            infoWindow.open(map, marker);
        });
    });
    document.getElementById("totalGps").innerText = total;
    document.getElementById("upGps").innerText = up;
    document.getElementById("downGps").innerText = down;

}


</script>
@endsection
