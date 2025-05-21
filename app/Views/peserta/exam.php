<!-- ujian_peserta.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian Online</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/ujian_peserta.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Add warning styles -->
    <style>
        .warning-banner {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            text-align: center;
        }
        
        .warning-banner i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <header class="exam-header">
            <div class="header-content">
                <h1><?= esc($exam['title']) ?></h1>
                <div class="exam-info">
                    <span class="duration"><i class="fas fa-clock"></i> Durasi: <?= esc($exam['duration']) ?> menit</span>
                    <div class="theme-switch-wrapper">
                        <label class="theme-switch" for="checkbox">
                            <input type="checkbox" id="checkbox" />
                            <div class="slider round">
                                <i class="fas fa-sun"></i>
                                <i class="fas fa-moon"></i>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="timer-container">
                <span id="timer" class="timer"></span>
            </div>
        </header>

        <!-- Add warning banner -->
        <div class="warning-banner">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Peringatan:</strong> Berpindah tab atau keluar dari halaman ujian akan dicatat dan dilaporkan ke admin. Mohon tetap fokus pada halaman ujian.
        </div>

        <div class="main-content">
            <div class="camera-container">
                <h2>Verifikasi Peserta</h2>
                <video id="cameraFeed" autoplay playsinline></video>
                <p class="camera-note">Kamera harus aktif selama ujian berlangsung.</p>
            </div>

            <div class="exam-container">
                <form action="<?= base_url('peserta/submitExam') ?>" method="post" id="examForm">
                    <input type="hidden" name="exam_id" value="<?= esc($exam['id']) ?>">
                    
                    <div class="questions-container">
                        <?php foreach ($questions as $index => $q): ?>
                            <div class="question-card">
                                <div class="question-number"><?= ($index + 1) ?></div>
                                <div class="question-content">
                                    <p class="question-text"><?= esc($q['question_text']) ?></p>
                                    <div class="options">
                                        <div class="option">
                                            <input type="radio" id="q<?= esc($q['id']) ?>_a" name="answers[<?= esc($q['id']) ?>]" value="A">
                                            <label for="q<?= esc($q['id']) ?>_a"><?= esc($q['option_a']) ?></label>
                                        </div>
                                        <div class="option">
                                            <input type="radio" id="q<?= esc($q['id']) ?>_b" name="answers[<?= esc($q['id']) ?>]" value="B">
                                            <label for="q<?= esc($q['id']) ?>_b"><?= esc($q['option_b']) ?></label>
                                        </div>
                                        <div class="option">
                                            <input type="radio" id="q<?= esc($q['id']) ?>_c" name="answers[<?= esc($q['id']) ?>]" value="C">
                                            <label for="q<?= esc($q['id']) ?>_c"><?= esc($q['option_c']) ?></label>
                                        </div>
                                        <div class="option">
                                            <input type="radio" id="q<?= esc($q['id']) ?>_d" name="answers[<?= esc($q['id']) ?>]" value="D">
                                            <label for="q<?= esc($q['id']) ?>_d"><?= esc($q['option_d']) ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="submit-container">
                        <button type="submit" id="submitBtn" class="submit-btn">
                            <i class="fas fa-check-circle"></i> Selesai Ujian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/ujian_peserta.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/js/exam_monitor.js') ?>"></script>
</body>
</html>