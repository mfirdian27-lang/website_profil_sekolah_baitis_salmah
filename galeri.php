<?php
// 1. KONEKSI DATABASE
include 'koneksi.php';

// 2. QUERY AMBIL SELURUH DATA DOKUMENTASI FOTO (Terbaru di posisi teratas)
$query_galeri = mysqli_query($koneksi, "SELECT * FROM galeri ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Dokumentasi Kegiatan - Baitis Salmah</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2 family=Cormorant+Garamond:wght@600;700&family=Arimo:wght@400;700&family=Azeret+Mono:wght@400&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        /* --- GLOBAL PALETTE (Sage Green & Akaroa Museum Style) --- */
        :root {
            --bg-main: #F7F3EC;
            --bg-section: #D4C4A8;
            --accent-primary: #6B8F71;
            --accent-hover: #A3B18A;
            --text-main: #1F2A24;
            --border-soft: rgba(31, 42, 36, 0.08);
            --shadow-museum: 0 15px 35px rgba(31, 42, 36, 0.05);
            --radius-card: 24px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            background-color: var(--bg-main); 
            color: var(--text-main); 
            font-family: 'Arimo', sans-serif; 
            line-height: 1.7; 
            overflow-x: hidden;
        }

        /* --- STICKY NAVIGATION BAR --- */
        .header {
            position: sticky; top: 0; z-index: 1000;
            background: var(--bg-section);
            border-bottom: 1px solid var(--border-soft);
        }
        .navbar {
            max-width: 1200px; margin: 0 auto; padding: 1rem 2rem;
            display: flex; justify-content: space-between; align-items: center;
        }
        .logo-img { height: 50px; border-radius: 8px; }
        .nav-links { display: flex; list-style: none; gap: 2.5rem; }
        .nav-links a { 
            text-decoration: none; color: var(--text-main); 
            font-weight: 700; font-size: 0.9rem; 
            text-transform: uppercase; letter-spacing: 1px;
            transition: color 0.3s;
        }
        .nav-links a.active { color: var(--accent-primary); }
        .nav-links a:hover { color: var(--accent-primary); }

        /* --- HERO HEADER DESIGN --- */
        .page-hero {
            height: 42vh;
            background: linear-gradient(rgba(31, 42, 36, 0.3), rgba(31, 42, 36, 0.3)), 
                        url('uploads/WhatsApp Image 2026-06-03 at 17.51.04 (1).jpeg') center/cover no-repeat;
            display: flex; align-items: center; justify-content: center;
        }
        .hero-title { 
            font-family: 'Cormorant Garamond', serif; 
            font-size: clamp(1.5rem, 4vw, 2.5rem); font-weight: 600;
            background: rgba(247, 243, 236, 0.95);
            padding: 1rem 3rem; border-radius: 100px;
            box-shadow: var(--shadow-museum); border: 1px solid var(--border-soft);
            text-align: center;
        }

        /* --- MAIN GRID LAYOUT --- */
        .main-container { max-width: 1200px; margin: 0 auto; padding: 5rem 2rem; }
        
        .galeri-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2.5rem;
        }

        .galeri-card {
            background: white;
            border-radius: var(--radius-card);
            overflow: hidden;
            box-shadow: var(--shadow-museum);
            border: 1px solid var(--border-soft);
            opacity: 0; transform: translateY(40px); /* Untuk inisiasi GSAP */
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }
        .galeri-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(31, 42, 36, 0.08);
        }

        .image-wrapper {
            position: relative;
            width: 100%; height: 240px;
            overflow: hidden;
            background-color: #eee;
        }
        .galeri-img {
            width: 100%; height: 100%;
            object-cover: cover;
            transition: transform 0.6s ease;
        }
        .galeri-card:hover .galeri-img {
            transform: scale(1.06);
        }

        .card-content { padding: 1.8rem; }
        .meta-date {
            font-family: 'Azeret Mono', monospace;
            font-size: 0.75rem; color: var(--accent-primary);
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 0.5rem; display: block;
        }
        .foto-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem; font-weight: 700;
            line-height: 1.3; color: var(--text-main);
            margin-bottom: 0.8rem;
        }
        .foto-desc {
            font-size: 0.9rem; color: #555;
            line-height: 1.6;
        }

        .empty-state {
            text-align: center; padding: 4rem 2rem; grid-column: 1 / -1;
            background: rgba(255, 255, 255, 0.6); border-radius: var(--radius-card);
            border: 2px dashed var(--border-soft); color: #777; font-style: italic;
        }

        /* --- FOOTER REGION --- */
        .footer { background: var(--bg-section); padding: 5rem 2rem; border-top: 1px solid var(--border-soft); }
        .footer-grid { 
            max-width: 1100px; margin: 0 auto;
            display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 4rem;
        }
        .footer h3 { font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; margin-bottom: 1rem; }

        @media (max-width: 768px) {
            .footer-grid { grid-template-columns: 1fr; gap: 2.5rem; }
            .galeri-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header class="header">
        <nav class="navbar">
            <a href="index.php"><img src="uploads/logo.jpeg" class="logo-img" alt="Logo"></a>
            <ul class="nav-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="ekstrakurikuler.php">Ekstrakurikuler</a></li>
                <li><a href="berita.php">Berita</a></li>
                <li><a href="galeri.php" class="active">Galeri</a></li>
                <li><a href="kontak.php">Kontak</a></li>
            </ul>
        </nav>
    </header>

    <section class="page-hero">
        <h1 class="hero-title">Galeri & Dokumentasi Kegiatan</h1>
    </section>

    <main class="main-container">
        <div class="galeri-grid">
            
            <?php if (mysqli_num_rows($query_galeri) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($query_galeri)): ?>
                    <article class="galeri-card reveal">
                        <div class="image-wrapper">
                            <img src="uploads/<?= $row['file_foto']; ?>" class="galeri-img" alt="<?= htmlspecialchars($row['judul_foto']); ?>">
                        </div>
                        <div class="card-content">
                            <span class="meta-date">📅 <?= date('d M Y', strtotime($row['created_at'])); ?></span>
                            <h2 class="foto-title"><?= htmlspecialchars($row['judul_foto']); ?></h2>
                            <p class="foto-desc"><?= htmlspecialchars($row['deskripsi']); ?></p>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>📸 Belum ada koleksi foto dokumentasi kegiatan yang dipublikasikan saat ini.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <footer class="footer">
        <div class="footer-grid">
            <div>
                <h3>Baitis Salmah</h3>
                <p>Mencetak generasi unggul dalam akademik dan berakhlak mulia melalui pembinaan ekstrakurikuler yang terstruktur.</p>
            </div>
            <div>
                <h4 style="margin-bottom: 1.5rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">Navigasi</h4>
                <ul style="list-style: none; space-y: 0.5rem;">
                    <li><a href="index.php" style="color: inherit; text-decoration: none; opacity: 0.8;">Beranda</a></li>
                    <li><a href="profil.php" style="color: inherit; text-decoration: none; opacity: 0.8;">Profil Sekolah</a></li>
                    <li><a href="login.php" style="color: inherit; text-decoration: none; opacity: 0.8;">Portal Admin</a></li>
                </ul>
            </div>
            <div>
                <h4 style="margin-bottom: 1.5rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">Kontak</h4>
                <p style="opacity: 0.8;">Ciputat, Tangerang Selatan</p>
                <p style="opacity: 0.8;">+62 856 9887 167</p>
            </div>
        </div>
        <div style="text-align: center; margin-top: 5rem; border-top: 1px solid rgba(31,42,36,0.06); padding-top: 2rem; font-size: 0.8rem; opacity: 0.4;">
            &copy; 2026 Baitis Salmah School. All Rights Reserved.
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            gsap.registerPlugin(ScrollTrigger);
            
            // Animasi Amplitudo Judul Utama Hero
            gsap.to(".hero-title", { opacity: 1, y: 0, duration: 1.4, ease: "power3.out" });

            // Efek Staggered Reveal Cards saat di-scroll kebawah
            gsap.utils.toArray(".reveal").forEach((card) => {
                gsap.to(card, {
                    opacity: 1,
                    y: 0,
                    duration: 1.2,
                    ease: "power2.out",
                    scrollTrigger: {
                        trigger: card,
                        start: "top 88%",
                        toggleActions: "play none none none"
                    }
                });
            });
        });
    </script>
</body>
</html>