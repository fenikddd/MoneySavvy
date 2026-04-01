<?php
// Memulai session di setiap halaman. Penting untuk sistem login.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - MoneySavvy' : 'MoneySavvy - Kelola Keuanganmu'; ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?php echo (isset($page_title) && $page_title == 'Home') ? 'homepage' : 'internal-page'; ?>">

<header>
    <nav id="mainNavbar" class="navbar navbar-expand-lg navbar-dark fixed-top navbar-moneysavvy">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <img src="assets/images/logofinal.png" alt="MoneySavvy Logo" height="40">
                MoneySavvy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($page_title) && $page_title == 'Kalkulator') ? 'active' : ''; ?>" href="kalkulator.php">Kalkulator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($page_title) && $page_title == 'Konsultasi') ? 'active' : ''; ?>" href="konsultasi.php">Konsultasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($page_title) && $page_title == 'Kelola Keuangan') ? 'active' : ''; ?>" href="kelola_keuangan.php">Kelola Keuangan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($page_title) && $page_title == 'Berita') ? 'active' : ''; ?>" href="berita.php">Berita</a>
                    </li>
                </ul>
                
                <div class="navbar-nav ms-lg-3">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i> Hai, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="profil.php">Profil Saya</a></li>
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                                    <li><a class="dropdown-item" href="admin/index.php">Dashboard Admin</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light me-2">Login</a>
                        <a href="register.php" class="btn btn-cta">Daftar Gratis</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>