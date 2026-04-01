<?php
// Panggil penjaga keamanan, koneksi, dan session
require_once 'includes/auth_check.php';
require_once '../includes/db_connect.php'; // Path ../ karena kita di dalam folder admin

// --- BAGIAN LOGIKA ---

// Logika untuk menandai sebagai sudah dibaca
if (isset($_GET['action']) && $_GET['action'] == 'mark_read' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_to_mark = (int)$_GET['id'];
    
    // Gunakan prepared statement untuk keamanan
    $sql_update = "UPDATE feedback SET status = 'dibaca' WHERE id = ?";
    if ($stmt_update = $conn->prepare($sql_update)) {
        $stmt_update->bind_param("i", $id_to_mark);
        $stmt_update->execute();
        $stmt_update->close();
    }
    
    // Redirect kembali ke halaman ini untuk menghilangkan parameter dari URL
    header("Location: kelola_saran.php");
    exit();
}

// Mengambil semua data feedback, yang baru paling atas
$sql = "SELECT id, nama, email, subjek, pesan, status, tanggal_kirim FROM feedback ORDER BY tanggal_kirim DESC";
$feedbacks = $conn->query($sql);

// Set judul halaman & panggil layout
$page_title = 'Kelola Masukan & Saran';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<h1 class="mt-4 fw-bold">Masukan & Saran Pengguna</h1>
<p>Daftar pesan yang dikirim oleh pengguna melalui form di halaman utama.</p>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Kotak Masuk</h5>
    </div>
    <div class="card-body p-0">
        <div class="accordion" id="feedbackAccordion">
            <?php if ($feedbacks && $feedbacks->num_rows > 0): ?>
                <?php while($fb = $feedbacks->fetch_assoc()): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-<?php echo $fb['id']; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $fb['id']; ?>">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <span class="fw-bold"><?php echo htmlspecialchars($fb['subjek']); ?></span>
                                    
                                    <div class="me-3 d-flex align-items-center">
                                        <span class="text-muted me-3 small"><?php echo htmlspecialchars($fb['nama']); ?></span>
                                        <?php if ($fb['status'] == 'baru'): ?>
                                            <span class="badge bg-success">Baru</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse-<?php echo $fb['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#feedbackAccordion">
                            <div class="accordion-body">
                                <p>
                                    <strong>Dari:</strong> <?php echo htmlspecialchars($fb['nama']); ?> 
                                    (<code>&lt;<?php echo htmlspecialchars($fb['email']); ?>&gt;</code>)
                                </p>
                                <p><strong>Tanggal:</strong> <?php echo date("d F Y, H:i", strtotime($fb['tanggal_kirim'])); ?></p>
                                <hr>
                                <p class="lh-lg">
                                    <?php echo nl2br(htmlspecialchars($fb['pesan'])); // nl2br untuk mengubah baris baru menjadi tag <br> ?>
                                </p>
                                <hr>

                                <?php if ($fb['status'] == 'baru'): ?>
                                    <a href="kelola_saran.php?action=mark_read&id=<?php echo $fb['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-check-lg"></i> Tandai sudah dibaca
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted"><i class="bi bi-check-all"></i> Sudah dibaca</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center p-5 text-muted">
                    <p>Belum ada masukan atau saran dari pengguna.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Panggil footer
require_once 'includes/footer.php';
$conn->close();
?>