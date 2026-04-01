<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db_connect.php';

// Wajib login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Silakan login untuk melihat profil Anda.");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success_message = '';

// Logika UPDATE PROFIL (tetap sama)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profil'])) {
    if (!empty(trim($_POST['nama_lengkap']))) {
        $new_nama = trim($_POST['nama_lengkap']);
        $sql_update_name = "UPDATE users SET nama_lengkap = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql_update_name)) {
            $stmt->bind_param("si", $new_nama, $user_id);
            if ($stmt->execute()) { $_SESSION['user_name'] = $new_nama; }
            $stmt->close();
        }
    }
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $target_dir = "uploads/profiles/";
        $image_ext = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
        $new_filename = "user_" . $user_id . "_" . time() . "." . $image_ext;
        $target_file = $target_dir . $new_filename;
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($image_ext, $allowed_types) && $_FILES['foto_profil']['size'] <= 2000000) {
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file)) {
                $sql_update_photo = "UPDATE users SET foto_profil = ? WHERE id = ?";
                if ($stmt = $conn->prepare($sql_update_photo)) {
                    $stmt->bind_param("si", $new_filename, $user_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
    header("Location: profil.php?status=update_success");
    exit();
}

// Mengambil data user
$sql_user = "SELECT nama_lengkap, email, foto_profil FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$stmt_user->close();

// Mengambil data langganan TERAKHIR (apapun statusnya)
$latest_subscription = null;
$sql_subscription = "SELECT paket, status FROM subscriptions WHERE user_id = ? ORDER BY tanggal_langganan DESC LIMIT 1";
if($stmt_sub = $conn->prepare($sql_subscription)) {
    $stmt_sub->bind_param("i", $user_id);
    $stmt_sub->execute();
    $result_sub = $stmt_sub->get_result();
    if($result_sub->num_rows > 0) {
        $latest_subscription = $result_sub->fetch_assoc();
    }
    $stmt_sub->close();
}

// Definisikan link WhatsApp
$whatsapp_links = [
    'Pemula Cerdas' => 'https://chat.whatsapp.com/Kl0a93cQLtv22Ibj1CfPsp',
    'Investor Andal' => 'https://chat.whatsapp.com/Kl0a93cQLtv22Ibj1CfPsp',
    'Sultan Finansial' => 'https://chat.whatsapp.com/Kl0a93cQLtv22Ibj1CfPsp'
];
$link_grup_wa = '';
if ($latest_subscription && $latest_subscription['status'] == 'approved' && isset($whatsapp_links[$latest_subscription['paket']])) {
    $link_grup_wa = $whatsapp_links[$latest_subscription['paket']];
}

$page_title = 'Profil Saya';
require_once 'includes/header.php';

// Notifikasi untuk update profil
if (isset($_GET['status']) && $_GET['status'] == 'update_success') {
    $success_message = "Profil Anda berhasil diperbarui!";
}
// Path foto profil
$foto_profil_path = 'assets/images/default-avatar.png';
if (!empty($user['foto_profil']) && file_exists('uploads/profiles/' . $user['foto_profil'])) {
    $foto_profil_path = 'uploads/profiles/' . $user['foto_profil'];
}
?>

<main>
    <div class="container py-5">
        <h1 class="fw-bold mb-4">Profil Saya</h1>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($latest_subscription): ?>
            <?php if ($latest_subscription['status'] == 'pending'): ?>
                <div class="alert alert-info">
                    <h5 class="alert-heading"><i class="bi bi-clock-history"></i> Menunggu Persetujuan</h5>
                    <p>Terima kasih! Bukti pembayaran Anda untuk paket <strong><?php echo htmlspecialchars($latest_subscription['paket']); ?></strong> sedang kami verifikasi. Notifikasi ini akan berubah setelah disetujui oleh admin.</p>
                </div>
            <?php elseif ($latest_subscription['status'] == 'approved'): ?>
                <div class="alert alert-success">
                    <h5 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Langganan Aktif!</h5>
                    <p>Selamat, langganan paket <strong><?php echo htmlspecialchars($latest_subscription['paket']); ?></strong> Anda telah disetujui. Silakan bergabung ke grup WhatsApp eksklusif kami.</p>
                    <hr>
                    <a href="<?php echo $link_grup_wa; ?>" target="_blank" class="btn btn-light fw-bold">
                        <i class="bi bi-whatsapp"></i> Gabung Grup Sekarang
                    </a>
                </div>
            <?php elseif ($latest_subscription['status'] == 'rejected'): ?>
                 <div class="alert alert-danger">
                    <h5 class="alert-heading"><i class="bi bi-x-circle-fill"></i> Langganan Ditolak</h5>
                    <p>Maaf, pembayaran Anda untuk paket <strong><?php echo htmlspecialchars($latest_subscription['paket']); ?></strong> tidak dapat kami verifikasi. Silakan hubungi admin untuk informasi lebih lanjut.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="row mt-4">
            <div class="col-lg-4 mb-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body p-4">
                        <img src="<?php echo $foto_profil_path; ?>" alt="Foto Profil" class="rounded-circle mb-3" width="150" height="150" style="object-fit: cover;">
                        <h4 class="card-title"><?php echo htmlspecialchars($user['nama_lengkap']); ?></h4>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                 <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white"><h5 class="mb-0">Ubah Data Profil</h5></div>
                    <div class="card-body">
                        <form action="profil.php" method="POST" enctype="multipart/form-data">
                             <div class="mb-3"><label for="nama_lengkap" class="form-label">Nama Lengkap</label><input type="text" class="form-control" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>"></div>
                             <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled readonly><div class="form-text">Email tidak dapat diubah.</div></div>
                             <div class="mb-3"><label for="foto_profil" class="form-label">Ganti Foto Profil</label><input class="form-control" type="file" id="foto_profil" name="foto_profil"><div class="form-text">Format: JPG, PNG, GIF. Ukuran Maks: 2MB.</div></div>
                             <button type="submit" name="update_profil" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';
$conn->close();
?>