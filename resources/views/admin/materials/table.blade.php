<table class="terrasoft-table">
    <thead>
        <tr>
            <th class="terrasoft-th-checkbox">
                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
            </th>
            <th class="terrasoft-th-sortable" data-sort="code">
                <span>Code</span>
                <i class="ti-chevron-up"></i>
            </th>
            <th class="terrasoft-th-sortable" data-sort="name">
                <span>Material Name</span>
                <i class="ti-chevron-up"></i>
            </th>
            <th class="terrasoft-th-sortable" data-sort="purchase_unit">
                <span>Units</span>
                <i class="ti-chevron-up"></i>
            </th>
            <th class="terrasoft-th-sortable" data-sort="qty_per_purchase_unit">
                <span>Conversion</span>
                <i class="ti-chevron-up"></i>
            </th>
            <th class="terrasoft-th-sortable" data-sort="has_serial">
                <span>Serial Required</span>
                <i class="ti-chevron-up"></i>
            </th>
            <th class="terrasoft-th-sortable" data-sort="created_at">
                <span>Created</span>
                <i class="ti-chevron-up"></i>
            </th>
            <th class="terrasoft-th-actions">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($materials as $material)
            <tr class="terrasoft-table-row">
                <td class="terrasoft-td-checkbox">
                    <input type="checkbox" class="material-checkbox" value="{{ $material->id }}" onchange="updateBulkActions()">
                </td>
                <td class="terrasoft-td-code">
                    <div class="terrasoft-material-code">{{ $material->code }}</div>
                </td>
                <td class="terrasoft-td-primary">
                    <div class="terrasoft-material-info">
                        <div class="terrasoft-material-name">{{ $material->name }}</div>
                        @if($material->description)
                            <div class="terrasoft-material-description">{{ Str::limit($material->description, 50) }}</div>
                        @endif
                    </div>
                </td>
                <td class="terrasoft-td-units">
                    <div class="terrasoft-unit-info">
                        <div class="terrasoft-purchase-unit">{{ $material->purchase_unit }}</div>
                        <div class="terrasoft-base-unit">Base: {{ $material->base_unit }}</div>
                    </div>
                </td>
                <td class="terrasoft-td-conversion">
                    <div class="terrasoft-conversion">
                        1 {{ $material->purchase_unit }} = {{ number_format($material->qty_per_purchase_unit, 3) }} {{ $material->base_unit }}
                    </div>
                </td>
                <td class="terrasoft-td-serial">
                    @if($material->has_serial)
                        <span class="terrasoft-badge terrasoft-badge-success">
                            <i class="ti-check"></i>
                            Required
                        </span>
                    @else
                        <span class="terrasoft-badge terrasoft-badge-secondary">
                            <i class="ti-x"></i>
                            Not Required
                        </span>
                    @endif
                </td>
                <td class="terrasoft-td-date">
                    <div class="terrasoft-date">{{ $material->created_at->format('d M Y') }}</div>
                    <div class="terrasoft-time">{{ $material->created_at->format('H:i') }}</div>
                </td>
                <td class="terrasoft-td-actions">
                    <div class="terrasoft-action-buttons">
                        <button class="terrasoft-action-btn terrasoft-btn-view" onclick="viewMaterial({{ $material->id }})" title="View Details">
                            <i class="ti-eye"></i>
                        </button>
                        <button class="terrasoft-action-btn terrasoft-btn-edit" onclick="editMaterial({{ $material->id }})" title="Edit">
                            <i class="ti-marker-alt"></i>
                        </button>
                        <button class="terrasoft-action-btn terrasoft-btn-delete" onclick="deleteMaterial({{ $material->id }})" title="Delete">
                            <i class="ti-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="terrasoft-empty-state">
                    <div class="terrasoft-empty-content">
                        <i class="ti-package"></i>
                        <h3>No Materials Found</h3>
                        <p>No materials match your current search criteria.</p>
                        <button class="terrasoft-btn terrasoft-btn-primary" onclick="openCreateModal()">
                            <i class="ti-plus"></i>
                            Add First Material
                        </button>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>