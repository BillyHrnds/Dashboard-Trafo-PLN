<?php
session_start();
include 'koneksi.php';

// 1. CEK KEAMANAN
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){ header("Location: login_awal.php"); exit; }
if($_SESSION['role'] != "admin"){ echo "<script>alert('AKSES DITOLAK!'); window.location='index.php';</script>"; exit; }

$namaUser = $_SESSION['nama'] ?? 'Admin';
$roleUser = 'Administrator';

// 2. AMBIL DATA BERDASARKAN ID GARDU DARI URL
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$gardu_id = $_GET['id'];

// Query Khusus: Ambil Data Gardu + Data Pengukuran Terakhirnya
$query = "SELECT p.*, g.no_gardu, g.alamat, g.daya_trafo, p.id as id_ukur 
          FROM tb_gardu g 
          LEFT JOIN tb_pengukuran p ON g.id = p.gardu_id 
          WHERE g.id = '$gardu_id' 
          ORDER BY p.id DESC LIMIT 1";

$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

// Jika belum ada data pengukuran, lempar ke input baru
if (!$data || $data['tanggal_ukur'] == null) {
    echo "<script>alert('Belum ada riwayat pengukuran untuk gardu ini. Silakan Input Baru.'); window.location='input_pengukuran.php';</script>";
    exit;
}

// 3. PROSES UPDATE DATA
if (isset($_POST['update'])) {
    $id_ukur = $_POST['id_ukur']; // ID Primary Key tabel pengukuran
    $petugas_input = mysqli_real_escape_string($koneksi, $_POST['petugas']);
    $tgl = $_POST['tanggal_ukur'];
    
    function getFloat($val) { return (float)$val; }

    // Ambil Inputan
    $in_lwbp_r = getFloat($_POST['in_lwbp_r']); $in_lwbp_s = getFloat($_POST['in_lwbp_s']); $in_lwbp_t = getFloat($_POST['in_lwbp_t']); $in_lwbp_n = getFloat($_POST['in_lwbp_n']);
    $in_wbp_r = getFloat($_POST['in_wbp_r']); $in_wbp_s = getFloat($_POST['in_wbp_s']); $in_wbp_t = getFloat($_POST['in_wbp_t']); $in_wbp_n = getFloat($_POST['in_wbp_n']);

    // Outgoing Loop
    $phases = ['r', 's', 't']; $types = ['lwbp', 'wbp']; 
    foreach($types as $type) {
        foreach($phases as $p){
            $total_phase = 0;
            for($j=1; $j<=4; $j++){
                $input_name = "out_{$type}_{$p}_j{$j}";
                $val = isset($_POST[$input_name]) ? getFloat($_POST[$input_name]) : 0;
                ${$input_name} = $val; $total_phase += $val;
            }
            ${"out_{$type}_{$p}_total"} = $total_phase;
        }
    }
    $out_lwbp_n_total = getFloat($_POST['out_lwbp_n_total']); $out_wbp_n_total = getFloat($_POST['out_wbp_n_total']); 
    
    $v_rn = getFloat($_POST['teg_r_n']); $v_sn = getFloat($_POST['teg_s_n']); $v_tn = getFloat($_POST['teg_t_n']);
    $v_rs = getFloat($_POST['teg_r_s']); $v_st = getFloat($_POST['teg_s_t']); $v_rt = getFloat($_POST['teg_r_t']);
    
    $pent_body_opsi = $_POST['pent_body_opsi']; $pent_body_val = ($pent_body_opsi == 'Ada') ? getFloat($_POST['nilai_pent_body']) : 0;
    $pent_phbtr_opsi = $_POST['pent_phbtr_opsi']; $pent_phbtr_val = ($pent_phbtr_opsi == 'Ada') ? getFloat($_POST['nilai_pent_phbtr']) : 0;
    $pent_arrester_opsi = $_POST['pent_arrester_opsi']; $pent_arrester_val = ($pent_arrester_opsi == 'Ada') ? getFloat($_POST['nilai_pent_arrester']) : 0;
    
    $daya_terpakai_lwbp = isset($_POST['result_beban_lwbp']) ? getFloat($_POST['result_beban_lwbp']) : 0;
    $ket_lwbp = $_POST['result_status_lwbp'] ?? 'NORMAL';
    $daya_terpakai_wbp = isset($_POST['result_beban_wbp']) ? getFloat($_POST['result_beban_wbp']) : 0;
    $ket_wbp = $_POST['result_status_wbp'] ?? 'NORMAL';
    
    $ketidakseimbangan_lwbp = $_POST['ketidakseimbangan_lwbp'];
    $ketidakseimbangan_wbp = $_POST['ketidakseimbangan_wbp'];
    $keterangan = $_POST['keterangan'];

    // Query Update
    $sql = "UPDATE tb_pengukuran SET 
            petugas='$petugas_input', tanggal_ukur='$tgl',
            in_lwbp_r='$in_lwbp_r', in_lwbp_s='$in_lwbp_s', in_lwbp_t='$in_lwbp_t', in_lwbp_n='$in_lwbp_n',
            in_wbp_r='$in_wbp_r', in_wbp_s='$in_wbp_s', in_wbp_t='$in_wbp_t', in_wbp_n='$in_wbp_n',
            out_lwbp_r_j1='$out_lwbp_r_j1', out_lwbp_r_j2='$out_lwbp_r_j2', out_lwbp_r_j3='$out_lwbp_r_j3', out_lwbp_r_j4='$out_lwbp_r_j4', out_lwbp_r_total='$out_lwbp_r_total',
            out_lwbp_s_j1='$out_lwbp_s_j1', out_lwbp_s_j2='$out_lwbp_s_j2', out_lwbp_s_j3='$out_lwbp_s_j3', out_lwbp_s_j4='$out_lwbp_s_j4', out_lwbp_s_total='$out_lwbp_s_total',
            out_lwbp_t_j1='$out_lwbp_t_j1', out_lwbp_t_j2='$out_lwbp_t_j2', out_lwbp_t_j3='$out_lwbp_t_j3', out_lwbp_t_j4='$out_lwbp_t_j4', out_lwbp_t_total='$out_lwbp_t_total',
            out_lwbp_n_total='$out_lwbp_n_total',
            out_wbp_r_j1='$out_wbp_r_j1', out_wbp_r_j2='$out_wbp_r_j2', out_wbp_r_j3='$out_wbp_r_j3', out_wbp_r_j4='$out_wbp_r_j4', out_wbp_r_total='$out_wbp_r_total',
            out_wbp_s_j1='$out_wbp_s_j1', out_wbp_s_j2='$out_wbp_s_j2', out_wbp_s_j3='$out_wbp_s_j3', out_wbp_s_j4='$out_wbp_s_j4', out_wbp_s_total='$out_wbp_s_total',
            out_wbp_t_j1='$out_wbp_t_j1', out_wbp_t_j2='$out_wbp_t_j2', out_wbp_t_j3='$out_wbp_t_j3', out_wbp_t_j4='$out_wbp_t_j4', out_wbp_t_total='$out_wbp_t_total',
            out_wbp_n_total='$out_wbp_n_total',
            teg_r_n='$v_rn', teg_s_n='$v_sn', teg_t_n='$v_tn', teg_r_s='$v_rs', teg_s_t='$v_st', teg_r_t='$v_rt',
            pent_body='$pent_body_opsi', nilai_pent_body='$pent_body_val', pent_phbtr='$pent_phbtr_opsi', nilai_pent_phbtr='$pent_phbtr_val', pent_arrester='$pent_arrester_opsi', nilai_pent_arrester='$pent_arrester_val',
            daya_terpakai_lwbp='$daya_terpakai_lwbp', ket_lwbp='$ket_lwbp', daya_terpakai_wbp='$daya_terpakai_wbp', ket_wbp='$ket_wbp',
            ketidakseimbangan_lwbp='$ketidakseimbangan_lwbp', ketidakseimbangan_wbp='$ketidakseimbangan_wbp', keterangan='$keterangan'
            WHERE id='$id_ukur'";

    if(mysqli_query($koneksi, $sql)){
        echo "<script>alert('Data Pengukuran Berhasil Diperbarui!'); window.location='index.php';</script>";
    } else { echo "Error Update: " . mysqli_error($koneksi); }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengukuran - PLN Trafo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-section-title { color: var(--primary-color); font-weight: 700; font-size: 0.9rem; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin: 25px 0 15px 0; text-transform: uppercase; letter-spacing: 1px; }
        .status-card { background-color: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: 12px; padding: 15px; text-align: center; height: 100%; }
        [data-theme="dark"] .form-control[readonly] { background-color: rgba(0,0,0,0.2) !important; }
    </style>
</head>
<body>

<div class="dashboard-container">
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-header"><div class="logo"><i class="fas fa-bolt"></i><span class="logo-text">PLN Trafo</span></div><button id="sidebarToggle" class="sidebar-toggle"><i class="fas fa-times"></i></button></div>
        <nav class="sidebar-nav p-3">
            <ul class="nav-menu">
                <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li class="nav-item"><a href="map_view.php" class="nav-link"><i class="fas fa-map-marked-alt"></i> <span>Peta Sebaran</span></a></li>
                <li class="nav-item"><a href="export_excel.php" class="nav-link"><i class="fas fa-file-excel"></i> <span>Export Excel</span></a></li>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <li style="margin: 1.5rem 1rem 0.5rem; font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Menu Petugas</li>
                <li class="nav-item"><a href="tambah_gardu.php" class="nav-link"><i class="fas fa-plus-circle"></i> <span>Tambah Gardu</span></a></li>
                <li class="nav-item"><a href="input_pengukuran.php" class="nav-link"><i class="fas fa-calculator"></i> <span>Input Ukur</span></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="sidebar-footer p-3 mt-auto border-top border-white border-opacity-10"><div class="user-profile d-flex align-items-center gap-3"><div class="user-avatar bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:40px; height:40px;">A</div><div class="user-info"><div class="user-name fw-bold"><?= $namaUser ?></div><div class="user-role small opacity-75"><?= $roleUser ?></div></div></div></div>
    </aside>

    <main class="main-content">
        <header class="header">
            <div class="header-left"><button id="mobileToggle" class="mobile-toggle"><i class="fas fa-bars"></i></button><div class="header-text"><h1 class="page-title">Edit Data Pengukuran</h1><p class="page-subtitle">Koreksi hasil ukur gardu: <b><?= $data['no_gardu'] ?></b></p></div></div>
            <div class="header-right"><button id="themeToggle" class="action-btn theme-toggle"><i class="fas fa-moon"></i></button><a href="index.php" class="action-btn"><i class="fas fa-arrow-left"></i></a></div>
        </header>

        <div class="dashboard-content">
            <div class="card border-0 shadow-sm" style="background: var(--bg-card); color: var(--text-primary); border-radius: 15px;">
                <div class="card-body p-4">
                    <form action="" method="POST">
                        
                        <input type="hidden" name="id_ukur" value="<?= $data['id_ukur'] ?>">
                        
                        <div class="row mb-4 p-3 rounded align-items-end" style="background-color: rgba(245, 158, 11, 0.1); border: 1px solid #f59e0b;">
                            <div class="col-md-4"><label class="form-label fw-bold text-warning">Gardu</label><input type="text" class="form-control fw-bold" value="<?= $data['no_gardu'] ?> - <?= $data['alamat'] ?>" readonly style="border-color:#f59e0b;"></div>
                            <div class="col-md-3"><label class="form-label fw-bold text-warning">Nama Petugas</label><input type="text" name="petugas" class="form-control border-warning" value="<?= $data['petugas'] ?>" required></div>
                            <div class="col-md-3"><label class="form-label text-muted small">Tanggal</label><input type="date" name="tanggal_ukur" class="form-control" value="<?= $data['tanggal_ukur'] ?>" required></div>
                            <div class="col-md-2"><label class="form-label text-muted small">Daya (kVA)</label><input type="text" id="show_daya" class="form-control" value="<?= $data['daya_trafo'] ?>" readonly></div>
                        </div>

                        <h6 class="form-section-title">A. Arus Masuk</h6>
                        <div class="row mb-4">
                            <div class="col-md-6"><label class="form-label text-muted mb-2">LWBP</label>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">R</span><input type="number" step="0.01" name="in_lwbp_r" value="<?= $data['in_lwbp_r'] ?>" class="form-control hitung-trigger"></div>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">S</span><input type="number" step="0.01" name="in_lwbp_s" value="<?= $data['in_lwbp_s'] ?>" class="form-control hitung-trigger"></div>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">T</span><input type="number" step="0.01" name="in_lwbp_t" value="<?= $data['in_lwbp_t'] ?>" class="form-control hitung-trigger"></div>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">N</span><input type="number" step="0.01" name="in_lwbp_n" value="<?= $data['in_lwbp_n'] ?>" class="form-control"></div>
                            </div>
                            <div class="col-md-6"><label class="form-label text-muted mb-2">WBP</label>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">R</span><input type="number" step="0.01" name="in_wbp_r" value="<?= $data['in_wbp_r'] ?>" class="form-control hitung-trigger"></div>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">S</span><input type="number" step="0.01" name="in_wbp_s" value="<?= $data['in_wbp_s'] ?>" class="form-control hitung-trigger"></div>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">T</span><input type="number" step="0.01" name="in_wbp_t" value="<?= $data['in_wbp_t'] ?>" class="form-control hitung-trigger"></div>
                                <div class="input-group input-group-sm mb-2"><span class="input-group-text w-25">N</span><input type="number" step="0.01" name="in_wbp_n" value="<?= $data['in_wbp_n'] ?>" class="form-control"></div>
                            </div>
                        </div>

                        <h6 class="form-section-title">C. Outgoing</h6>
                        <div class="mb-3"><label class="form-label small">Jumlah Jurusan:</label><select id="jml_jurusan" class="form-select w-auto d-inline-block"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                        <div class="row g-3">
                            <div class="col-md-6"><div class="table-responsive rounded border" style="border-color: var(--border-color) !important;"><table class="table table-sm text-center align-middle m-0 table-glass"><thead class="table-secondary"><tr><th>LWBP</th><th>J1</th><th>J2</th><th>J3</th><th>J4</th></tr></thead><tbody><?php $p_label=['R','S','T']; $p_name=['r','s','t']; for($x=0;$x<3;$x++): ?><tr><td class="fw-bold"><?=$p_label[$x]?></td><?php for($i=1;$i<=4;$i++): ?><td><input type="number" step="0.01" name="out_lwbp_<?=$p_name[$x]?>_j<?=$i?>" value="<?= $data['out_lwbp_'.$p_name[$x].'_j'.$i] ?>" class="form-control form-control-sm jur-<?=$i?>"></td><?php endfor; ?></tr><?php endfor; ?><tr><td class="fw-bold text-primary">N</td><td colspan="4"><input type="number" step="0.01" name="out_lwbp_n_total" value="<?= $data['out_lwbp_n_total'] ?>" class="form-control form-control-sm text-center"></td></tr></tbody></table></div></div>
                            <div class="col-md-6"><div class="table-responsive rounded border" style="border-color: var(--border-color) !important;"><table class="table table-sm text-center align-middle m-0 table-glass"><thead class="table-info"><tr><th>WBP</th><th>J1</th><th>J2</th><th>J3</th><th>J4</th></tr></thead><tbody><?php for($x=0;$x<3;$x++): ?><tr><td class="fw-bold"><?=$p_label[$x]?></td><?php for($i=1;$i<=4;$i++): ?><td><input type="number" step="0.01" name="out_wbp_<?=$p_name[$x]?>_j<?=$i?>" value="<?= $data['out_wbp_'.$p_name[$x].'_j'.$i] ?>" class="form-control form-control-sm jur-<?=$i?>"></td><?php endfor; ?></tr><?php endfor; ?><tr><td class="fw-bold text-primary">N</td><td colspan="4"><input type="number" step="0.01" name="out_wbp_n_total" value="<?= $data['out_wbp_n_total'] ?>" class="form-control form-control-sm text-center"></td></tr></tbody></table></div></div>
                        </div>

                        <h6 class="form-section-title">B. Tegangan & Analisa</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="row g-1">
                                    <div class="col-6"><div class="input-group input-group-sm mb-1"><span class="input-group-text">R-N</span><input type="number" step="0.1" name="teg_r_n" value="<?= $data['teg_r_n'] ?>" class="form-control"></div></div>
                                    <div class="col-6"><div class="input-group input-group-sm mb-1"><span class="input-group-text text-danger">R-S</span><input type="number" step="0.1" name="teg_r_s" value="<?= $data['teg_r_s'] ?>" class="form-control hitung-trigger" required></div></div>
                                    <div class="col-6"><div class="input-group input-group-sm mb-1"><span class="input-group-text">S-N</span><input type="number" step="0.1" name="teg_s_n" value="<?= $data['teg_s_n'] ?>" class="form-control"></div></div>
                                    <div class="col-6"><div class="input-group input-group-sm mb-1"><span class="input-group-text text-danger">S-T</span><input type="number" step="0.1" name="teg_s_t" value="<?= $data['teg_s_t'] ?>" class="form-control hitung-trigger" required></div></div>
                                    <div class="col-6"><div class="input-group input-group-sm mb-1"><span class="input-group-text">T-N</span><input type="number" step="0.1" name="teg_t_n" value="<?= $data['teg_t_n'] ?>" class="form-control"></div></div>
                                    <div class="col-6"><div class="input-group input-group-sm mb-1"><span class="input-group-text text-danger">R-T</span><input type="number" step="0.1" name="teg_r_t" value="<?= $data['teg_r_t'] ?>" class="form-control hitung-trigger" required></div></div>
                                </div>
                            </div>
                            <div class="col-md-4"><div class="status-card"><h6 class="text-muted small mb-2">LWBP</h6><h2 class="fw-bold m-0" id="lbl_persen_lwbp">0%</h2><div class="mt-2"><span id="lbl_status_lwbp" class="badge bg-secondary px-3">...</span></div></div></div>
                            <div class="col-md-4"><div class="status-card"><h6 class="text-muted small mb-2">WBP</h6><h2 class="fw-bold m-0" id="lbl_persen_wbp">0%</h2><div class="mt-2"><span id="lbl_status_wbp" class="badge bg-secondary px-3">...</span></div></div></div>
                        </div>
                        <input type="hidden" name="result_beban_lwbp" id="val_beban_lwbp"><input type="hidden" name="result_status_lwbp" id="val_status_lwbp">
                        <input type="hidden" name="result_beban_wbp" id="val_beban_wbp"><input type="hidden" name="result_status_wbp" id="val_status_wbp">

                        <h6 class="form-section-title">D. Grounding</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4"><label class="form-label small">Body</label><div class="input-group input-group-sm"><select name="pent_body_opsi" class="form-select toggle-grounding" data-target="#inp_body"><option value="Tidak Ada" <?=($data['pent_body']=='Tidak Ada')?'selected':'';?>>Tidak Ada</option><option value="Ada" <?=($data['pent_body']=='Ada')?'selected':'';?>>Ada</option></select><input type="number" step="0.01" name="nilai_pent_body" id="inp_body" value="<?= $data['nilai_pent_body'] ?>" class="form-control w-50" readonly></div></div>
                            <div class="col-md-4"><label class="form-label small">PHBTR</label><div class="input-group input-group-sm"><select name="pent_phbtr_opsi" class="form-select toggle-grounding" data-target="#inp_phbtr"><option value="Tidak Ada" <?=($data['pent_phbtr']=='Tidak Ada')?'selected':'';?>>Tidak Ada</option><option value="Ada" <?=($data['pent_phbtr']=='Ada')?'selected':'';?>>Ada</option></select><input type="number" step="0.01" name="nilai_pent_phbtr" id="inp_phbtr" value="<?= $data['nilai_pent_phbtr'] ?>" class="form-control w-50" readonly></div></div>
                            <div class="col-md-4"><label class="form-label small">Arrester</label><div class="input-group input-group-sm"><select name="pent_arrester_opsi" class="form-select toggle-grounding" data-target="#inp_arrester"><option value="Tidak Ada" <?=($data['pent_arrester']=='Tidak Ada')?'selected':'';?>>Tidak Ada</option><option value="Ada" <?=($data['pent_arrester']=='Ada')?'selected':'';?>>Ada</option></select><input type="number" step="0.01" name="nilai_pent_arrester" id="inp_arrester" value="<?= $data['nilai_pent_arrester'] ?>" class="form-control w-50" readonly></div></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label small text-warning">Ketidakseimbangan LWBP</label><select name="ketidakseimbangan_lwbp" class="form-select"><option value="SEIMBANG" <?=($data['ketidakseimbangan_lwbp']=='SEIMBANG')?'selected':'';?>>SEIMBANG</option><option value="TIDAK SEIMBANG" <?=($data['ketidakseimbangan_lwbp']=='TIDAK SEIMBANG')?'selected':'';?>>TIDAK SEIMBANG</option></select></div>
                            <div class="col-md-6"><label class="form-label small text-info">Ketidakseimbangan WBP</label><select name="ketidakseimbangan_wbp" class="form-select"><option value="SEIMBANG" <?=($data['ketidakseimbangan_wbp']=='SEIMBANG')?'selected':'';?>>SEIMBANG</option><option value="TIDAK SEIMBANG" <?=($data['ketidakseimbangan_wbp']=='TIDAK SEIMBANG')?'selected':'';?>>TIDAK SEIMBANG</option></select></div>
                        </div>
                        <div class="mb-4"><label class="form-label small">Catatan</label><textarea name="keterangan" class="form-control" rows="2"><?= $data['keterangan'] ?></textarea></div>

                        <div class="d-grid mt-5"><button type="submit" name="update" class="btn text-white btn-lg fw-bold shadow-sm" style="background: var(--warning-gradient); border:none;"><i class="fas fa-save me-2"></i> UPDATE DATA</button></div>
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
$(document).ready(function(){
    // DETEKSI JURUSAN
    var j4 = <?= ($data['out_lwbp_r_j4']>0 || $data['out_lwbp_s_j4']>0) ? 1 : 0 ?>;
    var j3 = <?= ($data['out_lwbp_r_j3']>0 || $data['out_lwbp_s_j3']>0) ? 1 : 0 ?>;
    var j2 = <?= ($data['out_lwbp_r_j2']>0 || $data['out_lwbp_s_j2']>0) ? 1 : 0 ?>;
    if(j4) $('#jml_jurusan').val(4); else if(j3) $('#jml_jurusan').val(3); else if(j2) $('#jml_jurusan').val(2); else $('#jml_jurusan').val(1);
    
    function updateJurusan() {
         var jml = parseInt($('#jml_jurusan').val());
         $('.jur-1, .jur-2, .jur-3, .jur-4').prop('readonly', false).css('background-color', 'var(--bg-card)');
         if(jml < 4) { $('.jur-5').val(0).prop('readonly', true).css('background-color', 'rgba(0,0,0,0.05)'); }
         if(jml < 3) { $('.jur-5').val(0).prop('readonly', true).css('background-color', 'rgba(0,0,0,0.05)'); }
         if(jml < 2) { $('.jur-5').val(0).prop('readonly', true).css('background-color', 'rgba(0,0,0,0.05)'); }
    }
    $('#jml_jurusan').change(function(){ updateJurusan(); });
    updateJurusan(); // Init

    // TOGGLE GROUNDING
    $('.toggle-grounding').change(function(){
        var status = $(this).val(); var targetId = $(this).data('target');
        if(status === 'Ada') { $(targetId).prop('readonly', false).css('background-color', 'var(--bg-card)').focus(); } 
        else { $(targetId).val('').prop('readonly', true).css('background-color', 'rgba(0,0,0,0.05)'); }
    });
    $('.toggle-grounding').trigger('change'); // Ini penting biar status awal ke-load

    // HITUNG RUMUS
    $('.hitung-trigger').keyup(function(){ hitungRumus(); });
    hitungRumus(); // Init agar status langsung muncul saat dibuka

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