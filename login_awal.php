<?php
session_start();

// Kode Akses Umum
$kode_akses_umum = "pln2025"; 

if(isset($_POST['masuk_tamu'])){
    $input = $_POST['kode'];
    if($input == $kode_akses_umum){
        $_SESSION['status'] = "login";
        $_SESSION['role'] = "tamu";
        $_SESSION['nama'] = "Pengunjung";
        header("Location: index.php");
        exit;
    } else {
        $error = "Kode Akses Salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Monitoring Trafo</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* Style Khusus Login yang menggunakan Variabel dari style.css */
        body {
            background-color: var(--bg-primary); /* Ikuti warna tema */
            color: var(--text-primary);
            
            /* Pattern Latar Belakang (Otomatis berubah warna mengikuti tema) */
            background-image: radial-gradient(var(--text-muted) 0.5px, transparent 0.5px);
            background-size: 20px 20px;
            
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Tombol Ganti Tema */
        .theme-toggle-btn {
            position: absolute;
            top: 20px; right: 20px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            width: 45px; height: 45px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-md);
            transition: 0.3s; z-index: 100;
        }
        .theme-toggle-btn:hover { transform: scale(1.1); box-shadow: var(--shadow-heavy); }

        /* Efek Lingkaran Blur (Glow) */
        .circle-bg {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.4; /* Sedikit transparan biar tidak nabrak teks */
        }
        .circle-1 { width: 300px; height: 300px; background: var(--primary-gradient); top: -50px; left: -50px; }
        .circle-2 { width: 250px; height: 250px; background: var(--secondary-gradient); bottom: -50px; right: -50px; }

        /* Kartu Login */
        .login-card {
            background: var(--bg-glass); /* Efek Kaca */
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            box-shadow: var(--shadow-heavy);
            padding: 3rem;
            width: 90%; max-width: 400px;
            text-align: center;
            position: relative; overflow: hidden;
        }

        /* Garis Aksen Atas */
        .login-card::before {
            content: ''; position: absolute;
            top: 0; left: 0; width: 100%; height: 5px;
            background: var(--primary-gradient);
        }

        .logo-icon {
            width: 80px; height: 80px;
            background: var(--primary-gradient);
            color: white;
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 25px rgba(42, 167, 225, 0.4);
        }

        .form-control {
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 12px;
            padding: 12px;
            font-size: 1.1rem;
            text-align: center;
            letter-spacing: 3px;
            font-weight: 600;
        }
        
        .form-control:focus {
            background: var(--bg-secondary);
            border-color: #2AA7E1;
            color: var(--text-primary);
            box-shadow: 0 0 0 4px rgba(42, 167, 225, 0.2);
        }
        
        .form-control::placeholder { color: var(--text-muted); opacity: 0.5; letter-spacing: 1px; }

        .btn-login {
            background: var(--primary-gradient);
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            color: white;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(42, 167, 225, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(42, 167, 225, 0.5);
        }

        .footer-text { margin-top: 2rem; font-size: 0.8rem; color: var(--text-muted); }
    </style>
</head>
<body>

    <button class="theme-toggle-btn" id="themeToggle" title="Ganti Tema">
        <i class="fas fa-moon"></i>
    </button>

    <div class="circle-bg circle-1"></div>
    <div class="circle-bg circle-2"></div>

    <div class="login-card">
        <div class="logo-icon">
            <i class="fas fa-bolt"></i>
        </div>
        
        <h3 class="fw-bold mb-1" style="color: var(--text-primary);">PLN TRAFO</h3>
        <p class="small mb-4" style="color: var(--text-secondary);">Monitoring Beban & Distribusi</p>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger py-2 small border-0 shadow-sm mb-3 rounded-3">
                <i class="fas fa-exclamation-circle me-1"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="small fw-bold mb-2" style="color: var(--text-muted); letter-spacing: 1px;">KODE AKSES</label>
                <input type="password" name="kode" class="form-control" placeholder="••••••••" required autofocus>
            </div>
            <div class="d-grid">
                <button type="submit" name="masuk_tamu" class="btn btn-login">
                    MASUK <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>
        </form>

        <div class="footer-text">
            &copy; 2025 PLN ULP Tanjung Balai<br>
            <span class="opacity-50">Secure Access System</span>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('themeToggle');
        const icon = toggleBtn.querySelector('i');
        const html = document.documentElement;

        // 1. Cek simpanan tema
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        updateIcon(savedTheme);

        // 2. Fungsi Ganti Tema
        toggleBtn.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });

        function updateIcon(theme) {
            if(theme === 'dark') {
                icon.className = 'fas fa-sun';
            } else {
                icon.className = 'fas fa-moon';
            }
        }
    </script>

</body>
</html>