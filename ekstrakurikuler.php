<?php
// 1. KONEKSI DATABASE
include 'koneksi.php';

// 2. QUERY DATA EKSTRAKURIKULER
$query_ekskul = mysqli_query($koneksi, "SELECT * FROM ekstrakurikuler ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekstrakurikuler - Baitis Salmah</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Arimo:wght@400;700&family=Azeret+Mono:wght@400&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        /* --- GLOBAL PALETTE (Sage Green & Akaroa) --- */
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
            height: 32vh;
            background: linear-gradient(rgba(31, 42, 36, 0.1), rgba(31, 42, 36, 0.1)), 
                        url('uploads/WhatsApp Image 2026-06-03 at 17.51.04 (1).jpeg') center/cover;
            display: flex; align-items: center; justify-content: center;
            text-align: center; color: var(--text-main);
        }
        .hero-title { 
            font-family: 'Cormorant Garamond', serif; 
            font-size: clamp(1.4rem, 3vw, 2rem); /* Ukuran tulisan Program Bakat & Minat diperkecil di sini */
            font-weight: 600;
            background: rgba(247, 243, 236, 0.92);
            padding: 0.8rem 2.5rem; border-radius: 100px;
            box-shadow: var(--shadow-museum);
            border: 1px solid var(--border-soft);
            letter-spacing: 0.5px;
        }

        /* --- LAYOUT UTAMA --- */
        .container { max-width: 950px; margin: 0 auto; padding: 4rem 1.5rem; }
        
        /* INDIVIDUAL EKSKUL CANVAS/CARD */
        .ekskul-card {
            background: #FFFFFF;
            border-radius: var(--radius-card);
            padding: 3.5rem 3rem;
            margin-bottom: 5rem;
            box-shadow: var(--shadow-museum);
            border: 1px solid var(--border-soft);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        /* SUSUNAN KE BAWAH: INFO KONTEN */
        .ekskul-info { width: 100%; margin-bottom: 2.5rem; }
        
        .ekskul-info h2 { 
            font-family: 'Cormorant Garamond', serif; 
            font-size: clamp(1.8rem, 3.2vw, 2.3rem); 
            color: var(--accent-primary);
            margin-bottom: 1rem; 
            line-height: 1.2;
        }
        
        .ekskul-info p { 
            font-size: 1.05rem; 
            color: #4A4A4A; 
            max-width: 780px; 
            margin: 0 auto;
            text-align: justify;
            text-justify: inter-word;
        }

        /* SUSUNAN KE BAWAH: GALERI FOTO */
        .gallery-grid {
            display: grid; 
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem; 
            width: 100%;
        }
        
        /* BINGKAI FOTO MUSEUM */
        .art-frame {
            background: white; 
            padding: 12px 12px 45px 12px;
            box-shadow: 0 10px 25px rgba(31, 42, 36, 0.05);
            border: 1px solid var(--border-soft);
            transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
        }
        
        .art-frame:hover { 
            transform: translateY(-10px) scale(1.02); 
            box-shadow: 0 20px 40px rgba(31, 42, 36, 0.12);
        }
        
        .art-frame img { 
            width: 100%; 
            height: 260px; 
            object-fit: cover; 
        }
        
        .art-caption {
            font-family: 'Azeret Mono', monospace;
            font-size: 0.75rem; 
            margin-top: 18px;
            text-align: center; 
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
        }

        /* --- FOOTER --- */
        .footer { background: var(--bg-section); padding: 5rem 2rem; border-top: 1px solid var(--border-soft); }
        .footer-grid { 
            max-width: 1100px; margin: 0 auto;
            display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 4rem;
        }
        .footer h3 { font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; margin-bottom: 1rem; }

        /* --- UTILITY ANIMASI (FADE IN UP) --- */
        .reveal { 
            opacity: 0; 
            transform: translateY(40px); 
            transition: opacity 1.2s cubic-bezier(0.165, 0.84, 0.44, 1), transform 1.2s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* RESPONSIVITAS MOBILE */
        @media (max-width: 768px) {
            .hero-title { font-size: 1.3rem; padding: 0.6rem 1.8rem; }
            .ekskul-card { padding: 2.5rem 1.5rem; margin-bottom: 3.5rem; }
            .ekskul-info h2 { font-size: 1.6rem; }
            .ekskul-info p { text-align: center; font-size: 0.95rem; }
            .gallery-grid { grid-template-columns: 1fr; gap: 1.5rem; }
            .art-frame img { height: 210px; }
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
                <li><a href="ekstrakurikuler.php" class="active">Ekstrakurikuler</a></li>
                <li><a href="kontak.php">Kontak</a></li>
            </ul>
        </nav>
    </header>

    <section class="page-hero">
        <h1 class="hero-title reveal">Program Bakat & Minat</h1>
    </section>

    <main class="container">
        <?php if(mysqli_num_rows($query_ekskul) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($query_ekskul)): 
                $ekskul_id = $row['id'];
                // Query mengambil foto dokumentasi terikat ekskul terkait (Maksimal 4 foto)
                $query_foto = mysqli_query($koneksi, "SELECT file_foto, keterangan FROM foto_ekstrakurikuler WHERE ekstrakurikuler_id = $ekskul_id ORDER BY id DESC LIMIT 4");
            ?>
                <div class="ekskul-card reveal">
                    
                    <div class="ekskul-info">
                        <h2><?= htmlspecialchars($row['nama_ekskul']); ?></h2>
                        <p><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></p>
                    </div>

                    <div class="gallery-grid">
                        <?php if(mysqli_num_rows($query_foto) > 0): ?>
                            <?php while($img = mysqli_fetch_assoc($query_foto)): ?>
                                <div class="art-frame">
                                    <img src="uploads/<?= $img['file_foto']; ?>" alt="Dokumentasi <?= htmlspecialchars($row['nama_ekskul']); ?>">
                                    <div class="art-caption"><?= htmlspecialchars($img['keterangan'] ?: 'Dokumentasi Kegiatan'); ?></div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="art-frame" style="grid-column: 1 / -1; padding: 40px 10px;">
                                <div style="height: 120px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #bbb; gap: 10px;">
                                    <i class="fa-regular fa-image" style="font-size: 2rem;"></i>
                                    <p style="font-style: italic; font-size: 0.9rem;">Dokumentasi foto akan segera diperbarui oleh pihak sekolah.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 5rem 0; background: white; border-radius: var(--radius-card); border: 1px dashed var(--bg-section);">
                <i class="fa-solid fa-graduation-cap" style="font-size: 2.5rem; color: var(--bg-section); margin-bottom: 1rem;"></i>
                <p style="opacity: 0.6; font-size: 1rem;">Belum ada data program ekstrakurikuler yang dimasukkan.</p>
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

            // Animasi Judul saat pertama kali halaman terbuka
            gsap.to(".hero-title", {
                opacity: 1,
                y: 0,
                duration: 1.4,
                ease: "power3.out"
            });

            // Memicu efek kemunculan halus (Fade In Up) untuk tiap card saat di-scroll ke bawah
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
    <script src="./js/animations.js"></script>
    <script src="./js/app.js"></script>
</body>
</html>