<!-- Edit Transaction Form -->
<div style="margin-bottom: 20px;">
    <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 12px; font-weight: 600; color: #2d3748; margin-bottom: 6px;">
            Employee
        </label>
        <input type="text" value="{{ $transaction->employee->first_name ?? 'N/A' }} {{ $transaction->employee->last_name ?? '' }}"
               readonly
               style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 6px; background: #f7fafc; color: #718096; font-size: 13px;">
    </div>

    <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 12px; font-weight: 600; color: #2d3748; margin-bottom: 6px;">
            Material
        </label>
        <input type="text" value="{{ $transaction->material->code }} - {{ $transaction->material->name ?? 'N/A' }}"
               readonly
               style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 6px; background: #f7fafc; color: #718096; font-size: 13px;">
    </div>

    <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 12px; font-weight: 600; color: #2d3748; margin-bottom: 6px;">
            Quantity
        </label>
        <input type="number"
               name="quantity"
               value="{{ old('quantity', $transaction->quantity) }}"
               step="0.001"
               min="0.001"
               style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 13px;"
               required>
    </div>
    

  @if($transaction->serialAllocations->count())
        <div class="mb-3">
            <label>Serial-wise Quantity</label>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Issued Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->serialAllocations as $alloc)
                        <tr>
                            <td>
                                {{ $alloc->serial->serial_number }}

                                {{-- hidden serial id --}}
                                <input type="hidden"
                                       name="serials[{{ $alloc->material_serial_id }}][id]"
                                       value="{{ $alloc->material_serial_id }}">
                            </td>

                            <td>
                               
                                <input type="number"
                                        name="serials[{{ $alloc->material_serial_id }}][quantity]"
                                        value="{{ old('serials.'.$alloc->material_serial_id.'.quantity', $alloc->quantity) }}"
                                        step="0.001"
                                        min="0.001"
                                        class="form-control"
                                        required>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="mb-3">
            <span class="badge bg-secondary">Non-serial material</span>
        </div>
    @endif

    <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 12px; font-weight: 600; color: #2d3748; margin-bottom: 6px;">
            Remarks (Optional)
        </label>
        <textarea name="remarks"
                  style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 13px; resize: vertical; min-height: 80px;">{{ $transaction->remarks ?? '' }}</textarea>
    </div>

    @if($errors->any())
        <div style="padding: 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px; font-size: 12px; color: #991b1b;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
