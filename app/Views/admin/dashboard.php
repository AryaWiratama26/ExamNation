<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ujian</title>
    <!-- CSS Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin_dashboard.css'); ?>">
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-speedometer2 me-2"></i> Admin Ujian
        </div>
        <nav class="sidebar-nav">
            <a href="<?= base_url('admin/manage-exam'); ?>" class="active">
                <i class="bi bi-pencil-square"></i> Kelola Ujian
            </a>
            <a href="<?= base_url('admin/manage-users'); ?>">
                <i class="bi bi-people"></i> Kelola Pengguna
            </a>
            <a href="<?= base_url('admin/add-exam'); ?>">
                <i class="bi bi-plus-circle"></i> Tambah Ujian
            </a>
            <a href="#statistik">
                <i class="bi bi-bar-chart"></i> Statistik Ujian
            </a>
            <a href="#rekap">
                <i class="bi bi-file-earmark-spreadsheet"></i> Rekap Hasil
            </a>
        </nav>
        <button onclick="toggleDarkMode()" class="btn btn-toggle-theme">
            <span id="theme-icon">🌙</span> <span id="theme-text">Mode Gelap</span>
        </button>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <button id="sidebarToggle" class="btn btn-sm btn-link d-lg-none me-2 toggleSidebar"
                    style="display: none;">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <span class="navbar-brand">Dashboard Ujian</span>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-bell me-1"></i>
                                <span class="badge bg-danger">3</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#">Ujian Baru Ditambahkan</a></li>
                                <li><a class="dropdown-item" href="#">5 Siswa Selesai Ujian</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">Lihat Semua Notifikasi</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> Admin
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?= base_url('admin/profile'); ?>">Profil</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('admin/settings'); ?>">Pengaturan</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= base_url('logout'); ?>">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="exam-header animate-fade-in">
            <h1>Selamat Datang, Admin!</h1>
            <p class="lead">Kelola ujian, pengguna, dan lihat statistik dengan mudah.</p>
        </div>

        <!-- Progress Stats -->
        <div class="progress-bar-stats mb-4">
            <div class="progress-stat">
                <h5>Total Ujian</h5>
                <div class="value text-primary"><?= esc($total_exams) ?></div>
            </div>
            <div class="progress-stat">
                <h5>Ujian Aktif</h5>
                <div class="value text-success"><?= esc($active_exams) ?></div>
            </div>
            <div class="progress-stat">
                <h5>Total Pengguna</h5>
                <div class="value text-info"><?= esc($total_users) ?></div>
            </div>
            <div class="progress-stat">
                <h5>Nilai Rata-rata</h5>
                <div class="value text-warning"><?= esc($average_score) ?></div>
            </div>
        </div>

        <!-- Real-time Activity Feed -->
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-activity"></i> Aktivitas Terbaru</h5>
                        <span class="badge bg-primary">Real-time</span>
                    </div>
                    <div class="card-body">
                        <div class="activity-feed">
                            <?php
                            $resultModel = new \App\Models\ResultModel();
                            $userModel = new \App\Models\UserModel();
                            $examModel = new \App\Models\ExamModel();
                            
                            // Get latest 5 exam results
                            $latestResults = $resultModel->orderBy('taken_at', 'DESC')
                                                       ->limit(5)
                                                       ->findAll();
                            
                            if (empty($latestResults)): ?>
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-info-circle"></i> Belum ada aktivitas ujian
                                </div>
                            <?php else:
                                foreach ($latestResults as $result):
                                    $user = $userModel->find($result['user_id']);
                                    $exam = $examModel->find($result['exam_id']);
                                    $timeAgo = time_elapsed_string($result['taken_at']);
                            ?>
                                <div class="activity-item">
                                    <div class="activity-icon bg-<?= getScoreColorClass($result['score']) ?>">
                                        <i class="bi bi-person-check"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="mb-1">
                                            <strong><?= esc($user['name']) ?></strong> menyelesaikan ujian 
                                            <strong><?= esc($exam['title']) ?></strong>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-<?= getScoreColorClass($result['score']) ?>">
                                                Nilai: <?= $result['score'] ?>
                                            </span>
                                            <small class="text-muted"><?= $timeAgo ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;
                            endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-trophy"></i> Top Performers</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $topPerformers = $resultModel->select('user_id, MAX(score) as highest_score')
                                                   ->groupBy('user_id')
                                                   ->orderBy('highest_score', 'DESC')
                                                   ->limit(3)
                                                   ->findAll();
                        
                        if (empty($topPerformers)): ?>
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-info-circle"></i> Belum ada data performa
                            </div>
                        <?php else:
                            foreach ($topPerformers as $index => $performer):
                                $user = $userModel->find($performer['user_id']);
                        ?>
                            <div class="top-performer-item">
                                <div class="rank-badge">#<?= $index + 1 ?></div>
                                <div class="performer-info">
                                    <h6 class="mb-1"><?= esc($user['name']) ?></h6>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-<?= getScoreColorClass($performer['highest_score']) ?>" 
                                             role="progressbar" 
                                             style="width: <?= $performer['highest_score'] ?>%">
                                        </div>
                                    </div>
                                    <small class="text-muted">Nilai: <?= $performer['highest_score'] ?></small>
                                </div>
                            </div>
                        <?php endforeach;
                        endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Violations Monitoring Section -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-exclamation-triangle"></i> Monitoring Pelanggaran Ujian
                        </h5>
                        <a href="<?= base_url('admin/violations') ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        <?php
                        $activityLogModel = new \App\Models\ExamActivityLogModel();
                        $recentViolations = $activityLogModel->getRecentViolations(10); // Limit to 10
                        
                        if (empty($recentViolations)): ?>
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-info-circle"></i> Belum ada pelanggaran tercatat
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="violationsTable">
                                    <thead>
                                        <tr>
                                            <th>Peserta</th>
                                            <th>Ujian</th>
                                            <th>Jenis Pelanggaran</th>
                                            <th>Waktu</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentViolations as $violation): 
                                            $warningLevel = strpos($violation['activity'], 'Maximum') !== false ? 'danger' : 'warning';
                                        ?>
                                            <tr>
                                                <td><?= esc($violation['user_name']) ?></td>
                                                <td><?= esc($violation['exam_title']) ?></td>
                                                <td><?= esc($violation['activity']) ?></td>
                                                <td><?= time_elapsed_string($violation['logged_at']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $warningLevel ?>">
                                                        <?= $warningLevel === 'danger' ? 'Serius' : 'Peringatan' ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if (count($recentViolations) >= 10): ?>
                                <div class="text-end mt-3">
                                    <a href="<?= base_url('admin/violations') ?>" class="btn btn-link">
                                        Lihat lebih banyak <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card feature-card animate-fade-in">
                    <div class="card-body text-center">
                        <div class="card-icon">
                            <i class="bi bi-pencil-square fs-1 text-primary"></i>
                        </div>
                        <h5 class="card-title">Kelola Ujian</h5>
                        <p class="card-text">Edit atau hapus ujian yang tersedia.</p>
                        <a href="<?= base_url('admin/manage-exam'); ?>" class="btn btn-primary">Kelola</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card feature-card animate-fade-in">
                    <div class="card-body text-center">
                        <div class="card-icon">
                            <i class="bi bi-people fs-1 text-warning"></i>
                        </div>
                        <h5 class="card-title">Kelola Pengguna</h5>
                        <p class="card-text">Atur pengguna yang terdaftar.</p>
                        <a href="<?= base_url('admin/manage-users'); ?>" class="btn btn-warning">Kelola</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card feature-card animate-fade-in">
                    <div class="card-body text-center">
                        <div class="card-icon">
                            <i class="bi bi-plus-circle fs-1 text-success"></i>
                        </div>
                        <h5 class="card-title">Tambah Ujian</h5>
                        <p class="card-text">Buat ujian baru dan sesuaikan pengaturannya.</p>
                        <a href="<?= base_url('admin/add-exam'); ?>" class="btn btn-success">Tambah</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card shadow mb-4" id="statistik">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> Statistik Ujian Mingguan
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="ujianChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Rekap Section -->
        <div id="rekap">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><i class="bi bi-file-earmark-spreadsheet me-2"></i>Rekap Hasil Ujian dari Google Spreadsheet</h4>
                <a href="https://docs.google.com/spreadsheets/d/1W6LodJZ8FCgQfrSDJZ8WT3KYyFbRQ3C-EVHKaIX3ZAE/export?format=xlsx"
                    class="btn btn-outline-success" target="_blank">
                    <i class="bi bi-file-earmark-excel me-2"></i>Download Excel
                </a>
            </div>

            <?php
            // Default kalau rows kosong
            if (!isset($rows) || !is_array($rows)) {
                $rows = [];
            }

            $rows_per_page = 5;
            $total_pages = (count($rows) > 1) ? ceil((count($rows) - 1) / $rows_per_page) : 1;
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $page = max(1, min($page, $total_pages));
            $start_index = ($page - 1) * $rows_per_page + 1;
            $end_index = min($start_index + $rows_per_page - 1, count($rows) - 1);
            ?>


            <?php if (empty($rows) || count($rows) <= 1): ?>
                <div class="alert alert-info">Belum ada data yang masuk ke spreadsheet.</div>
            <?php else: ?>
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Judul Ujian</th>
                                        <th>Nilai</th>
                                        <th>Tanggal Mengerjakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = $start_index; $i <= $end_index; $i++): ?>
                                        <tr>
                                            <td><?= esc($rows[$i][0] ?? '-') ?></td>
                                            <td><?= esc($rows[$i][1] ?? '-') ?></td>
                                            <td><?= esc($rows[$i][2] ?? '-') ?></td>
                                            <td><?= esc($rows[$i][3] ?? '-') ?></td>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Navigasi halaman -->
                        <?php if ($total_pages > 1): ?>
                            <div class="mt-4 d-flex justify-content-center">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link pagination-link" data-page="<?= $page - 1 ?>" href="javascript:void(0);" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                                            <?php $active = ($p == $page) ? 'active' : ''; ?>
                                            <li class="page-item <?= $active ?>">
                                                <a class="page-link pagination-link" data-page="<?= $p ?>" href="javascript:void(0);"><?= $p ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link pagination-link" data-page="<?= $page + 1 ?>" href="javascript:void(0);" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <footer class="mt-5 text-center text-muted">
            <p>© 2025 Admin Dashboard Ujian</p>
        </footer>
    </div>
    <!-- Script JS -->
    <script src="<?= base_url('assets/js/chart.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Add AJAX pagination functionality
    document.addEventListener('DOMContentLoaded', function() {
        const paginationLinks = document.querySelectorAll('.pagination-link');
        
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                
                // AJAX request
                fetch(`<?= base_url('admin/dashboard') ?>?page=${page}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Create a temporary element to parse the HTML
                    const tempElement = document.createElement('div');
                    tempElement.innerHTML = html;
                    
                    // Extract the table content
                    const newTableContent = tempElement.querySelector('.table-responsive');
                    const newPagination = tempElement.querySelector('.mt-4.d-flex.justify-content-center');
                    
                    // Update just the table and pagination
                    if (newTableContent) {
                        document.querySelector('.table-responsive').innerHTML = newTableContent.innerHTML;
                    }
                    
                    if (newPagination) {
                        document.querySelector('.mt-4.d-flex.justify-content-center').innerHTML = newPagination.innerHTML;
                        
                        // Reattach event listeners to new pagination links
                        const newLinks = document.querySelectorAll('.pagination-link');
                        newLinks.forEach(newLink => {
                            newLink.addEventListener('click', arguments.callee);
                        });
                    }
                    
                    // Update URL without page refresh
                    history.pushState({}, '', `?page=${page}`);
                })
                .catch(error => console.error('Error loading page:', error));
            });
        });
    });
    </script>
</body>

</html>

<style>
/* Activity Feed Styles */
.activity-feed {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: start;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: white;
}

.activity-content {
    flex: 1;
}

/* Top Performers Styles */
.top-performer-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.top-performer-item:last-child {
    border-bottom: none;
}

.rank-badge {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 15px;
}

.performer-info {
    flex: 1;
}

/* Score Color Classes */
.bg-excellent { background-color: #10B981; }
.bg-good { background-color: #3B82F6; }
.bg-average { background-color: #F59E0B; }
.bg-poor { background-color: #EF4444; }
</style>

<script>
// Helper function to format time elapsed
function time_elapsed_string(datetime) {
    const now = new Date();
    const past = new Date(datetime);
    const diff = Math.floor((now - past) / 1000);

    if (diff < 60) return 'Baru saja';
    if (diff < 3600) return Math.floor(diff / 60) + ' menit yang lalu';
    if (diff < 86400) return Math.floor(diff / 3600) + ' jam yang lalu';
    if (diff < 604800) return Math.floor(diff / 86400) + ' hari yang lalu';
    return Math.floor(diff / 604800) + ' minggu yang lalu';
}

// Helper function to get score color class
function getScoreColorClass(score) {
    if (score >= 80) return 'excellent';
    if (score >= 70) return 'good';
    if (score >= 60) return 'average';
    return 'poor';
}
</script>