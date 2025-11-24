<?php
include 'koneksi.php';

if(isset($_POST['id_pengukuran'])) {
    $id = $_POST['id_pengukuran'];

    // Ambil data pengukuran SPESIFIK berdasarkan ID-nya
    $query = "SELECT p.*, g.daya_trafo 
              FROM tb_pengukuran p 
              JOIN tb_gardu g ON p.gardu_id = g.id 
              WHERE p.id = '$id'";
              
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);

    if($data) {
        echo json_encode($data);
    } else {
        echo json_encode(null);
    }
}
?>