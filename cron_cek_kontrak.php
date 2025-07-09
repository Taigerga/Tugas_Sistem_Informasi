<?php
include 'includes/koneksi.php';

$today = date('Y-m-d');

// Ambil semua kontrak aktif yang sudah melewati tanggal selesai
$sql = "
SELECT ks.id_kontrak, ks.tanggal_selesai, ks.id_penghuni, ks.id_kamar, p.nama, p.gmail
FROM kontraksewa ks
JOIN penghuni p ON ks.id_penghuni = p.id_penghuni
WHERE ks.status = 'aktif'
  AND DATE(ks.tanggal_selesai) < '$today'
";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $tgl_selesai = $row['tanggal_selesai'];
    $tgl_batas = date('Y-m-d', strtotime($tgl_selesai . ' +14 days'));
    $id_kontrak = $row['id_kontrak'];
    $id_penghuni = $row['id_penghuni'];
    $id_kamar = $row['id_kamar'];
    $nama_penghuni = $row['nama'];
    $email_penghuni = $row['gmail'];

    // Cek apakah sudah ada pembayaran untuk bulan berikutnya
    $bulan_berikut = date('Y-m', strtotime($tgl_selesai . ' +1 month'));
    $cek_pembayaran = $conn->query("SELECT * FROM pembayaran 
                                   WHERE id_kontrak = $id_kontrak 
                                   AND bulan >= '$bulan_berikut' 
                                   AND status = 'lunas'");

    if ($cek_pembayaran->num_rows === 0 && $today > $tgl_batas) {
        $conn->begin_transaction();
        try {
            // 1. Update status kontrak menjadi nonaktif
            $conn->query("UPDATE kontraksewa SET status = 'nonaktif' WHERE id_kontrak = $id_kontrak");
            
            // 2. Update status kamar menjadi kosong
            $conn->query("UPDATE kamar SET status = 'kosong' WHERE id_kamar = $id_kamar");
            
            // 3. Kirim notifikasi email (opsional)
            // Anda bisa menambahkan kode pengiriman email di sini
            
            $conn->commit();
            
            // Log aksi
            $log_message = date('Y-m-d H:i:s') . " - Kontrak ID $id_kontrak (Penghuni: $nama_penghuni) dinonaktifkan\n";
            file_put_contents('kontrak_log.txt', $log_message, FILE_APPEND);
            
            echo "✅ Kontrak ID $id_kontrak dinonaktifkan (Penghuni: $nama_penghuni)\n";
        } catch (Exception $e) {
            $conn->rollback();
            
            // Log error
            $error_message = date('Y-m-d H:i:s') . " - Gagal menonaktifkan kontrak ID $id_kontrak: " . $e->getMessage() . "\n";
            file_put_contents('kontrak_error_log.txt', $error_message, FILE_APPEND);
            
            echo "❌ Gagal menonaktifkan kontrak ID $id_kontrak: " . $e->getMessage() . "\n";
        }
    }
}

// Tutup koneksi database
$conn->close();
?>