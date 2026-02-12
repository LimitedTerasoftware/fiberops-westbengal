@extends('admin.layout.base')

@section('title', 'Stock Issue to Employees')

@section('content')


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
                    <a href="{{ route('admin.stock-issue.create') }}" class="terrasoft-btn terrasoft-btn-primary">
                        <i class="ti-plus"></i>
                          Issue Stock
                    </a>
                </div>
            </div>
        </div>
       

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success">
                <div class="alert-icon"> <i class="ti-check"></i></div>
                <div class="alert-content">
                    <h4>Success</h4>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <div class="alert-icon"><i class="ti-close"></i></div>
                <div class="alert-content">
                    <h4>Error</h4>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Filters Card -->
        <div class="filters-card">
            <form action="{{ route('admin.stock-issue.index') }}" method="GET">
                <div class="filters-grid">
                  

                    <!-- District -->
                    <div class="filter-group">
                        <label class="filter-label">District</label>
                        <select name="district" id="district_id" class="filter-control">
                            <option value="">All Districts</option>
                            @foreach($districts as $dist)
                                <option value="{{ $dist->id }}" {{request('district') === $dist->id ? 'selected':''}} >
                                    {{ $dist->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                      <!-- Employee Name -->
                    <div class="filter-group">
                        <label class="filter-label">Employee Name</label>
                        <select name="employee" id="emp"class="filter-control">
                            <option value="">Select Employees</option>
                            <!-- @foreach($employees  as $emp)
                                <option value="{{ $emp->id }}" {{request('employee') == $emp->id ? 'selected' : ''}}>
                                    {{ $emp->first_name }} {{ $emp->last_name }}
                                </option>
                            @endforeach -->
                        </select>
                    </div>

                    <!-- Material -->
                    <div class="filter-group">
                        <label class="filter-label">Material</label>
                        <select name="material" class="filter-control">
                            <option value="">All Materials</option>
                            @foreach($materials as $mat)
                                <option value="{{ $mat->id }}" {{request('material') == $mat->id ? 'selected' : ''}}>
                                    {{ $mat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Serial Number -->
                    <div class="filter-group">
                        <label class="filter-label">Serial Number</label>
                        <input type="text" name="serial" class="filter-control"
                               placeholder="Enter serial..." value="{{ request('serial') }}">
                    </div>

                    <!-- Date From -->
                    <div class="filter-group">
                        <label class="filter-label">From</label>
                        <input type="date" name="date_from" class="filter-control"
                               value="{{ request('date_from') }}">
                    </div>

                    <!-- Date To -->
                    <div class="filter-group">
                        <label class="filter-label">To</label>
                        <input type="date" name="date_to" class="filter-control"
                               value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-search">
                         Search
                    </button>
                    <a href="{{ route('admin.stock-issue.index') }}" class="btn-reset">
                         Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>SL. NO</th>
                                <th>EMPLOYEE NAME</th>
                                <th>MATERIAL</th>
                                <th>SERIAL NUMBER</th>
                                <th>ISSUED QTY</th>
                                <th>UNIT</th>
                                <th>DATE</th>
                                <th style="text-align: center;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $index => $transaction)
                                <tr>
                                    <td>{{ ($transactions->currentPage() - 1) * 10 + $index + 1 }}</td>
                                    <td class="employee-name">
                                        {{ $transaction->employee->first_name ?? 'N/A' }}
                                        {{ $transaction->employee->last_name ?? '' }}
                                    </td>
                                    <td>
                                        <a href="#" class="material-name">
                                            {{ $transaction->material->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                   <td>
                                        @if($transaction->serialAllocations->count())
                                            @foreach($transaction->serialAllocations as $alloc)
                                                @if($alloc->serial)
                                                    <span class="serial-badge">{{ $alloc->serial->serial_number }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="serial-badge" style="background: #f3f4f6; color: #6b7280;">Non-serial</span>
                                        @endif
                                    </td>


                                  
                                    <td>{{ $transaction->quantity }}</td>
                                    <td>{{ $transaction->material->base_unit ?? 'Pcs' }}</td>
                                    <td class="date-cell">{{ $transaction->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="actions-cell">
                                            <button type="button" class="btn-action btn-view"
                                                    onclick="viewTransaction({{ $transaction->id }})"
                                                    title="View">
                                                 <i class="ti-eye"></i>
                                            </button>
                                            <button type="button" class="btn-action btn-edit"
                                                    onclick="editTransaction({{ $transaction->id }})"
                                                    title="Edit">
                                                <i class="ti-marker-alt"></i>
                                            </button>
                                            <button type="button" class="btn-action btn-delete"
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

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }}
                        of {{ $transactions->total() }} Entries
                    </div>
                    <div class="pagination">
                        {{ $transactions->links() }}
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="ti-plus"></i></div>
                    <p class="empty-state-text">No stock issue transactions found</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 25px rgba(0,0,0,0.15);">
        <div style="padding: 24px; border-bottom: 1px solid #e2e8f0;">
            <h2 style="margin: 0; font-size: 18px; font-weight: 600; color: #1a202c;">Transaction Details</h2>
        </div>
        <div id="viewModalContent" style="padding: 24px;">
            <!-- Content loaded via AJAX -->
        </div>
        <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; text-align: right;">
            <button type="button" onclick="closeModal('viewModal')"
                    style="padding: 8px 16px; background: #e2e8f0; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                Close
            </button>
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
                    style="flex: 1; padding: 8px; background: #e2e8f0; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
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
<link rel="stylesheet" href="{{ asset('/css/materials.css') }}">
<link rel="stylesheet" href="{{ asset('/css/stockissue.css') }}">

<script>
document.getElementById('district_id').addEventListener('change', function () {
    const districtId = this.value;
    const empSelect = document.getElementById('emp');

 

        fetch(`{{ route('admin.get-employees') }}?district_id=${districtId}`)
            .then(res => res.json())
            .then(data => {
                if(data.success){

                empSelect.innerHTML = '<option value="">All Employees</option>';

                data.employees.forEach(emp => {
                    empSelect.innerHTML += `<option value="${emp.id}">${emp.first_name} ${emp.last_name}</option>`;
                });

              
                empSelect.setAttribute('required', 'required');
            }else{
                showToast('Error:',data.message);

            }
            });

  
});

function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function viewTransaction(id) {
    fetch(`{{ route('admin.stock-issue.show', '') }}/${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('viewModalContent').innerHTML = html;
            openModal('viewModal');
        })
        .catch(error => alert('Error loading transaction'));
}

function editTransaction(id) {
    fetch(`{{ url('admin/stock-issue') }}/${id}/edit`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load transaction');
            }
            return response.text();
        })
        .then(html => {
            document.getElementById('editModalContent').innerHTML = html;
            document.getElementById('editForm').action =
                `{{ url('admin/stock-issue') }}/${id}`;
            openModal('editModal');
        })
        .catch(error => {
            alert(error.message);
        });
}

document.addEventListener('submit', function (e) {

    const form = e.target;
    if (form.id !== 'editForm') return;

    let errors = [];

   let totalSerialQty = 0;
    let valid = true;

    const qtyInputs = this.querySelectorAll(
        'input[name^="serials"][name$="[quantity]"]'
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
            this.querySelector('input[name="quantity"]').value
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
    document.getElementById('deleteForm').action = `{{ route('admin.stock-issue.destroy', '') }}/${id}`;
    openModal('deleteModal');
}

// Close modals when clicking outside
['viewModal', 'editModal', 'deleteModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(id);
        }
    });
});
</script>

@endsection
