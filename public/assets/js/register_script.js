document.addEventListener('DOMContentLoaded', function() {
    // Theme Toggle Functionality
    const themeButton = document.getElementById('theme-button');
    
    // Check for saved theme preference or use preferred color scheme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.remove('light-mode');
        document.body.classList.add('dark-mode');
    } else if (savedTheme === 'light') {
        document.body.classList.remove('dark-mode');
        document.body.classList.add('light-mode');
    } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.body.classList.remove('light-mode');
        document.body.classList.add('dark-mode');
    }
    
    // Theme toggle event listener
    themeButton.addEventListener('click', function() {
        if (document.body.classList.contains('light-mode')) {
            document.body.classList.remove('light-mode');
            document.body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.classList.remove('dark-mode');
            document.body.classList.add('light-mode');
            localStorage.setItem('theme', 'light');
        }
    });
    
    // Password Toggle Functionality
    const togglePassword = document.getElementById('toggle-password');
    const passwordField = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        
        // Change icon based on password visibility
        const showPasswordIcon = togglePassword.querySelector('.show-password');
        if (type === 'password') {
            showPasswordIcon.textContent = '👁️';
        } else {
            showPasswordIcon.textContent = '🔒';
        }
    });
    
    // Form Validation and Submission Animation
    const registerForm = document.querySelector('form');
    const registerButton = document.querySelector('.register-button');
    
    registerForm.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const terms = document.getElementById('terms');
        
        // Basic validation
        if (name.trim() === '' || email.trim() === '' || password.trim() === '') {
            e.preventDefault();
            showErrorMessage('Semua field harus diisi');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            showErrorMessage('Format email tidak valid');
            return;
        }
        
        // Password strength validation
        if (password.length < 8) {
            e.preventDefault();
            showErrorMessage('Password minimal 8 karakter');
            return;
        }
        
        // Terms check
        if (!terms.checked) {
            e.preventDefault();
            showErrorMessage('Anda harus menyetujui Syarat & Ketentuan');
            return;
        }
        
        // Show loading state on button
        registerButton.textContent = 'Mendaftar...';
        registerButton.style.opacity = '0.8';
        registerButton.style.pointerEvents = 'none';
        
        // Form will submit normally if validation passes
    });
    
    function showErrorMessage(message) {
        // Remove any existing error messages
        const existingError = document.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        
        // Find where to insert the error message
        if (message.includes('Syarat')) {
            // Insert after terms checkbox
            const termsPrivacy = document.querySelector('.terms-privacy');
            termsPrivacy.insertAdjacentElement('afterend', errorDiv);
        } else if (message.includes('Password')) {
            // Insert after password field
            const passwordGroup = document.getElementById('password').parentElement;
            passwordGroup.insertAdjacentElement('afterend', errorDiv);
        } else if (message.includes('email')) {
            // Insert after email field
            const emailGroup = document.getElementById('email').parentElement;
            emailGroup.insertAdjacentElement('afterend', errorDiv);
        } else {
            // Default: insert before the register button
            const registerButton = document.querySelector('.register-button');
            registerButton.parentElement.insertBefore(errorDiv, registerButton);
        }
        
        // Focus the first invalid field
        if (message.includes('field')) {
            const emptyField = Array.from(registerForm.elements).find(el => el.value.trim() === '' && el.type !== 'checkbox');
            if (emptyField) emptyField.focus();
        } else if (message.includes('email')) {
            document.getElementById('email').focus();
        } else if (message.includes('Password')) {
            document.getElementById('password').focus();
        } else if (message.includes('Syarat')) {
            document.getElementById('terms').focus();
        }
        
        // Add animation effect
        errorDiv.style.animation = 'fadeIn 0.3s ease';
    }
    
    // Add form field focus effects
    const formInputs = document.querySelectorAll('.form-group input, .form-group select');
    
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('input-focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('input-focused');
        });
    });
    
    // Ensure password strength visualization
    const passwordInput = document.getElementById('password');
    passwordInput.addEventListener('input', function() {
        // Remove existing strength indicator
        const existingIndicator = document.querySelector('.password-strength');
        if (existingIndicator) {
            existingIndicator.remove();
        }
        
        // Skip if password is empty
        if (this.value.trim() === '') {
            return;
        }
        
        // Create password strength container
        const strengthDiv = document.createElement('div');
        strengthDiv.className = 'password-strength';
        strengthDiv.style.display = 'flex';
        strengthDiv.style.gap = '5px';
        strengthDiv.style.marginTop = '8px';
        
        // Calculate password strength
        const strength = calculatePasswordStrength(this.value);
        
        // Create 3 bars
        for (let i = 0; i < 3; i++) {
            const bar = document.createElement('div');
            bar.style.height = '4px';
            bar.style.flexGrow = '1';
            bar.style.borderRadius = '2px';
            bar.style.transition = 'background 0.3s ease';
            
            if (i < strength) {
                if (strength === 1) {
                    bar.style.backgroundColor = '#e74c3c'; // Weak - Red
                } else if (strength === 2) {
                    bar.style.backgroundColor = '#f39c12'; // Medium - Orange
                } else {
                    bar.style.backgroundColor = '#2ecc71'; // Strong - Green
                }
            } else {
                bar.style.backgroundColor = '#e1e5eb'; // Empty bar
            }
            
            strengthDiv.appendChild(bar);
        }
        
        // Add strength text
        const strengthText = document.createElement('span');
        strengthText.style.fontSize = '12px';
        strengthText.style.marginLeft = '8px';
        
        if (strength === 1) {
            strengthText.textContent = 'Lemah';
            strengthText.style.color = '#e74c3c';
        } else if (strength === 2) {
            strengthText.textContent = 'Sedang';
            strengthText.style.color = '#f39c12';
        } else {
            strengthText.textContent = 'Kuat';
            strengthText.style.color = '#2ecc71';
        }
        
        strengthDiv.appendChild(strengthText);
        
        // Insert after password input
        this.parentElement.appendChild(strengthDiv);
    });
    
    function calculatePasswordStrength(password) {
        // Simple password strength calculation
        let strength = 0;
        
        if (password.length >= 8) strength++;
        
        // Check for mixed case, numbers, and special characters
        if (/[A-Z]/.test(password) && /[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) strength++;
        
        return strength;
    }
});