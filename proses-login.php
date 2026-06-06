<?php
// 1. Jalankan session PHP
session_start();

// 2. Hubungkan ke database
include 'koneksi.php';

if (isset($_POST['login'])) {
    
    // 3. Amankan input Username dari serangan SQL Injection
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = $_POST['password']; // Teks biasa dari form login

    // 4. Cari data admin berdasarkan username
    $query  = "SELECT * FROM admin WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // 5. Verifikasi Password teks biasa dengan password ter-hash di database kamu
        if (password_verify($password, $row['password'])) {
            
            // Jika benar, buat Session untuk mengunci dashboard
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $row['id'];
            $_SESSION['admin_username']  = $row['username'];
            $_SESSION['admin_nama']      = $row['nama_lengkap'];
            $_SESSION['admin_role']      = $row['role'];

            // Alihkan ke halaman dashboard utama kamu
            header("Location: dashboard.php");
            exit;
        }
    }

    // 6. Jika username tidak ada atau password salah, kembalikan ke login dengan status error
    // Pesan disamarkan demi keamanan agar peretas tidak tahu mana yang salah
    header("Location: login.php?error=1");
    exit;

} else {
    // Jika coba akses file ini langsung via URL, tendang balik ke form login
    header("Location: login.php");
    exit;
}
?>