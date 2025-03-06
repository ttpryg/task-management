// Utility Functions
function showFieldError(field, message) {
    const errorDiv = field.closest('.form-group').find('.error-message');
    field.addClass('border-rose-500 focus:ring-rose-500')
         .removeClass('border-gray-300 focus:ring-indigo-500');
    
    field.closest('.form-group').find('.fa-envelope, .fa-lock')
         .removeClass('text-gray-400')
         .addClass('text-rose-500');
    
    errorDiv.html(message).removeClass('hidden');
}

function clearFieldError(field) {
    const errorDiv = field.closest('.form-group').find('.error-message');
    field.removeClass('border-rose-500 focus:ring-rose-500')
         .addClass('border-gray-300 focus:ring-indigo-500');
    
    field.closest('.form-group').find('.fa-envelope, .fa-lock')
         .removeClass('text-rose-500')
         .addClass('text-gray-400');
    
    errorDiv.html('').addClass('hidden');
}

function clearAllErrors(form) {
    form.find('.form-group').each(function() {
        const field = $(this).find('input');
        clearFieldError(field);
    });
}

function showNotification(message, type = 'success') {
    Toastify({
        text: message,
        duration: 4000,
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

function togglePasswordVisibility(button) {
  const input = $(button).siblings('input');
  const icon = $(button).find('i');

  if (input.attr('type') === 'password') {
    input.attr('type', 'text');
    icon.removeClass('fa-eye').addClass('fa-eye-slash');
  } else {
    input.attr('type', 'password');
    icon.removeClass('fa-eye-slash').addClass('fa-eye');
  }
}

function refreshCsrfToken(newToken) {
  $('.csrf_token').val(newToken);
}

// Form Validation
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function validatePassword(password) {
  return password.length >= 8;
}

function validateName(name) {
  return name.trim().length >= 2;
}

function validateUsername(username) {
  return username.trim().length >= 3;
}

// Login Form Handling
$(document).ready(function() {
    const loginForm = $('#loginForm');
    const submitBtn = loginForm.find('button[type="submit"]');
    const originalBtnText = submitBtn.html();
    let isSubmitting = false;

    function showLoading() {
        isSubmitting = true;
        submitBtn.prop('disabled', true)
            .addClass('cursor-not-allowed opacity-75')
            .html('<span class="inline-flex items-center"><i class="fas fa-spinner fa-spin mr-2"></i>Signing in...</span>');
    }

    function hideLoading() {
        isSubmitting = false;
        submitBtn.prop('disabled', false)
            .removeClass('cursor-not-allowed opacity-75')
            .html(originalBtnText);
    }

    loginForm.on('submit', function(e) {
        e.preventDefault();
        clearAllErrors($(this));

        const emailOrUsername = $('#email').val().trim();
        const password = $('#password').val();
        let hasError = false;

        if (!emailOrUsername) {
            showFieldError($('#email'), 'Email or username is required');
            hasError = true;
        }

        if (!password) {
            showFieldError($('#password'), 'Password is required');
            hasError = true;
        }

        if (hasError) return;
        if (isSubmitting) return;

        showLoading();

        $.ajax({
            url: loginForm.attr('action'),
            type: 'POST',
            data: loginForm.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.csrf_token) {
                    refreshCsrfToken(response.csrf_token);
                }

                if (response.success) {
                    showNotification('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 1500);
                } else {
                    hideLoading();
                    if (response.field === 'email') {
                        showFieldError($('#email'), response.message);
                        clearFieldError($('#password'));
                    } else if (response.field === 'password') {
                        showFieldError($('#password'), response.message);
                        clearFieldError($('#email'));
                    } else {
                        showNotification(response.message || 'Login failed. Please try again.', 'error');
                    }
                }
            },
            error: function(xhr) {
                hideLoading();
                const response = xhr.responseJSON || {};
                
                if (response.csrf_token) {
                    refreshCsrfToken(response.csrf_token);
                }

                if (xhr.status === 401) {
                    if (response.field === 'email') {
                        showFieldError($('#email'), response.message);
                        clearFieldError($('#password'));
                    } else if (response.field === 'password') {
                        showFieldError($('#password'), response.message);
                        clearFieldError($('#email'));
                    } else {
                        showFieldError($('#email'), response.message);
                        showFieldError($('#password'), response.message);
                    }
                } else {
                    showNotification('An error occurred. Please try again.', 'error');
                }
            }
        });
    });

    // Add focus animations for login form
    loginForm.find('input').on('focus', function() {
        $(this).closest('.form-group').find('.fa-envelope, .fa-lock')
            .removeClass('text-gray-400')
            .addClass('text-indigo-500');
    }).on('blur', function() {
        if (!$(this).hasClass('border-rose-500')) {
            $(this).closest('.form-group').find('.fa-envelope, .fa-lock')
                .removeClass('text-indigo-500')
                .addClass('text-gray-400');
        }
    });

    // Clear field errors on input for login form
    loginForm.find('input').on('input', function() {
        clearFieldError($(this));
    });
});

// Register Form Handling
$(document).ready(function () {
  const registerForm = $('#registerForm');
  const submitBtn = registerForm.find('button[type="submit"]');
  let isSubmitting = false;

  function showLoading() {
    isSubmitting = true;
    submitBtn
      .prop('disabled', true)
      .addClass('cursor-not-allowed opacity-75')
      .html('<i class="fas fa-spinner fa-spin mr-2"></i>Creating account...');
  }

  function hideLoading() {
    isSubmitting = false;
    submitBtn
      .prop('disabled', false)
      .removeClass('cursor-not-allowed opacity-75')
      .html('<i class="fas fa-user-plus mr-2"></i>Create Account');
  }

  registerForm.on('submit', function (e) {
    e.preventDefault();
    
    if (isSubmitting) {
      return false;
    }

    if (!validateForm()) {
      return false;
    }

    showLoading();

    $.ajax({
      url: registerForm.attr('action'),
      type: 'POST',
      data: registerForm.serialize(),
      dataType: 'json',
      beforeSend: function(xhr) {
        xhr.setRequestHeader('X-CSRF-TOKEN', $('input[name="csrf_token"]').val());
      },
      success: function (response) {
        if (response.csrf_token) {
          refreshCsrfToken(response.csrf_token);
        }

        if (response.success) {
          showNotification('Registration successful! Redirecting...', 'success');
          setTimeout(() => {
            window.location.href = '/auth/login';
          }, 1500);
        } else {
          hideLoading();
          if (response.errors) {
            Object.entries(response.errors).forEach(([field, message]) => {
              const $field = $(`#${field}`);
              if ($field.length) {
                showFieldError($field, message);
              } else {
                showNotification(message, 'error');
              }
            });
          } else {
            showNotification(response.message || 'Registration failed. Please try again.', 'error');
          }
        }
      },
      error: function (xhr) {
        hideLoading();
        const response = xhr.responseJSON || {};
        
        if (response.csrf_token) {
          refreshCsrfToken(response.csrf_token);
        }

        if (xhr.status === 403) {
          showNotification('Your session has expired. Please refresh the page and try again.', 'error');
          return;
        }

        if (response.errors) {
          Object.entries(response.errors).forEach(([field, message]) => {
            const $field = $(`#${field}`);
            if ($field.length) {
              showFieldError($field, message);
            } else {
              showNotification(message, 'error');
            }
          });
        } else {
          showNotification(response.message || 'An error occurred. Please try again.', 'error');
        }
      }
    });
  });
});
