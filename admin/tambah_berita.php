<?php
// Panggil penjaga keamanan, koneksi, dan session
require_once 'includes/auth_check.php';
require_once '../includes/db_connect.php';

// --- BAGIAN LOGIKA & PEMROSESAN DATA ---

$errors = [];
$judul = '';
$konten = '';
$category_id = '';

// Mengambil daftar kategori untuk ditampilkan di form dropdown
$sql_categories = "SELECT id, nama_kategori FROM categories ORDER BY nama_kategori";
$result_categories = $conn->query($sql_categories);

// Cek jika form sudah di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $judul = trim($_POST['judul']);
    $konten = trim($_POST['konten']);
    $category_id = $_POST['category_id'];
    $author_id = $_SESSION['user_id']; // Ambil ID admin yang sedang login

    // Validasi dasar
    if (empty($judul)) { $errors[] = "Judul tidak boleh kosong."; }
    if (empty($konten)) { $errors[] = "Konten tidak boleh kosong."; }
    if (empty($category_id)) { $errors[] = "Kategori harus dipilih."; }
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] != 0) {
        $errors[] = "Gambar utama wajib di-upload.";
    }

    // Jika tidak ada error validasi dasar, proses upload gambar
    if (empty($errors)) {
        $target_dir = "../uploads/articles/"; // Path relatif dari file ini
        $image_ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $new_filename = "article_" . time() . "_" . rand(1000, 9999) . "." . $image_ext;
        $target_file = $target_dir . $new_filename;

        // Validasi file gambar
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($image_ext, $allowed_types) && $_FILES['gambar']['size'] <= 5000000) { // Maks 5MB
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                // Jika gambar berhasil diupload, lanjutkan simpan ke database
                $sql_insert = "INSERT INTO articles (judul, konten, category_id, gambar, author_id) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = $conn->prepare($sql_insert)) {
                    $stmt->bind_param("ssisi", $judul, $konten, $category_id, $new_filename, $author_id);
                    if ($stmt->execute()) {
                        // Jika berhasil, redirect ke halaman kelola berita dengan pesan sukses
                        header("Location: kelola_berita.php?status=add_success");
                        exit();
                    } else {
                        $errors[] = "Gagal menyimpan data ke database.";
                    }
                    $stmt->close();
                }
            } else {
                $errors[] = "Terjadi kesalahan saat mengupload file.";
            }
        } else {
            $errors[] = "Tipe file tidak valid (hanya JPG, PNG, GIF) atau ukuran file terlalu besar (Maks 5MB).";
        }
    }
}

// Set judul halaman & panggil layout
$page_title = 'Tambah Berita Baru';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<h1 class="mt-4 fw-bold">Tambah Berita Baru</h1>
<p>Isi semua field di bawah ini untuk mempublikasikan artikel baru.</p>

<a href="kelola_berita.php" class="btn btn-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Berita
</a>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Gagal!</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Form Artikel</h5>
    </div>
    <div class="card-body">
        <form action="tambah_berita.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="judul" class="form-label">Judul Artikel</label>
                <input type="text" class="form-control" id="judul" name="judul" value="<?php echo htmlspecialchars($judul); ?>" required>
            </div>
            <div class="mb-3">
                <label for="konten" class="form-label">Konten</label>
                <textarea class="form-control" id="konten" name="konten" rows="10" required><?php echo htmlspecialchars($konten); ?></textarea>
                <div class="form-text">Anda bisa menggunakan tag HTML dasar seperti &lt;p&gt;, &lt;b&gt;, &lt;ul&gt;, &lt;li&gt;, dll.</div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while($category = $result_categories->fetch_assoc()): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nama_kategori']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="gambar" class="form-label">Gambar Utama</label>
                    <input class="form-control" type="file" id="gambar" name="gambar" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Publikasikan Artikel</button>
        </form>
    </div>
</div>

<?php
// Panggil footer
require_once 'includes/footer.php';
$conn->close();
?>