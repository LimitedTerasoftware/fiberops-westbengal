@extends('admin.layout.base')

@section('title', 'Employee Location History - ')

@section('content')
@php
$roles = [
1 => 'OFC',
2 => 'FRT',
5 => 'Patroller',
3 => 'Zonal incharge',
4 => 'District incharge'
];
@endphp

<div class="location-history-container"> 
    <!-- Header Section --> 
    <div class="page-header"> 
        <div class="header-content">
             <button class="close-btn" onclick="history.back()"> 
                <i class="fa fa-arrow-left"></i> </button> 
                <h1 class="page-title">Employee Location History</h1> 
            </div> 
        </div>

<!-- Filter Section -->
<div class="filter-section">
    <div class="date-filters">
        <div class="date-group">
            <input type="date" class="date-input" name="from_date" id="from-date" value="{{ request('date', date('Y-m-d')) }}">
        </div>
        <div class="date-group">
            <input type="date" class="date-input" name ="to_date" id="to-date" value="{{ request('date', date('Y-m-d')) }}">
        </div>
    </div>
    <div class="filter-actions">
        <button class="filter-btn btn-primary" onclick="applyFilters()">
            <i class="fa fa-filter"></i>
            Filter
        </button>
        <button class="filter-btn btn-secondary" onclick="clearFilters()">
            <i class="fa fa-eraser"></i>
            Clear
        </button>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Left Sidebar -->
    <div class="left-sidebar">
        <!-- Employee Info Card -->
        <div class="employee-card">
            <div class="employee-header">
                <div class="employee-avatar">
                      <img 
                                src="{{ $provider->online_image 
                                    ? asset('uploads/attendance_images/' . $provider->online_image) 
                                    : 'https://cdn-icons-png.flaticon.com/512/847/847969.png' }}" 
                                alt="{{ $provider->first_name }} {{ $provider->last_name }}"
                                width="50" 
                                height="50">

                </div>
                <div class="employee-info">
                    <h3 class="employee-name">{{ $provider->first_name }} {{ $provider->last_name }}</h3>
                    <p class="employee-role">
                        @if(isset($roles[$provider->type]))
                            {{ $roles[$provider->type] }}
                        @else
                            Unknown
                        @endif
                    </p>
                    <p class="employee-id">Mobile Num: {{ $provider->mobile }}</p>
                </div>
            </div>
            
            <div class="date-range">
                <span id="selected-date-range">{{ request('date', date('d-M-Y')) }} to {{ request('date', date('d-M-Y')) }}</span>
            </div>
            <div  id="daily-distance">

                 <!-- <span id="daily-distance">0 km</span> -->
            </div>
          
            <div class="distance-info">
                <span class="distance-label">Total distance:</span>

                <span class="distance-value" id="total-distance">0 km</span>
            </div>
        </div>

        <!-- Location History List -->
        <div class="history-list">
            <div id="location-entries">
                <div class="loading-state">
                    <i class="fa fa-spinner fa-spin"></i>
                    <p>Loading location history...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="map-section">
        <div class="map-controls">
            <div class="view-toggles">
                <button class="map-toggle active" data-view="map">
                    <i class="fa fa-map"></i>
                    Map
                </button>
                <button class="map-toggle" data-view="satellite">
                    <i class="fa fa-satellite"></i>
                    Satellite
                </button>
            </div>
            <button class="fullscreen-btn" onclick="toggleFullscreen()">
                <i class="fa fa-expand"></i>
            </button>
        </div>
        
        <div class="map-container">
            <div id="tracking-map" style="height: 100%; width: 100%;"></div>
        </div>

        <!-- Map Legend -->
        <div class="map-legend">
            <h4 class="legend-title">Legend</h4>
            <div class="legend-items" id="legend-items">
                <div class="legend-item">
                    <span class="legend-marker pin-drop"></span>
                    <span>GP Point</span>
                </div>
                <div class="legend-item">
                    <span class="legend-marker start-marker"></span>
                    <span>Start Point</span>
                </div>
                <div class="legend-item">
                    <span class="legend-marker end-marker"></span>
                    <span>End Point</span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('styles')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <style> /* Reset and Base Styles */ * { box-sizing: border-box; }
body {
margin: 0;
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
background-color: #f8fafc;
color: #1e293b;
}

.location-history-container {
min-height: 100vh;
display: flex;
flex-direction: column;
}

/* Header Section */
.page-header {
background: white;
padding: 1rem 1rem;
border-bottom: 1px solid #e2e8f0;
box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-content {
display: flex;
align-items: center;
gap: 1rem;
}

.close-btn {
width: 40px;
height: 40px;
border-radius: 8px;
border: none;
/* background: #f1f5f9; */
color: #64748b !important;
cursor: pointer;
display: flex;
align-items: center;
justify-content: center;
transition: all 0.2s ease;
}

.close-btn:hover {
background: #e2e8f0;
color: #1e293b;
}

.page-title {
font-size: 1.5rem;
font-weight: 600;
color: #7c3aed;
margin: 0;
}

/* Filter Section */
.filter-section {
background: white;
padding: 1.5rem 2rem;
border-bottom: 1px solid #e2e8f0;
display: flex;
justify-content: space-between;
align-items: center;
flex-wrap: wrap;
gap: 1rem;
}

.date-filters {
display: flex;
gap: 1rem;
align-items: center;
}

.date-group {
display: flex;
flex-direction: column;
}

.date-input {
padding: 0.75rem 1rem;
border: 1px solid #d1d5db;
border-radius: 8px;
font-size: 0.875rem;
background: white;
color: #1e293b;
min-width: 150px;
transition: all 0.2s ease;
}

.date-input:focus {
outline: none;
border-color: #3b82f6;
box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-actions {
display: flex;
gap: 0.75rem;
}

.filter-btn {
padding: 0.75rem 1.5rem;
border: none;
border-radius: 8px;
font-size: 0.875rem;
font-weight: 500;
cursor: pointer;
display: flex;
align-items: center;
gap: 0.5rem;
transition: all 0.2s ease;
}

.btn-primary {
background: #3b82f6;
color: white;
}

.btn-primary:hover {
background: #2563eb;
}

.btn-secondary {
background: #ef4444;
color: white;
}

.btn-secondary:hover {
background: #dc2626;
}

/* Main Content */
.main-content {
flex: 1;
display: grid;
grid-template-columns: 350px 1fr;
gap: 0;
min-height: calc(100vh - 140px);
}

/* Left Sidebar */
.left-sidebar {
background: #f1f5f9;
border-right: 1px solid #e2e8f0;
overflow-y: auto;
padding: 0;
}

.employee-card {
background: white;
margin: 1rem;
padding: 1.5rem;
border-radius: 12px;
box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
border-left: 4px solid #7c3aed;
}

.employee-header {
display: flex;
align-items: center;
gap: 1rem;
margin-bottom: 1rem;
}

.employee-avatar {
width: 50px;
height: 50px;
border-radius: 50%;
overflow: hidden;
flex-shrink: 0;
}

.employee-avatar img {
width: 100%;
height: 100%;
object-fit: cover;
}

.employee-info {
flex: 1;
}

.employee-name {
font-size: 1rem;
font-weight: 600;
color: #1e293b;
margin: 0 0 0.25rem 0;
}

.employee-role {
font-size: 0.875rem;
color: #64748b;
margin: 0 0 0.125rem 0;
}

.employee-id {
font-size: 0.75rem;
color: #94a3b8;
margin: 0;
}

.date-range {
padding: 0.75rem 0;
border-bottom: 1px solid #f1f5f9;
font-size: 0.875rem;
color: #64748b;
}

.distance-info {
padding: 0.75rem 0;
display: flex;
justify-content: space-between;
align-items: center;
}

.distance-label {
font-size: 0.875rem;
color: #64748b;
}

.distance-value {
font-size: 1rem;
font-weight: 600;
color: #1e293b;
}

/* History List */
.history-list {
margin: 1rem;
background: white;
border-radius: 12px;
box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
max-height: calc(100vh - 400px);
overflow-y: auto;
}

.loading-state {
padding: 2rem;
text-align: center;
color: #64748b;
}

.loading-state i {
font-size: 1.5rem;
margin-bottom: 0.5rem;
color: #3b82f6;
}

.date-header {
position: sticky;
top: 0;
z-index: 10;
background: #f8fafc;
padding: 0.75rem 1.5rem;
border-bottom: 1px solid #e2e8f0;
font-weight: 600;
color: #1e293b;
font-size: 0.875rem;
margin: 0;
border-radius: 0;
}

.date-header:first-child {
border-top-left-radius: 12px;
border-top-right-radius: 12px;
}

.location-entry {
padding: 1rem 1.5rem;
border-bottom: 1px solid #f1f5f9;
display: flex;
align-items: flex-start;
gap: 1rem;
cursor: pointer;
transition: all 0.2s ease;
}

.location-entry:hover {
background: #f8fafc;
}

.location-entry:last-child {
border-bottom: none;
}

.entry-marker {
width: 12px;
height: 12px;
border-radius: 50%;
margin-top: 4px;
flex-shrink: 0;
}

.marker-start {
background: #10b981;
}

.marker-end {
background: #ef4444;
}

.marker-waypoint {
background: #3b82f6;
}

.entry-content {
flex: 1;
}

.entry-time {
font-size: 0.875rem;
font-weight: 600;
color: #1e293b;
margin-bottom: 0.25rem;
}

.entry-location {
font-size: 0.875rem;
color: #64748b;
margin-bottom: 0.125rem;
}

.entry-coordinates {
font-size: 0.75rem;
color: #94a3b8;
}

/* Map Section */
.map-section {
position: relative;
background: white;
}

.map-controls {
position: absolute;
top: 1rem;
left: 1rem;
right: 1rem;
display: flex;
justify-content: space-between;
align-items: center;
z-index: 10;
}

.view-toggles {
display: flex;
background: white;
border-radius: 8px;
box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
overflow: hidden;
}

.map-toggle {
padding: 0.5rem 1rem;
border: none;
background: white;
color: #64748b;
font-size: 0.875rem;
cursor: pointer;
display: flex;
align-items: center;
gap: 0.5rem;
transition: all 0.2s ease;
}

.map-toggle.active {
background: #3b82f6;
color: white;
}

.map-toggle:hover:not(.active) {
background: #f8fafc;
}

.fullscreen-btn {
width: 40px;
height: 40px;
border: none;
background: white;
border-radius: 8px;
box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
cursor: pointer;
display: flex;
align-items: center;
justify-content: center;
color: #64748b;
transition: all 0.2s ease;
}

.fullscreen-btn:hover {
background: #f8fafc;
color: #1e293b;
}

.map-container {
height: 100%;
width: 100%;
}

/* Map Legend */
.map-legend {
position: absolute;
bottom: 1rem;
right: 1rem;
background: white;
padding: 1rem;
border-radius: 8px;
box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
z-index: 10;
min-width: 150px;
max-height: 300px;
overflow-y: auto;
}

.legend-title {
font-size: 0.875rem;
font-weight: 600;
color: #1e293b;
margin: 0 0 0.75rem 0;
}

.legend-items {
display: flex;
flex-direction: column;
gap: 0.5rem;
}

.legend-item {
display: flex;
align-items: center;
gap: 0.5rem;
font-size: 0.75rem;
color: #64748b;
}

.legend-marker {
width: 12px;
height: 3px;
flex-shrink: 0;
}

.start-marker {
background: #10b981;
border-radius: 50%;
height: 12px;
}

.pin-drop {
width: 20px;
height: 20px;
background: url("http://maps.google.com/mapfiles/ms/icons/green-dot.png") no-repeat center center;
background-size: contain;
margin-left:-5px;
}

.end-marker {
background: #ef4444;
border-radius: 50%;
height: 12px;
}

.day-trail {
height: 3px;
}

/* Responsive Design */
@media (max-width: 1024px) {
.main-content {
grid-template-columns: 320px 1fr;
}
}

@media (max-width: 768px) {
.main-content {
grid-template-columns: 1fr;
grid-template-rows: auto 1fr;
}


.left-sidebar {
    max-height: 300px;
    border-right: none;
    border-bottom: 1px solid #e2e8f0;
}

.filter-section {
    flex-direction: column;
    align-items: stretch;
}

.date-filters {
    justify-content: center;
}

.filter-actions {
    justify-content: center;
}
}

@media (max-width: 576px) {
.location-history-container {
padding: 0;
}


.page-header,
.filter-section {
    padding: 1rem;
}

.employee-card,
.history-list {
    margin: 0.5rem;
}

.page-title {
    font-size: 1.25rem;
}

.date-filters {
    flex-direction: column;
    gap: 0.75rem;
    width: 100%;
}

.date-input {
    width: 100%;
}
}

/* Utility Classes */
.fa {
font-family: 'Font Awesome 5 Free';
font-weight: 900;
}

.text-success {
color: #10b981;
}

.text-danger {
color: #ef4444;
}

.text-warning {
color: #f59e0b;
}

.hidden {
display: none;
}
</style>
@endsection

@section('scripts')

<script src="https://maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&callback=initTrackingMap" async defer></script> 
<script> 
// Complete Fixed JavaScript for Employee Location Tracking

let trackingMap;
let trackingMarkers = [];
let trackingPolylines = [];
let trackingData = [];
let gpMarkers = [];
let groupedTrackingData = {};
let currentMapType = 'roadmap';
let GPData = {!! json_encode($GP) !!}; 
let distanceInfoWindow = null;
let routesLoadedCount = 0;
let totalRoutesToLoad = 0;

// Distance storage
let roadDistances = {
    total: 0,
    daily: {},
    idle: {}
};

// Color palette for different days
const dayColors = [
    '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
    '#06b6d4', '#f97316', '#84cc16', '#ec4899', '#6b7280'
];

// Initialize tracking map
function initTrackingMap() {
    trackingMap = new google.maps.Map(document.getElementById('tracking-map'), {
        zoom: 13,
        center: { lat: 20.8444, lng: 85.1511 },
        mapTypeId: google.maps.MapTypeId.ROADMAP,
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

    loadTrackingData();
    loadGP();
}

// Load GP markers
function loadGP() {
    const bounds = new google.maps.LatLngBounds();

    GPData.forEach(gp => {
        const lat = parseFloat(gp.latitude);
        const lng = parseFloat(gp.longitude);

        if (!isNaN(lat) && !isNaN(lng)) {
            const icon = "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
            const marker = new google.maps.Marker({
                position: { lat, lng },
                map: trackingMap,
                title: gp.gp_name,
                icon: {
                    url: icon,
                    scaledSize: new google.maps.Size(25, 25)
                }
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px; min-width: 200px;">
                        <h3 style="margin: 0 0 8px 0; color: #1f2937;">${gp.gp_name}</h3>
                        <p style="margin: 0; color: #6b7280; font-size: 12px;">LGD Code: ${gp.lgd_code}</p>
                    </div>
                `
            });

            marker.addListener('click', () => infoWindow.open(trackingMap, marker));
            gpMarkers.push(marker);
            bounds.extend(marker.getPosition());
        }
    });

    if (gpMarkers.length > 0) {
        trackingMap.fitBounds(bounds);
    }
}

// Load tracking data from server
function loadTrackingData() {
    const fromDate = document.getElementById('from-date').value;
    const toDate = document.getElementById('to-date').value;
     const providerId = {{ $provider->id }}; 
    if (!providerId) {
        console.error('Provider ID not found');
        return;
    }

    const url = `/admin/employee_tracking_data/${providerId}?from_date=${fromDate}&to_date=${toDate}`;

    showLoadingState();

    fetch(url)
        .then(response => response.json())
        .then(data => {
            trackingData = data.tracking || [];

            roadDistances = {
                total: 0,
                daily: {},
                idle: data.daily_idle_time || {}
            };

            groupTrackingDataByDate();
            displayTrackingOnMap();
            updateLocationHistory();
        })
        .catch(error => {
            console.error('Error loading tracking data:', error);
            showLoadingError();
        });
}

// Group tracking data by date
function groupTrackingDataByDate() {
    groupedTrackingData = {};

    trackingData.forEach(point => {
        const date = point.date;
        if (!groupedTrackingData[date]) {
            groupedTrackingData[date] = [];
        }
        groupedTrackingData[date].push(point);
    });
}

// Haversine distance calculation (for reference)
function calculateDistance(lat1, lng1, lat2, lng2) {
    const R = 6371; // Earth's radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLng / 2) * Math.sin(dLng / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// Smart waypoint reduction to avoid unnecessary turns
function optimizeWaypoints(points, maxWaypoints = 23) {
    if (points.length <= maxWaypoints + 2) {
        return points.slice(1, -1).map(p => ({
            location: { lat: p.lat, lng: p.lng },
            stopover: false
        }));
    }

    // Douglas-Peucker algorithm for path simplification
    const epsilon = 0.0001; // Tolerance for simplification
    const simplified = douglasPeucker(points, epsilon);

    // If still too many points, use uniform sampling
    if (simplified.length > maxWaypoints + 2) {
        const step = Math.ceil((simplified.length - 2) / maxWaypoints);
        const sampled = [simplified[0]];

        for (let i = step; i < simplified.length - 1; i += step) {
            sampled.push(simplified[i]);
        }

        sampled.push(simplified[simplified.length - 1]);
        return sampled.slice(1, -1).map(p => ({
            location: { lat: p.lat, lng: p.lng },
            stopover: false
        }));
    }

    return simplified.slice(1, -1).map(p => ({
        location: { lat: p.lat, lng: p.lng },
        stopover: false
    }));
}

// Douglas-Peucker algorithm for path simplification
function douglasPeucker(points, epsilon) {
    if (points.length <= 2) return points;

    let maxDistance = 0;
    let maxIndex = 0;

    const start = points[0];
    const end = points[points.length - 1];

    for (let i = 1; i < points.length - 1; i++) {
        const distance = perpendicularDistance(points[i], start, end);
        if (distance > maxDistance) {
            maxDistance = distance;
            maxIndex = i;
        }
    }

    if (maxDistance > epsilon) {
        const left = douglasPeucker(points.slice(0, maxIndex + 1), epsilon);
        const right = douglasPeucker(points.slice(maxIndex), epsilon);
        return left.slice(0, -1).concat(right);
    } else {
        return [start, end];
    }
}

// Calculate perpendicular distance from point to line
function perpendicularDistance(point, lineStart, lineEnd) {
    const dx = lineEnd.lat - lineStart.lat;
    const dy = lineEnd.lng - lineStart.lng;

    const numerator = Math.abs(
        dy * point.lat - dx * point.lng + lineEnd.lat * lineStart.lng - lineEnd.lng * lineStart.lat
    );
    const denominator = Math.sqrt(dx * dx + dy * dy);

    return numerator / denominator;
}

// Display tracking data on map with road-based routing
function displayTrackingOnMap() {
    clearTrackingMap();

    if (trackingData.length === 0) {
        showNoDataMessage();
        return;
    }

    const dates = Object.keys(groupedTrackingData).sort();
    const bounds = new google.maps.LatLngBounds();

    roadDistances = {
        total: 0,
        daily: {},
        idle: roadDistances.idle
    };

    routesLoadedCount = 0;
    totalRoutesToLoad = dates.filter(date => groupedTrackingData[date].length >= 2).length;

    if (!distanceInfoWindow) {
        distanceInfoWindow = new google.maps.InfoWindow();
    }

    updateMapLegend(dates);

    const directionsService = new google.maps.DirectionsService();

    dates.forEach((date, dayIndex) => {
        const dayPoints = groupedTrackingData[date];
        const dayColor = dayColors[dayIndex % dayColors.length];

        if (dayPoints.length < 2) {
            routesLoadedCount++;
            return;
        }

        // Convert to proper format
        const formattedPoints = dayPoints.map(p => ({
            lat: parseFloat(p.latitude),
            lng: parseFloat(p.longitude),
            datetime: p.datetime
        }));

        const startPoint = formattedPoints[0];
        const endPoint = formattedPoints[formattedPoints.length - 1];

        // Optimize waypoints to reduce unnecessary turns
        const waypoints = optimizeWaypoints(formattedPoints);

        directionsService.route(
            {
                origin: startPoint,
                destination: endPoint,
                waypoints: waypoints,
                travelMode: google.maps.TravelMode.DRIVING,
                optimizeWaypoints: false,
                avoidHighways: false,
                avoidTolls: false
            },
            (response, status) => {
                if (status === google.maps.DirectionsStatus.OK) {
                    const route = response.routes[0];
                    const path = route.overview_path;

                    // Calculate accurate road distance from Google Directions API
                    let routeDistance = 0;
                    route.legs.forEach(leg => {
                        routeDistance += leg.distance.value; // in meters
                    });
                    routeDistance = routeDistance / 1000; // convert to km

                    // Store road distance
                    roadDistances.daily[date] = routeDistance;
                    roadDistances.total += routeDistance;
                    routesLoadedCount++;

                    // Update UI when all routes loaded
                    if (routesLoadedCount === totalRoutesToLoad) {
                        updateDistanceInfo();
                    }

                    // Create smooth polyline with road path
                    const polyline = new google.maps.Polyline({
                        path: path,
                        geodesic: true,
                        strokeColor: dayColor,
                        strokeOpacity: 0.9,
                        strokeWeight: 5,
                        map: trackingMap
                    });

                    // Add hover effect for distance info
                    addPolylineHoverEffect(polyline, route, dayColor, date, dayPoints.length);

                    trackingPolylines.push(polyline);

                    // Add start marker
                    const startMarker = new google.maps.Marker({
                        position: startPoint,
                        map: trackingMap,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 10,
                            fillColor: '#10b981',
                            fillOpacity: 1,
                            strokeWeight: 3,
                            strokeColor: '#ffffff'
                        },
                        title: `Start: ${formatDate(date)} - ${dayPoints[0].datetime}`,
                        zIndex: 1000
                    });

                    addMarkerInfoWindow(startMarker, 'Start Point', dayPoints[0].datetime, dayPoints[0].address);
                    trackingMarkers.push(startMarker);

                    // Add end marker
                    const endMarker = new google.maps.Marker({
                        position: endPoint,
                        map: trackingMap,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 10,
                            fillColor: '#ef4444',
                            fillOpacity: 1,
                            strokeWeight: 3,
                            strokeColor: '#ffffff'
                        },
                        title: `End: ${formatDate(date)} - ${dayPoints[dayPoints.length - 1].datetime}`,
                        zIndex: 1000
                    });

                    addMarkerInfoWindow(endMarker, 'End Point', dayPoints[dayPoints.length - 1].datetime, dayPoints[dayPoints.length - 1].address);
                    trackingMarkers.push(endMarker);

                    // Extend bounds
                    path.forEach(p => bounds.extend(p));

                    if (routesLoadedCount === totalRoutesToLoad) {
                        trackingMap.fitBounds(bounds);
                    }
                } else {
                    console.error(`Directions request failed for ${date}: ${status}`);
                    routesLoadedCount++;

                    if (routesLoadedCount === totalRoutesToLoad) {
                        updateDistanceInfo();
                    }
                }
            }
        );
    });
}

// Add hover effect to polyline showing distance along route
function addPolylineHoverEffect(polyline, route, color, date, totalPoints) {
    let cumulativeDistances = [];
    let currentDistance = 0;

    // Calculate cumulative distances along the route
    route.legs.forEach(leg => {
        leg.steps.forEach(step => {
            cumulativeDistances.push({
                distance: currentDistance,
                position: step.start_location
            });
            currentDistance += step.distance.value / 1000; // Convert to km
        });
    });

    cumulativeDistances.push({
        distance: currentDistance,
        position: route.legs[route.legs.length - 1].end_location
    });

    google.maps.event.addListener(polyline, 'mousemove', function (event) {
        const cursorLatLng = event.latLng;

        // Find closest point on route
        let minDist = Infinity;
        let closestDistance = 0;

        cumulativeDistances.forEach(item => {
            const dist = google.maps.geometry.spherical.computeDistanceBetween(
                cursorLatLng,
                item.position
            );

            if (dist < minDist) {
                minDist = dist;
                closestDistance = item.distance;
            }
        });

        const content = `
            <div style="padding: 10px 14px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                <div style="font-weight: 600; color: #1e293b; margin-bottom: 6px; font-size: 0.9rem;">
                    Distance from Start
                </div>
                <div style="font-size: 1.5rem; font-weight: 700; color: ${color};">
                    ${closestDistance.toFixed(2)} km
                </div>
                <div style="font-size: 0.75rem; color: #64748b; margin-top: 6px;">
                    ${formatDate(date)} • Road Distance
                </div>
            </div>
        `;

        distanceInfoWindow.setContent(content);
        distanceInfoWindow.setPosition(cursorLatLng);
        distanceInfoWindow.open(trackingMap);
    });

    google.maps.event.addListener(polyline, 'mouseout', function () {
        distanceInfoWindow.close();
    });
}

// Add info window to marker
function addMarkerInfoWindow(marker, title, datetime, address) {
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div style="padding: 10px; min-width: 200px;">
                <h3 style="margin: 0 0 8px 0; color: #1f2937; font-size: 1rem;">${title}</h3>
                <p style="margin: 4px 0; color: #4b5563; font-size: 0.875rem;">
                    <strong>Time:</strong> ${formatDateTime(datetime)}
                </p>
                <p style="margin: 4px 0; color: #6b7280; font-size: 0.875rem;">
                    ${address || 'Unknown Location'}
                </p>
            </div>
        `
    });

    marker.addListener('click', () => infoWindow.open(trackingMap, marker));
}

// Update map legend
function updateMapLegend(dates) {
    const legendItems = document.getElementById('legend-items');
    let legendHtml = `
        <div class="legend-item">
            <span class="legend-marker pin-drop"></span>
            <span>GP Point</span>
        </div>
        <div class="legend-item">
            <span class="legend-marker start-marker"></span>
            <span>Start Point</span>
        </div>
        <div class="legend-item">
            <span class="legend-marker end-marker"></span>
            <span>End Point</span>
        </div>
    `;

    dates.forEach((date, index) => {
        const color = dayColors[index % dayColors.length];
        const formattedDate = formatDate(date);
        legendHtml += `
            <div class="legend-item">
                <span class="legend-marker day-trail" style="background-color: ${color};"></span>
                <span>${formattedDate}</span>
            </div>
        `;
    });

    legendItems.innerHTML = legendHtml;
}

// Update distance information display
function updateDistanceInfo() {
    const totalDistance = roadDistances.total;
    const dailyDistances = roadDistances.daily;
    const idleTimes = roadDistances.idle;

    // Update total distance
    const safeTotal = parseFloat(totalDistance) || 0;
    document.getElementById('total-distance').innerHTML = `
        ${safeTotal.toFixed(2)} km
        <span style="font-size: 0.75rem; color: #10b981;">• Road Distance</span>
    `;

    // Update daily distances
    let dailyHtml = '';
    const sortedDates = Object.keys(dailyDistances).sort();

    sortedDates.forEach(date => {
        const km = dailyDistances[date] || 0;
        const idle = idleTimes[date] ? formatSecondsToHms(idleTimes[date]) : '0m';

        dailyHtml += `
            <div class="distance-info" style="border-bottom: 1px solid #f1f5f9; padding: 0.75rem 0;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 4px;">
                    <div class="distance-label" style="font-weight: 600; font-size: 0.95rem;">${formatDate(date)}</div>
                    
                </div>
                <div class="distance-value" style="font-size: 1.2rem; font-weight: 700; color: #10b981;">
                        ${parseFloat(km).toFixed(2)} km
                    </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div class="idle-time" style="font-size: 0.75rem; color: #64748b; display: flex; align-items: center; gap: 4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        Idle: ${idle}
                    </div>
                </div>
            </div>
        `;
    });

    document.getElementById('daily-distance').innerHTML = dailyHtml;
}

// Format seconds to human readable time
function formatSecondsToHms(seconds) {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);

    let result = '';
    if (h > 0) result += h + 'h ';
    if (m > 0 || h === 0) result += m + 'm';

    return result.trim() || '0m';
}

// Update location history list
function updateLocationHistory() {
    const locationEntries = document.getElementById('location-entries');

    if (trackingData.length === 0) {
        locationEntries.innerHTML = `
            <div class="loading-state">
                <i class="fa fa-map-marker-alt"></i>
                <p>No location data found for selected date range</p>
            </div>
        `;
        return;
    }

    const dates = Object.keys(groupedTrackingData).sort().reverse();
    let html = '';

    dates.forEach(date => {
        const dayPoints = groupedTrackingData[date];
        const formattedDate = formatDate(date);

        html += `<div class="date-header">${formattedDate}</div>`;

        dayPoints.forEach((point, index) => {
            const isStart = index === 0;
            const isEnd = index === dayPoints.length - 1;
            const markerClass = isStart ? 'marker-start' : (isEnd ? 'marker-end' : 'marker-waypoint');
            const timeString = formatTime(point.datetime);

            html += `
                <div class="location-entry" onclick="focusOnPoint('${point.date}', ${index})">
                    <div class="entry-marker ${markerClass}"></div>
                    <div class="entry-content">
                        <div class="entry-time">${timeString}</div>
                        <div class="entry-location">${point.latitude} | ${point.longitude}</div>
                        <div class="entry-location">${point.address || 'Unknown Location'}</div>
                    </div>
                </div>
            `;
        });
    });

    locationEntries.innerHTML = html;
}

// Focus on specific point on map
function focusOnPoint(date, index) {
    if (groupedTrackingData[date] && groupedTrackingData[date][index]) {
        const point = groupedTrackingData[date][index];
        const position = new google.maps.LatLng(
            parseFloat(point.latitude),
            parseFloat(point.longitude)
        );

        trackingMap.setCenter(position);
        trackingMap.setZoom(17);

        // Highlight the point temporarily
        const highlightMarker = new google.maps.Marker({
            position: position,
            map: trackingMap,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 15,
                fillColor: '#fbbf24',
                fillOpacity: 0.8,
                strokeWeight: 3,
                strokeColor: '#ffffff'
            },
            animation: google.maps.Animation.BOUNCE
        });

        setTimeout(() => {
            highlightMarker.setMap(null);
        }, 2000);
    }
}

// Clear all tracking markers and polylines
function clearTrackingMap() {
    trackingMarkers.forEach(marker => marker.setMap(null));
    trackingPolylines.forEach(polyline => polyline.setMap(null));
    trackingMarkers = [];
    trackingPolylines = [];
}

// Show loading state
function showLoadingState() {
    document.getElementById('location-entries').innerHTML = `
        <div class="loading-state">
            <i class="fa fa-spinner fa-spin"></i>
            <p>Loading location history...</p>
        </div>
    `;
}

// Show loading error
function showLoadingError() {
    document.getElementById('location-entries').innerHTML = `
        <div class="loading-state">
            <i class="fa fa-exclamation-triangle"></i>
            <p>Error loading location data</p>
        </div>
    `;
}

// Show no data message
function showNoDataMessage() {
    const noDataOverlay = document.createElement('div');
    noDataOverlay.style.cssText = `
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: #64748b;
        z-index: 1000;
    `;
    noDataOverlay.innerHTML = `
        <i class="fa fa-map-marker-alt" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
        <h3 style="font-size: 1.25rem; font-weight: 600; color: #475569; margin: 0 0 0.5rem 0;">No Location Data</h3>
        <p style="font-size: 0.875rem; color: #64748b; margin: 0;">No tracking data found for the selected date range</p>
    `;
    document.querySelector('.map-container').appendChild(noDataOverlay);
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        weekday: 'short',
        month: 'short',
        day: '2-digit',
        year: 'numeric'
    });
}

// Format time for display
function formatTime(datetimeString) {
    const date = new Date(datetimeString);
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}

// Format datetime for display
function formatDateTime(datetimeString) {
    const date = new Date(datetimeString);
    return date.toLocaleString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}

// Apply filters
function applyFilters() {
    const fromDate = document.getElementById('from-date').value;
    const toDate = document.getElementById('to-date').value;

    if (!fromDate || !toDate) {
        alert('Please select both from and to dates');
        return;
    }

    if (new Date(fromDate) > new Date(toDate)) {
        alert('From date cannot be after To date');
        return;
    }

    // Update date range display
    const fromFormatted = new Date(fromDate).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
    const toFormatted = new Date(toDate).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });

    document.getElementById('selected-date-range').textContent = `${fromFormatted} to ${toFormatted}`;

    loadTrackingData();
}

// Clear filters
function clearFilters() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('from-date').value = today;
    document.getElementById('to-date').value = today;
    applyFilters();
}

// Toggle fullscreen
function toggleFullscreen() {
    const mapSection = document.querySelector('.map-section');
    const fullscreenBtn = document.querySelector('.fullscreen-btn i');

    if (!document.fullscreenElement) {
        mapSection.requestFullscreen().then(() => {
            fullscreenBtn.classList.remove('fa-expand');
            fullscreenBtn.classList.add('fa-compress');
        }).catch(err => {
            console.error('Error entering fullscreen:', err);
        });
    } else {
        document.exitFullscreen().then(() => {
            fullscreenBtn.classList.remove('fa-compress');
            fullscreenBtn.classList.add('fa-expand');
        });
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function () {
    // Map type toggles
    document.querySelectorAll('.map-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.map-toggle').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const view = this.dataset.view;
            if (view === 'satellite') {
                trackingMap.setMapTypeId(google.maps.MapTypeId.SATELLITE);
            } else {
                trackingMap.setMapTypeId(google.maps.MapTypeId.ROADMAP);
            }
        });
    });

    // Date change listeners
    document.getElementById('from-date').addEventListener('change', function () {
        const toDate = document.getElementById('to-date');
        if (this.value > toDate.value) {
            toDate.value = this.value;
        }
    });

    document.getElementById('to-date').addEventListener('change', function () {
        const fromDate = document.getElementById('from-date');
        if (this.value < fromDate.value) {
            fromDate.value = this.value;
        }
    });
});

// Initialize map when Google Maps API loads
if (typeof google !== 'undefined') {
    google.maps.event.addDomListener(window, 'load', initTrackingMap);
}
 
</script>
@endsection
