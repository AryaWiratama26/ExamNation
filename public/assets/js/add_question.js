document.addEventListener('DOMContentLoaded', function() {
    // Dark mode toggle functionality
    const darkModeToggle = document.getElementById('darkmode-toggle');
    
    // Check for saved theme preference or respect OS preference
    const prefersDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');
    
    // Set initial theme
    if (savedTheme === 'dark' || (!savedTheme && prefersDarkMode)) {
        document.documentElement.setAttribute('data-theme', 'dark');
        darkModeToggle.checked = true;
    }
    
    // Toggle theme when switch is clicked
    darkModeToggle.addEventListener('change', function() {
        if (this.checked) {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
        }
    });
    
    // File input display functionality
    const fileInput = document.getElementById('excel_file');
    const fileNameDisplay = document.getElementById('file-name');
    
    if (fileInput && fileNameDisplay) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileNameDisplay.textContent = this.files[0].name;
            } else {
                fileNameDisplay.textContent = 'Tidak ada file yang dipilih';
            }
        });
    }
    
    // Handle correct option value from text input if needed
    const correctOptionInput = document.querySelector('input[name="correct_option"]');
    const radioInputs = document.querySelectorAll('input[type="radio"][name="correct_option"]');
    
    if (correctOptionInput && correctOptionInput.value) {
        const value = correctOptionInput.value.toUpperCase();
        const radioToCheck = document.getElementById(`correct_${value.toLowerCase()}`);
        if (radioToCheck) {
            radioToCheck.checked = true;
        }
    }
    
    // Form submission handler to ensure backward compatibility
    const form = document.querySelector('form[action*="store-question"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedRadio = document.querySelector('input[type="radio"][name="correct_option"]:checked');
            if (checkedRadio) {
                // If we're using the new radio buttons, update the hidden field for backend compatibility
                if (!document.getElementById('correct_option').value) {
                    document.getElementById('correct_option').value = checkedRadio.value;
                }
            }
        });
    }
});