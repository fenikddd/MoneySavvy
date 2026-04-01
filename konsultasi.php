<?php
// Gunakan session start yang "pintar" agar bisa memeriksa status login
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tentukan judul halaman ini
$page_title = 'Konsultasi'; 

// Panggil header
require_once 'includes/header.php';
?>

<main>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Paket Konsultasi Keuangan</h1>
            <p class="text-muted fs-5">Pilih paket yang paling sesuai dengan kebutuhan finansial Anda untuk memulai.</p>
        </div>

        <div class="row justify-content-center">

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card pricing-card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title text-center fw-bold">Pemula Cerdas</h3>
                        <p class="text-center text-muted">Untuk Anda yang baru memulai perjalanan finansial.</p>
                        <div class="text-center my-4">
                            <span class="display-4 fw-bold">Rp 99k</span>
                            <span class="text-muted">/bulan</span>
                        </div>
                        <ul class="list-unstyled mb-4 flex-grow-1">
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>1 Sesi Konsultasi Online / Bulan</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Akses Grup Komunitas</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Laporan Keuangan Bulanan</li>
                            <li class="mb-3 text-muted"><i class="bi bi-x-circle me-2"></i>Analisis Portofolio Investasi</li>
                            <li class="mb-3 text-muted"><i class="bi bi-x-circle me-2"></i>Konsultasi Prioritas via Chat</li>
                        </ul>
                        
                        <?php
                        // Logika untuk link tombol paket 1
                        $link_paket1 = isset($_SESSION['user_id']) ? 'pembayaran.php?paket=basic' : 'login.php';
                        ?>
                        <a href="<?php echo $link_paket1; ?>" class="btn btn-outline-primary w-100 mt-auto">Pilih Paket</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card pricing-card recommended h-100 shadow">
                    <div class="ribbon"><span>PALING POPULER</span></div>
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title text-center fw-bold">Investor Andal</h3>
                        <p class="text-center text-muted">Untuk Anda yang ingin mengembangkan aset & investasi.</p>
                        <div class="text-center my-4">
                            <span class="display-4 fw-bold">Rp 249k</span>
                            <span class="text-muted">/bulan</span>
                        </div>
                        <ul class="list-unstyled mb-4 flex-grow-1">
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>4 Sesi Konsultasi Online / Bulan</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Akses Grup Komunitas Prioritas</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Laporan Keuangan Mingguan</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Analisis Portofolio Investasi</li>
                            <li class="mb-3 text-muted"><i class="bi bi-x-circle me-2"></i>Konsultasi Prioritas via Chat</li>
                        </ul>
                        
                        <?php
                        // Logika untuk link tombol paket 2
                        $link_paket2 = isset($_SESSION['user_id']) ? 'pembayaran.php?paket=premium' : 'login.php';
                        ?>
                        <a href="<?php echo $link_paket2; ?>" class="btn btn-cta w-100 mt-auto">Pilih Paket</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card pricing-card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title text-center fw-bold">Sultan Finansial</h3>
                        <p class="text-center text-muted">Layanan eksklusif untuk perencanaan keuangan kompleks.</p>
                        <div class="text-center my-4">
                            <span class="display-4 fw-bold">Rp 499k</span>
                            <span class="text-muted">/bulan</span>
                        </div>
                        <ul class="list-unstyled mb-4 flex-grow-1">
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Sesi Konsultasi Tanpa Batas</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Akses Grup & Personal Coaching</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Laporan Keuangan Real-time</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Analisis Portofolio Investasi</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Konsultasi Prioritas via Chat</li>
                        </ul>
                        
                        <?php
                        // Logika untuk link tombol paket 3
                        $link_paket3 = isset($_SESSION['user_id']) ? 'pembayaran.php?paket=ultimate' : 'login.php';
                        ?>
                        <a href="<?php echo $link_paket3; ?>" class="btn btn-outline-primary w-100 mt-auto">Pilih Paket</a>
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