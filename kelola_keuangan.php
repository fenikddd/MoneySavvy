<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db_connect.php';

// --- BAGIAN LOGIKA & PEMROSESAN DATA ---

// Wajib login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Silakan login untuk mengakses halaman ini.");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];

// Cek jika form tunggal di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan_transaksi'])) {
    $tipe = $_POST['tipe_transaksi'];
    $deskripsi = trim($_POST['deskripsi']);
    $jumlah = filter_var($_POST['jumlah'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $tanggal = $_POST['tanggal'];

    if (empty($deskripsi) || empty($jumlah) || empty($tanggal) || $jumlah <= 0 || empty($tipe)) {
        $errors[] = "Semua field harus diisi dengan benar.";
    } else {
        if ($tipe == 'pemasukan') {
            $sql = "INSERT INTO incomes (user_id, deskripsi, jumlah, tanggal) VALUES (?, ?, ?, ?)";
        } else { // tipe == 'pengeluaran'
            $sql = "INSERT INTO expenses (user_id, deskripsi, jumlah, tanggal) VALUES (?, ?, ?, ?)";
        }

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("isds", $user_id, $deskripsi, $jumlah, $tanggal);
            if ($stmt->execute()) {
                header("Location: kelola_keuangan.php"); exit();
            } else { $errors[] = "Gagal menyimpan transaksi."; }
            $stmt->close();
        }
    }
}

// --- Mengambil Data untuk Ditampilkan ---

// Ambil gabungan pemasukan dan pengeluaran dengan UNION ALL, diurutkan berdasarkan tanggal
$sql_transactions = "
    (SELECT id, tanggal, deskripsi, jumlah, 'pemasukan' as tipe FROM incomes WHERE user_id = ?)
    UNION ALL
    (SELECT id, tanggal, deskripsi, jumlah, 'pengeluaran' as tipe FROM expenses WHERE user_id = ?)
    ORDER BY tanggal DESC, id DESC
";
$stmt = $conn->prepare($sql_transactions);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$transactions_result = $stmt->get_result();
$transactions = $transactions_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// Hitung total (query terpisah lebih efisien untuk SUM)
$total_pemasukan = $conn->query("SELECT SUM(jumlah) as total FROM incomes WHERE user_id = $user_id")->fetch_assoc()['total'] ?? 0;
$total_pengeluaran = $conn->query("SELECT SUM(jumlah) as total FROM expenses WHERE user_id = $user_id")->fetch_assoc()['total'] ?? 0;
$saldo = $total_pemasukan - $total_pengeluaran;

// Set judul halaman & panggil layout
$page_title = 'Kelola Keuangan';
require_once 'includes/header.php';
?>

<main>
    <div class="container py-5">
        <h1 class="fw-bold mb-4">Manajemen Keuangan Pribadi</h1>

        <div class="row mb-4">
            <div class="col-md-4 mb-3"><div class="card text-center shadow-sm"><div class="card-body"><h6 class="card-title text-muted">Total Pemasukan</h6><h4 class="fw-bold text-success">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></h4></div></div></div>
            <div class="col-md-4 mb-3"><div class="card text-center shadow-sm"><div class="card-body"><h6 class="card-title text-muted">Total Pengeluaran</h6><h4 class="fw-bold text-danger">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></h4></div></div></div>
            <div class="col-md-4 mb-3"><div class="card text-center shadow-sm"><div class="card-body"><h6 class="card-title text-muted">Saldo Akhir</h6><h4 class="fw-bold <?php echo ($saldo >= 0) ? 'text-primary' : 'text-danger'; ?>">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h4></div></div></div>
        </div>

        <div class="row">
            <div class="col-lg-5 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white"><h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Catat Transaksi Baru</h5></div>
                    <div class="card-body">
                        <form action="kelola_keuangan.php" method="POST">
                            <label class="form-label">Jenis Transaksi</label>
                            <div class="btn-group w-100 mb-3" role="group">
                                <input type="radio" class="btn-check" name="tipe_transaksi" id="pemasukan" value="pemasukan" autocomplete="off" checked>
                                <label class="btn btn-outline-success" for="pemasukan"><i class="bi bi-plus-circle me-2"></i>Pemasukan</label>

                                <input type="radio" class="btn-check" name="tipe_transaksi" id="pengeluaran" value="pengeluaran" autocomplete="off">
                                <label class="btn btn-outline-danger" for="pengeluaran"><i class="bi bi-dash-circle me-2"></i>Pengeluaran</label>
                            </div>
                            <div class="mb-3"><label for="deskripsi" class="form-label">Deskripsi</label><input type="text" class="form-control" name="deskripsi" placeholder="Gaji, makan siang, dll." required></div>
                            <div class="mb-3"><label for="jumlah" class="form-label">Jumlah (Rp)</label><input type="number" class="form-control" name="jumlah" required></div>
                            <div class="mb-3"><label for="tanggal" class="form-label">Tanggal</label><input type="date" class="form-control" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required></div>
                            <button type="submit" name="simpan_transaksi" class="btn btn-primary w-100">Simpan Transaksi</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header"><h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Riwayat Transaksi</h5></div>
                    <div class="card-body table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-sm table-hover">
                            <thead><tr><th>Tanggal</th><th>Deskripsi</th><th class="text-end">Jumlah</th></tr></thead>
                            <tbody>
                                <?php if(empty($transactions)): ?>
                                    <tr><td colspan="3" class="text-center text-muted p-4">Belum ada transaksi.</td></tr>
                                <?php else: foreach ($transactions as $trx): ?>
                                    <tr>
                                        <td><?php echo date("d M Y", strtotime($trx['tanggal'])); ?></td>
                                        <td><?php echo htmlspecialchars($trx['deskripsi']); ?></td>
                                        <?php if ($trx['tipe'] == 'pemasukan'): ?>
                                            <td class="text-end text-success fw-bold">+ Rp <?php echo number_format($trx['jumlah'], 0, ',', '.'); ?></td>
                                        <?php else: ?>
                                            <td class="text-end text-danger">- Rp <?php echo number_format($trx['jumlah'], 0, ',', '.'); ?></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
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