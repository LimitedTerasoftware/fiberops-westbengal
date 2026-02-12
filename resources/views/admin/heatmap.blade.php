@extends('admin.layout.base')

@section('title', 'Employee Location Dashboard - ')

@section('content')
@php
    $user = Session::get('user');
    $DistId = null; 
    if ($user && isset($user->district_id)) {
        $DistId = $user->district_id;
    }
@endphp
<div class="location-dashboard">
  <!-- Main Content -->
    <div class="dashboard-content">
         <!-- Right Side - Filters -->
        <div class="filters-section" id="filtersSection">
            <div class="filters-header">
                <h2 class="filters-title">Filters & Controls</h2>
               <div class="filters-actions">
                    
                    <button class="clear-filters-btn" onclick="clearAllFilters()">
                        <i class="fas fa-eraser"></i> Clear All
                    </button>
                </div>
            </div>
			
            <div class="filters-content">
				<div class="filter-container">

					<div class="filter-header" onclick="toggleFilterSection()">
						<i class="fas fa-filter"></i> Filters
						<i class="fas fa-chevron-down filter-arrow"></i>
					</div>
                    <div class="filter-body" id="filterSection">

                        <div class="filter-group"> 
                            <label class="filter-label"> <i class="fas fa-calendar-alt"></i> Date Range </label>
                            <div class="date-range-inputs"> 
                                <input type="date" class="filter-input" id="from-date" value="{{ date('Y-m-d') }}">
                                <span class="date-separator">to</span> 
                                <input type="date" class="filter-input" id="to-date" value="{{ date('Y-m-d') }}"> 
                            </div> 
                        </div>
                        <form method="GET" id="filterForm">
                        
                        
                            <div class="filter-group">
                                <label class="filter-label">
                                    <i class="fas fa-map-marked-alt"></i> District
                                </label>
                                <select id="district-filter" name="district_id[]"  multiple="multiple">
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" 
                                        data-name="{{ $district->name }}"
                                           @php
                                                $selectedDistricts = collect(request('district_id', []));
                                                $isSelected = $selectedDistricts->contains($district->id) || ($DistId && $DistId == $district->id);
                                            @endphp
                                            {{ $isSelected ? 'selected' : '' }}>
                                            {{ $district->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Block Filter -->
                            <div class="filter-group">
                                <label class="filter-label">
                                    <i class="fas fa-building"></i>
                                    Block
                                </label>
                                <select   name="block_id[]"  id="block-filter" multiple="multiple">
                                    @foreach($blocks as $block)
                                        <option value="{{ $block->id }}"  data-name="{{ $block->name }}" {{ collect(request('block_id'))->contains($block->id) ? 'selected' : '' }}>{{ $block->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- GP Filter -->
                            <div class="filter-group">
                                <label class="filter-label">
                                    <i class="fas fa-home"></i>
                                    Gram Panchayat
                                </label>
                                <select name="gp_id[]"  id="gp-filter"  multiple="multiple">
                                    <option value="">All GPs</option>
                                    @foreach($allGPs as $gp)
                                        <option value="{{ $gp->lgd_code }}" data-name="{{ $gp->gp_name }}"{{ collect(request('gp_id'))->contains($gp->lgd_code) ? 'selected' : '' }}>{{ $gp->gp_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Employee Filter -->
                            <div class="filter-group">
                                <label class="filter-label">
                                    <i class="fas fa-users"></i>
                                    Employees
                                </label>
                                <select  name="provider_id[]"   id="employee-filter"  multiple="multiple">
                                    <option value="">All Employees</option>
                                    @foreach($providersData as $provider)
                                        <option value="{{ $provider->id }}" data-name="{{ $provider->first_name}}"{{ collect(request('provider_id'))->contains($provider->id) ? 'selected' : '' }}>{{ $provider->first_name }} {{ $provider->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                    

                            <!-- Apply Filters Button -->
                            <div class="filter-actions">
                                <button  type="submit" class="apply-filters-btn">
                                    <i class="fas fa-filter"></i>
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                     </div>
                </div>
				
				
                <div class="filter-group">

                    <!-- People Section -->
                    <div class="filter-section">
                        <div class="section-header" onclick="toggleSection('people-filters', this)">
                            <span><i class="fas fa-users"></i> People  <i class="fas fa-chevron-down arrows"></i></span>
                        </div>
                        <div id="people-filters" class="section-body" style="display: none;">

                        <div class="layer-row">
                                <label class="layer-title">
                                    Employees <span class="count" id="Tot-EMP">0</span>
                                </label>
                                <label class="layer-toggle">
                                    <input type="checkbox" id="show-employees" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="sub-options">
                                <label class="sub-toggle" title="Employees currently online and working">
                                    <input type="checkbox" id="show-active-employees" checked>
                                    <span class="checkbox"></span> Online
                                </label>
                                <label class="sub-toggle" title="Employees who are offline or not connected">
                                    <input type="checkbox" id="show-inactive-employees" checked>
                                    <span class="checkbox"></span> Offline
                                </label>
                                <label class="sub-toggle" title="Employees who are online but idle for too long">
                                    <input type="checkbox" id="show-idle-employees" checked>
                                    <span class="checkbox"></span> Idle
                                </label>
                                <label class="sub-toggle" title="Employees who never logged in / absent today">
                                    <input type="checkbox" id="show-absent-employees">
                                    <span class="checkbox"></span> Absent
                                </label>
                            </div>


                            <div class="layer-row">
                                <label class="layer-title">
                                    <div class="Icon-item">
                                    <img  src="https://maps.google.com/mapfiles/kml/shapes/cabs.png"
                                    alt="FRT" 
                                    style="width:20px; height:20px; margin-top:-6px; vertical-align:middle;">

                                    FRT </div>
                                    <span class="count" id="total-frt">0</span>
                                    
                                </label>
                                <label class="layer-toggle">
                                    <input type="checkbox" id="show-frt" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                                
                            </div>

                            <div class="layer-row">
                                <label class="layer-title">
                                <div class="Icon-item">

                                <img  src="https://maps.google.com/mapfiles/kml/shapes/motorcycling.png"
                                    alt="Patroller" 
                                    style="width:20px; height:20px; margin-top:-6px; vertical-align:middle;">

                                    Patrollers
                                    </div>
                                        <span class="count" id="total-patroller">0</span>
                                
                                </label>
                                <label class="layer-toggle">
                                    <input type="checkbox" id="show-patroller" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                              <div class="layer-row">
                                <label class="layer-title">
                                <div class="Icon-item">

                                <img  src="https://maps.google.com/mapfiles/kml/pal4/icon15.png"
                                    alt="Patroller" 
                                    style="width:20px; height:20px; margin-top:-6px; vertical-align:middle;">

                                    District Incharge
                                    </div>
                                        <span class="count" id="total-di">0</span>
                                
                                </label>
                                <label class="layer-toggle">
                                    <input type="checkbox" id="show-di" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Infrastructure Section -->
                    <div class="filter-section">
                        <div class="section-header" onclick="toggleSection('infra-filters', this)">
                            <span><i class="fas fa-layer-group"></i> Infrastructure   <i class="fas fa-chevron-down arrows"></i></span>
                        </div>
                        <div id="infra-filters" class="section-body" style="display: none;">
                            
                        
                        <div class="layer-row">
                                <label class="layer-title">
                                    GPs <span class="count" id="Tot-GP">0</span>
                                </label>
                                <label class="layer-toggle">
                                    <input type="checkbox" id="show-gps" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="sub-options">
                                <label class="sub-toggle">
                                    <img src="http://maps.google.com/mapfiles/ms/icons/green-dot.png"
                                                alt="UP GP" 
                                                style="width:20px; height:20px;">

                                    <input type="checkbox"  id="show-up-gps" checked>
                                    <span class="checkbox"></span> UP - <span id="total-upgp">0 </span>
                                    
                                </label>
                                <label class="sub-toggle">
                                <img src="http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                                                alt="DOWN GP" 
                                                style="width:20px; height:20px;">

                                    <input type="checkbox" id="show-down-gps" checked>
                                    <span class="checkbox"></span> DOWN - <span id="total-downgp">0 </span>
                                </label>
                            </div>
                        

                            <div class="layer-row">
                                <label class="layer-title">
                                    Cables
                                    
                                    <span class="count" id="cable-count">0</span>
                                
                                </label>
                                <label class="layer-toggle">
                                    <input type="checkbox" id="show-cables" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="sub-options">
                                <label class="sub-toggle">
                                    <input type="checkbox" id="show-inCable" checked>
                                    <span class="checkbox trail-lineIncr"></span> Incremental - <span id="total-inCable">0</span>
                                </label>

                                <label class="sub-toggle">
                                    <input type="checkbox" id="show-PropCable" checked>
                                    <span class="checkbox trail-lineProposed"></span> Block to FPOI - <span id="total-PropCable">0</span>
                                </label>
                            </div>


                            <div class="layer-row">
                                <label class="layer-title">
                                    <div class='Icon-item'>
                                    <img src="https://maps.google.com/mapfiles/kml/shapes/homegardenbusiness.png" 
                                                alt="OLT" 
                                                style="width:20px; height:20px;">

                                    Blocks</div> <span class="count olt-count" id="olt-count">0</span>
                                
                                </label>
                                <label class="layer-toggle">
                                    <input type="checkbox" id="show-ont">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
</div>
               
        </div>
        <!-- Left Side - Map -->
        <div class="map-section">
           
            
            <div class="map-container">
                 <div class="map-header">
                <div class="map-controls">
                    
                    <div class="map-actions">
                        @if(auth()->user()->role != 'client')
                        <button class="pdf-btn" onclick="downloadSelectedPDFs()">
                            <i class="fas fa-file-pdf"></i>
                            <span>Download PDFs</span>
                        </button>
                        @endif
                         <button class="refresh-btn" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i>
                            <span>Refresh</span>
                        </button>
                        <button class="map-action-btn" onclick="centerMap()">
                            <i class="fas fa-crosshairs"></i>
                        </button>
                        <button class="map-action-btn" onclick="toggleFullscreen()">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                    <div class="view-toggles">
                        <button class="map-toggle active" data-view="roadmap">
                            <i class="fas fa-map"></i>
                            <span>Map</span>
                        </button>
                        <button class="map-toggle" data-view="satellite">
                            <i class="fas fa-satellite"></i>
                            <span>Satellite</span>
                        </button>
                        <button class="map-toggle" data-view="terrain">
                            <i class="fas fa-mountain"></i>
                            <span>Terrain</span>
                        </button>
                    </div>
                </div>
            </div>
                <div id="tracking-map"></div>
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">

<style>
/* Reset and Base Styles */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #1a202c;
    line-height: 1.6;
}

/* .location-dashboard {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
} */
.location-dashboard {
    display: flex;
    width: 100%;
    height: 100vh;
}

/* Header Styles */
.dashboard-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(226, 232, 240, 0.8);
    padding: 0rem 1rem;
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1400px;
    margin: 0 auto;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.back-btn {
    width: 44px;
    height: 44px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.back-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.dashboard-title {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.25rem;
}

.dashboard-subtitle {
    color: #64748b;
    font-size: 0.875rem;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
}

.refresh-btn, .export-btn, .pdf-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.refresh-btn {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.refresh-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.export-btn {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.export-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}
.pdf-btn {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.pdf-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
}
/* Main Content */
.dashboard-content {
    flex: 1;
    display: grid;
    grid-template-columns:300px 1fr ;
    gap: 0;
    /* max-width: 1400px; */
    margin: 0 auto;
    width: 100%;
}

/* Map Section */
.map-section {
    background: white;
    /* border-radius: 0 20px 20px 0; */
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    position: relative;
    flex: 1;
    transition: flex 0.3s ease;
}
.filters-section.collapsed + .map-section {
    flex: 1 1 100%; /* map takes full width when filters collapse */
}
.toggle-filters-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    margin-right: 10px;
}

.toggle-filters-btn i {
    transition: transform 0.3s ease;
}

.filters-section.collapsed .toggle-filters-btn i {
    transform: rotate(180deg);
}
.map-header {
    /* background: linear-gradient(135deg, #f8fafc, #e2e8f0); */
    /* padding: 1.5rem; */
    border-bottom: 1px solid #e2e8f0;
    position: absolute;
    z-index: 10;
    right:0px;

}

.map-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.view-toggles {
    display: flex;
    background: white;
    border-radius: 12px;
    padding: 0.25rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.map-toggle {
    padding: 0.75rem 1rem;
    border: none;
    background: transparent;
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.map-toggle.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.map-toggle:hover:not(.active) {
    background: #f1f5f9;
    color: #1e293b;
}

.map-actions {
    display: flex;
    gap: 0.5rem;
}

.map-action-btn {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 10px;
    background: white;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.map-action-btn:hover {
    background: #f8fafc;
    color: #1e293b;
    transform: translateY(-2px);
}

.map-container {
    position: relative;
    height: calc(110vh - 100px);
}

#tracking-map {
    width: 100%;
    height: 100%;
}

.map-overlay {
    /* position: absolute; */
    /* top: 1rem;
    left: 1rem; */
    /* background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    z-index: 10; */
}

.overlay-stats {
    display: flex;
    gap: 2rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.stat-label {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin-top: 0.25rem;
}

.map-legend {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    z-index: 10;
    min-width: 180px;
}

.legend-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.75rem;
}

.legend-items {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.75rem;
    color: #64748b;
}

.legend-marker {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.active-marker {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
}

.inactive-marker {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.3);
}


.trail-lineIncr {
    width: 20px;
    height: 3px;
    border-radius: 2px;
    background: linear-gradient(135deg, #0ef530, #106315ff);
    margin-top:10px;
}

.trail-lineProposed {
    width: 20px;
    height: 3px;
    border-radius: 2px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    margin-top:10px;
}


/* Filters Section */
/* .filters-section {
    background: white;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    max-height:710px;
    overflow-Y:auto;
    
} */
.filters-section {
    width: 300px; /* default width when expanded */
    transition: width 0.3s ease;
    overflow: hidden;
    background: white;
    display: flex;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);

    flex-direction: column;
}

.filters-section.collapsed {
    width: 0;
}


.filters-header {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.filters-title {
    font-size: 1.25rem;
    font-weight: 600;
}

.clear-filters-btn {
    padding: 0.5rem 1rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    font-size: 0.875rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.clear-filters-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

.filters-content {
    flex: 1;
    padding: 12px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}
.filter-container {
    border: 1px solid #ccc;      
    border-radius: 5px;         
}

.filter-header {
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f9f9f9;
    padding: 10px;
    font-weight: 600;
    border-bottom: 1px solid #ccc; 
}

.filter-body {
    /* display: none;                 */
    padding: 10px;
    background: #fff;
}


.filter-header.active .filter-arrow {
    transform: rotate(180deg); 
}


.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-label i {
    color: #667eea;
    width: 16px;
}

.filter-input, .filter-select {
    padding: 5px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.875rem;
    background: white;
    color: #1f2937;
    transition: all 0.3s ease;
}

.filter-input:focus, .filter-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
/* Style the multiselect button */
.multiselect {
    padding: 0.75rem !important;
    border: 2px solid #e5e7eb !important;
    border-radius: 10px !important;
    font-size: 0.875rem !important;
    background: white !important;
    color: #1f2937 !important;
    transition: all 0.3s ease;
    width: 100% !important; /* make it full width */
    text-align: left;
}

/* Focus state */
.multiselect:focus {
    outline: none;
    border-color: #667eea !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
}

/* Dropdown menu style */
.multiselect-container {
    max-height: 200px;   /* fixed height */
    overflow-y: auto;    /* enable vertical scrolling */
    overflow-x: hidden;  /* no horizontal scroll */
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 5px;
    font-size: 0.875rem;
}

.multiselect-item .input-group{
    background: white !important;
    color: #1f2937 !important;
    padding:2px;


}
/* Reset input-group-addon and input-group-btn widths */
.input-group-addon,
.input-group-btn {
    width: auto !important;      /* remove default 1% */
    flex: 0 0 auto !important;   /* if using flex layout */
    display: inline-block !important;
}

/* Optional: fix background and color */
.input-group-addon {
    background: white !important;
    color: #1f2937 !important;
    border: none !important;
}

/* Multiselect search input override */
.multiselect-container .multiselect-search {
    padding: 0.5rem 0.75rem !important;
    border: 2px solid #e5e7eb !important;
    border-radius: 10px !important;
    background: white !important;
    color: #1f2937 !important;
    font-size: 0.875rem !important;
}

/* Force override for dark mode */
@media (prefers-color-scheme: dark) {
  .multiselect-container .multiselect-search {
      background: white !important;
      border: 2px solid #e5e7eb !important;
      color: #1f2937 !important;
  }
}



/* Checkbox labels inside dropdown */
.multiselect-container > li > a > label {
    padding: 4px 8px;
}

.date-range-inputs {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.date-separator {
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
}

.employee-filter-container {
    position: relative;
}

.selected-employees {
    margin-top: 0.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.selected-employee {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.remove-employee {
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s ease;
}

.remove-employee:hover {
    opacity: 1;
}
.selected-tags {
    margin-top: 0.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tag-remove {
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s ease;
}

.tag-remove:hover {
    opacity: 1;
}

.layer-controls {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
/* .layer-row {
    display: flex;
    gap: 5px; 
    margin-bottom: 10px; 
    flex-wrap: wrap;
} */

.layer-toggle {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    font-size: 0.875rem;
    color: #374151;
}

.layer-toggle input[type="checkbox"] {
    display: none;
}

.toggle-slider {
    width: 40px;
    height: 20px;
    background: #e5e7eb;
    border-radius: 20px;
    position: relative;
    transition: all 0.3s ease;
}

.toggle-slider::before {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    background: white;
    border-radius: 50%;
    top: 2px;
    left: 2px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.layer-toggle input[type="checkbox"]:checked + .toggle-slider {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.layer-toggle input[type="checkbox"]:checked + .toggle-slider::before {
    transform: translateX(20px);
}

.filter-actions {
    margin-top: 1rem;
}

.apply-filters-btn {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.apply-filters-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* New */



.Icon-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.filter-section {
    margin-bottom: 1rem;
    border-bottom: 1px solid #333;
    padding-bottom: 0.5rem;
}

.section-header {
    font-weight: 600;
    font-size: 1rem;
    /* color: #fff; */
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    margin-bottom: 0.5rem;
}
.section-header .arrows {
    /* position: relative; */
    transition: transform 0.3s ease;
}

.section-header.active .arrows {
    transform: rotate(180deg);
}

.section-body {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding:8px;
}
/* 
.layer-row {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
} */

.layer-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: -6px;
}

.layer-title {
    display: flex;
    flex-direction: column;
    font-weight: 600;
}

.layer-title .count {
    font-size: 12px;
    color: #777;
    margin-top: 2px;
}

/* make sub-options a block row, not affected by flex */
.sub-options {
    /* margin-top: 6px;
    padding-left: 20px; */
    display: block;     /* reset from flex layout */
}
.sub-options .sub-toggle {
    display: flex;    
    margin-bottom: 4px;
}

/* .sub-toggle input[type="checkbox"] {
    display: none;
} */

.layer-row .checkbox {
    width: 16px;
    height: 16px;
    border: 2px solid #666;
    border-radius: 4px;
    display: inline-block;
    position: relative;
}

/* .sub-toggle input[type="checkbox"]:checked + .checkbox {
    background: #667eea;
    border-color: #667eea;
} */



/* Employee List */
.employee-list {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 300px;
}

.list-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.list-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.list-controls {
    display: flex;
    background: #f3f4f6;
    border-radius: 8px;
    padding: 0.25rem;
}

.list-control-btn {
    padding: 0.5rem 0.75rem;
    border: none;
    background: transparent;
    color: #6b7280;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.list-control-btn.active {
    background: white;
    color: #667eea;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.employee-items {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    overflow-y: auto;
}

.employee-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.employee-item:hover {
    background: #f1f5f9;
    border-color: #667eea;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.employee-item.active {
    border-color: #10b981;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
}

.employee-item.inactive {
    border-color: #ef4444;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
}

.employee-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.employee-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
}

.employee-info {
    flex: 1;
}

.employee-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.125rem;
}

.employee-role {
    font-size: 0.75rem;
    color: #6b7280;
}

.employee-status {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.status-active {
    background: #10b981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
}

.status-inactive {
    background: #ef4444;
    box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.3);
}

.employee-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.75rem;
    color: #6b7280;
}

.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    color: #6b7280;
    text-align: center;
}

.loading-state i {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #667eea;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .dashboard-content {
        grid-template-columns: 350px 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard-content {
        grid-template-columns: 1fr;
        grid-template-rows: 60vh auto;
    }
    
    .map-section, .filters-section {
        border-radius: 0;
    }
    
    .header-content {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .header-actions {
        justify-content: center;
    }
    
    .overlay-stats {
        flex-direction: column;
        gap: 1rem;
    }
}

@media (max-width: 576px) {
    .dashboard-header {
        padding: 1rem;
    }
    
    .filters-content {
        padding: 1rem;
    }
    
    .date-range-inputs {
        flex-direction: column;
        align-items: stretch;
    }
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.employee-item {
    animation: fadeIn 0.3s ease-out;
}

/* Scrollbar Styling */
.filters-content::-webkit-scrollbar,
.employee-items::-webkit-scrollbar {
    width: 6px;
}

.filters-content::-webkit-scrollbar-track,
.employee-items::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.filters-content::-webkit-scrollbar-thumb,
.employee-items::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 3px;
}

.filters-content::-webkit-scrollbar-thumb:hover,
.employee-items::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a67d8, #6b46c1);
}
.pdf-selection-controls {
    margin-top: 0.75rem;
    display: flex;
    gap: 0.5rem;
}

.select-all-btn, .select-none-btn {
    flex: 1;
    padding: 0.5rem 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    color: #374151;
    font-size: 0.75rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    transition: all 0.2s ease;
}

.select-all-btn:hover {
    background: #f3f4f6;
    border-color: #10b981;
    color: #10b981;
}

.select-none-btn:hover {
    background: #f3f4f6;
    border-color: #ef4444;
    color: #ef4444;
}

/* PDF Download Progress */
.pdf-progress {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    min-width: 300px;
    text-align: center;
}

.pdf-progress-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin: 1rem 0;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    width: 0%;
    transition: width 0.3s ease;
}


</style>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

<script>
let map;
let markers = {};
let gpMarkers = {};
let ontMarkers = {};
let cablePolylines = [];
let polylines = [];
let employeeData ={!! json_encode($data) !!};
let gpData ={!! json_encode($gpsPoints) !!}; 
let downGPCodes ={!! json_encode($downGPSCodes) !!}; 
let state_id ={!! json_encode($state_id) !!};
let filteredEmployees = [];
let cableData = [];
let ONTData=[]; 

// Role colors mapping
const roleColors = {
    1: '#3b82f6', // OFC - Blue
    2: '#10b981', // FRT - Green
    5: '#f59e0b', // Patroller - Yellow
    3: '#8b5cf6', // Zonal incharge - Purple
    4: '#ef4444'  // District incharge - Red
};

// Role names mapping
const roleNames = {
    1: 'OFC',
    2: 'FRT',
    5: 'Patroller',
    3: 'Zonal incharge',
    4: 'District incharge'
};

// Initialize map
function initializeMap() {
    map = new google.maps.Map(document.getElementById('tracking-map'), {
        zoom: 10,
        center: { lat: 20.8444, lng: 85.1511 },
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles: [
            {
                featureType: 'all',
                elementType: 'geometry.fill',
                stylers: [{ color: '#f8fafc' }]
            },
            {
                featureType: 'water',
                elementType: 'geometry',
                stylers: [{ color: '#e0f2fe' }]
            },
            {
                featureType: 'road',
                elementType: 'geometry',
                stylers: [{ color: '#ffffff' }]
            },
            {
                featureType: 'road',
                elementType: 'geometry.stroke',
                stylers: [{ color: '#e2e8f0' }]
            }
        ],
        zoomControl: true,
        mapTypeControl: false,
        scaleControl: true,
        streetViewControl: false,
        rotateControl: false,
        fullscreenControl: false
    });

    processEmployeeData();
    loadCableData();
    loadONTData();
    updateMap();
    // updateEmployeeList();
    updateStats();
}
// Process employee data from controller
function processEmployeeData() {
    const processedData = [];
    employeeData.forEach(item => {
        const provider = item.provider;
        const liveLocation = item.live_location;
        
        if (liveLocation && liveLocation.latitude && liveLocation.longitude) {
            processedData.push({
                id: provider.id,
                name: `${provider.first_name} ${provider.last_name}`,
                role: roleNames[provider.type] || 'Unknown',
                roleId:provider.type,
                AttendanceStatus:item.status,
                idle_now:item.idle_now,
                status: provider.attendance_status == 'active' ? "active" : 'inactive',
                lat: parseFloat(liveLocation.latitude),
                lng: parseFloat(liveLocation.longitude),
                lastUpdate: formatDateTime(liveLocation.datetime),
                district: provider.district_name,
                block: provider.blockname,
				Zone:provider.zone_name,
                address: liveLocation.address || 'Unknown Location',
                mobile:provider.mobile
            });
        }
    });
   
    
    employeeData = processedData;
    filteredEmployees = [...employeeData];
}

// Load cable data from external API
async function loadCableData() {
    try {
        const filters = getSelectedFilters();
        const st_name = state_id === 1 ? "WEST BENGAL" : "ANDAMAN"; 

        const dt_name = filters.districts.map(d => d.name).join(",");
        
        const blk_name = filters.blocks.map(b => b.name).join(",");
        const params = new URLSearchParams();
        params.append("st_name", st_name);
       if (dt_name.length > 0) {
            params.append("dist_name", dt_name);
        }
        if (blk_name.length > 0) {
            params.append("blk_name", blk_name);
        }
        const url = `https://api.tricadtrack.com/get-bsnl-cables?${params.toString()}`;
        const response = await fetch(url);
        const result = await response.json();
        if (result.data) {
            cableData = result.data;
            showCableNetwork();
            
        }
    } catch (error) {
        console.error('Error loading cable data:', error);
    }
}

async function loadONTData() {
    try {
        const filters = getSelectedFilters();
        const st_name = state_id === 1 ? "WEST BENGAL" : "ANDAMAN"; 

        const dt_name = filters.districts.map(d => d.name).join(",");
        
        const blk_name = filters.blocks.map(b => b.name).join(",");

        const url = `https://api.tricadtrack.com/gpslist?noPagination=true&type=OLT&st_name=${encodeURIComponent(st_name)}&dt_name=${encodeURIComponent(dt_name)}&blk_name=${encodeURIComponent(blk_name)}`;
        const response = await fetch(url);
        const result = await response.json();
         
        if (result.data && Array.isArray(result.data)) {
            ONTData = result.data;
            document.querySelectorAll('.olt-count').forEach(el => {
                el.textContent = ONTData.length;
            });

        } else {
            console.warn("No ONT data found");
        }
    } catch (error) {
        console.error("Error loading ONT data:", error);
    }
}


// Update map with employee locations
function updateMap() {
    // clearMap();
    // Show employees if enabled
    if (document.getElementById('show-employees').checked || document.getElementById('show-active-employees').checked 
        || document.getElementById('show-inactive-employees').checked  || document.getElementById('show-absent-employees').checked || document.getElementById('show-idle-employees').checked  ||  document.getElementById('show-di').checked  || document.getElementById('show-frt').checked || document.getElementById('show-patroller').checked) {
            
        showEmployeeMarkers();
    }else
     {
                // If disabled, ensure we clear them
                Object.values(markers).forEach(m => m.setMap(null));
                markers = {};
     }
    
    // Show GP status if enabled
    if (document.getElementById('show-gps').checked || document.getElementById('show-up-gps').checked || 
        document.getElementById('show-down-gps').checked) {
        showGPMarkers();
    }else {
                Object.values(gpMarkers).forEach(m => m.setMap(null));
                gpMarkers = {};
            }
    
    // Show cables if enabled
    if (document.getElementById('show-cables').checked || document.getElementById('show-inCable').checked || document.getElementById('show-PropCable').checked) {
        showCableNetwork();
    }
     if (document.getElementById('show-ont').checked) {
       
        showONTNetwork();
    } else {
                Object.values(ontMarkers).forEach(m => m.setMap(null));
                ontMarkers = {};
            }
    
    // Fit map to show all markers
    const allMarkers = [...Object.values(markers), ...Object.values(gpMarkers), ...Object.values(ontMarkers)];

    if (allMarkers.length > 0) {
        const bounds = new google.maps.LatLngBounds();
        allMarkers.forEach(marker => bounds.extend(marker.getPosition()));
        map.fitBounds(bounds);
    }
}

// Show employee markers
function showEmployeeMarkers() {
    let totalFRT = 0;
    let totalPatroller = 0;
    let totDI = 0;
    let currentIds = [];
    filteredEmployees.forEach(employee => {
         const showActive = document.getElementById('show-active-employees').checked;
         const showInactive = document.getElementById('show-inactive-employees').checked;
		 const showAbsent = document.getElementById('show-absent-employees').checked; 
		 const showIdle = document.getElementById('show-idle-employees').checked;
         const FRT = document.getElementById('show-frt').checked;
         const Patroller = document.getElementById('show-patroller').checked;
         const DistInchar = document.getElementById('show-di').checked;

            if (employee.roleId === 2) {  
                totalFRT++;
            }
            if (employee.roleId === 5) {   
                totalPatroller++;
            }
            if (employee.roleId === 4) {   
                totDI++;
            }

		if (employee.AttendanceStatus === 'present') {
			if (employee.status === 'active') {
				if (employee.idle_now) {
					if (!showIdle) return;
				} else {
					if (!showActive) return;
				}
			} else if (employee.status === 'inactive') {
				if (!showInactive) return;
			}
		} 
		else if (employee.AttendanceStatus === 'obsent') { 
			if (!showAbsent) return;
		}

        if (employee.roleId === 2 && !FRT) return;
        if (employee.roleId === 5 && !Patroller) return;
        if (employee.roleId === 4 && !DistInchar) return;

        currentIds.push(employee.id);

         const roleColor = roleColors[employee.roleId] || '#6b7280';
        let iconUrl;

        if (employee.roleId === 2) {
            iconUrl = {
                url: "https://maps.google.com/mapfiles/kml/shapes/cabs.png",
                scaledSize: new google.maps.Size(20, 20), 
            };
        } else if (employee.roleId === 5) {
            iconUrl = {
                url:  "https://maps.google.com/mapfiles/kml/shapes/motorcycling.png",
                scaledSize: new google.maps.Size(20, 20),
            };
        } else if (employee.roleId === 4) {
            iconUrl = {
                url:  "https://maps.google.com/mapfiles/kml/pal4/icon15.png",
                scaledSize: new google.maps.Size(20, 20),
            };
        }
         else {
            // Default circle
            iconUrl = {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 12,
                fillColor: roleColors[employee.roleId] || '#6b7280',
                fillOpacity: 1,
                strokeWeight: 3,
                strokeColor: '#ffffff'
            };
        }
        const content = `
                <div style="padding: 12px; min-width: 250px;">
                    <h3 style="margin: 0 0 8px 0; color: #1f2937; font-size: 16px;">${employee.name}</h3>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <span style="width: 12px; height: 12px; border-radius: 50%; background: ${roleColor};"></span>
                        <span style="font-size: 14px; color: #374151; font-weight: 500;">${employee.role}</span>
                    </div>
                    <p style="margin: 0 0 4px 0; color: #6b7280; font-size: 13px;"> <i class="fas fa-phone-alt" style="margin-right: 6px; color: #2563eb;"></i> ${employee.mobile}</p>
                    <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 12px;"> <i class="fas fa-clock" style="margin-right: 6px; color: #f59e0b;"></i> Last update: ${employee.lastUpdate}</p>
                    <div style="margin-top: 10px;">
                        <a href="{{ url('/admin/staff_details/${employee.id}') }}" target="_blank" 
                           style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 6px 12px; 
                                  border-radius: 6px; text-decoration: none; font-size: 12px;">
                            <i class="fas fa-history" style="margin-right: 4px;"></i>  View Profile
                        </a>
                    </div>
                </div>
            `;
            
                if (markers[employee.id]) {
                    // Update existing
                    const marker = markers[employee.id];
                    marker.setPosition({ lat: employee.lat, lng: employee.lng });
                    marker.setIcon(iconUrl);
                    // could update content if we stored infowindow ref
                } else {
                    // New marker
                    const marker = new google.maps.Marker({
                        position: { lat: employee.lat, lng: employee.lng },
                        map: map,
                        title: employee.name,
                        icon: iconUrl
                    });

                    const infoWindow = new google.maps.InfoWindow({
                        content: content
                    });

                    marker.addListener('click', () => {
                        infoWindow.open(map, marker);
                    });

                    markers[employee.id] = marker;
                }


      
    });
    
            // Remove markers not in current filter
            for (const id in markers) {
                if (!currentIds.includes(parseInt(id))) {
                    markers[id].setMap(null);
                    delete markers[id];
                }
            }

    document.getElementById('total-frt').textContent = `${totalFRT}`;
    document.getElementById('total-patroller').textContent = `${totalPatroller}`;
    document.getElementById('total-di').textContent = `${totDI}`;

    document.getElementById('Tot-EMP').textContent = `${totalFRT + totalPatroller + totDI}`;

}

// Show GP markers with up/down status
  function showGPMarkers() {
            const showUpGPs = document.getElementById('show-up-gps').checked;
            const showDownGPs = document.getElementById('show-down-gps').checked;
            let currentLgdCodes = [];

            gpData.forEach(gp => {
                if (gp.latitude && gp.longitude) {
                    const isDown = downGPCodes.includes(gp.lgd_code);
                    // Filter by UP/DOWN status
                    if (isDown && !showDownGPs) return;
                    if (!isDown && !showUpGPs) return;

                    currentLgdCodes.push(gp.lgd_code);

                    const statusColor = isDown ? '#ef4444' : '#10b981';
                    const statusText = isDown ? 'DOWN' : 'UP';
                    const icon = isDown ? "http://maps.google.com/mapfiles/ms/icons/red-dot.png" : "http://maps.google.com/mapfiles/ms/icons/green-dot.png";

                    if (gpMarkers[gp.lgd_code]) {
                        // Update existing
                        const marker = gpMarkers[gp.lgd_code];
                        // GPs likely don't move, but we can update if needed
                        marker.setPosition({ lat: parseFloat(gp.latitude), lng: parseFloat(gp.longitude) });
                    } else {
                        // New marker
                        const marker = new google.maps.Marker({
                            position: { lat: parseFloat(gp.latitude), lng: parseFloat(gp.longitude) },
                            map: map,
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
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: ${statusColor};"></span>
                                    <span style="font-size: 14px; color: ${statusColor}; font-weight: 600;">${statusText}</span>
                                </div>
                                <p style="margin: 0; color: #6b7280; font-size: 12px;">LGD Code: ${gp.lgd_code}</p>
                                <p style="margin: 0; color: #6b7280; font-size: 12px;">Petroller Name: ${gp.petroller}</p>
                                <p style="margin: 0; color: #6b7280; font-size: 12px;">Petroller Num: ${gp.petroller_contact_no}</p>
                                <p style="margin: 0; color: #6b7280; font-size: 12px;">FRT Name: ${gp.provider}</p>
                                <p style="margin: 0; color: #6b7280; font-size: 12px;">FRT Num: ${gp.contact_no}</p>
                                <div style="margin-top: 10px;">
                                            <a href="{{ url('/admin/tickets?searchinfo=${gp.lgd_code}') }}" target="_blank" 
                                            style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 6px 12px; 
                                                    border-radius: 6px; text-decoration: none; font-size: 12px;">
                                                View
                                            </a>
                                    </div>

                            </div>
                        `
                        });

                        marker.addListener('click', () => {
                            infoWindow.open(map, marker);
                        });

                        gpMarkers[gp.lgd_code] = marker;
                    }
                }
            });

            // Remove filtered out markers
            for (const code in gpMarkers) {
                if (!currentLgdCodes.includes(parseInt(code)) && !currentLgdCodes.includes(code)) { // check both for safety
                    gpMarkers[code].setMap(null);
                    delete gpMarkers[code];
                }
            }
        }


function showCableNetwork() {
    const infoWindow = new google.maps.InfoWindow();
    const showcable = document.getElementById('show-cables').checked;
    const showInCable = document.getElementById('show-inCable')?.checked ?? false;
    const showPropCable = document.getElementById('show-PropCable')?.checked ?? false;

    // Reset counts
    let inCableTotal = 0;
    let propCableTotal = 0;

    // Clear previous polylines
    cablePolylines.forEach(line => line.setMap(null));
    cablePolylines = [];

    if (!showcable) {
        document.getElementById('total-inCable').textContent = '0 km';
        document.getElementById('total-PropCable').textContent = '0 km';
        return;
    }

    cableData.forEach(cableFile => {
        const polylines = cableFile.parsed_data?.polylines;
        if (!polylines || !Array.isArray(polylines)) return;

        polylines.forEach(polyline => {
            const coords = polyline.coordinates;
            if (!Array.isArray(coords)) return;

            const distance = polyline.distance || 0;

            // Filter and accumulate counts
            if (polyline.properties.type === 'Block to FPOI Cable') {
                propCableTotal += distance;
                if (!showPropCable) return;
            } else {
                inCableTotal += distance;
                if (!showInCable) return;
            }

            // Draw polyline
            const path = coords.map(coord => ({ lat: coord[1], lng: coord[0] }));
            const line = new google.maps.Polyline({
                path,
                geodesic: true,
                strokeColor: polyline.properties.type === 'Block to FPOI Cable' ? '#0000FF' : '#0ef530',
                strokeOpacity: 0.8,
                strokeWeight: 3
            });
            line.setMap(map);
            cablePolylines.push(line);

            // Click listener
            line.addListener('click', (event) => {
                const props = polyline.properties;
                let content = '<div style="max-width:250px;"><h3>' + (props.name || 'Cable Info') + '</h3><ul>';
                for (let key in props) {
                    if (props[key] !== "NULL" && props[key] !== null) {
                        content += `<li><strong>${key}:</strong> ${props[key]}</li>`;
                    }
                }
                content += '</ul></div>';
                infoWindow.setContent(content);
                infoWindow.setPosition(event.latLng);
                infoWindow.open(map);
            });
        });
    });

    document.getElementById('total-inCable').textContent = inCableTotal.toFixed(2) + ' km';
    document.getElementById('total-PropCable').textContent = propCableTotal.toFixed(2) + ' km';
    document.getElementById('cable-count').textContent = (inCableTotal + propCableTotal).toFixed(2) + ' km';
}


// Show OLT network
 function showONTNetwork() {
            let currentIds = [];
            ONTData.forEach(point => {
                let id = point.id || `${point.lattitude}_${point.longitude}`;
                currentIds.push(id);

                let iconColor = '#dc2626';
                const position = {
                    lat: parseFloat(point.lattitude),
                    lng: parseFloat(point.longitude)
                };
                const icon = {
                    url: "https://maps.google.com/mapfiles/kml/shapes/homegardenbusiness.png", // built-in home icon
                    scaledSize: new google.maps.Size(20, 20)
                };

                const content = `
                                <div style="padding: 8px;">
                                    <h4 style="margin: 0 0 4px 0; color: #1f2937;">${point.name}</h4>
                                    <p style="margin: 0; color: #6b7280; font-size: 12px;">Type: ${point.type}</p>
                                    <p style="margin: 0; color: #6b7280; font-size: 12px;">LGD Code: ${point.lgd_code}</p>
                                    <p style="margin: 0; color: #6b7280; font-size: 12px;">State: ${point.st_name}</p>
                                    <p style="margin: 0; color: #6b7280; font-size: 12px;">District: ${point.dt_name}</p>
                                    <p style="margin: 0; color: #6b7280; font-size: 12px;">Block: ${point.blk_name}</p>

                                </div>
                            `;

                if (ontMarkers[id]) {
                    // Update existing
                    const marker = ontMarkers[id];
                    marker.setPosition(position);
                    // marker.setIcon(icon); // Icon likely static
                } else {
                    // New Marker
                    const marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: point.name,
                        icon: icon
                    });

                    const infoWindow = new google.maps.InfoWindow({
                        content: content
                    });

                    marker.addListener('click', () => {
                        infoWindow.open(map, marker);
                    });

                    ontMarkers[id] = marker;
                }
            });

            // Clean up
            for (const id in ontMarkers) {
                if (!currentIds.includes(id) && !currentIds.includes(parseInt(id))) {
                    ontMarkers[id].setMap(null);
                    delete ontMarkers[id];
                }
            }
        }



// Update statistics
function updateStats() {
    // const activeCount = filteredEmployees.length;
    // document.getElementById('active-count').textContent = activeCount;
    const upGPs = gpData.length - downGPCodes.length;
    const downGPs = downGPCodes.length;
    // document.getElementById('total-distance').textContent = `${upGPs} UP / ${downGPs} DOWN`;
    document.getElementById('total-upgp').textContent = `${upGPs}`;
    document.getElementById('total-downgp').textContent = `${downGPs}`;
    document.getElementById('Tot-GP').textContent =`${upGPs + downGPs}`;

    

}

// Focus on specific employee
function focusOnEmployee(employeeId) {
    const employee = employeeData.find(emp => emp.id === employeeId);
    if (employee) {
        map.setCenter({ lat: employee.lat, lng: employee.lng });
        map.setZoom(15);
        
        // Find and click the marker
        const marker = markers.find(m => m.getTitle() === employee.name);
        if (marker) {
            google.maps.event.trigger(marker, 'click');
        }
    }
}

async function downloadSelectedPDFs() { 
  
    const showActive   = document.getElementById('show-active-employees').checked;
    const showInactive = document.getElementById('show-inactive-employees').checked;
    const showAbsent = document.getElementById('show-absent-employees').checked; 
	const showIdle = document.getElementById('show-idle-employees').checked;

    const FRT          = document.getElementById('show-frt').checked;
    const Patroller    = document.getElementById('show-patroller').checked;
    const DistInchar = document.getElementById('show-di').checked;

    const filteredForDownload = filteredEmployees.filter(employee => {
        // if (employee.status === 'active' && !showActive) return false;
        // if (employee.status === 'inactive' && !showInactive) return false;
      
        if (employee.AttendanceStatus === 'present') {
			if (employee.status === 'active') {
				if (employee.idle_now) {
					if (!showIdle) return false;
				} else {
					if (!showActive) return false;
				}
			} else if (employee.status === 'inactive') {
				if (!showInactive) return false;
			}
		} 
		else if (employee.AttendanceStatus === 'obsent') { 
			if (!showAbsent) return false;
		}
        if (employee.roleId === 2 && !FRT) return false;
        if (employee.roleId === 5 && !Patroller) return false;
        if (employee.roleId === 4 && !DistInchar) return false;
        return true;

    });

    if (filteredForDownload.length === 0) {
        alert('No employees match the selected filters.');
        return;
    }

   
    showProgressModal();
    
    const totalEmployees = filteredForDownload.length;
    let completedCount = 0;
    
    for (const employee of filteredForDownload) {
        try {
            updateProgress(completedCount, totalEmployees, 
                `Generating PDF for ${employee.name} (${completedCount + 1} of ${totalEmployees})...`
            );
            const fromDate = document.getElementById('from-date').value; 
            const toDate = document.getElementById('to-date').value;
            let baseUrl = "{{ url('/admin/employee-pdf-report') }}";
           
            const response =await fetch(`${baseUrl}/${employee.id}/${fromDate}/${toDate}`, {
                method: 'GET',
                headers: { 'Accept': 'application/pdf' }
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `${employee.name.replace(/\s+/g, '_')}_Report.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                await new Promise(resolve => setTimeout(resolve, 500)); // delay
            }

            completedCount++;
        } catch (error) {
            console.error(`Error downloading PDF for ${employee.id}:`, error);
            completedCount++;
        }
    }

    updateProgress(completedCount, totalEmployees, 'All PDFs downloaded successfully!');
    setTimeout(() => hideProgressModal(), 2000);
}

// Show progress modal
function showProgressModal() {
    const overlay = document.createElement('div');
    overlay.className = 'pdf-progress-overlay';
    overlay.id = 'pdf-progress-overlay';
    
    const modal = document.createElement('div');
    modal.className = 'pdf-progress';
    modal.id = 'pdf-progress-modal';
    modal.innerHTML = `
        <div style="margin-bottom: 1rem;">
            <i class="fas fa-file-pdf" style="font-size: 2rem; color: #dc2626; margin-bottom: 1rem;"></i>
            <h3 style="margin: 0 0 0.5rem 0; color: #1f2937;">Generating PDF Reports</h3>
            <p id="progress-text" style="margin: 0; color: #6b7280; font-size: 0.875rem;">Preparing...</p>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" id="progress-fill"></div>
        </div>
        <p id="progress-percentage" style="margin: 0; color: #374151; font-weight: 600;">0%</p>
    `;
    
    document.body.appendChild(overlay);
    document.body.appendChild(modal);
}

// Update progress
function updateProgress(completed, total, message) {
    const percentage = Math.round((completed / total) * 100);
    
    const progressText = document.getElementById('progress-text');
    const progressFill = document.getElementById('progress-fill');
    const progressPercentage = document.getElementById('progress-percentage');
    
    if (progressText) progressText.textContent = message;
    if (progressFill) progressFill.style.width = `${percentage}%`;
    if (progressPercentage) progressPercentage.textContent = `${percentage}%`;
}

// Hide progress modal
function hideProgressModal() {
    const overlay = document.getElementById('pdf-progress-overlay');
    const modal = document.getElementById('pdf-progress-modal');
    
    if (overlay) document.body.removeChild(overlay);
    if (modal) document.body.removeChild(modal);
}

// Clear map
  function clearMap() {
    Object.values(markers).forEach(marker => marker.setMap(null));
    Object.values(gpMarkers).forEach(marker => marker.setMap(null));
    cablePolylines.forEach(polyline => polyline.setMap(null));
    polylines.forEach(polyline => polyline.setMap(null));
    markers = {};
    gpMarkers = {};
    ontMarkers = {};
    cablePolylines = [];
    polylines = [];
}

// Format date time
function formatDateTime(datetime) {
    const date = new Date(datetime);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} mins ago`;
    if (diffMins < 1440) return `${Math.floor(diffMins / 60)} hours ago`;
    return date.toLocaleDateString();
}

function clearAllFilters() {
    // List of filter select IDs
    const selects = ['district-filter', 'block-filter', 'gp-filter', 'employee-filter'];

    // Reset selects & tags
    selects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (select) {
            Array.from(select.options).forEach(option => option.selected = false);
        }
        const tagsContainer = document.getElementById(selectId.replace('-filter', '-tags'));
        if (tagsContainer) tagsContainer.innerHTML = '';
    });

    window.location.href = window.location.pathname;
}


// Refresh data
function refreshData() {
    const refreshBtn = document.querySelector('.refresh-btn i');
    refreshBtn.classList.add('fa-spin');
    
    setTimeout(() => {
        location.reload(); 
        refreshBtn.classList.remove('fa-spin');
    }, 500);
}

// Center map
function centerMap() {
    if (markers.length > 0) {
        const bounds = new google.maps.LatLngBounds();
        markers.forEach(marker => bounds.extend(marker.getPosition()));
        map.fitBounds(bounds);
    }
}

// Toggle fullscreen
function toggleFullscreen() {
    const mapSection = document.querySelector('.map-section');
    const fullscreenBtn = document.querySelector('.map-action-btn i.fa-expand, .map-action-btn i.fa-compress');
    
    if (!document.fullscreenElement) {
        mapSection.requestFullscreen().then(() => {
            fullscreenBtn.classList.remove('fa-expand');
            fullscreenBtn.classList.add('fa-compress');
        });
    } else {
        document.exitFullscreen().then(() => {
            fullscreenBtn.classList.remove('fa-compress');
            fullscreenBtn.classList.add('fa-expand');
        });
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // initMultiSelect();
    
    // Map type toggles
    document.querySelectorAll('.map-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.map-toggle').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.dataset.view;
            if (map) {
                switch(view) {
                    case 'satellite':
                        map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
                        break;
                    case 'terrain':
                        map.setMapTypeId(google.maps.MapTypeId.TERRAIN);
                        break;
                    default:
                        map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
                }
            }
        });
    });
    
    // Employee list filter buttons
    document.querySelectorAll('.list-control-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.list-control-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            if (filter === 'all') {
                filteredEmployees = [...employeeData];
            } else {
                filteredEmployees = employeeData.filter(emp => emp.status === filter);
            }
            
            updateMap();
            // updateEmployeeList();
            updateStats();
        });
    });
    
    // Layer toggle listeners
    ['show-employees', 'show-gps', 'show-cables','show-inCable','show-PropCable','show-ont','show-frt','show-patroller','show-di','show-active-employees','show-inactive-employees','show-absent-employees','show-idle-employees','show-up-gps','show-down-gps'].forEach(id => {
        document.getElementById(id).addEventListener('change', function() {
        if (id === 'show-employees') {
            if (!this.checked) {
                document.getElementById('show-active-employees').checked = false;
                document.getElementById('show-inactive-employees').checked = false;
                document.getElementById('show-absent-employees').checked = false;
                document.getElementById('show-idle-employees').checked = false;

                document.getElementById('show-frt').checked = false;
                document.getElementById('show-patroller').checked = false;
                document.getElementById('show-di').checked = false;

            }
        } 
        
        if (id === 'show-active-employees' || id === 'show-inactive-employees' || id === 'show-absent-employees' || id === 'show-idle-employees') {
            if (this.checked) {
                document.getElementById('show-employees').checked = true;
            }
        }
         if (id === 'how-frt' || id === 'show-patroller' || id === 'show-di') {
            if (this.checked) {
                document.getElementById('show-employees').checked = true;
            }
        }
          if (id === 'show-gps') {
            if (!this.checked) {
                document.getElementById('show-up-gps').checked = false;
                document.getElementById('show-down-gps').checked = false;
            }
        } 
        
        if (id === 'show-up-gps' || id === 'show-down-gps') {
            if (this.checked) {
                document.getElementById('show-gps').checked = true;
            }
        }
            updateMap();
        });

    });
    

     $('#district-filter, #block-filter, #gp-filter, #employee-filter').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        buttonWidth: '100%',
        nonSelectedText: 'Select options',
        allSelectedText: 'All selected',
        nSelectedText: 'selected'
    });


  
});

function getSelectedFilters() {
    const districts = $('#district-filter option:selected').map(function () {
        return { id: $(this).val(), name: $(this).text().trim() };
    }).get();

    const blocks = $('#block-filter option:selected').map(function () {
        return { id: $(this).val(), name: $(this).text().trim() };
    }).get();

    const gps = $('#gp-filter option:selected').map(function () {
        return { id: $(this).val(), name: $(this).text().trim() };
    }).get();

    const employees = $('#employee-filter option:selected').map(function () {
        return { id: $(this).val(), name: $(this).text().trim() };
    }).get();

    return { districts, blocks, gps, employees };
}


function toggleSection(id, header) {
    const section = document.getElementById(id);
    const isOpen = window.getComputedStyle(section).display !== "none";

    if (isOpen) {
        section.style.display = "none";
        header.classList.remove("active");
    } else {
        section.style.display = "flex"; // or block if that's your layout
        header.classList.add("active");
    }
}
 
function toggleFilterSection() {
    const section = document.getElementById('filterSection');
    const header = section.previousElementSibling; // the filter-header

    if (section.style.display === "none" || section.style.display === "") {
        section.style.display = "block";
        header.classList.add("active");
    } else {
        section.style.display = "none";
        header.classList.remove("active");
    }
}

// Global function for Google Maps callback
function initMap() {
    initializeMap();
}
 if (typeof google !== 'undefined') {
        initMap();
    } else {
        window.addEventListener('load', initMap);
    }



</script>
@endsection