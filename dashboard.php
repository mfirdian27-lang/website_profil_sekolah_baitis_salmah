<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// Hitung statistik menggunakan nama tabel asli dari database Anda
$total_berita = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM berita"));
$total_ekskul = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM ekstrakurikuler"));
$total_pesan  = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM kontak_pesan"));
$total_galeri = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM galeri"));
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
                    <a href="dashboard.php" class="block py-2.5 px-4 rounded bg-amber-500 text-slate-950 font-semibold shadow-md shadow-amber-500/10 transition duration-200">📊 Dashboard</a>
                    <a href="admin-berita.php" class="block py-2.5 px-4 rounded text-slate-400 hover:bg-slate-800 hover:text-white transition duration-200">📰 Kelola Berita</a>
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
                    <h2 class="text-xl font-bold text-gray-800">Ringkasan Sistem Sekolah</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Artikel Berita</p>
                            <h3 class="text-3xl font-black text-slate-800 mt-1"><?= $total_berita; ?></h3>
                        </div>
                        <div class="text-3xl bg-blue-50 p-3 rounded-xl text-blue-600">📰</div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Program Ekskul</p>
                            <h3 class="text-3xl font-black text-slate-800 mt-1"><?= $total_ekskul; ?></h3>
                        </div>
                        <div class="text-3xl bg-green-50 p-3 rounded-xl text-green-600">🏆</div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Koleksi Galeri</p>
                            <h3 class="text-3xl font-black text-slate-800 mt-1"><?= $total_galeri; ?></h3>
                        </div>
                        <div class="text-3xl bg-purple-50 p-3 rounded-xl text-purple-600">🖼️</div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pesan Masuk</p>
                            <h3 class="text-3xl font-black text-slate-800 mt-1"><?= $total_pesan; ?></h3>
                        </div>
                        <div class="text-3xl bg-amber-50 p-3 rounded-xl text-amber-600">📩</div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="text-base font-bold text-gray-800 mb-2">Petunjuk Manajemen Konten</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Gunakan menu di bilah sebelah kiri untuk mengelola informasi sekolah. Anda dapat menambah, mengubah, atau menghapus artikel berita dan daftar kegiatan ekstrakurikuler siswa beserta dokumentasi fotonya secara langsung.
                    </p>
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