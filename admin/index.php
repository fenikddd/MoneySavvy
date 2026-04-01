<?php
// 1. Panggil si penjaga keamanan dan koneksi database
require_once 'includes/auth_check.php';
require_once '../includes/db_connect.php'; // Path diubah karena kita di dalam folder admin

// --- LOGIKA PENGAMBILAN DATA UNTUK DASHBOARD ---

// Total Pengguna
$total_users = $conn->query("SELECT COUNT(id) as total FROM users")->fetch_assoc()['total'];

// Berita Aktif
$total_articles = $conn->query("SELECT COUNT(id) as total FROM articles")->fetch_assoc()['total'];

// Langganan Baru (Bulan Ini)
$sql_subs_month = "SELECT COUNT(id) as total FROM subscriptions WHERE MONTH(tanggal_langganan) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_langganan) = YEAR(CURRENT_DATE())";
$new_subscriptions = $conn->query($sql_subs_month)->fetch_assoc()['total'];

// Pembayaran Pending
$pending_payments = $conn->query("SELECT COUNT(id) as total FROM subscriptions WHERE status = 'pending'")->fetch_assoc()['total'];

// Berita Terbaru (5 terakhir)
$recent_articles_result = $conn->query("SELECT judul, created_at FROM articles ORDER BY created_at DESC LIMIT 5");

// Pembayaran Tertunda (5 terakhir)
$pending_subs_result = $conn->query("SELECT u.nama_lengkap, s.paket, s.tanggal_langganan FROM subscriptions s JOIN users u ON s.user_id = u.id WHERE s.status = 'pending' ORDER BY s.tanggal_langganan DESC LIMIT 5");


// 2. Set judul halaman & panggil layout
$page_title = 'Dashboard';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<h1 class="mt-4 fw-bold">Dashboard</h1>
<p>Ringkasan data dan statistik dari website MoneySavvy.</p>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats-success h-100 shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Pengguna</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $total_users; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people-fill fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats-info h-100 shadow">
            <div class="card-body">
                 <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">Berita Aktif</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $total_articles; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-newspaper fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats-warning h-100 shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">Langganan Baru (Bulan ini)</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $new_subscriptions; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-plus-fill fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats-danger h-100 shadow">
            <div class="card-body">
                 <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1">Pembayaran Pending</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $pending_payments; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-clock-history fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Grafik Pendapatan Bulanan (Contoh)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Distribusi Paket Langganan (Contoh)</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4">
                    <canvas id="myPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header"><h6 class="m-0 fw-bold">Berita Terbaru</h6></div>
            <div class="list-group list-group-flush">
                <?php while($article = $recent_articles_result->fetch_assoc()): ?>
                    <a href="#" class="list-group-item list-group-item-action"><?php echo htmlspecialchars($article['judul']); ?></a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header"><h6 class="m-0 fw-bold">Pembayaran Tertunda</h6></div>
             <div class="list-group list-group-flush">
                <?php while($sub = $pending_subs_result->fetch_assoc()): ?>
                    <a href="kelola_langganan.php" class="list-group-item list-group-item-action">
                        <?php echo htmlspecialchars($sub['nama_lengkap']) . ' - Paket ' . htmlspecialchars($sub['paket']); ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<?php
// 4. Panggil footer
require_once 'includes/footer.php';
$conn->close();
?>