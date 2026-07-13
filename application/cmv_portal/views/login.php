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
:root {
    --aksara-yellow: #f2ad0d;
    --aksara-yellow-soft: #fff7df;
    --aksara-orange: #fb923c;
    --aksara-black: #111827;
    --aksara-blue-soft: #eef6ff;
    --aksara-green-soft: #ecfdf5;
}

body {
    background:
        /* radial-gradient(circle at top left, rgba(242, 173, 13, .26), transparent 28%), */
        radial-gradient(circle at bottom right, rgba(17, 24, 39, .10), transparent 30%),
        linear-gradient(135deg, #fffaf0, #ffffff 48%, #f8fbff);
}

.login-shell {
    max-width: 430px;
    margin: 0 auto;
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding: 20px;
}

.login-card {
    border: 1px solid rgba(242, 173, 13, .32);
    border-radius: 22px;
    box-shadow: 0 16px 42px rgba(15, 23, 42, .10);
    width: 100%;
    overflow: hidden;
    background: linear-gradient(180deg, #ffffff, #fffdf8);
}

.login-card .card-body {
    position: relative;
}

.login-card .card-body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 24px;
    right: 24px;
    height: 4px;
    border-radius: 0 0 999px 999px;
    /* background: linear-gradient(90deg, var(--aksara-black), var(--aksara-yellow), var(--aksara-orange)); */
}

.login-card img {
    padding: 8px;
    border-radius: 18px;
    background: #fff;
    /* box-shadow: 0 8px 24px rgba(242, 173, 13, .14); */
}

.login-card h4 {
    color: var(--aksara-black);
    letter-spacing: .3px;
}

.form-label {
    color: #334155;
}

.form-control {
    min-height: 46px;
    border-radius: 12px;
    border-color: #e8eef6;
    background: #fff;
}

.form-control:focus {
    border-color: var(--aksara-yellow);
    box-shadow: 0 0 0 .18rem rgba(242, 173, 13, .18);
}

.btn-login {
    min-height: 46px;
    border-radius: 12px;
    font-weight: 800;
    border: 0;
    color: var(--aksara-black);
    background: linear-gradient(135deg, var(--aksara-yellow), var(--aksara-orange));
    box-shadow: 0 10px 22px rgba(242, 173, 13, .24);
}

.btn-login:hover {
    color: var(--aksara-black);
    background: linear-gradient(135deg, #e7a006, #f97316);
}

.alert-light {
    background: linear-gradient(135deg, var(--aksara-yellow-soft), #ffffff);
    border-color: rgba(242, 173, 13, .28) !important;
    color: #64748b;
}
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
                    <button type="submit" class="btn btn-primary btn-login w-100 ">Login</button>
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
