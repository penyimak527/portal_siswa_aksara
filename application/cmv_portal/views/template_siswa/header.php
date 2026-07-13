<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?= $title; ?> | Portal Siswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/logo-a.jpg">
<link href="<?= base_url(); ?>assets/vendor/jsvectormap/jsvectormap.min.css" rel="stylesheet" type="text/css">
	<script src="<?= base_url(); ?>assets/js/config.js"></script>
	<link href="<?= base_url(); ?>assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
	<link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= base_url(); ?>assets/css/lightbox.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="https://unpkg.com/@tabler/icons-webfont@latest/tabler-icons.min.css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" rel="stylesheet"
		type="text/css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit@3.1.3/build/jodit.min.css" />
	<link rel="stylesheet" href="https://smkryoyuwaraja.sch.id/assets/admin/css/lightbox.css" />
	<link href="<?= base_url(); ?>assets/css/jquery.autocomplete.css" rel="stylesheet" type="text/css" />
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .portal-siswa-page {
            --aksara-yellow: #f2ad0d;
            --aksara-yellow-dark: #c98600;
            --aksara-yellow-hover: #d99a00;
            --aksara-yellow-soft: #fff6dd;
            --aksara-yellow-soft-2: #fffaf0;
            --aksara-black: #050505;
            --aksara-white: #ffffff;
            --aksara-dark: #111827;
            --aksara-muted: #64748b;
            --aksara-border: #eef0f4;
            --aksara-blue: #2563eb;
            --aksara-blue-soft: #eff6ff;
            --aksara-green: #10b981;
            --aksara-green-soft: #ecfdf5;
            --aksara-purple: #8b5cf6;
            --aksara-purple-soft: #f5f3ff;
            --aksara-red: #ef4444;
            --aksara-red-soft: #fef2f2;
            --aksara-orange-soft: #fff1e7;
            background: #ffffff;
            padding-bottom: 78px;
        }

        .portal-siswa-page .student-shell {
            max-width: 820px;
            margin: 0 auto;
        }

        .portal-siswa-page .student-topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #ffffff;
            border-bottom: 0;
            box-shadow: 0 6px 18px rgba(5, 5, 5, .05);
        }

        .portal-siswa-page .student-topbar h5 {
            color: var(--aksara-black);
        }

        .portal-siswa-page .student-topbar small {
            color: var(--aksara-muted) !important;
        }

        .portal-siswa-page .student-card {
            border: 1px solid var(--aksara-border);
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
            overflow: hidden;
            background: #fff;
        }

        .portal-siswa-page .student-card-soft {
            background: var(--aksara-yellow-soft);
            border-color: rgba(242, 173, 13, .38);
        }

        .portal-siswa-page .student-card .card-body > h5.fw-bold:first-child,
        .portal-siswa-page .student-card .card-body .fw-bold.mb-0 {
            color: var(--aksara-dark);
        }

        .portal-siswa-page .info-label {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .portal-siswa-page .info-value {
            font-weight: 700;
            color: var(--aksara-black);
        }

        .portal-siswa-page .session-card {
            border: 1px solid rgba(242, 173, 13, .35);
            border-radius: 16px;
            padding: 14px;
            margin-bottom: 12px;
            background: #ffffff;
            box-shadow: 0 5px 16px rgba(15, 23, 42, .035);
        }

        .portal-siswa-page .student-card .bg-light {
            background-color: var(--aksara-yellow-soft) !important;
            border-color: rgba(242, 173, 13, .22) !important;
        }

        .portal-siswa-page .student-card .row.g-2 > div:nth-child(2n) .bg-light {
            background-color: var(--aksara-blue-soft) !important;
            border-color: rgba(37, 99, 235, .14) !important;
        }

        .portal-siswa-page .student-card .row.g-2 > div:nth-child(3n) .bg-light {
            background-color: var(--aksara-green-soft) !important;
            border-color: rgba(16, 185, 129, .14) !important;
        }

        .portal-siswa-page .student-card .row.g-2 > div:nth-child(4n) .bg-light {
            background-color: var(--aksara-purple-soft) !important;
            border-color: rgba(139, 92, 246, .14) !important;
        }

        .portal-siswa-page .btn-touch {
            min-height: 42px;
            border-radius: 12px;
        }

        .portal-siswa-page .btn-primary {
            background: var(--aksara-yellow);
            border-color: var(--aksara-yellow);
            color: var(--aksara-white);
            font-weight: 800;
        }

        .portal-siswa-page .btn-primary:hover,
        .portal-siswa-page .btn-primary:focus {
            background: var(--aksara-yellow-hover);
            border-color: var(--aksara-yellow-hover);
            color: #fff;
        }

        .portal-siswa-page .btn-outline-primary {
            border-color: var(--aksara-yellow);
            color: #a16207;
            font-weight: 700;
            background: #fff;
        }

        .portal-siswa-page .btn-outline-primary:hover,
        .portal-siswa-page .btn-outline-primary:focus {
            background: var(--aksara-yellow-hover);
            border-color: var(--aksara-yellow-hover);
            color: #fff;
        }

        .portal-siswa-page .badge-soft {
            background: var(--aksara-yellow-soft);
            color: #a16207;
            border: 1px solid rgba(242, 173, 13, .24);
        }

        .portal-siswa-page .badge.bg-primary,
        .portal-siswa-page .badge.bg-primary-subtle,
        .portal-siswa-page .badge.text-primary {
            background-color: var(--aksara-yellow-soft) !important;
            color: #a16207 !important;
            border: 1px solid rgba(242, 173, 13, .20);
        }

        .portal-siswa-page .badge.bg-success-subtle,
        .portal-siswa-page .badge.text-success {
            background-color: var(--aksara-green-soft) !important;
            color: #059669 !important;
        }

        .portal-siswa-page .badge.bg-info,
        .portal-siswa-page .badge.bg-info-subtle,
        .portal-siswa-page .badge.text-info {
            background-color: var(--aksara-blue-soft) !important;
            color: #2563eb !important;
        }

        .portal-siswa-page .badge.bg-warning,
        .portal-siswa-page .badge.bg-warning-subtle,
        .portal-siswa-page .badge.text-warning {
            background-color: var(--aksara-orange-soft) !important;
            color: #c2410c !important;
        }

        .portal-siswa-page .materi-bar {
            height: 8px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .portal-siswa-page .materi-bar span {
            display: block;
            height: 100%;
            background: var(--aksara-yellow);
        }

        .portal-siswa-page .result-number {
            font-size: 36px;
            line-height: 1;
            color: var(--aksara-yellow-dark) !important;
        }

        .portal-siswa-page .form-control:focus,
        .portal-siswa-page .form-select:focus {
            border-color: rgba(242, 173, 13, .72);
            box-shadow: 0 0 0 .2rem rgba(242, 173, 13, .16);
        }

        .portal-siswa-page .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 70;
            background: #ffffff;
            border-top: 0;
            box-shadow: 0 -8px 20px rgba(15, 23, 42, .06);
        }

        .portal-siswa-page .bottom-nav-inner {
            max-width: 820px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }

        .portal-siswa-page .bottom-nav a {
            padding: 9px 4px;
            text-align: center;
            color: #64748b;
            font-size: 11px;
            text-decoration: none;
        }

        .portal-siswa-page .bottom-nav a.active {
            color: var(--aksara-black);
            font-weight: 800;
        }

        .portal-siswa-page .bottom-nav a.active i {
            color: var(--aksara-yellow);
        }

        .portal-siswa-page .bottom-nav i {
            display: block;
            font-size: 20px;
            margin-bottom: 2px;
        }

        .portal-siswa-page .bottom-nav a:hover,
        .portal-siswa-page .bottom-nav a:focus {
            color: var(--aksara-black);
        }

        .portal-siswa-page .bottom-nav a:hover i,
        .portal-siswa-page .bottom-nav a:focus i {
            color: var(--aksara-yellow);
        }
    </style>
</head>
<body class="portal-siswa-page">
    <?php $uri = $this->uri->segment(1); ?>
    <div class="student-topbar" id="header">
        <div class="student-shell px-3 py-3 d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0 fw-bold"><?= $title; ?></h5>
                <small class="text-muted"><?= $this->session->userdata('siswa')['nama_siswa'] ?? 'Siswa'; ?></small>
            </div>
            <a href="javascript:void(0)" onclick="logout()" class="btn btn-sm btn-outline-danger rounded-pill"><i class="ri-logout-box-r-line"></i></a>
        </div>
    </div>
    <main class="student-shell px-3 py-3">
    <script>
        function logout() {
            Swal.fire({
                title: 'Yakin ingin keluar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('login/keluar') ?>";
                }
            })
        }
    </script>
