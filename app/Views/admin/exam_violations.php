<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Pelanggaran Ujian - ExamNation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Header with Back Button -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-exclamation-triangle-fill text-warning"></i> 
                Monitoring Pelanggaran Ujian
            </h2>
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Ujian</h5>
                        <h2 class="mb-0"><?= count($exams) ?></h2>
                        <small>dengan pelanggaran tercatat</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Total Pelanggaran</h5>
                        <h2 class="mb-0"><?= array_sum(array_column($exams, 'violation_count')) ?></h2>
                        <small>dari semua ujian</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Pelanggaran Serius</h5>
                        <?php
                        $seriousCount = 0;
                        foreach ($exams as $exam) {
                            if ($exam['violation_count'] > 10) {
                                $seriousCount++;
                            }
                        }
                        ?>
                        <h2 class="mb-0"><?= $seriousCount ?></h2>
                        <small>ujian dengan >10 pelanggaran</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Violations Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Pelanggaran per Ujian</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="violationsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Judul Ujian</th>
                                <th>Dibuat Oleh</th>
                                <th>Total Pelanggaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exams as $exam): ?>
                            <tr>
                                <td><?= esc($exam['title']) ?></td>
                                <td><?= esc($exam['creator_name']) ?></td>
                                <td>
                                    <?php
                                    $violationClass = '';
                                    if ($exam['violation_count'] > 10) {
                                        $violationClass = 'danger';
                                    } elseif ($exam['violation_count'] > 5) {
                                        $violationClass = 'warning';
                                    } else {
                                        $violationClass = 'success';
                                    }
                                    ?>
                                    <span class="badge bg-<?= $violationClass ?>">
                                        <?= $exam['violation_count'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($exam['violation_count'] > 10): ?>
                                        <span class="badge bg-danger">Perlu Perhatian</span>
                                    <?php elseif ($exam['violation_count'] > 5): ?>
                                        <span class="badge bg-warning text-dark">Waspada</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Normal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('admin/violations/' . $exam['id']) ?>"
                                            class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <a href="<?= base_url('admin/exportViolations/' . $exam['id']) ?>"
                                            class="btn btn-sm btn-success">
                                            <i class="bi bi-download"></i> Export
                                        </a>
                                    </div>
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
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#violationsTable').DataTable({
                order: [[2, 'desc']], // Sort by violation count by default
                pageLength: 10, // Show 10 entries per page
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
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
        .btn-group .btn {
            margin-right: 5px;
        }
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