@extends('admin.layout.base')

@section('title', 'Issue Stock to Employee')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background-color: #f5f6f7;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .page-wrapper {
        background: #f8fafc;
        min-height: 100vh;
        /* padding: 24px 0; */
    }

    .container-main {
        /* max-width: 1400px;
        margin: 0 auto;
        padding: 0 16px; */
         padding: 24px;
    max-width: 100%;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 600;
        color: #1a202c;
        margin: 0;
    }

    .page-header .btn-back {
        padding: 10px 16px;
        background-color: #e2e8f0;
        border: none;
        border-radius: 6px;
        color: #4a5568;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .page-header .btn-back:hover {
        background-color: #cbd5e0;
        color: #2d3748;
    }

    .alerts-container {
        margin-bottom: 24px;
    }

    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 12px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .alert-success {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .alert-error {
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .alert-icon {
        flex-shrink: 0;
        margin-top: 2px;
    }

    .alert-content h4 {
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 8px 0;
    }

    .alert-content ul {
        margin: 0;
        padding-left: 20px;
        font-size: 13px;
    }

    .alert-content li {
        margin-bottom: 4px;
    }

    .form-container {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
    }

    @media (max-width: 1024px) {
        .form-container {
            grid-template-columns: 1fr;
        }
    }

    .cardissue {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .form-label .required {
        color: #e53e3e;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .input-groups {
        position: relative;
        display: flex;
        align-items: stretch;
    }

    .input-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #718096;
        font-size: 16px;
        pointer-events: none;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 10px 12px 10px 36px;
        border: 1px solid #cbd5e0;
        border-radius: 6px;
        font-size: 14px;
        color: #2d3748;
        background: white;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #3182ce;
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
    }

    .form-select {
        padding-right: 36px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23718096' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }

    .form-input[readonly],
    .form-input[disabled] {
        background-color: #f7fafc;
        color: #718096;
        cursor: not-allowed;
    }
    .serial-quantity-input{
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
    }

    .form-textarea {
        padding: 10px 12px;
        resize: vertical;
        min-height: 100px;
    }

    .info-box {
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        padding: 12px 14px;
        margin-top: 8px;
        font-size: 13px;
        color: #1e40af;
    }

    .info-box strong {
        font-weight: 600;
        color: #1e3a8a;
    }

    .form-row-3 {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .form-row-3 {
            grid-template-columns: 1fr;
        }
    }

    .btn {
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background-color: #3182ce;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2c5aa0;
    }

    .btn-success {
        background-color: #16a34a;
        color: white;
        font-weight: 600;
        padding: 12px 20px;
        font-size: 15px;
        width: 100%;
        justify-content: center;
    }

    .btn-success:hover {
        background-color: #15803d;
    }

    .btn-secondary {
        background-color: #e2e8f0;
        color: #4a5568;
        width: 100%;
        justify-content: center;
        font-weight: 500;
    }

    .btn-secondary:hover {
        background-color: #cbd5e0;
        color: #2d3748;
    }

    .btn-danger {
        background-color: transparent;
        color: #e53e3e;
        padding: 8px;
    }

    .btn-danger:hover {
        background-color: #fff5f5;
        color: #c53030;
    }

    .items-container {
        margin-bottom: 0;
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #a0aec0;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .empty-state-text {
        font-size: 14px;
        margin: 0;
    }

    .issue-item {
        background-color: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .issue-item:last-child {
        margin-bottom: 0;
    }

    .issue-item-info h4 {
        font-size: 14px;
        font-weight: 600;
        color: #1a202c;
        margin: 0 0 6px 0;
    }

    .issue-item-info p {
        font-size: 13px;
        color: #718096;
        margin: 0;
    }

    .issue-item-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .issue-item-serial {
        background-color: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 6px;
        padding: 12px;
        margin-top: 12px;
        font-size: 12px;
    }

    .issue-item-serial-label {
        font-weight: 600;
        color: #92400e;
        margin-bottom: 8px;
    }

    .issue-item-serial-list {
        list-style: none;
        margin: 0 0 8px 0;
        padding: 0;
    }

    .issue-item-serial-list li {
        color: #92400e;
        margin-bottom: 4px;
    }

    .btn-serial {
        background-color: white;
        color: #3182ce;
        border: 1px solid #bfdbfe;
        padding: 8px 12px;
        font-size: 12px;
        border-radius: 4px;
    }

    .btn-serial:hover {
        background-color: #eff6ff;
    }

    .sidebar {
        position: sticky;
        top: 20px;
    }

    .sidebar-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .sidebar-card-title {
        font-size: 15px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 16px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-label {
        font-size: 13px;
        color: #718096;
    }

    .summary-value {
        font-size: 18px;
        font-weight: 700;
        color: #3182ce;
    }

    .summary-value.serial {
        color: #16a34a;
    }

    .summary-value.non-serial {
        color: #8b5cf6;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: white;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f7fafc;
        border-radius: 12px 12px 0 0;
    }

    .modal-header h2 {
        font-size: 16px;
        font-weight: 600;
        color: #1a202c;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #718096;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }

    .modal-body {
        padding: 20px 24px;
    }

    .serial-checkbox-group {
        display: flex;
        align-items: center;
        padding: 12px;
        background-color: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .serial-checkbox-group:hover {
        background-color: #eff6ff;
        border-color: #bfdbfe;
    }

    .serial-checkbox-group input[type="checkbox"] {
        margin-right: 10px;
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .serial-checkbox-group label {
        flex: 1;
        cursor: pointer;
        margin: 0;
        font-weight: 500;
        color: #2d3748;
        font-size: 13px;
    }

    .serial-checkbox-group .qty-badge {
        font-size: 12px;
        color: #718096;
        white-space: nowrap;
    }

    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        background-color: #f7fafc;
        border-radius: 0 0 12px 12px;
    }

    .modal-footer .btn {
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .form-container {
            grid-template-columns: 1fr;
        }

        .sidebar {
            position: static;
        }

        .form-row,
        .form-row-3 {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-wrapper">
    <div class="container-main">
        <!-- Page Header -->
        <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-list text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">Stock Issue to Employees</h1>
                        <p class="terrasoft-page-subtitle">Manage all inventory issues</p>
                    </div>
                </div>
                <div class="terrasoft-header-actions">
                    <a href="{{ route('admin.stock-issue.index') }}" class="terrasoft-btn terrasoft-btn-primary">
                        <i class="ti-arrow"></i>
                         Back
                    </a>
                </div>
            </div>
        </div>
       

        <!-- Alerts -->
        <div class="alerts-container">
            @if(session('success'))
                <div class="alert alert-success">
                    <div class="alert-icon">✓</div>
                    <div class="alert-content">
                        <h4>{{ session('success') }}</h4>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <div class="alert-icon">✕</div>
                    <div class="alert-content">
                        <h4>{{ session('error') }}</h4>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <div class="alert-icon">✕</div>
                    <div class="alert-content">
                        <h4>Validation Errors:</h4>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <!-- Form Container -->
        <form action="{{ route('admin.stock-issue.store') }}" method="POST" id="issueForm" class="form-container">
            {{ csrf_field() }}

            <!-- Main Content -->
            <div>
                <!-- Transaction Details Card -->
                <div class="cardissue">
                    <h3 class="card-title">Transaction Details</h3>

                    <div class="form-row">
                        <!-- District -->
                        <div class="form-group">
                            <label class="form-label">District</label>
                             <div class="input-groups">
                                <i class="input-icon ti-location-pin"></i>
                                <select id="districtSelect" name="district_id" class="form-select" required>
                                    <option value="">Select District</option>
                                    @foreach($district as $dist)
                                        <option value="{{ $dist->id }}">{{ $dist->name }}</option>
                                    @endforeach
                                </select>
                             </div>
                            
                        </div>

                        <!-- Employee -->
                        <div class="form-group">
                            <label class="form-label">Select Employee</label>
                              <div class="input-groups">
                                <i class="input-icon ti-user"></i>
                                <select id="employeeSelect" name="employee_id" class="form-select" required>
                                    <option value="">Select district first</option>
                                </select>
                                </div>
                           
                            <div id="employeeBalanceContainer" class="info-box" style="display: none;">
                                <strong>Current Stock Balance:</strong> <span id="employeeBalance">0</span> Items
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Type -->
                    <div class="form-group">
                        <label class="form-label">Transaction Type</label>
                            <input type="text" class="form-input" value="ISSUE" readonly>
                       
                        <input type="hidden" name="transaction_type" value="ISSUE">
                    </div>

                    <!-- Ticket -->
                    <!-- <div class="form-group">
                        <label class="form-label">Ticket/Work Order</label>
                        <input type="text" name="ticket_id" class="form-input" placeholder="Optional ticket reference"
                               value="{{ old('ticket_id') }}" style="padding-left: 12px;">
                    </div> -->
                </div>

                <!-- Add Materials Card -->
                <div class="cardissue">
                    <h3 class="card-title">Add Materials</h3>

                    <div class="form-row-3">
                        <!-- Material -->
                        <div class="form-group">
                            <label class="form-label">Material</label>
                            <div class="input-groups">
                                <i class="input-icon ti-package"></i>
                                <select id="materialSelect" class="form-select" >
                                    <option value="">Select Material</option>
                                    @foreach($materials ?? [] as $material)
                                        <option value="{{ $material->id }}"
                                                data-name="{{ $material->name }}"
                                                data-code="{{ $material->code }}"
                                                data-unit="{{ $material->base_unit }}"
                                                data-has-serial="{{ $material->has_serial ? 'true' : 'false' }}"
                                                data-available="{{ $material->currentStock ?? 0 }}">
                                            {{ $material->code }} - {{ $material->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="form-group">
                            <label class="form-label">Quantity</label>
                            <input type="number" id="quantityInput" class="form-input" placeholder="0"
                                   step="0.001" min="0.001" style="padding-left: 12px;">
                        </div>

                        <!-- Availability -->
                        <div class="form-group">
                            <label class="form-label">Availability</label>
                            <input type="text" id="availabilityText" class="form-input" value="0 Units"
                                   readonly style="padding-left: 12px; background-color: #f7fafc;">
                        </div>
                    </div>

                    <button type="button" id="addMaterialBtn" class="btn btn-primary">
                        + Add
                    </button>
                </div>

                <!-- Items to be Issued Card -->
                <div class="cardissue">
                    <h3 class="card-title">Items to be Issued</h3>

                    <div id="issuesContainer" class="items-container">
                        <!-- Items will be added here -->
                    </div>

                    <div id="emptyState" class="empty-state">
                        <div class="empty-state-icon">📦</div>
                        <p class="empty-state-text">No materials added yet.</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Summary Card -->
                <div class="sidebar-card">
                    <h4 class="sidebar-card-title">Issue Summary</h4>

                    <div class="summary-item">
                        <span class="summary-label">Total Items:</span>
                        <span class="summary-value" id="summaryItems">0</span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">Serial-based Items:</span>
                        <span class="summary-value serial" id="summarySerial">0</span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">Non-serial Items:</span>
                        <span class="summary-value non-serial" id="summaryNonSerial">0</span>
                    </div>
                </div>

                <!-- Remarks Card -->
                <div class="sidebar-card">
                    <label class="form-label">Remarks (Optional)</label>
                    <textarea name="remarks" class="form-textarea" placeholder="Add any notes for this transaction...">{{ old('remarks') }}</textarea>
                </div>

                <!-- Action Buttons -->
                <button type="submit" class="btn btn-success">
                    ✓ Confirm & Issue Stock
                </button>
                <button type="reset" class="btn btn-secondary">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Serial Modal -->
<div id="serialModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Select Serial Numbers</h2>
            <button type="button" class="modal-close" onclick="closeSerialModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="serialsContainer">
                <!-- Serial checkboxes will be populated here -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeSerialModal()">
                Cancel
            </button>
            <button type="button" class="btn btn-primary" onclick="confirmSerialSelection()">
                Confirm Selection
            </button>
        </div>
    </div>
</div>

<!-- Item Template (hidden) -->
<template id="itemTemplate">
    <div class="issue-item" data-index="0" data-material-id="" data-has-serial="false">
        <div class="issue-item-info">
            <h4 class="item-name">Material Name</h4>
            <p>Quantity: <span class="item-quantity">0</span></p>
            <div class="issue-item-serial" style="display: none;">
                <div class="issue-item-serial-label">Serial Numbers to Issue:</div>
                <ul class="issue-item-serial-list" id="serialsList">
                    <!-- Serials will be added here -->
                </ul>
                <button type="button" class="btn-serial select-serial-btn">
                    Select Serial
                </button>
            </div>
        </div>
        <div class="issue-item-actions">
            <button type="button" class="btn btn-danger remove-item-btn">
                  <i class="ti-trash"></i>
            </button>
        </div>

        <!-- Hidden inputs -->
        <input type="hidden" name="items[INDEX][material_id]" class="material-id" value="">
        <input type="hidden" name="items[INDEX][quantity]" class="item-quantity-input" value="">
    </div>
</template>

@endsection
<link rel="stylesheet" href="{{ asset('/css/materials.css') }}">

@section('scripts')
<script>
let itemIndex = 0;
let selectedEmployee = null;
let materials = {!! json_encode($materials ?? []) !!};
let serials = {!! json_encode($serials ?? []) !!};
let currentSelectingIndex = null;

document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
});

// District change
document.getElementById('districtSelect').addEventListener('change', function() {
    const districtId = this.value;
    const employeeSelect = document.getElementById('employeeSelect');
    const balanceContainer = document.getElementById('employeeBalanceContainer');

    employeeSelect.innerHTML = '<option value="">Select Employee</option>';
    balanceContainer.style.display = 'none';

    if (!districtId) return;

    fetch(`{{ route('admin.get-employees') }}?district_id=${districtId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.employees.length) {
                data.employees.forEach(emp => {
                    const option = document.createElement('option');
                    option.value = emp.id;
                    option.textContent = `${emp.first_name} ${emp.last_name}`;
                    employeeSelect.appendChild(option);
                });
            }
        })
        .catch(err => console.error('Error:', err));
});

// Employee change
document.getElementById('employeeSelect').addEventListener('change', function() {
    if (this.value) {
        document.getElementById('employeeBalanceContainer').style.display = 'block';
        selectedEmployee = { id: this.value };
         fetch(`{{ route('admin.emp-stock-blnc') }}?employeeId=${this.value}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('employeeBalance').textContent = data.balance || '0';
            }
        })
        .catch(err => console.error('Error:', err));
       
    }
});

function setupEventListeners() {
    // Material selection
    document.getElementById('materialSelect').addEventListener('change', function() {
         if (this.value) {
        const option = this.options[this.selectedIndex];
          if (!option.value) {
            document.getElementById('availabilityText').value = '0 Units';
            document.getElementById('quantityInput').value = '';
            return;
        }
        const districtSelect = document.getElementById('districtSelect');
        if(!districtSelect.value) {
            alert('Please select district');
            return;}
        

       
         fetch(`{{ route('admin.material-stock-blnc') }}?mat_id=${this.value}&district_id=${districtSelect.value}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('availabilityText').value = (data.balance || 0) + ' ' + option.dataset.unit;

            }
        })
        .catch(err => console.error('Error:', err));
       
    }
      
    });

    // Add material button
    document.getElementById('addMaterialBtn').addEventListener('click', addMaterialItem);

    // Form submission
    document.getElementById('issueForm').addEventListener('submit', function(e) {
        if (!selectedEmployee) {
            e.preventDefault();
            alert('Please select an employee');
            return false;
        }
        if (document.querySelectorAll('.issue-item').length === 0) {
            e.preventDefault();
            alert('Please add at least one material');
            return false;
        }
        return true;
    });

    // Form reset
    document.querySelector('button[type="reset"]').addEventListener('click', function() {
        selectedEmployee = null;
        document.getElementById('issuesContainer').innerHTML = '';
        document.getElementById('emptyState').style.display = 'block';
        itemIndex = 0;
        updateSummary();
    });
}

function addMaterialItem() {
    const thisIndex = itemIndex; 
    const materialSelect = document.getElementById('materialSelect');
    const quantityInput = document.getElementById('quantityInput');
    const aval_bal = document.getElementById('availabilityText');
    const districtSelect = document.getElementById('districtSelect');

    

    const materialId = materialSelect.value;
    const districtId =districtSelect.value;
    const quantity = parseFloat(quantityInput.value);
    const aval_balval =parseFloat(aval_bal.value);

    if (!materialId) {
        alert('Please select a material');
        return;
    }

    if (!quantity || quantity <= 0 || quantity > aval_balval) {
        alert('Please enter a valid quantity');
        return;
    }

    const option = materialSelect.options[materialSelect.selectedIndex];
    const materialName = option.dataset.name || '';
    const hasSerial = option.dataset.hasSerial === 'true';

    const template = document.getElementById('itemTemplate');
    const clone = template.content.cloneNode(true);
    
    const itemElement = clone.querySelector('.issue-item');
    itemElement.setAttribute('data-index', thisIndex);
    itemElement.setAttribute('data-material-id', materialId);
    itemElement.setAttribute('data-has-serial', hasSerial);

    clone.querySelector('.item-name').textContent = materialName;
    clone.querySelector('.item-quantity').textContent = quantity;
    clone.querySelector('.material-id').name = `items[${itemIndex}][material_id]`;
    clone.querySelector('.material-id').value = materialId;
    clone.querySelector('.item-quantity-input').name = `items[${itemIndex}][quantity]`;
    clone.querySelector('.item-quantity-input').value = quantity;

    const serialSection = clone.querySelector('.issue-item-serial');
    if (hasSerial) {
        serialSection.style.display = 'block';
        clone.querySelector('.select-serial-btn').addEventListener('click', function(e) {
            e.preventDefault();
            currentSelectingIndex = thisIndex;
            showSerialModal(materialId, districtId,quantity);
        });
    }

    clone.querySelector('.remove-item-btn').addEventListener('click', function(e) {
        e.preventDefault();
        itemElement.remove();
        updateItemNumbers();
        updateSummary();
        if (document.querySelectorAll('.issue-item').length === 0) {
            document.getElementById('emptyState').style.display = 'block';
        }
    });

    document.getElementById('issuesContainer').appendChild(clone);
    document.getElementById('emptyState').style.display = 'none';

    materialSelect.value = '';
    quantityInput.value = '';
    document.getElementById('availabilityText').value = '0 Units';

    itemIndex++;
    updateSummary();
}

function showSerialModal(materialId, districtId,quantity) {
    const availableSerials = serials.filter(s => s.material_id == materialId && s.district_id == districtId && s.qtystatus != 'FULLY_ISSUED');
   
    const container = document.getElementById('serialsContainer');
    
    container.innerHTML = '';

    if (availableSerials.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #e53e3e;">No available serial numbers</p>';
    } else {
            const itemRow = document.querySelector(`.issue-item[data-index="${currentSelectingIndex}"]`);
            const existingInput = itemRow.querySelector(`input[name="items[${currentSelectingIndex}][serials]"]`);
         
            let preSelected = {};
            if (existingInput) {
                existingInput.value.split(',').forEach(s => {
                    const [id, qty] = s.split(':');
                    preSelected[id] = qty;
                });
            }

        availableSerials.forEach(s => {
                const prevQty = preSelected[s.id] || 0;
                   
            // const html = `
            //     <div class="serial-checkbox-group">
            //         <input type="checkbox" class="serial-checkbox" data-serial-id="${s.id}" data-serial="${s.serial_number}" value="${s.id}">
            //         <label>${s.serial_number}</label>
            //         <span class="qty-badge">Qty: ${s.quantity}</span>
            //     </div>
            // `;
            const html = `
                <div class="serial-checkbox-group">
                    <label>
                        <input type="checkbox" class="serial-checkbox" data-serial-id="${s.id}" data-serial="${s.serial_number}" data-max="${s.quantity}" ${prevQty > 0 ? 'checked' : ''}>
                        ${s.serial_number} <span class="qty-badge">(Available Qty: ${s.quantity})</span>
                    </label>
                    <input type="number" class="serial-quantity-input" data-serial-id="${s.id}" min="0" max="${s.quantity}" value="${prevQty}"  style="width: 80px; margin-left: 10px;" ${s.quantity === 0 ? 'disabled' : ''}>
                </div>
            `;
            container.innerHTML += html;
        });
        container.querySelectorAll('.serial-quantity-input').forEach(input => {
            input.addEventListener('input', () => validateSerialQuantities(quantity));
        });
    }

    document.getElementById('serialModal').classList.add('show');
}

function closeSerialModal() {
    document.getElementById('serialModal').classList.remove('show');
}
function validateSerialQuantities(requestedQuantity) {
    const inputs = document.querySelectorAll('.serial-quantity-input');
    let total = 0;
    let valid = true;

    inputs.forEach(input => {
        const max = parseFloat(input.dataset.max);
        let val = parseFloat(input.value) || 0;

        // Clamp to max/min
        if (val > max) {
            input.value = max;
            val = max;
        }
        // if (val < 1) {
        //     input.value = 1;
        //     val = 1;
        // }

        total += val;
    });

    if (total > requestedQuantity) {
        valid = false;
        document.getElementById('serialModalWarning')?.remove();
        const warning = document.createElement('div');
        warning.id = 'serialModalWarning';
        warning.style.color = 'red';
        warning.style.marginTop = '5px';
        warning.textContent = `Total selected quantity (${total}) exceeds requested quantity (${requestedQuantity}).`;
        document.getElementById('serialsContainer').appendChild(warning);
    } else {
        const warning = document.getElementById('serialModalWarning');
        if (warning) warning.remove();
    }

    return valid;
}

// function confirmSerialSelection() {
//     if (currentSelectingIndex === null) return;

//     const checkboxes = document.querySelectorAll('.serial-checkbox:checked');
//     const selectedSerials = Array.from(checkboxes).map(cb => ({
//         id: cb.dataset.serialId,
//         number: cb.dataset.serial
//     }));

//     const itemRow = document.querySelector(`.issue-item[data-index="${currentSelectingIndex}"]`);
    
//     if (selectedSerials.length > 0) {
//         const serialsList = itemRow.querySelector('#serialsList');
//         serialsList.innerHTML = selectedSerials.map(s => `<li>• ${s.number}</li>`).join('');

//         const serialsInput = document.createElement('input');
//         serialsInput.type = 'hidden';
//         serialsInput.name = `items[${currentSelectingIndex}][serials]`;
//         serialsInput.value = selectedSerials.map(s => s.id).join(',');

//         const existingInput = itemRow.querySelector('input[name*="serials"]');
//         if (existingInput) existingInput.remove();

//         itemRow.appendChild(serialsInput);
//     }

//     closeSerialModal();
// }
function confirmSerialSelection() {
    if (currentSelectingIndex === null) return;

    const requestedQuantity = parseFloat(document.querySelector(`.issue-item[data-index="${currentSelectingIndex}"] .item-quantity-input`).value);
    
    if (!validateSerialQuantities(requestedQuantity)) {
        alert('Total quantity exceeds requested quantity!');
        return;
    }


    const itemRow = document.querySelector(`.issue-item[data-index="${currentSelectingIndex}"]`);
    if (!itemRow) return;

    const selectedSerials = [];

    document.querySelectorAll('.serial-checkbox:checked').forEach(cb => {
        const serialId = cb.dataset.serialId;
        const serialNumber = cb.dataset.serial;
        const qtyInput = document.querySelector(`.serial-quantity-input[data-serial-id="${serialId}"]`);
        const quantity = parseFloat(qtyInput.value);

        if (quantity > 0 && quantity <= parseFloat(cb.dataset.max)) {
            selectedSerials.push({
                id: serialId,
                number: serialNumber,
                quantity: quantity
            });
        }
    });

    if (selectedSerials.length === 0) {
        alert('Please select at least one serial with quantity');
        return;
    }

    // Update UI
    const serialsList = itemRow.querySelector('.issue-item-serial-list');
    serialsList.innerHTML = selectedSerials.map(s => `<li>• ${s.number} - ${s.quantity}</li>`).join('');

    // Hidden input to send to backend
    const serialsInput = document.createElement('input');
    serialsInput.type = 'hidden';
    serialsInput.name = `items[${currentSelectingIndex}][serials]`;
    // Format: serialId:quantity,serialId:quantity
    serialsInput.value = selectedSerials.map(s => `${s.id}:${s.quantity}`).join(',');

    const existingInput = itemRow.querySelector(`input[name="items[${currentSelectingIndex}][serials]"]`);
    if (existingInput) existingInput.remove();

    itemRow.appendChild(serialsInput);

    closeSerialModal();
}

function updateItemNumbers() {
    document.querySelectorAll('.issue-item').forEach((item, idx) => {
        item.setAttribute('data-index', idx);
        item.querySelector('.material-id').name = `items[${idx}][material_id]`;
        item.querySelector('.item-quantity-input').name = `items[${idx}][quantity]`;
    });
    itemIndex = document.querySelectorAll('.issue-item').length;
}

function updateSummary() {
    let totalItems = 0;
    let serialItems = 0;
    let nonSerialItems = 0;

    document.querySelectorAll('.issue-item').forEach(item => {
        const quantity = parseFloat(item.querySelector('.item-quantity').textContent) || 0;
        totalItems += quantity;

        if (item.dataset.hasSerial === 'true') {
            serialItems++;
        } else {
            nonSerialItems++;
        }
    });

    document.getElementById('summaryItems').textContent = totalItems;
    document.getElementById('summarySerial').textContent = serialItems;
    document.getElementById('summaryNonSerial').textContent = nonSerialItems;
}

// Close modal on outside click
document.getElementById('serialModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSerialModal();
    }
});
</script>
@endsection
