"use strict";

function secure_password(input) {
    var password = input.val();
    var capital = /[ABCDEFGHIJKLMNOPQRSTUVWXYZ]/;
    var capital = capital.test(password);
    if (!capital) {
        $('.capital').removeClass('success');
        $('.capital').addClass('error');
    } else {
        $('.capital').removeClass('error');
        $('.capital').addClass('success');
    }
    var lower = /[abcdefghijklmnopqrstuvwxyz]/;
    var lower = lower.test(password);
    if (!lower) {
        $('.lower').removeClass('success');
        $('.lower').addClass('error');
    } else {
        $('.lower').removeClass('error');
        $('.lower').addClass('success');
    }
    var number = /[1234567890]/;
    var number = number.test(password);
    if (!number) {
        $('.number').removeClass('success');
        $('.number').addClass('error');
    } else {
        $('.number').removeClass('error');
        $('.number').addClass('success');
    }
    var special = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
    var special = special.test(password);
    if (!special) {
        $('.special').removeClass('success');
        $('.special').addClass('error');
    } else {
        $('.special').removeClass('error');
        $('.special').addClass('success');
    }
    var minimum = password.length;
    if (minimum < 6) {
        $('.minimum').removeClass('success');
        $('.minimum').addClass('error');
    } else {
        $('.minimum').removeClass('error');
        $('.minimum').addClass('success');
    }
}

// Enhanced Strong Password Validation
function updatePasswordRequirements(password) {
    // Uppercase check
    const capital = /[A-Z]/.test(password);
    updateRequirement('.capital', capital);
    
    // Lowercase check
    const lower = /[a-z]/.test(password);
    updateRequirement('.lower', lower);
    
    // Number check
    const number = /[0-9]/.test(password);
    updateRequirement('.number', number);
    
    // Special character check
    const special = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/.test(password);
    updateRequirement('.special', special);
    
    // Minimum length check
    const minimum = password.length >= 6;
    updateRequirement('.minimum', minimum);
    
    // Update password strength indicator
    updatePasswordStrength(password);
}

function updateRequirement(selector, isValid) {
    const requirement = $(selector).closest('.password-requirement');
    const icon = requirement.find('.requirement-icon');
    
    if (isValid) {
        icon.removeClass('error').addClass('success').text('✓');
        requirement.removeClass('error').addClass('success');
    } else {
        icon.removeClass('success').addClass('error').text('✗');
        requirement.removeClass('success').addClass('error');
    }
}

function updatePasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 6) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    const strengthContainer = $('.password-strength');
    const strengthBar = $('.password-strength-bar');
    
    if (strengthContainer.length && strengthBar.length) {
        strengthContainer.removeClass('password-strength-weak password-strength-fair password-strength-good password-strength-strong');
        
        if (strength <= 1) {
            strengthContainer.addClass('password-strength-weak');
        } else if (strength <= 2) {
            strengthContainer.addClass('password-strength-fair');
        } else if (strength <= 3) {
            strengthContainer.addClass('password-strength-good');
        } else {
            strengthContainer.addClass('password-strength-strong');
        }
    }
}

// Original function for backward compatibility
function secure_password(input) {
    var password = input.val();
    updatePasswordRequirements(password);
}

(function ($) {
    // Enhanced event handling
    $('.secure-password').on('input keyup', function() {
        updatePasswordRequirements($(this).val());
    });
    
    // Initialize on page load
    $('.secure-password').each(function() {
        updatePasswordRequirements($(this).val());
    });
    
    // Original event handling for backward compatibility
    $('.secure-password').on('input', function () {
        secure_password($(this));
    });

    $('.secure-password').focus(function () {
        $(this).closest('div').addClass('hover-input-popup');
    });

    $('.secure-password').focusout(function () {
        $(this).closest('div').removeClass('hover-input-popup');
    });
    $('.secure-password').closest('div').append(`<div class="input-popup">
                                                    <p class="error lower">1 small letter minimum</p>
                                                    <p class="error capital">1 capital letter minimum</p>
                                                    <p class="error number">1 number minimum</p>
                                                    <p class="error special">1 special character minimum</p>
                                                    <p class="error minimum">6 character password</p>
                                                </div>`);
})(jQuery);
