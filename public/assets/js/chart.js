document.addEventListener("DOMContentLoaded", function () {
    fetch("/admin/get-ujian-harian")
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('ujianChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                    datasets: [{
                        label: 'Jumlah Ujian',
                        data: data,
                        backgroundColor: function (context) {
                            const chart = context.chart;
                            const { ctx, chartArea } = chart;

                            if (!chartArea) return null; // Wait for chartArea to be available

                            const gradient = ctx.createLinearGradient(0, 0, 0, chartArea.bottom);
                            gradient.addColorStop(0, '#4facfe');  // biru muda
                            gradient.addColorStop(1, '#00f2fe');  // biru laut
                            return gradient;
                        },
                        borderRadius: 12,
                        barThickness: 40,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#333',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 8,
                            padding: 10
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                color: '#555'
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(200,200,200,0.2)',
                                borderDash: [5, 5]
                            },
                            ticks: {
                                beginAtZero: true,
                                precision: 0,
                                font: {
                                    size: 13
                                },
                                color: '#555'
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });
        });
});


window.toggleDarkMode = function() {
    const body = document.body;
    const themeIcon = document.getElementById('theme-icon');
    const themeText = document.getElementById('theme-text');

    body.classList.toggle('dark-mode');

    if (body.classList.contains('dark-mode')) {
        themeIcon.textContent = '☀️';
        themeText.textContent = 'Mode Terang';
        localStorage.setItem('darkMode', 'enabled');
    } else {
        themeIcon.textContent = '🌙';
        themeText.textContent = 'Mode Gelap';
        localStorage.setItem('darkMode', 'disabled');
    }
}

// Toggle sidebar on mobile
document.getElementById('sidebarToggle').addEventListener('click', function () {
    document.getElementById('sidebar').classList.toggle('show');
});

// Check for saved dark mode preference saat halaman dimuat
document.addEventListener('DOMContentLoaded', function () {
    const darkMode = localStorage.getItem('darkMode');
    const body = document.body;
    const themeIcon = document.getElementById('theme-icon');
    const themeText = document.getElementById('theme-text');

    if (darkMode === 'enabled') {
        body.classList.add('dark-mode');
        themeIcon.textContent = '☀️';
        themeText.textContent = 'Mode Terang';
    } else {
        body.classList.remove('dark-mode');
        themeIcon.textContent = '🌙';
        themeText.textContent = 'Mode Gelap';
    }
});
