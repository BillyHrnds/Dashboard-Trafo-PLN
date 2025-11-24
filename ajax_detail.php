<?php
include 'koneksi.php';

if(isset($_POST['id_gardu'])) {
    $id = $_POST['id_gardu'];

    $query = "SELECT g.*, p.* FROM tb_gardu g 
              LEFT JOIN tb_pengukuran p ON g.id = p.gardu_id 
              WHERE g.id = '$id' 
              ORDER BY p.id DESC LIMIT 1";
    
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);
    $ada_ukur = ($data['tanggal_ukur'] != null);
?>

<div class="modal-header p-4" style="border-bottom: 1px solid var(--border-color);">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle" 
             style="width: 50px; height: 50px; background: var(--primary-gradient); color: white; flex-shrink: 0;">
            <i class="fas fa-bolt fa-lg"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">Gardu <?= $data['no_gardu'] ?></h5>
            <p class="small m-0" style="color: var(--text-muted);"><i class="fas fa-map-marker-alt me-1"></i> <?= $data['alamat'] ?></p>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(var(--invert-value));"></button>
</div>

<div class="modal-body p-4">
    
    <ul class="nav nav-pills nav-fill mb-4 p-1 rounded-3" id="myTab" role="tablist" style="background-color: var(--bg-tertiary); border: 1px solid var(--border-color);">
        <li class="nav-item"><button class="nav-link active rounded-3 small fw-bold" data-bs-toggle="tab" data-bs-target="#tab1">Info</button></li>
        <li class="nav-item"><button class="nav-link rounded-3 small fw-bold" data-bs-toggle="tab" data-bs-target="#tab2">Outgoing</button></li>
        <li class="nav-item"><button class="nav-link rounded-3 small fw-bold" data-bs-toggle="tab" data-bs-target="#tab3">Teknis</button></li>
        <li class="nav-item"><button class="nav-link rounded-3 small fw-bold" data-bs-toggle="tab" data-bs-target="#tab4">Analisa</button></li>
    </ul>

    <div class="tab-content">
        
        <div class="tab-pane fade show active" id="tab1">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="p-3 rounded-3 h-100 info-box-glass">
                        <h6 class="fw-bold mb-3" style="color: var(--text-primary);">Data Aset</h6>
                        <table class="table table-sm table-borderless small m-0">
                            <tr><td style="color: var(--text-muted);">Posko</td><td class="fw-bold"><?= $data['posko'] ?></td></tr>
                            <tr><td style="color: var(--text-muted);">Penyulang</td><td class="fw-bold"><?= $data['penyulang'] ?></td></tr>
                            <tr><td style="color: var(--text-muted);">Merk</td><td class="fw-bold"><?= $data['merk'] ?></td></tr>
                            <tr><td style="color: var(--text-muted);">Daya</td><td class="fw-bold" style="color: #2AA7E1;"><?= $data['daya_trafo'] ?> kVA</td></tr>
                            <tr><td style="color: var(--text-muted);">Tiang</td><td class="fw-bold"><?= $data['tiang_trafo'] ?></td></tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="p-3 rounded-3 h-100 info-box-glass">
                        <h6 class="fw-bold mb-3" style="color: var(--text-primary);">Incoming (Arus Masuk)</h6>
                        <?php if($ada_ukur): ?>
                        <div class="table-responsive">
                            <table class="table table-sm text-center align-middle small m-0">
                                <thead><tr><th>Phs</th><th>LWBP</th><th>WBP</th></tr></thead>
                                <tbody>
                                    <tr><td class="fw-bold">R</td><td><?= $data['in_lwbp_r'] ?></td><td><?= $data['in_wbp_r'] ?></td></tr>
                                    <tr><td class="fw-bold">S</td><td><?= $data['in_lwbp_s'] ?></td><td><?= $data['in_wbp_s'] ?></td></tr>
                                    <tr><td class="fw-bold">T</td><td><?= $data['in_lwbp_t'] ?></td><td><?= $data['in_wbp_t'] ?></td></tr>
                                    <tr><td class="fw-bold" style="color: #2AA7E1;">N</td><td><?= $data['in_lwbp_n'] ?></td><td><?= $data['in_wbp_n'] ?></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?><p class="text-center small py-4" style="color: var(--text-muted);">Belum ada data ukur.</p><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab2">
            <?php if($ada_ukur): ?>
            
            <h6 class="small fw-bold mb-2" style="color: var(--text-muted);">1. OUTGOING LWBP</h6>
            <div class="table-responsive rounded-3 border mb-3" style="border-color: var(--border-color);">
                <table class="table table-sm text-center align-middle small m-0">
                    <thead><tr><th>Phs</th><th>J1</th><th>J2</th><th>J3</th><th>J4</th><th>TOT</th></tr></thead>
                    <tbody>
                        <tr><td class="fw-bold">R</td><td><?= $data['out_lwbp_r_j1']?></td><td><?= $data['out_lwbp_r_j2']?></td><td><?= $data['out_lwbp_r_j3']?></td><td><?= $data['out_lwbp_r_j4']?></td><td class="fw-bold" style="color: #2AA7E1;"><?= $data['out_lwbp_r_total']?></td></tr>
                        <tr><td class="fw-bold">S</td><td><?= $data['out_lwbp_s_j1']?></td><td><?= $data['out_lwbp_s_j2']?></td><td><?= $data['out_lwbp_s_j3']?></td><td><?= $data['out_lwbp_s_j4']?></td><td class="fw-bold" style="color: #2AA7E1;"><?= $data['out_lwbp_s_total']?></td></tr>
                        <tr><td class="fw-bold">T</td><td><?= $data['out_lwbp_t_j1']?></td><td><?= $data['out_lwbp_t_j2']?></td><td><?= $data['out_lwbp_t_j3']?></td><td><?= $data['out_lwbp_t_j4']?></td><td class="fw-bold" style="color: #2AA7E1;"><?= $data['out_lwbp_t_total']?></td></tr>
                        <tr style="background-color: var(--bg-tertiary);"><td class="fw-bold">N</td><td colspan="5" class="fw-bold"><?= $data['out_lwbp_n_total'] ?></td></tr>
                    </tbody>
                </table>
            </div>

            <h6 class="small fw-bold mb-2" style="color: var(--text-muted);">2. OUTGOING WBP</h6>
            <div class="table-responsive rounded-3 border" style="border-color: var(--border-color);">
                <table class="table table-sm text-center align-middle small m-0">
                    <thead><tr><th>Phs</th><th>J1</th><th>J2</th><th>J3</th><th>J4</th><th>TOT</th></tr></thead>
                    <tbody>
                        <tr><td class="fw-bold">R</td><td><?= $data['out_wbp_r_j1']?></td><td><?= $data['out_wbp_r_j2']?></td><td><?= $data['out_wbp_r_j3']?></td><td><?= $data['out_wbp_r_j4']?></td><td class="fw-bold" style="color: #F59E0B;"><?= $data['out_wbp_r_total']?></td></tr>
                        <tr><td class="fw-bold">S</td><td><?= $data['out_wbp_s_j1']?></td><td><?= $data['out_wbp_s_j2']?></td><td><?= $data['out_wbp_s_j3']?></td><td><?= $data['out_wbp_s_j4']?></td><td class="fw-bold" style="color: #F59E0B;"><?= $data['out_wbp_s_total']?></td></tr>
                        <tr><td class="fw-bold">T</td><td><?= $data['out_wbp_t_j1']?></td><td><?= $data['out_wbp_t_j2']?></td><td><?= $data['out_wbp_t_j3']?></td><td><?= $data['out_wbp_t_j4']?></td><td class="fw-bold" style="color: #F59E0B;"><?= $data['out_wbp_t_total']?></td></tr>
                        <tr style="background-color: var(--bg-tertiary);"><td class="fw-bold">N</td><td colspan="5" class="fw-bold"><?= $data['out_wbp_n_total'] ?></td></tr>
                    </tbody>
                </table>
            </div>
            
            <?php else: echo "<div class='text-center py-5' style='color: var(--text-muted);'>Belum ada data.</div>"; endif; ?>
        </div>

        <div class="tab-pane fade" id="tab3">
            <?php if($ada_ukur): ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 rounded-3 h-100 info-box-glass">
                        <h6 class="fw-bold mb-3" style="color: var(--text-primary);">Tegangan (Volt)</h6>
                        <table class="table table-sm text-center small m-0">
                            <tr><td style="color: var(--text-muted);">R-N</td><td class="fw-bold"><?= $data['teg_r_n'] ?></td><td style="color: var(--text-muted);">R-S</td><td class="fw-bold"><?= $data['teg_r_s'] ?></td></tr>
                            <tr><td style="color: var(--text-muted);">S-N</td><td class="fw-bold"><?= $data['teg_s_n'] ?></td><td style="color: var(--text-muted);">S-T</td><td class="fw-bold"><?= $data['teg_s_t'] ?></td></tr>
                            <tr><td style="color: var(--text-muted);">T-N</td><td class="fw-bold"><?= $data['teg_t_n'] ?></td><td style="color: var(--text-muted);">R-T</td><td class="fw-bold"><?= $data['teg_r_t'] ?></td></tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 h-100 info-box-glass">
                        <h6 class="fw-bold mb-3" style="color: var(--text-primary);">Grounding (Ohm)</h6>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span style="color: var(--text-muted);">Body Trafo</span>
                                <span><?= ($data['pent_body']=='Ada') ? $data['nilai_pent_body'].' Ω' : '<span class="badge bg-secondary">Tidak Ada</span>' ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span style="color: var(--text-muted);">PHBTR</span>
                                <span><?= ($data['pent_phbtr']=='Ada') ? $data['nilai_pent_phbtr'].' Ω' : '<span class="badge bg-secondary">Tidak Ada</span>' ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 border-0">
                                <span style="color: var(--text-muted);">Arrester</span>
                                <span><?= ($data['pent_arrester']=='Ada') ? $data['nilai_pent_arrester'].' Ω' : '<span class="badge bg-secondary">Tidak Ada</span>' ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php else: echo "<div class='text-center py-5' style='color: var(--text-muted);'>Belum ada data.</div>"; endif; ?>
        </div>

        <div class="tab-pane fade" id="tab4">
            <?php if($ada_ukur): 
                // Helper Warna
                function getStatusColor($status) {
                    if($status == 'OVERLOAD') return ['#EF4444', 'rgba(239, 68, 68, 0.1)']; // Merah
                    if($status == 'UNDERLOAD') return ['#F59E0B', 'rgba(245, 158, 11, 0.1)']; // Kuning
                    return ['#10B981', 'rgba(16, 185, 129, 0.1)']; // Hijau
                }
                list($lwbp_color, $lwbp_bg) = getStatusColor($data['ket_lwbp']);
                list($wbp_color, $wbp_bg) = getStatusColor($data['ket_wbp']);
            ?>
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="p-3 rounded-3 text-center h-100" style="background: <?= $lwbp_bg ?>; border: 1px solid <?= $lwbp_color ?>;">
                        <h6 class="small fw-bold mb-1" style="color: <?= $lwbp_color ?>">LWBP</h6>
                        <h3 class="fw-bold mb-1" style="color: var(--text-primary);"><?= number_format($data['daya_terpakai_lwbp'], 2) ?>%</h3>
                        <span class="badge rounded-pill" style="background: <?= $lwbp_color ?>"><?= $data['ket_lwbp'] ?></span>
                        <hr style="border-color: <?= $lwbp_color ?>; opacity: 0.3;">
                        <small style="color: var(--text-muted);">Ketidakseimbangan: <b style="color: <?= $lwbp_color ?>"><?= $data['ketidakseimbangan_lwbp'] ?></b></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 text-center h-100" style="background: <?= $wbp_bg ?>; border: 1px solid <?= $wbp_color ?>;">
                        <h6 class="small fw-bold mb-1" style="color: <?= $wbp_color ?>">WBP</h6>
                        <h3 class="fw-bold mb-1" style="color: var(--text-primary);"><?= number_format($data['daya_terpakai_wbp'], 2) ?>%</h3>
                        <span class="badge rounded-pill" style="background: <?= $wbp_color ?>"><?= $data['ket_wbp'] ?></span>
                        <hr style="border-color: <?= $wbp_color ?>; opacity: 0.3;">
                        <small style="color: var(--text-muted);">Ketidakseimbangan: <b style="color: <?= $wbp_color ?>"><?= $data['ketidakseimbangan_wbp'] ?></b></small>
                    </div>
                </div>
            </div>

            <div class="p-3 rounded-3 d-flex justify-content-between small info-box-glass">
                <span style="color: var(--text-muted);"><i class="fas fa-calendar me-1"></i> <?= date('d M Y', strtotime($data['tanggal_ukur'])) ?></span>
                <span style="color: var(--text-muted);"><i class="fas fa-user me-1"></i> <?= $data['petugas'] ?></span>
            </div>
            <?php if(!empty($data['keterangan'])): ?>
                <div class="mt-2 p-2 small fst-italic" style="color: var(--text-secondary); border-left: 3px solid var(--primary-color); padding-left: 10px;">
                    "<?= $data['keterangan'] ?>"
                </div>
            <?php endif; ?>

            <?php else: echo "<div class='text-center py-5' style='color: var(--text-muted);'>Belum ada data.</div>"; endif; ?>
        </div>
    </div>
</div>

<div class="modal-footer p-3" style="border-top: 1px solid var(--border-color);">
    <button type="button" class="btn btn-light btn-sm border px-4" data-bs-dismiss="modal">Tutup</button>
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <a href="edit_pengukuran.php?id=<?= $id ?>" class="btn btn-warning btn-sm fw-bold px-3 text-dark"><i class="fas fa-edit me-1"></i> Edit Data</a>
    <?php endif; ?>
</div>

<?php } ?>