<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$notifikasi = "";

// ==========================================
// 1. PROSES UNGGAH/UPLOAD FOTO GALERI
// ==========================================
if (isset($_POST['upload_galeri'])) {
    $judul_foto = mysqli_real_escape_string($koneksi, $_POST['judul_foto']);
    $deskripsi  = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $admin_id   = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : 1; 

    $filename = $_FILES['file_foto']['name'];
    $tmp_name = $_FILES['file_foto']['tmp_name'];
    
    if (!empty($filename)) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $nama_foto_baru = "galeri_" . time() . "_" . rand(100, 999) . "." . $ext;
            $target_dir     = "uploads/";

            if (move_uploaded_file($tmp_name, $target_dir . $nama_foto_baru)) {
                $query = "INSERT INTO galeri (admin_id, judul_foto, deskripsi, file_foto) 
                          VALUES ($admin_id, '$judul_foto', '$deskripsi', '$nama_foto_baru')";
                
                if (mysqli_query($koneksi, $query)) {
                    header("Location: admin-galeri.php?status=sukses");
                    exit;
                } else {
                    $notifikasi = "gagal_db";
                }
            } else {
                $notifikasi = "gagal_upload";
            }
        } else {
            $notifikasi = "ekstensi_salah";
        }
    } else {
        $notifikasi = "file_kosong";
    }
}

// ==========================================
// 2. PROSES HAPUS FOTO GALERI
// ==========================================
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    
    $cari_foto = mysqli_query($koneksi, "SELECT file_foto FROM galeri WHERE id = $id_hapus");
    if (mysqli_num_rows($cari_foto) > 0) {
        $data_foto = mysqli_fetch_assoc($cari_foto);
        $path_file = "uploads/" . $data_foto['file_foto'];
        
        if (file_exists($path_file)) {
            unlink($path_file);
        }
        
        mysqli_query($koneksi, "DELETE FROM galeri WHERE id = $id_hapus");
        header("Location: admin-galeri.php?status=terhapus");
        exit;
    }
}

$query_galeri = mysqli_query($koneksi, "SELECT g.*, a.nama_lengkap FROM galeri g 
                                        JOIN admin a ON g.admin_id = a.id 
                                        ORDER BY g.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Panel Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .safari-icon {
            width: 20px;
            height: 16px;
            border: 2px solid #475569;
            border-radius: 3px;
            position: relative;
            display: inline-block;
        }
        .safari-icon::before {
            content: "";
            position: absolute;
            top: 0;
            left: 5px;
            bottom: 0;
            width: 2px;
            background-color: #475569;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans flex flex-col h-screen overflow-hidden">

    <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center border-b z-50 shrink-0">
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="p-2 hover:bg-gray-100 rounded-lg transition duration-200 flex items-center justify-center">
                <span class="safari-icon"></span>
            </button>
            <h1 class="text-lg font-bold text-slate-800 tracking-wide">MBS ADMIN</h1>
        </div>
        <div class="flex items-center">
            <span class="text-sm text-gray-600 font-medium">Selamat Datang, <span class="text-slate-800 font-semibold">Admin Sekolah</span></span>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden relative">
        
        <aside id="sidebar" class="w-64 bg-slate-900 text-slate-200 flex flex-col justify-between transition-all duration-300 ease-in-out shrink-0 z-40 border-r border-slate-800">
            <div class="p-5">
                <nav class="space-y-2">
                    <a href="dashboard.php" class="block py-2.5 px-4 rounded text-slate-400 hover:bg-slate-800 hover:text-white transition duration-200">📊 Dashboard</a>
                    <a href="admin-berita.php" class="block py-2.5 px-4 rounded text-slate-400 hover:bg-slate-800 hover:text-white transition duration-200">📰 Kelola Berita</a>
                    <a href="admin-ekskul.php" class="block py-2.5 px-4 rounded text-slate-400 hover:bg-slate-800 hover:text-white transition duration-200">🏆 Kelola Ekskul</a>
                    <a href="admin-galeri.php" class="block py-2.5 px-4 rounded bg-amber-500 text-slate-950 font-semibold shadow-md shadow-amber-500/10 transition duration-200">🖼️ Kelola Galeri</a>
                    <a href="admin-pesan.php" class="block py-2.5 px-4 rounded text-slate-400 hover:bg-slate-800 hover:text-white transition duration-200">📩 Pesan Masuk</a>
                </nav>
            </div>
            <div class="p-5 border-t border-slate-800">
                <a href="logout.php" onclick="return confirm('Apakah anda yakin ingin keluar dari data panel admin?')" class="text-sm text-red-400 hover:text-red-300 font-medium flex items-center gap-2 py-2 px-4 rounded hover:bg-slate-800/50 transition duration-200">
                    <span>🚪</span> Keluar Panel
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-y-auto">
            <main class="p-6 space-y-6">
                
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Galeri Dokumentasi Kegiatan</h2>
                </div>

                <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
                    <div class="p-4 bg-green-50 border border-green-200 text-green-800 text-sm font-medium rounded-xl">✨ Foto dokumentasi baru berhasil diterbitkan ke halaman web klien!</div>
                <?php elseif (isset($_GET['status']) && $_GET['status'] == 'terhapus'): ?>
                    <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm font-medium rounded-xl">🗑️ Foto galeri telah dihapus permanen dari basis data dan server.</div>
                <?php elseif ($notifikasi == 'ekstensi_salah'): ?>
                    <div class="p-4 bg-red-50 border border-red-200 text-red-800 text-sm font-medium rounded-xl">❌ Format file salah! Sistem hanya menerima tipe gambar JPG, JPEG, PNG, atau WEBP.</div>
                <?php endif; ?>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 lg:col-span-1">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span>📥</span> Unggah Foto Baru
                        </h3>
                        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Judul Kegiatan / Foto</label>
                                <input type="text" name="judul_foto" required placeholder="Contoh: Upacara Hari Kemerdekaan" class="w-full text-sm border border-gray-200 bg-gray-50 px-3 py-2.5 rounded-lg focus:outline-none focus:border-slate-800 focus:bg-white transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Deskripsi Singkat Momen</label>
                                <textarea name="deskripsi" rows="3" placeholder="Tuliskan keterangan singkat mengenai kegiatan ini..." class="w-full text-sm border border-gray-200 bg-gray-50 px-3 py-2.5 rounded-lg focus:outline-none focus:border-slate-800 focus:bg-white transition resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Pilih File Gambar</label>
                                <input type="file" name="file_foto" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            </div>
                            <button type="submit" name="upload_galeri" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-3 rounded-lg shadow transition uppercase tracking-wider">
                                Publikasikan Foto
                            </button>
                        </form>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 lg:col-span-2 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-base font-bold text-gray-800">Daftar Koleksi Galeri Saat Ini</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                        <th class="p-4 w-24">Media Foto</th>
                                        <th class="p-4">Keterangan Kegiatan</th>
                                        <th class="p-4 w-32 text-center">Aksi Kendali</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    <?php while ($row = mysqli_fetch_assoc($query_galeri)): ?>
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="p-4 align-top">
                                            <img src="uploads/<?= $row['file_foto']; ?>" class="w-20 h-16 object-cover rounded-lg border border-gray-100 shadow-sm" alt="Media">
                                        </td>
                                        <td class="p-4 align-top space-y-1">
                                            <h4 class="font-bold text-gray-900"><?= htmlspecialchars($row['judul_foto']); ?></h4>
                                            <p class="text-xs text-gray-400 leading-relaxed"><?= htmlspecialchars($row['deskripsi']); ?></p>
                                            <div class="text-[10px] text-gray-400 pt-1">
                                                <span>👤 <?= htmlspecialchars($row['nama_lengkap']); ?></span> &bull; 
                                                <span>📅 <?= date('d M Y', strtotime($row['created_at'])); ?></span>
                                            </div>
                                        </td>
                                        <td class="p-4 align-top text-center">
                                            <a href="admin-galeri.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus foto dokumentasi ini dari server secara permanen?')" class="inline-block bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-900 font-semibold text-xs px-3 py-1.5 rounded-lg border border-red-200 transition">
                                                🗑️ Hapus
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if (mysqli_num_rows($query_galeri) == 0): ?>
                                    <tr>
                                        <td colspan="3" class="p-8 text-center text-gray-400 italic">Belum ada dokumentasi foto kegiatan sekolah yang diunggah.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-ml-64');
        }
    </script>
</body>
</html>