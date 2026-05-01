@extends('admin.layout.base')

@section('title', 'Add Holiday')

@section('content')
@php
$user = Session::get('user');
 $DistId = null; 
    if ($user && isset($user->district_id)) {
        $DistId = $user->district_id;
    }
@endphp

<div class="terrasoft-main-content">
    <div class="terrasoft-page-container">
        <div class="terrasoft-page-header">
            <div class="terrasoft-header-content">
                <div class="terrasoft-header-info">
                    <div class="terrasoft-header-icon">
                        <i class="ti-plus text-green-600"></i>
                    </div>
                    <div>
                        <h1 class="terrasoft-page-title">Add Holiday</h1>
                        <p class="terrasoft-page-subtitle">Create a new holiday entry</p>
                    </div>
                </div>
                <div class="terrasoft-header-actions">
                    <a href="{{ route('admin.holidays.index') }}" class="terrasoft-btn terrasoft-btn-secondary">
                        <i class="ti-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="terrasoft-form-container">
            <form action="{{ route('admin.holidays.store') }}" method="POST" class="terrasoft-form" id="holidayForm">
                {{ csrf_field() }}

                <div class="terrasoft-form-section">
                    <div class="terrasoft-form-grid">
                        <div class="terrasoft-form-group">
                            <label for="name" class="terrasoft-form-label">
                                Holiday Name <span class="terrasoft-required">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="terrasoft-form-input {{ $errors->has('name') ? 'terrasoft-input-error' : '' }}"
                                   placeholder="e.g., Independence Day"
                                   value="{{ old('name') }}"
                                   required>
                            @if ($errors->has('name'))
                                <div class="terrasoft-error-message">
                                    <i class="fa fa-exclamation-circle"></i> {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="date" class="terrasoft-form-label">
                                Date <span class="terrasoft-required">*</span>
                            </label>
                            <input type="date"
                                   id="date"
                                   name="date"
                                   class="terrasoft-form-input {{ $errors->has('date') ? 'terrasoft-input-error' : '' }}"
                                   value="{{ old('date') }}"
                                   required>
                            @if ($errors->has('date'))
                                <div class="terrasoft-error-message">
                                    <i class="fa fa-exclamation-circle"></i> {{ $errors->first('date') }}
                                </div>
                            @endif
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="type" class="terrasoft-form-label">
                                Type <span class="terrasoft-required">*</span>
                            </label>
                            <select name="type"
                                    id="type"
                                    class="terrasoft-form-input {{ $errors->has('type') ? 'terrasoft-input-error' : '' }}"
                                    required>
                                <option value="national" {{ old('type') == 'national' ? 'selected' : '' }}>National</option>
                                <option value="regional" {{ old('type') == 'regional' ? 'selected' : '' }}>Regional</option>
                                <option value="optional" {{ old('type') == 'optional' ? 'selected' : '' }}>Optional</option>
                            </select>
                            @if ($errors->has('type'))
                                <div class="terrasoft-error-message">
                                    <i class="fa fa-exclamation-circle"></i> {{ $errors->first('type') }}
                                </div>
                            @endif
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="applies_to" class="terrasoft-form-label">
                                Applies To <span class="terrasoft-required">*</span>
                            </label>
                            <select name="applies_to"
                                    id="applies_to"
                                    class="terrasoft-form-input"
                                    required
                                    onchange="toggleLocationFields()">
                                <option value="all">All</option>
                                <option value="state">State</option>
                                <option value="district">District</option>
                                <option value="block">Block</option>
                            </select>
                        </div>
                        <div class="terrasoft-form-group" id="district_field" style="display:none;">
                            <label for="district_id" class="terrasoft-form-label">
                                District
                            </label>
                            <select name="district_id"
                                    id="district_id"
                                    class="terrasoft-form-input"
                                    onchange="loadBlocks()">
                                <option value="">Select District</option>
                                @foreach($districts as $district)
                                <option value="{{ $district->id }}"  {{ (request('district_id') == $district->id) || ($DistId && $DistId == $district->id) ? 'selected' : '' }}>{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="terrasoft-form-group" id="block_field" style="display:none;">
                            <label for="block_id" class="terrasoft-form-label">
                                Block
                            </label>
                            <select name="block_id"
                                    id="block_id"
                                    class="terrasoft-form-input">
                                <option value="">Select Block</option>
                            </select>
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="duration" class="terrasoft-form-label">
                                Duration <span class="terrasoft-required">*</span>
                            </label>
                            <select name="duration"
                                    id="duration"
                                    class="terrasoft-form-input"
                                    required>
                                <option value="full">Full Day</option>
                                <option value="half">Half Day</option>
                            </select>
                        </div>

                        <div class="terrasoft-form-group">
                            <label class="terrasoft-form-label">Is Recurring</label>
                            <div class="terrasoft-checkbox">
                                <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                                <span>Recurring every year</span>
                            </div>
                        </div>

                        <div class="terrasoft-form-group">
                            <label for="substitute_date" class="terrasoft-form-label">
                                Substitute Date
                            </label>
                            <input type="date"
                                   id="substitute_date"
                                   name="substitute_date"
                                   class="terrasoft-form-input"
                                   value="{{ old('substitute_date') }}">
                        </div>
                    </div>
                </div>

                <div class="terrasoft-form-section">
                    <div class="terrasoft-form-grid">
                        <div class="terrasoft-form-group terrasoft-form-group-full">
                            <label for="description" class="terrasoft-form-label">
                                Description
                            </label>
                            <textarea name="description"
                                      id="description"
                                      class="terrasoft-form-textarea"
                                      rows="3"
                                      placeholder="Enter holiday description...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="terrasoft-form-actions">
                    <button type="button"
                            class="terrasoft-btn terrasoft-btn-secondary"
                            onclick="window.location.href='{{ route('admin.holidays.index') }}'">
                        <i class="ti-x"></i>
                        Cancel
                    </button>
                    <button type="submit" class="terrasoft-btn terrasoft-btn-primary" id="submitBtn">
                        <i class="ti-check"></i>
                        Save Holiday
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

.terrasoft-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
}

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

function toggleLocationFields() {
    var appliesTo = document.getElementById('applies_to').value;
    document.getElementById('district_field').style.display = (appliesTo == 'district' || appliesTo == 'block') ? '' : 'none';
   
    document.getElementById('block_field').style.display = appliesTo == 'block' ? '' : 'none';

}


function loadBlocks() {
    var districtId = document.getElementById('district_id').value;
    if (!districtId) return;
    $('#block_id').html('<option value="">All Blocks</option>');

        $.get("{{ url('admin/get_blocks') }}/" + districtId, function (res) {
            let h = '<option value="">All Blocks</option>';
            res.forEach(b => {
                h += `<option value="${b.id}">${b.name}</option>`;
            });
            $('#block_id').html(h);
        });
  
}
</script>

@endsection
