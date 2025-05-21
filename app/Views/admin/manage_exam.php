<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ujian</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/manage_exam.css') ?>">
    <script defer src="<?= base_url('assets/js/manage_exam.js') ?>"></script>
</head>
<body>

<div class="container">
    <header class="page-header">
        <h2>Kelola Ujian</h2>
        <div class="header-actions">
            <a href="<?= base_url('admin/add-exam'); ?>" class="btn btn-primary">
                <i class="icon-plus"></i> Tambah Ujian Baru
            </a>
        </div>
    </header>

    <div class="search-container">
        <input type="text" id="searchInput" class="search-input" placeholder="Cari ujian...">
        <select id="filterDuration" class="filter-select">
            <option value="">Semua Durasi</option>
            <option value="30">30 menit</option>
            <option value="60">60 menit</option>
            <option value="90">90 menit</option>
            <option value="120">120 menit</option>
        </select>
    </div>

    <div class="table-container">
        <table class="table" id="examTable">
            <thead>
                <tr>
                    <th data-sort="title">Judul <i class="sort-icon"></i></th>
                    <th>Deskripsi</th>
                    <th data-sort="duration">Durasi <i class="sort-icon"></i></th>
                    <th>Status Ujian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams as $exam) : ?>
                    <tr>
                        <td data-label="Judul"><?= esc($exam['title']) ?></td>
                        <td data-label="Deskripsi"><?= esc($exam['description']) ?></td>
                        <td data-label="Durasi"><?= esc($exam['duration']) ?> menit</td>
                        <td data-label="Status">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       class="form-check-input status-toggle" 
                                       id="status_<?= $exam['id'] ?>" 
                                       data-exam-id="<?= $exam['id'] ?>"
                                       <?= ($exam['exam_stat'] ?? 'draft') === 'published' ? 'checked' : '' ?>>
                                <span class="status-badge <?= ($exam['exam_stat'] ?? 'draft') === 'published' ? 'published' : 'draft' ?>">
                                    <?= ($exam['exam_stat'] ?? 'draft') === 'published' ? 'Published' : 'Draft' ?>
                                </span>
                            </div>
                        </td>
                        <td data-label="Aksi">
                            <div class="action-buttons">
                                <a href="<?= base_url('admin/edit-exam/' . $exam['id']) ?>" class="btn btn-warning" title="Edit ujian">
                                    <i class="icon-edit"></i>
                                </a>
                                <a href="<?= base_url('admin/add-question/' . $exam['id']); ?>" class="btn btn-success" title="Tambah soal">
                                    <i class="icon-question"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-danger delete-exam" title="Hapus ujian" data-id="<?= $exam['id'] ?>">
                                    <i class="icon-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination-container" id="pagination">
        <!-- Pagination akan diisi oleh JavaScript -->
    </div>

    <div class="back-link">
        <a href="<?= base_url('admin/dashboard'); ?>" class="btn btn-outline">
            <i class="icon-back"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Modal konfirmasi hapus -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Konfirmasi Hapus</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <p>Anda yakin ingin menghapus ujian ini?</p>
        </div>
        <div class="modal-footer">
            <button id="cancelDelete" class="btn btn-outline">Batal</button>
            <button id="confirmDelete" class="btn btn-danger">Hapus</button>
        </div>
    </div>
</div>

</body>
</html>