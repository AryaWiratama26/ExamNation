<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Ujian</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/add_exam.css'); ?>">
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
        <div class="card">
            <div class="card-header">
                <h1>Tambah Ujian</h1>
                <p class="subtitle">Silakan lengkapi form di bawah ini untuk menambahkan ujian baru</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= session()->getFlashdata('error'); ?></span>
                </div>
            <?php endif; ?>

            <div class="card-body">
                <form action="<?= base_url('admin/store-exam'); ?>" method="post" id="examForm">
                    <div class="form-group">
                        <label for="title">Judul Ujian</label>
                        <input type="text" id="title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="duration">Durasi (menit)</label>
                        <input type="number" id="duration" name="duration" min="1" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="<?= base_url('admin/dashboard'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/add_exam.js'); ?>"></script>
</body>
</html>