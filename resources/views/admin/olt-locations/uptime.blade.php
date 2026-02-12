{{-- ONT Uptime Dashboard --}}
@extends('admin.layout.base')

@section('title', 'ONT Uptime Management')

@section('content')
<div class="terrasoft-main-content">
    <div class="terrasoft-page-container">
        {{-- Page Header --}}
        <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-bar-chart text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">Dashboard Management</h1>
                        <p class="terrasoft-page-subtitle">Monitor ONT, OLT, and SAMRIDDH performance data</p>
                    </div>
                </div>
                @if(auth()->user()->role == 'admin' || auth()->user()->role == 'zone_admin')

                <div class="terrasoft-header-actions">
                    <button class="terrasoft-btn terrasoft-btn-success" id="uploadCsvBtn">
                        <i class="ti-upload"></i>
                        Upload CSV
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- Main Tab Navigation --}}
        <div class="terrasoft-tab-container">
            <div class="terrasoft-tab-nav">
                <button class="terrasoft-tab-btn active" data-tab="Ontdashboard">
                    <i class="ti-bar-chart"></i>
                    ONT
                </button>
                <button class="terrasoft-tab-btn" data-tab="Oltdashboard">
                    <i class="ti-trending-up"></i>
                    OLT
                </button>
                <button class="terrasoft-tab-btn" data-tab="Samriddhdashboard">
                    <i class="ti-pie-chart"></i>
                    SAMRIDDH
                </button>
            </div>

            {{-- ONT Dashboard Tab Content --}}
            <div class="terrasoft-tab-content active" id="Ontdashboard-tab">
                {{-- Date Filter Section --}}
                <div class="terrasoft-filter-section">
                    <div class="terrasoft-date-filters">
                        <div class="terrasoft-date-group">
                            <label class="terrasoft-date-label">Month</label>
                            <input type="month" name="month" id="month"
                                 value="{{ @Request::get('month') }}" class="terrasoft-date-input">
                        </div>
                        <div class="terrasoft-date-group">
                            <label class="terrasoft-date-label">From Date</label>
                            <input type="date" class="terrasoft-date-input" id="from_date" name="fromDate" value="{{ @Request::get('fromDate') }}">
                        </div>
                        <div class="terrasoft-date-group">
                            <label class="terrasoft-date-label">To Date</label>
                            <input type="date" class="terrasoft-date-input" id="to_date" name="toDate" value="{{ @Request::get('toDate') }}">
                        </div>
                        <button class="terrasoft-btn terrasoft-btn-primary" id="applyFilters">
                            <i class="ti-filter"></i>
                            Apply
                        </button>
                    </div>
                </div>

                {{-- Data Table Tabs --}}
                <div class="terrasoft-data-tab-container">
                    <div class="terrasoft-data-tab-nav">
                        <button class="terrasoft-data-tab-btn active" data-data-tab="ont-data">
                            <i class="ti-database"></i>
                            Dashboard
                          
                        </button>
                        <button class="terrasoft-data-tab-btn" data-data-tab="uptime-data">
                            <i class="ti-clock"></i>
                            Uptime Data
                          
                        </button>
                    </div>

                    {{-- Dashboard Data Table --}}
                    <!-- <div class="terrasoft-data-tab-content active" id="ont-data-tab">
                        <div class="terrasoft-loading" id="loadingIndicator" style="display: none;">
                            <div class="terrasoft-spinner"></div>
                            <span>Loading data...</span>
                        </div>
                        <div id="dataTableContainer">
                        </div>
                    </div> -->
                    <div class="terrasoft-data-tab-content active" id="ont-data-tab">
                        <div id="dataTableContainer" style="position: relative;">
                            <!-- Dynamic table content will be loaded here -->

                            <!-- Loading overlay inside table container -->
                            <div class="terrasoft-loading-overlay" id="loadingIndicator" style="display: none;">
                                <div class="terrasoft-spinner"></div>
                                <span>Loading data...</span>
                            </div>
                        </div>
                    </div>


                    {{-- Uptime Data Table --}}
                    <div class="terrasoft-data-tab-content" id="uptime-data-tab">
                        <div class="terrasoft-loading" id="loadingIndicatorUptime" style="display: none;">
                            <div class="terrasoft-spinner"></div>
                            <span>Loading uptime data...</span>
                        </div>
                        <div id="uptimeTableContainer">
                        </div>
                        
                    </div>
                </div>
            </div>

            {{-- OLT Dashboard Tab Content --}}
            <div class="terrasoft-tab-content" id="Oltdashboard-tab">
                {{-- Date Filter Section --}}
                <div class="terrasoft-filter-section">
                    <div class="terrasoft-date-filters">
                        <div class="terrasoft-date-group">
                            <label class="terrasoft-date-label">Month</label>
                            <input type="month" name="olt_month" id="olt_month" class="terrasoft-date-input">
                        </div>
                        <div class="terrasoft-date-group">
                            <label class="terrasoft-date-label">From Date</label>
                            <input type="date" class="terrasoft-date-input" id="olt_from_date" name="oltFromDate">
                        </div>
                        <div class="terrasoft-date-group">
                            <label class="terrasoft-date-label">To Date</label>
                            <input type="date" class="terrasoft-date-input" id="olt_to_date" name="oltToDate">
                        </div>
                        <button class="terrasoft-btn terrasoft-btn-primary" id="applyOltFilters">
                            <i class="ti-filter"></i>
                            Apply
                        </button>
                    </div>
                </div>

                {{-- OLT Data Table Tabs --}}
                <div class="terrasoft-data-tab-container">
                    <div class="terrasoft-data-tab-nav">
                        <button class="terrasoft-data-tab-btn active" data-data-tab="olt-dashboard">
                            <i class="ti-database"></i>
                            Dashboard
                           
                        </button>
                        <button class="terrasoft-data-tab-btn" data-data-tab="olt-performance">
                            <i class="ti-activity"></i>
                            Performance Data
                            
                        </button>
                    </div>

                    <div class="terrasoft-data-tab-content active" id="olt-dashboard-tab">
                        <div class="terrasoft-loading" id="loadingIndicatorOlt" style="display: none;">
                            <div class="terrasoft-spinner"></div>
                            <span>Loading OLT data...</span>
                        </div>
                        <div id="oltTableContainer">
                        </div>
                    </div>

                    <div class="terrasoft-data-tab-content" id="olt-performance-tab">
                        <div class="terrasoft-loading" id="loadingIndicatorOltPerf" style="display: none;">
                            <div class="terrasoft-spinner"></div>
                            <span>Loading OLT performance data...</span>
                        </div>
                        <div id="oltPerfTableContainer">
                        </div>
                    </div>
                </div>
            </div>

            {{-- SAMRIDDH Dashboard Tab Content --}}
            <div class="terrasoft-tab-content" id="Samriddhdashboard-tab">
                {{-- Date Filter Section --}}
                <div class="terrasoft-filter-section">
                    <div class="terrasoft-date-filters">
                        <div class="terrasoft-date-group">
                            <label class="terrasoft-date-label">Month</label>
                            <input type="month" name="samriddh_month" id="samriddh_month" class="terrasoft-date-input">
                        </div>
                        <div class="terrasoft-date-group">
                            <label class="terrasoft-date-label">From Date</label>
                            <input type="date" class="terrasoft-date-input" id="samriddh_from_date" name="samriddhFromDate">
                        </div>
                        <div class="terrasoft-date-group">
                            <label class="terrasoft-date-label">To Date</label>
                            <input type="date" class="terrasoft-date-input" id="samriddh_to_date" name="samriddhToDate">
                        </div>
                        <button class="terrasoft-btn terrasoft-btn-primary" id="applySamriddhFilters">
                            <i class="ti-filter"></i>
                            Apply
                        </button>
                    </div>
                </div>

                {{-- SAMRIDDH Data Table Tabs --}}
                <div class="terrasoft-data-tab-container">
                    <div class="terrasoft-data-tab-nav">
                        <button class="terrasoft-data-tab-btn active" data-data-tab="samriddh-dashboard">
                            <i class="ti-database"></i>
                            Dashboard
                         
                        </button>
                        <button class="terrasoft-data-tab-btn" data-data-tab="samriddh-analytics">
                            <i class="ti-pie-chart"></i>
                            Analytics Data
                            
                        </button>
                    </div>

                    <div class="terrasoft-data-tab-content active" id="samriddh-dashboard-tab">
                        <div class="terrasoft-loading" id="loadingIndicatorSamriddh" style="display: none;">
                            <div class="terrasoft-spinner"></div>
                            <span>Loading SAMRIDDH data...</span>
                        </div>
                        <div id="samriddhTableContainer">
                        </div>
                    </div>

                    <div class="terrasoft-data-tab-content" id="samriddh-analytics-tab">
                        <div class="terrasoft-loading" id="loadingIndicatorSamriddhAnalytics" style="display: none;">
                            <div class="terrasoft-spinner"></div>
                            <span>Loading SAMRIDDH analytics data...</span>
                        </div>
                        <div id="samriddhAnalyticsTableContainer">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSV Upload Modal --}}
<div id="csvUploadModal" class="terrasoft-modal">
    <div class="terrasoft-modal-content">
        <div class="terrasoft-modal-header">
            <h3 id="uploadModalTitle">Upload CSV Data</h3>
            <button class="terrasoft-modal-close" onclick="closeCsvModal()">
                <i class="ti-x"></i>
            </button>
        </div>
        <div class="terrasoft-modal-body">
            <div class="terrasoft-upload-area" id="uploadArea">
                <div class="terrasoft-upload-icon">
                    <i class="ti-upload"></i>
                </div>
                <div class="terrasoft-upload-text">
                    <h4>Drop your CSV file here or click to browse</h4>
                    <p>Supported format: .csv (Max size: 10MB)</p>
                </div>
                <input type="file" id="csvFileInput" accept=".csv" style="display: none;">
            </div>
            <div class="terrasoft-upload-progress" id="uploadProgress" style="display: none;">
                <div class="terrasoft-progress-bar">
                    <div class="terrasoft-progress-fill" id="progressFill"></div>
                </div>
                <div class="terrasoft-progress-text" id="progressText">Preparing upload...</div>
            </div>
        </div>
        <div class="terrasoft-modal-footer">
            <button class="terrasoft-btn terrasoft-btn-secondary" onclick="closeCsvModal()">Cancel</button>
            <button class="terrasoft-btn terrasoft-btn-primary" id="uploadBtn" onclick="uploadCsv()" disabled>
                <i class="ti-upload"></i>
                Upload
            </button>
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('/css/olt.css')}}">

<style>
/* Main Tab Navigation */
.terrasoft-tab-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.terrasoft-tab-nav {
    display: flex;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.terrasoft-tab-btn {
    flex: 1;
    padding: 16px 24px;
    background: none;
    border: none;
    font-size: 14px;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-bottom: 3px solid transparent;
}

.terrasoft-tab-btn:hover {
    background: #f1f5f9;
    color: #3b82f6;
}

.terrasoft-tab-btn.active {
    background: white;
    color: #3b82f6;
    border-bottom-color: #3b82f6;
}

.terrasoft-tab-content {
    display: none;
    padding: 24px;
}

.terrasoft-tab-content.active {
    display: block;
}

/* Filter Section */
.terrasoft-filter-section {
    background: #f8fafc;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    border: 1px solid #e2e8f0;
}

.terrasoft-date-filters {
    display: flex;
    gap: 16px;
    align-items: end;
    flex-wrap: wrap;
}

.terrasoft-date-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.terrasoft-date-label {
    font-size: 12px;
    font-weight: 500;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.terrasoft-date-input {
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    background: white;
    min-width: 140px;
}

.terrasoft-date-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Data Table Tabs */
.terrasoft-data-tab-container {
    background: white;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.terrasoft-data-tab-nav {
    display: flex;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.terrasoft-data-tab-btn {
    flex: 1;
    padding: 12px 20px;
    background: none;
    border: none;
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-bottom: 2px solid transparent;
    position: relative;
}

.terrasoft-data-tab-btn:hover {
    background: #f1f5f9;
    color: #3b82f6;
}

.terrasoft-data-tab-btn.active {
    background: white;
    color: #3b82f6;
    border-bottom-color: #3b82f6;
}

.terrasoft-upload-mini {
    background: #22c55e;
    border: none;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-left: 8px;
}

.terrasoft-upload-mini:hover {
    background: #16a34a;
    transform: scale(1.1);
}

.terrasoft-data-tab-content {
    display: none;
}

.terrasoft-data-tab-content.active {
    display: block;
}

/* Export Buttons */
.terrasoft-export-buttons {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.terrasoft-export-btn {
    padding: 8px 16px;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-right: 8px;
}

.terrasoft-export-btn:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.terrasoft-search-box {
    display: flex;
    align-items: center;
    gap: 8px;
}

.terrasoft-search-box label {
    font-size: 12px;
    font-weight: 500;
    color: #64748b;
}

.terrasoft-search-input {
    padding: 6px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 12px;
    width: 200px;
}

.terrasoft-search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Tabular Table Styles */
.terrasoft-tabular-table {
    font-size: 12px;
}

.terrasoft-tabular-table th {
    background: #f8fafc;
    padding: 12px 8px;
    font-weight: 600;
    color: #374151;
    border: 1px solid #e5e7eb;
    cursor: pointer;
    user-select: none;
}

.terrasoft-tabular-table th:hover {
    background: #f1f5f9;
}

.terrasoft-tabular-table th i {
    margin-left: 4px;
    font-size: 10px;
    opacity: 0.5;
}

.terrasoft-tabular-row {
    border-bottom: 1px solid #f1f5f9;
}

.terrasoft-tabular-row:hover {
    background: #f8fafc;
}

.terrasoft-tabular-table td {
    padding: 10px 8px;
    border: 1px solid #f1f5f9;
    vertical-align: middle;
}

.terrasoft-td-text {
    color: #374151;
}

.terrasoft-td-number {
    text-align: center;
    font-weight: 500;
    color: #64748b;
}

.terrasoft-td-percentage {
    text-align: center;
    font-weight: 600;
    color: #059669;
}

.terrasoft-td-date {
    text-align: center;
    color: #64748b;
    font-size: 11px;
}

/* Pagination Styles */
.terrasoft-pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

.terrasoft-pagination-info {
    font-size: 12px;
    color: #64748b;
}

.terrasoft-pagination {
    display: flex;
    gap: 4px;
    align-items: center;
}

.terrasoft-pagination-btn {
    padding: 6px 12px;
    border: 1px solid #d1d5db;
    background: white;
    color: #374151;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 4px;
}

.terrasoft-pagination-btn:hover:not(.disabled) {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.terrasoft-pagination-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.terrasoft-pagination-btn.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* Data Table */
.terrasoft-table-container {
    overflow: hidden;
}

.terrasoft-table-wrapper {
    overflow-x: auto;
}

.terrasoft-data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.terrasoft-data-table th {
    background: #f8fafc;
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
}

.terrasoft-th-fixed {
    width: 80px;
    text-align: center;
}

.terrasoft-th-description {
    min-width: 250px;
}

.terrasoft-th-date {
    width: 100px;
    text-align: center;
}

.terrasoft-th-average {
    width: 100px;
    text-align: center;
    background: #eff6ff;
    color: #1e40af;
}

.terrasoft-data-row {
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.2s ease;
}

.terrasoft-data-row:hover {
    background: #f8fafc;
}

.terrasoft-data-table td {
    padding: 12px 16px;
    vertical-align: middle;
}

.terrasoft-td-number {
    text-align: center;
    font-weight: 500;
    color: #64748b;
}

.terrasoft-td-description {
    font-weight: 500;
    color: #1e293b;
}

.terrasoft-td-value {
    text-align: center;
    font-weight: 500;
    color: #374151;
}

.terrasoft-td-average {
    text-align: center;
    font-weight: 600;
    color: #1e40af;
    background: #f0f9ff;
}

.terrasoft-total-row {
    background: #f8fafc;
    border-top: 2px solid #e2e8f0;
}

.terrasoft-td-total {
    font-weight: 600;
    color: #1e293b;
    text-align: center;
}

.terrasoft-td-total-value {
    text-align: center;
    font-weight: 600;
    color: #1e293b;
}

.terrasoft-percentage-row {
    background: #eff6ff;
    border-top: 1px solid #bfdbfe;
}

.terrasoft-td-percentage {
    font-weight: 600;
    color: #1e40af;
    text-align: center;
}

.terrasoft-td-percentage-value {
    text-align: center;
    font-weight: 600;
    color: #1e40af;
}

/* Performance Dashboard */
.terrasoft-performance-dashboard {
    padding: 20px 0;
}

.terrasoft-metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.terrasoft-metric-card {
    background: white;
    padding: 24px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.terrasoft-metric-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.terrasoft-metric-header h3 {
    font-size: 14px;
    font-weight: 500;
    color: #64748b;
    margin: 0;
}

.terrasoft-metric-header i {
    font-size: 20px;
    color: #3b82f6;
}

.terrasoft-metric-value {
    font-size: 32px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
}

.terrasoft-metric-change {
    font-size: 12px;
    font-weight: 500;
    padding: 4px 8px;
    border-radius: 4px;
    display: inline-block;
}

.terrasoft-metric-change.positive {
    background: #dcfce7;
    color: #166534;
}

.terrasoft-metric-change.negative {
    background: #fee2e2;
    color: #991b1b;
}

/* Analytics Dashboard */
.terrasoft-analytics-dashboard {
    padding: 20px 0;
}

.terrasoft-chart-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
}

.terrasoft-chart-card {
    background: white;
    padding: 24px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.terrasoft-chart-card h3 {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 20px 0;
}

.terrasoft-chart-placeholder {
    height: 300px;
    background: #f8fafc;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #64748b;
}

.terrasoft-chart-placeholder i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

/* CSV Upload Modal */
.terrasoft-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    backdrop-filter: blur(2px);
}

.terrasoft-modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.terrasoft-modal-content {
    background: white;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.terrasoft-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.terrasoft-modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
}

.terrasoft-modal-close {
    background: none;
    border: none;
    font-size: 20px;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

.terrasoft-modal-close:hover {
    background: #f3f4f6;
}

.terrasoft-modal-body {
    padding: 24px;
}

.terrasoft-upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.terrasoft-upload-area:hover {
    border-color: #3b82f6;
    background: #f8fafc;
}

.terrasoft-upload-area.dragover {
    border-color: #3b82f6;
    background: #eff6ff;
}

.terrasoft-upload-icon {
    font-size: 48px;
    color: #64748b;
    margin-bottom: 16px;
}

.terrasoft-upload-text h4 {
    margin: 0 0 8px 0;
    font-size: 16px;
    font-weight: 500;
    color: #1e293b;
}

.terrasoft-upload-text p {
    margin: 0;
    font-size: 14px;
    color: #64748b;
}

.terrasoft-upload-progress {
    margin-top: 20px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.terrasoft-progress-bar {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 8px;
}

.terrasoft-progress-fill {
    height: 100%;
    background: #3b82f6;
    border-radius: 4px;
    transition: width 0.3s ease;
    width: 0%;
}

.terrasoft-progress-text {
    text-align: center;
    margin-top: 8px;
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

/* Notification Styles */
.terrasoft-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border: 1px solid #e2e8f0;
    min-width: 300px;
    max-width: 500px;
    z-index: 10000;
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.3s ease;
}

.terrasoft-notification.show {
    transform: translateX(0);
    opacity: 1;
}

.terrasoft-notification-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px 20px;
}

.terrasoft-notification-content i {
    font-size: 20px;
    margin-top: 2px;
    flex-shrink: 0;
}

.terrasoft-notification-message {
    flex: 1;
    font-size: 14px;
    line-height: 1.5;
}

.terrasoft-notification-close {
    position: absolute;
    top: 8px;
    right: 8px;
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

.terrasoft-notification-close:hover {
    background: #f3f4f6;
}

.terrasoft-notification-success {
    border-left: 4px solid #10b981;
}

.terrasoft-notification-success .terrasoft-notification-content i {
    color: #10b981;
}

.terrasoft-notification-error {
    border-left: 4px solid #ef4444;
}

.terrasoft-notification-error .terrasoft-notification-content i {
    color: #ef4444;
}

.terrasoft-notification-info {
    border-left: 4px solid #3b82f6;
}

.terrasoft-notification-info .terrasoft-notification-content i {
    color: #3b82f6;
}

.upload-stats ul {
    margin: 8px 0 0 0;
    padding-left: 20px;
}

.upload-stats li {
    margin: 4px 0;
}

.terrasoft-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 20px 24px;
    border-top: 1px solid #e5e7eb;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .terrasoft-date-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .terrasoft-date-group {
        width: 100%;
    }
    
    .terrasoft-date-input {
        min-width: auto;
    }
    
    .terrasoft-tab-nav {
        flex-direction: column;
    }
    
    .terrasoft-data-tab-nav {
        flex-direction: column;
    }
    
    .terrasoft-metrics-grid {
        grid-template-columns: 1fr;
    }
    
    .terrasoft-chart-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .terrasoft-data-table {
        font-size: 11px;
    }
    
    .terrasoft-data-table th,
    .terrasoft-data-table td {
        padding: 8px 12px;
    }
}

/* Loading Styles */
.terrasoft-loading {
     position: absolute;
    display: flex;
       top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
    color: #64748b;
}
.terrasoft-loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.7); /* semi-transparent overlay */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 999; /* make sure it's on top of the table */
}
.terrasoft-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #ccc;
    border-top-color: #1d4ed8; /* spinner color */
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.terrasoft-no-data {
    text-align: center;
    padding: 40px;
    color: #64748b;
    font-size: 16px;
}

.terrasoft-error-message {
    text-align: center;
    padding: 40px;
    color: #ef4444;
    font-size: 16px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    margin: 20px;
}
</style>

<script>
     let currentMainTab = 'Ontdashboard';
    let currentDataTab = 'ont-data';
document.addEventListener('DOMContentLoaded', function() {
    // Global variables
   
    
    // Main Tab Navigation
    const tabBtns = document.querySelectorAll('.terrasoft-tab-btn');
    const tabContents = document.querySelectorAll('.terrasoft-tab-content');
    window.loadDataTabData =function (mainTab, dataTab,page=1) {

    const loadingId = getLoadingIndicatorId(mainTab, dataTab);
    const containerId = getContainerId(mainTab, dataTab);
    const apiEndpoint = getApiEndpoint(mainTab, dataTab);
    const filters = getFilters(mainTab);
    const queryParams = new URLSearchParams(filters).toString();
    const urlWithParams = `${apiEndpoint}?${queryParams}&page=${page}`; 

    showLoading(loadingId);

    fetch(urlWithParams)
        .then(response => response.json())
        .then(data => {
            hideLoading(loadingId);
            renderTable(containerId, data, mainTab, dataTab);

            // Destroy previous DataTable if exists
            if ($.fn.DataTable.isDataTable('#tabularDataTable')) {
                $('#tabularDataTable').DataTable().destroy();
            }

            $('#tabularDataTable').DataTable({
                scrollX: true,
                searching: true,
                responsive: false,
                paging: false,
                info: false,
                dom: 'Bfrtip',
                buttons: ['copyHtml5','excelHtml5','csvHtml5','pdfHtml5']
            });

        })
        .catch(error => {
            hideLoading(loadingId);
            console.error('Error loading data:', error);
            showError(containerId, 'Failed to load data. Please try again.');
        });
};
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            currentMainTab = tabId;
            const uploadCsvBtn = document.getElementById('uploadCsvBtn');
            if (uploadCsvBtn) {
            if (currentMainTab === 'Samriddhdashboard') {
                uploadCsvBtn.disabled = true; 
                uploadCsvBtn.title = "Upload disabled for Samriddh dashboard"; 
            } else {
                uploadCsvBtn.disabled = false; 
            }
        }

            
                        
            // Remove active class from all tabs
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            document.getElementById(tabId + '-tab').classList.add('active');
            
            // Load data for the selected main tab
            loadMainTabData(tabId);
        });
    });
    
    // Data Tab Navigation
    const dataTabBtns = document.querySelectorAll('.terrasoft-data-tab-btn');
    const dataTabContents = document.querySelectorAll('.terrasoft-data-tab-content');
    
    dataTabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.dataTab;
            currentDataTab = tabId;
            
            // Get the parent container to only affect tabs within the current main tab
            const parentContainer = this.closest('.terrasoft-data-tab-container');
            const siblingBtns = parentContainer.querySelectorAll('.terrasoft-data-tab-btn');
            const siblingContents = parentContainer.querySelectorAll('.terrasoft-data-tab-content');
            
            // Remove active class from sibling data tabs
            siblingBtns.forEach(b => b.classList.remove('active'));
            siblingContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            document.getElementById(tabId + '-tab').classList.add('active');
            
            // Load data for the selected data tab
            loadDataTabData(currentMainTab, tabId);
        });
    });
    
    // Filter Apply Buttons
    document.getElementById('applyFilters').addEventListener('click', function() {
        loadDataTabData('Ontdashboard', currentDataTab);
    });
    
    document.getElementById('applyOltFilters').addEventListener('click', function() {
        loadDataTabData('Oltdashboard', 'olt-dashboard');
    });
    
    document.getElementById('applySamriddhFilters').addEventListener('click', function() {
        loadDataTabData('Samriddhdashboard', 'samriddh-dashboard');
    });
    
    // Load initial data
    loadMainTabData('Ontdashboard');
    
    // Function to load main tab data
    function loadMainTabData(mainTab) {
        // Reset to first data tab when switching main tabs
        const firstDataTab = getFirstDataTabForMainTab(mainTab);
        loadDataTabData(mainTab, firstDataTab);
    }
    
    // Function to get first data tab for main tab
    function getFirstDataTabForMainTab(mainTab) {
        switch(mainTab) {
            case 'Ontdashboard':
                return 'ont-data';
            case 'Oltdashboard':
                return 'olt-dashboard';
            case 'Samriddhdashboard':
                return 'samriddh-dashboard';
            default:
                return 'ont-data';
        }
    }


  
    
    // Helper functions
    function getLoadingIndicatorId(mainTab, dataTab) {
        const mapping = {
            'Ontdashboard': {
                'ont-data': 'loadingIndicator',
                'uptime-data': 'loadingIndicatorUptime'
            },
            'Oltdashboard': {
                'olt-dashboard': 'loadingIndicatorOlt',
                'olt-performance': 'loadingIndicatorOltPerf'
            },
            'Samriddhdashboard': {
                'samriddh-dashboard': 'loadingIndicatorSamriddh',
                'samriddh-analytics': 'loadingIndicatorSamriddhAnalytics'
            }
        };
        return mapping[mainTab][dataTab];
    }
    
    function getContainerId(mainTab, dataTab) {
        const mapping = {
            'Ontdashboard': {
                'ont-data': 'dataTableContainer',
                'uptime-data': 'uptimeTableContainer'
            },
            'Oltdashboard': {
                'olt-dashboard': 'oltTableContainer',
                'olt-performance': 'oltPerfTableContainer'
            },
            'Samriddhdashboard': {
                'samriddh-dashboard': 'samriddhTableContainer',
                'samriddh-analytics': 'samriddhAnalyticsTableContainer'
            }
        };
        return mapping[mainTab][dataTab];
    }
    
    function getApiEndpoint(mainTab, dataTab) {
        const baseUrl = '/admin';
        return `${baseUrl}/${dataTab.replace('-', '_')}`;
    }
    
    function getFilters(mainTab) {
        let filters = {};
        
        switch(mainTab) {
            case 'Ontdashboard':
                filters = {
                    month: document.getElementById('month').value,
                    fromDate: document.getElementById('from_date').value,
                    toDate: document.getElementById('to_date').value
                };
                break;
            case 'Oltdashboard':
                filters = {
                    month: document.getElementById('olt_month').value,
                    fromDate: document.getElementById('olt_from_date').value,
                    toDate: document.getElementById('olt_to_date').value
                };
                break;
            case 'Samriddhdashboard':
                filters = {
                    month: document.getElementById('samriddh_month').value,
                    fromDate: document.getElementById('samriddh_from_date').value,
                    toDate: document.getElementById('samriddh_to_date').value
                };
                break;
        }
        
        return filters;
    }
    
    function showLoading(loadingId) {
        const loadingElement = document.getElementById(loadingId);
        if (loadingElement) {
            loadingElement.style.display = 'flex';
        }
    }
    
    function hideLoading(loadingId) {
        const loadingElement = document.getElementById(loadingId);
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
    }
    
    function renderTable(containerId, data, mainTab, dataTab) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        // Generate table HTML based on data structure
        let tableHtml = generateTableHtml(data, mainTab, dataTab);
        container.innerHTML = tableHtml;
    }
    
    function generateTableHtml(data, mainTab, dataTab) {
        if (!data || !data.data || data.data.length === 0) {
            return '<div class="terrasoft-no-data">No data available</div>';
        }
        
        // Check if this is tabular data (second tab format) or aggregated data (first tab format)
        const isTabularData = isSecondTabFormat(mainTab, dataTab);
        
        if (isTabularData) {
            return generateTabularTable(data, mainTab, dataTab);
        } else {
            return generateAggregatedTable(data, mainTab, dataTab);
        }
    }
    
    function isSecondTabFormat(mainTab, dataTab) {
        const secondTabFormats = {
            'Ontdashboard': 'uptime-data',
            'Oltdashboard': 'olt-performance', 
            'Samriddhdashboard': 'samriddh-analytics'
        };
        return secondTabFormats[mainTab] === dataTab;
    }
    
 function generateTabularTable(data, mainTab, dataTab) {
    let html = '<div class="terrasoft-table-container">';
    html += '<div class="terrasoft-table-wrapper">';
    html += '<table class="terrasoft-data-table terrasoft-tabular-table" id="tabularDataTable" style="width:100%">';

    // Generate headers dynamically from first data item
    if (data.data && data.data.data.length > 0) {
        const firstItem = data.data.data[0];
        const columns = getTabularColumns(mainTab, dataTab, firstItem);

        html += '<thead><tr>';
        columns.forEach(column => {
            html += `<th class="terrasoft-th-sortable" onclick="sortTable('${column.key}')">${column.label} <i class="ti-chevron-up"></i></th>`;
        });
        html += '</tr></thead>';

        // Generate rows
        html += '<tbody>';
        const startIndex = (data.data.current_page - 1) * data.data.per_page;

        data.data.data.forEach((row, index) => {
            html += '<tr class="terrasoft-tabular-row">';
            columns.forEach(column => {
                let value = row[column.key] || '';

                // Format specific columns
                if (column.key === 'uptime_percent' || column.key.includes('percentage')) {
                    value = parseFloat(value).toFixed(2);
                    html += `<td class="terrasoft-td-percentage">${value}</td>`;
                } else if (column.key === 'record_date' || column.key.includes('date')) {
                    const dateObj = new Date(value);
                    const formattedDate = `${String(dateObj.getDate()).padStart(2, '0')}/${String(dateObj.getMonth() + 1).padStart(2, '0')}/${dateObj.getFullYear()}`;
                    html += `<td class="terrasoft-td-date">${formattedDate}</td>`;
                } else if (column.key === 'id' || column.key === 'sl_no') {
                    html += `<td class="terrasoft-td-number">${startIndex + index + 1}</td>`;
       
                } else {
                    html += `<td class="terrasoft-td-text">${value}</td>`;
                }
            });
            html += '</tr>';
        });
        html += '</tbody>';
    }

    html += '</table></div>';

    // Pagination
    html += generatePagination(data,mainTab, dataTab);

    html += '</div>';
    return html;
}

function generatePagination(data, mainTab, dataTab) {
    if (!data.data) return '';
    const { current_page, last_page, total, from, to } = data.data;
    let html = '<div class="terrasoft-pagination-container">';
    html += `<div class="terrasoft-pagination-info">Showing ${from} to ${to} of ${total} entries</div>`;
    html += '<div class="terrasoft-pagination">';

    const prevDisabled = current_page <= 1 ? 'disabled' : '';
    html += `<button class="terrasoft-pagination-btn ${prevDisabled}" 
        ${prevDisabled ? '' : `onclick="loadDataTabData('${mainTab}', '${dataTab}', ${current_page - 1})"`}>
        <i class="ti-chevron-left"></i> Previous
    </button>`;

    const startPage = Math.max(1, current_page - 2);
    const endPage = Math.min(last_page, current_page + 2);
    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === current_page ? 'active' : '';
        html += `<button class="terrasoft-pagination-btn ${activeClass}" 
            ${activeClass ? '' : `onclick="loadDataTabData('${mainTab}', '${dataTab}', ${i})"`}>
            ${i}
        </button>`;
    }

    const nextDisabled = current_page >= last_page ? 'disabled' : '';
    html += `<button class="terrasoft-pagination-btn ${nextDisabled}" 
        ${nextDisabled ? '' : `onclick="loadDataTabData('${mainTab}', '${dataTab}', ${current_page + 1})"`}>
        Next <i class="ti-chevron-right"></i>
    </button>`;

    html += '</div></div>';
    return html;
}



// Event listener for pagination clicks
// document.addEventListener('click', function(e) {
//     if (e.target.matches('.page-link')) {
//         e.preventDefault();
//         const page = parseInt(e.target.dataset.page);
//         if (!isNaN(page) && page > 0) {
//             loadDataTabData(mainTab, dataTab,page);
//         }
//     }
// });

    function getTabularColumns(mainTab, dataTab, sampleData) {
        // Define columns for different tab combinations
        const columnMappings = {
            'Ontdashboard': {
                'uptime-data': [
                    { key: 'id', label: 'ID' },
                    { key: 'district_name', label: 'District' },
                    { key: 'block_name', label: 'Block' },
                    { key: 'zone_name', label: 'Zone' },
                    { key: 'phase', label: 'Phase' },
                    { key: 'gp_name', label: 'GP Name' },
                    { key: 'lgd_code', label: 'LGD Code' },
                    { key: 'uptime_percent', label: 'Uptime %' },
                    { key: 'record_date', label: 'Record Date' }
                ]
            },
            'Oltdashboard': {
                'olt-performance': [
                    { key: 'id', label: 'ID' },
                    { key: 'district_name', label: 'District' },
                    { key: 'block_name', label: 'Block' },
                    { key: 'olt_location', label: 'OLT Name' },
                    { key: 'olt_ip', label: 'IP Address' },
                    { key: 'no_of_gps', label: 'No Of Gps' },
                    { key: 'lgd_code', label: 'LGD Code' },
                    { key: 'uptime_percent', label: 'Uptime %' },
                    { key: 'record_date', label: 'Record Date' }

                ]
            },
            'Samriddhdashboard': {
                'samriddh-analytics': [
                    { key: 'id', label: 'ID' },
                    { key: 'district_name', label: 'District' },
                    { key: 'block_name', label: 'Block' },
                    { key: 'zone_name', label: 'Zone' },
                    { key: 'phase', label: 'Phase' },
                    { key: 'gp_name', label: 'GP Name' },
                    { key: 'lgd_code', label: 'LGD Code' },
                    { key: 'uptime_percent', label: 'Uptime %' },
                    { key: 'record_date', label: 'Record Date' }
                ]
            }
        };
        
        // Get predefined columns or generate from sample data
        let columns = columnMappings[mainTab] && columnMappings[mainTab][dataTab] 
            ? columnMappings[mainTab][dataTab]
            : Object.keys(sampleData).map(key => ({ key, label: key.replace(/_/g, ' ').toUpperCase() }));
            
        return columns;
    }
    
    function generateAggregatedTable(data, mainTab, dataTab) {
        let html = '<div class="terrasoft-table-container"><div class="terrasoft-table-wrapper">';
        html += '<table class="terrasoft-data-table"><thead><tr>';
        
        // Generate headers
        html += '<th class="terrasoft-th-fixed">SL.NO</th>';
        
        html += '<th class="terrasoft-th-description">' + getDescriptionHeader(mainTab, dataTab) + '</th>';
        
        // Date columns
        if (data.data && data.data.length > 0) {
            data.data.forEach(date => {
                const dateObj = new Date(date.day);

                html += `<th class="terrasoft-th-date">${formatDate(dateObj)}</th>`;
            });
        }
        
        html += '<th class="terrasoft-th-average">Average</th>';
        html += '</tr></thead><tbody>';
        
        // Generate rows
        if (data.data && data.data.length > 0) {
            
            html += generateTableRow(data.data);

        }
        
        // Add total and percentage rows if available
        if (data.totals) {
            html += generateTotalRow(data.totals, data.dates);
        }
        
        if (data.percentages) {
            html += generatePercentageRow(data.percentages, data.dates);
        }
        
        html += '</tbody></table></div></div>';
        return html;
    }
    
    function getDescriptionHeader(mainTab, dataTab) {
        const headers = {
            'Ontdashboard': {
                'ont-data': 'GP UPTIME BIFURCATION',
                'uptime-data': 'UPTIME METRICS'
            },
            'Oltdashboard': {
                'olt-dashboard': 'OLT PERFORMANCE METRICS',
                'olt-performance': 'PERFORMANCE INDICATORS'
            },
            'Samriddhdashboard': {
                'samriddh-dashboard': 'SAMRIDDH METRICS',
                'samriddh-analytics': 'ANALYTICS DATA'
            }
        };
        return headers[mainTab][dataTab] || 'METRICS';
    }

    function generateTableRow(data) {
    const categories = [
        { key: 'gte98', label: 'GPs with (>=98) to 100%', color: 'text-green-500', rowClass: 'terrasoft-data-row', colClass: 'terrasoft-td-description', valClass: 'terrasoft-td-value' },
        { key: 'gte90', label: 'GPs with (>=90) to <98%', rowClass: 'terrasoft-data-row', colClass: 'terrasoft-td-description', valClass: 'terrasoft-td-value' },
        { key: 'gte75', label: 'GPs with (>=75) to <90%', rowClass: 'terrasoft-data-row', colClass: 'terrasoft-td-description', valClass: 'terrasoft-td-value' },
        { key: 'gte50', label: 'GPs with (>=50) to <75%', rowClass: 'terrasoft-data-row', colClass: 'terrasoft-td-description', valClass: 'terrasoft-td-value' },
        { key: 'gte20', label: 'GPs with (>=20) to <50%', rowClass: 'terrasoft-data-row', colClass: 'terrasoft-td-description', valClass: 'terrasoft-td-value' },
        { key: 'lt20', label: 'GPs with (0) to <20%', rowClass: 'terrasoft-data-row', colClass: 'terrasoft-td-description', valClass: 'terrasoft-td-value' },
        { key: 'total', label: 'Total', rowClass: 'terrasoft-total-row', colClass: 'terrasoft-td-total', valClass: 'terrasoft-td-total-value' },
        { key: 'pct_gte98', label: '>98%', rowClass: 'terrasoft-percentage-row', colClass: 'terrasoft-td-percentage', valClass: 'terrasoft-td-percentage-value', isPercent: true }
    ];

    let html = '<tbody>';
    let slNo = 1;

    categories.forEach(cat => {
        html += `<tr class="${cat.rowClass || ''}">`;

        // serial and description
        html += `<td class="terrasoft-td-number">${cat.key !== 'total' && cat.key !== 'pct_gte98' ? slNo++ : ''}</td>`;
        html += `<td class="${cat.colClass || ''}">${cat.label}</td>`;

        // date-wise values
        data.forEach(row => {
            let value = row[cat.key] ?? 0;
            if (cat.isPercent) value = `${value}%`;
            html += `<td class="${cat.valClass || ''}">${value}</td>`;
        });

        // calculate and show average
        const avg = calcAverage(data, cat.key, cat.isPercent);
        html += `<td class="terrasoft-td-average">${avg}</td>`;

        html += '</tr>';
    });

    html += '</tbody>';
    return html;
}

// Helper for averages (up to two decimal points)
function calcAverage(data, key, isPercent) {
    const nums = data.map(d => parseFloat(d[key]) || 0);
    const avg = nums.reduce((a, b) => a + b, 0) / nums.length;
    const formatted = avg % 1 === 0 ? avg.toFixed(0) : avg.toFixed(2).replace(/\.?0+$/, '');
    return isPercent ? `${formatted}%` : formatted;
}


    
    
    function generateTotalRow(totals, dates) {
        let html = '<tr class="terrasoft-total-row">';
        html += '<td class="terrasoft-td-total" colspan="2"><strong>Total</strong></td>';
        
        if (dates && dates.length > 0) {
            dates.forEach(date => {
                const value = totals[date] || '0';
                html += `<td class="terrasoft-td-total-value"><strong>${value}</strong></td>`;
            });
        }
        
        html += `<td class="terrasoft-td-total-value"><strong>${totals.average || '0'}</strong></td>`;
        html += '</tr>';
        return html;
    }
    
    function generatePercentageRow(percentages, dates) {
        let html = '<tr class="terrasoft-percentage-row">';
        html += '<td class="terrasoft-td-percentage" colspan="2"><strong>>98%</strong></td>';
        
        if (dates && dates.length > 0) {
            dates.forEach(date => {
                const value = percentages[date] || '0';
                html += `<td class="terrasoft-td-percentage-value">${value}%</td>`;
            });
        }
        
        html += `<td class="terrasoft-td-percentage-value">${percentages.average || '0'}%</td>`;
        html += '</tr>';
        return html;
    }
    
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
    }
    
    function showError(containerId, message) {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `<div class="terrasoft-error-message">${message}</div>`;
        }
    }
    // CSV Upload functionality
    const uploadCsvBtn = document.getElementById('uploadCsvBtn');
    const csvModal = document.getElementById('csvUploadModal');
    const uploadArea = document.getElementById('uploadArea');
    const csvFileInput = document.getElementById('csvFileInput');
    const uploadBtn = document.getElementById('uploadBtn');
    
    // Main upload button
if (uploadCsvBtn) {
  uploadCsvBtn.addEventListener('click', function() {
   

    uploadModalTitle.textContent =
      `Upload ${currentMainTab.charAt(0).toUpperCase() + currentMainTab.slice(1)} CSV`;
    csvModal.classList.add('show');
  });
}
    
    // Mini upload buttons
    document.querySelectorAll('.terrasoft-upload-mini').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const uploadType = this.dataset.upload;
            document.getElementById('uploadModalTitle').textContent = `Upload ${uploadType.charAt(0).toUpperCase() + uploadType.slice(1)} CSV`;
            csvModal.classList.add('show');
        });
    });
    
    // Upload area click
    uploadArea.addEventListener('click', function() {
        csvFileInput.click();
    });
    
    // File input change
    csvFileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            uploadBtn.disabled = false;
            uploadArea.style.borderColor = '#22c55e';
            uploadArea.style.background = '#f0fdf4';
        }
    });
    
    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type === 'text/csv') {
            csvFileInput.files = files;
            uploadBtn.disabled = false;
            this.style.borderColor = '#22c55e';
            this.style.background = '#f0fdf4';
        }
    });
});

function closeCsvModal() {
    document.getElementById('csvUploadModal').classList.remove('show');
    document.getElementById('csvFileInput').value = '';
    document.getElementById('uploadBtn').disabled = true;
    document.getElementById('uploadArea').style.borderColor = '#d1d5db';
    document.getElementById('uploadArea').style.background = '';
    document.getElementById('uploadProgress').style.display = 'none';
}

    const routes = {
        Ontdashboard : "{{ route('admin.ont-upload') }}",
        Oltdashboard: "{{ route('admin.olt-upload') }}",
    };

  function uploadCsv() {
    const uploadBtn = document.getElementById('uploadBtn');
    const fileInput = document.getElementById('csvFileInput');
    const file = fileInput.files[0];

    if (!file) return alert('Please select a CSV file.');

    const formData = new FormData();
    formData.append('csv_file', file);

    const url = routes[currentMainTab];
    if (!url) return alert('Invalid dashboard tab selected.');

    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="ti-reload ti-spin"></i> Uploading...';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Upload successful');
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="ti-upload"></i> Upload';
        closeCsvModal();
    })
    .catch(err => {
        console.error(err);
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="ti-upload"></i> Upload';
        alert('Error uploading CSV');
    });
}


</script>
@endsection