@extends('admin.layout.base')

@section('title', 'Live Ticket Tracking')

@section('content')

<style>
/* PAGE LAYOUT */
.tracking-wrapper {
    display: grid;
    grid-template-columns: 350px 1fr;
    height: calc(100vh - 90px);
    margin-top: 15px;
    overflow: hidden;
}

/* LEFT PANEL */
.left-card {
    background: #fff;
    border-right: 1px solid #e2e8f0;
    overflow-y: auto;
    padding: 20px;
}

.left-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 15px;
    background: linear-gradient(90deg, #0066ff, #7b2ff7);
    color: #fff;
    padding: 12px;
    border-radius: 10px;
}

.info-label {
    font-size: 13px;
    color: #6b7280;
    margin-top: 6px;
}

.info-value {
    font-size: 15px;
    font-weight: 600;
    color: #1e293b;
}

.distance-box {
    display: inline-block;
    padding: 6px 12px;
    background: #2563eb;
    color: white;
    border-radius: 6px;
    margin-top: 8px;
    font-weight: bold;
}

/* MAP SECTION */
.map-area {
    position: relative;
    background: #fff;
}

#ticket-map {
    width: 100%;
    height: 100%;
}

/* LEGEND */
.legend-box {
    position: absolute;
    bottom: 20px;
    right: 20px;
    background: #fff;
    padding: 14px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    font-size: 12px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.dot-start { background: #10b981; }
.dot-end { background: #ef4444; }
.dot-gp { background: #3b82f6; }

.legend-line {
    width: 25px;
    height: 4px;
    background: #2563eb;
    border-radius: 4px;
}

.dotted-line {
    width: 25px;
    height: 4px;
    background: repeating-linear-gradient(
            to right,
            orange 0 4px,
            transparent 4px 8px
    );
}
</style>


<div class="tracking-wrapper">

    <!-- LEFT PANEL -->
    <div class="left-card">
        <div class="left-title">Live Ticket Tracking</div>

        <div class="info-label">Provider</div>
        <div class="info-value">
            {{ $data['provider']->first_name }} {{ $data['provider']->last_name }}
            ({{ $data['provider']->mobile }})
        </div>

        <div class="info-label">Booking ID</div>
        <div class="info-value">{{ $data['ticket']->booking_id }}</div>

        <div class="info-label">GP Location</div>
        <div class="info-value">
            {{ $data['ticket']->gpname }},
            {{ $data['ticket']->mandal }},
            {{ $data['ticket']->district }}
        </div>

        <div class="info-label">Start Time</div>
        <div class="info-value">{{ $data['ticket']->started_at }}</div>

        <div class="info-label">Start Point Time</div>
        <div class="info-value">{{ $data['start_point']['datetime'] ?? '-' }}</div>

        <div class="info-label">Last Point Time</div>
        <div class="info-value">{{ $data['last_point']['datetime'] ?? '-' }}</div>

        <div class="info-label">Travel Distance</div>
        <div class="distance-box"><span id="distanceKm">Calculating...</span> km</div>
    </div>


    <!-- MAP SECTION -->
    <div class="map-area">
        <div id="ticket-map"></div>

        <!-- LEGEND -->
        <div class="legend-box">
            <div class="legend-item"><span class="legend-dot dot-start"></span> Start Point</div>
            <div class="legend-item"><span class="legend-dot dot-end"></span> Last Point</div>
            <div class="legend-item"><span class="legend-dot dot-gp"></span> GP Location</div>
            <div class="legend-item"><span class="legend-line"></span> Travel Path</div>
            <div class="legend-item"><span class="dotted-line"></span> GP ? End (Dotted)</div>
        </div>
    </div>

</div>
@endsection




@section('scripts')
<script async defer 
src="https://maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&libraries=geometry&callback=initTicketMap">
</script>

<script>

/* ------------------------------------------------------
   CONFIG
-------------------------------------------------------*/
const USE_ROADS = true;
const SAMPLE_RATE = 1;
const SNAP_CHUNK_SIZE = 40;
const MIN_MOVE_METERS = 5;
const MAX_SPEED_KMPH = 180;

/* ------------------------------------------------------
   MAIN INITIALIZER
-------------------------------------------------------*/
async function initTicketMap() {

    const raw = {!! json_encode($data['travel_path']) !!} || [];

    const startPoint = {!! json_encode($data['start_point']) !!};
    const endPoint   = {!! json_encode($data['last_point']) !!};
    const gpPoint    = {!! json_encode($data['gp_location']) !!};

    // Convert data
    let points = raw.map(p => ({
        lat: parseFloat(p.lat),
        lng: parseFloat(p.lng),
        datetime: p.datetime
    })).filter(p => isFinite(p.lat) && isFinite(p.lng));

    if (points.length < 2) return alert("No tracking points");

    // Clean and filter GPS noise
    points = cleanPath(points);

    // Initialize map
    const map = new google.maps.Map(document.getElementById("ticket-map"), {
        center: points[0],
        zoom: 15
    });

    let snappedPath = [];

    if (USE_ROADS) {
        snappedPath = await snapToRoads(points);
    } else {
        snappedPath = points;
    }

    // Draw path
    drawPolyline(snappedPath, map);

    // Tooltip markers
    drawMarkers(map, startPoint, endPoint, gpPoint);

    // Distance calculation
    calculateDistance(snappedPath);

    // Animation
    animateMarker(snappedPath, map);
}

/* ------------------------------------------------------
   CLEAN RAW GPS POINTS
-------------------------------------------------------*/
function cleanPath(points) {
    const clean = [];

    for (let i = 0; i < points.length; i++) {

        if (!clean.length) {
            clean.push(points[i]);
            continue;
        }

        const prev = clean[clean.length - 1];
        const curr = points[i];

        const d = distance(prev, curr);

        if (d < MIN_MOVE_METERS) continue;

        const t1 = new Date(prev.datetime).getTime() / 1000;
        const t2 = new Date(curr.datetime).getTime() / 1000;
        const dt = Math.max(t2 - t1, 1);

        const speed = (d/1000) / (dt/3600);

        if (speed > MAX_SPEED_KMPH) continue;

        clean.push(curr);
    }

    return clean;
}

function distance(a, b) {
    return google.maps.geometry.spherical.computeDistanceBetween(
        new google.maps.LatLng(a.lat, a.lng),
        new google.maps.LatLng(b.lat, b.lng)
    );
}

/* ------------------------------------------------------
   SNAP TO ROADS
-------------------------------------------------------*/
async function snapToRoads(points) {

    let snapped = [];

    async function snapChunk(chunk) {

        if (chunk.length < 3) return;

        const path = chunk.map(p => `${p.lat},${p.lng}`).join("|");

        const url =
            `https://roads.googleapis.com/v1/snapToRoads?interpolate=true` +
            `&path=${path}` +
            `&key={{ Setting::get('map_key') }}`;

        try {
            const res = await fetch(url);
            const data = await res.json();

            if (data.snappedPoints) {
                data.snappedPoints.forEach(pt => {
                    snapped.push({
                        lat: pt.location.latitude,
                        lng: pt.location.longitude
                    });
                });
            }

        } catch (err) {
            console.error("Roads API error:", err);
        }
    }

    for (let i = 0; i < points.length; i += SNAP_CHUNK_SIZE) {

        let chunk = points.slice(i, i + SNAP_CHUNK_SIZE);

        if (i > 0) chunk.unshift(points[i - 1]);

        await snapChunk(chunk);
    }

    return snapped.filter((p, i, arr) =>
        i === 0 || !(p.lat === arr[i-1].lat && p.lng === arr[i-1].lng)
    );
}

/* ------------------------------------------------------
   DRAW PATH
-------------------------------------------------------*/
function drawPolyline(path, map) {
    new google.maps.Polyline({
        map,
        path,
        strokeColor: "#2563EB",
        strokeOpacity: 1.0,
        strokeWeight: 5
    });
}

/* ------------------------------------------------------
   MARKERS WITH TOOLTIP (START, END, GP)
-------------------------------------------------------*/
function drawMarkers(map, start, end, gp) {

    const infoWindow = new google.maps.InfoWindow();

    function addMarker(position, iconUrl, title, detailsHtml) {

        const marker = new google.maps.Marker({
            map,
            position: position,
            icon: iconUrl
        });

        marker.addListener("click", () => {
            infoWindow.setContent(`
                <div style="font-size:14px; line-height:20px; padding:4px;">
                    <b>${title}</b><br>
                    ${detailsHtml}
                </div>
            `);
            infoWindow.open(map, marker);
        });

        return marker;
    }

    // START MARKER
    if (start) {
        addMarker(
            { lat: parseFloat(start.lat), lng: parseFloat(start.lng) },
            "http://maps.google.com/mapfiles/ms/icons/green-dot.png",
            "Start Point",
            `
                <b>Time:</b> ${start.datetime}<br>
                <b>Latitude:</b> ${start.lat}<br>
                <b>Longitude:</b> ${start.lng}
            `
        );
    }

    // END MARKER
    if (end) {
        addMarker(
            { lat: parseFloat(end.lat), lng: parseFloat(end.lng) },
            "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
            "Last Tracked Point",
            `
                <b>Time:</b> ${end.datetime}<br>
                <b>Latitude:</b> ${end.lat}<br>
                <b>Longitude:</b> ${end.lng}
            `
        );
    }

    // GP MARKER
    if (gp) {
        addMarker(
            { lat: parseFloat(gp.lat), lng: parseFloat(gp.lng) },
            "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
            "GP Location",
            `
                <b>Latitude:</b> ${gp.lat}<br>
                <b>Longitude:</b> ${gp.lng}
            `
        );
    }
}

/* ------------------------------------------------------
   DISTANCE
-------------------------------------------------------*/
function calculateDistance(path) {
    let total = 0;

    for (let i = 1; i < path.length; i++) {
        total += google.maps.geometry.spherical.computeDistanceBetween(
            new google.maps.LatLng(path[i-1]),
            new google.maps.LatLng(path[i])
        );
    }

    document.getElementById("distanceKm").innerText = (total / 1000).toFixed(2);
}

/* ------------------------------------------------------
   ANIMATE MARKER
-------------------------------------------------------*/
function animateMarker(path, map) {

    let marker = new google.maps.Marker({
        map,
        position: path[0],
        icon: {
            url: "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png",
            scaledSize: new google.maps.Size(40, 40)
        }
    });

    let i = 0;

    function move() {
        if (i >= path.length) return;
        marker.setPosition(path[i]);
        map.panTo(path[i]);
        i++;
        requestAnimationFrame(move);
    }

    move();
}

</script>
@endsection

