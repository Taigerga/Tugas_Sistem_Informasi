<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

function kirimEmail($tujuan, $subjek, $pesanHTML) {
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi server SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'valorant270306@gmail.com'; // Ganti dengan Gmail kamu
        $mail->Password   = 'uats fidx fheq bsqh'; // Gunakan App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Pengirim & penerima
        $mail->setFrom('valorant270306@gmail.com', 'Admin Kosan'); // GANTI
        $mail->addAddress($tujuan);

        // Konten
        $mail->isHTML(true);
        $mail->Subject = $subjek;
        $mail->Body    = $pesanHTML;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email gagal dikirim. Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
