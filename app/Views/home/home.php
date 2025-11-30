<?= $this->extend('layouts/home_layout') ?>

<?= $this->section('head') ?>
<title>Perpustakaan Unmus</title>

<!-- ScrollReveal -->
<script src="https://unpkg.com/scrollreveal"></script>

<style>
    body {
        background: #f7f9fc;
        font-family: "Inter", sans-serif;
        overflow-x: hidden;
    }

    /* NAVBAR */
    .nav-container {
        width: 100%;
        padding: 18px 50px;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(12px);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 999;
        box-shadow: 0 3px 20px rgba(0,0,0,.07);

        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .nav-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .nav-left img {
        height: 48px;
    }

    .nav-title {
        font-size: 22px;
        font-weight: 800;
        color: #2b3a8a;
        letter-spacing: 0.2px;
    }

    .nav-login {
        padding: 10px 22px;
        background: #4c6fff;
        color: white;
        border-radius: 8px;
        font-weight: 600;
        transition: .2s;
    }
    .nav-login:hover {
        background: #3456ff;
    }

    /* HERO SECTION */
    .hero-section {
        padding: 170px 20px 120px;
        text-align: center;
    }

    .hero-title {
        font-size: 48px;
        font-weight: 900;
        color: #111827;
        margin-bottom: 20px;
        line-height: 1.2;
        max-width: 750px;
        margin-left: auto;
        margin-right: auto;
    }

    .hero-desc {
        font-size: 18px;
        color: #4b5563;
        max-width: 680px;
        margin: 10px auto 30px;
        line-height: 1.6;
    }

    .hero-buttons {
        margin-top: 20px;
    }

    .hero-btn {
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        margin: 6px;
        display: inline-block;
        transition: .25s;
    }

    .hero-btn-primary {
        background: #4c6fff;
        color: white;
    }
    .hero-btn-primary:hover {
        background: #3355ff;
        transform: translateY(-3px);
    }

    .hero-btn-outline {
        border: 2px solid #4c6fff;
        color: #4c6fff;
    }
    .hero-btn-outline:hover {
        background: #4c6fff;
        color: white;
        transform: translateY(-3px);
    }

    /* STAT CARDS */
    .stats-grid {
        max-width: 1100px;
        margin: 40px auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 25px;
    }

    .stat-item {
        background: white;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 8px 22px rgba(0,0,0,0.06);
        text-align: center;
        transition: .25s;
    }

    .stat-item:hover {
        transform: translateY(-6px);
    }

    .stat-icon {
        font-size: 40px;
        color: #4c6fff;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 38px;
        font-weight: 800;
        color: #4c6fff;
    }

    .stat-label {
        margin-top: 6px;
        font-size: 16px;
        color: #4b5563;
        font-weight: 500;
    }

    /* SECTION TITLE */
    .section-title {
        text-align: center;
        font-size: 28px;
        font-weight: 800;
        color: #1f2a55;
        margin-top: 60px;
        margin-bottom: 25px;
    }

    /* FEATURE GRID */
    .features-grid {
        max-width: 1000px;
        margin: 20px auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 25px;
    }

    .feature-card {
        background: white;
        padding: 28px;
        border-radius: 16px;
        box-shadow: 0 8px 22px rgba(0,0,0,0.06);
        text-align: center;
        min-height: 180px;
        transition: .25s;
    }

    .feature-card:hover {
        transform: translateY(-6px);
    }

    .feature-icon {
        font-size: 42px;
        color: #4c6fff;
        margin-bottom: 12px;
    }

    .feature-card h3 {
        color: #4c6fff;
        font-weight: 700;
        margin-bottom: 10px;
    }

    /* OPERATIONAL */
    .operational-grid {
        max-width: 900px;
        margin: 20px auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 25px;
    }

    .operational-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 8px 22px rgba(0,0,0,0.06);
        text-align: center;
    }

    .operational-card b {
        color: #1f2a55;
        font-size: 18px;
    }

    /* GALLERY */
    .gallery-grid {
        max-width: 1100px;
        margin: 35px auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 18px;
    }

    .gallery-grid img {
        width: 100%;
        border-radius: 16px;
        box-shadow: 0 8px 22px rgba(0,0,0,0.12);
        transition: .25s;
    }

    .gallery-grid img:hover {
        transform: scale(1.03);
    }

    /* MAP */
    .map-box {
        max-width: 1100px;
        margin: 40px auto;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 22px rgba(0,0,0,0.15);
    }

    /* FOOTER */
    footer {
        padding: 40px;
        text-align: center;
        color: #4c6fff;
        font-weight: 500;
        margin-top: 50px;
    }
</style>

<?= $this->endSection() ?>


<?= $this->section('content') ?>

<!-- NAVBAR -->
<div class="nav-container">
    <div class="nav-left">
        <img src="<?= base_url('assets/images/logo-unmus.png') ?>">
        <div class="nav-title">Perpustakaan Unmus</div>
    </div>
    <a href="<?= base_url('/login') ?>" class="nav-login">Login</a>
</div>

<!-- HERO -->
<div class="hero-section">
    <h1 class="hero-title">Akses Informasi Dengan Cepat & Modern</h1>
    <p class="hero-desc">
        Sistem perpustakaan digital Universitas Musamus dengan tampilan profesional,
        proses peminjaman cepat, dan otomatisasi berbasis QR Code.
    </p>

    <div class="hero-buttons">
        <a href="<?= base_url('/login'); ?>" class="hero-btn hero-btn-primary">Login Petugas</a>
        <a href="<?= base_url('/book'); ?>" class="hero-btn hero-btn-outline">Daftar Buku</a>
    </div>
</div>

<!-- STATISTICS -->
<div class="stats-grid">

    <div class="stat-item">
        <div class="stat-icon"><i class="ti ti-book"></i></div>
        <div class="stat-value"><?= $booksCount ?></div>
        <div class="stat-label">Total Buku</div>
    </div>

    <div class="stat-item">
        <div class="stat-icon"><i class="ti ti-category-2"></i></div>
        <div class="stat-value"><?= $categoryCount ?></div>
        <div class="stat-label">Kategori</div>
    </div>

    <div class="stat-item">
        <div class="stat-icon"><i class="ti ti-columns"></i></div>
        <div class="stat-value"><?= $rackCount ?></div>
        <div class="stat-label">Rak Buku</div>
    </div>

    <div class="stat-item">
        <div class="stat-icon"><i class="ti ti-users"></i></div>
        <div class="stat-value"><?= $memberCount ?></div>
        <div class="stat-label">Anggota</div>
    </div>

</div>

<!-- FEATURES -->
<h2 class="section-title">Layanan Perpustakaan</h2>

<div class="features-grid">

    <div class="feature-card">
        <div class="feature-icon"><i class="ti ti-arrow-big-right-lines"></i></div>
        <h3>Peminjaman Buku</h3>
        <p>Peminjaman cepat & efisien dengan QR Code.</p>
    </div>

    <div class="feature-card">
        <div class="feature-icon"><i class="ti ti-arrow-big-left-lines"></i></div>
        <h3>Pengembalian Otomatis</h3>
        <p>Sistem pengembalian terintegrasi dan akurat.</p>
    </div>

    <div class="feature-card">
        <div class="feature-icon"><i class="ti ti-scan"></i></div>
        <h3>QR Scan Buku</h3>
        <p>Lihat detail buku dengan sekali scan.</p>
    </div>

    <div class="feature-card">
        <div class="feature-icon"><i class="ti ti-report-money"></i></div>
        <h3>Denda Otomatis</h3>
        <p>Denda dihitung otomatis tanpa input manual.</p>
    </div>

</div>

<!-- OPERASIONAL -->
<h2 class="section-title">Jam Operasional</h2>
<div class="operational-grid">
    <div class="operational-card">
        <b>Senin–Jumat</b>
        <p>08.00–16.00</p>
    </div>
    <div class="operational-card">
        <b>Sabtu</b>
        <p>09.00–12.00</p>
    </div>
    <div class="operational-card">
        <b>Minggu</b>
        <p>Tutup</p>
    </div>
</div>

<!-- GALLERY -->
<h2 class="section-title">Galeri Perpustakaan</h2>
<div class="gallery-grid">
    <img src="<?= base_url('assets/images/home.jpg') ?>">
    <img src="<?= base_url('assets/images/home1.jpg') ?>">
    <img src="<?= base_url('assets/images/home.jpg') ?>">
</div>

<!-- MAP -->
<h2 class="section-title">Lokasi Perpustakaan</h2>
<div class="map-box">
    <iframe 
        src="https://maps.google.com/maps?q=Universitas%20Musamus&t=&z=15&ie=UTF8&iwloc=&output=embed"
        width="100%" height="400" frameborder="0">
    </iframe>
</div>

<!-- ScrollReveal -->
<script>
    ScrollReveal().reveal('.hero-title', { delay: 100, distance: '40px', origin: 'top' });
    ScrollReveal().reveal('.hero-desc', { delay: 200, distance: '40px', origin: 'bottom' });
    ScrollReveal().reveal('.hero-btn', { delay: 300, interval: 120 });
    ScrollReveal().reveal('.stat-item', { interval: 120 });
    ScrollReveal().reveal('.feature-card', { interval: 120 });
    ScrollReveal().reveal('.gallery-grid img', { interval: 120 });
    ScrollReveal().reveal('.map-box', { delay: 200 });
</script>

<?= $this->endSection() ?>
