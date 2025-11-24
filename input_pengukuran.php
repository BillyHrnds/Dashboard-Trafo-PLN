<?php
session_start();
include 'koneksi.php';

// 1. CEK KEAMANAN
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){ header("Location: login_awal.php"); exit; }
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){ 
    echo "<script>alert('AKSES DITOLAK! Hanya Admin yang boleh input data.'); window.location='index.php';</script>"; exit; 
}

// VARIABEL SYSTEM
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin');
$namaUser = $isAdmin ? 'Admin TJ Balai' : 'Pengunjung';
$roleUser = $isAdmin ? 'Administrator' : 'Guest View';

// 2. PROSES SIMPAN (LOGIC PHP TETAP SAMA)
if (isset($_POST['simpan'])) {
    $gardu_id = $_POST['gardu_id'];
    $petugas_input = mysqli_real_escape_string($koneksi, $_POST['petugas']);
    $tgl = $_POST['tanggal_ukur'];
    
    function getFloat($val) { return (float)$val; }

    // Incoming
    $in_lwbp_r = getFloat($_POST['in_lwbp_r']); $in_lwbp_s = getFloat($_POST['in_lwbp_s']); $in_lwbp_t = getFloat($_POST['in_lwbp_t']); $in_lwbp_n = getFloat($_POST['in_lwbp_n']);
    $in_wbp_r = getFloat($_POST['in_wbp_r']); $in_wbp_s = getFloat($_POST['in_wbp_s']); $in_wbp_t = getFloat($_POST['in_wbp_t']); $in_wbp_n = getFloat($_POST['in_wbp_n']);

    // Outgoing Loop
    $phases = ['r', 's', 't']; $types = ['lwbp', 'wbp']; 
    foreach($types as $type) {
        foreach($phases as $p){
            $total_phase = 0;
            for($j=1; $j<=4; $j++){
                $input_name = "out_{$type}_{$p}_j{$j}";
                $val = isset($_POST[$input_name]) && $_POST[$input_name] !== '' ? getFloat($_POST[$input_name]) : 0;
                ${$input_name} = $val; 
                $total_phase += $val;
            }
            ${"out_{$type}_{$p}_total"} = $total_phase;
        }
    }
    $out_lwbp_n_total = getFloat($_POST['out_lwbp_n_total']); $out_wbp_n_total = getFloat($_POST['out_wbp_n_total']); 
    
    // Tegangan
    $v_rn = getFloat($_POST['teg_r_n']); $v_sn = getFloat($_POST['teg_s_n']); $v_tn = getFloat($_POST['teg_t_n']);
    $v_rs = getFloat($_POST['teg_r_s']); $v_st = getFloat($_POST['teg_s_t']); $v_rt = getFloat($_POST['teg_r_t']);
    
    // Grounding
    $pent_body_opsi = $_POST['pent_body_opsi']; $pent_body_val = ($pent_body_opsi == 'Ada') ? getFloat($_POST['nilai_pent_body']) : 0;
    $pent_phbtr_opsi = $_POST['pent_phbtr_opsi']; $pent_phbtr_val = ($pent_phbtr_opsi == 'Ada') ? getFloat($_POST['nilai_pent_phbtr']) : 0;
    $pent_arrester_opsi = $_POST['pent_arrester_opsi']; $pent_arrester_val = ($pent_arrester_opsi == 'Ada') ? getFloat($_POST['nilai_pent_arrester']) : 0;
    
    // Hasil
    $daya_terpakai_lwbp = isset($_POST['result_beban_lwbp']) ? getFloat($_POST['result_beban_lwbp']) : 0;
    $ket_lwbp = $_POST['result_status_lwbp'] ?? 'NORMAL';
    $daya_terpakai_wbp = isset($_POST['result_beban_wbp']) ? getFloat($_POST['result_beban_wbp']) : 0;
    $ket_wbp = $_POST['result_status_wbp'] ?? 'NORMAL';
    
    $ketidakseimbangan_lwbp = $_POST['ketidakseimbangan_lwbp'];
    $ketidakseimbangan_wbp  = $_POST['ketidakseimbangan_wbp'];
    $keterangan = $_POST['keterangan'];

    // Query Insert
    $query = "INSERT INTO tb_pengukuran (
        gardu_id, petugas, tanggal_ukur,
        in_lwbp_r, in_lwbp_s, in_lwbp_t, in_lwbp_n, in_wbp_r, in_wbp_s, in_wbp_t, in_wbp_n,
        out_lwbp_r_j1, out_lwbp_r_j2, out_lwbp_r_j3, out_lwbp_r_j4, out_lwbp_r_total,
        out_lwbp_s_j1, out_lwbp_s_j2, out_lwbp_s_j3, out_lwbp_s_j4, out_lwbp_s_total,
        out_lwbp_t_j1, out_lwbp_t_j2, out_lwbp_t_j3, out_lwbp_t_j4, out_lwbp_t_total, out_lwbp_n_total,
        out_wbp_r_j1, out_wbp_r_j2, out_wbp_r_j3, out_wbp_r_j4, out_wbp_r_total,
        out_wbp_s_j1, out_wbp_s_j2, out_wbp_s_j3, out_wbp_s_j4, out_wbp_s_total,
        out_wbp_t_j1, out_wbp_t_j2, out_wbp_t_j3, out_wbp_t_j4, out_wbp_t_total, out_wbp_n_total,
        teg_r_n, teg_s_n, teg_t_n, teg_r_s, teg_s_t, teg_r_t,
        pent_body, nilai_pent_body, pent_phbtr, nilai_pent_phbtr, pent_arrester, nilai_pent_arrester,
        daya_terpakai_lwbp, ket_lwbp, daya_terpakai_wbp, ket_wbp,
        ketidakseimbangan_lwbp, ketidakseimbangan_wbp, keterangan
    ) VALUES (
        '$gardu_id', '$petugas_input', '$tgl',
        '$in_lwbp_r', '$in_lwbp_s', '$in_lwbp_t', '$in_lwbp_n', '$in_wbp_r', '$in_wbp_s', '$in_wbp_t', '$in_wbp_n',
        '$out_lwbp_r_j1', '$out_lwbp_r_j2', '$out_lwbp_r_j3', '$out_lwbp_r_j4', '$out_lwbp_r_total',
        '$out_lwbp_s_j1', '$out_lwbp_s_j2', '$out_lwbp_s_j3', '$out_lwbp_s_j4', '$out_lwbp_s_total',
        '$out_lwbp_t_j1', '$out_lwbp_t_j2', '$out_lwbp_t_j3', '$out_lwbp_t_j4', '$out_lwbp_t_total', '$out_lwbp_n_total',
        '$out_wbp_r_j1', '$out_wbp_r_j2', '$out_wbp_r_j3', '$out_wbp_r_j4', '$out_wbp_r_total',
        '$out_wbp_s_j1', '$out_wbp_s_j2', '$out_wbp_s_j3', '$out_wbp_s_j4', '$out_wbp_s_total',
        '$out_wbp_t_j1', '$out_wbp_t_j2', '$out_wbp_t_j3', '$out_wbp_t_j4', '$out_wbp_t_total', '$out_wbp_n_total',
        '$v_rn', '$v_sn', '$v_tn', '$v_rs', '$v_st', '$v_rt',
        '$pent_body_opsi', '$pent_body_val', '$pent_phbtr_opsi', '$pent_phbtr_val', '$pent_arrester_opsi', '$pent_arrester_val',
        '$daya_terpakai_lwbp', '$ket_lwbp', '$daya_terpakai_wbp', '$ket_wbp',
        '$ketidakseimbangan_lwbp', '$ketidakseimbangan_wbp', '$keterangan'
    )";

    if(mysqli_query($koneksi, $query)){
        echo "<script>alert('Data Pengukuran Berhasil Disimpan!'); window.location='index.php';</script>";
    } else {
        echo "Error SQL: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pengukuran - PLN Trafo</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .form-section-title {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
            margin: 25px 0 15px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        /* Gaya Kartu Status Beban */
        .status-card {
            background-color: var(--bg-tertiary); /* Adaptif Dark/Light */
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: 0.3s;
        }
        /* Saat Dark Mode, input Readonly lebih gelap */
        [data-theme="dark"] .form-control[readonly] {
            background-color: rgba(0,0,0,0.2) !important;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    
    <!-- SIDEBAR -->
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
                <li class="nav-item"><a href="tambah_gardu.php" class="nav-link"><i class="fas fa-plus-circle"></i> <span>Tambah Gardu</span></a></li>
                <!-- Menu Aktif -->
                <li class="nav-item active">
                    <a href="input_pengukuran.php" class="nav-link"><i class="fas fa-calculator"></i> <span>Input Ukur</span></a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="sidebar-footer p-3 mt-auto border-top border-white border-opacity-10">
            <div class="user-profile d-flex align-items-center gap-3">
               
                <div class="user-info"><div class="user-name fw-bold"><?= $namaUser ?></div><div class="user-role small opacity-75"><?= $roleUser ?></div></div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="header">
            <div class="header-left">
                <button id="mobileToggle" class="mobile-toggle"><i class="fas fa-bars"></i></button>
                <div class="header-text">
                    <h1 class="page-title">Input Pengukuran</h1>
                    <p class="page-subtitle">Catat hasil pengukuran beban dan tegangan di lapangan.</p>
                </div>
            </div>
            <div class="header-right">
                <button id="themeToggle" class="action-btn theme-toggle" title="Toggle Theme"><i class="fas fa-moon"></i></button>
                <a href="index.php" class="action-btn" title="Kembali"><i class="fas fa-arrow-left"></i></a>
            </div>
        </header>

        <div class="dashboard-content">
            
            <!-- CARD UTAMA -->
            <div class="card border-0 shadow-sm" style="background: var(--bg-card); color: var(--text-primary); border-radius: 15px;">
                <div class="card-body p-4">
                    <form action="" method="POST">
                        
                        <!-- 1. PILIH GARDU & INFO -->
                        <div class="row mb-4 p-3 rounded align-items-end" style="background-color: rgba(42, 167, 225, 0.1); border: 1px solid var(--primary-color);">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-primary">Pilih Gardu</label>
                                <select name="gardu_id" id="pilih_gardu" class="form-select border-primary" required>
                                    <option value="">-- Cari Nomor Gardu --</option>
                                    <?php $sqlg = mysqli_query($koneksi, "SELECT * FROM tb_gardu ORDER BY no_gardu ASC");
                                    while($g = mysqli_fetch_assoc($sqlg)){ echo "<option value='$g[id]' data-daya='$g[daya_trafo]'>$g[no_gardu] - $g[alamat]</option>"; } ?>
                                </select>
                            </div>
                            
                            <!-- INPUT PETUGAS MANUAL -->
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-primary">Nama Petugas</label>
                                <input type="text" name="petugas" class="form-control border-primary" placeholder="Ketik nama..." required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-muted small">Tanggal Ukur</label>
                                <input type="date" name="tanggal_ukur" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-muted small">Daya (kVA)</label>
                                <input type="text" id="show_daya" class="form-control" readonly>
                            </div>
                        </div>

                        <!-- 2. INCOMING -->
                        <h6 class="form-section-title">A. Arus Masuk (Incoming)</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold mb-2">LWBP (Ampere)</label>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">Phase R</span><input type="number" step="0.01" name="in_lwbp_r" class="form-control hitung-trigger"></div>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">Phase S</span><input type="number" step="0.01" name="in_lwbp_s" class="form-control hitung-trigger"></div>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">Phase T</span><input type="number" step="0.01" name="in_lwbp_t" class="form-control hitung-trigger"></div>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">Neutral</span><input type="number" step="0.01" name="in_lwbp_n" class="form-control"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold mb-2">WBP (Ampere)</label>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">Phase R</span><input type="number" step="0.01" name="in_wbp_r" class="form-control hitung-trigger"></div>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">Phase S</span><input type="number" step="0.01" name="in_wbp_s" class="form-control hitung-trigger"></div>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">Phase T</span><input type="number" step="0.01" name="in_wbp_t" class="form-control hitung-trigger"></div>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">Neutral</span><input type="number" step="0.01" name="in_wbp_n" class="form-control"></div>
                            </div>
                        </div>

                        <!-- 4. OUTGOING -->
                        <h6 class="form-section-title">B. Outgoing (Jurusan)</h6>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Jumlah Jurusan:</label>
                            <select id="jml_jurusan" class="form-select w-auto d-inline-block">
                                <option value="1">1 Jurusan</option><option value="2">2 Jurusan</option><option value="3">3 Jurusan</option><option value="4">4 Jurusan</option>
                            </select>
                        </div>
                        
                        <div class="row g-3">
                            <!-- LWBP Outgoing -->
                            <div class="col-md-6">
                                <div class="table-responsive rounded border" style="border-color: var(--border-color) !important;">
                                    <table class="table table-sm text-center align-middle m-0 table-glass">
                                        <thead class="table-secondary"><tr><th>LWBP</th><th>J1</th><th>J2</th><th>J3</th><th>J4</th></tr></thead>
                                        <tbody>
                                            <?php $p_label=['R','S','T']; $p_name=['r','s','t']; for($x=0;$x<3;$x++): ?>
                                            <tr><td class="fw-bold"><?=$p_label[$x]?></td>
                                            <?php for($i=1;$i<=4;$i++): ?><td><input type="number" step="0.01" name="out_lwbp_<?=$p_name[$x]?>_j<?=$i?>" class="form-control form-control-sm jur-<?=$i?>"></td><?php endfor; ?></tr>
                                            <?php endfor; ?>
                                            <tr><td class="fw-bold text-primary">N</td><td colspan="4"><input type="number" step="0.01" name="out_lwbp_n_total" class="form-control form-control-sm text-center" placeholder="Netral Total"></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- WBP Outgoing -->
                            <div class="col-md-6">
                                <div class="table-responsive rounded border" style="border-color: var(--border-color) !important;">
                                    <table class="table table-sm text-center align-middle m-0 table-glass">
                                        <thead class="table-info"><tr><th>WBP</th><th>J1</th><th>J2</th><th>J3</th><th>J4</th></tr></thead>
                                        <tbody>
                                            <?php for($x=0;$x<3;$x++): ?>
                                            <tr><td class="fw-bold"><?=$p_label[$x]?></td>
                                            <?php for($i=1;$i<=4;$i++): ?><td><input type="number" step="0.01" name="out_wbp_<?=$p_name[$x]?>_j<?=$i?>" class="form-control form-control-sm jur-<?=$i?>"></td><?php endfor; ?></tr>
                                            <?php endfor; ?>
                                            <tr><td class="fw-bold text-primary">N</td><td colspan="4"><input type="number" step="0.01" name="out_wbp_n_total" class="form-control form-control-sm text-center" placeholder="Netral Total"></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- 3. TEGANGAN & ANALISA -->
                        <h6 class="form-section-title">C. Tegangan & Analisa Beban</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold mb-2">Tegangan (Volt)</label>
                                <div class="row g-1">
                                    <div class="col-6"><div class="input-group input-group-sm mb-1"><span class="input-group-text">R-N</span><input type="number" step="0.1" name="teg_r_n" class="form-control"></div></div>
                                    <div class="col-6"><div class="input-group input-group-sm mb-1"><span class="input-group-text text-danger fw-bold">R-S</span><input type="number" step="0.1" name="teg_r_s" class="form-control hitung-trigger" required></div></div>
                                    <div class="col-6"><div class="input-group input-group-sm mb-1"><span class="input-group-text">S-N</span><input type="number" step="0.1" name="teg_s_n" class="form-control"></div></div>
                                    <div class="col-6"><div class="input-group input-group-sm mb-1"><span class="input-group-text text-danger fw-bold">S-T</span><input type="number" step="0.1" name="teg_s_t" class="form-control hitung-trigger" required></div></div>
                                    <div class="col-6"><div class="input-group input-group-sm mb-1"><span class="input-group-text">T-N</span><input type="number" step="0.1" name="teg_t_n" class="form-control"></div></div>
                                    <div class="col-6"><div class="input-group input-group-sm mb-1"><span class="input-group-text text-danger fw-bold">R-T</span><input type="number" step="0.1" name="teg_r_t" class="form-control hitung-trigger" required></div></div>
                                </div>
                            </div>
                            
                            <!-- KARTU STATUS OTOMATIS -->
                            <div class="col-md-4">
                                <div class="status-card" style="border-color: #f59e0b;">
                                    <h6 class="text-muted small fw-bold mb-2">BEBAN LWBP</h6>
                                    <h2 class="fw-bold m-0" id="lbl_persen_lwbp">0.00%</h2>
                                    <div class="mt-2"><span id="lbl_status_lwbp" class="badge bg-secondary px-3 rounded-pill">BELUM DIHITUNG</span></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="status-card" style="border-color: #0ea5e9;">
                                    <h6 class="text-muted small fw-bold mb-2">BEBAN WBP</h6>
                                    <h2 class="fw-bold m-0" id="lbl_persen_wbp">0.00%</h2>
                                    <div class="mt-2"><span id="lbl_status_wbp" class="badge bg-secondary px-3 rounded-pill">BELUM DIHITUNG</span></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden Inputs untuk hasil hitung -->
                        <input type="hidden" name="result_beban_lwbp" id="val_beban_lwbp"><input type="hidden" name="result_status_lwbp" id="val_status_lwbp">
                        <input type="hidden" name="result_beban_wbp" id="val_beban_wbp"><input type="hidden" name="result_status_wbp" id="val_status_wbp">

                        

                        <!-- 5. GROUNDING -->
                        <h6 class="form-section-title">D. Grounding & Lainnya</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Body Trafo</label>
                                <div class="input-group input-group-sm">
                                    <select name="pent_body_opsi" class="form-select toggle-grounding" data-target="#inp_body"><option value="Tidak Ada">Tidak Ada</option><option value="Ada">Ada</option></select>
                                    <input type="number" step="0.01" name="nilai_pent_body" id="inp_body" class="form-control w-50" readonly placeholder="Kosong">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">PHBTR</label>
                                <div class="input-group input-group-sm">
                                    <select name="pent_phbtr_opsi" class="form-select toggle-grounding" data-target="#inp_phbtr"><option value="Tidak Ada">Tidak Ada</option><option value="Ada">Ada</option></select>
                                    <input type="number" step="0.01" name="nilai_pent_phbtr" id="inp_phbtr" class="form-control w-50" readonly placeholder="Kosong">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Arrester</label>
                                <div class="input-group input-group-sm">
                                    <select name="pent_arrester_opsi" class="form-select toggle-grounding" data-target="#inp_arrester"><option value="Tidak Ada">Tidak Ada</option><option value="Ada">Ada</option></select>
                                    <input type="number" step="0.01" name="nilai_pent_arrester" id="inp_arrester" class="form-control w-50" readonly placeholder="Kosong">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-warning">Ketidakseimbangan LWBP</label>
                                <select name="ketidakseimbangan_lwbp" class="form-select"><option value="SEIMBANG">SEIMBANG</option><option value="TIDAK SEIMBANG">TIDAK SEIMBANG</option></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-info">Ketidakseimbangan WBP</label>
                                <select name="ketidakseimbangan_wbp" class="form-select"><option value="SEIMBANG">SEIMBANG</option><option value="TIDAK SEIMBANG">TIDAK SEIMBANG</option></select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Catatan Tambahan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Kondisi lingkungan..."></textarea>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" name="simpan" class="btn text-white btn-lg fw-bold shadow-sm" style="background: var(--primary-gradient); border:none;">
                                <i class="fas fa-save me-2"></i> SIMPAN DATA PENGUKURAN
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>

<script>
$(document).ready(function(){
    
    // 1. AUTO FILL DATA TERAKHIR
    $('#pilih_gardu').change(function(){
        var garduId = $(this).val(); var daya = $(this).find(':selected').data('daya'); $('#show_daya').val(daya);
        if(garduId){
            $.ajax({ url: 'get_data_terakhir.php', type: 'POST', data: {gardu_id: garduId}, dataType: 'json',
                success: function(data){
                    if(data){
                        $('input[name="in_lwbp_r"]').val(data.in_lwbp_r); $('input[name="in_lwbp_s"]').val(data.in_lwbp_s); $('input[name="in_lwbp_t"]').val(data.in_lwbp_t); $('input[name="in_lwbp_n"]').val(data.in_lwbp_n);
                        $('input[name="in_wbp_r"]').val(data.in_wbp_r); $('input[name="in_wbp_s"]').val(data.in_wbp_s); $('input[name="in_wbp_t"]').val(data.in_wbp_t); $('input[name="in_wbp_n"]').val(data.in_wbp_n);
                        var phases=['r','s','t']; var types=['lwbp','wbp'];
                        types.forEach(function(type){ phases.forEach(function(p){ for(var i=1; i<=4; i++){ $('input[name="out_'+type+'_'+p+'_j'+i+'"]').val(data['out_'+type+'_'+p+'_j'+i]); } }); });
                        $('input[name="out_lwbp_n_total"]').val(data.out_lwbp_n_total); $('input[name="out_wbp_n_total"]').val(data.out_wbp_n_total);
                        $('input[name="teg_r_n"]').val(data.teg_r_n); $('input[name="teg_s_n"]').val(data.teg_s_n); $('input[name="teg_t_n"]').val(data.teg_t_n);
                        $('input[name="teg_r_s"]').val(data.teg_r_s); $('input[name="teg_s_t"]').val(data.teg_s_t); $('input[name="teg_r_t"]').val(data.teg_r_t);
                        
                        // Grounding
                        $('select[name="pent_body_opsi"]').val(data.pent_body); $('select[name="pent_phbtr_opsi"]').val(data.pent_phbtr); $('select[name="pent_arrester_opsi"]').val(data.pent_arrester);
                        $('.toggle-grounding').trigger('change');
                        if(data.pent_body=='Ada')$('#inp_body').val(data.nilai_pent_body); if(data.pent_phbtr=='Ada')$('#inp_phbtr').val(data.nilai_pent_phbtr); if(data.pent_arrester=='Ada')$('#inp_arrester').val(data.nilai_pent_arrester);
                        
                        $('select[name="ketidakseimbangan_lwbp"]').val(data.ketidakseimbangan_lwbp); $('select[name="ketidakseimbangan_wbp"]').val(data.ketidakseimbangan_wbp);

                        // Jurusan logic
                        var j4=parseFloat(data.out_lwbp_r_j4)+parseFloat(data.out_lwbp_s_j4); var j3=parseFloat(data.out_lwbp_r_j3)+parseFloat(data.out_lwbp_s_j3); var j2=parseFloat(data.out_lwbp_r_j2)+parseFloat(data.out_lwbp_s_j2);
                        if(j4>0)$('#jml_jurusan').val(4); else if(j3>0)$('#jml_jurusan').val(3); else if(j2>0)$('#jml_jurusan').val(2); else $('#jml_jurusan').val(1);
                        $('#jml_jurusan').trigger('change');
                        hitungRumus();
                    } else { 
                        $('form')[0].reset(); $('#pilih_gardu').val(garduId); $('#show_daya').val(daya); 
                        $('#jml_jurusan').val(1).trigger('change'); $('.toggle-grounding').val('Tidak Ada').trigger('change'); 
                    }
                }
            });
        }
    });

    // 2. TOGGLE GROUNDING
    $('.toggle-grounding').change(function(){
        var status = $(this).val(); var targetId = $(this).data('target');
        if(status === 'Ada') { $(targetId).prop('readonly', false).attr('required', true).attr('placeholder', 'Nilai...').css('background-color', 'var(--bg-card)'); } 
        else { $(targetId).val('').prop('readonly', true).removeAttr('required').attr('placeholder', 'Kosong').css('background-color', 'rgba(0,0,0,0.05)'); }
    });
    $('.toggle-grounding').trigger('change');

    // 3. LOCK JURUSAN
    $('#jml_jurusan').change(function(){
         var jml = parseInt($(this).val());
         $('.jur-1, .jur-2, .jur-3, .jur-4').prop('readonly', false).css('background-color', 'var(--bg-card)');
         if(jml < 4) { $('.jur-5').val(0).prop('readonly', true).css('background-color', 'rgba(0,0,0,0.05)'); }
         if(jml < 3) { $('.jur-5').val(0).prop('readonly', true).css('background-color', 'rgba(0,0,0,0.05)'); }
         if(jml < 2) { $('.jur-5').val(0).prop('readonly', true).css('background-color', 'rgba(0,0,0,0.05)'); }
    }); $('#jml_jurusan').trigger('change');

    // 4. HITUNG RUMUS
    $('.hitung-trigger').keyup(function(){ hitungRumus(); });
    function hitungRumus() {
        var daya = parseFloat($('#show_daya').val()) || 0;
        var v_rs = parseFloat($('input[name="teg_r_s"]').val()) || 0; var v_st = parseFloat($('input[name="teg_s_t"]').val()) || 0; var v_rt = parseFloat($('input[name="teg_r_t"]').val()) || 0;
        if(daya > 0 && v_rs > 0) {
            var avg_volt = (v_rs + v_st + v_rt) / 3; var i_nominal = daya / ((avg_volt/1000)*1.73*0.86);
            
            var ir = parseFloat($('input[name="in_lwbp_r"]').val())||0; var is = parseFloat($('input[name="in_lwbp_s"]').val())||0; var it = parseFloat($('input[name="in_lwbp_t"]').val())||0;
            var persen_l = ((ir+is+it)/3 / i_nominal)*100;
            $('#lbl_persen_lwbp').text(persen_l.toFixed(2)+'%'); $('#val_beban_lwbp').val(persen_l.toFixed(2));
            var stat_l="NORMAL"; var cls_l="bg-success";
            if(persen_l>80){stat_l="OVERLOAD";cls_l="bg-danger";} else if(persen_l<20){stat_l="UNDERLOAD";cls_l="bg-warning text-dark";}
            $('#lbl_status_lwbp').text(stat_l).removeClass().addClass('badge '+cls_l); $('#val_status_lwbp').val(stat_l);

            var wr = parseFloat($('input[name="in_wbp_r"]').val())||0; var ws = parseFloat($('input[name="in_wbp_s"]').val())||0; var wt = parseFloat($('input[name="in_wbp_t"]').val())||0;
            var persen_w = ((wr+ws+wt)/3 / i_nominal)*100;
            $('#lbl_persen_wbp').text(persen_w.toFixed(2)+'%'); $('#val_beban_wbp').val(persen_w.toFixed(2));
            var stat_w="NORMAL"; var cls_w="bg-info text-white";
            if(persen_w>80){stat_w="OVERLOAD";cls_w="bg-danger";} else if(persen_w<20){stat_w="UNDERLOAD";cls_w="bg-warning text-dark";}
            $('#lbl_status_wbp').text(stat_w).removeClass().addClass('badge '+cls_w); $('#val_status_wbp').val(stat_w);
        }
    }
});
</script>
</body>
</html>