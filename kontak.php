<?php
// 1. KONEKSI DATABASE
include 'koneksi.php';

$notifikasi = "";

// 2. PROSES INSERT DATA FORMULIR KE KOTAK MASUK ADMIN (Sesuai Struktur Baru)
if (isset($_POST['kirim_pesan'])) {
    $nama_pengirim = mysqli_real_escape_string($koneksi, $_POST['nama_pengirim']);
    $email         = mysqli_real_escape_string($koneksi, $_POST['email']);
    $subjek        = mysqli_real_escape_string($koneksi, $_POST['subjek']);
    $isi_pesan     = mysqli_real_escape_string($koneksi, $_POST['isi_pesan']);
    
    // Query disesuaikan dengan kolom tabel asli: nama_pengirim & isi_pesan (Tanpa kolom telepon)
    $query = "INSERT INTO kontak_pesan (nama_pengirim, email, subjek, isi_pesan, status_baca) 
              VALUES ('$nama_pengirim', '$email', '$subjek', '$isi_pesan', 'belum')";
              
    if (mysqli_query($koneksi, $query)) {
        $notifikasi = "sukses";
    } else {
        $notifikasi = "gagal";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - Baitis Salmah</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Arimo:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <style>
        /* --- GLOBAL PALETTE (Sage Green & Akaroa) --- */
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

        /* --- HEADER --- */
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
        }
        .nav-links a.active { color: var(--accent-primary); }

        /* --- HERO HEADER --- */
        .page-hero {
            height: 38vh;
            background: linear-gradient(rgba(31, 42, 36, 0.35), rgba(31, 42, 36, 0.35)), 
                        url('uploads/WhatsApp Image 2026-06-03 at 17.51.04 (1).jpeg') center/cover no-repeat;
            display: flex; align-items: center; justify-content: center;
        }
        .hero-title { 
            font-family: 'Cormorant Garamond', serif; 
            font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 600;
            background: rgba(247, 243, 236, 0.94);
            padding: 0.8rem 2.5rem; border-radius: 100px;
            box-shadow: var(--shadow-museum); border: 1px solid var(--border-soft);
        }

        /* --- LAYOUT DUA KOLOM --- */
        .container { max-width: 1050px; margin: 0 auto; padding: 4rem 1.5rem; }
        
        .contact-wrapper {
            display: grid;
            grid-template-columns: 1fr 1.3fr;
            gap: 4rem;
            background: white;
            padding: 3.5rem;
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-museum);
            border: 1px solid var(--border-soft);
        }

        .info-side h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            color: var(--accent-primary);
            margin-bottom: 1.5rem;
        }

        .info-details { margin-top: 2rem; }
        .info-item { display: flex; gap: 1rem; margin-bottom: 1.5rem; align-items: flex-start;}
        .info-item i { color: var(--accent-primary); margin-top: 5px; font-size: 1.1rem;}

        /* FORMULIR STYLING */
        .form-side form { display: flex; flex-direction: column; gap: 1.2rem; }
        .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
        .form-group label { font-size: 0.8rem; font-weight: bold; text-transform: uppercase; color: #666; letter-spacing: 0.5px;}
        
        .form-control {
            font-family: 'Arimo', sans-serif;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            border: 1px solid var(--border-soft);
            background: var(--bg-main);
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s, background 0.3s;
        }
        .form-control:focus { border-color: var(--accent-primary); background: white; }

        .btn-submit {
            background: var(--text-main);
            color: white;
            border: none;
            padding: 0.9rem;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 0.5rem;
        }
        .btn-submit:hover { background: var(--accent-primary); }

        /* NOTIFIKASI BANNER */
        .alert {
            padding: 1rem; border-radius: 8px; font-size: 0.9rem; font-weight: 500; margin-bottom: 1.5rem;
        }
        .alert-success { background: #E8F5E9; color: #2E7D32; border: 1px solid #C8E6C9; }
        .alert-error { background: #FFEBEE; color: #C62828; border: 1px solid #FFCDD2; }

        /* --- FOOTER --- */
        .footer { background: var(--bg-section); padding: 5rem 2rem; border-top: 1px solid var(--border-soft); }
        .footer-grid { 
            max-width: 1100px; margin: 0 auto;
            display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 4rem;
        }
        .footer h3 { font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; }

        @media (max-width: 768px) {
            .contact-wrapper { grid-template-columns: 1fr; padding: 2rem 1.5rem; gap: 2.5rem; }
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
                <li><a href="berita.php">Berita</a></li>
                <li><a href="kontak.php" class="active">Kontak</a></li>
            </ul>
        </nav>
    </header>

    <section class="page-hero">
        <h1 class="hero-title" id="titleHero" style="opacity:0; transform: translateY(30px);">Suara Pembaca & Pengunjung</h1>
    </section>

    <main class="container">
        <div class="contact-wrapper">
            
            <div class="info-side">
                <h2>Beri Masukan & Saran</h2>
                <p>Pendapat, komentar, serta saran Anda sangat berharga bagi kami untuk terus meningkatkan kualitas pelayanan, fasilitas akademik, serta performa kegiatan pembelajaran di Baitis Salmah.</p>
                
                <div class="info-details">
                    <div class="info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <div>
                            <strong>Alamat Kampus</strong>
                            <p style="font-size: 0.95rem; color:#555; margin-top: 4px;">Ciputat, Tangerang Selatan, Banten, Indonesia</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-phone"></i>
                        <div>
                            <strong>Hubungan Masyarakat</strong>
                            <p style="font-size: 0.95rem; color:#555; margin-top: 4px;">+62 856 9887 167</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-envelope"></i>
                        <div>
                            <strong>Surat Elektronik Resmi</strong>
                            <p style="font-size: 0.95rem; color:#555; margin-top: 4px;">info@baitissalmah.sch.id</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-side">
                <?php if($notifikasi == "sukses"): ?>
                    <div class="alert alert-success">✨ Terima kasih! Pendapat, masukan, dan saran Anda telah tersimpan dan akan menjadi bahan evaluasi internal kami.</div>
                <?php elseif($notifikasi == "gagal"): ?>
                    <div class="alert alert-error">❌ Maaf, pesan gagal terkirim. Mohon periksa kembali jaringan koneksi database Anda.</div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="form-group">
                        <label>Nama Pengirim</label>
                        <input type="text" name="nama_pengirim" required placeholder="Contoh: Ahmad Subagja" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Alamat Email Kontak</label>
                        <input type="email" name="email" required placeholder="Contoh: ahmad@gmail.com" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Perihal / Kepentingan</label>
                        <input type="text" name="subjek" required placeholder="Contoh: Kritik/Saran Mengenai Fasilitas atau Konten Website" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Komentar, Masukan, atau Saran</label>
                        <textarea name="isi_pesan" rows="6" required placeholder="Tuliskan ulasan, pendapat, kritik, atau saran membangun Anda secara rinci di sini..." class="form-control" style="resize: none;"></textarea>
                    </div>
                    <button type="submit" name="kirim_pesan" class="btn-submit">Kirim Masukan & Saran</button>
                </form>
            </div>

        </div>
    </main>

    <footer class="footer">
        <div class="footer-grid">
            <div>
                <h3>MTsBaitis Salmah</h3>
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
            gsap.to("#titleHero", { opacity: 1, y: 0, duration: 1.2, ease: "power3.out" });
        });
    </script>
    <script src="./js/animations.js"></script>
    <script src="./js/app.js"></script>
</body>
</html>