<?php
// 1. KONEKSI DATABASE
include 'koneksi.php';

// 2. QUERY HANYA AMBIL BERITA YANG BERSTATUS 'publish' (Sesuai Skema Terbaru)
$query_berita = mysqli_query($koneksi, "SELECT * FROM berita WHERE status = 'publish' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Pengumuman - Baitis Salmah</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Arimo:wght@400;700&family=Azeret+Mono:wght@400&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        /* --- GLOBAL PALETTE (Sage Green & Akaroa - Senada Ekskul) --- */
        :root {
            --bg-main: #F7F3EC;
            --bg-section: #D4C4A8;
            --accent-primary: #6B8F71;
            --accent-hover: #A3B18A;
            --text-main: #1F2A24;
            --text-white: #FFFFFF;
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

        /* --- HEADER --- */
        .header {
            position: sticky; top: 0; z-index: 1000;
            background: var(--bg-section);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-soft);
        }
        .navbar {
            max-width: 1200px; margin: 0 auto;
            padding: 1rem 2rem;
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
        .nav-links a:hover, .nav-links a.active { color: var(--accent-primary); }

        /* --- HERO HEADER --- */
        .page-hero {
            height: 38vh;
            background: linear-gradient(rgba(31, 42, 36, 0.35), rgba(31, 42, 36, 0.35)), 
                        url('uploads/WhatsApp Image 2026-06-03 at 17.51.04 (1).jpeg') center/cover no-repeat;
            display: flex; align-items: center; justify-content: center;
            text-align: center; color: var(--text-main);
        }
        .hero-title { 
            font-family: 'Cormorant Garamond', serif; 
            font-size: clamp(1.4rem, 3vw, 2rem); 
            font-weight: 600;
            background: rgba(247, 243, 236, 0.94);
            padding: 0.8rem 2.5rem; border-radius: 100px;
            box-shadow: var(--shadow-museum);
            border: 1px solid var(--border-soft);
            letter-spacing: 0.5px;
        }

        /* --- LAYOUT UTAMA KONTEN BERITA --- */
        .container { max-width: 1100px; margin: 0 auto; padding: 4rem 1.5rem; }
        
        .news-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 3.5rem;
        }

        .news-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-museum);
            border: 1px solid var(--border-soft);
            display: flex;
            flex-direction: column;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }
        
        .news-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(31, 42, 36, 0.08);
        }

        .news-img-wrapper {
            width: 100%;
            height: 250px;
            overflow: hidden;
            border-radius: 8px;
            background-color: var(--bg-main);
            border: 1px solid var(--border-soft);
        }

        .news-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .news-card:hover .news-img-wrapper img {
            transform: scale(1.04);
        }

        .news-meta {
            font-family: 'Azeret Mono', monospace;
            font-size: 0.75rem;
            color: #888;
            margin-top: 1.2rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .news-tag {
            background: rgba(107, 143, 113, 0.1);
            color: var(--accent-primary);
            padding: 2px 10px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .news-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            color: var(--text-main);
            margin: 0.8rem 0;
            line-height: 1.3;
        }

        .news-excerpt {
            font-size: 0.95rem;
            color: #555;
            text-align: justify;
            text-justify: inter-word;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-main);
            color: var(--text-main);
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            font-size: 0.8rem;
            font-weight: bold;
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            width: fit-content;
            transition: all 0.3s;
        }

        .btn-download:hover {
            background: var(--accent-primary);
            color: white;
        }

        /* --- FOOTER --- */
        .footer { background: var(--bg-section); padding: 5rem 2rem; border-top: 1px solid var(--border-soft); }
        .footer-grid { 
            max-width: 1100px; margin: 0 auto;
            display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 4rem;
        }
        .footer h3 { font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; margin-bottom: 1rem; }

        .reveal { opacity: 0; transform: translateY(40px); }

        @media (max-width: 768px) {
            .news-grid { grid-template-columns: 1fr; gap: 2rem; }
            .news-img-wrapper { height: 200px; }
            .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
        }
    </style>
</head>
<body>

    <header class="header">
        <nav class="navbar">
            <a href="index.php"><img src="uploads/logo.jpeg" class="logo-img" alt="Baitis Salmah"></a>
            <ul class="nav-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="ekstrakurikuler.php">Ekstrakurikuler</a></li>
                <li><a href="berita.php" class="active">Berita</a></li>
                <li><a href="kontak.php">Kontak</a></li>
            </ul>
        </nav>
    </header>

    <section class="page-hero">
        <h1 class="hero-title reveal">Berita & Informasi Sekolah</h1>
    </section>

    <main class="container">
        <?php if(mysqli_num_rows($query_berita) > 0): ?>
            <div class="news-grid">
                <?php while($row = mysqli_fetch_assoc($query_berita)): ?>
                    <article class="news-card reveal">
                        <div class="news-img-wrapper">
                            <?php if(!empty($row['foto'])): ?>
                                <img src="uploads/<?= $row['foto']; ?>" alt="<?= htmlspecialchars($row['judul']); ?>">
                            <?php else: ?>
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#ccc;">
                                    <i class="fa-regular fa-image" style="font-size:3rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="news-meta">
                            <span class="news-tag"><?= ucfirst(htmlspecialchars($row['kategori'])); ?></span>
                            <span><i class="fa-regular fa-calendar-days"></i> <?= date('d M Y', strtotime($row['created_at'])); ?></span>
                        </div>

                        <h2 class="news-title"><?= htmlspecialchars($row['judul']); ?></h2>
                        <p class="news-excerpt"><?= nl2br(htmlspecialchars($row['konten'])); ?></p>
                        
                        <?php if(!empty($row['file_pdf'])): ?>
                            <a href="uploads/<?= $row['file_pdf']; ?>" target="_blank" class="btn-download">
                                <i class="fa-regular fa-file-pdf"></i> Unduh Lampiran PDF
                            </a>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 5rem 0; background: white; border-radius: var(--radius-card); border: 1px dashed var(--bg-section);">
                <i class="fa-regular fa-newspaper" style="font-size: 2.5rem; color: var(--bg-section); margin-bottom: 1rem;"></i>
                <p style="opacity: 0.6;">Belum ada artikel berita atau pengumuman yang diunggah.</p>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <div class="footer-grid">
            <div>
                <h3>MTs Baitis Salmah</h3>
                <p>Mencetak generasi unggul dalam akademik dan berakhlak mulia melalui pembinaan ekstrakurikuler yang terstruktur.</p>
            </div>
            <div>
                <h4 style="margin-bottom: 1.5rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">Navigasi</h4>
                <ul style="list-style: none;">
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
            gsap.to(".hero-title", { opacity: 1, y: 0, duration: 1.4, ease: "power3.out" });

            gsap.utils.toArray(".reveal").forEach((card) => {
                gsap.to(card, {
                    opacity: 1,
                    y: 0,
                    duration: 1.2,
                    ease: "power2.out",
                    scrollTrigger: { trigger: card, start: "top 90%", toggleActions: "play none none none" }
                });
            });
        });
    </script>
    <script src="./js/animations.js"></script>
    <script src="./js/app.js"></script>
</body>
</html>