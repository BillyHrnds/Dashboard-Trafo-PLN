<?php
session_start();
include 'koneksi.php';

// 1. CEK KEAMANAN: Apakah sudah login?
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){ 
    header("Location: login_awal.php"); 
    exit; 
}

// 2. CEK ROLE: Apakah dia Admin?
// Jika Tamu mencoba akses file ini manual lewat URL, tolak dan lempar balik.
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){ 
    echo "<script>
            alert('AKSES DITOLAK! Hanya Petugas Admin yang boleh menghapus data.'); 
            window.location='index.php';
          </script>"; 
    exit; 
}

// 3. PROSES HAPUS DATA
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Karena di database kita sudah set 'ON DELETE CASCADE', 
    // Cukup hapus Induknya (tb_gardu), maka anak-anaknya (tb_pengukuran) ikut terhapus otomatis.
    $query = "DELETE FROM tb_gardu WHERE id = '$id'";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>
                alert('Data Gardu dan seluruh riwayat pengukurannya BERHASIL DIHAPUS!'); 
                window.location = 'index.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal Menghapus: " . mysqli_error($koneksi) . "'); 
                window.location = 'index.php';
              </script>";
    }
} else {
    // Jika file dibuka tanpa ID, kembalikan ke index
    header("Location: index.php");
}
?>