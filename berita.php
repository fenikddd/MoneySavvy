<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db_connect.php';

// --- BAGIAN LOGIKA ---

// Ambil semua kategori untuk ditampilkan sebagai tombol filter
$sql_categories = "SELECT id, nama_kategori FROM categories ORDER BY nama_kategori ASC";
$result_categories = $conn->query($sql_categories);
$categories = $result_categories->fetch_all(MYSQLI_ASSOC);

// Query dasar untuk mengambil artikel
// Kita menggunakan JOIN untuk mendapatkan nama kategori dan nama author
$sql_articles = "
    SELECT 
        articles.id, 
        articles.judul, 
        articles.konten, 
        articles.gambar, 
        articles.created_at, 
        categories.nama_kategori, 
        users.nama_lengkap AS nama_author
    FROM articles
    JOIN categories ON articles.category_id = categories.id
    JOIN users ON articles.author_id = users.id
";

// Cek apakah ada filter kategori dari URL
$kategori_terpilih = null;
if (isset($_GET['kategori_id']) && is_numeric($_GET['kategori_id'])) {
    $kategori_id = $_GET['kategori_id'];
    $sql_articles .= " WHERE articles.category_id = ?";
    $stmt = $conn->prepare($sql_articles);
    $stmt->bind_param("i", $kategori_id);
    
    // Simpan nama kategori yang dipilih untuk judul
    foreach($categories as $cat) {
        if ($cat['id'] == $kategori_id) {
            $kategori_terpilih = $cat['nama_kategori'];
            break;
        }
    }
} else {
    $sql_articles .= " ORDER BY articles.created_at DESC";
    $stmt = $conn->prepare($sql_articles);
}

$stmt->execute();
$result_articles = $stmt->get_result();
$articles = $result_articles->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$page_title = 'Berita';
require_once 'includes/header.php';
?>

<main>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Berita & Artikel Keuangan</h1>
            <p class="text-muted fs-5">
                <?php 
                    echo $kategori_terpilih ? 'Menampilkan Kategori: <strong>' . htmlspecialchars($kategori_terpilih) . '</strong>' : 'Wawasan terbaru seputar dunia finansial untuk Anda.';
                ?>
            </p>
        </div>

        <div class="text-center mb-5">
            <a href="berita.php" class="btn <?php echo !isset($_GET['kategori_id']) ? 'btn-primary' : 'btn-outline-primary'; ?> me-1 mb-2">Semua Kategori</a>
            <?php foreach ($categories as $category): ?>
                <a href="berita.php?kategori_id=<?php echo $category['id']; ?>" class="btn <?php echo (isset($_GET['kategori_id']) && $_GET['kategori_id'] == $category['id']) ? 'btn-primary' : 'btn-outline-primary'; ?> me-1 mb-2">
                    <?php echo htmlspecialchars($category['nama_kategori']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="row">
            <?php if (empty($articles)): ?>
                <div class="col text-center">
                    <p class="text-muted">Tidak ada artikel yang ditemukan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                    <div class="col-lg-4 col-md-6 mb-4 d-flex align-items-stretch">
                        <div class="card shadow-sm h-100">
                            <img src="uploads/articles/<?php echo htmlspecialchars($article['gambar']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($article['judul']); ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <div>
                                    <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($article['nama_kategori']); ?></span>
                                </div>
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($article['judul']); ?></h5>
                                <p class="card-text text-muted small mb-3">
                                    Oleh <?php echo htmlspecialchars($article['nama_author']); ?> &bull; <?php echo date("d M Y", strtotime($article['created_at'])); ?>
                                </p>
                                <p class="card-text flex-grow-1">
                                    <?php echo htmlspecialchars(substr(strip_tags($article['konten']), 0, 100)) . '...'; ?>
                                </p>
                                <a href="detail_berita.php?id=<?php echo $article['id']; ?>" class="btn btn-dark mt-auto align-self-start">Baca Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';
$conn->close();
?>