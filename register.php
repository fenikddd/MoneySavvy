<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db_connect.php';

// --- BAGIAN LOGIKA & VALIDASI ---

// Inisialisasi variabel
$nama_lengkap = "";
$email = "";
$errors = []; // Menggunakan array untuk menampung semua pesan error

// Cek jika form sudah di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Ambil & bersihkan data input
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // 2. Lakukan Validasi
    if (empty($nama_lengkap)) {
        $errors[] = "Nama lengkap wajib diisi.";
    }
    if (empty($email)) {
        $errors[] = "Email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }
    if (empty($password)) {
        $errors[] = "Password wajib diisi.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password minimal harus 8 karakter.";
    }
    if ($password !== $password_confirm) {
        $errors[] = "Konfirmasi password tidak cocok.";
    }

    // 3. Jika tidak ada error validasi awal, cek duplikasi email di database
    if (empty($errors)) {
        $sql_check = "SELECT id FROM users WHERE email = ? LIMIT 1";
        
        if ($stmt_check = $conn->prepare($sql_check)) {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows == 1) {
                $errors[] = "Email ini sudah terdaftar. Silakan gunakan email lain atau login.";
            }
            $stmt_check->close();
        }
    }

    // 4. Jika semua validasi lolos (array $errors kosong), proses pendaftaran
    if (empty($errors)) {
        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql_insert = "INSERT INTO users (nama_lengkap, email, password) VALUES (?, ?, ?)";
        
        if ($stmt_insert = $conn->prepare($sql_insert)) {
            $stmt_insert->bind_param("sss", $nama_lengkap, $email, $hashed_password);
            
            if ($stmt_insert->execute()) {
                // Pendaftaran berhasil, arahkan ke halaman login dengan pesan sukses
                header("Location: login.php?status=reg_success");
                exit();
            } else {
                $errors[] = "Terjadi kesalahan pada server. Gagal mendaftar.";
            }
            $stmt_insert->close();
        }
    }
    // Tutup koneksi database
    $conn->close();
}

// Set judul halaman
$page_title = 'Registrasi';
// Panggil header
require_once 'includes/header.php';
?>

<main>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center fw-bold mb-4">Buat Akun Baru</h2>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php foreach ($errors as $error): ?>
                                    - <?php echo $error; ?><br>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form action="register.php" method="POST">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($nama_lengkap); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Alamat Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Minimal 8 karakter.</div>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Daftar</button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted">Sudah punya akun? <a href="login.php">Login di sini</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Panggil footer
require_once 'includes/footer.php'; 
?>