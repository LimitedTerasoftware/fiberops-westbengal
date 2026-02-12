@extends('admin.layout.base')

@section('title', 'OLT Management System')

@section('content')

<div class="terrasoft-main-content">
    <div class="terrasoft-page-container">
        {{-- Page Header --}}
        <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-server text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">OLT Management System</h1>
                        <p class="terrasoft-page-subtitle">Manage Optical Line Terminal locations and configurations</p>
                    </div>
                </div>
                <div class="terrasoft-header-stats">
                    <div class="terrasoft-stat-item">
                        <i class="ti-map-pin text-green-600"></i>
                        <span class="terrasoft-stat-number">{{$oltLocations->total() }}</span>
                        <span class="terrasoft-stat-label">Locations</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Bar --}}
        <div class="terrasoft-action-bar">
            <form method="GET" action="{{ route('admin.olt-locations.index') }}" class="form-inline">

            <div class="terrasoft-search-container">
                <div class="terrasoft-search-input">
                    <i class="ti-search"></i>
                     <input type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Search locations..."
                    >
                   
                </div>
            </div>
            </form>
            
            <div class="terrasoft-action-buttons">
                
                
                <button class="terrasoft-btn terrasoft-btn-primary" onclick="window.location.href='{{ route('admin.olt-locations.create') }}'">
                    <i class="ti-plus"></i>
                    Add New
                </button>
                <button id="export-btn" class="btn btn-export">
                    <span id="btn-text"><i class="fa fa-download me-2"></i>Export</span>
                    <span id="btn-loading" style="display:none;"><i class="fa fa-spinner fa-spin me-2"></i>Loading...</span>
                </button>

            </div>
        </div>

        {{-- Data Table --}}
        <div class="terrasoft-table-container">
            <div class="terrasoft-table-wrapper">
                <table class="terrasoft-table">
                    <thead>
                        <tr>
                            <th class="terrasoft-th-sortable" data-sort="name">
                                <span>Location Details</span>
                                <i class="ti-chevron-up"></i>
                            </th>
                            <th class="terrasoft-th-sortable" data-sort="administrative">
                                <span>Administrative</span>
                                <i class="ti-chevron-up"></i>
                            </th>
                            <th class="terrasoft-th-sortable" data-sort="technical">
                                <span>Technical</span>
                                <i class="ti-chevron-up"></i>
                            </th>
                            <th class="terrasoft-th-sortable" data-sort="created">
                                <span>Created</span>
                                <i class="ti-chevron-up"></i>
                            </th>
                            <th class="terrasoft-th-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                         @forelse($oltLocations as $location)
                        <tr class="terrasoft-table-row">
                            <td class="terrasoft-td-primary">
                                <div class="terrasoft-location-info">
                                    <div class="terrasoft-location-name">{{ $location->olt_location }}</div>
                                    <div class="terrasoft-location-code">Code: {{ $location->olt_location_code }}</div>
                                </div>
                            </td>
                            <td class="terrasoft-td-secondary">
                                <div class="terrasoft-admin-info">
                                    <div class="terrasoft-state">{{ $location->state->name }}</div>
                                    <div class="terrasoft-district">{{ $location->district->name }} • {{ $location->block->name }}</div>
                                    <div class="terrasoft-lgd">LGD: {{ $location->lgd_code }}</div>
                                </div>
                            </td>
                            <td class="terrasoft-td-technical">
                                <div class="terrasoft-technical-info">
                                    <div class="terrasoft-ip">{{ $location->olt_ip }}</div>
                                    <div class="terrasoft-gps">{{ $location->no_of_gps }} GPs</div>
                                </div>
                            </td>
                            <td class="terrasoft-td-date">
                                <div class="terrasoft-date">{{ $location->created_at->format('M d, Y') }}</div>
                            </td>
                           <td class="terrasoft-td-actions">
                                <div class="terrasoft-action-buttons">
                                    {{-- View --}}
                                    <a href="{{ url('/admin/olt-locations/' . $location->id) }}" 
                                    class="terrasoft-action-btn terrasoft-btn-view" 
                                    title="View Details">
                                        <i class="ti-eye"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ url('/admin/olt-locations/' . $location->id . '/edit') }}" 
                                    class="terrasoft-action-btn terrasoft-btn-edit" 
                                    title="Edit">
                                        <i class="ti-marker-alt"></i>
                                    </a>
                                    <button class="terrasoft-action-btn terrasoft-btn-danger"   onclick="deleteLocation({{ $location->id }}, '{{ $location->olt_location }}')">
                                        <i class="ti-trash"></i>
                                      
                                    </button>
                                  
                                </div>
                            </td>

                        </tr>
                         @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    No OLT locations found
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="pagination-section">
            <div class="pagination-info">
                Showing 
                {{ ($oltLocations->currentPage() - 1) * $oltLocations->perPage() + 1 }} 
                to 
                {{ ($oltLocations->currentPage() * $oltLocations->perPage()) > $oltLocations->total() 
                    ? $oltLocations->total() 
                    : $oltLocations->currentPage() * $oltLocations->perPage() 
                }} 
                of {{ $oltLocations->total() }} entries
            </div>
            <nav class="pagination-nav">
                {{ $oltLocations->appends(request()->all())->links('pagination::bootstrap-4') }}

            </nav>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="terrasoft-modal" data-location-id="">
    <div class="terrasoft-modal-content">
        <div class="terrasoft-modal-header">
            <h3>Confirm Delete</h3>
            <button class="terrasoft-modal-close" onclick="closeDeleteModal()">
                <i class="ti-x"></i>
            </button>
        </div>
        <div class="terrasoft-modal-body">
            <div class="terrasoft-delete-warning">
                <i class="ti-alert-triangle text-red-500"></i>
                <p>Are you sure you want to delete this <strong id="locationName"></strong> OLT location? This action cannot be undone.</p>
            </div>
        </div>
        <div class="terrasoft-modal-footer">
            <button class="terrasoft-btn terrasoft-btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <button class="terrasoft-btn terrasoft-btn-danger" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('/css/olt.css')}}">
<style>
  .btn-export {
    background-color: #38a169;
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-export:hover {
    background-color: #2f855a;
    transform: translateY(-1px);
    color:white;
}  
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>


$(document).ready(function(){
    $('#export-btn').on('click',function(){
        const btnText = $('#btn-text');
        const btnLoading = $('#btn-loading');
        btnText.hide();
        btnLoading.show();
       fetch(`{{ route('admin.olt-export', ['search' => request('search')]) }}`)
       .then(response=>{
            if(!response.ok) throw new Error('Network response was not ok');
            return response.blob();
        }).then(blob=>{
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = "olt.xlsx";
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);

        }).catch(error=>{
             alert("Export failed! Please try again.");
        }).finally(()=>{
            btnText.show();
            btnLoading.hide();
        });


    });
});





function deleteLocation(id,name) {
    const modal = document.getElementById('deleteModal');
    modal.dataset.locationId = id;
    document.getElementById('locationName').textContent = name;
    modal.classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}

function confirmDelete() {
    const modal = document.getElementById('deleteModal');
    const id = modal.dataset.locationId;
    
    fetch(`/admin/olt-locations/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error deleting location');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting location');
    });
    
    closeDeleteModal();
}
</script>
@endsection