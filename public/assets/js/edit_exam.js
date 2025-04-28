document.addEventListener('DOMContentLoaded', function() {
    // Check for saved theme preference or use preferred color scheme
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    const currentTheme = localStorage.getItem('theme');
    
    if (currentTheme === 'dark' || (!currentTheme && prefersDarkScheme.matches)) {
        document.body.classList.add('dark-theme');
        document.getElementById('theme-switch').checked = true;
    }
    
    // Theme toggle switch functionality
    const themeSwitch = document.getElementById('theme-switch');
    themeSwitch.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('dark-theme');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.classList.remove('dark-theme');
            localStorage.setItem('theme', 'light');
        }
    });
    
    // Animated labels and form validation
    const inputs = document.querySelectorAll('input, textarea');
    
    inputs.forEach(input => {
        // Focus effects
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
            
            // Simple validation
            if (this.value.trim() === '' && this.hasAttribute('required')) {
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });
        
        // Initial validation state
        if (input.value.trim() !== '') {
            input.classList.add('has-value');
        }
        
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });
    });
    
    // Form submission with validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        let hasError = false;
        
        inputs.forEach(input => {
            if (input.hasAttribute('required') && input.value.trim() === '') {
                input.classList.add('error');
                hasError = true;
            }
        });
        
        if (hasError) {
            event.preventDefault();
            // Create alert for form errors
            if (!document.querySelector('.form-error-alert')) {
                const errorAlert = document.createElement('div');
                errorAlert.className = 'alert form-error-alert';
                errorAlert.innerHTML = '<i class="fas fa-exclamation-circle"></i> Harap isi semua field yang diperlukan.';
                
                const cardBody = document.querySelector('.card-body');
                cardBody.insertBefore(errorAlert, cardBody.firstChild);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (errorAlert.parentNode) {
                        errorAlert.parentNode.removeChild(errorAlert);
                    }
                }, 5000);
            }
        }
    });
});