<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// PROSES TAMBAH BERITA + DOUBLE UPLOAD (FOTO & PDF) SESUAI SKEMA TERBARU
if (isset($_POST['tambah_berita'])) {
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $konten   = mysqli_real_escape_string($koneksi, $_POST['konten']);
    $kategori = $_POST['kategori']; // Mengambil value huruf kecil: berita, pengumuman, atau prestasi
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 1; 
    
    // Membuat URL Slug Otomatis
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul)));

    $nama_foto_baru = null;
    $nama_pdf_baru  = null;

    // 1. Handler Upload FOTO
    if (!empty($_FILES['foto']['name'])) {
        $foto_name = $_FILES['foto']['name'];
        $foto_tmp  = $_FILES['foto']['tmp_name'];
        $foto_ext  = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));
        
        if (in_array($foto_ext, ['jpg', 'jpeg', 'png'])) {
            $nama_foto_baru = "img_" . time() . "_" . rand(100,999) . "." . $foto_ext;
            move_uploaded_file($foto_tmp, "uploads/" . $nama_foto_baru);
        }
    }

    // 2. Handler Upload PDF
    if (!empty($_FILES['file_pdf']['name'])) {
        $pdf_name = $_FILES['file_pdf']['name'];
        $pdf_tmp  = $_FILES['file_pdf']['tmp_name'];
        $pdf_ext  = strtolower(pathinfo($pdf_name, PATHINFO_EXTENSION));
        
        if ($pdf_ext == 'pdf') {
            $nama_pdf_baru = "doc_" . time() . "_" . rand(100,999) . "." . $pdf_ext;
            move_uploaded_file($pdf_tmp, "uploads/" . $nama_pdf_baru);
        }
    }

    // Kueri disesuaikan dengan struktur ENUM lowercase dan status default 'publish'
    $query = "INSERT INTO berita (judul, slug, konten, foto, file_pdf, kategori, admin_id, status) 
              VALUES ('$judul', '$slug', '$konten', '$nama_foto_baru', '$nama_pdf_baru', '$kategori', '$admin_id', 'publish')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin-berita.php?status=sukses_tambah");
        exit;
    }
}

// PROSES HAPUS BERITA
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    
    $res_file = mysqli_query($koneksi, "SELECT foto, file_pdf FROM berita WHERE id = $id");
    if ($data_file = mysqli_fetch_assoc($res_file)) {
        if (!empty($data_file['foto']) && file_exists("uploads/" . $data_file['foto'])) {
            unlink("uploads/" . $data_file['foto']);
        }
        if (!empty($data_file['file_pdf']) && file_exists("uploads/" . $data_file['file_pdf'])) {
            unlink("uploads/" . $data_file['file_pdf']);
        }
    }

    mysqli_query($koneksi, "DELETE FROM berita WHERE id = $id");
    header("Location: admin-berita.php?status=sukses_hapus");
    exit;
}

$query_berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita Sekolah - Admin Panel</title>
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
                    <a href="admin-berita.php" class="block py-2.5 px-4 rounded bg-amber-500 text-slate-950 font-semibold shadow-md shadow-amber-500/10 transition duration-200">📰 Kelola Berita</a>
                    <a href="admin-ekskul.php" class="block py-2.5 px-4 rounded text-slate-400 hover:bg-slate-800 hover:text-white transition duration-200">🏆 Kelola Ekskul</a>
                    <a href="admin-galeri.php" class="block py-2.5 px-4 rounded text-slate-400 hover:bg-slate-800 hover:text-white transition duration-200">🖼️ Kelola Galeri</a>
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
                    <h2 class="text-xl font-bold text-gray-800">Manajemen Pengumuman & Berita</h2>
                </div>

                <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses_tambah'): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl text-sm font-medium">✨ Artikel berita baru telah berhasil dipublikasikan!</div>
                <?php elseif (isset($_GET['status']) && $_GET['status'] == 'sukses_hapus'): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm font-medium">🗑️ Artikel berita beserta file fisiknya telah dihapus.</div>
                <?php endif; ?>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="text-base font-bold text-gray-800 mb-4">✍️ Tulis Berita Baru</h3>
                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Judul Artikel</label>
                                <input type="text" name="judul" required placeholder="Ketikkan judul utama berita..." class="w-full text-sm px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-amber-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kategori Berita</label>
                                <select name="kategori" required class="w-full text-sm px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-amber-500 outline-none transition">
                                    <option value="berita">Berita</option>
                                    <option value="pengumuman">Pengumuman</option>
                                    <option value="prestasi">Prestasi</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Gambar Sampul</label>
                                <input type="file" name="foto" accept="image/*" class="w-full text-xs px-2 py-2 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Lampiran Tambahan (PDF)</label>
                                <input type="file" name="file_pdf" accept=".pdf" class="w-full text-xs px-2 py-2 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Isi Konten Berita</label>
                            <textarea name="konten" rows="6" required placeholder="Ketikkan pokok pembahasan tulisan di sini..." class="w-full text-sm px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-amber-500 outline-none transition resize-none"></textarea>
                        </div>
                        <button type="submit" name="tambah_berita" class="w-full py-2.5 bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white text-xs font-bold rounded-xl shadow transition tracking-wider uppercase">Terbitkan Berita Sekarang</button>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-800">Daftar Arsip Berita Sekolah</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="p-4 w-32">Sampul</th>
                                    <th class="p-4">Detail Konten Berita</th>
                                    <th class="p-4 w-36">Lampiran PDF</th>
                                    <th class="p-4 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <?php while($row = mysqli_fetch_assoc($query_berita)): ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4 align-top">
                                        <?php if (!empty($row['foto'])): ?>
                                            <div class="w-24 h-16 rounded-lg overflow-hidden border border-gray-200">
                                                <img src="uploads/<?= $row['foto']; ?>" class="w-full h-full object-cover">
                                            </div>
                                        <?php else: ?>
                                            <span class="text-[10px] text-gray-400 bg-gray-50 border px-2 py-1 rounded block text-center italic">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 align-top">
                                        <div class="flex gap-2 items-center">
                                            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 border border-amber-100 rounded"><?= ucfirst(htmlspecialchars($row['kategori'])); ?></span>
                                            <?php if(isset($row['status']) && $row['status'] == 'draft'): ?>
                                                <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 border rounded">Draft</span>
                                            <?php endif; ?>
                                        </div>
                                        <h4 class="font-bold text-gray-800 text-base mt-1.5"><?= htmlspecialchars($row['judul']); ?></h4>
                                        <p class="text-gray-500 text-xs mt-1 leading-relaxed line-clamp-2"><?= strip_tags($row['konten']); ?></p>
                                    </td>
                                    <td class="p-4 align-top">
                                        <?php if (!empty($row['file_pdf'])): ?>
                                            <a href="uploads/<?= $row['file_pdf']; ?>" target="_blank" class="inline-flex items-center text-xs text-emerald-600 font-semibold bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg hover:bg-emerald-100 transition">📄 Lihat PDF</a>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400 italic">Tidak ada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 align-top text-center">
                                        <a href="admin-berita.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Apakah Anda yakin hendak menghapus permanen berita publikasi ini?')" class="inline-block bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-900 font-semibold text-xs px-3 py-1.5 rounded-lg border border-red-200 transition">🗑️ Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if (mysqli_num_rows($query_berita) == 0): ?>
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-gray-400 italic">Belum ada artikel berita yang dipublikasikan dalam sistem.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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