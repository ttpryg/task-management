// CSRF token handling
const csrf_token_name = 'csrf_token';

// Update the AJAX setup
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'X-Requested-With': 'XMLHttpRequest'
    },
    beforeSend: function(xhr, settings) {
        // Add CSRF token to all non-GET requests
        if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type)) {
            const token = $('meta[name="csrf-token"]').attr('content');
            if (settings.data instanceof FormData) {
                settings.data.append(csrf_token_name, token);
            } else {
                settings.data = settings.data || {};
                if (typeof settings.data === 'string') {
                    settings.data += '&' + csrf_token_name + '=' + token;
                } else {
                    settings.data[csrf_token_name] = token;
                }
            }
        }
    },
    complete: function(xhr) {
        // Update CSRF token from response headers
        const newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
        if (newToken) {
            refreshCsrfToken(newToken);
        }
    }
});

// Add function to refresh CSRF token
function refreshCsrfToken(newToken) {
    if (newToken) {
        $('meta[name="csrf-token"]').attr('content', newToken);
        $('input[name="csrf_token"]').val(newToken);
    }
}

// Enhanced AJAX request handler
function makeAjaxRequest(options) {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    const defaultOptions = {
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            let errorMessage = 'An error occurred. Please try again.';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            showToast(errorMessage, 'error');
        }
    };

    // Merge default options with provided options
    const mergedOptions = { ...defaultOptions, ...options };
    
    // For POST/PUT/DELETE requests, add CSRF token to form data
    if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(mergedOptions.method || mergedOptions.type || 'GET')) {
        if (mergedOptions.data instanceof FormData) {
            mergedOptions.data.append('csrf_token', csrfToken);
        } else if (typeof mergedOptions.data === 'object' && !(mergedOptions.data instanceof FormData)) {
            mergedOptions.data = {
                ...mergedOptions.data,
                csrf_token: csrfToken
            };
        }
    }

    return $.ajax(mergedOptions).always(function(response, textStatus, jqXHR) {
        // Handle both success and error cases
        let newToken;
        
        // Check if jqXHR is actually the response (happens in error case)
        if (response.hasOwnProperty('responseJSON')) {
            jqXHR = response;
            response = response.responseJSON;
        }
        
        // Try to get token from header first
        try {
            newToken = jqXHR.getResponseHeader('X-CSRF-TOKEN');
        } catch (e) {
            // If header access fails, try to get from response body
            newToken = response?.csrf_token;
        }
        
        // Update token if we got one
        if (newToken) {
            refreshCsrfToken(newToken);
        }
    });
}

// Helper function for creating toasts
function showToast(message, type = 'success') {
    Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "right",
        className: `colored-toast ${type}`,
        stopOnFocus: true,
        close: true,
        style: {
            background: "transparent"
        }
    }).showToast();
}

// Helper function to get category name from task
function getCategoryName(categoryId) {
    const $category = $(`#categoryFilter option[value="${categoryId}"]`);
    return $category.length ? $category.text() : '';
}

// Helper function to get category color
function getCategoryColorClasses(categoryId) {
    const colors = [
        {
            bg: 'bg-blue-100',
            text: 'text-blue-800',
            icon: 'text-blue-500'
        },
        {
            bg: 'bg-green-100',
            text: 'text-green-800',
            icon: 'text-green-500'
        },
        {
            bg: 'bg-purple-100',
            text: 'text-purple-800',
            icon: 'text-purple-500'
        },
        {
            bg: 'bg-yellow-100',
            text: 'text-yellow-800',
            icon: 'text-yellow-500'
        },
        {
            bg: 'bg-pink-100',
            text: 'text-pink-800',
            icon: 'text-pink-500'
        },
        {
            bg: 'bg-indigo-100',
            text: 'text-indigo-800',
            icon: 'text-indigo-500'
        },
        {
            bg: 'bg-red-100',
            text: 'text-red-800',
            icon: 'text-red-500'
        },
        {
            bg: 'bg-teal-100',
            text: 'text-teal-800',
            icon: 'text-teal-500'
        }
    ];
    
    const colorIndex = (categoryId - 1) % colors.length;
    return colors[colorIndex];
}

// Function to update task UI
function updateTaskUI(task, action = 'update') {
    // Ensure task status is properly formatted before updating UI
    const formatStatus = (status) => {
        return status.split('_')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    };

    // Create a copy of the task with formatted status for display
    const displayTask = {
        ...task,
        displayStatus: formatStatus(task.status)
    };

    const taskElement = $(`.task-item[data-id="${task.id}"]`);
    const taskHtml = createTaskElement(displayTask);

    if (action === 'create') {
        $(`.task-list[data-status="${task.status}"]`).prepend($(taskHtml).hide().fadeIn(300));
    } else if (action === 'update' || action === 'move') {
        // Check if status has changed
        const currentStatus = taskElement.data('status');
        if (currentStatus !== task.status) {
            // Status has changed, move task to new list
            const targetList = $(`.task-list[data-status="${task.status}"]`);
            taskElement.fadeOut(300, function() {
                $(this).remove();
                const newElement = $(taskHtml).hide();
                targetList.prepend(newElement);
                newElement.fadeIn(300);
                updateEmptyStates();
            });
        } else {
            // Just update the task in place
            taskElement.replaceWith($(taskHtml).hide().fadeIn(300));
        }
    } else if (action === 'delete') {
        taskElement.fadeOut(300, function() {
            $(this).remove();
            updateEmptyStates();
        });
    }

    // Update empty states
    updateEmptyStates();
}

// Helper function to get task action buttons based on status
function getTaskActionButtons(task) {
    const buttons = [];
    
    // Create a flex container for consistent spacing
    buttons.push('<div class="flex items-center gap-1">');
    
    // Status-specific movement buttons (always first)
    if (task.status === 'completed') {
        buttons.push(`
            <button onclick="moveTask(${task.id}, 'in_progress')" 
                    class="p-2 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition-colors duration-200"
                    title="Undo - Move to In Progress">
                <i class="fas fa-undo"></i>
            </button>
        `);
    } else if (task.status === 'pending') {
        buttons.push(`
            <button onclick="moveTask(${task.id}, 'in_progress')" 
                    class="p-2 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition-colors duration-200"
                    title="Move to In Progress">
                <i class="fas fa-arrow-right"></i>
            </button>
        `);
    } else if (task.status === 'in_progress') {
        buttons.push(`
            <button onclick="moveTask(${task.id}, 'pending')" 
                    class="p-2 rounded-lg text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 transition-colors duration-200"
                    title="Move back to Pending">
                <i class="fas fa-arrow-left"></i>
            </button>
            <button onclick="moveTask(${task.id}, 'completed')" 
                    class="p-2 rounded-lg text-green-600 hover:text-green-800 hover:bg-green-50 transition-colors duration-200"
                    title="Mark as Completed">
                <i class="fas fa-check"></i>
            </button>
        `);
    }

    // Edit button (always in the middle for non-completed tasks)
    if (task.status !== 'completed') {
        buttons.push(`
            <button onclick="editTask(${task.id})" 
                    class="edit-button p-2 rounded-lg text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-colors duration-200"
                    title="Edit Task">
                <i class="fas fa-edit"></i>
            </button>
        `);
    }

    // Delete button (always last)
    buttons.push(`
        <button onclick="deleteTask(${task.id})" 
                class="p-2 rounded-lg text-red-600 hover:text-red-800 hover:bg-red-50 transition-colors duration-200"
                title="Delete Task">
            <i class="fas fa-trash"></i>
        </button>
    `);

    // Close the flex container
    buttons.push('</div>');

    return buttons.join('');
}

// Initialize event handlers when document is ready
$(document).ready(function() {
    // Add CSRF token to all forms dynamically
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    $('form').each(function() {
        if (!$(this).find('input[name="csrf_token"]').length) {
            $(this).append(`<input type="hidden" name="csrf_token" value="${csrfToken}">`);
        }
    });

    // Initialize search and filter functionality
    const $searchInput = $('#taskSearch');
    const $statusFilter = $('#statusFilter');
    const $categoryFilter = $('#categoryFilter');
    const $clearSearch = $('#clearSearch');
    const $clearFilters = $('#clearFilters');

    // Debounced search function
    const debouncedSearch = debounce(() => {
        filterTasks();
    }, 300);

    // Search input handler
    $searchInput.on('input', function() {
        const hasValue = $(this).val().length > 0;
        $clearSearch.toggle(hasValue).css('opacity', hasValue ? '1' : '0');
        debouncedSearch();
    });

    // Clear search button handler
    $clearSearch.on('click', function() {
        $searchInput.val('');
        $(this).hide().css('opacity', '0');
        filterTasks();
    });

    // Status and category filter handlers
    $statusFilter.on('change', filterTasks);
    $categoryFilter.on('change', filterTasks);

    // Clear all filters button handler
    $clearFilters.on('click', function() {
        $searchInput.val('');
        $statusFilter.val('');
        $categoryFilter.val('');
        $clearSearch.hide().css('opacity', '0');
        filterTasks();
    });

    // Remove any existing submit handlers to prevent duplicates
    $('#taskForm').off('submit');
    
    // Handle form submission
    $('#taskForm').on('submit', function(e) {
        e.preventDefault();
        
        const taskId = $('#taskId').val();
        const formData = new FormData(this);
        const $submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = $submitButton.html();
        
        // Clear any existing errors
        clearFormErrors();
        
        // Show saving state
        $submitButton.html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...').prop('disabled', true);
        
        makeAjaxRequest({
            url: taskId ? `/tasks/update/${taskId}` : '/tasks/create',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    const task = response.task;
                    
                    // Format the status before updating UI
                    task.status = task.status || 'pending';
                    
                    // Update UI with formatted status
                    updateTaskUI(task, taskId ? 'update' : 'create');
                    showToast(response.message || 'Task saved successfully', 'success');
                    closeTaskModal();
                } else {
                    if (response.errors) {
                        displayFormErrors(response.errors);
                    }
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                console.error('Error saving task:', response);
                
                if (response.errors) {
                    displayFormErrors(response.errors);
                }
            },
            complete: function() {
                // Restore button state
                $submitButton.html(originalButtonText).prop('disabled', false);
            }
        });
    });

    // Initialize empty states
    updateEmptyStates();

    // Initialize date picker
    initializeDatePicker();

    // Request notification permission
    if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
        Notification.requestPermission();
    }
});

// Function to filter tasks
function filterTasks() {
    const searchQuery = $('#taskSearch').val().toLowerCase();
    const statusFilter = $('#statusFilter').val();
    const categoryFilter = $('#categoryFilter').val();

    // Show/hide tasks based on filters
    $('.task-item').each(function() {
        const $task = $(this);
        const matches = checkTaskMatches($task, searchQuery, statusFilter, categoryFilter);
        $task.toggle(matches);
    });

    // Update empty states after filtering
    updateEmptyStates();
}

// Function to check if a task matches the current filters
function checkTaskMatches($task, searchQuery, statusFilter, categoryFilter) {
    // Get task data
    const title = ($task.data('title') || '').toLowerCase();
    const description = ($task.data('description') || '').toLowerCase();
    const status = $task.data('status');
    const categoryId = $task.data('category-id')?.toString();

    // Check if task matches search query
    const matchesSearch = !searchQuery || 
        title.includes(searchQuery) || 
        description.includes(searchQuery);

    // Check if task matches status filter
    const matchesStatus = !statusFilter || status === statusFilter;

    // Check if task matches category filter
    const matchesCategory = !categoryFilter || categoryId === categoryFilter;

    // Task must match all active filters
    return matchesSearch && matchesStatus && matchesCategory;
}

// Function to update empty states
function updateEmptyStates() {
    $('.task-list').each(function() {
        const $list = $(this);
        const $visibleTasks = $list.find('.task-item:visible');
        const $emptyState = $list.find('.empty-state');

        if ($visibleTasks.length === 0) {
            if ($emptyState.length === 0) {
                $list.append(`
                    <div class="empty-state p-4 text-center text-gray-500">
                        <p>No tasks found</p>
                    </div>
                `);
            }
        } else {
            $emptyState.remove();
        }
    });
}

// Function to delete task
function deleteTask(taskId) {
    // Create and show the confirmation modal
    const confirmModal = document.createElement('div');
    confirmModal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    confirmModal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Task</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to delete this task? This action cannot be undone.
                    </p>
                </div>
                <div class="flex justify-center gap-4 mt-4">
                    <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Delete
                    </button>
                    <button id="cancelDelete" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(confirmModal);
    
    // Handle confirmation
    document.getElementById('confirmDelete').addEventListener('click', function() {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('csrf_token', $('meta[name="csrf-token"]').attr('content'));
        
        makeAjaxRequest({
            url: `/tasks/delete/${taskId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast('Task deleted successfully', 'success');
                    const $task = $(`.task-item[data-id="${taskId}"]`);
                    $task.fadeOut(300, function() {
                        $(this).remove();
                        updateEmptyStates();
                    });
                } else {
                    showToast(response.message || 'Failed to delete task', 'error');
                }
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Error deleting task', 'error');
            },
            complete: function() {
                confirmModal.remove();
            }
        });
    });
    
    // Handle cancellation
    document.getElementById('cancelDelete').addEventListener('click', function() {
        confirmModal.remove();
    });
    
    // Close on background click
    confirmModal.addEventListener('click', function(e) {
        if (e.target === confirmModal) {
            confirmModal.remove();
        }
    });
}

// Function to create task element HTML
function createTaskElement(task) {
    const statusClasses = {
        pending: 'bg-yellow-100 text-yellow-800',
        in_progress: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800'
    };

    const statusIcons = {
        pending: 'clock',
        in_progress: 'spinner fa-spin',
        completed: 'check'
    };

    const formatStatus = (status) => {
        return status.split('_')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    };

    const categoryName = getCategoryName(task.category_id);
    const categoryColor = getCategoryColorClasses(task.category_id);
    const deadlineFormatted = task.deadline ? new Date(task.deadline).toLocaleString() : '';
    const formattedStatus = formatStatus(task.status);

    return `
        <div class="task-item bg-white shadow-sm rounded-lg p-4 mb-4" 
             data-id="${task.id}"
             data-status="${task.status}"
             data-category-id="${task.category_id}"
             data-title="${task.title}"
             data-description="${task.description || ''}"
             draggable="true"
             ondragstart="drag(event)">
            <div class="flex justify-between items-start gap-4">
                <div class="flex-1">
                    <h3 class="font-medium text-gray-900 ${task.status === 'completed' ? 'line-through' : ''}">${task.title}</h3>
                    <p class="text-sm text-gray-600">${task.description || ''}</p>
                    
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClasses[task.status]}">
                            <i class="fas fa-${statusIcons[task.status]}"></i>
                            ${formattedStatus}
                        </span>
                        
                        ${categoryName ? `
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${categoryColor.bg} ${categoryColor.text}">
                                <i class="fas fa-folder mr-1 ${categoryColor.icon}"></i>
                                ${categoryName}
                            </span>
                        ` : ''}
                        
                        ${deadlineFormatted ? `
                            <span class="inline-flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar-alt mr-1.5"></i>
                                ${deadlineFormatted}
                            </span>
                        ` : ''}
                    </div>
                </div>
                
                <div class="flex items-center space-x-1">
                    ${getTaskActionButtons(task)}
                </div>
            </div>
        </div>
    `;
}

// Enhanced move task function
function moveTask(taskId, newStatus) {
    const formData = new FormData();
    formData.append('status', newStatus);
    formData.append('csrf_token', $('meta[name="csrf-token"]').attr('content'));

    makeAjaxRequest({
        url: `/tasks/updateStatus/${taskId}`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                // Update task UI first
                updateTaskUI(response.task, 'move');
                
                // Then show success message
                showToast('Task status updated successfully', 'success');
                
                // Update CSRF token if provided
                if (response.csrf_token) {
                    refreshCsrfToken(response.csrf_token);
                }
            } else {
                showToast(response.message || 'Failed to update task status', 'error');
            }
        },
        error: function(xhr) {
            let message = 'Failed to update task status';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast(message, 'error');
        }
    });
}

// Function to open task modal
function openTaskModal(taskId = null) {
    const modal = document.getElementById('taskModal');
    const form = document.getElementById('taskForm');
    const modalTitle = document.getElementById('modalTitle');
    const statusField = document.getElementById('statusField');
    
    if (taskId) {
        modalTitle.textContent = 'Edit Task';
        statusField.classList.remove('hidden');
        
        // Show loading state
        form.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
        
        // Fetch task data and populate form
        makeAjaxRequest({
            url: `/tasks/${taskId}`,  // Updated endpoint
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const task = response.task;
                    document.getElementById('taskId').value = task.id;
                    document.getElementById('title').value = task.title;
                    document.getElementById('description').value = task.description || '';
                    document.getElementById('category_id').value = task.category_id;
                    
                    // Format the deadline if it exists
                    if (task.deadline) {
                        const deadlineDate = new Date(task.deadline);
                        const formattedDeadline = deadlineDate.toISOString().slice(0, 16);
                        $('#deadline').val(formattedDeadline);
                    } else {
                        $('#deadline').val('');
                    }
                    
                    document.getElementById('status').value = task.status;
                    
                    // Enable form fields after loading
                    form.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
                    
                    // Initialize date picker after populating the field
                    initializeDatePicker();
                }
            },
            error: function(xhr) {
                showToast('Failed to load task details', 'error');
                closeTaskModal();
            }
        });
    } else {
        modalTitle.textContent = 'Add New Task';
        statusField.classList.add('hidden');
        form.reset();
        document.getElementById('taskId').value = '';
    }
    
    modal.classList.remove('hidden');
    // Focus on title field after modal opens
    setTimeout(() => document.getElementById('title').focus(), 100);
}

// Function to close task modal
function closeTaskModal() {
    const modal = document.getElementById('taskModal');
    modal.classList.add('hidden');
    // Reset form
    document.getElementById('taskForm').reset();
    // Clear any error states
    $('.error-message').text('');
    $('input, select, textarea').removeClass('border-red-500');
}

// Function to edit task
function editTask(taskId) {
    const modal = document.getElementById('taskModal');
    const form = document.getElementById('taskForm');
    const modalTitle = document.getElementById('modalTitle');
    const statusField = document.getElementById('statusField');
    
    modalTitle.textContent = 'Edit Task';
    statusField.classList.remove('hidden');
    
    // Show loading state
    form.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
    
    makeAjaxRequest({
        url: `/tasks/${taskId}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const task = response.task;
                $('#taskId').val(task.id);
                $('#title').val(task.title);
                $('#description').val(task.description || '');
                $('#category_id').val(task.category_id);
                
                // Format the deadline if it exists
                if (task.deadline) {
                    const deadlineDate = new Date(task.deadline);
                    const formattedDeadline = deadlineDate.toISOString().slice(0, 16);
                    $('#deadline').val(formattedDeadline);
                } else {
                    $('#deadline').val('');
                }
                
                $('#status').val(task.status);
                
                // Store original category ID for comparison
                form.dataset.originalCategoryId = task.category_id;
                
                // Enable form fields after loading
                form.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
                
                // Show modal
                modal.classList.remove('hidden');
                
                // Initialize date picker after populating the field
                setTimeout(() => {
                    initializeDatePicker();
                    document.getElementById('title').focus();
                }, 100);
            } else {
                showToast(response.message || 'Failed to load task', 'error');
                closeTaskModal();
            }
        },
        error: function(xhr) {
            showToast('Failed to load task details', 'error');
            closeTaskModal();
        }
    });
}

// Function to clear form errors
function clearFormErrors() {
    $('.error-message').text('');
    $('.is-invalid').removeClass('is-invalid');
    $('input, select, textarea').removeClass('border-red-500').addClass('border-gray-300');
}

function displayFormErrors(errors) {
    clearFormErrors();
    
    // Handle both object and array error formats
    const errorMessages = Array.isArray(errors) ? errors : Object.entries(errors);
    
    errorMessages.forEach(error => {
        let field, message;
        
        if (Array.isArray(error)) {
            [field, message] = error;
        } else {
            message = error;
        }
        
        if (field) {
            const input = $(`#${field}`);
            if (input.length) {
                input.removeClass('border-gray-300')
                     .addClass('border-red-500 is-invalid');
                     
                const errorDiv = input.closest('.relative').find('.error-message');
                if (errorDiv.length) {
                    errorDiv.text(message);
                } else {
                    input.closest('.relative').append(`<div class="error-message text-red-500 text-sm mt-1">${message}</div>`);
                }
            }
        }
    });
}

function validateForm(form, fields) {
    const errors = {};
    const formData = new FormData(form);

    for (const [fieldName, rules] of Object.entries(fields)) {
        const value = formData.get(fieldName)?.trim() || '';

        if (rules.required && !value) {
            errors[fieldName] = rules.message.required;
        } else if (value) {
            if (rules.minLength && value.length < rules.minLength) {
                errors[fieldName] = rules.message.minLength;
            }
            if (rules.maxLength && value.length > rules.maxLength) {
                errors[fieldName] = rules.message.maxLength;
            }
            if (rules.pattern && !rules.pattern.test(value)) {
                errors[fieldName] = rules.message.pattern;
            }
        }
    }

    return errors;
}

// Debounce function to limit how often a function is called
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Function to handle deadline notifications
function scheduleDeadlineNotification(deadline) {
    // Request notification permission if not granted
    if (Notification.permission !== 'granted') {
        Notification.requestPermission();
    }

    const now = new Date();
    const deadlineDate = new Date(deadline);
    const timeUntilDeadline = deadlineDate.getTime() - now.getTime();
    
    // Schedule notifications at different intervals
    const notifications = [
        { time: 60 * 60 * 1000, message: '1 hour' },      // 1 hour before
        { time: 24 * 60 * 60 * 1000, message: '1 day' },  // 1 day before
    ];

    notifications.forEach(({ time, message }) => {
        const notificationTime = deadlineDate.getTime() - time;
        if (notificationTime > now.getTime()) {
            setTimeout(() => {
                showDeadlineNotification(deadlineDate, message);
            }, notificationTime - now.getTime());
        }
    });
}

// Function to show deadline notification
function showDeadlineNotification(deadline, timeRemaining) {
    // Browser notification
    if (Notification.permission === 'granted') {
        new Notification('Task Deadline Reminder', {
            body: `Task deadline approaching in ${timeRemaining}`,
            icon: '/favicon.ico'
        });
    }

    // Toast notification
    showToast(`Task deadline approaching in ${timeRemaining}`, 'warning');
}

// Initialize Flatpickr with consistent styling
function initializeDatePicker() {
    try {
        // Destroy any existing instance first
        const existingPicker = document.querySelector("#deadline")?._flatpickr;
        if (existingPicker) {
            existingPicker.destroy();
        }

        const picker = flatpickr("#deadline", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: false,
            minuteIncrement: 15,
            defaultHour: 9,
            position: "auto",
            allowInput: true,
            clickOpens: true,
            wrap: false,
            enableSeconds: false,
            disableMobile: false,
            locale: {
                firstDayOfWeek: 0,
                weekdays: {
                    shorthand: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    longhand: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
                }
            },
            theme: "light",
            placeholder: "Select deadline (optional)",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates[0]) {
                    scheduleDeadlineNotification(selectedDates[0]);
                }
            }
        });

        return function cleanup() {
            if (picker) {
                picker.destroy();
            }
        };
    } catch (error) {
        console.error('Error initializing date picker:', error);
    }
}
