<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Soal</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/add_question.css'); ?>">
</head>
<body>
    <div class="container">
        <h1>Tambah Soal</h1>

        <?php if (session()->getFlashdata('error')) : ?>
            <p class="flash-error"><?= session()->getFlashdata('error'); ?></p>
        <?php endif; ?>

        <form action="<?= base_url('admin/store-question'); ?>" method="post">
            <input type="hidden" name="exam_id" value="<?= esc($exam_id) ?>">

            <label for="question_text">Pertanyaan:</label>
            <textarea id="question_text" name="question_text" required></textarea>

            <label for="option_a">Pilihan A:</label>
            <input type="text" id="option_a" name="option_a" required>

            <label for="option_b">Pilihan B:</label>
            <input type="text" id="option_b" name="option_b" required>

            <label for="option_c">Pilihan C:</label>
            <input type="text" id="option_c" name="option_c" required>

            <label for="option_d">Pilihan D:</label>
            <input type="text" id="option_d" name="option_d" required>

            <label for="correct_option">Jawaban Benar (A/B/C/D):</label>
            <input type="text" id="correct_option" name="correct_option" required>

            <button type="submit">Simpan</button>
        </form>

        <a href="<?= base_url('admin/manage-exam'); ?>">← Kembali ke Dashboard</a>
    </div>
</body>

</html>
