<?php
session_start();
include 'includes/koneksi.php';

// Jika form login dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek di tabel pemilik
    $stmt_pemilik = $conn->prepare("SELECT * FROM pemilik WHERE username = ? AND password = ?");
    $stmt_pemilik->bind_param("ss", $username, $password);
    $stmt_pemilik->execute();
    $result_pemilik = $stmt_pemilik->get_result();

    if ($result_pemilik->num_rows == 1) {
        $data = $result_pemilik->fetch_assoc();
        $_SESSION['id_pemilik'] = $data['id_pemilik'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = 'pemilik';
        header("Location: dashboard_pemilik.php");
        exit;
    }

    // Cek di tabel penghuni
    $stmt_penghuni = $conn->prepare("SELECT * FROM penghuni WHERE username = ? AND password = ?");
    $stmt_penghuni->bind_param("ss", $username, $password);
    $stmt_penghuni->execute();
    $result_penghuni = $stmt_penghuni->get_result();

    if ($result_penghuni->num_rows == 1) {
        $data = $result_penghuni->fetch_assoc();
        $_SESSION['id_penghuni'] = $data['id_penghuni'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = 'penghuni';
        
        // Redirect berdasarkan status kontrak
        $id_penghuni = $data['id_penghuni'];
        $kontrak = $conn->query("SELECT status FROM kontraksewa WHERE id_penghuni = $id_penghuni ORDER BY id_kontrak DESC LIMIT 1");
        
        if ($kontrak->num_rows > 0 && $kontrak->fetch_assoc()['status'] == 'aktif') {
            header("Location: dashboard_penghuni.php");
        } else {
            header("Location: pending.php"); // Halaman untuk penghuni yang belum disetujui
        }
        exit;
    }

    // Gagal login
    $error = "Username atau password salah";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | KosanKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 0 auto;
            margin-top: 100px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-container">
        <h2 class="text-center mb-4">Login KosanKu</h2>
        
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required autocomplete="off" readonly
                            onfocus="this.removeAttribute('readonly')">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required autocomplete="off" readonly
                            onfocus="this.removeAttribute('readonly')">
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="mt-3 text-center">
            <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
            <p><a href="index.php">← Kembali ke Beranda</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Kosongkan field saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('input[name="username"]').value = '';
    document.querySelector('input[name="password"]').value = '';
    
});
</script>
</body>
</html>
