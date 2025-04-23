<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Ujian</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/history_peserta.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2><i class="bi bi-clock-history me-2"></i>Riwayat Ujian Kamu</h2>

        <a href="/peserta/dashboard" class="btn btn-outline-secondary mb-4 back-btn">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>

        <div class="card card-history p-4">
            <?php if (empty($results)): ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> Belum ada ujian yang dikerjakan.
                </div>
            <?php else: ?>
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>📘 Judul Ujian</th>
                            <th>📊 Nilai</th>
                            <th>🕒 Tanggal Pengerjaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $res): ?>
                            <tr>
                                <td><?= esc($res['exam_title']) ?></td>
                                <td>
                                    <span class="score-badge"><?= $res['score'] ?></span>
                                </td>
                                <td><?= date('d M Y - H:i', strtotime($res['taken_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
