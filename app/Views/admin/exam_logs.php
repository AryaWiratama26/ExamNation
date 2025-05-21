<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pelanggaran Ujian - ExamNation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Header with Back Button -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-exclamation-triangle-fill text-warning"></i> 
                Detail Pelanggaran Ujian
            </h2>
            <div>
                <a href="<?= base_url('admin/exportViolations/' . $exam['id']) ?>" class="btn btn-success me-2">
                    <i class="bi bi-download"></i> Export CSV
                </a>
                <a href="<?= base_url('admin/violations') ?>" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Exam Info Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="card-title"><?= esc($exam['title']) ?></h4>
                        <p class="text-muted mb-0"><?= esc($exam['description']) ?></p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="mb-2">
                            <span class="text-muted">Durasi:</span>
                            <strong><?= esc($exam['duration']) ?> menit</strong>
                        </div>
                        <div>
                            <span class="text-muted">Total Pelanggaran:</span>
                            <strong class="text-danger"><?= count($logs) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Violation Summary -->
        <?php
        $activityLogModel = new \App\Models\ExamActivityLogModel();
        $summary = $activityLogModel->getViolationSummary($exam['id']);
        ?>
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Pelanggaran</h5>
                        <h2 class="mb-0"><?= $summary->total_violations ?></h2>
                        <small>tercatat dalam ujian ini</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Pelanggaran Serius</h5>
                        <h2 class="mb-0"><?= $summary->serious_violations ?></h2>
                        <small>mencapai batas maksimal</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Peserta Melanggar</h5>
                        <h2 class="mb-0"><?= $summary->unique_violators ?></h2>
                        <small>peserta unik</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Violations Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Riwayat Pelanggaran</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="detailedLogsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Peserta</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i:s', strtotime($log['logged_at'])) ?></td>
                                <td><?= esc($log['user_name']) ?></td>
                                <td><?= esc($log['activity']) ?></td>
                                <td>
                                    <?php
                                    $warningLevel = strpos($log['activity'], 'Maximum') !== false ? 'danger' : 'warning';
                                    $warningText = $warningLevel === 'danger' ? 'Serius' : 'Peringatan';
                                    ?>
                                    <span class="badge bg-<?= $warningLevel ?>">
                                        <?= $warningText ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#detailedLogsTable').DataTable({
                order: [[0, 'desc']], // Sort by time by default
                pageLength: 10, // Show 10 entries per page
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        });
    </script>

    <style>
        .badge {
            font-size: 0.9em;
            padding: 0.5em 0.7em;
        }
        .card {
            transition: transform 0.2s;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 1rem;
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .btn {
            border-radius: 5px;
        }
    </style>
</body>

</html> 