<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ujian</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/manage_exam.css') ?>">
</head>
<body>

<div class="container">
    <h2>Kelola Ujian</h2>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Durasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams as $exam) : ?>
                    <tr>
                        <td data-label="Judul"><?= esc($exam['title']) ?></td>
                        <td data-label="Deskripsi"><?= esc($exam['description']) ?></td>
                        <td data-label="Durasi"><?= esc($exam['duration']) ?> menit</td>
                        <td data-label="Aksi">
                            <div class="action-buttons">
                                <a href="<?= base_url('admin/edit_exam/' . $exam['id']) ?>" class="btn btn-warning">Edit</a>
                                <a href="<?= base_url('admin/delete_exam/' . $exam['id']) ?>" class="btn btn-danger" onclick="return confirm('Hapus ujian?')">Hapus</a>
                                <a href="<?= base_url('admin/add-question/' . $exam['id']); ?>" class="btn btn-success">Tambah Soal</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <a href="<?= base_url('admin/dashboard'); ?>">← Kembali ke Dashboard</a>
</div>

</body>
</html>
