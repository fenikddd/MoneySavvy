<?php
require_once 'includes/db_connect.php';

// Cek jika form disubmit dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil dan bersihkan data
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $subjek = trim($_POST['subjek']);
    $pesan = trim($_POST['pesan']);

    // Validasi sederhana
    if (!empty($nama) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($subjek) && !empty($pesan)) {
        
        // Simpan ke database menggunakan prepared statement
        $sql = "INSERT INTO feedback (nama, email, subjek, pesan) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $nama, $email, $subjek, $pesan);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Setelah memproses, arahkan kembali ke halaman utama ke bagian form saran
// dengan status sukses, terlepas dari apakah data valid atau tidak, untuk
// mencegah pengguna melihat halaman putih kosong.
header("Location: index.php?status=saran_terkirim#kritik-saran");
exit();

?>