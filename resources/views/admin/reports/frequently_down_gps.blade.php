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
                                <th>Total Downtime Tickets</th>
                                <th>Top Reason</th>
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
                                    <td>{{ $row->top_reason }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 p-2">
                    {{ $results->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
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
    </style>
@endsection