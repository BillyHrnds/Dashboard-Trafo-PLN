<?php
include 'koneksi.php';

$user = "tjbalai";
$pass = "12345";
$hash = password_hash($pass, PASSWORD_DEFAULT);

// 1. Hapus user lama biar bersih
mysqli_query($koneksi, "DELETE FROM tb_admin WHERE username='$user'");

// 2. Masukkan user baru
$query = "INSERT INTO tb_admin (username, password) VALUES ('$user', '$hash')";

if(mysqli_query($koneksi, $query)){
    echo "<h1>SUKSES!</h1>";
    echo "User: <b>$user</b><br>";
    echo "Pass: <b>$pass</b><br>";
    echo "Silakan coba login lagi.";
} else {
    echo "Gagal: " . mysqli_error($koneksi);
}
?>