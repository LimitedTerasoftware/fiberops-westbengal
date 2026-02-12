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
                        <h1 class="terrasoft-page-title">{{ $transaction->material->name }}</h1>
                        <p class="terrasoft-page-subtitle">Stock Transaction Details</p>
                    </div>
                </div>
                <div class="terrasoft-header-actions">
                   
                    <a href="{{ route('admin.stock-entry.index') }}" class="terrasoft-btn terrasoft-btn-secondary">
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
                            <label class="terrasoft-detail-label">Material Code</label>
                            <div class="terrasoft-detail-value terrasoft-code">{{ $transaction->material->code ?? '-' }} </div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Material Name</label>
                            <div class="terrasoft-detail-value">{{ $transaction->material->name ?? '-' }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">District Name</label>
                            <div class="terrasoft-detail-value">{{ $transaction->district->name ?? '-' }}</div>
                        </div>
                        
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Created Date</label>
                            <div class="terrasoft-detail-value">{{ $transaction->created_at->format('d M Y h:i A') }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Last Updated</label>
                            <div class="terrasoft-detail-value">{{ $transaction->updated_at->format('d M Y, H:i') }}</div>
                        </div>
                        @if($transaction->reference_type )
                        <div class="terrasoft-detail-item terrasoft-detail-full">
                            <label class="terrasoft-detail-label">Reference</label>
                            <div class="terrasoft-detail-value terrasoft-description">{{ $transaction->reference_type ?? '-' }} / {{ $transaction->reference_id ?? '-' }}</div>
                        </div>
                        @endif
                        @if($transaction->remarks  )
                        <div class="terrasoft-detail-item terrasoft-detail-full">
                            <label class="terrasoft-detail-label">Remarks</label>
                            <div class="terrasoft-detail-value terrasoft-description">{{ $transaction->remarks  ?? '-' }}</div>
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
                        Stock Information
                    </h3>
                </div>
                <div class="terrasoft-card-content">
                    <div class="terrasoft-detail-grid">
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Transaction Type</label>
                            <div class="terrasoft-detail-value terrasoft-metric">{{$transaction->transaction_type}}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Base Unit</label>
                            <div class="terrasoft-detail-value terrasoft-metric">{{ $transaction->material->base_unit }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Quantity per Base Unit</label>
                            <div class="terrasoft-detail-value terrasoft-metric">{{ number_format($transaction->quantity, 3) }}</div>
                        </div>
                        <div class="terrasoft-detail-item">
                            <label class="terrasoft-detail-label">Serial Number Required</label>
                            <div class="terrasoft-detail-value">
                                 @if($transaction->serial->count())
                                    <ul>
                                        @foreach($transaction->serial as $serial)
                                        <span class="terrasoft-status-badge terrasoft-status-success">
                                            <li>{{ $serial->serial_number }} ({{ $serial->received_quantity }})</li>
                                        </span>
                                        @endforeach
                                    </ul>
                                @else
                                    -
                                @endif
                             
                            </div>
                        </div>
                    </div>

                    {{-- Conversion Display --}}
                    <!-- <div class="terrasoft-conversion-display">
                        <div class="terrasoft-conversion-header">
                            <i class="ti-calculator"></i>
                            <span>Unit Conversion</span>
                        </div>
                        <div class="terrasoft-conversion-formula">
                            <div class="terrasoft-conversion-item">
                                <span class="terrasoft-conversion-value">1</span>
                                <span class="terrasoft-conversion-unit">{{ $transaction->material->purchase_unit }}</span>
                            </div>
                            <div class="terrasoft-conversion-equals">
                                <i class="ti-equal"></i>
                            </div>
                            <div class="terrasoft-conversion-item">
                                <span class="terrasoft-conversion-value">{{ number_format($transaction->material->qty_per_purchase_unit,2) }}</span>
                                <span class="terrasoft-conversion-unit">{{ $transaction->material->base_unit }}</span>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>

           
        </div>

        {{-- Action Buttons --}}
        <!-- <div class="terrasoft-action-bar">
           
            <div class="terrasoft-action-group">
                <button class="terrasoft-btn terrasoft-btn-danger" onclick="deleteMaterial()">
                    <i class="ti-trash"></i>
                    Delete Material
                </button>
                <a href="{{ route('admin.materials.edit', $transaction->material->id) }}" class="terrasoft-btn terrasoft-btn-primary">
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
    fetch('{{ route("admin.materials.destroy", $transaction->material->id) }}', {
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
