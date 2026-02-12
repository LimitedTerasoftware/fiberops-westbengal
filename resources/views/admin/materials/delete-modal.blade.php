{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="terrasoft-modal">
    <div class="terrasoft-modal-overlay" onclick="closeDeleteModal()">
        <div class="terrasoft-modal-container terrasoft-modal-sm" onclick="event.stopPropagation()">
            <div class="terrasoft-modal-header">
                <h3 class="terrasoft-modal-title">Confirm Delete</h3>
                <button class="terrasoft-modal-close-btn" onclick="closeDeleteModal()">
                    <i class="ti-x"></i>
                </button>
            </div>
            <div class="terrasoft-modal-body">
                <div class="terrasoft-delete-warning">
                    <i class="ti-alert-triangle text-red-500"></i>
                    <div>
                        <p><strong>Are you sure you want to delete this material?</strong></p>
                        <p id="deleteMessage">This action cannot be undone.</p>
                    </div>
                </div>
            </div>
            <div class="terrasoft-modal-footer">
                <button class="terrasoft-btn terrasoft-btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button class="terrasoft-btn terrasoft-btn-danger" onclick="confirmDelete()" id="confirmDeleteBtn">
                    <i class="ti-trash"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Delete Modal --}}
<div id="bulkDeleteModal" class="terrasoft-modal">
    <div class="terrasoft-modal-overlay" onclick="closeBulkDeleteModal()">
        <div class="terrasoft-modal-container terrasoft-modal-sm" onclick="event.stopPropagation()">
            <div class="terrasoft-modal-header">
                <h3 class="terrasoft-modal-title">Confirm Bulk Delete</h3>
                <button class="terrasoft-modal-close-btn" onclick="closeBulkDeleteModal()">
                    <i class="ti-x"></i>
                </button>
            </div>
            <div class="terrasoft-modal-body">
                <div class="terrasoft-delete-warning">
                    <i class="ti-alert-triangle text-red-500"></i>
                    <div>
                        <p><strong>Are you sure you want to delete <span id="bulkDeleteCount">0</span> materials?</strong></p>
                        <p>This action cannot be undone and will permanently remove all selected materials.</p>
                    </div>
                </div>
            </div>
            <div class="terrasoft-modal-footer">
                <button class="terrasoft-btn terrasoft-btn-secondary" onclick="closeBulkDeleteModal()">Cancel</button>
                <button class="terrasoft-btn terrasoft-btn-danger" onclick="confirmBulkDelete()" id="confirmBulkDeleteBtn">
                    <i class="ti-trash"></i>
                    Delete All
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.terrasoft-modal-sm {
    max-width: 400px;
}

.terrasoft-delete-warning {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.terrasoft-delete-warning i {
    font-size: 24px;
    margin-top: 2px;
}

.terrasoft-delete-warning p {
    margin: 0 0 8px 0;
    color: #374151;
    line-height: 1.5;
}

.terrasoft-delete-warning p:last-child {
    margin-bottom: 0;
}
</style>