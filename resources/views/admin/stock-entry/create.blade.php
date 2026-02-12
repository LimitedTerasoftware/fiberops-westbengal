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
                        <i class="ti-layers-alt text-green-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">Stock Entry</h1>
                        <p class="terrasoft-page-subtitle">Add multiple materials to inventory in one entry</p>
                    </div>
                </div>
                <div class="terrasoft-header-actions">
                    <a href="{{ route('admin.stock-entry.index') }}" class="terrasoft-btn terrasoft-btn-secondary">
                        <i class="ti-list"></i>
                        View All Entries
                    </a>
                </div>
            </div>
        </div>

        {{-- Form Container --}}
        <div class="terrasoft-form-container">
            <form id="stockEntryForm" class="terrasoft-form">
                {{csrf_field()}}

                {{-- Location & Type Section --}}
                <div class="terrasoft-form-section">
                    <div class="terrasoft-section-header">
                        <h3 class="terrasoft-section-title">
                            <i class="ti-settings"></i>
                            Entry Details
                        </h3>
                        <p class="terrasoft-section-subtitle">Configure location and transaction type</p>
                    </div>

                    <div class="terrasoft-form-grid">
                      

                        <div class="terrasoft-form-group">
                            <label for="district_id" class="terrasoft-form-label">
                                District <span class="terrasoft-required">*</span>
                            </label>
                            <select id="district_id" name="district_id" class="terrasoft-form-input" required >
                                <option value="">Select District</option>
                                   @foreach($district as $dist)
                                    <option value="{{ $dist->id}}">{{ $dist->name}}</option>
                                  @endforeach
                            </select>
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="transaction_type" class="terrasoft-form-label">
                                Transaction Type <span class="terrasoft-required">*</span>
                            </label>
                            <select id="transaction_type" name="transaction_type" class="terrasoft-form-input" required>
                                <option value="">Select Type</option>
                                @foreach($transactionTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="terrasoft-form-group" id="empGroup" style="display:none;">
                            <label for="emp" class="terrasoft-form-label">
                                Employee <span class="terrasoft-required">*</span>
                            </label>

                            <select id="emp" name="emp" class="terrasoft-form-input">
                                <option value="">Select</option>
                            </select>
                        </div>
                    </div>

                    <div class="terrasoft-form-grid">
                        <div class="terrasoft-form-group">
                            <label for="reference_type" class="terrasoft-form-label">
                                Reference Type
                            </label>
                            <input type="text"
                                   id="reference_type"
                                   name="reference_type"
                                   class="terrasoft-form-input"
                                   placeholder="e.g., PO, GR, Invoice"
                                   maxlength="50">
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="reference_id" class="terrasoft-form-label">
                                Reference ID
                            </label>
                            <input type="text"
                                   id="reference_id"
                                   name="reference_id"
                                   class="terrasoft-form-input"
                                   placeholder="e.g., PO-2024-001"
                                   maxlength="100">
                        </div>
                    </div>

                    <div class="terrasoft-form-grid">
                        <div class="terrasoft-form-group terrasoft-form-group-full">
                            <label for="remarks" class="terrasoft-form-label">
                                Remarks
                            </label>
                            <textarea id="remarks"
                                      name="remarks"
                                      class="terrasoft-form-textarea"
                                      placeholder="Enter any remarks about this entry..."
                                      rows="2"
                                      maxlength="500"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Line Items Section --}}
                <div class="terrasoft-form-section">
                    <div class="terrasoft-section-header">
                        <div>
                            <h3 class="terrasoft-section-title">
                                <i class="ti-package"></i>
                                Materials
                            </h3>
                            <p class="terrasoft-section-subtitle">Add materials to this entry</p>
                        </div>
                    </div>

                    {{-- Line Items Table --}}
                    <div class="terrasoft-line-items-container">
                        <div class="terrasoft-table-responsive">
                            <table class="terrasoft-line-items-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Material</th>
                                        <th>Code</th>
                                        <th>Qty</th>
                                        <th>Unit</th>
                                        <th>Current Stock</th>
                                        <th>Serials</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="lineItemsBody">
                                    {{-- Line items will be dynamically added here --}}
                                </tbody>
                            </table>
                        </div>

                        <div class="terrasoft-add-row-container">
                            <button type="button" class="terrasoft-btn terrasoft-btn-outline" id="addRowBtn">
                                <i class="ti-plus"></i>
                                Add Material
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="terrasoft-form-actions">
                    <button type="button"
                            class="terrasoft-btn terrasoft-btn-secondary"
                            onclick="window.location.href='{{ route('admin.stock-entry.index') }}'">
                        <i class="ti-x"></i>
                        Cancel
                    </button>
                    <button type="submit" class="terrasoft-btn terrasoft-btn-primary" id="submitBtn">
                        <i class="ti-check"></i>
                        Save Stock Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Line Item Template (hidden) --}}
<template id="lineItemTemplate">
    <tr class="terrasoft-line-item" data-row-index="0">
        <td class="terrasoft-row-number">1</td>
        <td>
            <select name="items[0][material_id]" class="terrasoft-table-input material-select" required>
                <option value="">Select Material</option>
                @foreach($materials as $material)
                    <option value="{{ $material->id }}"
                            data-code="{{ $material->code }}"
                            data-unit="{{ $material->purchase_unit }}"
                            data-has-serial="{{ $material->has_serial }}">
                        {{ $material->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td class="terrasoft-material-code">-</td>
        <td>
            <input type="number"
                   name="items[0][quantity]"
                   class="terrasoft-table-input quantity-input"
                   placeholder="0"
                   step="0.001"
                   min="0.001"
                   required>
        </td>
        <td class="terrasoft-material-unit">unit</td>
        <td class="terrasoft-current-stock">-</td>
        <td class="terrasoft-serial-indicator">-</td>
        <td>
            <button type="button" class="terrasoft-btn terrasoft-btn-sm terrasoft-btn-danger remove-row">
                <i class="ti-trash"></i>
            </button>
        </td>
    </tr>

    <tr class="terrasoft-serial-row" style="display: none;" data-row-index="0">
        <td colspan="8">
            <div class="terrasoft-serial-inputs-wrapper">
                <label class="terrasoft-serial-label">Serial Numbers & Quantity:</label>
                <div class="terrasoft-serial-inputs" id="serialInputs0"></div>
            </div>
        </td>
    </tr>
</template>
{{-- Toast Notifications --}}
<div id="toast" class="terrasoft-toast">
    <div class="terrasoft-toast-content">
        <div class="terrasoft-toast-icon" id="toastIcon">
            <i class="ti-check"></i>
        </div>
        <div class="terrasoft-toast-message" id="toastMessage">
            Success message
        </div>
        <button class="terrasoft-toast-close" onclick="closeToast()">
            <i class="ti-x"></i>
        </button>
    </div>
</div>
@endsection

<link rel="stylesheet" href="{{ asset('/css/materials.css') }}">
<style>
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
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-bottom: 20px;
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
    min-height: 60px;
}

/* Line Items Table */
.terrasoft-line-items-container {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 16px;
}

.terrasoft-table-responsive {
    overflow-x: auto;
}

.terrasoft-line-items-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.terrasoft-line-items-table thead {
    background: #f1f5f9;
    border-bottom: 2px solid #e2e8f0;
}

.terrasoft-line-items-table th {
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #1e293b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.terrasoft-line-items-table td {
    padding: 12px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
}

.terrasoft-line-item:hover {
    background: #f8fafc;
}

.terrasoft-serial-row {
    background: #eff6ff;
}

.terrasoft-row-number {
    font-weight: 600;
    color: #3b82f6;
    width: 40px;
}

.terrasoft-material-code,
.terrasoft-material-unit,
.terrasoft-current-stock,
.terrasoft-serial-indicator {
    font-size: 13px;
    color: #64748b;
}

.terrasoft-table-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
    background: white;
}

.terrasoft-table-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

/* Serial Numbers */
.terrasoft-serial-inputs-wrapper {
    padding: 12px;
    background: white;
    border-radius: 6px;
}

.terrasoft-serial-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #0369a1;
    margin-bottom: 8px;
    text-transform: uppercase;
}

.terrasoft-serial-inputs {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 8px;
}

.terrasoft-serial-input {
    padding: 8px 10px;
    border: 1px solid #bae6fd;
    border-radius: 4px;
    font-size: 12px;
    font-family: 'Monaco', 'Menlo', monospace;
    background: #f0f9ff;
}

.terrasoft-serial-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

/* Add Row Button */
.terrasoft-add-row-container {
    padding: 16px;
    background: white;
    text-align: center;
    border-top: 1px solid #e2e8f0;
}

.terrasoft-btn-outline {
    background: transparent;
    border: 1px solid #d1d5db;
    color: #374151;
}

.terrasoft-btn-outline:hover {
    background: #f3f4f6;
}

/* Button Styles */
.terrasoft-btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.terrasoft-btn-danger {
    background: #ef4444;
    color: white;
}

.terrasoft-btn-danger:hover {
    background: #dc2626;
}

/* Form Actions */
.terrasoft-form-actions {
    padding: 24px 32px;
    background: #f8fafc;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

/* Mobile Responsive */
@media (max-width: 1024px) {
    .terrasoft-form-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .terrasoft-line-items-table {
        font-size: 12px;
    }

    .terrasoft-line-items-table th,
    .terrasoft-line-items-table td {
        padding: 8px;
    }

    .terrasoft-serial-inputs {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
}

@media (max-width: 768px) {
    .terrasoft-form-grid {
        grid-template-columns: 1fr;
    }

    .terrasoft-form-section {
        padding: 24px 16px;
    }

    .terrasoft-form-actions {
        padding: 16px;
        flex-direction: column;
    }

    .terrasoft-line-items-table {
        font-size: 11px;
    }

    .terrasoft-line-items-table th,
    .terrasoft-line-items-table td {
        padding: 6px;
    }

    .terrasoft-table-input {
        font-size: 12px;
        padding: 6px 8px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('stockEntryForm');
    const districtSelect = document.getElementById('district_id');
    const lineItemsBody = document.getElementById('lineItemsBody');
    const addRowBtn = document.getElementById('addRowBtn');
    const template = document.getElementById('lineItemTemplate');
    const submitBtn = document.getElementById('submitBtn');

    let rowCount = 0;

    const transactionType = document.getElementById('transaction_type');
    const empGroup = document.getElementById('empGroup');

    transactionType.addEventListener('change', function() {
        if (this.value === 'ISSUE') {
            empGroup.style.display = '';
            document.getElementById('emp').setAttribute('required', 'required');
        } else {
            empGroup.style.display = 'none';
            document.getElementById('emp').removeAttribute('required');
        }
    });

    // Add row button
    addRowBtn.addEventListener('click', function() {
        addLineItem();
    });

    // Add initial row
    addLineItem();

    function addLineItem() {
        const newRow = template.content.cloneNode(true);

        // Update all indices
        const rowIndex = rowCount;
        newRow.querySelectorAll('[name]').forEach(input => {
            const name = input.name.replace(/\[\d+\]/g, `[${rowIndex}]`);
            input.name = name;
        });

        newRow.querySelectorAll('[data-row-index]').forEach(el => {
            el.setAttribute('data-row-index', rowIndex);
        });

        // Update row number
        const firstTd = newRow.querySelector('td:first-child');
        firstTd.textContent = rowIndex + 1;

        // Update serial row ID
        const serialRow = newRow.querySelector('.terrasoft-serial-row');
        const serialInputsDiv = newRow.querySelector('.terrasoft-serial-inputs');
        serialInputsDiv.id = `serialInputs${rowIndex}`;

        // Add to table
        lineItemsBody.appendChild(newRow);

        // Attach event listeners to new row
        const materialSelect = lineItemsBody.querySelector(`[name="items[${rowIndex}][material_id]"]`);
        const quantityInput = lineItemsBody.querySelector(`[name="items[${rowIndex}][quantity]"]`);
        const removeBtn = lineItemsBody.querySelector(`tr:nth-last-child(2) .remove-row`);

        materialSelect.addEventListener('change', (e) => loadMaterialDetails(e, rowIndex));
        quantityInput.addEventListener('input', (e) => updateSerialInputs(e, rowIndex));
        removeBtn.addEventListener('click', (e) => removeLineItem(e, rowIndex));

        rowCount++;
    }

    function loadMaterialDetails(event, rowIndex) {
        const materialId = event.target.value;
        const option = event.target.options[event.target.selectedIndex];

        if (!materialId) {
            resetMaterialRow(rowIndex);
            return;
        }

        fetch(`{{ route('admin.stock-entry.get-material-details') }}?material_id=${materialId}&district_id=${districtSelect.value}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const material = data.material;
                    const row = lineItemsBody.querySelector(`tr[data-row-index="${rowIndex}"]`);

                    row.querySelector('.terrasoft-material-code').textContent = material.code;
                    row.querySelector('.terrasoft-material-unit').textContent = material.purchase_unit;
                    row.querySelector('.terrasoft-current-stock').textContent = Number(material.current_stock).toFixed(3);

                    if (material.has_serial) {
                        row.querySelector('.terrasoft-serial-indicator').innerHTML = '<span class="terrasoft-badge-serial">Serial Required</span>';
                    } else {
                        row.querySelector('.terrasoft-serial-indicator').textContent = '-';
                    }

                    updateSerialInputs(null, rowIndex);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function updateSerialInputs(event, rowIndex) {
    const row = lineItemsBody.querySelector(`tr[data-row-index="${rowIndex}"]`);
    const materialSelect = row.querySelector('.material-select');
    const quantityInput = row.querySelector('.quantity-input');
    const quantity = parseInt(quantityInput.value) || 0;
    const option = materialSelect.options[materialSelect.selectedIndex];
    const hasSerial = option.dataset.hasSerial === '1';
    const serialRow = lineItemsBody.querySelector(`tr.terrasoft-serial-row[data-row-index="${rowIndex}"]`);
    const serialInputsDiv = document.getElementById(`serialInputs${rowIndex}`);

    // Reset if quantity <= 0
    if (quantity <= 0) {
        serialRow.style.display = 'none';
        serialInputsDiv.innerHTML = "";
        return;
    }

    if (hasSerial) {
        // Show serial + qty inputs
        serialRow.style.display = 'table-row';
        serialInputsDiv.innerHTML = "";

        for (let i = 0; i < quantity; i++) {
            const wrapper = document.createElement("div");
            wrapper.className = "serial-qty-row";

            const serialInput = document.createElement('input');
            serialInput.type = 'text';
            serialInput.className = 'terrasoft-serial-input';
            serialInput.name = `items[${rowIndex}][serials][${i}][serial]`;
            serialInput.placeholder = `Serial #${i + 1}`;
            serialInput.maxLength = 100;

            const qtyInput = document.createElement('input');
            qtyInput.type = 'number';
            qtyInput.className = 'terrasoft-serial-input';
            qtyInput.name = `items[${rowIndex}][serials][${i}][qty]`;
            qtyInput.placeholder = `Qty`;
            qtyInput.step = "0.001";
            qtyInput.min = "0.001";

            wrapper.appendChild(serialInput);
            wrapper.appendChild(qtyInput);
            serialInputsDiv.appendChild(wrapper);
        }

        // Hide main quantity input since serials have their own qty
        quantityInput.readOnly = true;

    } else {
        // Material without serial → main quantity input is active
        serialRow.style.display = 'none';
        serialInputsDiv.innerHTML = "";
        quantityInput.readOnly = false; // user can enter quantity directly
    }
}




    function removeLineItem(event, rowIndex) {
        event.preventDefault();
        const rows = lineItemsBody.querySelectorAll(`tr[data-row-index="${rowIndex}"]`);
        rows.forEach(row => row.remove());

        // Renumber remaining rows
        renumberRows();

        // Show add button if no rows
        if (lineItemsBody.children.length === 0) {
            addLineItem();
        }
    }

    function renumberRows() {
        const rows = lineItemsBody.querySelectorAll('.terrasoft-line-item');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
    }

    function resetMaterialRow(rowIndex) {
        const row = lineItemsBody.querySelector(`tr[data-row-index="${rowIndex}"]`);
        row.querySelector('.terrasoft-material-code').textContent = '-';
        row.querySelector('.terrasoft-material-unit').textContent = 'unit';
        row.querySelector('.terrasoft-current-stock').textContent = '-';
        row.querySelector('.terrasoft-serial-indicator').textContent = '-';
        const serialRow = lineItemsBody.querySelector(`tr.terrasoft-serial-row[data-row-index="${rowIndex}"]`);
        serialRow.style.display = 'none';
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (lineItemsBody.children.length === 0) {
            alert('Please add at least one material');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="ti-loader"></i> Saving...';

        const formData = new FormData(form);

        fetch('{{ route('admin.stock-entry.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
                window.location.href = '{{ route('admin.stock-entry.index') }}';
            } else {
                showToast('Error:',data.message);
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = '<i class="ti-check"></i> Save Stock Entry';
            }
           
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error: ' , error);
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = '<i class="ti-check"></i> Save Stock Entry';
        });
    });
      

    document.getElementById('transaction_type').addEventListener('change', function () {
    const type = this.value;
    const empGroup = document.getElementById('empGroup');
    const empSelect = document.getElementById('emp');
    const districtId = document.getElementById('district_id').value; 

    if (type === 'ISSUE') {

        fetch(`{{ route('admin.get-employees') }}?district_id=${districtId}`)
            .then(res => res.json())
            .then(data => {
                if(data.success){

                empSelect.innerHTML = '<option value="">Select</option>';

                data.employees.forEach(emp => {
                    empSelect.innerHTML += `<option value="${emp.id}">${emp.first_name} ${emp.last_name}</option>`;
                });

                empGroup.style.display = '';
                 empGroup.style.gridColumn = 'auto';
                empSelect.setAttribute('required', 'required');
            }else{
                showToast('Error:',data.message);

            }
            });

    } else {
        empGroup.style.display = 'none';
        empSelect.removeAttribute('required');
        empSelect.innerHTML = '<option value="">Select</option>';
    }
});
});
function showToast(type, message) {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    const messageEl = document.getElementById('toastMessage');
    
    messageEl.textContent = message;
    toast.className = `terrasoft-toast ${type}`;
    
    if (type === 'success') {
        icon.innerHTML = '<i class="ti-check"></i>';
    } else {
        icon.innerHTML = '<i class="ti-info-alt" style="color: red;"></i>';
    }
    
    toast.classList.add('show');
    
    setTimeout(() => {
        closeToast();
    }, 5000);
}

// Close toast
function closeToast() {
    document.getElementById('toast').classList.remove('show');
}
</script>

<style>
.terrasoft-badge-serial {
    display: inline-block;
    padding: 2px 6px;
    background: #fef3c7;
    color: #92400e;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

.terrasoft-btn.loading {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>
