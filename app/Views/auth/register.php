<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>Register<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="text-center mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Create Account</h1>
    <p class="mt-2 text-sm text-gray-600">
        Already have an account? 
        <a href="/auth/login" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
            Sign in here
        </a>
    </p>
</div>

<?= form_open('auth/attemptRegister', ['id' => 'registerForm', 'class' => 'space-y-6', 'novalidate' => true]) ?>
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" class="csrf_token">
    <div class="space-y-4">
        <!-- Username Field -->
        <div class="form-group">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Username <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                <input type="text" 
                       id="name" 
                       name="username" 
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm 
                              placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                              focus:border-transparent transition-colors text-sm"
                       placeholder="Choose a username"
                       autocomplete="off">
            </div>
            <div class="error-message mt-1 text-sm text-rose-500 hidden"></div>
        </div>

        <!-- Email Field -->
        <div class="form-group">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email address <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm 
                              placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                              focus:border-transparent transition-colors text-sm"
                       placeholder="Enter your email"
                       autocomplete="off">
            </div>
            <div class="error-message mt-1 text-sm text-rose-500 hidden"></div>
        </div>

        <!-- Password Field -->
        <div class="form-group">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Password <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg shadow-sm 
                              placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                              focus:border-transparent transition-colors text-sm"
                       placeholder="Enter your password"
                       autocomplete="off">
                <button type="button" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 
                               focus:outline-none transition-colors"
                        onclick="togglePasswordVisibility(this)">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="error-message mt-1 text-sm text-rose-500 hidden"></div>
            
            <!-- Password Strength Meter -->
            <div class="password-strength mt-2 hidden">
                <div class="flex space-x-1 mb-1">
                    <div class="flex-1 h-1 rounded-full bg-gray-200 transition-colors"></div>
                    <div class="flex-1 h-1 rounded-full bg-gray-200 transition-colors"></div>
                    <div class="flex-1 h-1 rounded-full bg-gray-200 transition-colors"></div>
                    <div class="flex-1 h-1 rounded-full bg-gray-200 transition-colors"></div>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="strength-text text-gray-500">Password strength</span>
                    <span class="text-gray-400">min. 8 characters</span>
                </div>
            </div>
        </div>

        <!-- Confirm Password Field -->
        <div class="form-group">
            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                Confirm Password <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input type="password" 
                       id="confirm_password" 
                       name="confirm_password" 
                       class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg shadow-sm 
                              placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                              focus:border-transparent transition-colors text-sm"
                       placeholder="Confirm your password"
                       autocomplete="off">
                <button type="button" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 
                               focus:outline-none transition-colors"
                        onclick="togglePasswordVisibility(this)">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="error-message mt-1 text-sm text-rose-500 hidden"></div>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" 
                class="relative w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg 
                       shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 
                       transition-all duration-200 ease-in-out">
            <span class="inline-flex items-center">
                <i class="fas fa-user-plus mr-2"></i>
                Create Account
            </span>
        </button>
    </div>
<?= form_close() ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Password visibility toggle
function togglePasswordVisibility(button) {
    const input = button.parentElement.querySelector('input');
    const icon = button.querySelector('i');
    
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}

// Password strength checker
function checkPasswordStrength(password) {
    const criteria = [
        password.length >= 8,           // Length check
        /\d/.test(password),           // Contains number
        /[a-zA-Z]/.test(password),     // Contains letter
        /[!@#$%^&*(),.?":{}|<>]/.test(password) // Contains special character
    ];
    
    const strength = criteria.filter(Boolean).length;
    updateStrengthIndicators(strength);
    return strength;
}

function updateStrengthIndicators(strength) {
    const indicators = document.querySelectorAll('.password-strength .h-1');
    const strengthText = document.querySelector('.strength-text');
    const strengthLevels = {
        weak: { color: 'rose', text: 'weak' },
        medium: { color: 'yellow', text: 'medium' },
        strong: { color: 'green', text: 'strong' }
    };

    // Reset indicators
    indicators.forEach(indicator => {
        indicator.className = 'flex-1 h-1 rounded-full bg-gray-200';
    });

    // Get strength level
    const level = strength <= 2 ? 'weak' : strength === 3 ? 'medium' : 'strong';
    const { color, text } = strengthLevels[level];

    // Update indicators and text
    for (let i = 0; i < strength; i++) {
        indicators[i].classList.remove('bg-gray-200');
        indicators[i].classList.add(`bg-${color}-500`);
    }

    strengthText.textContent = text;
    strengthText.className = `strength-text text-${color}-500`;
}

// Form handling
$(document).ready(function() {
    const form = $('#registerForm');
    const submitButton = form.find('button[type="submit"]');
    let isSubmitting = false;

    // Validation rules
    const validationRules = {
        username: {
            minLength: 3,
            maxLength: 50,
            pattern: /^[a-zA-Z0-9_-]+$/,
            messages: {
                required: 'Username is required',
                minLength: 'Username must be at least 3 characters long',
                maxLength: 'Username cannot exceed 50 characters',
                pattern: 'Username can only contain letters, numbers, underscores and hyphens'
            }
        },
        email: {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            messages: {
                required: 'Email address is required',
                pattern: 'Please enter a valid email address'
            }
        },
        password: {
            minLength: 8,
            pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/,
            messages: {
                required: 'Password is required',
                minLength: 'Password must be at least 8 characters long',
                pattern: 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character'
            }
        }
    };

    // Form field event handlers
    function setupFormFieldHandlers() {
        // Clear errors on input
        form.find('input').on('input', clearFieldError);

        // Password strength meter
        const $password = $('#password');
        $password
            .on('focus', () => $('.password-strength').removeClass('hidden'))
            .on('input', e => checkPasswordStrength(e.target.value));

        // Focus animations
        form.find('input').on('focus blur', function(e) {
            const $icon = $(this).closest('.form-group').find('.fa-user, .fa-envelope, .fa-lock');
            if (!$(this).hasClass('border-rose-500')) {
                $icon.toggleClass('text-gray-400', e.type === 'blur')
                     .toggleClass('text-indigo-500', e.type === 'focus');
            }
        });
    }

    // Form state management
    function showLoading() {
        isSubmitting = true;
        submitButton
            .prop('disabled', true)
            .addClass('cursor-not-allowed opacity-75')
            .html('<span class="inline-flex items-center"><i class="fas fa-spinner fa-spin mr-2"></i>Creating Account...</span>');
    }

    function hideLoading() {
        isSubmitting = false;
        submitButton
            .prop('disabled', false)
            .removeClass('cursor-not-allowed opacity-75')
            .html('<span class="inline-flex items-center"><i class="fas fa-user-plus mr-2"></i>Create Account</span>');
    }

    function refreshCsrfToken(newToken) {
        $('input[name="csrf_token"]').val(newToken);
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': newToken }
        });
    }

    // Error handling
    function showFieldError($field, message) {
        const $group = $field.closest('.form-group');
        const $errorDiv = $group.find('.error-message');
        const $icon = $group.find('.fa-user, .fa-envelope, .fa-lock');

        $field.removeClass('border-gray-300 focus:ring-indigo-500')
              .addClass('border-rose-500 focus:ring-rose-500');
        
        $icon.removeClass('text-gray-400')
             .addClass('text-rose-500');
        
        $errorDiv.html(message).removeClass('hidden');
    }

    function clearFieldError() {
        const $field = $(this);
        const $group = $field.closest('.form-group');
        const $errorDiv = $group.find('.error-message');
        const $icon = $group.find('.fa-user, .fa-envelope, .fa-lock');

        $field.removeClass('border-rose-500 focus:ring-rose-500')
              .addClass('border-gray-300 focus:ring-indigo-500');
        
        $icon.removeClass('text-rose-500')
             .addClass('text-gray-400');
        
        $errorDiv.html('').addClass('hidden');
    }

    // Form validation
    function validateField($field, rules, value) {
        if (!value && rules.messages.required) {
            return rules.messages.required;
        }

        if (value) {
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

    function validateForm() {
        let isValid = true;
        const fields = {
            username: { $el: $('#name'), rules: validationRules.username },
            email: { $el: $('#email'), rules: validationRules.email },
            password: { $el: $('#password'), rules: validationRules.password },
            confirmPassword: { $el: $('#confirm_password') }
        };

        // Clear previous errors
        form.find('input').each(function() {
            clearFieldError.call(this);
        });

        // Validate each field
        Object.entries(fields).forEach(([field, { $el, rules }]) => {
            const value = $el.val().trim();
            let error = null;

            if (field === 'confirmPassword') {
                if (!value) {
                    error = 'Please confirm your password';
                } else if (value !== fields.password.$el.val()) {
                    error = 'Passwords do not match';
                }
            } else {
                error = validateField($el, rules, value);
            }

            // Additional password strength check
            if (field === 'password' && !error && checkPasswordStrength(value) < 3) {
                error = 'Please choose a stronger password';
            }

            if (error) {
                showFieldError($el, error);
                isValid = false;
            }
        });

        return isValid;
    }

    // Form submission
    function handleFormSubmit(e) {
        e.preventDefault();
        
        if (isSubmitting || !validateForm()) {
            return false;
        }

        showLoading();
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            beforeSend: xhr => xhr.setRequestHeader('X-CSRF-TOKEN', $('input[name="csrf_token"]').val()),
            success: function(response) {
                if (response.csrf_token) {
                    refreshCsrfToken(response.csrf_token);
                }

                if (response.success) {
                    showNotification('Registration successful! Redirecting...', 'success');
                    setTimeout(() => window.location.href = '/auth/login', 1500);
                } else {
                    handleFormErrors(response);
                }
            },
            error: function(xhr) {
                handleFormErrors(xhr.responseJSON || {});
            }
        });
        
        return false;
    }

    function handleFormErrors(response) {
        if (response.csrf_token) {
            refreshCsrfToken(response.csrf_token);
        }

        if (response.status === 403) {
            showNotification('Your session has expired. Please refresh the page and try again.', 'error');
            return;
        }

        if (response.errors) {
            Object.entries(response.errors).forEach(([field, message]) => {
                const $field = $(`#${field}`);
                $field.length ? showFieldError($field, message) : showNotification(message, 'error');
            });
        } else {
            showNotification(response.message || 'An error occurred. Please try again.', 'error');
        }

        hideLoading();
    }

    // Initialize form
    setupFormFieldHandlers();
    form.on('submit', handleFormSubmit);
});
</script>
<?= $this->endSection() ?> 