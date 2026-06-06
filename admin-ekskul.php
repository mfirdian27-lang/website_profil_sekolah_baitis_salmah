<?php
session_start();
// Proteksi Halaman Dashboard Admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// ==========================================
// 1. PROSES TAMBAH EKSTRAKURIKULER
// ==========================================
if (isset($_POST['tambah_ekskul'])) {
    $nama_ekskul = mysqli_real_escape_string($koneksi, $_POST['nama_ekskul']);
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    $query = "INSERT INTO ekstrakurikuler (nama_ekskul, deskripsi) VALUES ('$nama_ekskul', '$deskripsi')";
    if (mysqli_query($koneksi, $query)) {
        header("Location: admin-ekskul.php?status=sukses_ekskul");
        exit;
    }
}

// ==========================================
// 2. PROSES UPLOAD FOTO DOKUMENTASI EKSKUL
// ==========================================
if (isset($_POST['upload_foto'])) {
    $ekstrakurikuler_id = intval($_POST['ekstrakurikuler_id']);
    $keterangan         = mysqli_real_escape_string($koneksi, $_POST['keterangan']);

    // Handler File Image
    $filename = $_FILES['file_foto']['name'];
    $tmp_name = $_FILES['file_foto']['tmp_name'];
    
    if (!empty($filename)) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Validasi Ekstensi
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            // Generate nama file unik baru
            $nama_foto_baru = "ekskul_" . time() . "_" . rand(100, 999) . "." . $ext;
            $target_dir     = "uploads/";

            if (move_uploaded_file($tmp_name, $target_dir . $nama_foto_baru)) {
                // Simpan nama file ke tabel: foto_ekstrakurikuler
                $query_foto = "INSERT INTO foto_ekstrakurikuler (ekstrakurikuler_id, file_foto, keterangan) 
                               VALUES ($ekstrakurikuler_id, '$nama_foto_baru', '$keterangan')";
                
                if (mysqli_query($koneksi, $query_foto)) {
                    header("Location: admin-ekskul.php?status=sukses_foto");
                    exit;
                }
            }
        }
    }
    header("Location: admin-ekskul.php?status=gagal_foto");
    exit;
}

// ==========================================
// 3. PROSES HAPUS EKSTRAKURIKULER beserta Fotonya
// ==========================================
if (isset($_GET['hapus_ekskul'])) {
    $id = intval($_GET['hapus_ekskul']);

    // Ambil semua file foto terkait ekskul ini untuk dihapus dari folder uploads
    $query_file = mysqli_query($koneksi, "SELECT file_foto FROM foto_ekstrakurikuler WHERE ekstrakurikuler_id = $id");
    while ($f = mysqli_fetch_assoc($query_file)) {
        $path_file = "uploads/" . $f['file_foto'];
        if (file_exists($path_file)) {
            unlink($path_file); // Hapus file fisik gambar
        }
    }

    // Hapus data dari database
    mysqli_query($koneksi, "DELETE FROM ekstrakurikuler WHERE id = $id");
    header("Location: admin-ekskul.php?status=sukses_hapus");
    exit;
}

// ==========================================
// 4. PROSES HAPUS SATU FOTO MANDIRI (Fitur Baru)
// ==========================================
if (isset($_GET['hapus_foto'])) {
    $foto_id = intval($_GET['hapus_foto']);

    // Ambil data nama file gambar terlebih dahulu sebelum dihapus dari DB
    $query_foto_single = mysqli_query($koneksi, "SELECT file_foto FROM foto_ekstrakurikuler WHERE id = $foto_id");
    $data_foto = mysqli_fetch_assoc($query_foto_single);

    if ($data_foto) {
        $path_file_single = "uploads/" . $data_foto['file_foto'];
        if (file_exists($path_file_single)) {
            unlink($path_file_single); // Hapus file fisik dari direktori server
        }
        
        // Hapus baris data foto dari tabel database
        mysqli_query($koneksi, "DELETE FROM foto_ekstrakurikuler WHERE id = $foto_id");
        header("Location: admin-ekskul.php?status=sukses_hapus_foto");
        exit;
    }
    header("Location: admin-ekskul.php?status=gagal_hapus_foto");
    exit;
}

// Ambil data untuk ditampilkan di tabel
$query_ekskul = mysqli_query($koneksi, "SELECT * FROM ekstrakurikuler ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ekstrakurikuler - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom gaya ikon jendela Safari menggunakan utility border CSS (Persis dashboard.php) */
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
            <button onclick="toggleSidebar()" class="p-2 hover:bg-gray-100 rounded-lg transition duration-200 flex items-center justify-center" aria-label="Toggle Sidebar">
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
                    <a href="admin-ekskul.php" class="block py-2.5 px-4 rounded bg-amber-500 text-slate-950 font-semibold shadow-md shadow-amber-500/10 transition duration-200">🏆 Kelola Ekskul</a>
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
                    <h2 class="text-xl font-bold text-gray-800">Manajemen Ekstrakurikuler</h2>
                </div>

                <?php if (isset($_GET['status'])): ?>
                    <?php if ($_GET['status'] == 'sukses_ekskul'): ?>
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl text-sm font-medium">✨ Program ekstrakurikuler baru berhasil ditambahkan ke sistem!</div>
                    <?php elseif ($_GET['status'] == 'sukses_foto'): ?>
                        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-xl text-sm font-medium">📸 Foto dokumentasi berhasil diunggah ke database!</div>
                    <?php elseif ($_GET['status'] == 'sukses_hapus'): ?>
                        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm font-medium">🗑️ Data ekstrakurikuler beserta seluruh dokumentasi foto fisiknya telah dihapus.</div>
                    <?php elseif ($_GET['status'] == 'sukses_hapus_foto'): ?>
                        <div class="bg-teal-50 border border-teal-200 text-teal-800 p-4 rounded-xl text-sm font-medium">🗑️ File foto dokumentasi berhasil dihapus permanen!</div>
                    <?php elseif ($_GET['status'] == 'gagal_foto'): ?>
                        <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-xl text-sm font-medium">⚠️ Gagal mengunggah foto. Pastikan format file adalah JPG, JPEG, PNG, atau WEBP.</div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-fit">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">🏅 Tambah Program Ekskul</h3>
                        <form action="" method="POST" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Kegiatan</label>
                                <input type="text" name="nama_ekskul" required placeholder="Contoh: Paskibra, Pramuka, Hadroh..." class="w-full text-sm px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-amber-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Deskripsi Kegiatan</label>
                                <textarea name="deskripsi" rows="3" required placeholder="Jelaskan mengenai visi, tujuan, atau jadwal singkat kegiatan ini..." class="w-full text-sm px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-amber-500 outline-none transition resize-none"></textarea>
                            </div>
                            <button type="submit" name="tambah_ekskul" class="w-full py-2.5 bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white text-xs font-bold rounded-xl shadow transition tracking-wider uppercase">Simpan Ekskul</button>
                        </form>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-fit">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">📸 Unggah Dokumentasi Foto</h3>
                        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Program Ekskul</label>
                                <select name="ekstrakurikuler_id" required class="w-full text-sm px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-amber-500 outline-none transition">
                                    <option value="">-- Pilih Ekstrakurikuler --</option>
                                    <?php 
                                    $opt_query = mysqli_query($koneksi, "SELECT id, nama_ekskul FROM ekstrakurikuler ORDER BY nama_ekskul ASC");
                                    while($opt = mysqli_fetch_assoc($opt_query)):
                                    ?>
                                        <option value="<?= $opt['id']; ?>"><?= htmlspecialchars($opt['nama_ekskul']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Berkas Gambar (FOTO)</label>
                                    <input type="file" name="file_foto" required class="w-full text-xs px-2 py-2 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Keterangan Singkat</label>
                                    <input type="text" name="keterangan" placeholder="Contoh: Lomba Tingkat Jabar" class="w-full text-sm px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-amber-500 outline-none transition">
                                </div>
                            </div>
                            <button type="submit" name="upload_foto" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold rounded-xl shadow transition tracking-wider uppercase">Unggah Foto Galeri</button>
                        </form>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-800">List Program & Dokumentasi Teraktif</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="p-4">Nama Ekskul & Deskripsi</th>
                                    <th class="p-4 w-96">Dokumentasi Foto (`uploads/`)</th>
                                    <th class="p-4 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <?php while($row = mysqli_fetch_assoc($query_ekskul)): ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4 align-top max-w-sm">
                                        <h4 class="font-bold text-gray-800 text-base"><?= htmlspecialchars($row['nama_ekskul']); ?></h4>
                                        <p class="text-gray-500 text-xs mt-1 leading-relaxed"><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></p>
                                    </td>
                                    <td class="p-4 align-top">
                                        <?php 
                                        $ekskul_id = $row['id'];
                                        $query_galeri = mysqli_query($koneksi, "SELECT id, file_foto, keterangan FROM foto_ekstrakurikuler WHERE ekstrakurikuler_id = $ekskul_id ORDER BY id DESC");
                                        if (mysqli_num_rows($query_galeri) > 0):
                                        ?>
                                            <div class="flex flex-wrap gap-3">
                                                <?php while($img = mysqli_fetch_assoc($query_galeri)): ?>
                                                    <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 shadow-sm relative group" title="<?= htmlspecialchars($img['keterangan']); ?>">
                                                        <img src="uploads/<?= $img['file_foto']; ?>" class="w-full h-full object-cover">
                                                        <a href="admin-ekskul.php?hapus_foto=<?= $img['id']; ?>" onclick="return confirm('Hapus foto dokumentasi ini saja?')" class="absolute top-1 right-1 bg-rose-600 hover:bg-rose-700 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shadow transition opacity-80 hover:opacity-100" title="Hapus Foto Ini">
                                                            ✕
                                                        </a>
                                                    </div>
                                                <?php endwhile; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400 italic bg-gray-50 px-2 py-1 rounded border border-dashed block w-fit">Belum ada dokumentasi foto.</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 align-top text-center">
                                        <a href="admin-ekskul.php?hapus_ekskul=<?= $row['id']; ?>" onclick="return confirm('PERINGATAN: Menghapus ekskul ini akan otomatis menghapus seluruh foto galeri di dalamnya! Lanjutkan?')" class="inline-block bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-900 font-semibold text-xs px-3 py-1.5 rounded-lg border border-red-200 transition">
                                            🗑️ Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
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