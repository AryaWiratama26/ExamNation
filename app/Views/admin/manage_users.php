<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/manage_user.css') ?>">
</head>
<body>

<div class="container">
    <h2>Kelola Pengguna</h2>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td data-label="Nama"><?= esc($user['name']) ?></td>
                        <td data-label="Email"><?= esc($user['email']) ?></td>
                        <td data-label="Role"><?= esc($user['role']) ?></td>
                        <td data-label="Aksi">
                            <a href="<?= base_url('admin/delete_user/' . $user['id']) ?>" 
                               class="btn-danger" 
                               onclick="return confirm('Hapus user?')">
                                Hapus
                            </a>
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
