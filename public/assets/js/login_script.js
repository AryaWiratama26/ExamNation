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
    const loginForm = document.querySelector('form');
    const loginButton = document.querySelector('.login-button');
    
    loginForm.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        if (email.trim() === '' || password.trim() === '') {
            e.preventDefault();
            showErrorMessage('Please fill in all fields');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            showErrorMessage('Please enter a valid email address');
            return;
        }
        
        // Show loading state on button
        loginButton.textContent = 'Signing in...';
        loginButton.style.opacity = '0.8';
        loginButton.style.pointerEvents = 'none';
        
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
        errorDiv.style.color = '#e74c3c';
        errorDiv.style.fontSize = '14px';
        errorDiv.style.marginTop = '-16px';
        errorDiv.style.marginBottom = '16px';
        errorDiv.style.textAlign = 'left';
        errorDiv.textContent = message;
        
        // Insert before the form options
        const formOptions = document.querySelector('.form-options');
        formOptions.parentNode.insertBefore(errorDiv, formOptions);
        
        // Focus the first invalid field
        if (message.includes('email')) {
            document.getElementById('email').focus();
        } else if (message.includes('password')) {
            document.getElementById('password').focus();
        }
        
        // Add animation effect
        errorDiv.style.animation = 'fadeIn 0.3s ease';
    }
    
    // Add form field focus effects
    const formInputs = document.querySelectorAll('.form-group input');
    
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('input-focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('input-focused');
        });
    });
});