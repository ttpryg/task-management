<!-- Task Modal -->
<div id="taskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative min-h-screen md:min-h-0 md:top-20 mx-auto p-4 border w-full max-w-xl shadow-lg rounded-xl bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 id="modalTitle" class="text-xl font-semibold text-gray-900">Add New Task</h3>
            <button onclick="closeTaskModal()" 
                    class="text-gray-400 hover:text-gray-600 focus:outline-none p-2 rounded-lg hover:bg-gray-100">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Task Form -->
        <form id="taskForm" class="space-y-4">
            <input type="hidden" id="taskId" name="id" value="">
            <input type="hidden" name="csrf_token" value="<?= csrf_hash() ?>">

            <!-- Title Field -->
            <div class="relative form-field">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Title <span class="text-rose-500 ml-0.5" aria-hidden="true">*</span>
                </label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg 
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                              transition duration-150 ease-in-out text-base"
                       required
                       aria-required="true">
                <div class="error-message text-red-500 text-sm mt-1"></div>
            </div>

            <!-- Description Field -->
            <div class="relative form-field">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <textarea id="description" 
                         name="description" 
                         rows="3" 
                         class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg 
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                transition duration-150 ease-in-out text-base"></textarea>
                <div class="error-message text-red-500 text-sm mt-1"></div>
            </div>

            <!-- Form Grid for Category and Deadline -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Category Field -->
                <div class="relative form-field">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Category <span class="text-rose-500 ml-0.5" aria-hidden="true">*</span>
                    </label>
                    <div class="relative">
                        <select id="category_id" 
                                name="category_id" 
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg 
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                       transition duration-150 ease-in-out appearance-none cursor-pointer text-base"
                                required
                                aria-required="true">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    <div class="error-message text-red-500 text-sm mt-1"></div>
                </div>

                <!-- Deadline Field -->
                <div class="relative form-field">
                    <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">
                        Deadline <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <div class="relative flatpickr">
                        <input type="text" 
                               id="deadline" 
                               name="deadline" 
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg 
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                      transition duration-150 ease-in-out cursor-pointer text-base"
                               data-input
                               placeholder="Select deadline">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-8">
                            <button type="button" 
                                    class="text-gray-400 hover:text-gray-600 focus:outline-none" 
                                    data-clear
                                    title="Clear deadline">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                    </div>
                    <div class="error-message text-red-500 text-sm mt-1"></div>
                </div>
            </div>

            <!-- Status Field -->
            <div id="statusField" class="relative form-field hidden">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                    Status <span class="text-rose-500 ml-0.5" aria-hidden="true">*</span>
                </label>
                <div class="relative">
                    <select id="status" 
                            name="status" 
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg 
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                   transition duration-150 ease-in-out appearance-none cursor-pointer text-base"
                            required
                            aria-required="true">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
                <div class="error-message text-red-500 text-sm mt-1"></div>
            </div>

            <!-- Form actions -->
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-6">
                <button type="button" 
                        onclick="closeTaskModal()" 
                        class="w-full sm:w-auto px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg 
                               hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 
                               transition duration-150 ease-in-out text-base">
                    Cancel
                </button>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 text-white rounded-lg 
                               hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                               focus:ring-offset-2 transition duration-150 ease-in-out text-base">
                    <i class="fas fa-save mr-2"></i>
                    Save Task
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Mobile-specific modal styles */
    @media (max-width: 768px) {
        #taskModal .relative {
            min-height: 100vh;
            margin: 0;
            border-radius: 0;
            display: flex;
            flex-direction: column;
        }

        #taskModal form {
            flex: 1;
            overflow-y: auto;
            padding-bottom: env(safe-area-inset-bottom);
        }

        #taskForm .flex.flex-col-reverse {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 1rem 0;
            margin-top: auto;
            border-top: 1px solid #e5e7eb;
        }
    }

    /* Prevent background scroll when modal is open */
    body.modal-open {
        overflow: hidden;
        position: fixed;
        width: 100%;
    }

    /* Improved touch scrolling on iOS */
    #taskForm {
        -webkit-overflow-scrolling: touch;
    }
</style>

<script>
    // Add/remove modal-open class to body
    function toggleModalOpen(isOpen) {
        if (isOpen) {
            document.body.classList.add('modal-open');
        } else {
            document.body.classList.remove('modal-open');
        }
    }

    // Update modal position on virtual keyboard
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            if (window.innerWidth < 768) {
                setTimeout(() => {
                    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        });
    });
</script> 