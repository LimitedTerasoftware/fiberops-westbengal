@extends('admin.layout.base')

@section('title', 'Employee Stock Report')

@section('content')
<style>
    :root {
        --primary: #2196F3;
        --success: #4CAF50;
        --danger: #f44336;
        --warning: #FF9800;
        --light: #f8f9fa;
        --border: #e0e0e0;
        --text-primary: #333;
        --text-secondary: #666;
        --text-muted: #999;
    }

    .sr-container {
        padding: 24px;
        background: #f8fafc;
        min-height: 100vh;
    }

    .sr-header {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--border);
    }

    .sr-header h1 {
        color: var(--text-primary);
        font-weight: 700;
        font-size: 28px;
        margin: 0 0 8px 0;
    }

    .sr-header p {
        color: var(--text-muted);
        font-size: 14px;
        margin: 0;
    }

    .sr-filters {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid var(--border);
    }

    .sr-filters form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 15px;
        align-items: flex-end;
    }

    .sr-filter-group {
        display: flex;
        flex-direction: column;
    }

    .sr-filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: 0.5px;
    }

    .sr-filter-group select,
    .sr-filter-group input {
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 4px;
        font-size: 13px;
        background: white;
        transition: all 0.3s ease;
    }

    .sr-filter-group select:focus,
    .sr-filter-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
    }

    .sr-btn-filter {
        background: var(--success);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sr-btn-filter:hover {
        background: #45a049;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(76, 175, 80, 0.2);
    }
  .btn-reset {
        background-color: #e2e8f0;
        color: #4a5568;
        padding: 8px 20px;
        border-radius: 4px;
    }

    .btn-reset:hover {
        background-color: #cbd5e0;
    }

    .sr-table-wrapper {
        background: white;
        border-radius: 8px;
        border: 1px solid var(--border);
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }

    .sr-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    .sr-table thead {
        background: var(--light);
        border-bottom: 2px solid var(--border);
    }

    .sr-table th {
        padding: 14px 16px;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: left;
    }

    .sr-table th:nth-child(3),
    .sr-table th:nth-child(4),
    .sr-table th:nth-child(5),
    .sr-table th:nth-child(6),
    .sr-table th:nth-child(7) {
        text-align: center;
    }

    .sr-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: all 0.2s ease;
    }

    .sr-table tbody tr:hover {
        background: var(--light);
    }

    .sr-table td {
        padding: 14px 16px;
        color: var(--text-primary);
        font-size: 13px;
    }

    .sr-table td:nth-child(3),
    .sr-table td:nth-child(4),
    .sr-table td:nth-child(5),
    .sr-table td:nth-child(6),
    .sr-table td:nth-child(7) {
        text-align: center;
    }

    .sr-employee-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .sr-material-name {
        color: var(--text-secondary);
    }

    .sr-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .sr-badge-serial {
        background: rgba(33, 150, 243, 0.15);
        color: var(--primary);
    }

    .sr-badge-bulk {
        background: rgba(156, 39, 176, 0.15);
        color: #9C27B0;
    }

    .sr-issued {
        color: var(--success);
        font-weight: 600;
    }

    .sr-used {
        color: var(--danger);
        font-weight: 600;
    }

    .sr-balance {
        background: var(--light);
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .sr-action-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: none;
        background: none;
        padding: 0;
    }

    .sr-action-link:hover {
        color: #1976D2;
        text-decoration: underline;
    }

    .sr-empty {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .sr-empty i {
        font-size: 48px;
        color: #ddd;
        margin-bottom: 16px;
        display: block;
    }

    .sr-empty p {
        font-size: 14px;
        margin: 0;
    }

    .sr-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 30px;
        padding: 20px 0;
        flex-wrap: wrap;
    }

    .sr-pagination a,
    .sr-pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        border: 1px solid var(--border);
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .sr-pagination a:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(33, 150, 243, 0.05);
    }

    .sr-pagination .active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .sr-pagination .disabled {
        color: #ddd;
        cursor: not-allowed;
    }

    .sr-modal-header {
        background: var(--light);
        border-bottom: 2px solid var(--border);
        padding: 20px !important;
    }

    .sr-modal-title {
        color: var(--text-primary);
        font-weight: 700;
        font-size: 16px;
        margin: 0 0 4px 0;
    }

    .sr-modal-subtitle {
        color: var(--text-muted);
        font-size: 12px;
        margin: 0;
    }

    .sr-serial-item {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s ease;
    }

    .sr-serial-item:hover {
        background: var(--light);
    }

    .sr-serial-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .sr-serial-number {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sr-serial-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 16px;
        padding: 12px 0;
    }

    .sr-stat-box {
        display: flex;
        flex-direction: column;
    }

    .sr-stat-label {
        font-size: 10px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }

    .sr-stat-value {
        font-weight: 700;
        font-size: 16px;
    }

    .sr-ticket-list {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--border);
    }

    .sr-ticket-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin-bottom: 8px;
        display: block;
        letter-spacing: 0.5px;
    }

    .sr-ticket-item {
        font-size: 12px;
        color: var(--text-secondary);
        padding: 4px 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sr-modal-body {
        max-height: 70vh;
        overflow-y: auto;
        padding: 0 !important;
    }

    .sr-modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .sr-modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .sr-modal-body::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .sr-modal-body::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .sr-modal-footer {
        background: var(--light);
        border-top: 1px solid var(--border);
        padding: 15px 20px !important;
    }

    @media (max-width: 768px) {
        .sr-filters form {
            grid-template-columns: 1fr;
        }

        .sr-header h1 {
            font-size: 22px;
        }

        .sr-table th,
        .sr-table td {
            padding: 10px 8px;
            font-size: 12px;
        }

        .sr-pagination {
            gap: 4px;
        }

        .sr-pagination a,
        .sr-pagination span {
            min-width: 32px;
            height: 32px;
            font-size: 12px;
        }
    }
</style>

<div class="sr-container">
    <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-list text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">Employee Stock Report</h1>
                        <p class="terrasoft-page-subtitle">Track material distribution and usage across employees</p>
                    </div>
                </div>
                <!-- <div class="terrasoft-header-actions">
                    <a href="{{ route('admin.stock-issue.index') }}" class="terrasoft-btn terrasoft-btn-primary">
                      
                          Issue Stock
                    </a>
                </div> -->
            </div>
        </div>
  

    <div class="sr-filters">
        <form method="GET" action="{{ route('admin.stock-report') }}">
            <div class="sr-filter-group">
                <label>District</label>
                <select name="district" id="district_id" >
                    <option value="">All Districts</option>
                    @foreach($districts ?? [] as $district)
                        <option value="{{ $district->id }}" {{ request('district') == $district->id ? 'selected' : '' }}>
                            {{ $district->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sr-filter-group">
                <label>Employee</label>
                <select name="employee_id"  id="emp">
                    <option value="">Select Employees</option>
                </select>
            </div>

            <div class="sr-filter-group">
                <label>Material</label>
                <select name="material_id">
                    <option value="">All Materials</option>
                    @foreach($materials ?? [] as $material)
                        <option value="{{ $material->id }}" {{ request('material_id') == $material->id ? 'selected' : '' }}>
                            {{ $material->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sr-filter-group">
                <label>From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}">
            </div>

            <div class="sr-filter-group">
                <label>To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}">
            </div>
            <div class="sr-filter-group">
                <label>Search</label>
                <input type="text" name="search" 
                  placeholder="Search ..." value="{{ request('search') }}">
            </div>

            <button type="submit" class="sr-btn-filter">
                Filter
            </button>
             <a href="{{ route('admin.stock-report') }}" class="btn-reset">
                         Reset
            </a>
        </form>
    </div>

    <div class="sr-table-wrapper">
        @if($report->count() > 0)
            <table class="sr-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Material</th>
                        <th>Type</th>
                        <th>Issued</th>
                        <th>Used</th>
                        <th>Balance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report as $item)
                        <tr>
                            <td><span class="sr-employee-name">{{ $item['employee'] }}</span></td>
                            <td><span class="sr-material-name">{{ $item['material'] }}</span></td>
                            <td>
                                @if($item['is_serial'])
                                    <span class="sr-badge sr-badge-serial">Serial</span>
                                @else
                                    <span class="sr-badge sr-badge-bulk">Non Serial</span>
                                @endif
                            </td>
                            <td><span class="sr-issued">{{ number_format($item['issued'], 2) }}({{$item['baseunit']}})</span></td>
                            <td><span class="sr-used">{{ number_format($item['used'], 2) }}({{$item['baseunit']}})</span></td>
                            <td><span class="sr-balance">{{ number_format($item['balance'], 2) }}({{$item['baseunit']}})</span></td>
                            <td>
                                    @if(
                                        ($item['is_serial'] && count($item['serials']) > 0) ||
                                        (!$item['is_serial'] && count($item['issued_indents']) > 0)
                                    )
                                    <button type="button" class="sr-action-link" data-toggle="modal" data-target="#detailsModal" onclick="loadSerialDetails('{{ json_encode($item) }}')">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                @else
                                    <span style="color: #ddd;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($report->hasPages())
                <div class="sr-pagination">
                    {{ $report->links() }}
                </div>
            @endif
        @else
            <div class="sr-empty">
                <i class="fa fa-inbox"></i>
                <p>No stock records found. Try adjusting your filters.</p>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0">
            <div class="sr-modal-header">
                <h5 class="sr-modal-title">Serial-wise Stock Details</h5>
                <p class="sr-modal-subtitle">
                    <span id="modalEmployee"></span> • <span id="modalMaterial"></span>
                </p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="sr-modal-body" id="serialDetailsContainer">
            </div>

            <div class="sr-modal-footer text-right">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('/css/materials.css') }}">

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
function loadSerialDetails(itemJson) {
    const item = JSON.parse(itemJson);

    document.getElementById('modalEmployee').textContent = item.employee;
    document.getElementById('modalMaterial').textContent = item.material;

  

    let html = '';
     if (item.is_serial) {
          if (!item.serials || item.serials.length === 0) {
        document.getElementById('serialDetailsContainer').innerHTML =
            '<div class="sr-empty" style="padding: 40px 20px;"><i class="fa fa-inbox"></i><p>No serial details available</p></div>';
        return;
    }

         item.serials.forEach((serial) => {
        html += `
            <div class="sr-serial-item">
                <div class="sr-serial-header">
                    <span class="sr-serial-number">
                        <i class="fa fa-barcode"></i>
                        ${serial.serial_number}
                    </span>
                </div>

                <div class="sr-serial-stats">
                    <div class="sr-stat-box">
                        <span class="sr-stat-label">Issued</span>
                        <span class="sr-stat-value" style="color: var(--success);">${parseFloat(serial.issued).toFixed(2)}</span>
                    </div>
                    <div class="sr-stat-box">
                        <span class="sr-stat-label">Used</span>
                        <span class="sr-stat-value" style="color: var(--danger);">${parseFloat(serial.used).toFixed(2)}</span>
                    </div>
                    <div class="sr-stat-box">
                        <span class="sr-stat-label">Balance</span>
                        <span class="sr-stat-value" style="color: var(--warning);">${parseFloat(serial.balance).toFixed(2)}</span>
                    </div>
                </div>
        `;
         if (serial.issued_indents && serial.issued_indents.length > 0) {
                html += `<div class="sr-ticket-list">
                            <span class="sr-ticket-label">Issued (Indent-wise)</span>`;
                serial.issued_indents.forEach(ind => {
                    html += `
                        <div class="sr-ticket-item">
                            <i class="fa fa-file-text"></i>
                            Indent ${ind.indent_no} :
                            <strong>${Number(ind.qty).toFixed(2)}</strong>
                        </div>`;
                });
                html += `</div>`;
            }

        if (serial.tickets && serial.tickets.length > 0) {
            html += '<div class="sr-ticket-list"><span class="sr-ticket-label">Usage by Ticket</span>';

            serial.tickets.forEach(ticket => {
                html += `
                    <div class="sr-ticket-item">
                        <i class="fa fa-ticket"></i>
                        <strong>Ticket ${ticket.ticket_id || 'N/A'}</strong>: <span style="color: var(--danger); font-weight: 600;">${parseFloat(ticket.used).toFixed(2)}</span>
                    </div>
                `;
            });

            html += '</div>';
        }

        html += '</div>';
    });
}else{
       html += `
            <div class="sr-serial-item">
                  <div class="sr-serial-stats">
                    <div class="sr-stat-box">
                        <span class="sr-stat-label">Issued</span>
                        <span class="sr-stat-value" style="color: var(--success);">${parseFloat(item.issued).toFixed(2)}</span>
                    </div>
                    <div class="sr-stat-box">
                        <span class="sr-stat-label">Used</span>
                        <span class="sr-stat-value" style="color: var(--danger);">${parseFloat(item.used).toFixed(2)}</span>
                    </div>
                    <div class="sr-stat-box">
                        <span class="sr-stat-label">Balance</span>
                        <span class="sr-stat-value" style="color: var(--warning);">${parseFloat(item.balance).toFixed(2)}</span>
                    </div>
                </div>
              
        `;
          if (item.issued_indents && item.issued_indents.length > 0) {
            html += `<div class="sr-ticket-list">
                        <span class="sr-ticket-label">Issued (Indent-wise)</span>`;
            item.issued_indents.forEach(ind => {
                html += `
                    <div class="sr-ticket-item">
                        <i class="fa fa-file-text"></i>
                        Indent ${ind.indent_no} :
                        <strong>${Number(ind.qty).toFixed(2)}</strong>
                    </div>`;
            });
            html += `</div>`;
        }


        if (item.tickets && item.tickets.length > 0) {
            html += '<div class="sr-ticket-list"><span class="sr-ticket-label">Usage by Ticket</span>';


            item.tickets.forEach(ticket => {
                html += `
                   <div class="sr-ticket-item">
                        <i class="fa fa-ticket"></i>
                        <strong>Ticket ${ticket.ticket_id || 'N/A'}</strong>: <span style="color: var(--danger); font-weight: 600;">${parseFloat(ticket.used).toFixed(2)}</span>
                    </div>
                   
                `;
            });

            html += `</div>`;
        } else {
            html += `<p>No ticket usage found</p>`;
        }

        html += `</div>`;

}

    document.getElementById('serialDetailsContainer').innerHTML = html;
}
</script>

@endsection
