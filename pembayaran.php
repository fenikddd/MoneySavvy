<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db_connect.php';

// --- BAGIAN LOGIKA ---

// Wajib login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Silakan login untuk melanjutkan pembayaran.");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success_message = '';

// Data paket-paket yang kita tawarkan
$paket_details = [
    'basic' => ['nama' => 'Pemula Cerdas', 'harga' => 99000],
    'premium' => ['nama' => 'Investor Andal', 'harga' => 249000],
    'ultimate' => ['nama' => 'Sultan Finansial', 'harga' => 499000]
];

// Validasi paket yang dipilih dari URL
$paket_key = isset($_GET['paket']) ? $_GET['paket'] : '';
if (!array_key_exists($paket_key, $paket_details)) {
    header("Location: konsultasi.php"); // Jika paket tidak valid, kembalikan ke halaman konsultasi
    exit();
}

$paket_terpilih = $paket_details[$paket_key];

// Variabel untuk mengontrol tampilan form
$show_upload_form = false;

// Kondisi 1: User baru memilih metode pembayaran
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pilih_metode'])) {
    $show_upload_form = true;
}

// Kondisi 2: User meng-upload bukti pembayaran
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['konfirmasi_pembayaran'])) {
    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
        $target_dir = "uploads/payments/";
        $image_ext = strtolower(pathinfo($_FILES['bukti_pembayaran']['name'], PATHINFO_EXTENSION));
        $new_filename = "payment_" . $user_id . "_" . time() . "." . $image_ext;
        $target_file = $target_dir . $new_filename;

        // Validasi file
        $allowed_types = ['jpg', 'jpeg', 'png'];
        if (in_array($image_ext, $allowed_types) && $_FILES['bukti_pembayaran']['size'] <= 5000000) { // Maks 5MB
            if (move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $target_file)) {
                // Jika upload berhasil, simpan data ke tabel 'subscriptions'
                $sql = "INSERT INTO subscriptions (user_id, paket, harga, bukti_pembayaran) VALUES (?, ?, ?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("isds", $user_id, $paket_terpilih['nama'], $paket_terpilih['harga'], $new_filename);
                    if ($stmt->execute()) {
                        // Redirect ke profil dengan pesan sukses
                        header("Location: profil.php?status=payment_pending");
                        exit();
                    } else {
                        $errors[] = "Gagal menyimpan data langganan.";
                    }
                    $stmt->close();
                }
            } else {
                $errors[] = "Gagal mengupload file.";
            }
        } else {
            $errors[] = "Tipe file tidak valid (hanya JPG, PNG) atau ukuran file terlalu besar (Maks 5MB).";
        }
    } else {
        $errors[] = "Anda wajib mengunggah bukti pembayaran.";
    }
    // Jika ada error saat upload, tetap tampilkan form upload
    $show_upload_form = true; 
}


// Set judul halaman
$page_title = 'Pembayaran';
require_once 'includes/header.php';
?>

<main>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center">
                    <h1 class="fw-bold">Proses Pembayaran</h1>
                    <p class="fs-5 text-muted">Selesaikan langkah berikut untuk mengaktifkan langganan Anda.</p>
                </div>
                <hr class="my-4">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error) echo "- $error<br>"; ?>
                    </div>
                <?php endif; ?>


                <?php if ($show_upload_form): ?>
                    
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="card-title fw-bold">Langkah 1: Lakukan Transfer</h4>
                            <p>Silakan lakukan transfer sebesar <strong>Rp <?php echo number_format($paket_terpilih['harga'], 0, ',', '.'); ?></strong> ke rekening berikut:</p>
                            <div class="alert alert-info">
                                <h5><i class="bi bi-bank me-2"></i>Bank Central Asia (BCA)</h5>
                                <p class="mb-0">Nomor Rekening: <strong>123-456-7890</strong></p>
                                <p class="mb-0">Atas Nama: <strong>PT MoneySavvy Indonesia</strong></p>
                            </div>

                            <hr>

                            <h4 class="card-title fw-bold mt-4">Langkah 2: Unggah Bukti Pembayaran</h4>
                            <p>Setelah transfer berhasil, unggah bukti pembayaran Anda di bawah ini untuk kami verifikasi.</p>
                            <form action="pembayaran.php?paket=<?php echo $paket_key; ?>" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="bukti_pembayaran" class="form-label">File Bukti Pembayaran</label>
                                    <input class="form-control" type="file" id="bukti_pembayaran" name="bukti_pembayaran" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="konfirmasi_pembayaran" class="btn btn-primary btn-lg">Konfirmasi & Kirim Bukti</button>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php else: ?>

                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="card-title fw-bold">Ringkasan Pesanan</h4>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Paket Langganan
                                    <span class="fw-bold"><?php echo $paket_terpilih['nama']; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Harga
                                    <span class="fw-bold">Rp <?php echo number_format($paket_terpilih['harga'], 0, ',', '.'); ?></span>
                                </li>
                            </ul>
                            
                            <hr>

                            <h4 class="card-title fw-bold mt-4">Pilih Metode Pembayaran</h4>
                            <form action="pembayaran.php?paket=<?php echo $paket_key; ?>" method="POST">
                                <div class="form-check border rounded p-3">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="bank_transfer" value="bank_transfer" checked>
                                    <label class="form-check-label fw-bold" for="bank_transfer">
                                        <i class="bi bi-wallet2 me-2"></i>Bank Transfer
                                    </label>
                                    <p class="text-muted small mb-0 ms-4">Pembayaran melalui transfer ke rekening Bank BCA, Mandiri, atau BRI.</p>
                                </div>
                                <div class="d-grid mt-4">
                                    <button type="submit" name="pilih_metode" class="btn btn-primary btn-lg">Lanjutkan Pembayaran</button>
                                </div>
                            </form>
                        </div>
                    </div>
                
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';
$conn->close();
?> 