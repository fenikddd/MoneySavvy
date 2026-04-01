<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db_connect.php';

// --- BAGIAN LOGIKA ---

$article = null;

// 1. Validasi ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Jika ID tidak ada atau bukan angka, kita tidak perlu lanjut
    $article = null;
} else {
    $article_id = (int)$_GET['id'];

    // 2. Query untuk mengambil satu artikel spesifik
    $sql = "
        SELECT 
            articles.id, 
            articles.judul, 
            articles.konten, 
            articles.gambar, 
            articles.created_at, 
            categories.id AS category_id,
            categories.nama_kategori, 
            users.nama_lengkap AS nama_author
        FROM articles
        JOIN categories ON articles.category_id = categories.id
        JOIN users ON articles.author_id = users.id
        WHERE articles.id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc(); // Ambil satu baris data
    $stmt->close();
}

// Set judul halaman berdasarkan hasil
$page_title = $article ? $article['judul'] : 'Artikel Tidak Ditemukan';
require_once 'includes/header.php';
?>

<main>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <?php if ($article): // Jika artikel ditemukan, tampilkan kontennya ?>

                    <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($article['judul']); ?></h1>

                    <p class="text-muted border-bottom pb-3 mb-4">
                        <i class="bi bi-person-fill"></i> Oleh <?php echo htmlspecialchars($article['nama_author']); ?> | 
                        <i class="bi bi-calendar-event"></i> <?php echo date("d F Y", strtotime($article['created_at'])); ?> | 
                        <i class="bi bi-tag-fill"></i> Kategori: <a href="berita.php?kategori_id=<?php echo $article['category_id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($article['nama_kategori']); ?></a>
                    </p>
                    
                    <img src="uploads/articles/<?php echo htmlspecialchars($article['gambar']); ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($article['judul']); ?>">
                    
                    <div class="article-content">
                        <?php echo $article['konten']; // Kita echo langsung untuk merender tag <p> dari database ?>
                    </div>

                    <hr class="my-5">

                    <a href="berita.php" class="btn btn-primary"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Berita</a>

                <?php else: // Jika artikel tidak ditemukan, tampilkan pesan error ?>

                    <div class="text-center py-5">
                        <h1 class="display-1 fw-bold">404</h1>
                        <h2>Artikel Tidak Ditemukan</h2>
                        <p class="lead text-muted">Maaf, artikel yang Anda cari tidak ada atau mungkin telah dihapus.</p>
                        <a href="berita.php" class="btn btn-primary mt-3">Kembali ke Daftar Berita</a>
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