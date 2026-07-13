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
        .portal-login-page {
            --aksara-yellow: #f2ad0d;
            --aksara-yellow-dark: #c98600;
            --aksara-yellow-hover: #d99a00;
            --aksara-yellow-soft: #fff6dd;
            --aksara-black: #050505;
            --aksara-white: #ffffff;
            --aksara-dark: #111827;
            --aksara-muted: #64748b;
            background: #ffffff;
        }

        .portal-login-page .login-shell {
            max-width: 430px;
            margin: 0 auto;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
        }

        .portal-login-page .login-card {
            /* border: 1px solid rgba(242, 173, 13, .38); */
            border-radius: 22px;
            box-shadow: 0 16px 42px rgba(15, 23, 42, .10);
            width: 100%;
            overflow: hidden;
            background: #ffffff;
        }

        .portal-login-page .login-card .card-body {
            position: relative;
            /* border-top: 5px solid var(--aksara-yellow); */
        }

        .portal-login-page .login-card img {
            padding: 8px;
            border-radius: 18px;
            background: #fff;
            /* border: 1px solid rgba(242, 173, 13, .25); */
        }

        .portal-login-page .login-card h4 {
            color: var(--aksara-black);
            letter-spacing: .3px;
        }

        .portal-login-page .form-label {
            color: #334155;
        }

        .portal-login-page .form-control {
            min-height: 46px;
            border-radius: 12px;
            border-color: #e8eef6;
            background: #fff;
        }

        .portal-login-page .form-control:focus {
            border-color: var(--aksara-yellow);
            box-shadow: 0 0 0 .18rem rgba(242, 173, 13, .18);
        }

        .portal-login-page .btn-login {
            min-height: 46px;
            border-radius: 12px;
            font-weight: 800;
            border-color: var(--aksara-yellow);
            color: var(--aksara-white);
            background: var(--aksara-yellow);
            box-shadow: 0 10px 22px rgba(242, 173, 13, .20);
        }

        .portal-login-page .btn-login:hover,
        .portal-login-page .btn-login:focus {
            color: #fff;
            background: var(--aksara-yellow-hover);
            border-color: var(--aksara-yellow-hover);
        }

        .portal-login-page .alert-light {
            background: var(--aksara-yellow-soft);
            border-color: rgba(242, 173, 13, .28) !important;
            color: #64748b;
        }
    </style>
</head>
<body class="portal-login-page">
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
