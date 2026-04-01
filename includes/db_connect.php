<?php
/*
    File ini bertanggung jawab untuk membuat koneksi ke database MySQL.
    Gunakan konstanta untuk kredensial agar mudah diubah jika diperlukan.
*/

// Definisikan kredensial database
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Default username untuk XAMPP
define('DB_PASSWORD', '');     // Default password untuk XAMPP adalah kosong
define('DB_NAME', 'db_moneysavvy');

// Mencoba untuk membuat koneksi ke database
try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    // Cek koneksi
    if ($conn->connect_error) {
        throw new Exception("ERROR: Koneksi gagal. " . $conn->connect_error);
    }
} catch (Exception $e) {
    // Hentikan eksekusi dan tampilkan pesan error jika koneksi gagal
    die($e->getMessage());
}
?>