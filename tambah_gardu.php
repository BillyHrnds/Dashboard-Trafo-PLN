<?php
session_start();
include 'koneksi.php';

// 1. CEK KEAMANAN
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){ header("Location: login_awal.php"); exit; }
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){ 
    echo "<script>alert('AKSES DITOLAK!'); window.location='index.php';</script>"; exit; 
}

// VARIABEL SYSTEM
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin');
$namaUser = $isAdmin ? 'Admin TJ Balai' : 'Pengunjung';
$roleUser = $isAdmin ? 'Administrator' : 'Guest View';

// 2. PROSES SIMPAN
if (isset($_POST['simpan'])) {
    $no_gardu   = mysqli_real_escape_string($koneksi, $_POST['no_gardu']);
    $alamat     = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $posko      = mysqli_real_escape_string($koneksi, $_POST['posko']);
    $penyulang  = mysqli_real_escape_string($koneksi, $_POST['penyulang']);
    $merk       = mysqli_real_escape_string($koneksi, $_POST['merk']);
    $daya       = $_POST['daya_trafo'];
    $no_seri    = mysqli_real_escape_string($koneksi, $_POST['nomor_seri']);
    $tiang      = $_POST['tiang_trafo'];
    $koordinat  = mysqli_real_escape_string($koneksi, $_POST['koordinat']);

    $cek = mysqli_query($koneksi, "SELECT id FROM tb_gardu WHERE no_gardu = '$no_gardu'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Nomor Gardu '$no_gardu' sudah terdaftar!";
    } else {
        $query = "INSERT INTO tb_gardu (no_gardu, alamat, posko, penyulang, merk, daya_trafo, nomor_seri, tiang_trafo, koordinat) 
                  VALUES ('$no_gardu', '$alamat', '$posko', '$penyulang', '$merk', '$daya', '$no_seri', '$tiang', '$koordinat')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Data Berhasil Disimpan!'); window.location='index.php';</script>";
        } else { $error = "Gagal menyimpan: " . mysqli_error($koneksi); }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Master Gardu</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        #mapPicker { height: 350px; width: 100%; border-radius: 12px; border: 2px solid var(--border-color); z-index: 1; }
        .form-section-title { 
            color: var(--primary-color); 
            font-weight: 700; 
            font-size: 0.9rem; 
            border-bottom: 1px solid var(--border-color); 
            padding-bottom: 10px; 
            margin: 25px 0 15px 0; 
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    
    <aside id="sidebar" class="sidebar">
    <div class="sidebar-header">
    <a href="index.php" class="logo text-decoration-none"> 
        <i class="fas fa-bolt fa-lg"></i>
        <span class="logo-text">PLN TRAFO</span>
    </a>
    <button id="sidebarToggle" class="sidebar-toggle"><i class="fas fa-times"></i></button>
</div>
        <nav class="sidebar-nav p-3">
            <ul class="nav-menu">
                <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li class="nav-item"><a href="map_view.php" class="nav-link"><i class="fas fa-map-marked-alt"></i> <span>Peta Sebaran</span></a></li>
                <li class="nav-item"><a href="export_excel.php" class="nav-link"><i class="fas fa-file-excel"></i> <span>Export Excel</span></a></li>
                
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <li style="margin: 1.5rem 1rem 0.5rem; font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Menu Petugas</li>
                <li class="nav-item active">
                    <a href="tambah_gardu.php" class="nav-link"><i class="fas fa-plus-circle"></i> <span>Tambah Gardu</span></a>
                </li>
                <li class="nav-item"><a href="input_pengukuran.php" class="nav-link"><i class="fas fa-calculator"></i> <span>Input Ukur</span></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="sidebar-footer p-3 mt-auto border-top border-white border-opacity-10">
            <div class="user-profile d-flex align-items-center gap-3">
                
                <div class="user-info"><div class="user-name fw-bold"><?= $namaUser ?></div><div class="user-role small opacity-75"><?= $roleUser ?></div></div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <header class="header">
            <div class="header-left">
                <button id="mobileToggle" class="mobile-toggle"><i class="fas fa-bars"></i></button>
                <div class="header-text">
                    <h1 class="page-title">Input Master Gardu</h1>
                    <p class="page-subtitle">Tambah data aset baru ke database.</p>
                </div>
            </div>
            <div class="header-right">
                <button id="themeToggle" class="action-btn theme-toggle" title="Toggle Theme"><i class="fas fa-moon"></i></button>
                <a href="index.php" class="action-btn" title="Kembali"><i class="fas fa-arrow-left"></i></a>
            </div>
        </header>

        <div class="dashboard-content">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4"><i class="fas fa-exclamation-circle me-2"></i> <?= $error ?></div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm" style="background: var(--bg-card); color: var(--text-primary); border-radius: 15px;">
                <div class="card-body p-4">
                    <form action="" method="POST">
                        
                        <h6 class="form-section-title">A. Identitas Gardu</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">No. Gardu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" name="no_gardu" class="form-control fw-bold">
                                </div>
                            </div>
                            <div class="col-md-4"><label class="form-label small fw-bold text-muted">Posko</label><input type="text" name="posko" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label small fw-bold text-muted">Penyulang</label><input type="text" name="penyulang" class="form-control"></div>
                        </div>
                        <div class="mb-4"><label class="form-label small fw-bold text-muted">Alamat</label><textarea name="alamat" class="form-control" rows="2"></textarea></div>

                        <h6 class="form-section-title">B. Spesifikasi</h6>
                        <div class="row mb-4">
                            <div class="col-md-3"><label class="form-label small fw-bold text-muted">Merk</label><input type="text" name="merk" class="form-control"></div>
                            <div class="col-md-3"><label class="form-label small fw-bold text-muted">Daya (kVA)</label><input type="number" name="daya_trafo" class="form-control fw-bold" required></div>
                            <div class="col-md-3"><label class="form-label small fw-bold text-muted">No Seri</label><input type="text" name="nomor_seri" class="form-control"></div>
                            <div class="col-md-3"><label class="form-label small fw-bold text-muted">Tiang</label>
                                <select name="tiang_trafo" class="form-select">
                                    <option value="SINGLE POLE">SINGLE POLE</option>
                                    <option value="DOUBLE POLE">DOUBLE POLE</option>
                                    <option value="TRIPLE POLE">TRIPLE POLE</option>
                                </select>
                            </div>
                        </div>

                        <h6 class="form-section-title">C. Lokasi</h6>
                        <div class="row g-3">
                            <div class="col-md-12 position-relative">
                                <button type="button" onclick="getLocation()" class="btn btn-sm btn-light position-absolute top-0 end-0 m-3 shadow-sm fw-bold" style="z-index: 9999; border:1px solid #ccc; cursor:pointer;">
                                    <i class="fas fa-crosshairs text-primary me-1"></i> GPS
                                </button>
                                <div id="mapPicker"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small text-muted">Koordinat</label>
                                <input type="text" name="koordinat" id="latlong" class="form-control fw-bold" readonly required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: var(--border-color) !important;">
                            <button type="submit" name="simpan" class="btn text-white px-5 fw-bold shadow-sm" style="background: var(--primary-gradient); border:none;">SIMPAN</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
<script>
    // PETA
    var defaultLat = 2.963807; 
    var defaultLng = 99.801371;
    var map = L.map('mapPicker').setView([defaultLat, defaultLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '© OpenStreetMap'}).addTo(map);
    var marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);
    
    marker.on('dragend', function (e) {
        var pos = marker.getLatLng();
        document.getElementById('latlong').value = pos.lat.toFixed(6) + ", " + pos.lng.toFixed(6);
    });
    map.on('click', function(e){
        marker.setLatLng(e.latlng);
        document.getElementById('latlong').value = e.latlng.lat.toFixed(6) + ", " + e.latlng.lng.toFixed(6);
    });
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos){
                var lat = pos.coords.latitude; var lng = pos.coords.longitude;
                map.setView([lat, lng], 18); marker.setLatLng([lat, lng]);
                document.getElementById('latlong').value = lat.toFixed(6) + ", " + lng.toFixed(6);
            });
        }
    }
    document.getElementById('latlong').value = defaultLat + ", " + defaultLng;
</script>
</body>
</html>