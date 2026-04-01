document.addEventListener('DOMContentLoaded', function () {
    // Fungsi untuk Navbar Shrink saat scroll
    const navbar = document.getElementById('mainNavbar');
    if (navbar) {
        const navbarShrink = function () {
            if (window.scrollY > 100) {
                navbar.classList.add('navbar-shrink');
            } else {
                navbar.classList.remove('navbar-shrink');
            }
        };

        // Jalankan fungsi saat pertama kali load
        navbarShrink();

        // Jalankan fungsi setiap kali ada event scroll
        document.addEventListener('scroll', navbarShrink);
    }
});