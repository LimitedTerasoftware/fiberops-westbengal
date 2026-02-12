@extends('admin.layout.base')

@section('title', 'Material Details')

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
                        <h1 class="terrasoft-page-title">{{ $material->name }}</h1>
                        <p class="terrasoft-page-subtitle">Material Details and Specifications</p>
                    </div>
                </div>
                <div class="terrasoft-header-actions">
                   
                    <a href="{{ route('admin.materials.index') }}" class="terrasoft-btn terrasoft-btn-secondary">
                        <i class="ti-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        {{-- Status Banner --}}
        <!-- <div class="terrasoft-status-banner terrasoft-status-active">
            <div class="terrasoft-status-indicator">
                <i class="ti-check-circle"></i>
            </div>
            <div class="terrasoft-status-content">
                <div class="terrasoft-status-title">Material Status: Active</div>
                <div class="terrasoft-status-subtitle">Last updated: {{ $material->updated_at->diffForHumans() }}</div>
            </div>
        </div> -->

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
                            <label class="terrasoft-detail-label">Material Code</label>
                            <div class="terrasoft-detail-value terrasoft-code">{{ $material->code }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Material Name</label>
                            <div class="terrasoft-detail-value">{{ $material->name }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Created Date</label>
                            <div class="terrasoft-detail-value">{{ $material->created_at->format('d M Y, H:i') }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Last Updated</label>
                            <div class="terrasoft-detail-value">{{ $material->updated_at->format('d M Y, H:i') }}</div>
                        </div>
                        @if($material->description)
                        <div class="terrasoft-detail-item terrasoft-detail-full">
                            <label class="terrasoft-detail-label">Description</label>
                            <div class="terrasoft-detail-value terrasoft-description">{{ $material->description }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Unit Information Card --}}
            <div class="terrasoft-details-card">
                <div class="terrasoft-card-header">
                    <h3 class="terrasoft-card-title">
                        <i class="ti-package"></i>
                        Unit Information
                    </h3>
                </div>
                <div class="terrasoft-card-content">
                    <div class="terrasoft-detail-grid">
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Purchase Unit</label>
                            <div class="terrasoft-detail-value terrasoft-metric">{{ $material->purchase_unit }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Base Unit</label>
                            <div class="terrasoft-detail-value terrasoft-metric">{{ $material->base_unit }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Quantity per Purchase Unit</label>
                            <div class="terrasoft-detail-value terrasoft-metric">{{ number_format($material->qty_per_purchase_unit, 3) }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Serial Number Required</label>
                            <div class="terrasoft-detail-value">
                                @if($material->has_serial)
                                    <span class="terrasoft-status-badge terrasoft-status-success">
                                        <i class="ti-check-circle"></i>
                                        Yes, Required
                                    </span>
                                @else
                                    <span class="terrasoft-status-badge terrasoft-status-secondary">
                                        <i class="ti-x-circle"></i>
                                        Not Required
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Conversion Display --}}
                    <div class="terrasoft-conversion-display">
                        <div class="terrasoft-conversion-header">
                            <i class="ti-calculator"></i>
                            <span>Unit Conversion</span>
                        </div>
                        <div class="terrasoft-conversion-formula">
                            <div class="terrasoft-conversion-item">
                                <span class="terrasoft-conversion-value">1</span>
                                <span class="terrasoft-conversion-unit">{{ $material->purchase_unit }}</span>
                            </div>
                            <div class="terrasoft-conversion-equals">
                                <i class="ti-equal"></i>
                            </div>
                            <div class="terrasoft-conversion-item">
                                <span class="terrasoft-conversion-value">{{ number_format($material->qty_per_purchase_unit, 3) }}</span>
                                <span class="terrasoft-conversion-unit">{{ $material->base_unit }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Usage Statistics Card --}}
            <!-- <div class="terrasoft-details-card terrasoft-card-wide">
                <div class="terrasoft-card-header">
                    <h3 class="terrasoft-card-title">
                        <i class="ti-activity"></i>
                        Usage Statistics
                    </h3>
                    <div class="terrasoft-card-actions">
                        <select class="terrasoft-select-sm">
                            <option>Last 30 Days</option>
                            <option>Last 90 Days</option>
                            <option>Last 6 Months</option>
                            <option>Last Year</option>
                        </select>
                    </div>
                </div>
                <div class="terrasoft-card-content">
                    <div class="terrasoft-metrics-grid">
                        <div class="terrasoft-metric-card">
                            <div class="terrasoft-metric-icon terrasoft-metric-success">
                                <i class="ti-trending-up"></i>
                            </div>
                            <div class="terrasoft-metric-content">
                                <div class="terrasoft-metric-value">1,247</div>
                                <div class="terrasoft-metric-label">Total Transactions</div>
                            </div>
                        </div>

                        <div class="terrasoft-metric-card">
                            <div class="terrasoft-metric-icon terrasoft-metric-info">
                                <i class="ti-package"></i>
                            </div>
                            <div class="terrasoft-metric-content">
                                <div class="terrasoft-metric-value">850</div>
                                <div class="terrasoft-metric-label">Current Stock</div>
                            </div>
                        </div>

                        <div class="terrasoft-metric-card">
                            <div class="terrasoft-metric-icon terrasoft-metric-warning">
                                <i class="ti-arrow-up"></i>
                            </div>
                            <div class="terrasoft-metric-content">
                                <div class="terrasoft-metric-value">456</div>
                                <div class="terrasoft-metric-label">Total Received</div>
                            </div>
                        </div>

                        <div class="terrasoft-metric-card">
                            <div class="terrasoft-metric-icon terrasoft-metric-danger">
                                <i class="ti-arrow-down"></i>
                            </div>
                            <div class="terrasoft-metric-content">
                                <div class="terrasoft-metric-value">394</div>
                                <div class="terrasoft-metric-label">Total Issued</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity Card --}}
            <div class="terrasoft-details-card terrasoft-card-wide">
                <div class="terrasoft-card-header">
                    <h3 class="terrasoft-card-title">
                        <i class="ti-clock"></i>
                        Recent Activity
                    </h3>
                    <div class="terrasoft-card-actions">
                        <button class="terrasoft-btn terrasoft-btn-sm terrasoft-btn-outline">
                            <i class="ti-external-link"></i>
                            View All
                        </button>
                    </div>
                </div>
                <div class="terrasoft-card-content">
                    <div class="terrasoft-activity-list">
                        <div class="terrasoft-activity-item">
                            <div class="terrasoft-activity-icon terrasoft-activity-success">
                                <i class="ti-arrow-up"></i>
                            </div>
                            <div class="terrasoft-activity-content">
                                <div class="terrasoft-activity-title">Material Received</div>
                                <div class="terrasoft-activity-description">50 {{ $material->purchase_unit }} received from supplier</div>
                                <div class="terrasoft-activity-time">2 hours ago</div>
                            </div>
                        </div>

                        <div class="terrasoft-activity-item">
                            <div class="terrasoft-activity-icon terrasoft-activity-danger">
                                <i class="ti-arrow-down"></i>
                            </div>
                            <div class="terrasoft-activity-content">
                                <div class="terrasoft-activity-title">Material Issued</div>
                                <div class="terrasoft-activity-description">25 {{ $material->purchase_unit }} issued to Project A</div>
                                <div class="terrasoft-activity-time">5 hours ago</div>
                            </div>
                        </div>

                        <div class="terrasoft-activity-item">
                            <div class="terrasoft-activity-icon terrasoft-activity-info">
                                <i class="ti-edit"></i>
                            </div>
                            <div class="terrasoft-activity-content">
                                <div class="terrasoft-activity-title">Material Updated</div>
                                <div class="terrasoft-activity-description">Material details modified by Admin User</div>
                                <div class="terrasoft-activity-time">{{ $material->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        <div class="terrasoft-activity-item">
                            <div class="terrasoft-activity-icon terrasoft-activity-success">
                                <i class="ti-plus"></i>
                            </div>
                            <div class="terrasoft-activity-content">
                                <div class="terrasoft-activity-title">Material Created</div>
                                <div class="terrasoft-activity-description">Material added to inventory system</div>
                                <div class="terrasoft-activity-time">{{ $material->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>

        {{-- Action Buttons --}}
        <!-- <div class="terrasoft-action-bar">
           
            <div class="terrasoft-action-group">
                <button class="terrasoft-btn terrasoft-btn-danger" onclick="deleteMaterial()">
                    <i class="ti-trash"></i>
                    Delete Material
                </button>
                <a href="{{ route('admin.materials.edit', $material->id) }}" class="terrasoft-btn terrasoft-btn-primary">
                    <i class="ti-edit"></i>
                    Edit Material
                </a>
            </div>
        </div> -->
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="terrasoft-modal">
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
                    <p><strong>Are you sure you want to delete this material?</strong></p>
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

@endsection

<link rel="stylesheet" href="{{ asset('/css/materials.css') }}">
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

.terrasoft-metric {
    font-weight: 600;
    color: #059669;
}

.terrasoft-description {
    line-height: 1.6;
    color: #475569;
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

.terrasoft-status-success {
    background: #dcfce7;
    color: #166534;
}

.terrasoft-status-secondary {
    background: #f1f5f9;
    color: #64748b;
}

/* Conversion Display */
.terrasoft-conversion-display {
    margin-top: 20px;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 8px;
    padding: 16px;
}

.terrasoft-conversion-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    color: #0369a1;
    margin-bottom: 12px;
}

.terrasoft-conversion-formula {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
}

.terrasoft-conversion-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.terrasoft-conversion-value {
    font-size: 24px;
    font-weight: 700;
    color: #0c4a6e;
}

.terrasoft-conversion-unit {
    font-size: 14px;
    color: #0369a1;
    font-weight: 500;
}

.terrasoft-conversion-equals {
    font-size: 20px;
    color: #0369a1;
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

.terrasoft-activity-danger {
    background: #ef4444;
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

    .terrasoft-conversion-formula {
        flex-direction: column;
        gap: 8px;
    }
}

@media (max-width: 480px) {
    .terrasoft-metrics-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<script src="{{ asset('/js/materials.js') }}"></script>

<script>
// Delete material function
function deleteMaterial() {
    document.getElementById('deleteModal').classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}

function confirmDelete() {
    fetch('{{ route("admin.materials.destroy", $material->id) }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("admin.materials.index") }}';
        } else {
            alert('Error deleting material');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting material');
    });

    closeDeleteModal();
}


</script>
