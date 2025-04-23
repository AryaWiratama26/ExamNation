<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Peserta</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard_peserta.css') ?>">
</head>

<body>

    <div class="container">
        <h2>Dashboard Peserta</h2>
        <p>Selamat datang, <?= esc($session->get('user_name')) ?>!</p>

        <h3>Daftar Ujian</h3>
        <table class="exam-table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Durasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><?= esc($exam['title']) ?></td>
                        <td><?= esc($exam['duration']) ?> menit</td>
                        <td><a href="<?= base_url('peserta/exam/' . $exam['id']) ?>" class="btn">Mulai</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="button-history">
        <a href="/peserta/history" class="btn">📄 Lihat Riwayat Ujian</a>
    </div>

</body>

</html>