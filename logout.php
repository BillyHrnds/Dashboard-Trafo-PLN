<?php
session_start();

// 1. Kosongkan semua variabel session
$_SESSION = [];

// 2. Hapus session dari memori server
session_destroy();

// 3. Arahkan kembali ke Halaman Login Awal (Gerbang Depan)
header("Location: login_awal.php");
exit;
?>