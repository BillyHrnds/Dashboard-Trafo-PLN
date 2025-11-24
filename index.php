    <?php
    session_start();
    include 'koneksi.php';

    // 1. CEK AKSES GERBANG DEPAN
    if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
        header("Location: login_awal.php");
        exit;
    }

    // 2. PROSES LOGIN ADMIN (UPDATE: KE TABEL ADMIN)
    if(isset($_POST['login_admin'])){
        $username = mysqli_real_escape_string($koneksi, $_POST['username']);
        $password = $_POST['password'];
        
        // Cek ke tabel admin
        $cek = mysqli_query($koneksi, "SELECT * FROM tb_admin WHERE username='$username'");
        if(mysqli_num_rows($cek) > 0){
            $data = mysqli_fetch_assoc($cek);
            if(password_verify($password, $data['password'])){
                $_SESSION['role'] = "admin"; 
                echo "<script>alert('Login Berhasil! Selamat bekerja.'); window.location='index.php';</script>";
            } else { echo "<script>alert('Password Salah!');</script>"; }
        } else { echo "<script>alert('Username Tidak Ditemukan!');</script>"; }
    }

    // 3. LOGOUT ADMIN
    if(isset($_GET['mode']) && $_GET['mode'] == 'keluar_admin'){
        $_SESSION['role'] = "tamu";
        header("Location: index.php"); exit;
    }



    // VARIABEL SYSTEM
    $isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin');
    $namaUser = $isAdmin ? 'Admin TJ Balai' : 'Pengunjung';
    $roleUser = $isAdmin ? 'Administrator' : 'Guest View';

    // 4. HITUNG DATA DASHBOARD
    $q_total = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM tb_gardu"));
    $total_data = $q_total['c'];

    // Logic Status
    $q_aman = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM tb_pengukuran WHERE ket_lwbp = 'NORMAL' AND id IN (SELECT MAX(id) FROM tb_pengukuran GROUP BY gardu_id)"));
    $total_aman = $q_aman['c'];

    $q_warn = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM tb_pengukuran WHERE ket_lwbp = 'UNDERLOAD' AND id IN (SELECT MAX(id) FROM tb_pengukuran GROUP BY gardu_id)"));
    $total_warn = $q_warn['c'];

    $q_ob = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM tb_pengukuran WHERE ket_lwbp = 'OVERLOAD' AND id IN (SELECT MAX(id) FROM tb_pengukuran GROUP BY gardu_id)"));
    $total_ob = $q_ob['c'];

    // PENCARIAN
    $keyword = isset($_GET['cari']) ? $_GET['cari'] : "";
    $sql_filter = $keyword ? "WHERE no_gardu LIKE '%$keyword%' OR alamat LIKE '%$keyword%'" : "";
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PLN Dashboard 2025</title>
        
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/style.css">
        
        <style>
            /* Override Bootstrap conflicts */
            a { text-decoration: none; }
            
            /* Tabel Glassmorphism */
            .table-glass {
                color: var(--text-primary) !important;
                --bs-table-bg: transparent;
                --bs-table-color: var(--text-primary);
                --bs-table-border-color: var(--border-color);
            }
            .table-glass thead th {
                background-color: rgba(255,255,255,0.05);
                color: var(--text-primary);
                border-bottom: 2px solid var(--border-color);
                font-size: 0.85rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .table-glass td {
                vertical-align: middle;
                font-size: 0.9rem;
            }
            
            /* Badge Status */
            .badge-status { padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; }
            .st-normal { background: rgba(79, 172, 254, 0.2); color: #4facfe; border: 1px solid rgba(79, 172, 254, 0.3); }
            .st-warn { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); } /* Kuning */
            .st-danger { background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); } /* Merah */
            .st-null { background: rgba(107, 114, 128, 0.2); color: #9ca3af; border: 1px solid rgba(107, 114, 128, 0.3); }

            /* Modal Fix */
            .modal-content {
                background-color: var(--bg-secondary);
                border: 1px solid var(--border-color);
                backdrop-filter: blur(20px);
                color: var(--text-primary);
            }
            .modal-header, .modal-footer { border-color: var(--border-color); }
            .btn-close { filter: invert(0.5); } 
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

                <nav class="sidebar-nav">
                    <ul class="nav-menu">
                        <li class="nav-item active">
                            <a href="index.php" class="nav-link"><i class="fas fa-home"></i> <span>Dashboard</span></a>
                        </li>
                        <li class="nav-item">
                            <a href="map_view.php" class="nav-link"><i class="fas fa-map-marked-alt"></i> <span>Peta Sebaran</span></a>
                        </li>
                        <li class="nav-item">
                            <a href="export_excel.php" class="nav-link"><i class="fas fa-file-excel"></i> <span>Export Excel</span></a>
                        </li>

                        <?php if($isAdmin): ?>
                        <li style="margin: 1.5rem 1rem 0.5rem; font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                            Menu Petugas
                        </li>
                        <li class="nav-item">
                            <a href="tambah_gardu.php" class="nav-link"> <i class="fas fa-plus-circle"></i> <span>Tambah Gardu</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="input_pengukuran.php" class="nav-link"><i class="fas fa-calculator"></i> <span>Input Ukur</span></a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <div class="sidebar-footer">
                    <div class="user-profile">
                        
                        <div class="user-info">
                            <div class="user-name"><?= $namaUser ?></div>
                            <div class="user-role"><?= $roleUser ?></div>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="main-content">
                <header class="header">
                    <div class="header-left">
                        <button id="mobileToggle" class="mobile-toggle"><i class="fas fa-bars"></i></button>
                        <div class="header-text">
                            <h1 class="page-title">Monitoring Trafo</h1>
                            <p class="page-subtitle">Halo <b><?= $namaUser ?></b>, berikut ringkasan beban trafo hari ini.</p>
                        </div>
                    </div>
                    
                    <div class="header-right">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <form action="" method="GET" style="display:inline;">
                                <input type="text" name="cari" placeholder="Cari No Gardu..." value="<?= $keyword ?>">
                            </form>
                        </div>
                        
                        <div class="header-actions">
                            <button id="themeToggle" class="action-btn theme-toggle" title="Toggle Theme"><i class="fas fa-moon"></i></button>
                            
                            <?php if($isAdmin): ?>
                                <a href="?mode=keluar_admin" class="action-btn" title="Kunci Admin" onclick="return confirm('Kunci Akses Admin?')"><i class="fas fa-lock"></i></a>
                            <?php else: ?>
                                <button class="action-btn" title="Login Admin" data-bs-toggle="modal" data-bs-target="#modalLoginAdmin"><i class="fas fa-key"></i></button>
                            <?php endif; ?>
                            
                            <a href="logout.php" class="action-btn" title="Keluar" onclick="return confirm('Keluar?')"><i class="fas fa-sign-out-alt"></i></a>
                        </div>
                    </div>
                </header>

                <div class="dashboard-content">
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="stat-card blue">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div><h2 class="stat-value"><?= $total_data ?></h2><div class="stat-label">Total Gardu</div><small class="text-muted">Unit Terdata</small></div>
                                    <div class="stat-icon" style="background: var(--primary-gradient);"><i class="fas fa-database"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card green">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div><h2 class="stat-value text-success"><?= $total_aman ?></h2><div class="stat-label">Kondisi Aman</div><small class="text-success fw-bold">Normal (20-80%)</small></div>
                                    <div class="stat-icon" style="background: var(--success-gradient);"><i class="fas fa-check-circle"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card yellow">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div><h2 class="stat-value" style="color: #F59E0B;"><?= $total_warn ?></h2><div class="stat-label">Underload</div><small class="fw-bold" style="color: #F59E0B;">Warning (< 20%)</small></div>
                                    <div class="stat-icon" style="background: var(--warning-gradient);"><i class="fas fa-exclamation-triangle"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card red">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div><h2 class="stat-value text-danger"><?= $total_ob ?></h2><div class="stat-label">Overload</div><small class="text-danger fw-bold">Bahaya (> 80%)</small></div>
                                    <div class="stat-icon" style="background: var(--danger-gradient);"><i class="fas fa-bomb"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="chart-title">Data Gardu Terkini</h3>
                                <p class="chart-subtitle">Daftar gardu dan status beban terakhir</p>
                            </div>
                        </div>
                        <div class="table-responsive px-3">
                            <table class="table table-glass table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>No Gardu</th>
                                        <th>Alamat</th>
                                        <th>Posko</th> <th>Merk</th>  <th>Daya (kVA)</th>
                                        <th>Tiang</th> <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($koneksi, "SELECT g.*, (SELECT ket_lwbp FROM tb_pengukuran WHERE gardu_id = g.id ORDER BY id DESC LIMIT 1) as status_lwbp FROM tb_gardu g $sql_filter ORDER BY g.no_gardu ASC LIMIT 50");
                                    if(mysqli_num_rows($query) > 0):
                                    while($row = mysqli_fetch_assoc($query)):
                                        $st = $row['status_lwbp'];
                                        $cls = 'st-null';
                                        if($st=='NORMAL') $cls='st-normal'; if($st=='UNDERLOAD') $cls='st-warn'; if($st=='OVERLOAD') $cls='st-danger';
                                    ?>
                                    <tr>
                                        <td class="fw-bold text-primary"><?= $row['no_gardu'] ?></td>
                                        <td class="small text-muted"><?= substr($row['alamat'], 0, 25) ?>...</td>
                                        <td class="small"><?= $row['posko'] ?></td>
                                        <td class="small"><?= $row['merk'] ?></td>
                                        <td class="fw-bold"><?= $row['daya_trafo'] ?></td>
                                        <td class="small"><?= $row['tiang_trafo'] ?></td>
                                        <td><span class="badge-status <?= $cls ?>"><?= $st ?? 'BELUM UKUR' ?></span></td>
                                        <td class="text-center">
                                            <div class="action-grid">
                                                <button class="action-btn btn-detail" data-id="<?= $row['id'] ?>" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </button>
        
                                            <?php if($isAdmin): ?>
                                                <a href="edit_pengukuran.php?id=<?= $row['id'] ?>" class="action-btn text-success" title="Update Ukur">
                                                    <i class="fas fa-clipboard-check"></i>
                                                </a>
            
                                                <a href="edit_gardu.php?id=<?= $row['id'] ?>" class="action-btn text-warning" title="Edit Data">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
            
                                                <a href="hapus_gardu.php?id=<?= $row['id'] ?>" class="action-btn text-danger" title="Hapus" onclick="return confirm('Hapus data ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
        
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                        <tr><td colspan="8" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </main>
        </div>

        <div class="modal fade" id="modalLoginAdmin" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content admin-glass-content rounded-4 overflow-hidden">
        
        <div class="modal-header admin-glass-header p-4 border-0 d-flex flex-column align-items-center position-relative">
            <button type="button" class="btn-close admin-glass-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
            
            <div class="admin-icon-box">
                <i class="fas fa-user-shield fa-2x text-white"></i>
            </div>
            
            <h5 class="fw-bold mt-2 mb-0">Login Petugas</h5>
            <p class="small text-white-50 m-0">Akses Administrator</p>
        </div>

        <div class="modal-body p-4 pt-2">
            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control form-control-lg admin-glass-input" placeholder="Username" required autocomplete="off">
                </div>
                
                <div class="mb-4">
                    <input type="password" name="password" class="form-control form-control-lg admin-glass-input" placeholder="Password" required>
                </div>

                <button type="submit" name="login_admin" class="btn w-100 fw-bold text-white py-2 shadow-lg" style="background: var(--primary-gradient); border-radius: 12px; border:none;">
                    MASUK <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>
        </div>

        </div>
    </div>
    </div>
        <div class="modal fade" id="modalDetail" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"><div class="modal-content" id="modalContent"><div class="text-center py-5"><div class="spinner-border text-primary"></div></div></div></div></div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/script.js"></script>
        
        <script>
            $(document).ready(function(){
                $('.btn-detail').click(function(){
                    var garduId = $(this).data('id');
                    $('#modalDetail').modal('show');
                    $.ajax({ url: 'ajax_detail.php', type: 'POST', data: {id_gardu: garduId}, success: function(res){ $('#modalContent').html(res); } });
                });
            });
        </script>

<script>
    // SCRIPT PENGENDALI TOMBOL MENU (HAMBURGER)
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.querySelector(".sidebar");
        const mobileToggle = document.getElementById("mobileToggle");

        if (mobileToggle) {
            mobileToggle.addEventListener("click", function(e) {
                e.stopPropagation(); // Cegah klik tembus
                sidebar.classList.toggle("active"); // Buka/Tutup Sidebar
            });
        }

        // Tutup sidebar kalau klik di luar (sembarang tempat)
        document.addEventListener("click", function(e) {
            if (window.innerWidth <= 1200) { // Sesuaikan angka ini dengan CSS
                if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
                    sidebar.classList.remove("active");
                }
            }
        });
    });
</script>
    </body>
    </html>