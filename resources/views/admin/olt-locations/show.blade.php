
@extends('admin.layout.base')

@section('title', 'OLT Location Details')

@section('content')
<div class="terrasoft-main-content">
    <div class="terrasoft-page-container">
        {{-- Page Header --}}
        <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-eye text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">{{ $location->olt_location ?  $location->olt_location : '-' }}</h1>
                        <p class="terrasoft-page-subtitle">OLT Location Details and Configuration</p>
                    </div>
                </div>
                <div class="terrasoft-header-actions">
                    
                
                    <a href="{{ route('admin.olt-locations.index') }}" class="terrasoft-btn terrasoft-btn-secondary">
                        <i class="ti-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

       

        <div class="terrasoft-details-grid">
            {{-- Basic Information Card --}}
            <div class="terrasoft-details-card">
                <div class="terrasoft-card-header">
                    <h3 class="terrasoft-card-title">
                        <i class="ti-info-circle"></i>
                        Basic Information
                    </h3>
                </div>
                <div class="terrasoft-card-content">
                    <div class="terrasoft-detail-grid">
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Location Name</label>
                            <div class="terrasoft-detail-value">{{ $location->olt_location }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Location Code</label>
                            <div class="terrasoft-detail-value terrasoft-code">{{ $location->olt_location_code }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Created Date</label>
                            <div class="terrasoft-detail-value">{{ $location->created_at }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Last Updated</label>
                            <div class="terrasoft-detail-value">{{ $location->updated_at }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Administrative Information Card --}}
            <div class="terrasoft-details-card">
                <div class="terrasoft-card-header">
                    <h3 class="terrasoft-card-title">
                        <i class="ti-map-pin"></i>
                        Administrative Information
                    </h3>
                </div>
                <div class="terrasoft-card-content">
                    <div class="terrasoft-detail-grid">
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">State</label>
                            <div class="terrasoft-detail-value">{{ $location->state->state_name }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">District</label>
                            <div class="terrasoft-detail-value">{{ $location->district->name  }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Block</label>
                            <div class="terrasoft-detail-value">{{ $location->block->name}}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">LGD Code</label>
                            <div class="terrasoft-detail-value terrasoft-code">{{ $location->lgd_code }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Technical Information Card --}}
            <div class="terrasoft-details-card">
                <div class="terrasoft-card-header">
                    <h3 class="terrasoft-card-title">
                        <i class="ti-server"></i>
                        Technical Information
                    </h3>
                   
                </div>
                <div class="terrasoft-card-content">
                    <div class="terrasoft-detail-grid">
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">IP Address</label>
                            <div class="terrasoft-detail-value terrasoft-ip">
                                {{ $location->olt_ip}}
                                <button class="terrasoft-copy-btn" onclick="copyToClipboard('{{ $location->olt_ip  }}')">
                                    <i class="ti-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">GP Count</label>
                            <div class="terrasoft-detail-value terrasoft-metric">
                                {{ $location->no_of_gps }} GPs
                            </div>
                        </div>
                      
                    </div>
                </div>
            </div>

         
        </div>

        {{-- Action Buttons --}}
        <div class="terrasoft-action-bar">
            <div class="terrasoft-action-group">
                <a href="{{ route('admin.olt-locations.edit', $location->id) }}" class="terrasoft-btn terrasoft-btn-primary">
                    <i class="ti-edit"></i>
                    Edit Location
                </a>
                  <button class="terrasoft-btn terrasoft-btn-danger"   onclick="deleteLocation({{ $location->id }}, '{{ $location->olt_location }}')">
                    <i class="ti-trash"></i>
                    Delete Location
                </button>
               
            </div>
            <div class="terrasoft-action-group">
              
                
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="terrasoft-modal" data-location-id="">
    <div class="terrasoft-modal-content">
        <div class="terrasoft-modal-header">
            <h3>Confirm Delete</h3>
            <button class="terrasoft-modal-close" onclick="closeDeleteModal()">
                <i class="ti-x"></i>
            </button>
        </div>
        <div class="terrasoft-modal-body">
            <div class="terrasoft-delete-warning">
                <i class="ti-alert-triangle text-red-500"></i>
                <div>
                    <p><strong>Are you sure you want to delete this  <strong id="locationName"></strong> OLT location ?</strong></p>
                    <p>This action cannot be undone and will remove all associated data.</p>
                </div>
            </div>
        </div>
        <div class="terrasoft-modal-footer">
            <button class="terrasoft-btn terrasoft-btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <button class="terrasoft-btn terrasoft-btn-danger" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('/css/olt.css')}}">

<style>
/* Details View Styles */
.terrasoft-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.terrasoft-details-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.terrasoft-card-wide {
    grid-column: 1 / -1;
}

.terrasoft-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
}

.terrasoft-card-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.terrasoft-card-title i {
    color: #3b82f6;
}

.terrasoft-card-actions {
    display: flex;
    gap: 8px;
}

.terrasoft-card-content {
    padding: 24px;
}

/* Detail Items */
.terrasoft-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.terrasoft-detail-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.terrasoft-detail-full {
    grid-column: 1 / -1;
}

.terrasoft-detail-label {
    font-size: 12px;
    font-weight: 500;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.terrasoft-detail-value {
    font-size: 14px;
    color: #1e293b;
    font-weight: 500;
}

.terrasoft-code {
    font-family: 'Monaco', 'Menlo', monospace;
    background: #f1f5f9;
    padding: 4px 8px;
    border-radius: 4px;
    display: inline-block;
}

.terrasoft-ip {
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: 'Monaco', 'Menlo', monospace;
}

.terrasoft-copy-btn {
    background: none;
    border: none;
    color: #64748b;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.terrasoft-copy-btn:hover {
    background: #f1f5f9;
    color: #3b82f6;
}

.terrasoft-metric {
    font-weight: 600;
    color: #059669;
}

.terrasoft-description {
    line-height: 1.6;
    color: #475569;
}

.terrasoft-phone-link {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #3b82f6;
    text-decoration: none;
    transition: color 0.2s ease;
}

.terrasoft-phone-link:hover {
    color: #1d4ed8;
}

/* Status Styles */
.terrasoft-status-banner {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
}

.terrasoft-status-active {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
}

.terrasoft-status-indicator {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #22c55e;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.terrasoft-status-content {
    flex: 1;
}

.terrasoft-status-title {
    font-weight: 600;
    color: #166534;
    margin-bottom: 2px;
}

.terrasoft-status-subtitle {
    font-size: 14px;
    color: #16a34a;
}

.terrasoft-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
}

.terrasoft-status-badge.terrasoft-status-active {
    background: #dcfce7;
    color: #166534;
}

/* Metrics */
.terrasoft-metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.terrasoft-metric-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.terrasoft-metric-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
}

.terrasoft-metric-success {
    background: #22c55e;
}

.terrasoft-metric-info {
    background: #3b82f6;
}

.terrasoft-metric-warning {
    background: #f59e0b;
}

.terrasoft-metric-danger {
    background: #ef4444;
}

.terrasoft-metric-value {
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
}

.terrasoft-metric-label {
    font-size: 12px;
    color: #64748b;
}

/* Activity List */
.terrasoft-activity-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.terrasoft-activity-item {
    display: flex;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}

.terrasoft-activity-item:last-child {
    border-bottom: none;
}

.terrasoft-activity-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: white;
    flex-shrink: 0;
}

.terrasoft-activity-success {
    background: #22c55e;
}

.terrasoft-activity-info {
    background: #3b82f6;
}

.terrasoft-activity-warning {
    background: #f59e0b;
}

.terrasoft-activity-title {
    font-weight: 500;
    color: #1e293b;
    margin-bottom: 2px;
}

.terrasoft-activity-description {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 4px;
}

.terrasoft-activity-time {
    font-size: 12px;
    color: #9ca3af;
}

/* Small Components */
.terrasoft-btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.terrasoft-btn-outline {
    background: transparent;
    border: 1px solid #d1d5db;
    color: #374151;
}

.terrasoft-btn-outline:hover {
    background: #f3f4f6;
}

.terrasoft-select-sm {
    padding: 6px 12px;
    font-size: 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
}

/* Action Bar */
.terrasoft-action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 0;
    border-top: 1px solid #e2e8f0;
}

.terrasoft-action-group {
    display: flex;
    gap: 12px;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .terrasoft-details-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .terrasoft-detail-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .terrasoft-metrics-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .terrasoft-action-bar {
        flex-direction: column;
        gap: 16px;
    }
    
    .terrasoft-action-group {
        width: 100%;
        justify-content: center;
    }
    
    .terrasoft-header-actions {
        flex-direction: column;
        width: 100%;
    }
}

@media (max-width: 480px) {
    .terrasoft-metrics-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const btn = event.target.closest('.terrasoft-copy-btn');
        const originalIcon = btn.innerHTML;
        btn.innerHTML = '<i class="ti-check"></i>';
        btn.style.color = '#22c55e';
        
        setTimeout(() => {
            btn.innerHTML = originalIcon;
            btn.style.color = '';
        }, 2000);
    });
}



// Delete location function
function deleteLocation(id, name) {
    const modal = document.getElementById('deleteModal');
    modal.dataset.locationId = id;
    document.getElementById('locationName').textContent = name;
    modal.classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}

function confirmDelete() {
     const modal = document.getElementById('deleteModal');
    const id = modal.dataset.locationId;
    fetch(`/admin/olt-locations/${ id }`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("admin.olt-locations.index") }}';
        } else {
            alert('Error deleting location');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting location');
    });
    
    closeDeleteModal();
}



// Auto-refresh status
setInterval(() => {
    // Update status indicator
    const statusTime = document.querySelector('.terrasoft-status-subtitle');
    if (statusTime) {
        const now = new Date();
        statusTime.textContent = `All systems operational • Last checked: ${now.getSeconds()} seconds ago`;
    }
}, 60000);
</script>
@endsection