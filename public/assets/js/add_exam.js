document.addEventListener('DOMContentLoaded', function() {
    // Mengatur dark mode
    const darkModeToggle = document.getElementById('darkmode-toggle');
    const body = document.body;
    
    // Cek apakah preferensi tema sudah tersimpan
    const savedTheme = localStorage.getItem('dark-mode');
    
    // Terapkan tema yang tersimpan atau gunakan preferensi sistem
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        body.classList.add('dark-mode');
        darkModeToggle.checked = true;
    }
    
    // Event listener untuk toggle dark mode
    darkModeToggle.addEventListener('change', function() {
        if (this.checked) {
            body.classList.add('dark-mode');
            localStorage.setItem('dark-mode', 'dark');
        } else {
            body.classList.remove('dark-mode');
            localStorage.setItem('dark-mode', 'light');
        }
    });
    
    // Form validation
    const examForm = document.getElementById('examForm');
    
    examForm.addEventListener('submit', function(e) {
        // Validasi judul ujian
        const title = document.getElementById('title').value.trim();
        if (title.length < 3) {
            e.preventDefault();
            showFormError('Judul ujian minimal 3 karakter.');
            return;
        }
        
        // Validasi deskripsi
        const description = document.getElementById('description').value.trim();
        if (description.length < 10) {
            e.preventDefault();
            showFormError('Deskripsi minimal 10 karakter.');
            return;
        }
        
        // Validasi durasi
        const duration = document.getElementById('duration').value;
        if (isNaN(duration) || duration <= 0) {
            e.preventDefault();
            showFormError('Durasi harus berupa angka positif.');
            return;
        }
        
        // Animasi loading saat submit
        const submitBtn = document.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        submitBtn.disabled = true;
    });
    
    // Fungsi untuk menampilkan error
    function showFormError(message) {
        // Cek apakah alert sudah ada
        let alert = document.querySelector('.alert-error');
        
        if (!alert) {
            // Buat alert baru
            alert = document.createElement('div');
            alert.className = 'alert alert-error';
            alert.innerHTML = `<i class="fas fa-exclamation-circle"></i><span>${message}</span>`;
            
            // Tambahkan alert ke awal card-body
            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(alert, cardBody.firstChild);
        } else {
            // Update pesan alert yang sudah ada
            alert.querySelector('span').textContent = message;
        }
        
        // Animasi efek shake untuk alert
        alert.style.animation = 'none';
        setTimeout(() => {
            alert.style.animation = 'shake 0.5s';
        }, 10);
    }
    
    // Animasi shake untuk alert
    const styleSheet = document.createElement('style');
    styleSheet.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    `;
    document.head.appendChild(styleSheet);
    
    // Input focus effect
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});