<?php
// Selalu mulai session di awal untuk bisa mengakses dan menghancurkannya.
// Kita gunakan lagi pengecekan "pintar" kita.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Langkah 1: Kosongkan semua variabel di dalam array $_SESSION.
$_SESSION = array();

// Langkah 2: Hancurkan session.
// Ini akan menghapus semua data session dari server.
session_destroy();

// Langkah 3: Arahkan pengguna kembali ke halaman login.
// Kita kirim status agar bisa menampilkan pesan di halaman login.
header("Location: login.php?status=logout_success");
exit; // Wajib ada setelah header location untuk menghentikan eksekusi skrip.
?>