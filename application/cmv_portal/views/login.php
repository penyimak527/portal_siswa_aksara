<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Login Siswa | Portal Siswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/logo-a.jpg">
    <script src="<?= base_url(); ?>assets/js/config.js"></script>
    <link href="<?= base_url(); ?>assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <style>
        body { background: linear-gradient(135deg, #eef6ff, #ffffff); }
        .login-shell { max-width: 430px; margin: 0 auto; min-height: 100vh; display: flex; align-items: center; padding: 20px; }
        .login-card { border: 0; border-radius: 22px; box-shadow: 0 14px 40px rgba(15, 23, 42, .08); width: 100%; }
        .form-control { min-height: 46px; border-radius: 12px; }
        .btn-login { min-height: 46px; border-radius: 12px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="login-shell">
        <div class="card login-card">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <img src="<?= base_url(); ?>assets/logo-aksara.png" onerror="this.src='<?= base_url(); ?>assets/logo-a.jpg'" height="78" alt="Logo">
                    <h4 class="fw-bold mt-3 mb-1">LOGIN SISWA</h4>
                    <p class="text-muted mb-0">Portal Latihan Soal Bimbel Aksara</p>
                </div>

                <?= $this->session->flashdata('message'); ?>

                <form action="<?= base_url('login/masuk') ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIS</label>
                        <input type="text" name="nis" class="form-control" placeholder="Masukkan NIS ..." autocomplete="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password ..." autocomplete="current-password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-login w-100">Login</button>
                </form>

                <div class="alert alert-light border mt-4 mb-0 text-center small">
                    Lupa password? Silakan hubungi admin / tentor.
                </div>
            </div>
        </div>
    </div>
    <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>
    <script src="<?= base_url(); ?>assets/js/app.js"></script>
</body>
</html>
