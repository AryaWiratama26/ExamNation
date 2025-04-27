<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/manage_user.css') ?>">
</head>
<body>

<div class="container">
    <div class="dashboard-header">
        <h2 class="dashboard-title">Kelola Pengguna</h2>
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari pengguna...">
        </div>
    </div>
    
    <div class="card">
        <div class="table-responsive">
            <table id="userTable">
                <thead>
                    <tr>
                        <th data-sort="name">Nama <i class="fas fa-sort"></i></th>
                        <th data-sort="email">Email <i class="fas fa-sort"></i></th>
                        <th data-sort="role">Role <i class="fas fa-sort"></i></th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td data-label="Nama"><?= esc($user['name']) ?></td>
                            <td data-label="Email"><?= esc($user['email']) ?></td>
                            <td data-label="Role">
                                <span class="user-role <?= strtolower($user['role']) == 'admin' ? 'role-admin' : 'role-user' ?>">
                                    <?= esc($user['role']) ?>
                                </span>
                            </td>
                            <td data-label="Aksi">
                                <div class="action-buttons">
                                    <a href="javascript:void(0);" 
                                       class="btn btn-danger delete-btn" 
                                       data-id="<?= $user['id'] ?>"
                                       data-name="<?= esc($user['name']) ?>">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="pagination" id="pagination">
        </div>
    </div>
    
    <a href="<?= base_url('admin/dashboard'); ?>" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

<!-- Modal Konfirmasi -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Konfirmasi Hapus</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus pengguna <span id="userName"></span>?</p>
            <p class="warning"><i class="fas fa-exclamation-triangle"></i> Tindakan ini tidak dapat dibatalkan!</p>
        </div>
        <div class="modal-footer">
            <button id="cancelDelete" class="btn btn-outline">Batal</button>
            <a id="confirmDelete" href="#" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus
            </a>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/manage_user.js') ?>"></script>
</body>
</html>