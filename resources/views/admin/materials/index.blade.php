@extends('admin.layout.base')

@section('title', 'Material Management')

@section('content')
<div class="terrasoft-main-content">
    <div class="terrasoft-page-container">
        {{-- Page Header --}}
        <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-package text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">Material Management</h1>
                        <p class="terrasoft-page-subtitle">Manage inventory materials and their specifications</p>
                    </div>
                </div>
                <div class="terrasoft-header-stats">
                    <div class="terrasoft-stat-item">
                        <i class="ti-package text-green-600"></i>
                        <span class="terrasoft-stat-number">{{ $materials->total() }}</span>
                        <span class="terrasoft-stat-label">Total Materials</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Bar --}}
        <div class="terrasoft-action-bar">
            <div class="terrasoft-search-container">
                <div class="terrasoft-search-input">
                    <i class="ti-search"></i>
                    <input type="text" placeholder="Search materials..." id="searchInput" value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="terrasoft-action-buttons">
                {{-- Filters --}}
                <div class="terrasoft-filter-dropdown">
                    <button class="terrasoft-filter-btn" id="unitFilterBtn">
                        <i class="ti-filter"></i>
                        <span>{{ request('unit_filter') ? request('unit_filter') : 'All Units' }}</span>
                        <i class="ti-chevron-down"></i>
                    </button>
                    <div class="terrasoft-dropdown-menu" id="unitFilterMenu">
                        <a href="#" class="terrasoft-dropdown-item {{ !request('unit_filter') ? 'active' : '' }}" data-filter="">All Units</a>
                        @foreach($units as $unit)
                            <a href="#" class="terrasoft-dropdown-item {{ request('unit_filter') == $unit ? 'active' : '' }}" data-filter="{{ $unit }}">{{ $unit }}</a>
                        @endforeach
                    </div>
                </div>

                <div class="terrasoft-filter-dropdown">
                    <button class="terrasoft-filter-btn" id="serialFilterBtn">
                        <i class="ti-hash"></i>
                        <span>{{ request('serial_filter') === '1' ? 'With Serial' : (request('serial_filter') === '0' ? 'Without Serial' : 'All Types') }}</span>
                        <i class="ti-chevron-down"></i>
                    </button>
                    <div class="terrasoft-dropdown-menu" id="serialFilterMenu">
                        <a href="#" class="terrasoft-dropdown-item {{ request('serial_filter') === null ? 'active' : '' }}" data-filter="">All Types</a>
                        <a href="#" class="terrasoft-dropdown-item {{ request('serial_filter') === '1' ? 'active' : '' }}" data-filter="1">With Serial</a>
                        <a href="#" class="terrasoft-dropdown-item {{ request('serial_filter') === '0' ? 'active' : '' }}" data-filter="0">Without Serial</a>
                    </div>
                </div>
                
                <button class="terrasoft-btn terrasoft-btn-primary" onclick="openCreateModal()">
                    <i class="ti-plus"></i>
                    Add Material
                </button>
            </div>
        </div>

        {{-- Bulk Actions --}}
        <div class="terrasoft-bulk-actions" id="bulkActions" style="display: none;">
            <div class="terrasoft-bulk-info">
                <span id="selectedCount">0</span> materials selected
            </div>
            <div class="terrasoft-bulk-buttons">
                <button class="terrasoft-btn terrasoft-btn-danger" onclick="bulkDelete()">
                    <i class="ti-trash"></i>
                    Delete Selected
                </button>
            </div>
        </div>

        {{-- Data Table --}}
        <div class="terrasoft-table-container">
            <div class="terrasoft-table-wrapper" id="materialsTable">
                @include('admin.materials.table')
            </div>
        </div>

        {{-- Pagination --}}
        <div class="terrasoft-pagination-container">
            <div class="terrasoft-pagination-info">
                Showing {{ $materials->firstItem() ?? 0 }} to {{ $materials->lastItem() ?? 0 }} of {{ $materials->total() }} entries
            </div>
            <div class="terrasoft-pagination">
                {{ $materials->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Create/Edit Modal --}}
@include('admin.materials.modal')

{{-- Delete Confirmation Modal --}}
@include('admin.materials.delete-modal')

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

<script src="{{ asset('/js/materials.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize material management
    initializeMaterialManagement();
    
    // Show success message if exists
    @if(session('success'))
        showToast('success', '{{ session('success') }}');
    @endif
    
    @if(session('error'))
        showToast('error', '{{ session('error') }}');
    @endif
});
</script>
