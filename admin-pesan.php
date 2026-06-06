<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// 1. PROSES UBAH STATUS BACA
if (isset($_GET['baca'])) {
    $id = intval($_GET['baca']);
    mysqli_query($koneksi, "UPDATE kontak_pesan SET status_baca = 'sudah' WHERE id = $id");
    header("Location: admin-pesan.php");
    exit;
}

// 2. PROSES HAPUS PESAN MASUK
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM kontak_pesan WHERE id = $id");
    header("Location: admin-pesan.php?status=sukses_hapus");
    exit;
}

// Ambil seluruh korespondensi pesan berdasarkan nama kolom tabel baru
$query_pesan = mysqli_query($koneksi, "SELECT * FROM kontak_pesan ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kotak Masuk Pesan - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .safari-icon {
            width: 20px; height: 16px; border: 2px solid #475569; border-radius: 3px; position: relative; display: inline-block;
        }
        .safari-icon::before {
            content: ""; position: absolute; top: 0; left: 5px; bottom: 0; width: 2px; background-color: #475569;
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
                    <a href="admin-galeri.php" class="block py-2.5 px-4 rounded text-slate-400 hover:bg-slate-800 hover:text-white transition duration-200">🖼️ Kelola Galeri</a>
                    <a href="admin-pesan.php" class="block py-2.5 px-4 rounded bg-amber-500 text-slate-950 font-semibold shadow-md shadow-amber-500/10 transition duration-200">📩 Pesan Masuk</a>
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
                    <h2 class="text-xl font-bold text-gray-800">Kotak Surat & Pesan Pengunjung</h2>
                </div>

                <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses_hapus'): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm font-medium">🗑️ Rekaman data korespondensi pesan berhasil dihapus secara permanen.</div>
                <?php endif; ?>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="p-4 w-52">Identitas Pengirim</th>
                                    <th class="p-4">Subjek & Intisari Pesan Masuk</th>
                                    <th class="p-4 text-center w-40">Tindakan</th>
                                    <th class="p-4 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <?php while($row = mysqli_fetch_assoc($query_pesan)): ?>
                                <tr class="hover:bg-gray-50/50 transition <?= ($row['status_baca'] == 'belum') ? 'bg-amber-50/30 font-medium' : ''; ?>">
                                    <td class="p-4 align-top">
                                        <h4 class="font-bold text-gray-800"><?= htmlspecialchars($row['nama_pengirim']); ?></h4>
                                        <p class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($row['email']); ?></p>
                                        <span class="text-[10px] text-gray-400 block mt-2 font-normal"><i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($row['created_at'])); ?></span>
                                    </td>
                                    <td class="p-4 align-top">
                                        <div class="flex items-center gap-2 mb-1">
                                            <?php if($row['status_baca'] == 'belum'): ?>
                                                <span class="text-[9px] font-bold tracking-wider bg-amber-100 border border-amber-200 text-amber-800 px-1.5 py-0.5 rounded uppercase">Baru</span>
                                            <?php else: ?>
                                                <span class="text-[9px] font-bold tracking-wider bg-gray-100 border text-gray-400 px-1.5 py-0.5 rounded uppercase">Terbaca</span>
                                            <?php endif; ?>
                                            <span class="font-bold text-gray-700"><?= htmlspecialchars($row['subjek']); ?></span>
                                        </div>
                                        <p class="text-gray-500 text-xs line-clamp-2 leading-relaxed"><?= htmlspecialchars($row['isi_pesan']); ?></p>
                                    </td>
                                    <td class="p-4 align-top text-center">
                                        <button onclick="bukaModal(<?= $row['id']; ?>, '<?= addslashes($row['nama_pengirim']); ?>', '<?= addslashes($row['email']); ?>', '<?= addslashes($row['subjek']); ?>', '<?= addslashes($row['isi_pesan']); ?>', '<?= $row['status_baca']; ?>')" class="bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm transition">🔎 Detail & Baca</button>
                                        <?php if($row['status_baca'] == 'belum'): ?>
                                            <a href="admin-pesan.php?baca=<?= $row['id']; ?>" class="block mt-2 text-[11px] text-amber-700 hover:underline font-bold">✔ Tandai Sudah Dibaca</a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 align-top text-center">
                                        <a href="admin-pesan.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data pesan masuk ini secara permanen?')" class="inline-block bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-900 font-semibold text-xs px-3 py-1.5 rounded-lg border border-red-200 transition">🗑️ Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if (mysqli_num_rows($query_pesan) == 0): ?>
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-gray-400 italic">Kotak masuk masih kosong. Belum ada pesan masuk.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="modalPesan" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[999] flex items-center justify-center hidden p-4">
        <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border overflow-hidden flex flex-col transform scale-95 transition-all duration-300">
            <div class="bg-slate-900 px-6 py-4 text-white flex justify-between items-center">
                <h3 class="font-bold text-sm tracking-wide uppercase">📋 Ringkasan Maklumat Pesan</h3>
                <button onclick="tutupModal()" class="text-slate-400 hover:text-white font-bold text-lg">&times;</button>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto max-h-[70vh] text-sm">
                <div class="border-b pb-3">
                    <span class="text-xs font-bold text-gray-400 uppercase">Subjek Maklumat</span>
                    <h4 id="modalSubjek" class="text-base font-black text-gray-800 mt-0.5"></h4>
                </div>
                <div class="grid grid-cols-2 gap-4 border-b pb-3">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase">Nama Pengirim</span>
                        <p id="modalNama" class="font-semibold text-gray-800 mt-0.5"></p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase">Alamat Korespondensi (Email)</span>
                        <p id="modalEmail" class="font-semibold text-gray-600 mt-0.5 break-all"></p>
                    </div>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase">Isi Pesan Surat</span>
                    <p id="modalIsi" class="text-gray-600 leading-relaxed mt-1.5 bg-gray-50 border p-4 rounded-xl whitespace-pre-line text-justify"></p>
                </div>
            </div>
            <div class="p-4 border-t bg-gray-50 flex justify-end">
                <button onclick="tutupModal()" class="bg-gray-800 hover:bg-gray-900 text-white font-semibold text-xs px-5 py-2.5 rounded-xl shadow transition">Tutup Keluar</button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalPesan');
        let refreshOnClose = false;

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-ml-64');
        }

        function bukaModal(id, nama, email, subjek, isi, status) {
            document.getElementById('modalSubjek').innerText = subjek;
            document.getElementById('modalNama').innerText = nama;
            document.getElementById('modalEmail').innerText = email;
            document.getElementById('modalIsi').innerText = isi;
            modal.classList.remove('hidden');

            if (status === 'belum') {
                refreshOnClose = true;
                // Mengirim permintaan asinkron di latar belakang untuk merubah status baca menjadi 'sudah'
                fetch('admin-pesan.php?baca=' + id);
            }
        }

        function tutupModal() {
            modal.classList.add('hidden');
            if (refreshOnClose) {
                window.location.reload();
            }
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                tutupModal();
            }
        }
    </script>
</body>
</html>