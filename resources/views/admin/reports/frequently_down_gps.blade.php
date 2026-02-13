@extends('admin.layout.base')

@section('title', 'Frequently Down GP Report')

@section('content')
    <div class="content-area dashboard-page py-1">
        <div class="container-fluid">
            <div class="row header-row mb-3">
                <div class="col-md-12">
                    <h4 class="mb-0">Frequently Down GPs</h4>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-card">
                <form action="{{ route('admin.frequently_down_gps') }}" method="GET">

                    <div class="filter-row">
                       
                            <div class="badge bg-warning text-dark me-2">
                                <i class="bi bi-calendar-check-fill"></i> Weekly Persistent
                            </div>
                      

                        <div class="filter-pill">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <select name="district_id" onchange="this.form.submit()">
                                <option value="">All Districts</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ (isset($district_id) && $district_id == $district->id) ? 'selected' : '' }}>{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if(isset($blocks) && count($blocks) > 0)
                            <div class="filter-pill">
                                <i class="bi bi-geo text-danger"></i>
                                <select name="block_id" onchange="this.form.submit()">
                                    <option value="">All Blocks</option>
                                    @foreach($blocks as $block)
                                        <option value="{{ $block->id }}" {{ (isset($block_id) && $block_id == $block->id) ? 'selected' : '' }}>{{ $block->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="filter-pill">
                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                            <select name="issue_filter" onchange="this.form.submit()">
                                <option value="">All Issues</option>
                                @foreach($allIssues as $issue)
                                    <option value="{{ $issue }}" {{ (isset($issue_filter) && $issue_filter == $issue) ? 'selected' : '' }}>{{ $issue }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-pill">
                            <i class="bi bi-calendar-week-fill text-warning"></i>
                            <input type="date" name="from_date" value="{{ $from_date }}" placeholder="From Date">
                        </div>
                        <div class="filter-pill">
                            <i class="bi bi-calendar-week-fill text-warning"></i>
                            <input type="date" name="to_date" value="{{ $to_date }}" placeholder="To Date">
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm btn-apply">Apply</button>
                        <a href="{{ route('admin.frequently_down_gps') }}"
                            class="btn btn-secondary btn-sm btn-apply">Reset</a>
                    </div>
                </form>
            </div>

            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped new-table">
                        <thead>
                            <tr>
                                <th>SL No</th>
                                <th>District</th>
                                <th>Block (Mandal)</th>
                                <th>GP Name (LGD Code)</th>
                                <th>Total Tickets</th>
                                <th>Issue Breakdown</th>
                                <th>Issue %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($results as $index => $row)
                                <tr>
                                    <td>{{ $results->firstItem() + $index }}</td>
                                    <td>{{ $row->district }}</td>
                                    <td>{{ $row->mandal }}</td>
                                    <td>{{ $row->gpname }} ({{ $row->lgd_code }})</td>
                                    <td>
                                        <span class="ticket-badge ticket-danger">{{ $row->ticket_count }}</span>
                                    </td>
                                    <td>
                                        @if(isset($row->breakdown) && count($row->breakdown) > 0)
                                            <ul style="list-style: none; padding: 0; margin: 0; font-size: 11px;">
                                                @foreach($row->breakdown as $bd)
                                                    <li class="mb-1 d-flex justify-content-between">
                                                        <span>{{ $bd->downreason }}:</span>
                                                        <span class="fw-bold">{{ $bd->count }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($row->breakdown) && count($row->breakdown) > 0 && $row->ticket_count > 0)
                                            <ul style="list-style: none; padding: 0; margin: 0; font-size: 11px;">
                                                @foreach($row->breakdown as $bd)
                                                    @php
                                                        $pct = round(($bd->count / $row->ticket_count) * 100, 1);
                                                    @endphp
                                                    <li class="mb-1 d-flex justify-content-between">
                                                        <span>{{ $bd->downreason }}:</span>
                                                        <span class="fw-bold">{{ $pct }}%</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                   <div class="pagination-section">
                        <div class="pagination-info">
                            Showing 
                            {{ ($results->currentPage() - 1) * $results->perPage() + 1 }} 
                            to 
                            {{ ($results->currentPage() * $results->perPage()) > $results->total() 
                                ? $results->total() 
                                : $results->currentPage() * $results->perPage() 
                            }} 
                            of {{ $results->total() }} entries
                        </div>
                        <nav class="pagination-nav">
                            {{ $results->appends(request()->input())->links() }}
                        </nav>
                    </div>

            
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Pagination */
.pagination-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 0.5rem;
}

.pagination-info {
    font-size: 0.875rem;
    color: #718096;
}

.pagination {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 0.25rem;
}

.page-item {
    display: block;
}

.page-link {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    color: #4a5568;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.page-item.active .page-link {
    background-color: #4299e1;
    border-color: #4299e1;
    color: white;
}

.page-item.disabled .page-link {
    color: #a0aec0;
    cursor: not-allowed;
}

.page-link:hover:not(.disabled) {
    background-color: #f7fafc;
    border-color: #cbd5e0;
}
        .dashboard-page {
            background-color: #f8fafc;
        }

        .filter-card {
            background: #fff;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .filter-pill {
            border-radius: 12px;
            padding: 6px 15px;
            border: 1px solid #e0e0e0;
            background: #fff;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-pill select,
        .filter-pill input {
            border: none;
            background: transparent;
            height: auto;
            outline: none;
        }

        .filter-row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-apply {
            border-radius: 20px;
        }

        .table-wrapper {
            background: #fff;
            padding: 0;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .new-table {
            width: 100%;
            margin-bottom: 0;
        }

        .new-table thead th {
            background: #f9fafb;
            font-weight: 600;
            padding: 12px;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        .new-table tbody td {
            padding: 12px;
            vertical-align: middle;
        }

        .ticket-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .ticket-danger {
            background: #fee2e2;
            color: #dc2626;
        }
        
@media (max-width: 768px) {
.pagination-section {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

}
    </style>
@endsection