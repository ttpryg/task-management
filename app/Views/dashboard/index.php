<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">My Tasks</h1>
        <a href="/categories" 
           class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white 
                  rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 
                  focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" />
            </svg>
            <span class="font-medium">Manage Categories</span>
        </a>
    </div>

    <!-- Update the filters section -->
    <div class="mb-6 bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex flex-col gap-4 sm:flex-row sm:gap-6">
            <!-- Search input with icon and clear button -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" 
                       id="taskSearch" 
                       placeholder="Search tasks..." 
                       class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-lg 
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                              transition duration-150 ease-in-out text-gray-900 placeholder-gray-400">
                <button type="button" 
                        id="clearSearch" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer opacity-0 transition-opacity duration-150 ease-in-out"
                        style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Filter controls wrapper -->
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                <!-- Filter by status with icon -->
                <div class="flex-1 sm:flex-initial relative min-w-[200px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-filter text-gray-400"></i>
                    </div>
                    <select id="statusFilter" 
                            class="w-full pl-10 pr-12 py-2.5 bg-gray-50 border border-gray-200 rounded-lg 
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                   transition duration-150 ease-in-out text-gray-900 appearance-none cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Filter by category with icon -->
                <div class="flex-1 sm:flex-initial relative min-w-[200px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-folder text-gray-400"></i>
                    </div>
                    <select id="categoryFilter" 
                            class="w-full pl-10 pr-12 py-2.5 bg-gray-50 border border-gray-200 rounded-lg 
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                   transition duration-150 ease-in-out text-gray-900 appearance-none cursor-pointer">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
            </div>
            
            <!-- Clear filters button -->
            <button id="clearFilters" 
                    class="flex-none inline-flex items-center justify-center px-4 py-2.5 bg-white text-gray-600 
                           border border-gray-200 rounded-lg group hover:text-rose-500 hover:bg-rose-50 
                           hover:border-rose-200 focus:outline-none active:bg-rose-100 
                           shadow-sm hover:shadow-md transform transition-all duration-200 ease-in-out 
                           hover:-translate-y-0.5 active:translate-y-0 w-full sm:w-auto">
                <i class="fas fa-times mr-2 group-hover:text-rose-500 transition-colors"></i>
                <span class="group-hover:translate-x-0.5 transition-transform duration-150">
                    Clear Filters
                </span>
            </button>
        </div>
    </div>

    <!-- Add New Task FAB -->
    <button onclick="openTaskModal()" 
            class="fixed right-6 bottom-6 w-14 h-14 bg-indigo-600 text-white rounded-full shadow-lg hover:bg-indigo-700 
                   flex items-center justify-center transform hover:scale-110 active:scale-95 transition-all duration-200
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        <span class="sr-only">Add New Task</span>
    </button>

    <!-- Task columns grid container -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
        <!-- Pending Tasks -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6 sm:col-span-1">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Pending</h2>
            <div class="space-y-3 sm:space-y-4 task-list" data-status="pending" ondragover="allowDrop(event)" ondrop="drop(event)">
                <?php foreach ($tasks as $task): ?>
                    <?php if ($task['status'] === 'pending'): ?>
                        <div class="task-item bg-white shadow-sm rounded-lg p-4 mb-4" 
                             data-id="<?= $task['id'] ?>"
                             data-status="<?= $task['status'] ?>"
                             data-category-id="<?= $task['category_id'] ?>"
                             data-title="<?= esc($task['title']) ?>"
                             data-description="<?= esc($task['description']) ?>"
                             draggable="true"
                             ondragstart="drag(event)">
                            <div class="flex justify-between items-start gap-4">
                                <!-- Task content on the left -->
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900 <?= $task['status'] === 'completed' ? 'line-through' : '' ?>">
                                        <?= esc($task['title']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-600"><?= esc($task['description']) ?></p>
                                    
                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <!-- Status badge -->
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php
                                            switch ($task['status']) {
                                                case 'pending':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'in_progress':
                                                    echo 'bg-blue-100 text-blue-800';
                                                    break;
                                                case 'completed':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                            }
                                            ?>">
                                            <i class="fas fa-<?php
                                                switch ($task['status']) {
                                                    case 'pending':
                                                        echo 'clock';
                                                        break;
                                                    case 'in_progress':
                                                        echo 'spinner fa-spin';
                                                        break;
                                                    case 'completed':
                                                        echo 'check';
                                                        break;
                                                }
                                            ?>"></i>
                                            <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                        </span>

                                        <!-- Category badge -->
                                        <?php foreach ($categories as $category): ?>
                                            <?php if ($category['id'] === $task['category_id']): ?>
                                                <?php $color = getCategoryColor($category['id']); ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $color['bg'] ?> <?= $color['text'] ?>">
                                                    <i class="fas fa-folder mr-1 <?= $color['icon'] ?>"></i>
                                                    <?= esc($category['name']) ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <!-- Deadline -->
                                        <?php if ($task['deadline']): ?>
                                            <span class="inline-flex items-center text-sm text-gray-500">
                                                <i class="fas fa-calendar-alt mr-1.5"></i>
                                                <?= date('M d, Y h:i A', strtotime($task['deadline'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Action buttons on the right -->
                                <div class="flex items-center space-x-1">
                                    <?php if ($task['status'] === 'pending'): ?>
                                        <button onclick="moveTask(<?= $task['id'] ?>, 'in_progress')" 
                                                class="p-2 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition-colors duration-200"
                                                title="Move to In Progress">
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($task['status'] !== 'completed'): ?>
                                        <button onclick="editTask(<?= $task['id'] ?>)" 
                                                class="p-2 rounded-lg text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-colors duration-200"
                                                title="Edit Task">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button onclick="deleteTask(<?= $task['id'] ?>)" 
                                            class="p-2 rounded-lg text-red-600 hover:text-red-800 hover:bg-red-50 transition-colors duration-200"
                                            title="Delete Task">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- In Progress Tasks -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6 sm:col-span-1">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">In Progress</h2>
            <div class="space-y-3 sm:space-y-4 task-list" data-status="in_progress" ondragover="allowDrop(event)" ondrop="drop(event)">
                <?php foreach ($tasks as $task): ?>
                    <?php if ($task['status'] === 'in_progress'): ?>
                        <div class="task-item bg-white shadow-sm rounded-lg p-4 mb-4" 
                             data-id="<?= $task['id'] ?>"
                             data-status="<?= $task['status'] ?>"
                             data-category-id="<?= $task['category_id'] ?>"
                             data-title="<?= esc($task['title']) ?>"
                             data-description="<?= esc($task['description']) ?>"
                             draggable="true"
                             ondragstart="drag(event)">
                            <div class="flex justify-between items-start gap-4">
                                <!-- Task content on the left -->
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900 <?= $task['status'] === 'completed' ? 'line-through' : '' ?>">
                                        <?= esc($task['title']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-600"><?= esc($task['description']) ?></p>
                                    
                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <!-- Status badge -->
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php
                                            switch ($task['status']) {
                                                case 'pending':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'in_progress':
                                                    echo 'bg-blue-100 text-blue-800';
                                                    break;
                                                case 'completed':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                            }
                                            ?>">
                                            <i class="fas fa-<?php
                                                switch ($task['status']) {
                                                    case 'pending':
                                                        echo 'clock';
                                                        break;
                                                    case 'in_progress':
                                                        echo 'spinner fa-spin';
                                                        break;
                                                    case 'completed':
                                                        echo 'check';
                                                        break;
                                                }
                                            ?>"></i>
                                            <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                        </span>

                                        <!-- Category badge -->
                                        <?php foreach ($categories as $category): ?>
                                            <?php if ($category['id'] === $task['category_id']): ?>
                                                <?php $color = getCategoryColor($category['id']); ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $color['bg'] ?> <?= $color['text'] ?>">
                                                    <i class="fas fa-folder mr-1 <?= $color['icon'] ?>"></i>
                                                    <?= esc($category['name']) ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <!-- Deadline -->
                                        <?php if ($task['deadline']): ?>
                                            <span class="inline-flex items-center text-sm text-gray-500">
                                                <i class="fas fa-calendar-alt mr-1.5"></i>
                                                <?= date('M d, Y h:i A', strtotime($task['deadline'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Action buttons on the right -->
                                <div class="flex items-center space-x-1">
                                    <?php if ($task['status'] === 'in_progress'): ?>
                                        <button onclick="moveTask(<?= $task['id'] ?>, 'pending')" 
                                                class="p-2 rounded-lg text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 transition-colors duration-200"
                                                title="Move back to Pending">
                                            <i class="fas fa-arrow-left"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($task['status'] !== 'completed'): ?>
                                        <button onclick="moveTask(<?= $task['id'] ?>, 'completed')" 
                                                class="p-2 rounded-lg text-green-600 hover:text-green-800 hover:bg-green-50 transition-colors duration-200"
                                                title="Mark as Completed">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($task['status'] !== 'completed'): ?>
                                        <button onclick="editTask(<?= $task['id'] ?>)" 
                                                class="p-2 rounded-lg text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-colors duration-200"
                                                title="Edit Task">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button onclick="deleteTask(<?= $task['id'] ?>)" 
                                            class="p-2 rounded-lg text-red-600 hover:text-red-800 hover:bg-red-50 transition-colors duration-200"
                                            title="Delete Task">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Completed Tasks -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6 sm:col-span-2 xl:col-span-1">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Completed</h2>
            <div class="space-y-3 sm:space-y-4 task-list" data-status="completed" ondragover="allowDrop(event)" ondrop="drop(event)">
                <?php foreach ($tasks as $task): ?>
                    <?php if ($task['status'] === 'completed'): ?>
                        <div class="task-item bg-white shadow-sm rounded-lg p-4 mb-4" 
                             data-id="<?= $task['id'] ?>"
                             data-status="<?= $task['status'] ?>"
                             data-category-id="<?= $task['category_id'] ?>"
                             data-title="<?= esc($task['title']) ?>"
                             data-description="<?= esc($task['description']) ?>"
                             draggable="true"
                             ondragstart="drag(event)">
                            <div class="flex justify-between items-start gap-4">
                                <!-- Task content on the left -->
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900 line-through"><?= esc($task['title']) ?></h3>
                                    <p class="text-sm text-gray-600"><?= esc($task['description']) ?></p>
                                    
                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <!-- Status badge -->
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php
                                            switch ($task['status']) {
                                                case 'pending':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'in_progress':
                                                    echo 'bg-blue-100 text-blue-800';
                                                    break;
                                                case 'completed':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                            }
                                            ?>">
                                            <i class="fas fa-<?php
                                                switch ($task['status']) {
                                                    case 'pending':
                                                        echo 'clock';
                                                        break;
                                                    case 'in_progress':
                                                        echo 'spinner fa-spin';
                                                        break;
                                                    case 'completed':
                                                        echo 'check';
                                                        break;
                                                }
                                            ?>"></i>
                                            <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                        </span>

                                        <!-- Category badge -->
                                        <?php foreach ($categories as $category): ?>
                                            <?php if ($category['id'] === $task['category_id']): ?>
                                                <?php $color = getCategoryColor($category['id']); ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $color['bg'] ?> <?= $color['text'] ?>">
                                                    <i class="fas fa-folder mr-1 <?= $color['icon'] ?>"></i>
                                                    <?= esc($category['name']) ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <!-- Deadline -->
                                        <?php if ($task['deadline']): ?>
                                            <span class="inline-flex items-center text-sm text-gray-500">
                                                <i class="fas fa-calendar-alt mr-1.5"></i>
                                                <?= date('M d, Y h:i A', strtotime($task['deadline'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Action buttons on the right -->
                                <div class="flex items-center space-x-1">
                                    <?php if ($task['status'] === 'completed'): ?>
                                        <button onclick="moveTask(<?= $task['id'] ?>, 'in_progress')" 
                                                class="p-2 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition-colors duration-200"
                                                title="Undo - Move to In Progress">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($task['status'] !== 'completed'): ?>
                                        <button onclick="editTask(<?= $task['id'] ?>)" 
                                                class="p-2 rounded-lg text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-colors duration-200"
                                                title="Edit Task">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button onclick="deleteTask(<?= $task['id'] ?>)" 
                                            class="p-2 rounded-lg text-red-600 hover:text-red-800 hover:bg-red-50 transition-colors duration-200"
                                            title="Delete Task">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Task Modal -->
<div id="taskModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50">
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-100">
                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900" id="modalTitle">Add New Task</h3>
                <button type="button" onclick="closeTaskModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal body -->
            <div class="p-4 sm:p-6">
                <form id="taskForm" class="space-y-4 sm:space-y-6" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" id="taskId" name="id">
                    
                    <!-- Title field -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-tasks text-gray-400"></i>
                            </div>
                            <input type="text" id="title" name="title" 
                                class="block w-full pl-10 pr-4 py-2.5 sm:py-3 text-base rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" 
                                placeholder="Enter task title" required>
                            <div class="error-message text-red-500 text-sm mt-1"></div>
                        </div>
                    </div>

                    <!-- Description field -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute left-0 top-3 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-align-left text-gray-400"></i>
                            </div>
                            <textarea id="description" name="description" rows="4" 
                                class="block w-full pl-10 pr-4 py-2.5 sm:py-3 text-base rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" 
                                placeholder="Enter task description"
                                aria-label="Task description"></textarea>
                            <div class="error-message text-red-500 text-sm mt-1"></div>
                        </div>
                    </div>

                    <!-- Status field (hidden by default, shown when editing) -->
                    <div id="statusField" class="hidden">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-flag text-gray-400"></i>
                            </div>
                            <select id="status" name="status" 
                                class="block w-full pl-10 pr-10 py-2.5 sm:py-3 text-base rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 appearance-none cursor-pointer">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                            <div class="error-message text-red-500 text-sm mt-1"></div>
                        </div>
                    </div>

                    <!-- Category and Deadline fields in a grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Category field -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-folder text-gray-400"></i>
                                </div>
                                <select id="category_id" name="category_id" 
                                    class="block w-full pl-10 pr-10 py-2.5 sm:py-3 text-base rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" 
                                    required>
                                    <option value="">Select a category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                                <div class="error-message text-red-500 text-sm mt-1"></div>
                            </div>
                        </div>

                        <!-- Deadline field -->
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">
                                Deadline <span class="text-gray-400 font-normal">(optional)</span>
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       id="deadline" 
                                       name="deadline" 
                                       class="block w-full pl-10 pr-10 py-2.5 sm:py-3 text-base rounded-lg border-gray-300 
                                              focus:ring-indigo-500 focus:border-indigo-500" 
                                       placeholder="Select deadline (optional)">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" 
                                            class="text-gray-400 hover:text-gray-500 focus:outline-none" 
                                            onclick="document.getElementById('deadline').value = ''">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="error-message text-red-500 text-sm mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Form actions -->
                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4 mt-6">
                        <button type="button" 
                                onclick="closeTaskModal()" 
                                class="w-full sm:w-auto px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="w-full sm:w-auto px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr
    flatpickr("#deadline", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        altInput: true,
        altFormat: "F j, Y at h:i K",
        time_24hr: false,
        minDate: "today",
        minuteIncrement: 15,
        defaultHour: 9,
        position: "auto",
        allowInput: true,
        disableMobile: false,
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates[0]) {
                scheduleDeadlineNotification(selectedDates[0]);
            }
        }
    });

    // Form validation
    const taskForm = document.getElementById('taskForm');
    const formFields = {
        title: {
            required: true,
            minLength: 3,
            maxLength: 100,
            message: {
                required: 'Title is required',
                minLength: 'Title must be at least 3 characters',
                maxLength: 'Title cannot exceed 100 characters'
            }
        },
        description: {
            maxLength: 500,
            message: {
                maxLength: 'Description cannot exceed 500 characters'
            }
        },
        category_id: {
            required: true,
            message: {
                required: 'Please select a category'
            }
        }
    };

    // Validate form before submission
    taskForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        clearFormErrors();
        
        // Validate all fields
        const errors = validateForm(this, formFields);
        
        if (Object.keys(errors).length === 0) {
            // If no errors, proceed with submission
            const taskId = $('#taskId').val();
            if (taskId) {
                submitTaskEdit(taskId);
            } else {
                submitTaskForm();
            }
        } else {
            // Display errors
            displayFormErrors(errors);
            // Scroll to first error
            const firstError = document.querySelector('.error-message:not(:empty)');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    // Clear form errors when inputs change
    $('#taskForm input, #taskForm textarea, #taskForm select').on('input change', function() {
        $(this).removeClass('border-red-500')
               .closest('.relative')
               .find('.error-message')
               .text('');
    });
});

// Form validation function
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
        }
    }

    return errors;
}

// Clear all form errors
function clearFormErrors() {
    $('.error-message').text('');
    $('input, select, textarea').removeClass('border-red-500');
}

// Display form errors
function displayFormErrors(errors) {
    for (const [field, message] of Object.entries(errors)) {
        const input = $(`#${field}`);
        if (input.length) {
            input.addClass('border-red-500')
                 .closest('.relative')
                 .find('.error-message')
                 .text(message);
        }
    }
}
</script>
<?= $this->endSection() ?> 