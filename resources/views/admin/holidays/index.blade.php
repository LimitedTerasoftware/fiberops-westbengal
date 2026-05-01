@extends('admin.layout.base')

@section('title', 'Holiday Management')

@section('content')

<div class="terrasoft-main-content">
    <div class="terrasoft-page-container">
        <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-calendar text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">Holiday Management</h1>
                        <p class="terrasoft-page-subtitle">Manage holidays and their configurations</p>
                    </div>
                </div>
                <div class="terrasoft-header-stats">
                    <div class="terrasoft-stat-item">
                        <i class="ti-calendar text-green-600"></i>
                        <span class="terrasoft-stat-number">{{$holidays->total() }}</span>
                        <span class="terrasoft-stat-label">Holidays</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="terrasoft-action-bar">
            <form method="GET" action="{{ route('admin.holidays.index') }}" class="form-inline">
                <div class="terrasoft-search-container">
                    <div class="terrasoft-search-input">
                        <i class="ti-search"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search holidays...">
                    </div>
                </div>
            </form>

            <div class="terrasoft-action-buttons">
                <button class="terrasoft-btn terrasoft-btn-primary" onclick="window.location.href='{{ route('admin.holidays.create') }}'">
                    <i class="ti-plus"></i> Add New
                </button>
            </div>
        </div>

        <div class="terrasoft-table-container">
            <div class="terrasoft-table-wrapper">
                <table class="terrasoft-table">
                    <thead>
                        <tr>
                            <th>Holiday Details</th>
                            <th>Date & Duration</th>
                            <th>Type & Scope</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $holiday)
                        <tr class="terrasoft-table-row">
                            <td class="terrasoft-td-primary">
                                <div class="terrasoft-location-info">
                                    <div class="terrasoft-location-name">{{ $holiday->name }}</div>
                                    @if($holiday->description)
                                    <div class="terrasoft-location-code">{{ Str::limit($holiday->description, 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($holiday->date)->format('d M Y') }}</div>
                                <span class="badge badge-{{ $holiday->duration == 'full' ? 'success' : 'warning' }}">{{ ucfirst($holiday->duration) }} Day</span>
                                @if($holiday->is_recurring)
                                <span class="badge badge-info">Recurring</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $holiday->type == 'national' ? 'primary' : ($holiday->type == 'regional' ? 'info' : 'secondary') }}">{{ ucfirst($holiday->type) }}</span>
                                <div class="terrasoft-district">{{ ucfirst($holiday->applies_to) }}</div>
                            </td>
                            <td>
                                <div class="terrasoft-admin-info">
                                    <div class="terrasoft-state">{{ $holiday->state->state_name ?? 'All States' }}</div>
                                    @if($holiday->district)
                                    <div class="terrasoft-district">{{ $holiday->district->name }}</div>
                                    @endif
                                    @if($holiday->block)
                                    <div class="terrasoft-district">{{ $holiday->block->name }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="terrasoft-td-actions">
                                <div class="terrasoft-action-buttons">

                                <button class="terrasoft-action-btn terrasoft-btn-edit" onclick="window.location.href='{{ route('admin.holidays.edit', $holiday->id) }}'">
                                    <i class="ti-pencil"></i>
                                </button>
                                <button class="terrasoft-action-btn terrasoft-btn-delete" onclick="deleteHoliday({{ $holiday->id }})">
                                    <i class="ti-trash"></i>
                                </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No holidays found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="terrasoft-pagination">
                {{ $holidays->links() }}
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('/css/materials.css') }}">
<style>
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

</style>
<script>
function deleteHoliday(id) {
    if (confirm('Are you sure you want to delete this holiday?')) {
        $.ajax({
            url: '/admin/holidays/' + id,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    }
}
</script>

@endsection
