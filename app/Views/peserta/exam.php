<!-- Load CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/ujian_peserta.css') ?>">

<div class="container">
    <h2>Ujian: <?= esc($exam['title']) ?></h2>
    <p>Durasi: <?= esc($exam['duration']) ?> menit</p>
    <p id="timer"></p>

    <form action="<?= base_url('peserta/submitExam') ?>" method="post" id="examForm">
        <input type="hidden" name="exam_id" value="<?= esc($exam['id']) ?>">

        <?php foreach ($questions as $index => $q) : ?>
            <div class="question">
                <p><?= ($index + 1) . '. ' . esc($q['question_text']) ?></p>
                <label><input type="radio" name="answers[<?= esc($q['id']) ?>]" value="A"> <?= esc($q['option_a']) ?></label>
                <label><input type="radio" name="answers[<?= esc($q['id']) ?>]" value="B"> <?= esc($q['option_b']) ?></label>
                <label><input type="radio" name="answers[<?= esc($q['id']) ?>]" value="C"> <?= esc($q['option_c']) ?></label>
                <label><input type="radio" name="answers[<?= esc($q['id']) ?>]" value="D"> <?= esc($q['option_d']) ?></label>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn" id="submitBtn">Selesai</button>
    </form>
</div>

<!-- Timer Script -->
<script>
    var durationInSeconds = <?= esc($exam['duration']) ?> * 60;

    function formatTime(seconds) {
        var minutes = Math.floor(seconds / 60);
        var seconds = seconds % 60;
        return minutes + "m " + (seconds < 10 ? "0" : "") + seconds + "s";
    }

    function updateTimer() {
        if (durationInSeconds <= 0) {
            document.getElementById('examForm').submit();
        } else {
            document.getElementById('timer').textContent = "Waktu tersisa: " + formatTime(durationInSeconds);
            durationInSeconds--;
        }
    }

    var timerInterval = setInterval(updateTimer, 1000);

    document.getElementById('examForm').addEventListener('submit', function (e) {
        var answers = document.querySelectorAll('input[type="radio"]:checked');
        if (answers.length === 0) {
            alert('Anda harus memilih jawaban untuk soal yang telah diberikan.');
            e.preventDefault();
        }
    });
</script>
