<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= $this->renderSection('title') ?> - Task Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        /* Base container styles */
        .container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 1rem;
        }

        /* Responsive styles */
        @media (max-width: 1024px) {
            .container {
                max-width: none;
                padding: 1rem;
            }

            .task-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 1rem;
            }

            /* Tablet navigation fixes */
            .nav-container {
                padding: 0 1rem;
            }

            /* Ensure buttons don't overflow */
            .button-container {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .button-container button {
                flex: 0 1 auto;
                white-space: nowrap;
            }
        }

        @media (max-width: 768px) {
            .task-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            /* Improve mobile navigation */
            .nav-content {
                padding: 0.5rem;
            }

            /* Adjust button sizes for better touch targets */
            .button-container button {
                padding: 0.625rem 1rem;
                font-size: 0.875rem;
            }
        }

        @media (max-width: 640px) {
            .container {
                padding: 0.75rem;
            }
            
            /* Mobile modal improvements */
            .modal-container {
                width: 100% !important;
                margin: 0 !important;
                padding: 1rem !important;
                max-height: 100vh !important;
                overflow-y: auto !important;
                border-radius: 0 !important;
            }

            /* Mobile form improvements */
            .form-grid {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
            
            .form-field {
                margin-bottom: 0.75rem;
            }

            /* Improve input touch targets */
            input, select, textarea {
                font-size: 16px !important; /* Prevent iOS zoom */
                padding: 0.75rem !important;
                min-height: 44px !important; /* Better touch targets */
            }

            /* Mobile date picker improvements */
            .flatpickr-calendar {
                width: calc(100% - 2rem) !important;
                max-width: none !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
                position: fixed !important;
                top: auto !important;
                bottom: 0 !important;
                border-radius: 1rem 1rem 0 0 !important;
                box-shadow: 0 -2px 10px rgba(0,0,0,0.1) !important;
            }

            .flatpickr-months {
                border-radius: 1rem 1rem 0 0;
            }

            /* Improve mobile buttons */
            .action-buttons {
                display: flex;
                flex-direction: column-reverse;
                gap: 0.5rem;
                width: 100%;
            }

            .action-buttons button {
                width: 100%;
                margin: 0;
                padding: 0.75rem;
                min-height: 44px;
            }

            /* Toast improvements for mobile */
            .toastify {
                max-width: calc(100% - 2rem) !important;
                min-width: 0 !important;
                width: auto !important;
                margin: 0.5rem 1rem !important;
                left: 0 !important;
                right: 0 !important;
                transform: none !important;
                border-radius: 0.5rem !important;
            }
        }

        /* Modern toast styles */
        .toastify {
            padding: 1rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.75rem;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 9999;
        }

        /* Toast variants */
        .toastify.colored-toast.success {
            background: #f0fdf4 !important;
            border-left: 4px solid #15803d;
            color: #15803d;
        }

        .toastify.colored-toast.error {
            background: #fef2f2 !important;
            border-left: 4px solid #b91c1c;
            color: #b91c1c;
        }

        .toastify.colored-toast.info {
            background: white !important;
            border-left: 4px solid #2563EB;
            color: #1e40af;
        }

        /* Flatpickr customization */
        .flatpickr-calendar {
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            width: 100% !important;
            max-width: 320px;
        }
        
        .flatpickr-day.selected {
            background: #4f46e5 !important;
            border-color: #4f46e5 !important;
        }

        .flatpickr-day:hover {
            background: #e0e7ff !important;
        }

        /* Fix navigation container */
        .nav-fixed {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
        }

        /* Ensure content doesn't hide under fixed nav */
        .nav-spacer {
            height: 4rem;
        }

        /* Improve button touch targets */
        .btn {
            min-height: 44px;
            padding: 0.625rem 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php if(session()->get('isLoggedIn')): ?>
        <nav class="bg-white border-b border-gray-100 nav-fixed">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left side - Brand -->
                    <div class="flex items-center">
                        <a href="/dashboard" class="text-xl font-semibold text-indigo-600 hover:text-indigo-700 transition-colors truncate max-w-[200px]">
                            Task Management
                        </a>
                    </div>

                    <!-- Right side - User Menu -->
                    <div class="flex items-center">
                        <div class="relative group">
                            <button id="userMenuButton" 
                                    class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-gray-100 
                                           hover:bg-gray-50 hover:border-gray-200 shadow-sm transition-all duration-200 group">
                                <!-- User Avatar -->
                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 
                                           flex items-center justify-center shadow-sm ring-2 ring-white">
                                    <span class="text-sm font-medium text-white">
                                        <?= strtoupper(substr(session()->get('username'), 0, 1)) ?>
                                    </span>
                                </div>
                                
                                <!-- Welcome Text - Hide on mobile -->
                                <div class="hidden sm:flex flex-col items-start">
                                    <span class="text-xs font-medium text-gray-400">Welcome,</span>
                                    <span class="text-sm text-gray-700 font-semibold group-hover:text-gray-900 transition-colors truncate max-w-[120px]">
                                        <?= esc(session()->get('username')) ?>
                                    </span>
                                </div>
                            </button>

                            <!-- Dropdown menu -->
                            <div class="absolute right-0 mt-2 w-48 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 
                                      border border-gray-100 invisible group-hover:visible opacity-0 group-hover:opacity-100 
                                      transition-all duration-200 transform origin-top-right z-50">
                                <div class="px-2 py-1.5">
                                    <div class="px-3 py-1.5 mb-1.5 border-b border-gray-100">
                                        <p class="text-xs font-medium text-gray-400">Signed in as</p>
                                        <p class="text-sm font-medium text-gray-700 truncate"><?= esc(session()->get('username')) ?></p>
                                    </div>
                                    
                                    <a href="/auth/logout" 
                                       class="group flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg
                                              text-gray-600 hover:text-rose-500 hover:bg-rose-50/80
                                              transition-all duration-200 ease-in-out">
                                        <svg class="mr-2.5 w-4 h-4 text-gray-400 group-hover:text-rose-500 transition-colors" 
                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Sign out
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <div class="nav-spacer"></div>
    <?php endif; ?>

    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>

    <script>
        // Helper function for creating toasts
        function showNotification(message, type = 'success') {
            Toastify({
                text: message,
                duration: 4000,
                gravity: "top",
                position: window.innerWidth <= 640 ? "center" : "right",
                className: `colored-toast ${type}`,
                stopOnFocus: true,
                close: true,
                style: {
                    background: "transparent"
                }
            }).showToast();
        }

        // Setup AJAX with proper CSRF handling
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                // Only add CSRF token for non-GET requests
                if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type)) {
                    const token = $('meta[name="csrf-token"]').attr('content');
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }
            }
        });

        // Update CSRF token after each AJAX request
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (xhr.responseJSON && xhr.responseJSON.csrf_token) {
                const newToken = xhr.responseJSON.csrf_token;
                $('meta[name="csrf-token"]').attr('content', newToken);
                $('.csrf_token').val(newToken);
            }
        });

        // Handle AJAX errors globally
        $(document).ajaxError(function(event, xhr, settings, error) {
            if (xhr.status === 403 && xhr.responseJSON) {
                // Handle CSRF token expiration/mismatch
                if (xhr.responseJSON.csrf_token) {
                    const newToken = xhr.responseJSON.csrf_token;
                    $('meta[name="csrf-token"]').attr('content', newToken);
                    $('.csrf_token').val(newToken);
                    
                    // Show error message
                    showNotification('Session expired. Retrying...', 'error');
                    
                    // Retry the failed request
                    setTimeout(() => {
                        $.ajax(settings);
                    }, 100);
                } else {
                    showNotification('Access denied. Please refresh the page and try again.', 'error');
                }
            } else if (xhr.status === 500) {
                showNotification('An error occurred. Please try again.', 'error');
            }
        });

        // Responsive handlers
        function handleResize() {
            // Update any size-dependent UI elements
            const toasts = document.querySelectorAll('.toastify');
            toasts.forEach(toast => {
                if (window.innerWidth <= 640) {
                    toast.style.right = '0';
                    toast.style.left = '0';
                    toast.style.transform = 'none';
                } else {
                    toast.style.right = '16px';
                    toast.style.left = 'auto';
                    toast.style.transform = 'none';
                }
            });

            // Handle datepicker positioning
            const datePicker = document.querySelector('.flatpickr-calendar');
            if (datePicker && window.innerWidth <= 640) {
                datePicker.style.position = 'fixed';
                datePicker.style.bottom = '0';
                datePicker.style.left = '50%';
                datePicker.style.transform = 'translateX(-50%)';
                datePicker.style.width = 'calc(100% - 2rem)';
                datePicker.style.maxWidth = 'none';
            }
        }

        // Listen for window resize and orientation change
        window.addEventListener('resize', handleResize);
        window.addEventListener('orientationchange', handleResize);
        
        // Initial call
        handleResize();

        // Initialize Flatpickr with mobile-friendly options
        flatpickr.setDefaults({
            disableMobile: true, // Disable native mobile datetime input
            dateFormat: "Y-m-d H:i",
            enableTime: true,
            time_24hr: true,
            position: window.innerWidth <= 640 ? "below" : "auto"
        });
    </script>
    <script src="/js/script.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html> 