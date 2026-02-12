@extends('admin.layout.base')

@section('title', 'Stock Entries')

@section('content')
<div class="terrasoft-main-content">
    <div class="terrasoft-page-container">
        {{-- Page Header --}}
        <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-list text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">Stock Entries</h1>
                        <p class="terrasoft-page-subtitle">Manage all inventory transactions</p>
                    </div>
                </div>
                <div class="terrasoft-header-actions">
                    <a href="{{ route('admin.stock-entry.create') }}" class="terrasoft-btn terrasoft-btn-primary">
                        <i class="ti-plus"></i>
                        New Entry
                    </a>
                </div>
            </div>
        </div>

        {{-- Success Alert --}}
        @if(session('success'))
        <div class="terrasoft-alert terrasoft-alert-success">
            <i class="ti-check-circle"></i>
            <div>{{ session('success') }}</div>
            <button class="terrasoft-alert-close" onclick="this.parentElement.style.display='none';">
                <i class="ti-x"></i>
            </button>
        </div>
        @endif
      

        @if(session('error'))
            <div class="terrasoft-alert terrasoft-alert-error">
            <i class="ti-check-circle"></i>
            <div>{{ session('error') }}</div>
            <button class="terrasoft-alert-close" onclick="this.parentElement.style.display='none';">
                <i class="ti-x"></i>
            </button>
              
            </div>
        @endif

        {{-- Filters Section --}}
        <div class="terrasoft-filters-section">
            <form method="GET" class="terrasoft-filters-form">
                <div class="terrasoft-filters-grid">
                    <div class="terrasoft-filter-group">
                        <input type="text"
                               name="search"
                               class="terrasoft-filter-input"
                               placeholder="Search material..."
                               value="{{ request('search') }}">
                    </div>

                    <div class="terrasoft-filter-group">
                        <select name="type_filter" class="terrasoft-filter-input">
                            <option value="">All Types</option>
                               @foreach($transactionTypes as $key => $label)
                                    <option value="{{ $key }}"  {{ request('type_filter') === $key ? 'selected' : '' }}>{{ $label }}</option>
                               @endforeach
                        </select>
                    </div>

                    <div class="terrasoft-filter-group">
                        <button type="submit" class="terrasoft-btn terrasoft-btn-primary">
                            <i class="ti-search"></i>
                            Search
                        </button>
                        <a href="{{ route('admin.stock-entry.index') }}" class="terrasoft-btn terrasoft-btn-secondary">
                            <i class="ti-refresh"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="terrasoft-table-section">
            @if($transactions->count() > 0)
                <div class="terrasoft-table-container">
                    <table class="terrasoft-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Material</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>District</th>
                                <th>Serials</th>
                                <th>Reference</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td class="terrasoft-cell-date">
                                    {{ $transaction->created_at->format('d M Y') }}
                                    <span class="terrasoft-cell-time">{{ $transaction->created_at->format('H:i') }}</span>
                                </td>
                                <td class="terrasoft-cell-material">
                                    <div class="terrasoft-material-info">
                                        <strong>{{ $transaction->material->name }}</strong>
                                        <span class="terrasoft-material-code">{{ $transaction->material->code }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="terrasoft-badge terrasoft-badge-{{ strtolower($transaction->transaction_type) }}">
                                        {{ $transaction->transaction_type }}
                                    </span>
                                </td>
                                <td class="terrasoft-cell-quantity">
                                    <strong>{{ number_format($transaction->quantity, 3) }}</strong>
                                    <span class="terrasoft-unit">{{ $transaction->material->base_unit }}</span>
                                </td>
                                <td>{{ $transaction->district->name ?? '-' }}</td>
                                <td>
                                    @if($transaction->serial->count())
                                       Count - {{ $transaction->serial->count() }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td class="terrasoft-cell-reference">
                                    @if($transaction->reference_id)
                                        <span class="terrasoft-reference">
                                            {{ $transaction->reference_type }}: {{ $transaction->reference_id }}
                                        </span>
                                    @else
                                        <span class="terrasoft-na">-</span>
                                    @endif
                                </td>
                                <td class="terrasoft-cell-remarks">
                                    @if($transaction->remarks)
                                        <span title="{{ $transaction->remarks }}" class="terrasoft-remarks-text">
                                            {{ Str::limit($transaction->remarks, 30) }}
                                        </span>
                                    @else
                                        <span class="terrasoft-na">-</span>
                                    @endif
                                </td>
                                <td class="terrasoft-cell-actions">
                                    <div class="terrasoft-action-buttons">

                                    <a href="{{url('/admin/stock-entry/'.$transaction->id)}}" class="terrasoft-action-btn terrasoft-btn-view" title="View Details">
                                        <i class="ti-eye"></i>
                                    </a>
                                    <button type="button" class="terrasoft-action-btn terrasoft-btn-edit"
                                                onclick="editTransaction({{ $transaction->id }})"
                                                title="Edit">
                                            <i class="ti-marker-alt"></i>
                                    </button>
                                    <button type="button" class="terrasoft-action-btn terrasoft-btn-delete"
                                            onclick="deleteTransaction({{ $transaction->id }})"
                                            title="Delete">
                                        <i class="ti-trash"></i>
                                    </button>
                                      </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="terrasoft-pagination">
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="terrasoft-empty-state">
                    <div class="terrasoft-empty-icon">
                        <i class="ti-inbox"></i>
                    </div>
                    <h3>No Stock Entries</h3>
                    <p>No stock entries found. Create your first entry to get started.</p>
                    <a href="{{ route('admin.stock-entry.create') }}" class="terrasoft-btn terrasoft-btn-primary">
                        <i class="ti-plus"></i>
                        Create Entry
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
<!-- Edit Modal -->
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 25px rgba(0,0,0,0.15);">
        <div style="padding: 24px; border-bottom: 1px solid #e2e8f0;">
            <h2 style="margin: 0; font-size: 18px; font-weight: 600; color: #1a202c;">Edit Transaction</h2>
        </div>
        <form id="editForm" method="POST" style="padding: 24px;">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="PUT">

            <div id="editModalContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" style="flex: 1; padding: 10px; background: #16a34a; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    Update
                </button>
                <button type="button" onclick="closeModal('editModal')" style="flex: 1; padding: 10px; background: #e2e8f0; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 0 20px 25px rgba(0,0,0,0.15);">
        <div style="padding: 24px; text-align: center;">
            <!-- <div style="font-size: 48px; margin-bottom: 16px;">??</div> -->
            <h2 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600; color: #1a202c;">Delete Transaction?</h2>
            <p style="margin: 0; color: #718096; font-size: 14px;">This action cannot be undone.</p>
        </div>
        <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; gap: 12px;">
            <button type="button" onclick="closeModal('deleteModal')"
                    style=" width: 100%;  flex: 1; padding: 8px; background: #e2e8f0; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                Cancel
            </button>
            <form id="deleteForm" method="POST" style="flex: 1;">
                {{ csrf_field() }}
                <input type="hidden" name="_method" value="DELETE">
              
                <button type="submit" style="width: 100%; padding: 8px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    Delete
                </button>
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
    background: #f0fdf4;
    color: #b91419ff;
    border: 1px solid #bbf7d0;
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
    opacity: 0.7;
    transition: opacity 0.2s;
}

.terrasoft-alert-close:hover {
    opacity: 1;
}

/* Filters Section */
.terrasoft-filters-section {
    background: white;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e2e8f0;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.terrasoft-filters-form {
    display: flex;
    flex-direction: column;
}

.terrasoft-filters-grid {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 12px;
    align-items: flex-end;
}

.terrasoft-filter-group {
    display: flex;
    gap: 8px;
}

.terrasoft-filter-input {
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
    background: white;
    min-width: 150px;
}

.terrasoft-filter-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Table Section */
.terrasoft-table-section {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.terrasoft-table-container {
    overflow-x: auto;
}

.terrasoft-table {
    width: 100%;
    border-collapse: collapse;
}

.terrasoft-table thead {
    background: #f3f4f6;
    border-bottom: 2px solid #e5e7eb;
}

.terrasoft-table th {
    padding: 14px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.terrasoft-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #e5e7eb;
    font-size: 14px;
}

.terrasoft-table tbody tr:hover {
    background: #f9fafb;
}

.terrasoft-cell-date {
    font-weight: 500;
    color: #1f2937;
}

.terrasoft-cell-time {
    display: block;
    font-size: 12px;
    color: #6b7280;
    font-weight: 400;
}

.terrasoft-material-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.terrasoft-material-code {
    font-size: 12px;
    color: #6b7280;
}

.terrasoft-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.terrasoft-badge-opening {
    background: #d1fae5;
    color: #065f46;
}

.terrasoft-badge-IN {
    background: #dbeafe;
    color: #1e40af;
}

.terrasoft-badge-issue {
    background: #fee2e2;
    color: #991b1b;
}

.terrasoft-badge-return {
    background: #fce7f3;
    color: #831843;
}

.terrasoft-badge-adjustment {
    background: #fef3c7;
    color: #92400e;
}

.terrasoft-badge-transfer {
    background: #e0e7ff;
    color: #312e81;
}

.terrasoft-cell-quantity {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.terrasoft-unit {
    font-size: 12px;
    color: #6b7280;
    font-weight: 400;
}

.terrasoft-cell-reference {
    font-size: 13px;
}

.terrasoft-reference {
    background: #f3f4f6;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: 'Monaco', 'Menlo', monospace;
}

.terrasoft-na {
    color: #9ca3af;
}

.terrasoft-cell-remarks {
    font-size: 13px;
    color: #6b7280;
}

.terrasoft-remarks-text {
    cursor: help;
}

.terrasoft-cell-actions {
    text-align: right;
}

.terrasoft-btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

/* Pagination */
.terrasoft-pagination {
    padding: 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: center;
}

.terrasoft-pagination a,
.terrasoft-pagination span {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    margin: 0 2px;
    font-size: 13px;
    cursor: pointer;
}

.terrasoft-pagination a:hover {
    background: #f3f4f6;
}

.terrasoft-pagination .active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.terrasoft-pagination .disabled {
    color: #d1d5db;
    cursor: not-allowed;
}

/* Empty State */
.terrasoft-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 20px;
    text-align: center;
}

.terrasoft-empty-icon {
    font-size: 64px;
    color: #d1d5db;
    margin-bottom: 16px;
}

.terrasoft-empty-state h3 {
    font-size: 18px;
    font-weight: 600;
    color: #374151;
    margin: 0 0 8px 0;
}

.terrasoft-empty-state p {
    font-size: 14px;
    color: #6b7280;
    margin: 0 0 24px 0;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .terrasoft-filters-grid {
        grid-template-columns: 1fr;
    }

    .terrasoft-filter-group {
        flex-direction: column;
    }

    .terrasoft-filter-input {
        width: 100%;
        min-width: unset;
    }

    .terrasoft-table {
        font-size: 12px;
    }

    .terrasoft-table th,
    .terrasoft-table td {
        padding: 10px 12px;
    }

    .terrasoft-cell-remarks {
        max-width: 150px;
    }
}
</style>
<script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
function editTransaction(id) {
    fetch(`{{ url('admin/stock-entry') }}/${id}/edit`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load transaction');
            }
            return response.text();
        })
        .then(html => {
            document.getElementById('editModalContent').innerHTML = html;
            document.getElementById('editForm').action =
                `{{ url('admin/stock-entry') }}/${id}`;
            openModal('editModal');
        })
        .catch(error => {
            alert(error.message);
        });
}
['editModal', 'deleteModal'].forEach(id => {
    const modal = document.getElementById(id);
    if (!modal) return;

    modal.addEventListener('click', function (e) {
        if (e.target === this) {
            closeModal(id);
        }
    });
});
document.addEventListener('submit', function (e) {

    const form = e.target;
    if (form.id !== 'editForm') return;

    let errors = [];

   let totalSerialQty = 0;
    let valid = true;

    const qtyInputs = this.querySelectorAll(
        'input[name^="serials"][name$="[received_quantity]"]'
    );
    if (qtyInputs.length > 0) {
        qtyInputs.forEach(input => {
            const val = parseFloat(input.value);

            if (isNaN(val) || val <= 0) {
                valid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
                totalSerialQty += val;
            }
        });

        const mainQty = parseFloat(
            this.querySelector('input[name="received_quantity"]').value
        );

        if (totalSerialQty !== mainQty) {
            alert('Serial quantities must match total quantity');
            valid = false;
        }
    }

    if (!valid) {
        e.preventDefault();
    }
});
function deleteTransaction(id) {
    document.getElementById('deleteForm').action = `{{ route('admin.stock-entry.destroy', '') }}/${id}`;
    openModal('deleteModal');
}


</script>
