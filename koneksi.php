<?php
$host     = "localhost";
$username = "root"; // sesuaikan dengan username database kamu
$password = "";     // sesuaikan dengan password database kamu
$database = "baitis_salmah_db";

$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>