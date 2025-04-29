<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ujian</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/edit_exam.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="theme-toggle">
            <input type="checkbox" id="theme-switch">
            <label for="theme-switch" class="toggle-label">
                <i class="fas fa-sun"></i>
                <i class="fas fa-moon"></i>
                <span class="toggle-ball"></span>
            </label>
        </div>

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-edit"></i> Edit Ujian</h2>
            </div>
            
            <div class="card-body">
                <?php if (session()->has('error')) : ?>
                    <div class="alert">
                        <i class="fas fa-exclamation-circle"></i> <?= session('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('admin/update_exam/' . $exams['id']) ?>" method="post">
                    <div class="form-group">
                        <label for="title"><i class="fas fa-heading"></i> Judul:</label>
                        <input type="text" id="title" name="title" value="<?= esc($exams['title']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description"><i class="fas fa-align-left"></i> Deskripsi:</label>
                        <textarea id="description" name="description" required><?= esc($exams['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="duration"><i class="fas fa-clock"></i> Durasi (menit):</label>
                        <input type="number" id="duration" name="duration" value="<?= esc($exams['duration']) ?>" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="<?= base_url('admin/exams') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="<?= base_url('assets/js/edit_exam.js') ?>"></script>
</body>
</html>