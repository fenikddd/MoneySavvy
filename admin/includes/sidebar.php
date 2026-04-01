<div class="bg-dark border-right" id="sidebar-wrapper">
    <div class="sidebar-heading text-white fw-bold">Admin MoneySavvy</div>
    <div class="list-group list-group-flush">
        <a href="index.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="kelola_berita.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-newspaper me-2"></i>Kelola Berita</a>
        <a href="kelola_langganan.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-person-check-fill me-2"></i>Kelola Langganan</a>
        
        <a href="kelola_saran.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-chat-left-text-fill me-2"></i>Kelola Saran</a>
        
        <a href="../logout.php" class="list-group-item list-group-item-action bg-dark text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </div>
</div>
<div id="page-content-wrapper">
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container-fluid">
            <div class="navbar-nav ms-auto">
                <span class="navbar-text">
                    Selamat Datang, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                </span>
            </div>
        </div>
    </nav>

    <div class="container-fluid p-4">