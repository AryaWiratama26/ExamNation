document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('darkmode-toggle');
    
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-theme');
        darkModeToggle.checked = true;
    }

    darkModeToggle.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('dark-theme');
            localStorage.setItem('darkMode', 'enabled');
        } else {
            document.body.classList.remove('dark-theme');
            localStorage.setItem('darkMode', null);
        }
    });

    const toggleSidebarBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');

    toggleSidebarBtn.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('active');
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });

    const closeSidebarBtn = document.getElementById('closeSidebar');
    
    closeSidebarBtn.addEventListener('click', function() {
        sidebar.classList.remove('active');
    });

    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768 && 
            !sidebar.contains(event.target) && 
            !toggleSidebarBtn.contains(event.target) && 
            sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });

    function adjustSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        }
    }

    window.addEventListener('load', adjustSidebar);
    window.addEventListener('resize', adjustSidebar);

    const userDropdownBtn = document.querySelector('.user-dropdown-btn');
    const userDropdown = document.querySelector('.user-dropdown');
    
    if (userDropdownBtn) {
        userDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });
        
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
    }

    const examSearch = document.getElementById('examSearch');
    if (examSearch) {
        examSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.exam-table tbody tr');
            
            rows.forEach(row => {
                const title = row.querySelector('td:first-child').textContent.toLowerCase();
                if (title.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    const startExamButtons = document.querySelectorAll('.start-exam-btn');
    const cameraModal = document.getElementById('cameraModal');
    const cameraPreview = document.getElementById('cameraPreview');
    const cancelExamBtn = document.getElementById('cancelExam');
    const confirmExamBtn = document.getElementById('confirmExam');
    const closeModalBtn = document.querySelector('.close-modal');
    
    let currentExamId = null;
    let stream = null;

    if (startExamButtons.length > 0) {
        startExamButtons.forEach(button => {
            button.addEventListener('click', function() {
                currentExamId = this.getAttribute('data-exam-id');
                openCameraModal();
            });
        });
    }

    function openCameraModal() {
        cameraModal.style.display = 'flex';
        initCamera();
    }

    async function initCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                    facingMode: "user" 
                } 
            });
            
            cameraPreview.srcObject = stream;
            
            confirmExamBtn.disabled = false;
        } catch (err) {
            console.error("Error accessing camera:", err);
            
            const cameraPreviewContainer = document.querySelector('.camera-preview');
            cameraPreviewContainer.innerHTML = `
                <div style="color: var(--danger); text-align: center;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <p>Tidak dapat mengakses kamera. Pastikan kamera terhubung dan izin akses diberikan.</p>
                </div>
            `;
            
            confirmExamBtn.disabled = true;
        }
    }

    function closeCameraModal() {
        cameraModal.style.display = 'none';
        
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        
        currentExamId = null;
    }

    if (cancelExamBtn) {
        cancelExamBtn.addEventListener('click', closeCameraModal);
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeCameraModal);
    }

    if (confirmExamBtn) {
        confirmExamBtn.addEventListener('click', function() {
            if (currentExamId) {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                
                window.location.href = `/peserta/exam/${currentExamId}`;
            }
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target === cameraModal) {
            closeCameraModal();
        }
    });

    if (cameraModal) {
        const modalContent = cameraModal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        }
    }

    const notificationBtn = document.querySelector('.notification-btn');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            alert('Notifikasi akan tersedia segera!');
        });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && cameraModal.style.display === 'flex') {
            closeCameraModal();
        }
    });

    function animateStats() {
        const statNumbers = document.querySelectorAll('.stat-details h3');
        statNumbers.forEach(number => {
            const targetNumber = parseInt(number.textContent);
            let currentNumber = 0;
            const duration = 1000; // 1 second
            const interval = 50; // update every 50ms
            const steps = duration / interval;
            const increment = targetNumber / steps;
            
            const counter = setInterval(() => {
                currentNumber += increment;
                if (currentNumber >= targetNumber) {
                    currentNumber = targetNumber;
                    clearInterval(counter);
                }
                number.textContent = Math.floor(currentNumber);
            }, interval);
        });
    }

    window.addEventListener('load', function() {
        if (document.querySelector('.stats-section')) {
            animateStats();
        }
    });

    window.addEventListener('popstate', function() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });

    document.addEventListener('visibilitychange', function() {
        if (document.hidden && stream) {
            cameraPreview.pause();
        } else if (cameraPreview.srcObject) {
            cameraPreview.play();
        }
    });

    function checkBrowserSupport() {
        const unsupportedFeatures = [];
        
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            unsupportedFeatures.push('Kamera');
        }
        
        if (!window.localStorage) {
            unsupportedFeatures.push('Local Storage');
        }
        
        if (unsupportedFeatures.length > 0) {
            console.warn('Browser Anda tidak mendukung fitur berikut: ' + unsupportedFeatures.join(', '));
        }
    }
    
    checkBrowserSupport();

});