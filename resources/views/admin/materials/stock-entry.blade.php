@extends('admin.layout.base')

@section('title', 'Stock Entry')

@section('content')
<div class="terrasoft-main-content">
    <div class="terrasoft-page-container">
        {{-- Page Header --}}
        <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-inbox text-green-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">Stock Entry</h1>
                        <p class="terrasoft-page-subtitle">Add inventory to system</p>
                    </div>
                </div>
                <div class="terrasoft-header-actions">
                    <a href="{{ route('admin.materials.index') }}" class="terrasoft-btn terrasoft-btn-secondary">
                        <i class="ti-arrow-left"></i>
                        Back
                    </a>
                </div>
            </div>
        </div>

        {{-- Success Alert --}}
        @if(session('success'))
        <div class="terrasoft-alert terrasoft-alert-success">
            <i class="ti-check-circle"></i>
            <div>
                <strong>Success!</strong> {{ session('success') }}
            </div>
            <button class="terrasoft-alert-close" onclick="this.parentElement.style.display='none';">
                <i class="ti-x"></i>
            </button>
        </div>
        @endif

        {{-- Error Alert --}}
        @if(session('error'))
        <div class="terrasoft-alert terrasoft-alert-error">
            <i class="ti-alert-circle"></i>
            <div>
                <strong>Error!</strong> {{ session('error') }}
            </div>
            <button class="terrasoft-alert-close" onclick="this.parentElement.style.display='none';">
                <i class="ti-x"></i>
            </button>
        </div>
        @endif

        {{-- Form Container --}}
        <div class="terrasoft-form-container">
            <form id="openingStockForm" class="terrasoft-form">
               {{csrf_field()}}

                {{-- Location Section --}}
                <div class="terrasoft-form-section">
                    <div class="terrasoft-section-header">
                        <h3 class="terrasoft-section-title">
                            <i class="ti-map-pin"></i>
                            Location Details
                        </h3>
                        <p class="terrasoft-section-subtitle">Select district for inventory</p>
                    </div>

                    <div class="terrasoft-form-grid">
                        <div class="terrasoft-form-group">
                            <label for="district_id" class="terrasoft-form-label">
                                District <span class="terrasoft-required">*</span>
                            </label>
                            <select id="district_id" name="district_id" class="terrasoft-form-input" required>
                                <option value="">Select District</option>
                                  @foreach($district as $dist)
                                    <option value="{{ $dist->id}}">{{ $dist->name}}</option>
                                  @endforeach
                            </select>
                            <span class="terrasoft-error-message" id="district_id-error"></span>
                        </div>
                    </div>
                </div>

                {{-- Material Section --}}
                <div class="terrasoft-form-section">
                    <div class="terrasoft-section-header">
                        <h3 class="terrasoft-section-title">
                            <i class="ti-package"></i>
                            Material Details
                        </h3>
                        <p class="terrasoft-section-subtitle">Select material and enter quantity</p>
                    </div>

                    <div class="terrasoft-form-grid">
                        <div class="terrasoft-form-group">
                            <label for="material_id" class="terrasoft-form-label">
                                Material <span class="terrasoft-required">*</span>
                            </label>
                            <select id="material_id" name="material_id" class="terrasoft-form-input" required>
                                <option value="">Select Material</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}"
                                            data-has-serial="{{ $material->has_serial }}"
                                            data-purchase-unit="{{ $material->purchase_unit }}"
                                            data-base-unit="{{ $material->base_unit }}"
                                            data-qty-per-unit="{{ $material->qty_per_purchase_unit }}">
                                        {{ $material->code }} - {{ $material->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="terrasoft-error-message" id="material_id-error"></span>
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="quantity" class="terrasoft-form-label">
                                Quantity <span class="terrasoft-required">*</span>
                            </label>
                            <div class="terrasoft-input-group">
                                <input type="number"
                                       id="quantity"
                                       name="quantity"
                                       class="terrasoft-form-input"
                                       placeholder="0.000"
                                       step="0.001"
                                       min="0.001"
                                       required>
                                <span class="terrasoft-input-unit" id="quantityUnit">unit</span>
                            </div>
                            <span class="terrasoft-error-message" id="quantity-error"></span>
                        </div>
                    </div>

                    {{-- Material Info Card --}}
                    <div id="materialInfo" class="terrasoft-material-info" style="display: none;">
                        <div class="terrasoft-info-grid">
                            <div class="terrasoft-info-item">
                                <label>Material Code</label>
                                <span id="infoCode">-</span>
                            </div>
                            <div class="terrasoft-info-item">
                                <label>Purchase Unit</label>
                                <span id="infoPurchaseUnit">-</span>
                            </div>
                            <div class="terrasoft-info-item">
                                <label>Base Unit</label>
                                <span id="infoBaseUnit">-</span>
                            </div>
                            <div class="terrasoft-info-item">
                                <label>Conversion Rate</label>
                                <span id="infoConversion">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Serial Numbers Section (Conditional) --}}
                <div id="serialSection" class="terrasoft-form-section" style="display: none;">
                    <div class="terrasoft-section-header">
                        <h3 class="terrasoft-section-title">
                            <i class="ti-barcode"></i>
                            Serial Numbers
                        </h3>
                        <p class="terrasoft-section-subtitle">Enter serial numbers for this material</p>
                    </div>

                    <div class="terrasoft-serial-container">
                        <div id="serialInputs" class="terrasoft-serial-list">
                            {{-- Serial inputs will be dynamically added here --}}
                        </div>
                    </div>

                    <div class="terrasoft-serial-helper">
                        <i class="ti-info-circle"></i>
                        <span id="serialHelperText">Enter serial numbers to match quantity</span>
                    </div>
                </div>

                {{-- Remarks Section --}}
                <div class="terrasoft-form-section">
                    <div class="terrasoft-section-header">
                        <h3 class="terrasoft-section-title">
                            <i class="ti-file-text"></i>
                            Additional Information
                        </h3>
                        <p class="terrasoft-section-subtitle">Optional remarks and notes</p>
                    </div>

                    <div class="terrasoft-form-grid">
                        <div class="terrasoft-form-group terrasoft-form-group-full">
                            <label for="remarks" class="terrasoft-form-label">
                                Remarks
                            </label>
                            <textarea id="remarks"
                                      name="remarks"
                                      class="terrasoft-form-textarea"
                                      placeholder="Enter any remarks about this opening stock entry..."
                                      rows="3"
                                      maxlength="500"></textarea>
                            <span class="terrasoft-error-message" id="remarks-error"></span>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="terrasoft-form-actions">
                    <button type="button"
                            class="terrasoft-btn terrasoft-btn-secondary"
                            onclick="window.location.href='{{ route('admin.materials.index') }}'">
                        <i class="ti-x"></i>
                        Cancel
                    </button>
                    <button type="submit" class="terrasoft-btn terrasoft-btn-primary" id="submitBtn">
                        <i class="ti-check"></i>
                        Save Opening Stock
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
    position: relative;
}

.terrasoft-alert-success {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.terrasoft-alert-error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.terrasoft-alert i {
    font-size: 18px;
    flex-shrink: 0;
}

.terrasoft-alert-close {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    color: inherit;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.terrasoft-alert-close:hover {
    opacity: 1;
}

/* Form Container */
.terrasoft-form-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    margin-bottom: 24px;
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

/* Form Grid */
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
.terrasoft-form-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.terrasoft-form-input:disabled {
    background: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
}

.terrasoft-form-textarea {
    resize: vertical;
    min-height: 80px;
}

.terrasoft-error-message {
    font-size: 12px;
    color: #ef4444;
    display: none;
}

.terrasoft-error-message.show {
    display: block;
}

/* Input Group */
.terrasoft-input-group {
    display: flex;
    align-items: center;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: white;
    overflow: hidden;
}

.terrasoft-input-group .terrasoft-form-input {
    flex: 1;
    border: none;
    box-shadow: none;
}

.terrasoft-input-group .terrasoft-form-input:focus {
    border: none;
    box-shadow: none;
}

.terrasoft-input-unit {
    padding: 0 16px;
    background: #f3f4f6;
    color: #6b7280;
    font-size: 13px;
    font-weight: 500;
    border-left: 1px solid #d1d5db;
    white-space: nowrap;
}

/* Material Info Card */
.terrasoft-material-info {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 8px;
    padding: 16px;
    margin-top: 16px;
}

.terrasoft-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
}

.terrasoft-info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.terrasoft-info-item label {
    font-size: 12px;
    font-weight: 500;
    color: #0369a1;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.terrasoft-info-item span {
    font-size: 14px;
    font-weight: 600;
    color: #0c4a6e;
}

/* Serial Numbers Section */
.terrasoft-serial-container {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
}

.terrasoft-serial-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
}

.terrasoft-serial-input-wrapper {
    display: flex;
    gap: 8px;
    align-items: flex-start;
}

.terrasoft-serial-input-wrapper input {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
    font-family: 'Monaco', 'Menlo', monospace;
}

.terrasoft-serial-input-wrapper input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.terrasoft-serial-input-wrapper input.error {
    border-color: #ef4444;
    background: #fef2f2;
}

.terrasoft-serial-input-wrapper input.success {
    border-color: #22c55e;
    background: #f0fdf4;
}

.terrasoft-serial-input-wrapper button {
    padding: 8px 12px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
}

.terrasoft-serial-input-wrapper button:hover {
    background: #dc2626;
}

.terrasoft-serial-helper {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    font-size: 13px;
    color: #1e40af;
}

.terrasoft-serial-helper i {
    color: #3b82f6;
}

/* Form Actions */
.terrasoft-form-actions {
    padding: 24px 32px;
    background: #f8fafc;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

/* Stats Grid */
.terrasoft-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.terrasoft-stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.terrasoft-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.terrasoft-stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
}

.terrasoft-stat-label {
    font-size: 12px;
    color: #64748b;
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

    .terrasoft-serial-list {
        grid-template-columns: 1fr;
    }

    .terrasoft-info-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .terrasoft-stats-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .terrasoft-info-grid {
        grid-template-columns: 1fr;
    }

    .terrasoft-serial-input-wrapper {
        flex-direction: column;
    }

    .terrasoft-serial-input-wrapper button {
        width: 100%;
    }
}
</style>
<script src="{{ asset('/js/materials.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('openingStockForm');
    const districtSelect = document.getElementById('district_id');
    const materialSelect = document.getElementById('material_id');
    const quantityInput = document.getElementById('quantity');
    const serialSection = document.getElementById('serialSection');
    const serialInputs = document.getElementById('serialInputs');
    const materialInfo = document.getElementById('materialInfo');
    const submitBtn = document.getElementById('submitBtn');


   
    // Load material details on material change
    materialSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const hasSerial = option.dataset.hasSerial === '1';
        const quantity = quantityInput.value || 0;

        if (this.value) {
            updateMaterialInfo(option);
            updateUnitDisplay(option);
        }

        // Show/hide serial section based on material selection and has_serial flag
        if (hasSerial && quantity > 0) {
            showSerialSection(parseInt(quantity));
        } else if (hasSerial) {
            serialSection.style.display = 'none';
        } else {
            serialSection.style.display = 'none';
        }

        clearSerialErrors();
    });

    // Handle quantity change
    quantityInput.addEventListener('input', function() {
        const quantity = parseInt(this.value) || 0;
        const option = materialSelect.options[materialSelect.selectedIndex];
        const hasSerial = option.dataset.hasSerial === '1';

        if (hasSerial && quantity > 0) {
            showSerialSection(quantity);
        } else {
            serialSection.style.display = 'none';
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="ti-loader"></i> Saving...';

        const formData = new FormData(form);
        const option = materialSelect.options[materialSelect.selectedIndex];
        const hasSerial = option.dataset.hasSerial === '1';

        // Collect serial numbers if required
        if (hasSerial) {
            const serials = [];
            document.querySelectorAll('.terrasoft-serial-input-wrapper input').forEach(input => {
                if (input.value.trim()) {
                    serials.push(input.value.trim());
                }
            });
            formData.append('serials', JSON.stringify(serials));
        }

        fetch('{{ route('admin.stock-entry') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                setTimeout(() => {
                    window.location.href = '{{ route('admin.stock-entry') }}';
                }, 1500);
            } else {
                showError(data.message);
                if (data.errors) {
                    displayErrors(data.errors);
                }
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = '<i class="ti-check"></i> Save Opening Stock';
            }
        })
        .catch(error => {
            console.error('Error:',error);
            showError('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = '<i class="ti-check"></i> Save Opening Stock';
        });
    });

    function updateMaterialInfo(option) {
        document.getElementById('infoCode').textContent = option.textContent.split(' - ')[0];
        document.getElementById('infoPurchaseUnit').textContent = option.dataset.purchaseUnit;
        document.getElementById('infoBaseUnit').textContent = option.dataset.baseUnit;
        document.getElementById('infoConversion').textContent =
            `1 ${option.dataset.purchaseUnit} = ${option.dataset.qtyPerUnit} ${option.dataset.baseUnit}`;
        materialInfo.style.display = 'block';
    }

    function updateUnitDisplay(option) {
        document.getElementById('quantityUnit').textContent = option.dataset.purchaseUnit || 'unit';
    }

    function showSerialSection(quantity) {
        serialSection.style.display = 'block';
        serialInputs.innerHTML = '';

        for (let i = 0; i < quantity; i++) {
            const wrapper = document.createElement('div');
            wrapper.className = 'terrasoft-serial-input-wrapper';
            wrapper.innerHTML = `
                <input type="text"
                       class="serial-input"
                       placeholder="Serial #${i + 1}"
                       maxlength="100"
                       autocomplete="off">
                <button type="button" onclick="this.parentElement.remove()">
                    <i class="ti-x"></i>
                </button>
            `;
            serialInputs.appendChild(wrapper);
        }

        document.getElementById('serialHelperText').textContent =
            `Enter ${quantity} serial number(s) to match quantity`;
    }

    function clearSerialErrors() {
        document.querySelectorAll('.serial-input').forEach(input => {
            input.classList.remove('error', 'success');
        });
    }

    function displayErrors(errors) {
        for (let field in errors) {
            const errorElement = document.getElementById(`${field}-error`);
            if (errorElement) {
                errorElement.textContent = errors[field][0];
                errorElement.classList.add('show');
            }
        }
    }

    function showSuccess(message) {
        const alert = document.createElement('div');
        alert.className = 'terrasoft-alert terrasoft-alert-success';
        alert.innerHTML = `
            <i class="ti-check-circle"></i>
            <div>${message}</div>
        `;
        document.querySelector('.terrasoft-page-header').insertAdjacentElement('afterend', alert);
    }

    function showError(message) {
        const alert = document.createElement('div');
        alert.className = 'terrasoft-alert terrasoft-alert-error';
        alert.innerHTML = `
            <i class="ti-alert-circle"></i>
            <div>${message}</div>
            <button class="terrasoft-alert-close" onclick="this.parentElement.remove();">
                <i class="ti-x"></i>
            </button>
        `;
        document.querySelector('.terrasoft-page-header').insertAdjacentElement('afterend', alert);
    }
});
</script>
