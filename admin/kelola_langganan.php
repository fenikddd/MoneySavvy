<?php
// 1. Panggil penjaga keamanan dan koneksi database
require_once 'includes/auth_check.php';
require_once '../includes/db_connect.php';

// --- BAGIAN LOGIKA ---

// 2. Logika untuk UPDATE status langganan (Approve/Reject)
if (isset($_GET['action']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $action = $_GET['action'];
    $subscription_id = (int)$_GET['id'];
    $new_status = '';

    if ($action == 'approve') {
        $new_status = 'approved';
    } elseif ($action == 'reject') {
        $new_status = 'rejected';
    }

    if (!empty($new_status)) {
        $sql_update = "UPDATE subscriptions SET status = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql_update)) {
            $stmt->bind_param("si", $new_status, $subscription_id);
            $stmt->execute();
            $stmt->close();
            // Redirect untuk refresh halaman dan membersihkan parameter URL
            header("Location: kelola_langganan.php?status=update_success");
            exit();
        }
    }
}


// 3. Logika untuk MENGAMBIL data langganan
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; // Default filter
$allowed_filters = ['all', 'pending', 'approved', 'rejected'];

// Query dasar
$sql = "
    SELECT 
        s.id, s.paket, s.harga, s.bukti_pembayaran, s.status, s.tanggal_langganan, 
        u.nama_lengkap 
    FROM subscriptions s 
    JOIN users u ON s.user_id = u.id
";

// Tambahkan filter jika dipilih
if (in_array($filter, $allowed_filters) && $filter != 'all') {
    $sql .= " WHERE s.status = ?";
}

// Urutkan berdasarkan status (pending paling atas), lalu tanggal terbaru
$sql .= " ORDER BY FIELD(s.status, 'pending', 'approved', 'rejected'), s.tanggal_langganan DESC";

$stmt = $conn->prepare($sql);
if (in_array($filter, $allowed_filters) && $filter != 'all') {
    $stmt->bind_param("s", $filter);
}
$stmt->execute();
$result = $stmt->get_result();

// Set judul halaman & panggil layout
$page_title = 'Kelola Langganan';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<h1 class="mt-4 fw-bold">Kelola Langganan Pengguna</h1>
<p>Verifikasi pembayaran dan aktifkan langganan pengguna di halaman ini.</p>

<?php if(isset($_GET['status']) && $_GET['status'] == 'update_success'): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    Status langganan berhasil diperbarui!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Daftar Langganan</h5>
        <ul class="nav nav-tabs card-header-tabs mt-2">
            <li class="nav-item"><a class="nav-link <?php if($filter == 'all') echo 'active'; ?>" href="?filter=all">Semua</a></li>
            <li class="nav-item"><a class="nav-link <?php if($filter == 'pending') echo 'active'; ?>" href="?filter=pending">Pending</a></li>
            <li class="nav-item"><a class="nav-link <?php if($filter == 'approved') echo 'active'; ?>" href="?filter=approved">Approved</a></li>
            <li class="nav-item"><a class="nav-link <?php if($filter == 'rejected') echo 'active'; ?>" href="?filter=rejected">Rejected</a></li>
        </ul>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>Nama Pengguna</th>
                        <th>Paket</th>
                        <th class="text-end">Harga</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Bukti Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $nomor = 1; ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $nomor++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($row['paket']); ?></td>
                                <td class="text-end">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo date("d M Y, H:i", strtotime($row['tanggal_langganan'])); ?></td>
                                <td>
                                    <?php
                                        $status = $row['status'];
                                        $badge_class = 'bg-secondary';
                                        if ($status == 'approved') $badge_class = 'bg-success';
                                        if ($status == 'pending') $badge_class = 'bg-warning text-dark';
                                        if ($status == 'rejected') $badge_class = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td>
                                    <a href="../uploads/payments/<?php echo htmlspecialchars($row['bukti_pembayaran']); ?>" target="_blank" class="btn btn-info btn-sm">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'pending'): ?>
                                        <a href="?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Anda yakin ingin menyetujui langganan ini?');" title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                        <a href="?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menolak langganan ini?');" title="Tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Tidak ada data langganan yang cocok dengan filter ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Panggil footer
require_once 'includes/footer.php';
$conn->close();
?>