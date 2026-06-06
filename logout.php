<?php
session_start();
// Hapus semua data session
session_unset();
session_destroy();

// Pindahkan kembali ke halaman beranda
header("Location: index.php");
exit;
?>