<?php
session_start();
include 'koneksi.php';

// 1. CEK LOGIN
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("Location: login_awal.php");
    exit;
}

// 2. HEADER EXCEL
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_Pengukuran_Trafo_".date('d-m-Y').".xls");

// Fungsi Format Angka (Ubah titik jadi koma untuk Excel Indo)
function formatAngka($angka) {
    return ($angka == 0 || $angka == '') ? '0' : str_replace('.', ',', $angka);
}
?>

<table border="1">
    <thead>
        <tr style="background-color: #2AA7E1; color: white; font-weight: bold; text-align: center;">
            <th style="background-color: #333;">DATA GARDU</th>
            <th colspan="8" style="background-color: #2AA7E1;">INCOMING</th>
            <th colspan="16" style="background-color: #F59E0B;">OUTGOING LWBP</th>
            <th colspan="16" style="background-color: #0EA5E9;">OUTGOING WBP</th>
            <th colspan="6" style="background-color: #6B7280;">TEGANGAN</th>
            <th colspan="6" style="background-color: #8B5CF6;">PENTANAHAN</th>
            <th colspan="6" style="background-color: #10B981;">ANALISA BEBAN</th>
            <th colspan="3" style="background-color: #374151;">INFO UKUR</th>
        </tr>
        
        <tr style="background-color: #e5e7eb; font-weight: bold; text-align: center;">
            <th>No Gardu</th>
            <th>Alamat</th>
            <th>Posko</th>
            <th>Penyulang</th>
            <th>Merk</th>
            <th>Daya Trafo (KVA)</th>
            <th>Nomor Seri</th>
            <th>Tiang Trafo</th>

            <th>Incoming LWBP R (A)</th>
            <th>Incoming LWBP S (A)</th>
            <th>Incoming LWBP T (A)</th>
            <th>Incoming LWBP N (A)</th>
            <th>Incoming WBP R (A)</th>
            <th>Incoming WBP S (A)</th>
            <th>Incoming WBP T (A)</th>
            <th>Incoming WBP N (A)</th>

            <th>Outgoing LWBP R J1 (A)</th>
            <th>Outgoing LWBP R J2 (A)</th>
            <th>Outgoing LWBP R J3 (A)</th>
            <th>Outgoing LWBP R J4 (A)</th>
            <th>Outgoing LWBP R Total (A)</th>
            
            <th>Outgoing LWBP S J1 (A)</th>
            <th>Outgoing LWBP S J2 (A)</th>
            <th>Outgoing LWBP S J3 (A)</th>
            <th>Outgoing LWBP S J4 (A)</th>
            <th>Outgoing LWBP S Total (A)</th>

            <th>Outgoing LWBP T J1 (A)</th>
            <th>Outgoing LWBP T J2 (A)</th>
            <th>Outgoing LWBP T J3 (A)</th>
            <th>Outgoing LWBP T J4 (A)</th>
            <th>Outgoing LWBP T Total (A)</th>

            <th>Outgoing LWBP N Total (A)</th>

            <th>Outgoing WBP R J1 (A)</th>
            <th>Outgoing WBP R J2 (A)</th>
            <th>Outgoing WBP R J3 (A)</th>
            <th>Outgoing WBP R J4 (A)</th>
            <th>Outgoing WBP R Total (A)</th>

            <th>Outgoing WBP S J1 (A)</th>
            <th>Outgoing WBP S J2 (A)</th>
            <th>Outgoing WBP S J3 (A)</th>
            <th>Outgoing WBP S J4 (A)</th>
            <th>Outgoing WBP S Total (A)</th>

            <th>Outgoing WBP T J1 (A)</th>
            <th>Outgoing WBP T J2 (A)</th>
            <th>Outgoing WBP T J3 (A)</th>
            <th>Outgoing WBP T J4 (A)</th>
            <th>Outgoing WBP T Total (A)</th>

            <th>Outgoing WBP N Total (A)</th>

            <th>R-N (V)</th>
            <th>S-N (V)</th>
            <th>T-N (V)</th>
            <th>R-S (V)</th>
            <th>S-T (V)</th>
            <th>R-T (V)</th>

            <th>Pentanahan Body Trafo (Ohm)</th> <th>Nilai Pentanahan Body</th>       <th>Pentanahan PHBTR Trafo (Ohm)</th>
            <th>Nilai Pentanahan PHBTR</th>
            <th>Pentanahan Arrester (Ohm)</th>
            <th>Nilai Pentanahan Arrester</th>

            <th>Daya Terpakai LWBP</th>
            <th>Keterangan LWBP</th>
            <th>Daya Terpakai WBP</th>
            <th>Keterangan WBP</th>
            <th>Ketidak Seimbangan LWBP</th>
            <th>Ketidak Seimbangan WBP</th>

            <th>Petugas</th>
            <th>Tanggal Ukur</th>
            <th>Koordinat</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // QUERY: Ambil data Gardu + Pengukuran Terakhir
        // Menggunakan LEFT JOIN agar gardu yang belum diukur tetap muncul (opsional)
        // Disini pakai JOIN biasa agar yang didownload hanya yang sudah diukur
        $query = mysqli_query($koneksi, "SELECT p.*, g.no_gardu, g.alamat, g.posko, g.penyulang, g.merk, g.daya_trafo, g.nomor_seri, g.tiang_trafo, g.koordinat
                                         FROM tb_pengukuran p 
                                         JOIN tb_gardu g ON p.gardu_id = g.id 
                                         ORDER BY p.tanggal_ukur DESC, p.id DESC");

        while($row = mysqli_fetch_assoc($query)):
        ?>
        <tr>
            <td><?= $row['no_gardu'] ?></td>
            <td><?= $row['alamat'] ?></td>
            <td><?= $row['posko'] ?></td>
            <td><?= $row['penyulang'] ?></td>
            <td><?= $row['merk'] ?></td>
            <td><?= $row['daya_trafo'] ?></td>
            <td>'<?= $row['nomor_seri'] ?></td>
            <td><?= $row['tiang_trafo'] ?></td>

            <td><?= formatAngka($row['in_lwbp_r']) ?></td>
            <td><?= formatAngka($row['in_lwbp_s']) ?></td>
            <td><?= formatAngka($row['in_lwbp_t']) ?></td>
            <td><?= formatAngka($row['in_lwbp_n']) ?></td>
            <td><?= formatAngka($row['in_wbp_r']) ?></td>
            <td><?= formatAngka($row['in_wbp_s']) ?></td>
            <td><?= formatAngka($row['in_wbp_t']) ?></td>
            <td><?= formatAngka($row['in_wbp_n']) ?></td>

            <td><?= formatAngka($row['out_lwbp_r_j1']) ?></td> <td><?= formatAngka($row['out_lwbp_r_j2']) ?></td> <td><?= formatAngka($row['out_lwbp_r_j3']) ?></td> <td><?= formatAngka($row['out_lwbp_r_j4']) ?></td> <td style="background:#FEF3C7; font-weight:bold;"><?= formatAngka($row['out_lwbp_r_total']) ?></td>
            <td><?= formatAngka($row['out_lwbp_s_j1']) ?></td> <td><?= formatAngka($row['out_lwbp_s_j2']) ?></td> <td><?= formatAngka($row['out_lwbp_s_j3']) ?></td> <td><?= formatAngka($row['out_lwbp_s_j4']) ?></td> <td style="background:#FEF3C7; font-weight:bold;"><?= formatAngka($row['out_lwbp_s_total']) ?></td>
            <td><?= formatAngka($row['out_lwbp_t_j1']) ?></td> <td><?= formatAngka($row['out_lwbp_t_j2']) ?></td> <td><?= formatAngka($row['out_lwbp_t_j3']) ?></td> <td><?= formatAngka($row['out_lwbp_t_j4']) ?></td> <td style="background:#FEF3C7; font-weight:bold;"><?= formatAngka($row['out_lwbp_t_total']) ?></td>
            <td style="background:#E5E7EB; font-weight:bold;"><?= formatAngka($row['out_lwbp_n_total']) ?></td>

            <td><?= formatAngka($row['out_wbp_r_j1']) ?></td> <td><?= formatAngka($row['out_wbp_r_j2']) ?></td> <td><?= formatAngka($row['out_wbp_r_j3']) ?></td> <td><?= formatAngka($row['out_wbp_r_j4']) ?></td> <td style="background:#E0F2FE; font-weight:bold;"><?= formatAngka($row['out_wbp_r_total']) ?></td>
            <td><?= formatAngka($row['out_wbp_s_j1']) ?></td> <td><?= formatAngka($row['out_wbp_s_j2']) ?></td> <td><?= formatAngka($row['out_wbp_s_j3']) ?></td> <td><?= formatAngka($row['out_wbp_s_j4']) ?></td> <td style="background:#E0F2FE; font-weight:bold;"><?= formatAngka($row['out_wbp_s_total']) ?></td>
            <td><?= formatAngka($row['out_wbp_t_j1']) ?></td> <td><?= formatAngka($row['out_wbp_t_j2']) ?></td> <td><?= formatAngka($row['out_wbp_t_j3']) ?></td> <td><?= formatAngka($row['out_wbp_t_j4']) ?></td> <td style="background:#E0F2FE; font-weight:bold;"><?= formatAngka($row['out_wbp_t_total']) ?></td>
            <td style="background:#E5E7EB; font-weight:bold;"><?= formatAngka($row['out_wbp_n_total']) ?></td>

            <td><?= formatAngka($row['teg_r_n']) ?></td> <td><?= formatAngka($row['teg_s_n']) ?></td> <td><?= formatAngka($row['teg_t_n']) ?></td>
            <td style="color:blue; font-weight:bold;"><?= formatAngka($row['teg_r_s']) ?></td> <td style="color:blue; font-weight:bold;"><?= formatAngka($row['teg_s_t']) ?></td> <td style="color:blue; font-weight:bold;"><?= formatAngka($row['teg_r_t']) ?></td>

            <td><?= $row['pent_body'] ?></td>     <td><?= ($row['pent_body']=='Ada') ? formatAngka($row['nilai_pent_body']) : '-' ?></td>
            <td><?= $row['pent_phbtr'] ?></td>    <td><?= ($row['pent_phbtr']=='Ada') ? formatAngka($row['nilai_pent_phbtr']) : '-' ?></td>
            <td><?= $row['pent_arrester'] ?></td> <td><?= ($row['pent_arrester']=='Ada') ? formatAngka($row['nilai_pent_arrester']) : '-' ?></td>

            <td><?= formatAngka($row['daya_terpakai_lwbp']) ?>%</td> <td><?= $row['ket_lwbp'] ?></td>
            <td><?= formatAngka($row['daya_terpakai_wbp']) ?>%</td>  <td><?= $row['ket_wbp'] ?></td>
            <td><?= $row['ketidakseimbangan_lwbp'] ?></td> <td><?= $row['ketidakseimbangan_wbp'] ?></td>

            <td><?= $row['petugas'] ?></td>
            <td><?= $row['tanggal_ukur'] ?></td>
            <td><?= $row['koordinat'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>