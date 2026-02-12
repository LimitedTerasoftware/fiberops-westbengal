@extends('admin.layout.base')

@section('title', 'Add New OLT Location')

@section('content')
<div class="terrasoft-main-content">
    <div class="terrasoft-page-container">
        {{-- Page Header --}}
        <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-plus text-green-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">Add New OLT Location</h1>
                        <p class="terrasoft-page-subtitle">Create a new Optical Line Terminal location configuration</p>
                    </div>
                </div>
                <div class="terrasoft-header-actions">
                    <a href="{{ route('admin.olt-locations.index') }}" class="terrasoft-btn terrasoft-btn-secondary">
                        <i class="ti-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        {{-- Form Container --}}
        <div class="terrasoft-form-container">
            <form action="{{ route('admin.olt-locations.store') }}" method="POST" class="terrasoft-form" id="oltForm">
               {{csrf_field()}}
                
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
                                        <option value="{{ $dist->id }}" {{ old('district_id') == $dist->id ? 'selected' : '' }}>
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
                            <label for="block_id" class="terrasoft-form-label">
                                Block <span class="terrasoft-required">*</span>
                            </label>
                            <select name="block_id" 
                                    id="block_id"
                                    class="terrasoft-form-input {{ $errors->has('block_id') ? 'terrasoft-input-error' : '' }}"
                                    disabled>
                                <option value="">Select Block</option>
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
                                   value="{{ old('olt_location') }}"
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
                                   value="{{ old('olt_location_code') }}"
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
                                   value="{{ old('lgd_code') }}"
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
                                   class="terrasoft-form-input {{ $errors->has('olt_ip') ? 'terrasoft-input-error' : '' }}"
                                   placeholder="e.g., 192.168.1.10"
                                   value="{{ old('olt_ip') }}"
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
                                   value="{{ old('no_of_gps') }}"
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
                            onclick="window.location.href='{{ route('admin.olt-locations.index') }}'">
                        <i class="ti-x"></i>
                        Cancel
                    </button>
                    <button type="submit" class="terrasoft-btn terrasoft-btn-primary" id="submitBtn">
                        <i class="ti-check"></i>
                        Create Location
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('/css/olt.css')}}">

<style>
/* Form Styles */
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
    const form = document.getElementById('oltForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="ti-loader"></i> Creating...';
        submitBtn.disabled = true;
        
        // Validate IP address
        const ipInput = document.getElementById('olt_ip');
        const ipPattern = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        
        if (!ipPattern.test(ipInput.value)) {
            alert('Please enter a valid IP address');
            resetSubmitButton();
            return;
        }
        
        // Submit form
        setTimeout(() => {
            form.submit();
        }, 1000);
    });
    
    function resetSubmitButton() {
        submitBtn.classList.remove('loading');
        submitBtn.innerHTML = '<i class="ti-check"></i> Create Location';
        submitBtn.disabled = false;
    }
    
   
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
@endsection