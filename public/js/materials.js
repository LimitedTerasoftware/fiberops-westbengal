// Material Management JavaScript
let currentMaterialId = null;
let selectedMaterials = [];

// Initialize material management
function initializeMaterialManagement() {
    setupEventListeners();
    setupFilters();
    setupSearch();
}

// Setup event listeners
function setupEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 500);
        });
    }

    // Sort functionality
    document.querySelectorAll('.terrasoft-th-sortable').forEach(th => {
        th.addEventListener('click', function() {
            const sortBy = this.dataset.sort;
            const currentOrder = this.classList.contains('sort-asc') ? 'desc' : 'asc';
            sortTable(sortBy, currentOrder);
        });
    });
}

// Setup filters
function setupFilters() {
    // Unit filter
    const unitFilterBtn = document.getElementById('unitFilterBtn');
    const unitFilterMenu = document.getElementById('unitFilterMenu');
    
    if (unitFilterBtn && unitFilterMenu) {
        unitFilterBtn.addEventListener('click', function() {
            unitFilterMenu.classList.toggle('show');
        });

        unitFilterMenu.querySelectorAll('.terrasoft-dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                applyFilter('unit_filter', this.dataset.filter);
                updateFilterButton(unitFilterBtn, this.textContent);
                unitFilterMenu.classList.remove('show');
            });
        });
    }

    // Serial filter
    const serialFilterBtn = document.getElementById('serialFilterBtn');
    const serialFilterMenu = document.getElementById('serialFilterMenu');
    
    if (serialFilterBtn && serialFilterMenu) {
        serialFilterBtn.addEventListener('click', function() {
            serialFilterMenu.classList.toggle('show');
        });

        serialFilterMenu.querySelectorAll('.terrasoft-dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                applyFilter('serial_filter', this.dataset.filter);
                updateFilterButton(serialFilterBtn, this.textContent);
                serialFilterMenu.classList.remove('show');
            });
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.terrasoft-filter-dropdown')) {
            document.querySelectorAll('.terrasoft-dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
}

// Setup search
function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
}

// Perform search
function performSearch() {
    const searchTerm = document.getElementById('searchInput').value;
    const url = new URL(window.location);
    
    if (searchTerm) {
        url.searchParams.set('search', searchTerm);
    } else {
        url.searchParams.delete('search');
    }
    
    loadMaterials(url.toString());
}

// Apply filter
function applyFilter(filterType, filterValue) {
    const url = new URL(window.location);
    
    if (filterValue) {
        url.searchParams.set(filterType, filterValue);
    } else {
        url.searchParams.delete(filterType);
    }
    
    loadMaterials(url.toString());
}

// Update filter button text
function updateFilterButton(button, text) {
    const span = button.querySelector('span');
    if (span) {
        span.textContent = text;
    }
}

// Sort table
function sortTable(sortBy, sortOrder) {
    const url = new URL(window.location);
    url.searchParams.set('sort_by', sortBy);
    url.searchParams.set('sort_order', sortOrder);
    
    loadMaterials(url.toString());
}

// Load materials via AJAX
function loadMaterials(url) {
    showLoading();
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('materialsTable').innerHTML = data.html;
            
            // Update pagination if provided
            if (data.pagination) {
                document.querySelector('.terrasoft-pagination').innerHTML = data.pagination;
            }
            
            // Update URL without page reload
            window.history.pushState({}, '', url);
            
            // Reset selections
            selectedMaterials = [];
            updateBulkActions();
        }
    })
    .catch(error => {
        console.error('Error loading materials:', error);
        showToast('error', 'Error loading materials');
    })
    .finally(() => {
        hideLoading();
    });
}

// Show loading state
function showLoading() {
    const table = document.getElementById('materialsTable');
    if (table) {
        table.style.opacity = '0.5';
        table.style.pointerEvents = 'none';
    }
}

// Hide loading state
function hideLoading() {
    const table = document.getElementById('materialsTable');
    if (table) {
        table.style.opacity = '1';
        table.style.pointerEvents = 'auto';
    }
}

// Open create modal
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Add New Material';
    document.getElementById('submitText').textContent = 'Create Material';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('materialId').value = '';
    document.getElementById('materialForm').reset();
    clearErrors();
    document.getElementById('materialModal').classList.add('show');
}

// Edit material
function editMaterial(id) {
    fetch(`/admin/materials/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const material = data.material;
            
            document.getElementById('modalTitle').textContent = 'Edit Material';
            document.getElementById('submitText').textContent = 'Update Material';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('materialId').value = material.id;
            
            // Fill form fields
            document.getElementById('code').value = material.code;
            document.getElementById('name').value = material.name;
            document.getElementById('purchase_unit').value = material.purchase_unit;
            document.getElementById('base_unit').value = material.base_unit;
            document.getElementById('qty_per_purchase_unit').value = material.qty_per_purchase_unit;
            document.getElementById('has_serial').checked = material.has_serial;
            document.getElementById('description').value = material.description || '';
            
            clearErrors();
            document.getElementById('materialModal').classList.add('show');
        }
    })
    .catch(error => {
        console.error('Error loading material:', error);
        showToast('error', 'Error loading material details');
    });
}

// View material
function viewMaterial(id) {
    window.location.href = `/admin/materials/${id}`;
}

// Delete material
function deleteMaterial(id) {
    currentMaterialId = id;
    document.getElementById('deleteMessage').textContent = 'This action cannot be undone.';
    document.getElementById('deleteModal').classList.add('show');
}

// Close modal
function closeModal() {
    document.getElementById('materialModal').classList.remove('show');
    document.getElementById('materialForm').reset();
    clearErrors();
}

// Close delete modal
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
    currentMaterialId = null;
}

// Submit form
function submitForm(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    if (formData.has('has_serial')) {
    const checkbox = form.querySelector('#has_serial');
    formData.set('has_serial', checkbox.checked ? '1' : '0');
    }
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="ti-loader"></i> Processing...';
    submitBtn.disabled = true;
    
    clearErrors();
    
    const materialId = document.getElementById('materialId').value;
    const method = document.getElementById('formMethod').value;
    const url = materialId ? `/admin/materials/${materialId}` : '/admin/materials';
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            closeModal();
            loadMaterials(window.location.href);
        } else {
            if (data.errors) {
                displayErrors(data.errors);
            } else {
                showToast('error', data.message || 'An error occurred');
            }
        }
    })
    .catch(error => {
        console.error('Error submitting form:', error);
        showToast('error', 'An error occurred while saving');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Confirm delete
function confirmDelete() {
    if (!currentMaterialId) return;
    
    const btn = document.getElementById('confirmDeleteBtn');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="ti-loader"></i> Deleting...';
    btn.disabled = true;
    
    fetch(`/admin/materials/${currentMaterialId}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            closeDeleteModal();
            loadMaterials(window.location.href);
        } else {
            showToast('error', data.message || 'Error deleting material');
        }
    })
    .catch(error => {
        console.error('Error deleting material:', error);
        showToast('error', 'An error occurred while deleting');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Toggle select all
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.material-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

// Update bulk actions
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.material-checkbox:checked');
    selectedMaterials = Array.from(checkboxes).map(cb => cb.value);
    
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    if (selectedMaterials.length > 0) {
        bulkActions.style.display = 'flex';
        selectedCount.textContent = selectedMaterials.length;
    } else {
        bulkActions.style.display = 'none';
    }
    
    // Update select all checkbox
    const selectAll = document.getElementById('selectAll');
    const allCheckboxes = document.querySelectorAll('.material-checkbox');
    
    if (selectedMaterials.length === 0) {
        selectAll.indeterminate = false;
        selectAll.checked = false;
    } else if (selectedMaterials.length === allCheckboxes.length) {
        selectAll.indeterminate = false;
        selectAll.checked = true;
    } else {
        selectAll.indeterminate = true;
    }
}

// Bulk delete
function bulkDelete() {
    if (selectedMaterials.length === 0) return;
    
    document.getElementById('bulkDeleteCount').textContent = selectedMaterials.length;
    document.getElementById('bulkDeleteModal').classList.add('show');
}

// Close bulk delete modal
function closeBulkDeleteModal() {
    document.getElementById('bulkDeleteModal').classList.remove('show');
}

// Confirm bulk delete
function confirmBulkDelete() {
    const btn = document.getElementById('confirmBulkDeleteBtn');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="ti-loader"></i> Deleting...';
    btn.disabled = true;
    
    fetch('/admin/materials/bulk-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            ids: selectedMaterials
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            closeBulkDeleteModal();
            loadMaterials(window.location.href);
        } else {
            showToast('error', data.message || 'Error deleting materials');
        }
    })
    .catch(error => {
        console.error('Error deleting materials:', error);
        showToast('error', 'An error occurred while deleting');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Display form errors
function displayErrors(errors) {
    Object.keys(errors).forEach(field => {
        const errorElement = document.getElementById(field + 'Error');
        const inputElement = document.getElementById(field);
        
        if (errorElement) {
            errorElement.textContent = errors[field][0];
            errorElement.style.display = 'block';
        }
        
        if (inputElement) {
            inputElement.classList.add('terrasoft-input-error');
        }
    });
}

// Clear form errors
function clearErrors() {
    document.querySelectorAll('.terrasoft-error-message').forEach(error => {
        error.textContent = '';
        error.style.display = 'none';
    });
    
    document.querySelectorAll('.terrasoft-input-error').forEach(input => {
        input.classList.remove('terrasoft-input-error');
    });
}

// Show toast notification
function showToast(type, message) {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    const messageEl = document.getElementById('toastMessage');
    
    messageEl.textContent = message;
    toast.className = `terrasoft-toast ${type}`;
    
    if (type === 'success') {
        icon.innerHTML = '<i class="ti-check"></i>';
    } else {
        icon.innerHTML = '<i class="ti-alert-circle"></i>';
    }
    
    toast.classList.add('show');
    
    setTimeout(() => {
        closeToast();
    }, 5000);
}

// Close toast
function closeToast() {
    document.getElementById('toast').classList.remove('show');
}