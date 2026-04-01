<?php
// 1. Panggil penjaga keamanan dan koneksi database
require_once 'includes/auth_check.php';
require_once '../includes/db_connect.php';

// --- BAGIAN LOGIKA ---

// Query untuk mengambil semua artikel dengan data terkait (nama kategori & penulis)
$sql = "
    SELECT 
        articles.id, 
        articles.judul, 
        articles.created_at, 
        categories.nama_kategori, 
        users.nama_lengkap AS nama_author
    FROM articles
    JOIN categories ON articles.category_id = categories.id
    JOIN users ON articles.author_id = users.id
    ORDER BY articles.created_at DESC
";

$result = $conn->query($sql);

// Set judul halaman & panggil layout
$page_title = 'Kelola Berita';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<h1 class="mt-4 fw-bold">Kelola Berita</h1>
<p>Di sini Anda bisa menambah, mengubah, dan menghapus artikel berita.</p>

<a href="tambah_berita.php" class="btn btn-success mb-3">
    <i class="bi bi-plus-circle"></i> Tambah Berita Baru
</a>

<?php if(isset($_GET['status'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php
        if($_GET['status'] == 'add_success') echo "Artikel baru berhasil ditambahkan!";
        if($_GET['status'] == 'edit_success') echo "Artikel berhasil diperbarui!";
        if($_GET['status'] == 'delete_success') echo "Artikel berhasil dihapus!";
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>


<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Daftar Artikel</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>Judul Artikel</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th>Tanggal Publikasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $nomor = 1; ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $nomor++; ?></td>
                                <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['nama_kategori']); ?></span></td>
                                <td><?php echo htmlspecialchars($row['nama_author']); ?></td>
                                <td><?php echo date("d M Y, H:i", strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="edit_berita.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="hapus_berita.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Anda yakin ingin menghapus artikel ini secara permanen?');">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada artikel.</td>
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