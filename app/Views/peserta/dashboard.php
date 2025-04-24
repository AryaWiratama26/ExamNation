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

        <video id="cameraFeed" width="1" height="1" autoplay playsinline style="display:none;"></video>

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
                        <td>
                            <button class="btn start-exam-btn" data-exam-id="<?= $exam['id'] ?>">
                                Mulai
                            </button>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="button-history">
        <a href="/peserta/history" class="btn">📄 Lihat Riwayat Ujian</a>
    </div>

    <script>
        const buttons = document.querySelectorAll('.start-exam-btn');
        const video = document.getElementById('cameraFeed');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const examId = this.getAttribute('data-exam-id');

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert("Browser kamu tidak mendukung kamera.");
                    return;
                }

                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function (stream) {
                        video.srcObject = stream;
                        // Redirect ke halaman ujian
                        window.location.href = `/peserta/exam/${examId}`;
                    })
                    .catch(function (error) {
                        alert("Kamu harus mengizinkan kamera untuk mulai ujian.");
                    });
            });
        });
    </script>

</body>

</html>