<?php
session_start();
include 'koneksi.php';

// 1. CEK KEAMANAN
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("Location: login_awal.php");
    exit;
}

// VARIABEL SYSTEM
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin');
$namaUser = $isAdmin ? 'Admin TJ Balai' : 'Pengunjung';
$roleUser = $isAdmin ? 'Administrator' : 'Guest View';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Sebaran - PLN Trafo</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        #map { height: 75vh; width: 100%; border-radius: 15px; z-index: 1; }
        .leaflet-popup-content-wrapper { border-radius: 10px; padding: 5px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .leaflet-popup-content b { color: #2AA7E1; font-size: 1.1em; }
    </style>
</head>
<body>

<div class="dashboard-container d-flex">
    
    <aside id="sidebar" class="sidebar">
    <div class="sidebar-header">
    <a href="index.php" class="logo text-decoration-none"> 
        <i class="fas fa-bolt fa-lg"></i>
        <span class="logo-text">PLN TRAFO</span>
    </a>
    <button id="sidebarToggle" class="sidebar-toggle"><i class="fas fa-times"></i></button>
</div>

        <nav class="sidebar-nav p-3">
            <?php
            // LOGIKA DETEKSI HALAMAN AKTIF
            $pg = basename($_SERVER['PHP_SELF']);
            
            // REVISI WARNA: BACKGROUND BIRU GRADASI, TEKS PUTIH (Sesuai Gambar)
            $active_style = 'style="background: linear-gradient(135deg, #2AA7E1 0%, #0077B6 100%); color: white !important; font-weight: 700; box-shadow: 0 5px 15px rgba(0,0,0,0.2);"';
            ?>
            
            <ul class="nav flex-column gap-1">
                <li class="nav-item">
                    <a href="index.php" class="nav-link" <?= ($pg=='index.php') ? $active_style : '' ?>>
                        <i class="fas fa-home"></i> <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="map_view.php" class="nav-link" <?= ($pg=='map_view.php') ? $active_style : '' ?>>
                        <i class="fas fa-map-marked-alt"></i> <span>Peta Sebaran</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="export_excel.php" class="nav-link" <?= ($pg=='export_excel.php') ? $active_style : '' ?>>
                        <i class="fas fa-file-excel"></i> <span>Export Excel</span>
                    </a>
                </li>
                
                <?php if($isAdmin): ?>
                <li style="margin: 1.5rem 1rem 0.5rem; font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                    Menu Petugas
                </li>
                
                <li class="nav-item">
                    <a href="tambah_gardu.php" class="nav-link" <?= ($pg=='tambah_gardu.php' || $pg=='edit_gardu.php') ? $active_style : '' ?>>
                        <i class="fas fa-plus-circle"></i> <span>Tambah Gardu</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="input_pengukuran.php" class="nav-link" <?= ($pg=='input_pengukuran.php' || $pg=='edit_pengukuran.php') ? $active_style : '' ?>>
                        <i class="fas fa-calculator"></i> <span>Input Ukur</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="sidebar-footer p-3 mt-auto border-top border-white border-opacity-10">
            <div class="user-profile d-flex align-items-center gap-3">
                
                <div class="user-info">
                    <div class="user-name fw-bold"><?= $namaUser ?></div>
                    <div class="user-role small opacity-75"><?= $roleUser ?></div>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content flex-grow-1">
        <header class="header">
            <div class="header-left">
                <button id="mobileToggle" class="mobile-toggle"><i class="fas fa-bars"></i></button>
                <div class="header-text">
                    <h1 class="page-title">Peta Sebaran Gardu</h1>
                    <p class="page-subtitle">Visualisasi lokasi aset gardu dan statusnya.</p>
                </div>
            </div>
            
            <div class="header-right d-flex gap-3 align-items-center">
                <button id="themeToggle" class="action-btn theme-toggle" title="Toggle Theme"><i class="fas fa-moon"></i></button>
                
                <?php if($isAdmin): ?>
                    <a href="?mode=keluar_admin" class="action-btn bg-warning text-white" title="Kunci Admin" onclick="return confirm('Kunci Akses Admin?')"><i class="fas fa-lock"></i></a>
                <?php else: ?>
                    <button class="btn-admin-login shadow-sm" data-bs-toggle="modal" data-bs-target="#modalLoginAdmin"><i class="fas fa-key me-2"></i> Login Edit</button>
                <?php endif; ?>

                <a href="logout.php" class="action-btn bg-danger text-white" title="Keluar" onclick="return confirm('Keluar?')"><i class="fas fa-power-off"></i></a>
            </div>
        </header>

        <div class="dashboard-content container-fluid">
            <div class="card border-0 shadow-sm" style="background: var(--bg-card); border-radius: 15px;">
                <div class="card-body p-2">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="modalLoginAdmin" tabindex="-1"><div class="modal-dialog modal-sm modal-dialog-centered"><div class="modal-content admin-glass-content rounded-4 overflow-hidden"><div class="modal-header admin-glass-header p-4 border-0 d-flex flex-column align-items-center position-relative"><button type="button" class="btn-close admin-glass-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button><div class="admin-icon-box"><i class="fas fa-user-shield fa-2x text-white"></i></div><h5 class="fw-bold mt-2 mb-0 text-white">Login Petugas</h5><p class="small text-white-50 m-0">Akses Administrator</p></div><div class="modal-body p-4 pt-2"><form method="POST" action="index.php"><div class="mb-3"><input type="text" name="username" class="form-control form-control-lg admin-glass-input" placeholder="Username" required autocomplete="off"></div><div class="mb-4"><input type="password" name="password" class="form-control form-control-lg admin-glass-input" placeholder="Password" required></div><button type="submit" name="login_admin" class="btn w-100 fw-bold text-white py-2 shadow-lg" style="background: var(--primary-gradient); border-radius: 12px; border:none;">MASUK <i class="fas fa-arrow-right ms-2"></i></button></form></div></div></div></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>

<script>
    // INISIALISASI PETA
    var map = L.map('map').setView([-2.965, 99.798], 10); 

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var markers = [];

    <?php
    // Query Data Gardu + Status Terakhir
    $query = mysqli_query($koneksi, "SELECT g.no_gardu, g.alamat, g.koordinat, g.daya_trafo,
             (SELECT ket_lwbp FROM tb_pengukuran WHERE gardu_id = g.id ORDER BY tanggal_ukur DESC LIMIT 1) as status_lwbp 
             FROM tb_gardu g");
    
    while($row = mysqli_fetch_assoc($query)) {
        if(empty($row['koordinat']) || strpos($row['koordinat'], ',') === false) continue;
        
        $statusRaw = $row['status_lwbp'] ?? 'BELUM DIUKUR';
        $colorStyle = "color: #6c757d;";
        if($statusRaw == 'OVERLOAD') $colorStyle = "color: #dc3545; font-weight:bold;";
        if($statusRaw == 'NORMAL') $colorStyle = "color: #198754; font-weight:bold;";
        if($statusRaw == 'UNDERLOAD') $colorStyle = "color: #ffc107; font-weight:bold;";
        $alamat = preg_replace( "/\r|\n/", " ", addslashes($row['alamat']));
    ?>
        
        var rawCoord = "<?= $row['koordinat'] ?>"; 
        var coords = rawCoord.split(',');

        if(coords.length == 2) {
            var lat = parseFloat(coords[0]);
            var lng = parseFloat(coords[1]);
            var marker = L.marker([lat, lng]).addTo(map);
            var popupContent = `
                <div style='font-family: "Inter", sans-serif; min-width:150px;'>
                    <strong style='font-size:14px; color:#2AA7E1;'><?= $row['no_gardu'] ?></strong><br>
                    <span style='font-size:12px; color:#555;'><?= $alamat ?></span><br>
                    <hr style='margin:5px 0;'>
                    Daya: <b><?= $row['daya_trafo'] ?> kVA</b><br>
                    Status: <span style='<?= $colorStyle ?>'><?= $statusRaw ?></span>
                </div>
            `;
            marker.bindPopup(popupContent);
            markers.push(marker);
        }
    <?php } ?>

    if (markers.length > 0) {
        var group = new L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.1));
    }
</script>

</body>
</html>