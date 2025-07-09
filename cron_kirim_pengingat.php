<?php
// Import class PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load file autoloader Composer
require 'vendor/autoload.php';
include 'includes/koneksi.php';

$today  = date('Y-m-d');
$target = date('Y-m-d', strtotime('+5 days'));

// Query yang disesuaikan dengan struktur database kosan.sql
$sql = "
SELECT p.nama, p.gmail, ks.tanggal_selesai
FROM kontraksewa ks
JOIN penghuni p ON ks.id_penghuni = p.id_penghuni
WHERE ks.status = 'aktif'
  AND DATE(ks.tanggal_selesai) BETWEEN '$today' AND '$target'
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Buat instance PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Konfigurasi SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'valorant270306@gmail.com'; // Ganti dengan email pengirim
            $mail->Password   = 'uats fidx fheq bsqh';     // Gunakan App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Pengirim dan Penerima
            $mail->setFrom('valorant270306@gmail.com', 'Manajemen Kosan');
            $mail->addAddress($row['gmail'], $row['nama']);

            // Konten Email
            $tgl = date('d/m/Y', strtotime($row['tanggal_selesai']));
            $mail->isHTML(false);
            $mail->Subject = "⏰ Pengingat: Masa Kos Anda Habis dalam 5 Hari";
            $mail->Body = "Halo {$row['nama']},\n\nKontrak kos Anda akan berakhir pada tanggal $tgl.\nSegera lakukan pembayaran untuk bulan berikutnya atau perpanjang kontrak Anda.\n\nSalam,\nManajemen Kosan";

            // Kirim email
            if ($mail->send()) {
                // Log keberhasilan pengiriman
                file_put_contents('email_log.txt', date('Y-m-d H:i:s') . " - Email terkirim ke {$row['nama']} ({$row['gmail']})\n", FILE_APPEND);
                echo "✅ Email berhasil dikirim ke {$row['nama']} ({$row['gmail']})<br>";
            }
        } catch (Exception $e) {
            // Log error
            file_put_contents('email_error_log.txt', date('Y-m-d H:i:s') . " - Gagal mengirim ke {$row['gmail']}: {$mail->ErrorInfo}\n", FILE_APPEND);
            echo "❌ Gagal mengirim email ke {$row['nama']} ({$row['gmail']}): {$mail->ErrorInfo}<br>";
        }
    }
} else {
    // Log ketika tidak ada pengingat
    file_put_contents('email_log.txt', date('Y-m-d H:i:s') . " - Tidak ada kontrak yang berakhir dalam 5 hari\n", FILE_APPEND);
    echo "⚠️ Tidak ada penghuni yang masa kontraknya habis dalam 5 hari.";
}

// Tutup koneksi database
$conn->close();
?>