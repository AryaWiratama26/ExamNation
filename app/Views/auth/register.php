<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="<?= base_url('/register/store') ?>" method="post">
        <input type="text" name="name" placeholder="Nama" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <select name="role">
            <option value="participant">Peserta</option>
            <option value="admin">Admin</option>
        </select><br>
        <button type="submit">Daftar</button>
    </form>
</body>
</html>
