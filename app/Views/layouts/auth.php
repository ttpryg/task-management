<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?= csrf_meta() ?>
    <title><?= $this->renderSection('title') ?> - Task Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <style>
        /* Modern toast styles */
        .toastify {
            padding: 16px 40px 16px 20px;
            color: #1a1a1a;
            font-size: 14px;
            line-height: 1.5;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
            position: fixed;
            top: 16px !important;
            right: 16px !important;
            margin: 0 !important;
            max-width: min(380px, calc(100vw - 32px));
            min-width: min(320px, calc(100vw - 32px));
            backdrop-filter: blur(8px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            transform: translateY(0);
            transition: all 0.2s ease;
            z-index: 9999;
            background: white !important;
        }

        @media (max-width: 640px) {
            .toastify {
                top: auto !important;
                bottom: 16px !important;
                left: 16px !important;
                right: 16px !important;
                width: calc(100% - 32px) !important;
                max-width: none;
                min-width: 0;
            }
        }

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

        /* Modern close button */
        .toast-close {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            opacity: 0.5;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .toast-close::before,
        .toast-close::after {
            content: '';
            position: absolute;
            width: 12px;
            height: 2px;
            background-color: currentColor;
            border-radius: 1px;
        }

        .toast-close::before {
            transform: rotate(45deg);
        }

        .toast-close::after {
            transform: rotate(-45deg);
        }

        .toast-close:hover {
            opacity: 1;
            background: rgba(0, 0, 0, 0.05);
        }

        .toastify.colored-toast.success .toast-close {
            color: #15803d;
        }

        .toastify.colored-toast.error .toast-close {
            color: #b91c1c;
        }

        /* Hover and animation effects */
        @media (min-width: 640px) {
            .toastify:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.16);
            }
        }

        /* Animation for new toasts */
        @keyframes slideIn {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .toastify {
            animation: slideIn 0.3s ease forwards;
        }

        .form-group input:focus {
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }
        .form-group input.border-rose-500:focus {
            box-shadow: 0 0 0 2px rgba(225, 29, 72, 0.2);
        }

        /* Responsive form styles */
        @media (max-width: 640px) {
            .form-group input,
            .form-group select {
                font-size: 16px !important; /* Prevent iOS zoom */
            }
        }

        /* Safe area handling for modern iOS devices */
        @supports (padding: max(0px)) {
            body {
                padding-left: env(safe-area-inset-left);
                padding-right: env(safe-area-inset-right);
                padding-bottom: env(safe-area-inset-bottom);
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-6 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md mx-auto">
            <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <script>
        // Helper function for creating toasts
        function showNotification(message, type = 'success') {
            Toastify({
                text: message,
                duration: 4000,
                gravity: window.innerWidth < 640 ? "bottom" : "top",
                position: window.innerWidth < 640 ? "center" : "right",
                className: `colored-toast ${type}`,
                stopOnFocus: true,
                close: true,
                style: {
                    background: "transparent"
                }
            }).showToast();
        }

        // Setup AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            beforeSend: function(xhr, settings) {
                if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type)) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('input[name="csrf_token"]').val());
                }
            }
        });

        // Function to refresh CSRF token
        function refreshCsrfToken(newToken) {
            if (newToken) {
                $('input[name="csrf_token"]').val(newToken);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': newToken
                    }
                });
            }
        }

        // Handle iOS viewport height issue
        function setVH() {
            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        }
        
        window.addEventListener('resize', setVH);
        window.addEventListener('orientationchange', setVH);
        setVH();
    </script>
    <?= $this->renderSection('scripts') ?>
    <script src="<?= base_url('js/auth.js') ?>"></script>
</body>
</html> 