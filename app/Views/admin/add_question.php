<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Soal</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/add_question.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="theme-toggle">
        <input type="checkbox" id="darkmode-toggle">
        <label for="darkmode-toggle">
            <i class="fas fa-sun"></i>
            <i class="fas fa-moon"></i>
        </label>
    </div>

    <div class="container">
        <h1>Tambah Soal</h1>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <p><?= session()->getFlashdata('error'); ?></p>
            </div>
        <?php endif; ?>

        <div class="card">
            <form action="<?= base_url('admin/store-question'); ?>" method="post">
                <input type="hidden" name="exam_id" value="<?= esc($exam_id) ?>">

                <div class="form-group">
                    <label for="question_text">Pertanyaan:</label>
                    <textarea id="question_text" name="question_text" required></textarea>
                </div>

                <div class="options-grid">
                    <div class="form-group">
                        <label for="option_a">Pilihan A:</label>
                        <input type="text" id="option_a" name="option_a" required>
                    </div>

                    <div class="form-group">
                        <label for="option_b">Pilihan B:</label>
                        <input type="text" id="option_b" name="option_b" required>
                    </div>

                    <div class="form-group">
                        <label for="option_c">Pilihan C:</label>
                        <input type="text" id="option_c" name="option_c" required>
                    </div>

                    <div class="form-group">
                        <label for="option_d">Pilihan D:</label>
                        <input type="text" id="option_d" name="option_d" required>
                    </div>
                </div>

                <div class="form-group correct-answer">
                    <label for="correct_option">Jawaban Benar:</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="correct_a" name="correct_option" value="A" required>
                            <label for="correct_a">A</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="correct_b" name="correct_option" value="B">
                            <label for="correct_b">B</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="correct_c" name="correct_option" value="C">
                            <label for="correct_c">C</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="correct_d" name="correct_option" value="D">
                            <label for="correct_d">D</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>

        <div class="card import-section">
            <h2>Import Soal dari Excel</h2>
            <form action="<?= base_url('admin/importExcel') ?>" method="post" enctype="multipart/form-data" class="import-form">
                <input type="hidden" name="exam_id" value="<?= $exam_id ?>">
                <div class="file-upload">
                    <input type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls" required>
                    <label for="excel_file">
                        <i class="fas fa-file-excel"></i>
                        <span class="file-label">Pilih file Excel</span>
                    </label>
                    <span id="file-name">Tidak ada file yang dipilih</span>
                </div>
                <button type="submit" class="btn-secondary">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </form>
        </div>
    </div>

    <script src="<?= base_url('assets/js/add_question.js'); ?>"></script>
</body>

</html>