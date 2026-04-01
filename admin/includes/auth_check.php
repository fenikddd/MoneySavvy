<?php
// File ini akan menjadi gerbang keamanan untuk semua halaman admin.

// Gunakan session start yang "pintar"
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek dua hal: 
// 1. Apakah pengguna sudah login (ada user_id di session)?
// 2. Apakah peran (role) pengguna adalah 'admin'?
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // Jika salah satu atau kedua kondisi tidak terpenuhi, "tendang" pengguna
    // ke halaman login di folder utama dengan pesan error.
    header("Location: ../login.php?error=Anda tidak memiliki hak akses.");
    exit();
}

// Jika lolos, skrip akan lanjut mengeksekusi sisa halaman.
?>