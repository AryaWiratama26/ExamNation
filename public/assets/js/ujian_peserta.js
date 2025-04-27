// ujian_peserta.js
document.addEventListener('DOMContentLoaded', function() {
    // Camera setup
    const video = document.getElementById('cameraFeed');
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert("Browser kamu tidak mendukung akses kamera.");
        return;
    }

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
            video.srcObject = stream;
        })
        .catch(function(error) {
            console.error("Gagal membuka kamera:", error);
            alert("Tidak bisa membuka kamera: " + error.message);
        });

    // Timer setup
    const urlParams = new URLSearchParams(window.location.search);
    const duration = parseInt(document.querySelector('.duration').textContent.match(/\d+/)[0]);
    let durationInSeconds = duration * 60;
    let formSubmitted = false;

    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return minutes + "m " + (secs < 10 ? "0" : "") + secs + "s";
    }

    function updateTimer() {
        if (durationInSeconds <= 0 && !formSubmitted) {
            clearInterval(timerInterval);
            document.getElementById('examForm').submit();
            formSubmitted = true;
        } else if (durationInSeconds > 0) {
            document.getElementById('timer').textContent = "Waktu tersisa: " + formatTime(durationInSeconds);
            durationInSeconds--;
            
            // Add visual indication when time is running out
            if (durationInSeconds < 300) { // less than 5 minutes
                document.getElementById('timer').classList.add('time-warning');
            }
        }
    }

    updateTimer(); // Initialize timer display
    const timerInterval = setInterval(updateTimer, 1000);

    // Form submission validation
    document.getElementById('examForm').addEventListener('submit', function(e) {
        const totalQuestions = document.querySelectorAll('.question-card').length;
        const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
        
        if (answeredQuestions === 0) {
            e.preventDefault();
            alert('Anda harus memilih jawaban untuk minimal satu soal.');
        } else if (answeredQuestions < totalQuestions) {
            const confirmSubmit = confirm(`Anda baru menjawab ${answeredQuestions} dari ${totalQuestions} soal. Yakin ingin menyelesaikan ujian?`);
            if (!confirmSubmit) {
                e.preventDefault();
            } else {
                formSubmitted = true;
            }
        } else {
            formSubmitted = true;
        }
    });

    // Dark mode toggle
    const toggleSwitch = document.querySelector('#checkbox');
    const currentTheme = localStorage.getItem('theme');

    if (currentTheme) {
        document.documentElement.setAttribute('data-theme', currentTheme);
        if (currentTheme === 'dark') {
            toggleSwitch.checked = true;
        }
    }

    function switchTheme(e) {
        if (e.target.checked) {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
        }
    }

    toggleSwitch.addEventListener('change', switchTheme);

    // Radio button labels - make the whole option clickable
    const options = document.querySelectorAll('.option');
    options.forEach(option => {
        option.addEventListener('click', function(e) {
            if (e.target !== this.querySelector('input')) {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
            }
        });
    });

    // Animation for question cards
    const questionCards = document.querySelectorAll('.question-card');
    questionCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 * (index + 1));
    });
});