@extends('admin.layout.base')

@section('title', 'Edit Material')

@section('content')
<div class="terrasoft-main-content">
    <div class="terrasoft-page-container">
        {{-- Page Header --}}
        <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-marker-alt text-orange-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">Edit Material</h1>
                        <p class="terrasoft-page-subtitle">Update the configuration for {{ $material->name }}</p>
                    </div>
                </div>
                <div class="terrasoft-header-actions">
                    <a href="{{ route('admin.materials.show', $material->id) }}" class="terrasoft-btn terrasoft-btn-secondary">
                        <i class="ti-eye"></i>
                        View Details
                    </a>
                    <a href="{{ route('admin.materials.index') }}" class="terrasoft-btn terrasoft-btn-secondary">
                        <i class="ti-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        {{-- Status Alert --}}
        <!-- <div class="terrasoft-alert terrasoft-alert-info">
            <i class="ti-info-circle"></i>
            <div>
                <strong>Last Updated:</strong> {{ $material->updated_at->format('d M Y, H:i') }}
            </div>
        </div> -->

        {{-- Form Container --}}
        <div class="terrasoft-form-container">
            <form action="{{ route('admin.materials.update', $material->id) }}" method="POST" class="terrasoft-form" id="materialEditForm">
                {{csrf_field()}}
                <input type="hidden" name="_method" value="PUT">


                {{-- Basic Information Section --}}
                <div class="terrasoft-form-section">
                   

                    <div class="terrasoft-form-grid">
                        <div class="terrasoft-form-group">
                            <label for="code" class="terrasoft-form-label">
                                Material Code <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text"
                                   id="code"
                                   name="code"
                                   class="terrasoft-form-input {{ $errors->has('code') ? 'terrasoft-input-error' : '' }}"
                                   placeholder="e.g., MAT001"
                                   value="{{ old('code', $material->code) }}"
                                   required
                                   maxlength="50">
                             @if($errors->has('code'))
                                <span class="terrasoft-error-message">{{ $errors->first('code') }}</span>
                              @endif
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="name" class="terrasoft-form-label">
                                Material Name <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="terrasoft-form-input {{ $errors->has('name') ? 'terrasoft-input-error' : '' }}"
                                   placeholder="e.g., Steel Rod"
                                   value="{{ old('name', $material->name) }}"
                                   required
                                   maxlength="255">
                             @if($errors->has('name'))
                                <span class="terrasoft-error-message">{{ $errors->first('name') }}</span>
                              @endif
                        </div>
                    </div>
               

                {{-- Unit Information Section --}}
               
                   

                    <div class="terrasoft-form-grid">
                        <div class="terrasoft-form-group">
                            <label for="purchase_unit" class="terrasoft-form-label">
                                Purchase Unit <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text"
                                   id="purchase_unit"
                                   name="purchase_unit"
                                   class="terrasoft-form-input  {{ $errors->has('purchase_unit') ? 'terrasoft-input-error' : '' }}"
                                   placeholder="e.g., Box, Kg, Meter"
                                   value="{{ old('purchase_unit', $material->purchase_unit) }}"
                                   required
                                   maxlength="50">
                              @if($errors->has('purchase_unit'))
                                <span class="terrasoft-error-message">{{ $errors->first('purchase_unit') }}</span>
                              @endif
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="base_unit" class="terrasoft-form-label">
                                Base Unit <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text"
                                   id="base_unit"
                                   name="base_unit"
                                   class="terrasoft-form-input  {{ $errors->has('base_unit') ? 'terrasoft-input-error' : '' }}"
                                   placeholder="e.g., Piece, Gram, CM"
                                   value="{{ old('base_unit', $material->base_unit) }}"
                                   required
                                   maxlength="50">
                            @if($errors->has('base_unit'))
                                <span class="terrasoft-error-message">{{ $errors->first('base_unit') }}</span>
                              @endif
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="qty_per_purchase_unit" class="terrasoft-form-label">
                                Quantity per Purchase Unit <span class="terrasoft-required">*</span>
                            </label>
                            <input type="number"
                                   id="qty_per_purchase_unit"
                                   name="qty_per_purchase_unit"
                                   class="terrasoft-form-input {{ $errors->has('qty_per_purchase_unit') ? 'terrasoft-input-error' : '' }}"
                                   placeholder="e.g., 100"
                                   step="0.001"
                                   min="0.001"
                                   max="999999.999"
                                   value="{{ old('qty_per_purchase_unit', $material->qty_per_purchase_unit) }}"
                                   required>
                            <div class="terrasoft-form-help">How many base units in one purchase unit</div>
                             @if($errors->has('qty_per_purchase_unit'))
                                <span class="terrasoft-error-message">{{ $errors->first('qty_per_purchase_unit') }}</span>
                              @endif
                        </div>

                       
                    </div>

                    {{-- Conversion Preview --}}
                    <div class="terrasoft-conversion-preview" id="conversionPreview">
                        <div class="terrasoft-preview-header">
                            <i class="ti-calculator"></i>
                            <span>Conversion Preview</span>
                        </div>
                        <div class="terrasoft-preview-content" id="conversionText">
                            1 {{ $material->purchase_unit }} = {{ number_format($material->qty_per_purchase_unit, 3) }} {{ $material->base_unit }}
                        </div>
                    </div>
                </div>

                {{-- Additional Information Section --}}
                <div class="terrasoft-form-section">
                   
                    <div class="terrasoft-form-grid">
                        <div class="terrasoft-form-group terrasoft-form-group-full">
                            <label for="description" class="terrasoft-form-label">
                                Description
                            </label>
                            <textarea id="description"
                                      name="description"
                                      class="terrasoft-form-textarea {{ $errors->has('description') ? 'terrasoft-input-error' : '' }}"
                                      placeholder="Enter material description..."
                                      rows="4"
                                      maxlength="1000">{{ old('description', $material->description) }}</textarea>
                              @if($errors->has('description'))
                                <span class="terrasoft-error-message">{{ $errors->first('description') }}</span>
                              @endif
                        </div>
                    </div>
                </div>


                {{-- Form Actions --}}
                <div class="terrasoft-form-actions">
                    <button type="button"
                            class="terrasoft-btn terrasoft-btn-secondary"
                            onclick="window.location.href='{{ route('admin.materials.show', $material->id) }}'">
                        <i class="ti-x"></i>
                        Cancel
                    </button>
                    <button type="submit" class="terrasoft-btn terrasoft-btn-primary" id="updateBtn">
                        <i class="ti-check"></i>
                        Update Material
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


<link rel="stylesheet" href="{{ asset('/css/materials.css') }}">
<style>
/* Alert Styles */
.terrasoft-alert {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
}

.terrasoft-alert-info {
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}

.terrasoft-alert i {
    font-size: 18px;
    flex-shrink: 0;
}

/* Form Styles */
.terrasoft-form-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.terrasoft-form {
    padding: 0;
}

.terrasoft-form-section {
    padding: 32px;
    border-bottom: 1px solid #f1f5f9;
}

.terrasoft-form-section:last-child {
    border-bottom: none;
}

.terrasoft-section-header {
    margin-bottom: 24px;
}

.terrasoft-section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 4px 0;
}

.terrasoft-section-title i {
    color: #3b82f6;
}

.terrasoft-section-subtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

.terrasoft-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
}

.terrasoft-form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.terrasoft-form-group-full {
    grid-column: 1 / -1;
}

.terrasoft-form-label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 4px;
}

.terrasoft-required {
    color: #ef4444;
}

.terrasoft-form-input,
.terrasoft-form-select,
.terrasoft-form-textarea {
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    transition: all 0.2s ease;
    font-family: inherit;
}

.terrasoft-form-input:focus,
.terrasoft-form-select:focus,
.terrasoft-form-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.terrasoft-input-error {
    border-color: #ef4444;
}

.terrasoft-input-error:focus {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.terrasoft-error-message {
    font-size: 12px;
    color: #ef4444;
}

.terrasoft-form-textarea {
    resize: vertical;
    min-height: 100px;
}

.terrasoft-form-help {
    font-size: 12px;
    color: #6b7280;
    margin-top: -4px;
}

/* Conversion Preview Styles */
.terrasoft-conversion-preview {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 8px;
    padding: 16px;
    margin-top: 24px;
}

.terrasoft-preview-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    color: #0369a1;
    margin-bottom: 8px;
}

.terrasoft-preview-content {
    font-size: 14px;
    color: #0c4a6e;
    font-weight: 500;
}

/* Checkbox Styles */
.terrasoft-checkbox-group {
    margin-top: 8px;
}

.terrasoft-checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 14px;
}

.terrasoft-checkbox {
    display: none;
}

.terrasoft-checkbox-custom {
    width: 18px;
    height: 18px;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.terrasoft-checkbox:checked + .terrasoft-checkbox-custom {
    background: #3b82f6;
    border-color: #3b82f6;
}

.terrasoft-checkbox:checked + .terrasoft-checkbox-custom::after {
    content: '?';
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.terrasoft-checkbox-text {
    color: #374151;
}

/* Change Log Styles */
.terrasoft-change-log {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.terrasoft-change-item {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 3px solid #e2e8f0;
}

.terrasoft-change-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.terrasoft-change-content {
    flex: 1;
}

.terrasoft-change-title {
    font-weight: 500;
    color: #1e293b;
    margin-bottom: 4px;
}

.terrasoft-change-details {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 4px;
}

.terrasoft-change-meta {
    font-size: 12px;
    color: #9ca3af;
}

.terrasoft-form-actions {
    padding: 24px 32px;
    background: #f8fafc;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

/* Loading State */
.terrasoft-btn.loading {
    opacity: 0.7;
    cursor: not-allowed;
}

.terrasoft-btn.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Header Actions */
.terrasoft-header-actions {
    display: flex;
    gap: 12px;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .terrasoft-form-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .terrasoft-form-section {
        padding: 24px 16px;
    }

    .terrasoft-form-actions {
        padding: 16px;
        flex-direction: column;
    }

    .terrasoft-header-content {
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }

    .terrasoft-header-actions {
        flex-direction: column;
        width: 100%;
    }
}
</style>
<script src="{{ asset('/js/materials.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('materialEditForm');
    const updateBtn = document.getElementById('updateBtn');

    // Track form changes
    let originalFormData = new FormData(form);
    let hasChanges = false;

    // Monitor form changes
    form.addEventListener('input', function() {
        const currentFormData = new FormData(form);
        hasChanges = false;

        for (let [key, value] of currentFormData.entries()) {
            if (originalFormData.get(key) !== value) {
                hasChanges = true;
                break;
            }
        }

        // Update button state
        if (hasChanges) {
            updateBtn.classList.add('terrasoft-btn-warning');
            updateBtn.innerHTML = '<i class="ti-alert-circle"></i> Save Changes';
        } else {
            updateBtn.classList.remove('terrasoft-btn-warning');
            updateBtn.innerHTML = '<i class="ti-check"></i> Update Material';
        }
    });
   function beforeUnloadHandler(e) {
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    }

    window.addEventListener('beforeunload', beforeUnloadHandler);
    // Form submission
    form.addEventListener('submit', function(e) {
        if (!hasChanges) {
            e.preventDefault();
            alert('No changes detected. Please make some changes before updating.');
            return;
        }
         hasChanges = false;
        window.removeEventListener('beforeunload', beforeUnloadHandler);
        // Show loading state
        updateBtn.classList.add('loading');
        updateBtn.innerHTML = '<i class="ti-loader"></i> Updating...';
        updateBtn.disabled = true;
    });

    

    // Update conversion preview
    const purchaseUnit = document.getElementById('purchase_unit');
    const baseUnit = document.getElementById('base_unit');
    const qtyPerUnit = document.getElementById('qty_per_purchase_unit');
    const preview = document.getElementById('conversionPreview');
    const previewText = document.getElementById('conversionText');

    // function updatePreview() {
    //     const purchase = purchaseUnit.value.trim();
    //     const base = baseUnit.value.trim();
    //     const qty = qtyPerUnit.value;

    //     if (purchase && base && qty) {
    //         previewText.textContent = `1 ${purchase} = ${qty} ${base}`;
    //         preview.style.display = 'block';
    //     } else {
    //         preview.style.display = 'none';
    //     }
    // }
     function updatePreview() {
        const purchase = purchaseUnit.value.trim();
        const base = baseUnit.value.trim();
        const qty = parseFloat(qtyPerUnit.value);

        if (!purchase || !base || isNaN(qty) || qty <= 0) {
            preview.style.display = 'none';
            return;
        }

        // If purchase unit and base unit are the same, always 1
        if (purchase.toLowerCase() === base.toLowerCase()) {
            previewText.textContent = `1 ${purchase} = 1 ${base}`;
            qtyPerUnit.value = 1; // enforce 1 in the input
        } else {
            previewText.textContent = `1 ${purchase} = ${qty} ${base}`;
        }

        preview.style.display = 'block';
    }

    purchaseUnit.addEventListener('input', updatePreview);
    baseUnit.addEventListener('input', updatePreview);
    qtyPerUnit.addEventListener('input', updatePreview);

    // Auto-uppercase material code
    document.getElementById('code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>

<style>
.terrasoft-btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.terrasoft-btn-warning:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
}
</style>
