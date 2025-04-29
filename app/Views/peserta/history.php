<?php
// TARUH fungsi ini di atas sebelum <!DOCTYPE html>

if (!function_exists('getScoreClass')) {
    function getScoreClass($score)
    {
        if ($score >= 80)
            return 'excellent';
        if ($score >= 70)
            return 'good';
        if ($score >= 60)
            return 'average';
        return 'needs-improvement';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Ujian</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/history_peserta.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
</head>

<body class="light-mode">
    <div class="theme-toggle-wrapper">
        <button id="theme-toggle" class="theme-toggle">
            <i class="bi bi-moon-fill"></i>
            <i class="bi bi-sun-fill"></i>
        </button>
    </div>

    <div class="container main-container">
        <div class="header-area d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title"><i class="bi bi-clock-history me-2"></i>Riwayat Ujian Kamu</h2>

            <div class="actions-area d-flex gap-2">
                <a href="<?= base_url('/peserta/dashboard') ?>" class="btn btn-back">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
                <?php if (!empty($results)): ?>
                    <a href="<?= base_url('peserta/downloadHistoryPdf') ?>" class="btn btn-download" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
                    </a>
                <?php endif; ?>

            </div>
        </div>

        <div class="card-container">
            <?php if (empty($results)): ?>
                <div class="empty-state text-center py-5">
                    <i class="bi bi-journal-x display-1"></i>
                    <p class="mt-3 fs-5">Belum ada ujian yang dikerjakan.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table custom-table" id="exam-history-table">
                        <thead class="table-light">
                            <tr>
                                <th><i class="bi bi-journal-text"></i> Judul Ujian</th>
                                <th><i class="bi bi-graph-up"></i> Nilai</th>
                                <th><i class="bi bi-calendar-date"></i> Tanggal Pengerjaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $res): ?>
                                <tr>
                                    <td><?= esc($res['exam_title']) ?></td>
                                    <td>
                                        <div class="score-badge <?= esc(getScoreClass($res['score'])) ?>">
                                            <?= esc($res['score']) ?>
                                        </div>
                                    </td>
                                    <td><?= date('d M Y - H:i', strtotime($res['taken_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?= base_url('assets/js/history_peserta.js') ?>"></script>
</body>

</html>