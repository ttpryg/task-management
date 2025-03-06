<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Categories<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto">
    <!-- Add meta tag for CSRF token -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>" />
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
        <button onclick="openCategoryModal()" 
                class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white rounded-lg 
                       hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                       focus:ring-offset-2 transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>
            <span class="text-sm font-medium">Add New Category</span>
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <ul class="divide-y divide-gray-100" id="categoryList">
            <?php foreach ($categories as $category): ?>
                <li class="px-6 py-4 hover:bg-gray-50 transition-colors duration-150" data-category-id="<?= $category['id'] ?>" data-color-class="<?= $category['color_class'] ?>">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg category-icon">
                                <i class="fas fa-folder"></i>
                            </span>
                            <span class="text-sm font-medium text-gray-900 category-name"><?= esc($category['name']) ?></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="editCategory(<?= $category['id'] ?>, '<?= esc($category['name']) ?>')" 
                                    class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-200"
                                    title="Edit Category">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="confirmDeleteCategory(<?= $category['id'] ?>, '<?= esc($category['name']) ?>')" 
                                    class="p-2 text-gray-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors duration-200"
                                    title="Delete Category">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50">
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-white rounded-xl shadow-2xl transform transition-all">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="text-2xl font-semibold text-gray-900" id="modalTitle">Add New Category</h3>
                <button type="button" onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal body -->
            <div class="p-6">
                <form id="categoryForm" class="space-y-6" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" id="categoryId" name="id">
                    
                    <!-- Name field -->
                    <div class="form-group">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-folder text-gray-400"></i>
                            </div>
                            <input type="text" id="name" name="name" 
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm 
                                          placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                                          focus:border-transparent transition-colors text-sm"
                                   placeholder="Enter category name"
                                   autocomplete="off">
                        </div>
                        <div class="error-message mt-1 text-sm text-rose-500 hidden"></div>
                    </div>

                    <!-- Form actions -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <button type="button" onclick="closeCategoryModal()" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 
                                       rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 
                                       focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent 
                                       rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 
                                       focus:ring-indigo-500">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmationModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50">
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-white rounded-xl shadow-2xl transform transition-all">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-rose-100">
                    <i class="fas fa-exclamation-triangle text-rose-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">Delete Category</h3>
                <p class="text-sm text-gray-500 text-center mb-6">
                    Are you sure you want to delete "<span id="deleteCategoryName" class="font-medium text-gray-900"></span>"? 
                    All associated tasks will also be deleted. This action cannot be undone.
                </p>
                <div class="flex items-center justify-center space-x-3">
                    <button onclick="executeDeleteCategory()" 
                            class="px-4 py-2 text-sm font-medium text-white bg-rose-600 border border-transparent 
                                   rounded-lg hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 
                                   focus:ring-rose-500">
                        Delete Category
                    </button>
                    <button onclick="closeDeleteConfirmationModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 
                                   rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 
                                   focus:ring-indigo-500">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Keep the existing HTML structure but add a template for new category items -->
<template id="categoryItemTemplate">
    <li class="px-6 py-4 hover:bg-gray-50 transition-colors duration-150">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg category-icon">
                    <i class="fas fa-folder"></i>
                </span>
                <span class="text-sm font-medium text-gray-900 category-name"></span>
            </div>
            <div class="flex items-center space-x-2">
                <button class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-200 edit-btn"
                        title="Edit Category">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="p-2 text-gray-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors duration-200 delete-btn"
                        title="Delete Category">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </li>
</template>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let categoryToDelete = null;

// Add CSRF setup for AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Store category colors in memory
const categoryColors = new Map();

// Helper function to get category color classes
function getCategoryColorClasses(categoryId) {
    const colors = [
        { bg: 'bg-blue-100', text: 'text-blue-800' },
        { bg: 'bg-green-100', text: 'text-green-800' },
        { bg: 'bg-purple-100', text: 'text-purple-800' },
        { bg: 'bg-yellow-100', text: 'text-yellow-800' },
        { bg: 'bg-pink-100', text: 'text-pink-800' },
        { bg: 'bg-indigo-100', text: 'text-indigo-800' },
        { bg: 'bg-red-100', text: 'text-red-800' },
        { bg: 'bg-teal-100', text: 'text-teal-800' }
    ];

    // If this category already has a color, keep it
    if (categoryColors.has(categoryId)) {
        return categoryColors.get(categoryId);
    }

    // Get all used colors
    const usedColors = new Set();
    document.querySelectorAll('#categoryList > li').forEach(item => {
        const icon = item.querySelector('.category-icon');
        if (icon) {
            colors.forEach((color, index) => {
                if (icon.classList.contains(color.bg)) {
                    usedColors.add(index);
                }
            });
        }
    });

    // Find the first unused color
    let colorIndex = 0;
    for (let i = 0; i < colors.length; i++) {
        if (!usedColors.has(i)) {
            colorIndex = i;
            break;
        }
    }

    // If all colors are used, use a deterministic assignment based on ID
    if (usedColors.size === colors.length) {
        colorIndex = Math.abs(parseInt(categoryId)) % colors.length;
    }

    const selectedColor = colors[colorIndex];
    categoryColors.set(categoryId, selectedColor);
    saveColorsToLocalStorage();
    return selectedColor;
}

// Save colors to localStorage
function saveColorsToLocalStorage() {
    try {
        const colorsObject = {};
        categoryColors.forEach((value, key) => {
            colorsObject[key] = value;
        });
        localStorage.setItem('categoryColors', JSON.stringify(colorsObject));
    } catch (e) {
        console.error('Error saving colors:', e);
    }
}

// Load colors from localStorage
function loadColorsFromLocalStorage() {
    try {
        const savedColors = JSON.parse(localStorage.getItem('categoryColors') || '{}');
        Object.entries(savedColors).forEach(([id, color]) => {
            categoryColors.set(parseInt(id), color);
        });
    } catch (e) {
        console.error('Error loading saved colors:', e);
    }
}

// Initialize color assignments for existing categories
function initializeCategoryColors() {
    loadColorsFromLocalStorage();
    
    const existingCategories = document.querySelectorAll('#categoryList > li');
    existingCategories.forEach(category => {
        const categoryId = category.getAttribute('data-category-id');
        if (categoryId) {
            const colorClasses = getCategoryColorClasses(parseInt(categoryId));
            applyColorToCategory(category, colorClasses);
        }
    });
}

// Apply color classes to a category element
function applyColorToCategory(categoryElement, colorClasses) {
    const iconElement = categoryElement.querySelector('.category-icon');
    if (iconElement) {
        iconElement.className = `inline-flex items-center justify-center w-8 h-8 rounded-lg category-icon ${colorClasses.bg} ${colorClasses.text}`;
    }
}

// Helper function to create/update category item in the list
function updateCategoryInList(category, action = 'create') {
    if (!category || !category.id) {
        console.error('Invalid category data:', category);
        return null;
    }

    const categoryList = document.getElementById('categoryList');
    let categoryItem;
    
    if (action === 'create') {
        categoryItem = document.createElement('li');
        categoryItem.className = 'px-6 py-4 hover:bg-gray-50 transition-colors duration-150';
        categoryList.appendChild(categoryItem);
    } else {
        categoryItem = document.querySelector(`[data-category-id="${category.id}"]`);
        if (!categoryItem) {
            console.error('Category item not found:', category.id);
            return null;
        }
    }
    
    // Set data attribute
    categoryItem.setAttribute('data-category-id', category.id);
    
    // Get color classes
    const colorClasses = getCategoryColorClasses(category.id);
    
    // Create/Update the inner HTML
    categoryItem.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg category-icon ${colorClasses.bg} ${colorClasses.text}">
                    <i class="fas fa-folder"></i>
                </span>
                <span class="text-sm font-medium text-gray-900 category-name">${category.name}</span>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="editCategory(${category.id}, '${category.name}')" 
                        class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-200"
                        title="Edit Category">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="confirmDeleteCategory(${category.id}, '${category.name}')" 
                        class="p-2 text-gray-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors duration-200"
                        title="Delete Category">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    return categoryItem;
}

// Helper function to remove category from list
function removeCategoryFromList(categoryId) {
    const categoryItem = document.querySelector(`[data-category-id="${categoryId}"]`);
    if (categoryItem) {
        categoryColors.delete(categoryId);
        saveColorsToLocalStorage();
        categoryItem.remove();
    }
}

function openCategoryModal(categoryId = null, categoryName = '') {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const modalTitle = document.getElementById('modalTitle');
    
    if (categoryId) {
        modalTitle.textContent = 'Edit Category';
        document.getElementById('categoryId').value = categoryId;
        document.getElementById('name').value = categoryName;
    } else {
        modalTitle.textContent = 'Add New Category';
        form.reset();
        document.getElementById('categoryId').value = '';
    }
    
    clearFormErrors();
    modal.classList.remove('hidden');
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

function editCategory(categoryId, categoryName) {
    openCategoryModal(categoryId, categoryName);
}

function confirmDeleteCategory(categoryId, categoryName) {
    categoryToDelete = categoryId;
    document.getElementById('deleteCategoryName').textContent = categoryName;
    document.getElementById('deleteConfirmationModal').classList.remove('hidden');
}

function closeDeleteConfirmationModal() {
    document.getElementById('deleteConfirmationModal').classList.add('hidden');
    categoryToDelete = null;
}

function executeDeleteCategory() {
    if (!categoryToDelete) return;
    
    // Create form data with CSRF token
    const formData = new FormData();
    formData.append('csrf_token', $('meta[name="csrf-token"]').attr('content'));
    
    $.ajax({
        url: `/categories/delete/${categoryToDelete}`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                removeCategoryFromList(categoryToDelete);
                closeDeleteConfirmationModal();
                showNotification('Category deleted successfully', 'success');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showNotification(response?.message || 'Failed to delete category', 'error');
            closeDeleteConfirmationModal();
        }
    });
}

// Form validation rules
const validationRules = {
    name: {
        required: true,
        minLength: 3,
        maxLength: 100,
        pattern: /^[a-zA-Z0-9\s\-_]+$/,
        messages: {
            required: 'Category name is required',
            minLength: 'Category name must be at least 3 characters long',
            maxLength: 'Category name cannot exceed 100 characters',
            pattern: 'Category name can only contain letters, numbers, spaces, hyphens and underscores'
        }
    }
};

// Validate a single field
function validateField(field, value) {
    const rules = validationRules[field];
    if (!rules) return null;

    if (rules.required && !value.trim()) {
        return rules.messages.required;
    }

    if (value.trim()) {
        if (rules.minLength && value.length < rules.minLength) {
            return rules.messages.minLength;
        }
        if (rules.maxLength && value.length > rules.maxLength) {
            return rules.messages.maxLength;
        }
        if (rules.pattern && !rules.pattern.test(value)) {
            return rules.messages.pattern;
        }
    }

    return null;
}

// Show error for a field
function showFieldError(field, error, showImmediately = false) {
    const formGroup = field.closest('.form-group');
    const errorDiv = formGroup.querySelector('.error-message');
    const icon = formGroup.querySelector('.fa-folder');
    
    if (error && showImmediately) {
        field.classList.remove('border-gray-300', 'focus:ring-indigo-500');
        field.classList.add('border-rose-500', 'focus:ring-rose-500');
        icon.classList.remove('text-gray-400', 'text-indigo-500');
        icon.classList.add('text-rose-500');
        errorDiv.textContent = error;
        errorDiv.classList.remove('hidden');
    } else {
        field.classList.remove('border-rose-500', 'focus:ring-rose-500');
        field.classList.add('border-gray-300', 'focus:ring-indigo-500');
        icon.classList.remove('text-rose-500');
        icon.classList.add('text-gray-400');
        errorDiv.textContent = '';
        errorDiv.classList.add('hidden');
    }
}

// Clear all form errors
function clearFormErrors() {
    const form = document.getElementById('categoryForm');
    form.querySelectorAll('.form-group').forEach(group => {
        const input = group.querySelector('input');
        const icon = group.querySelector('.fa-folder');
        const errorDiv = group.querySelector('.error-message');
        
        input.classList.remove('border-rose-500', 'focus:ring-rose-500');
        input.classList.add('border-gray-300', 'focus:ring-indigo-500');
        icon.classList.remove('text-rose-500');
        icon.classList.add('text-gray-400');
        errorDiv.textContent = '';
        errorDiv.classList.add('hidden');
    });
}

// Add input event listeners for real-time validation
document.getElementById('categoryForm').querySelectorAll('input').forEach(input => {
    // Clear error on input
    input.addEventListener('input', function() {
        showFieldError(this, null);
    });

    // Add focus animation
    input.addEventListener('focus', function() {
        if (!this.classList.contains('border-rose-500')) {
            this.closest('.relative').querySelector('.fa-folder')
                .classList.replace('text-gray-400', 'text-indigo-500');
        }
    });

    // Remove focus animation
    input.addEventListener('blur', function() {
        if (!this.classList.contains('border-rose-500')) {
            this.closest('.relative').querySelector('.fa-folder')
                .classList.replace('text-indigo-500', 'text-gray-400');
        }
    });
});

// Update form submission handler with validation
$('#categoryForm').on('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const categoryId = $('#categoryId').val();
    const url = categoryId ? `/categories/update/${categoryId}` : '/categories/create';
    
    // Validate all fields
    let hasErrors = false;
    form.querySelectorAll('input').forEach(input => {
        const error = validateField(input.name, input.value);
        if (error) {
            hasErrors = true;
            showFieldError(input, error, true); // Show error immediately on submit
        }
    });

    if (hasErrors) {
        return; // Just return without showing toast message
    }
    
    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    
    $.ajax({
        url: url,
        method: 'POST',
        data: $(form).serialize(),
        success: function(response) {
            if (response.success) {
                updateCategoryInList(response.category, categoryId ? 'update' : 'create');
                closeCategoryModal();
                showNotification(response.message, 'success');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response && response.errors) {
                Object.entries(response.errors).forEach(([field, message]) => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        showFieldError(input, message, true);
                    }
                });
            }
        },
        complete: function() {
            // Reset button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    });
});

// Initialize colors when the page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCategoryColors();
});
</script>
<?= $this->endSection() ?> 