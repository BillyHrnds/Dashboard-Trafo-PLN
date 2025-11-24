<?php
$koneksi = mysqli_connect("localhost", "root", "", "db_trafo");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
