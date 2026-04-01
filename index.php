<?php
// Tentukan judul halaman ini
$page_title = 'Home'; 

// 1. Panggil header (yang sudah tidak punya tag <main>)
require_once 'includes/header.php';
?>

<section class="hero-section">
    <video autoplay loop muted playsinline class="hero-video">
        <source src="assets/videos/perushaan.mp4" type="video/mp4">
        Browser Anda tidak mendukung tag video.
    </video>
    <div class="hero-content text-center text-white">
        <h1 class="display-3 fw-bold">SELAMAT DATANG DI MONEYSAVVY</h1>
        <p class="lead fw-normal mt-3">Wujudkan Kebebasan Finansial Anda, Mulai Hari Ini.</p>
        <a href="#layanan" class="btn btn-cta btn-lg mt-4">Pelajari Layanan Kami</a>
    </div>
</section>

<main>
    <section id="visimisi" class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col">
                    <h2 class="fw-bold">Tujuan Kami</h2>
                    <p class="text-muted">Landasan yang mendorong kami untuk terus berinovasi.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm p-4 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-bullseye display-4 text-warning mb-3"></i>
                            <h3 class="card-title fw-bold">Visi</h3>
                            <p class="card-text">Menjadi platform literasi dan manajemen keuangan terdepan di Indonesia yang memberdayakan setiap individu untuk mencapai kesejahteraan finansial.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm p-4 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-list-check display-4 text-warning mb-3"></i>
                            <h3 class="card-title fw-bold">Misi</h3>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Menyediakan alat finansial yang mudah diakses.</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Memberikan konten edukasi keuangan yang relevan.</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Menawarkan layanan konsultasi keuangan yang terpercaya.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="py-5 bg-light">
        <div class="container">
             <div class="row text-center mb-5">
                <div class="col">
                    <h2 class="fw-bold">Layanan Unggulan</h2>
                    <p class="text-muted">Solusi lengkap untuk setiap langkah perjalanan keuangan Anda.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card card-layanan text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-calculator-fill display-4 text-warning mb-3"></i>
                            <h5 class="card-title fw-bold">Kalkulator Keuangan</h5>
                            <p class="card-text">Hitung potensi tabungan dan investasimu dengan mudah.</p>
                            <a href="kalkulator.php" class="btn btn-outline-primary">Coba Sekarang</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card card-layanan text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-headset display-4 text-warning mb-3"></i>
                            <h5 class="card-title fw-bold">Konsultasi Ahli</h5>
                            <p class="card-text">Dapatkan panduan dari para profesional di bidang keuangan.</p>
                            <a href="konsultasi.php" class="btn btn-outline-primary">Pilih Paket</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card card-layanan text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-wallet-fill display-4 text-warning mb-3"></i>
                            <h5 class="card-title fw-bold">Kelola Anggaran</h5>
                            <p class="card-text">Catat dan pantau semua pemasukan dan pengeluaran harianmu.</p>
                            <a href="kelola_keuangan.php" class="btn btn-outline-primary">Mulai Kelola</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card card-layanan text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-newspaper display-4 text-warning mb-3"></i>
                            <h5 class="card-title fw-bold">Berita Finansial</h5>
                            <p class="card-text">Tetap update dengan berita dan tren terbaru seputar ekonomi.</p>
                            <a href="berita.php" class="btn btn-outline-primary">Baca Berita</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="kritik-saran" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="fw-bold">Punya Masukan?</h2>
                    <p class="text-muted mb-4">Kami sangat menghargai setiap kritik dan saran dari Anda untuk membuat MoneySavvy menjadi lebih baik.</p>
                    
                    <?php if (isset($_GET['status']) && $_GET['status'] == 'saran_terkirim'): ?>
                    <div class="alert alert-success">
                        Terima kasih! Pesan Anda telah kami terima.
                    </div>
                    <?php endif; ?>

                    <form action="proses_saran.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="text" name="nama" class="form-control" placeholder="Nama Anda" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Email Anda" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="subjek" class="form-control" placeholder="Subjek Pesan" required>
                        </div>
                        <div class="mb-3">
                            <textarea name="pesan" class="form-control" rows="5" placeholder="Tuliskan pesan Anda di sini..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
// 3. Panggil footer (yang sudah tidak punya tag </main>)
require_once 'includes/footer.php'; 
?>