<?php
// Gunakan session start yang "pintar" untuk mencegah notice
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Jika pengguna sudah login, arahkan ke halaman utama
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/db_connect.php';

// --- BAGIAN LOGIKA & VALIDASI ---

$email = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil data input
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 2. Validasi dasar
    if (empty($email) || empty($password)) {
        $errors[] = "Email dan password wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    } else {
        // 3. Jika validasi dasar lolos, verifikasi dengan database
        $sql = "SELECT id, nama_lengkap, password, role FROM users WHERE email = ? LIMIT 1";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            
            // Cek jika email ditemukan
            if ($stmt->num_rows == 1) {
                // Bind hasil query ke variabel
                $stmt->bind_result($id, $nama_lengkap, $hashed_password, $role);
                $stmt->fetch();
                
                // 4. Verifikasi password
                if (password_verify($password, $hashed_password)) {
                    // Password benar, login berhasil!
                    
                    // Regenerasi session ID untuk keamanan (mencegah session fixation)
                    session_regenerate_id(true);
                    
                    // Simpan data pengguna ke dalam session
                    $_SESSION['user_id'] = $id;
                    $_SESSION['user_name'] = $nama_lengkap;
                    $_SESSION['user_role'] = $role;
                    
                    // Arahkan ke halaman utama (atau dashboard)
                    header("Location: index.php");
                    exit();
                } else {
                    // Password salah
                    $errors[] = "Kombinasi email dan password salah.";
                }
            } else {
                // Email tidak ditemukan
                $errors[] = "Kombinasi email dan password salah.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}

// Set judul halaman
$page_title = 'Login';
// Panggil header
require_once 'includes/header.php';
?>

<main>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center fw-bold mb-4">Login Akun</h2>

                        <?php if (isset($_GET['status']) && $_GET['status'] == 'reg_success'): ?>
                            <div class="alert alert-success">
                                Registrasi berhasil! Silakan login dengan akun baru Anda.
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php echo $errors[0]; // Tampilkan hanya error pertama ?>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Alamat Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Login</button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
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