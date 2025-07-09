<?php
// Konfigurasi koneksi database
$host     = "localhost";     // atau 127.0.0.1
$user     = "root";          // default XAMPP
$password = "";              // default XAMPP
$database = "kosan";      // sesuaikan nama databasenya

// Buat koneksi
$conn = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
