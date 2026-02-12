@extends('admin.layout.base')

@section('title', 'Edit OLT Location')

@section('content')
<div class="terrasoft-main-content">
    <div class="terrasoft-page-container">
        {{-- Page Header --}}
        <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-marker-alt text-orange-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">Edit OLT Location</h1>
                        <p class="terrasoft-page-subtitle">Update the configuration for {{ $location->olt_location ? $location->olt_location :  '-' }}</p>
                    </div>
                </div>
                <div class="terrasoft-header-actions">
                    <a href="{{ route('admin.olt-locations.show', $location->id ) }}" class="terrasoft-btn terrasoft-btn-secondary">
                        <i class="ti-eye"></i>
                        View Details
                    </a>
                    <a href="{{ route('admin.olt-locations.index') }}" class="terrasoft-btn terrasoft-btn-secondary">
                        <i class="ti-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

      

        {{-- Form Container --}}
        <div class="terrasoft-form-container">
            <form action="{{ route('admin.olt-locations.update', $location->id) }}" method="POST" class="terrasoft-form" id="oltEditForm">
                {{csrf_field()}}
                <input type="hidden" name="_method" value="PUT">

                
                {{-- Basic Information Section --}}
                <div class="terrasoft-form-section">
                    <div class="terrasoft-form-grid">

                        <div class="terrasoft-form-group">
                            <label for="district_id" class="terrasoft-form-label">
                                District <span class="terrasoft-required">*</span>
                            </label>
                            <select name="district_id" 
                                    id="district_id"
                                    class="terrasoft-form-input {{ $errors->has('district_id') ? 'terrasoft-input-error' : '' }}"
                                    onchange="loadBlocks()">
                                    <option value="">Select District</option>
                                    @foreach($districts as $dist)
                                        <option value="{{ $dist->id }}" {{  $location->district_id == $dist->id ? 'selected' : '' }}>
                                            {{ $dist->name }}
                                        </option>
                                    @endforeach
                                </select>
                          
                                @if ($errors->has('district_id'))
                                        <div class="terrasoft-error-message">
                                            <i class="fa fa-exclamation-circle"></i> {{ $errors->first('district_id') }}
                                        </div>
                                    @endif
                           
                        </div>
                        
                        <div class="terrasoft-form-group">
                            <label for="sub_district" class="terrasoft-form-label">
                               Block
                            </label>
                             <select name="block_id" 
                                    id="block_id"
                                    class="terrasoft-form-input {{ $errors->has('block_id') ? 'terrasoft-input-error' : '' }}"
                                    >
                                <option value="">Select Block</option>
                                 @foreach($blocks as $block)
                                    <option value="{{ $block->id }}" {{  $location->block_id == $block->id ? 'selected' : '' }}>
                                        {{ $block->name }}
                                    </option>
                                @endforeach
                            </select>
                            
                                     @if ($errors->has('block_id'))
                                        <div class="terrasoft-error-message">
                                            <i class="fa fa-exclamation-circle"></i> {{ $errors->first('block_id') }}
                                        </div>
                                    @endif
                           
                        </div>
                        
                      
                    
                        <div class="terrasoft-form-group">
                            <label for="olt_location" class="terrasoft-form-label">
                                Location Name <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text" 
                                   id="olt_location" 
                                   name="olt_location" 
                                   class="terrasoft-form-input {{ $errors->has('olt_location') ? 'terrasoft-input-error' : '' }}"
                                   placeholder="e.g., Electronic City Phase 1"
                                   value="{{  $location->olt_location  }}"
                                   required>
                                    @if ($errors->has('olt_location'))
                                        <div class="terrasoft-error-message">
                                            <i class="fa fa-exclamation-circle"></i> {{ $errors->first('olt_location') }}
                                        </div>
                                    @endif
                        </div>
                        
                        <div class="terrasoft-form-group">
                            <label for="olt_location_code" class="terrasoft-form-label">
                                Location Code <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text" 
                                   id="olt_location_code" 
                                   name="olt_location_code" 
                                   class="terrasoft-form-input {{ $errors->has('olt_location_code') ? 'terrasoft-input-error' : '' }}"
                                   placeholder="e.g., EC01"
                                   value="{{ $location->olt_location_code  }}"
                                   required>
                                   @if ($errors->has('olt_location_code'))
                                        <div class="terrasoft-error-message">
                                            <i class="fa fa-exclamation-circle"></i> {{ $errors->first('olt_location_code') }}
                                        </div>
                                    @endif
                        </div>
                          <div class="terrasoft-form-group">
                            <label for="lgd_code" class="terrasoft-form-label">
                                LGD Code <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text" 
                                   id="lgd_code" 
                                   name="lgd_code" 
                                   class="terrasoft-form-input {{ $errors->has('lgd_code') ? 'terrasoft-input-error' : '' }}"
                                   placeholder="e.g., KA001"
                                   value="{{$location->lgd_code }}"
                                   required>
                           @if ($errors->has('lgd_code'))
                                        <div class="terrasoft-error-message">
                                            <i class="fa fa-exclamation-circle"></i> {{ $errors->first('lgd_code') }}
                                        </div>
                                    @endif
                        </div>
                           <div class="terrasoft-form-group">
                            <label for="olt_ip" class="terrasoft-form-label">
                                IP Address <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text" 
                                   id="olt_ip" 
                                   name="olt_ip" 
                                   class="terrasoft-form-input {{ $errors->has('olt_ip') ? 'terrasoft-input-error' : '' }}"                                   placeholder="e.g., 192.168.1.10"
                                   value="{{ $location->olt_ip }}"
                                   pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                                   required>
                                     @if ($errors->has('olt_ip'))
                                        <div class="terrasoft-error-message">
                                            <i class="fa fa-exclamation-circle"></i> {{ $errors->first('olt_ip') }}
                                        </div>
                                    @endif
                           
                        </div>
                        
                        <div class="terrasoft-form-group">
                            <label for="no_of_gps" class="terrasoft-form-label">
                                GP Count <span class="terrasoft-required">*</span>
                            </label>
                            <input type="number" 
                                   id="no_of_gps" 
                                   name="no_of_gps" 
                                    class="terrasoft-form-input {{ $errors->has('no_of_gps') ? 'terrasoft-input-error' : '' }}"
                                   placeholder="e.g., 25"
                                   value="{{  $location->no_of_gps }}"
                                   min="1"
                                   max="1000"
                                   required>
                              @if ($errors->has('no_of_gps'))
                                        <div class="terrasoft-error-message">
                                            <i class="fa fa-exclamation-circle"></i> {{ $errors->first('no_of_gps') }}
                                        </div>
                                    @endif
                        </div>
                    </div>
                </div>

               {{-- Form Actions --}}
                <div class="terrasoft-form-actions">
                    <button type="button" 
                            class="terrasoft-btn terrasoft-btn-secondary"
                            onclick="window.location.href='{{ route('admin.olt-locations.show', $location->id) }}'">
                        <i class="ti-x"></i>
                        Cancel
                    </button>
                    <button type="submit" class="terrasoft-btn terrasoft-btn-primary" id="updateBtn">
                        <i class="ti-check"></i>
                        Update Location
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('/css/olt.css')}}">

<style>
    .terrasoft-form-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    overflow: hidden;
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

.terrasoft-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
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
.terrasoft-form-select,
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
.terrasoft-form-select:focus,
.terrasoft-form-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.terrasoft-form-input::placeholder,
.terrasoft-form-textarea::placeholder {
    color: #9ca3af;
}

.terrasoft-input-error {
    border-color: #ef4444;
}

.terrasoft-input-error:focus {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.terrasoft-error-message {
    font-size: 12px;
    color: #ef4444;
    margin-top: 4px;
}

.terrasoft-form-textarea {
    resize: vertical;
    min-height: 100px;
}

.terrasoft-form-actions {
    padding: 24px 32px;
    background: #f8fafc;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

/* Loading State */
.terrasoft-btn.loading {
    opacity: 0.7;
    cursor: not-allowed;
}

.terrasoft-btn.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .terrasoft-form-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .terrasoft-form-section {
        padding: 24px 16px;
    }
    
    .terrasoft-form-actions {
        padding: 16px;
        flex-direction: column;
    }
    
    .terrasoft-header-content {
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }
}
/* Alert Styles */
.terrasoft-alert {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
}

.terrasoft-alert-info {
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}

.terrasoft-alert i {
    font-size: 18px;
    flex-shrink: 0;
}

/* Change Log Styles */
.terrasoft-change-log {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.terrasoft-change-item {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 3px solid #e2e8f0;
}

.terrasoft-change-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.terrasoft-change-content {
    flex: 1;
}

.terrasoft-change-title {
    font-weight: 500;
    color: #1e293b;
    margin-bottom: 4px;
}

.terrasoft-change-details {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 4px;
}

.terrasoft-change-meta {
    font-size: 12px;
    color: #9ca3af;
}

/* Header Actions */
.terrasoft-header-actions {
    display: flex;
    gap: 12px;
}

@media (max-width: 768px) {
    .terrasoft-header-actions {
        flex-direction: column;
        width: 100%;
    }
}
</style>

<script>
     let selectedBlockId = '{{ old('block_id') }}';

function loadBlocks() {
    const districtId = document.getElementById('district_id').value;
    const blockSelect = document.getElementById('block_id');
    
    // Reset blocks
    blockSelect.innerHTML = '<option value="">Select Block</option>';
    blockSelect.disabled = true;
    
    if (!districtId) return;
    let baseUrl = "{{ url('/admin') }}";
    fetch(`${baseUrl}/blocks?district_id=${districtId}`)
        .then(response => response.json())
        .then(data => {
            data.forEach(block => {
                const option = document.createElement('option');
                option.value = block.id;
                option.textContent = block.name;
                if (block.id == selectedBlockId) {
                    option.selected = true;
                }
                blockSelect.appendChild(option);
            });
            blockSelect.disabled = false;
        })
        .catch(error => console.error('Error loading blocks:', error));
}
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('oltEditForm');
    const updateBtn = document.getElementById('updateBtn');
    
    // Track form changes
    let originalFormData = new FormData(form);
    let hasChanges = false;
    
    // Monitor form changes
    form.addEventListener('input', function() {
        const currentFormData = new FormData(form);
        hasChanges = false;
        
        for (let [key, value] of currentFormData.entries()) {
            if (originalFormData.get(key) !== value) {
                hasChanges = true;
                break;
            }
        }
        
        // Update button state
        if (hasChanges) {
            updateBtn.classList.add('terrasoft-btn-warning');
            updateBtn.innerHTML = '<i class="ti-alert-circle"></i> Save Changes';
        } else {
            updateBtn.classList.remove('terrasoft-btn-warning');
            updateBtn.innerHTML = '<i class="ti-check"></i> Update Location';
        }
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!hasChanges) {
            alert('No changes detected. Please make some changes before updating.');
            return;
        }
        
        // Show loading state
        updateBtn.classList.add('loading');
        updateBtn.innerHTML = '<i class="ti-loader"></i> Updating...';
        updateBtn.disabled = true;
        
        // Validate IP address
        const ipInput = document.getElementById('olt_ip');
        const ipPattern = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        
        if (!ipPattern.test(ipInput.value)) {
            alert('Please enter a valid IP address');
            resetUpdateButton();
            return;
        }
        
        // Submit form
        setTimeout(() => {
            form.submit();
        }, 1000);
    });
    
    function resetUpdateButton() {
        updateBtn.classList.remove('loading');
        updateBtn.innerHTML = hasChanges ? '<i class="ti-alert-circle"></i> Save Changes' : '<i class="ti-check"></i> Update Location';
        updateBtn.disabled = false;
    }
    
    // // Warn about unsaved changes
    // window.addEventListener('beforeunload', function(e) {
    //     if (hasChanges) {
    //         e.preventDefault();
    //         e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
    //     }
    // });
    
    // Real-time IP validation
    document.getElementById('olt_ip').addEventListener('input', function() {
        const ip = this.value;
        const ipPattern = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        
        if (ip && !ipPattern.test(ip)) {
            this.classList.add('terrasoft-input-error');
        } else {
            this.classList.remove('terrasoft-input-error');
        }
    });
});
</script>

<style>
.terrasoft-btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.terrasoft-btn-warning:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
}
</style>
@endsection