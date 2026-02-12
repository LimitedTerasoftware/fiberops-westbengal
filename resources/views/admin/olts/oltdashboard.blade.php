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
                        <i class="ti-activity text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">ONT Uptime</h1>
                        <p class="terrasoft-page-subtitle">Monitor and manage Optical Network Terminal uptime data</p>
                    </div>
                </div>
                <div class="terrasoft-header-actions">
                    <button class="terrasoft-btn terrasoft-btn-success" id="uploadCsvBtn">
                        <i class="ti-upload"></i>
                        Upload CSV
                    </button>
                </div>
            </div>
        </div>

        {{-- Main Tab Navigation --}}
        <div class="terrasoft-tab-container">
            <div class="terrasoft-tab-nav">
                <button class="terrasoft-tab-btn active" data-tab="dashboard">
                    <i class="ti-bar-chart"></i>
                    Dashboard
                </button>
                <button class="terrasoft-tab-btn" data-tab="performance">
                    <i class="ti-trending-up"></i>
                    Performance
                </button>
                <button class="terrasoft-tab-btn" data-tab="analytics">
                    <i class="ti-pie-chart"></i>
                    Analytics
                </button>
            </div>

            {{-- Dashboard Tab Content --}}
            <div class="terrasoft-tab-content active" id="dashboard-tab">
                {{-- Date Filter Section --}}
                <div class="terrasoft-filter-section">
                    <div class="terrasoft-date-filters">
                        <div class="terrasoft-date-group">
                            <label class="terrasoft-date-label">Location</label>
                            <select class="terrasoft-date-input">
                                <option value="">Select Location</option>
                                <option value="all">All Locations</option>
                                <option value="ec01">Electronic City Phase 1</option>
                                <option value="km01">Koramangala</option>
                                <option value="cp01">Chromepet</option>
                            </select>
                        </div>
                        <div class="terrasoft-date-group">
                            <label class="terrasoft-date-label">From Date</label>
                            <input type="date" class="terrasoft-date-input" value="2024-10-02">
                        </div>
                        <div class="terrasoft-date-group">
                            <label class="terrasoft-date-label">To Date</label>
                            <input type="date" class="terrasoft-date-input" value="2024-10-07">
                        </div>
                        <button class="terrasoft-btn terrasoft-btn-primary">
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
                            ONT Data
                            <button class="terrasoft-upload-mini" data-upload="ont">
                                <i class="ti-upload"></i>
                            </button>
                        </button>
                        <button class="terrasoft-data-tab-btn" data-data-tab="uptime-data">
                            <i class="ti-clock"></i>
                            Uptime Data
                            <button class="terrasoft-upload-mini" data-upload="uptime">
                                <i class="ti-upload"></i>
                            </button>
                        </button>
                        <button class="terrasoft-data-tab-btn" data-data-tab="performance-data">
                            <i class="ti-activity"></i>
                            Performance Data
                            <button class="terrasoft-upload-mini" data-upload="performance">
                                <i class="ti-upload"></i>
                            </button>
                        </button>
                    </div>

                    {{-- ONT Data Table --}}
                    <div class="terrasoft-data-tab-content active" id="ont-data-tab">
                        <div class="terrasoft-table-container">
                            <div class="terrasoft-table-wrapper">
                                <table class="terrasoft-data-table">
                                    <thead>
                                        <tr>
                                            <th class="terrasoft-th-fixed">SL.NO</th>
                                            <th class="terrasoft-th-description">GP UPTIME BIFURCATION</th>
                                            <th class="terrasoft-th-date">02 Oct</th>
                                            <th class="terrasoft-th-date">03 Oct</th>
                                            <th class="terrasoft-th-date">04 Oct</th>
                                            <th class="terrasoft-th-date">05 Oct</th>
                                            <th class="terrasoft-th-date">06 Oct</th>
                                            <th class="terrasoft-th-date">07 Oct</th>
                                            <th class="terrasoft-th-average">Average</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">1</td>
                                            <td class="terrasoft-td-description">GPs with (>=98) to 100%</td>
                                            <td class="terrasoft-td-value">1776</td>
                                            <td class="terrasoft-td-value">1688</td>
                                            <td class="terrasoft-td-value">1682</td>
                                            <td class="terrasoft-td-value">1613</td>
                                            <td class="terrasoft-td-value">1713</td>
                                            <td class="terrasoft-td-value">1778</td>
                                            <td class="terrasoft-td-average">1708.33</td>
                                        </tr>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">2</td>
                                            <td class="terrasoft-td-description">GPs with (>=90) to <98%</td>
                                            <td class="terrasoft-td-value">100</td>
                                            <td class="terrasoft-td-value">114</td>
                                            <td class="terrasoft-td-value">126</td>
                                            <td class="terrasoft-td-value">119</td>
                                            <td class="terrasoft-td-value">92</td>
                                            <td class="terrasoft-td-value">91</td>
                                            <td class="terrasoft-td-average">107</td>
                                        </tr>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">3</td>
                                            <td class="terrasoft-td-description">GPs with (>=75) to <90%</td>
                                            <td class="terrasoft-td-value">41</td>
                                            <td class="terrasoft-td-value">60</td>
                                            <td class="terrasoft-td-value">54</td>
                                            <td class="terrasoft-td-value">63</td>
                                            <td class="terrasoft-td-value">36</td>
                                            <td class="terrasoft-td-value">45</td>
                                            <td class="terrasoft-td-average">49.83</td>
                                        </tr>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">4</td>
                                            <td class="terrasoft-td-description">GPs with (>=50) to <75%</td>
                                            <td class="terrasoft-td-value">42</td>
                                            <td class="terrasoft-td-value">74</td>
                                            <td class="terrasoft-td-value">86</td>
                                            <td class="terrasoft-td-value">82</td>
                                            <td class="terrasoft-td-value">115</td>
                                            <td class="terrasoft-td-value">82</td>
                                            <td class="terrasoft-td-average">80.17</td>
                                        </tr>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">5</td>
                                            <td class="terrasoft-td-description">GPs with (>=20) to <50%</td>
                                            <td class="terrasoft-td-value">65</td>
                                            <td class="terrasoft-td-value">106</td>
                                            <td class="terrasoft-td-value">95</td>
                                            <td class="terrasoft-td-value">131</td>
                                            <td class="terrasoft-td-value">109</td>
                                            <td class="terrasoft-td-value">85</td>
                                            <td class="terrasoft-td-average">98.5</td>
                                        </tr>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">6</td>
                                            <td class="terrasoft-td-description">GPs with (0) to <20%</td>
                                            <td class="terrasoft-td-value">650</td>
                                            <td class="terrasoft-td-value">632</td>
                                            <td class="terrasoft-td-value">631</td>
                                            <td class="terrasoft-td-value">666</td>
                                            <td class="terrasoft-td-value">609</td>
                                            <td class="terrasoft-td-value">593</td>
                                            <td class="terrasoft-td-average">630.17</td>
                                        </tr>
                                        <tr class="terrasoft-total-row">
                                            <td class="terrasoft-td-total" colspan="2"><strong>Total</strong></td>
                                            <td class="terrasoft-td-total-value"><strong>2674</strong></td>
                                            <td class="terrasoft-td-total-value"><strong>2674</strong></td>
                                            <td class="terrasoft-td-total-value"><strong>2674</strong></td>
                                            <td class="terrasoft-td-total-value"><strong>2674</strong></td>
                                            <td class="terrasoft-td-total-value"><strong>2674</strong></td>
                                            <td class="terrasoft-td-total-value"><strong>2674</strong></td>
                                            <td class="terrasoft-td-total-value"><strong>2674</strong></td>
                                        </tr>
                                        <tr class="terrasoft-percentage-row">
                                            <td class="terrasoft-td-percentage" colspan="2"><strong>>98%</strong></td>
                                            <td class="terrasoft-td-percentage-value">66.40%</td>
                                            <td class="terrasoft-td-percentage-value">63.13%</td>
                                            <td class="terrasoft-td-percentage-value">62.90%</td>
                                            <td class="terrasoft-td-percentage-value">60.32%</td>
                                            <td class="terrasoft-td-percentage-value">64.06%</td>
                                            <td class="terrasoft-td-percentage-value">66.49%</td>
                                            <td class="terrasoft-td-percentage-value">63.88%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Uptime Data Table --}}
                    <div class="terrasoft-data-tab-content" id="uptime-data-tab">
                        <div class="terrasoft-table-container">
                            <div class="terrasoft-table-wrapper">
                                <table class="terrasoft-data-table">
                                    <thead>
                                        <tr>
                                            <th class="terrasoft-th-fixed">SL.NO</th>
                                            <th class="terrasoft-th-description">UPTIME METRICS</th>
                                            <th class="terrasoft-th-date">02 Oct</th>
                                            <th class="terrasoft-th-date">03 Oct</th>
                                            <th class="terrasoft-th-date">04 Oct</th>
                                            <th class="terrasoft-th-date">05 Oct</th>
                                            <th class="terrasoft-th-date">06 Oct</th>
                                            <th class="terrasoft-th-date">07 Oct</th>
                                            <th class="terrasoft-th-average">Average</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">1</td>
                                            <td class="terrasoft-td-description">Total Active ONTs</td>
                                            <td class="terrasoft-td-value">2674</td>
                                            <td class="terrasoft-td-value">2674</td>
                                            <td class="terrasoft-td-value">2674</td>
                                            <td class="terrasoft-td-value">2674</td>
                                            <td class="terrasoft-td-value">2674</td>
                                            <td class="terrasoft-td-value">2674</td>
                                            <td class="terrasoft-td-average">2674</td>
                                        </tr>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">2</td>
                                            <td class="terrasoft-td-description">High Performance (>95%)</td>
                                            <td class="terrasoft-td-value">1876</td>
                                            <td class="terrasoft-td-value">1802</td>
                                            <td class="terrasoft-td-value">1808</td>
                                            <td class="terrasoft-td-value">1732</td>
                                            <td class="terrasoft-td-value">1805</td>
                                            <td class="terrasoft-td-value">1869</td>
                                            <td class="terrasoft-td-average">1815.33</td>
                                        </tr>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">3</td>
                                            <td class="terrasoft-td-description">Medium Performance (75-95%)</td>
                                            <td class="terrasoft-td-value">148</td>
                                            <td class="terrasoft-td-value">240</td>
                                            <td class="terrasoft-td-value">235</td>
                                            <td class="terrasoft-td-value">276</td>
                                            <td class="terrasoft-td-value">260</td>
                                            <td class="terrasoft-td-value">212</td>
                                            <td class="terrasoft-td-average">228.5</td>
                                        </tr>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">4</td>
                                            <td class="terrasoft-td-description">Low Performance (<75%)</td>
                                            <td class="terrasoft-td-value">650</td>
                                            <td class="terrasoft-td-value">632</td>
                                            <td class="terrasoft-td-value">631</td>
                                            <td class="terrasoft-td-value">666</td>
                                            <td class="terrasoft-td-value">609</td>
                                            <td class="terrasoft-td-value">593</td>
                                            <td class="terrasoft-td-average">630.17</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Performance Data Table --}}
                    <div class="terrasoft-data-tab-content" id="performance-data-tab">
                        <div class="terrasoft-table-container">
                            <div class="terrasoft-table-wrapper">
                                <table class="terrasoft-data-table">
                                    <thead>
                                        <tr>
                                            <th class="terrasoft-th-fixed">SL.NO</th>
                                            <th class="terrasoft-th-description">PERFORMANCE INDICATORS</th>
                                            <th class="terrasoft-th-date">02 Oct</th>
                                            <th class="terrasoft-th-date">03 Oct</th>
                                            <th class="terrasoft-th-date">04 Oct</th>
                                            <th class="terrasoft-th-date">05 Oct</th>
                                            <th class="terrasoft-th-date">06 Oct</th>
                                            <th class="terrasoft-th-date">07 Oct</th>
                                            <th class="terrasoft-th-average">Average</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">1</td>
                                            <td class="terrasoft-td-description">Signal Strength (dBm)</td>
                                            <td class="terrasoft-td-value">-15.2</td>
                                            <td class="terrasoft-td-value">-14.8</td>
                                            <td class="terrasoft-td-value">-15.1</td>
                                            <td class="terrasoft-td-value">-15.5</td>
                                            <td class="terrasoft-td-value">-14.9</td>
                                            <td class="terrasoft-td-value">-15.0</td>
                                            <td class="terrasoft-td-average">-15.08</td>
                                        </tr>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">2</td>
                                            <td class="terrasoft-td-description">Bandwidth Utilization (%)</td>
                                            <td class="terrasoft-td-value">78.5</td>
                                            <td class="terrasoft-td-value">82.1</td>
                                            <td class="terrasoft-td-value">79.8</td>
                                            <td class="terrasoft-td-value">85.2</td>
                                            <td class="terrasoft-td-value">81.3</td>
                                            <td class="terrasoft-td-value">77.9</td>
                                            <td class="terrasoft-td-average">80.8</td>
                                        </tr>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">3</td>
                                            <td class="terrasoft-td-description">Error Rate (%)</td>
                                            <td class="terrasoft-td-value">0.12</td>
                                            <td class="terrasoft-td-value">0.08</td>
                                            <td class="terrasoft-td-value">0.15</td>
                                            <td class="terrasoft-td-value">0.18</td>
                                            <td class="terrasoft-td-value">0.09</td>
                                            <td class="terrasoft-td-value">0.11</td>
                                            <td class="terrasoft-td-average">0.122</td>
                                        </tr>
                                        <tr class="terrasoft-data-row">
                                            <td class="terrasoft-td-number">4</td>
                                            <td class="terrasoft-td-description">Latency (ms)</td>
                                            <td class="terrasoft-td-value">12.5</td>
                                            <td class="terrasoft-td-value">11.8</td>
                                            <td class="terrasoft-td-value">13.2</td>
                                            <td class="terrasoft-td-value">14.1</td>
                                            <td class="terrasoft-td-value">12.0</td>
                                            <td class="terrasoft-td-value">11.9</td>
                                            <td class="terrasoft-td-average">12.58</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Performance Tab Content --}}
            <div class="terrasoft-tab-content" id="performance-tab">
                <div class="terrasoft-performance-dashboard">
                    <div class="terrasoft-metrics-grid">
                        <div class="terrasoft-metric-card">
                            <div class="terrasoft-metric-header">
                                <h3>Total ONTs</h3>
                                <i class="ti-server"></i>
                            </div>
                            <div class="terrasoft-metric-value">2,674</div>
                            <div class="terrasoft-metric-change positive">+2.5%</div>
                        </div>
                        <div class="terrasoft-metric-card">
                            <div class="terrasoft-metric-header">
                                <h3>High Performance</h3>
                                <i class="ti-trending-up"></i>
                            </div>
                            <div class="terrasoft-metric-value">1,815</div>
                            <div class="terrasoft-metric-change positive">+5.2%</div>
                        </div>
                        <div class="terrasoft-metric-card">
                            <div class="terrasoft-metric-header">
                                <h3>Average Uptime</h3>
                                <i class="ti-clock"></i>
                            </div>
                            <div class="terrasoft-metric-value">98.2%</div>
                            <div class="terrasoft-metric-change positive">+1.1%</div>
                        </div>
                        <div class="terrasoft-metric-card">
                            <div class="terrasoft-metric-header">
                                <h3>Issues Detected</h3>
                                <i class="ti-alert-triangle"></i>
                            </div>
                            <div class="terrasoft-metric-value">23</div>
                            <div class="terrasoft-metric-change negative">-12%</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Analytics Tab Content --}}
            <div class="terrasoft-tab-content" id="analytics-tab">
                <div class="terrasoft-analytics-dashboard">
                    <div class="terrasoft-chart-grid">
                        <div class="terrasoft-chart-card">
                            <h3>Uptime Trends</h3>
                            <div class="terrasoft-chart-placeholder">
                                <i class="ti-bar-chart"></i>
                                <p>Chart visualization would be displayed here</p>
                            </div>
                        </div>
                        <div class="terrasoft-chart-card">
                            <h3>Performance Distribution</h3>
                            <div class="terrasoft-chart-placeholder">
                                <i class="ti-pie-chart"></i>
                                <p>Pie chart visualization would be displayed here</p>
                            </div>
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
                <div class="terrasoft-progress-text" id="progressText">Uploading... 0%</div>
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
}

.terrasoft-progress-bar {
    width: 100%;
    height: 8px;
    background: #f1f5f9;
    border-radius: 4px;
    overflow: hidden;
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
    color: #64748b;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Main Tab Navigation
    const tabBtns = document.querySelectorAll('.terrasoft-tab-btn');
    const tabContents = document.querySelectorAll('.terrasoft-tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            
            // Remove active class from all tabs
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            document.getElementById(tabId + '-tab').classList.add('active');
        });
    });
    
    // Data Tab Navigation
    const dataTabBtns = document.querySelectorAll('.terrasoft-data-tab-btn');
    const dataTabContents = document.querySelectorAll('.terrasoft-data-tab-content');
    
    dataTabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.dataTab;
            
            // Remove active class from all data tabs
            dataTabBtns.forEach(b => b.classList.remove('active'));
            dataTabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            document.getElementById(tabId + '-tab').classList.add('active');
        });
    });
    
    // CSV Upload functionality
    const uploadCsvBtn = document.getElementById('uploadCsvBtn');
    const csvModal = document.getElementById('csvUploadModal');
    const uploadArea = document.getElementById('uploadArea');
    const csvFileInput = document.getElementById('csvFileInput');
    const uploadBtn = document.getElementById('uploadBtn');
    
    // Main upload button
    uploadCsvBtn.addEventListener('click', function() {
        document.getElementById('uploadModalTitle').textContent = 'Upload CSV Data';
        csvModal.classList.add('show');
    });
    
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

function uploadCsv() {
    const progressDiv = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    
    progressDiv.style.display = 'block';
    
    // Simulate upload progress
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 100) progress = 100;
        
        progressFill.style.width = progress + '%';
        progressText.textContent = `Uploading... ${Math.round(progress)}%`;
        
        if (progress >= 100) {
            clearInterval(interval);
            setTimeout(() => {
                alert('CSV uploaded successfully!');
                closeCsvModal();
            }, 500);
        }
    }, 200);
}
</script>
@endsection