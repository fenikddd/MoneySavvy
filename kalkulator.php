<?php
// Tentukan judul halaman ini. INI BAGIAN YANG PALING PENTING.
$page_title = 'Kalkulator'; 

// 1. Panggil header
require_once 'includes/header.php';


// Inisialisasi variabel untuk menampung data dan hasil
$jumlah_per_bulan = '';
$durasi_bulan = '';
$hasil_kalkulasi = [];
$error_message = '';

// Cek apakah form sudah di-submit dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil dan bersihkan data dari form
    $jumlah_per_bulan = filter_input(INPUT_POST, 'jumlah_per_bulan', FILTER_VALIDATE_FLOAT);
    $durasi_bulan = filter_input(INPUT_POST, 'durasi_bulan', FILTER_VALIDATE_INT);

    // Validasi input
    if ($jumlah_per_bulan === false || $jumlah_per_bulan <= 0) {
        $error_message = "Jumlah tabungan per bulan harus angka dan lebih dari nol.";
    } elseif ($durasi_bulan === false || $durasi_bulan <= 0) {
        $error_message = "Durasi menabung harus angka (bulan) dan lebih dari nol.";
    } else {
        // Jika validasi sukses, lakukan kalkulasi
        for ($i = 1; $i <= $durasi_bulan; $i++) {
            $total_tabungan = $i * $jumlah_per_bulan;
            $hasil_kalkulasi[] = [
                'bulan' => $i,
                'total' => $total_tabungan
            ];
        }
    }
}
?>

<main>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="text-center mb-5">
                    <h1 class="fw-bold">Kalkulator Proyeksi Tabungan</h1>
                    <p class="text-muted fs-5">Lihat seberapa besar pertumbuhan tabungan Anda dari waktu ke waktu.</p>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Masukkan Data</h5>
                            </div>
                            <div class="card-body">
                                <form action="kalkulator.php" method="POST">
                                    <div class="mb-3">
                                        <label for="jumlah_per_bulan" class="form-label">Jumlah Nabung / Bulan (Rp)</label>
                                        <input type="number" class="form-control" id="jumlah_per_bulan" name="jumlah_per_bulan" placeholder="Contoh: 500000" value="<?php echo htmlspecialchars($jumlah_per_bulan); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="durasi_bulan" class="form-label">Durasi Menabung (Bulan)</label>
                                        <input type="number" class="form-control" id="durasi_bulan" name="durasi_bulan" placeholder="Contoh: 12" value="<?php echo htmlspecialchars($durasi_bulan); ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Hitung Proyeksi</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0">Hasil Proyeksi</h5>
                            </div>
                            <div class="card-body" style="min-height: 250px;">
                                <?php if (!empty($error_message)): ?>
                                    <div class="alert alert-danger">
                                        <?php echo $error_message; ?>
                                    </div>
                                <?php elseif (!empty($hasil_kalkulasi)): ?>
                                    <p>Berikut adalah proyeksi tabungan Anda selama <strong><?php echo $durasi_bulan; ?> bulan</strong> dengan menabung <strong>Rp <?php echo number_format($jumlah_per_bulan, 0, ',', '.'); ?></strong> setiap bulan.</p>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th scope="col">Bulan Ke-</th>
                                                    <th scope="col" class="text-end">Total Tabungan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($hasil_kalkulasi as $hasil): ?>
                                                    <tr>
                                                        <td><?php echo $hasil['bulan']; ?></td>
                                                        <td class="text-end">Rp <?php echo number_format($hasil['total'], 0, ',', '.'); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center">
                                        <i class="bi bi-graph-up-arrow" style="font-size: 4rem; color: #ccc;"></i>
                                        <p class="text-muted mt-3">Silakan isi form di samping untuk melihat proyeksi tabungan Anda.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
<?php
// 3. Panggil footer
require_once 'includes/footer.php'; 
?>