<!-- Load CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/ujian_peserta.css') ?>">

<div class="container">
    <h2>Ujian: <?= esc($exam['title']) ?></h2>
    <p>Durasi: <?= esc($exam['duration']) ?> menit</p>

    <video id="cameraFeed" width="320" height="240" autoplay playsinline
        style="border: 2px solid #ccc; border-radius: 10px; display: block; margin: 0 auto 10px auto;"></video>

    <p id="timer"></p>

    <form action="<?= base_url('peserta/submitExam') ?>" method="post" id="examForm">
        <input type="hidden" name="exam_id" value="<?= esc($exam['id']) ?>">

        <?php foreach ($questions as $index => $q): ?>
            <div class="question">
                <p><?= ($index + 1) . '. ' . esc($q['question_text']) ?></p>
                <label><input type="radio" name="answers[<?= esc($q['id']) ?>]" value="A">
                    <?= esc($q['option_a']) ?></label>
                <label><input type="radio" name="answers[<?= esc($q['id']) ?>]" value="B">
                    <?= esc($q['option_b']) ?></label>
                <label><input type="radio" name="answers[<?= esc($q['id']) ?>]" value="C">
                    <?= esc($q['option_c']) ?></label>
                <label><input type="radio" name="answers[<?= esc($q['id']) ?>]" value="D">
                    <?= esc($q['option_d']) ?></label>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn" id="submitBtn">Selesai</button>
    </form>
</div>

<!-- Timer Script -->
<script>
    
    window.onload = function () {
        const video = document.getElementById('cameraFeed');
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert("Browser kamu tidak mendukung akses kamera.");
            return;
        }

        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                video.srcObject = stream;
            })
            .catch(function (error) {
                console.error("Gagal membuka kamera:", error);
                alert("Tidak bisa membuka kamera: " + error.message);
            });
    };

    var durationInSeconds = <?= esc($exam['duration']) ?> * 60;
    var formSubmitted = false;

    function formatTime(seconds) {
        var minutes = Math.floor(seconds / 60);
        var seconds = seconds % 60;
        return minutes + "m " + (seconds < 10 ? "0" : "") + seconds + "s";
    }

    function updateTimer() {
        if (durationInSeconds <= 0 && !formSubmitted) {
            clearInterval(timerInterval);
            document.getElementById('examForm').submit();
            formSubmitted = true;
        } else if (durationInSeconds > 0) {
            document.getElementById('timer').textContent = "Waktu tersisa: " + formatTime(durationInSeconds);
            durationInSeconds--;
        }
    }

    var timerInterval = setInterval(updateTimer, 1000);

    document.getElementById('examForm').addEventListener('submit', function (e) {
        var answers = document.querySelectorAll('input[type="radio"]:checked');
        if (answers.length === 0) {
            alert('Anda harus memilih jawaban untuk setiap soal.');
            e.preventDefault();
        } else {
            formSubmitted = true;
        }
    });

</script>