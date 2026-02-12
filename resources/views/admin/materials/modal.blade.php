{{-- Create/Edit Material Modal --}}
<div id="materialModal" class="terrasoft-modal">
    <div class="terrasoft-modal-overlay" onclick="closeModal()">
        <div class="terrasoft-modal-container" onclick="event.stopPropagation()">
            {{-- Modal Header --}}
            <div class="terrasoft-modal-header">
                <div class="terrasoft-modal-title-section">
                    <div class="terrasoft-modal-icon">
                        <i class="ti-package"></i>
                    </div>
                    <h3 class="terrasoft-modal-title" id="modalTitle">Add New Material</h3>
                </div>
                <button class="terrasoft-modal-close-btn" onclick="closeModal()" type="button">
                    <i class="ti-x"></i>
                </button>
            </div>

            {{-- Modal Form --}}
            <form id="materialForm" class="terrasoft-modal-form" onsubmit="submitForm(event)">
              {{csrf_field()}}
                <input type="hidden" id="materialId" name="id">
                <input type="hidden" id="formMethod" name="_method" value="POST">
                
                <div class="terrasoft-modal-body">
                    {{-- Code and Name Row --}}
                    <div class="terrasoft-form-row">
                        <div class="terrasoft-form-group">
                            <label for="code" class="terrasoft-form-label">
                                Material Code <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text" 
                                   id="code" 
                                   name="code" 
                                   class="terrasoft-form-input" 
                                   placeholder="e.g., MAT001"
                                   required
                                   maxlength="50">
                            <div class="terrasoft-error-message" id="codeError"></div>
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="name" class="terrasoft-form-label">
                                Material Name <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="terrasoft-form-input" 
                                   placeholder="e.g., Steel Rod"
                                   required
                                   maxlength="255">
                            <div class="terrasoft-error-message" id="nameError"></div>
                        </div>
                    </div>

                    {{-- Units Row --}}
                    <div class="terrasoft-form-row">
                        <div class="terrasoft-form-group">
                            <label for="purchase_unit" class="terrasoft-form-label">
                                Purchase Unit <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text" 
                                   id="purchase_unit" 
                                   name="purchase_unit" 
                                   class="terrasoft-form-input" 
                                   placeholder="e.g., Box, Kg, Meter"
                                   required
                                   maxlength="50">
                            <div class="terrasoft-error-message" id="purchaseUnitError"></div>
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="base_unit" class="terrasoft-form-label">
                                Base Unit <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text" 
                                   id="base_unit" 
                                   name="base_unit" 
                                   class="terrasoft-form-input" 
                                   placeholder="e.g., Piece, Gram, CM"
                                   required
                                   maxlength="50">
                            <div class="terrasoft-error-message" id="baseUnitError"></div>
                        </div>
                    </div>

                    {{-- Conversion and Serial Row --}}
                    <div class="terrasoft-form-row">
                        <div class="terrasoft-form-group">
                            <label for="qty_per_purchase_unit" class="terrasoft-form-label">
                                Quantity per Purchase Unit <span class="terrasoft-required">*</span>
                            </label>
                            <input type="number" 
                                   id="qty_per_purchase_unit" 
                                   name="qty_per_purchase_unit" 
                                   class="terrasoft-form-input" 
                                   placeholder="e.g., 100"
                                   step="0.001"
                                   min="0.001"
                                   max="999999.999"
                                   required>
                            <div class="terrasoft-form-help">How many base units in one purchase unit</div>
                            <div class="terrasoft-error-message" id="qtyError"></div>
                        </div>

                        <div class="terrasoft-form-group">
                            <label class="terrasoft-form-label">Serial Number Required</label>
                            <div class="terrasoft-checkbox-group">
                                <label class="terrasoft-checkbox-label">

                                    <input type="checkbox" 
                                           id="has_serial" 
                                           name="has_serial" 
                                          class="terrasoft-checkbox">
                                    <span class="terrasoft-checkbox-custom"></span>
                                    <span class="terrasoft-checkbox-text">This material requires serial numbers</span>
                                </label>
                            </div>
                        </div>

                        
                    </div>

                    {{-- Description --}}
                    <div class="terrasoft-form-group">
                        <label for="description" class="terrasoft-form-label">
                            <i class="ti-file-text"></i>
                            Description
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  class="terrasoft-form-textarea" 
                                  rows="3" 
                                  placeholder="Enter material description..."
                                  maxlength="1000"></textarea>
                        <div class="terrasoft-error-message" id="descriptionError"></div>
                    </div>

                    {{-- Conversion Preview --}}
                    <div class="terrasoft-conversion-preview" id="conversionPreview" style="display: none;">
                        <div class="terrasoft-preview-header">
                            <i class="ti-calculator"></i>
                            <span>Conversion Preview</span>
                        </div>
                        <div class="terrasoft-preview-content" id="conversionText">
                            1 Box = 100 Pieces
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="terrasoft-modal-footer">
                    <button type="button" 
                            class="terrasoft-btn terrasoft-btn-secondary" 
                            onclick="closeModal()">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="terrasoft-btn terrasoft-btn-primary" 
                            id="submitBtn">
                        <i class="ti-check"></i>
                        <span id="submitText">Create Material</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Conversion Preview Styles */
.terrasoft-conversion-preview {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 8px;
    padding: 16px;
    margin-top: 16px;
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
    content: '✓';
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.terrasoft-checkbox-text {
    color: #374151;
}

/* Form Help Text */
.terrasoft-form-help {
    font-size: 12px;
    color: #6b7280;
    margin-top: 4px;
}
</style>

<script>
// Update conversion preview when units or quantity change
document.addEventListener('DOMContentLoaded', function() {
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