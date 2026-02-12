<!-- View Transaction Details -->
<div style="margin-bottom: 20px;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <!-- Employee Info -->
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #718096; margin-bottom: 6px;">
                Employee Name
            </label>
            <p style="margin: 0; font-size: 14px; color: #1a202c; font-weight: 500;">
                {{ $transaction->employee->first_name ?? 'N/A' }} {{ $transaction->employee->last_name ?? '' }}
            </p>
        </div>

        <!-- District -->
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #718096; margin-bottom: 6px;">
                District
            </label>
            <p style="margin: 0; font-size: 14px; color: #1a202c; font-weight: 500;">
                {{ $transaction->district->name ?? 'N/A' }}
            </p>
        </div>

        <!-- Material -->
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #718096; margin-bottom: 6px;">
                Material
            </label>
            <p style="margin: 0; font-size: 14px; color: #1a202c; font-weight: 500;">
                {{ $transaction->material->code }} - {{ $transaction->material->name ?? 'N/A' }}
            </p>
        </div>

        <!-- Quantity -->
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #718096; margin-bottom: 6px;">
                Quantity Issued
            </label>
            <p style="margin: 0; font-size: 14px; color: #1a202c; font-weight: 500;">
                {{ $transaction->quantity }} {{ $transaction->material->base_unit ?? 'Pcs' }}
            </p>
        </div>

        <!-- Serial Number -->
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #718096; margin-bottom: 6px;">
                Serial Number
            </label>
            <p style="margin: 0; font-size: 14px; color: #1a202c; font-weight: 500; font-family: monospace;">
                @if($transaction->serialAllocations->count())
                    @foreach($transaction->serialAllocations as $alloc)
                        @if($alloc->serial)
                            <span style="background: #eff6ff; color: #1e40af; padding: 4px 8px; border-radius: 4px; display: inline-block;">

                                {{ $alloc->serial->serial_number }} - {{$alloc->quantity}}(Quantity)</span>
                        @endif
                    @endforeach
                @else
                    <span style="color: #718096;">Non-serial item</span>
                 @endif
               
            </p>
        </div>

      

        <!-- Date -->
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #718096; margin-bottom: 6px;">
                Date
            </label>
            <p style="margin: 0; font-size: 14px; color: #1a202c; font-weight: 500;">
                {{ $transaction->created_at->format('d M Y') }}
            </p>
        </div>

        <!-- Time -->
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #718096; margin-bottom: 6px;">
                Time
            </label>
            <p style="margin: 0; font-size: 14px; color: #1a202c; font-weight: 500;">
                {{ $transaction->created_at->format('H:i A') }}
            </p>
        </div>
    </div>

    <!-- Remarks -->
    @if($transaction->remarks)
        <div style="margin-top: 16px; padding: 12px; background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #718096; margin-bottom: 6px;">
                Remarks
            </label>
            <p style="margin: 0; font-size: 13px; color: #4a5568;">
                {{ $transaction->remarks }}
            </p>
        </div>
    @endif
</div>
