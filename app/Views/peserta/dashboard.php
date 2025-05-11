<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Peserta | Examination</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard_peserta.css') ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="<?= base_url('assets/img/logo-3d-test2.png') ?>" alt="Logo">
                    <span>Examination</span>
                </div>
                <button class="close-sidebar" id="closeSidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="sidebar-content">
                <div class="user-profile-sidebar">
                    <div class="user-avatar">
                        <img src="<?= base_url('assets/img/avatar.jpeg') ?>" alt="User Avatar">
                    </div>
                    <div class="user-info">
                        <h4><?= esc($session->get('user_name')) ?></h4>
                        <span class="user-role">Peserta</span>
                    </div>
                </div>

                <nav class="sidebar-nav">
                    <ul>
                        <li class="active">
                            <a href="/peserta/dashboard">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="/peserta/history">
                                <i class="fas fa-history"></i>
                                <span>Riwayat Ujian</span>
                            </a>
                        </li>
                        <li class="logout">
                            <a href="/logout">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Keluar</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button class="toggle-sidebar" id="toggleSidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="page-title">
                        <h1>Dashboard</h1>
                    </div>
                </div>
                <div class="header-right">
                    <div class="theme-toggle">
                        <input type="checkbox" id="darkmode-toggle" />
                        <label for="darkmode-toggle">
                            <i class="fas fa-sun"></i>
                            <i class="fas fa-moon"></i>
                        </label>
                    </div>
                    <div class="notifications">
                        <button class="notification-btn">
                            <i class="fas fa-bell"></i>
                            <span class="badge">2</span>
                        </button>
                    </div>
                    <div class="user-dropdown">
                        <button class="user-dropdown-btn">
                            <img src="<?= base_url('assets/img/avatar.jpeg') ?>" alt="User Avatar">
                            <span class="user-name"><?= esc($session->get('user_name')) ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown-menu">
                            <a href="/logout">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="breadcrumb">
                    <a href="/peserta/dashboard"><i class="fas fa-home"></i></a>
                    <span>/</span>
                    <span>Dashboard</span>
                </div>

                <section class="welcome-section">
                    <div class="welcome-card">
                        <div class="welcome-text">
                            <h2>Selamat datang, <?= esc($session->get('user_name')) ?>!</h2>
                            <p>Siap untuk ujian hari ini? Berikut adalah daftar ujian yang tersedia untuk Anda.</p>
                        </div>
                        <div class="welcome-image">
                            <img src="<?= base_url('assets/img/logo-3d-test2.png') ?>" alt="Welcome">
                        </div>
                    </div>
                </section>

                <section class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?= count($exams) ?></h3>
                                <p>Ujian Tersedia</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?= $completedExams ?></h3>
                                <p>Ujian Selesai</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?= $highestScore ?></h3>
                                <p>Nilai Tertinggi</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="exam-list-section">
                    <div class="section-header">
                        <h2>Daftar Ujian</h2>
                        <div class="search-container">
                            <input type="text" id="examSearch" placeholder="Cari ujian...">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>

                    <div class="exam-table-container">
                        <table class="exam-table">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Durasi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($exams as $exam): ?>
                                    <tr>
                                        <td><?= esc($exam['title']) ?></td>
                                        <td><span class="duration-badge"><?= esc($exam['duration']) ?> menit</span></td>
                                        <td><span class="status-badge available">Tersedia</span></td>
                                        <td>
                                            <button class="btn start-exam-btn" data-exam-id="<?= $exam['id'] ?>">
                                                <i class="fas fa-play-circle"></i> Mulai Ujian
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="history-section">
                    <a href="/peserta/history" class="btn history-btn">
                        <i class="fas fa-history"></i> Lihat Riwayat Ujian
                    </a>
                </section>
            </div>

            <footer class="main-footer">
                <p>&copy; <?= date('Y') ?> Examination System. All rights reserved.</p>
            </footer>
        </main>
    </div>

    <!-- Hidden video element for camera check -->
    <video id="cameraFeed" width="1" height="1" autoplay playsinline style="display:none;"></video>

    <!-- Camera Modal -->
    <div class="modal" id="cameraModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Konfirmasi Kamera</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Untuk memulai ujian, Anda perlu mengizinkan akses kamera.</p>
                <div class="camera-preview">
                    <video id="cameraPreview" width="320" height="240" autoplay playsinline></video>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn cancel-btn" id="cancelExam">Batalkan</button>
                <button class="btn confirm-btn" id="confirmExam">Lanjutkan ke Ujian</button>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/dashboard_peserta.js') ?>"></script>
</body>

</html>