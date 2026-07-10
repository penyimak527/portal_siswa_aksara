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
        body { background: #f6f8fb; padding-bottom: 78px; }
        .student-shell { max-width: 820px; margin: 0 auto; }
        .student-topbar { position: sticky; top: 0; z-index: 50; background: #ffffff; border-bottom: 1px solid #eef0f4; }
        .student-card { border: 0; border-radius: 18px; box-shadow: 0 8px 24px rgba(15, 23, 42, .06); }
        .student-card-soft { background: linear-gradient(135deg, #eaf7ff, #ffffff); }
        .info-label { color: #6b7280; font-size: 12px; margin-bottom: 2px; }
        .info-value { font-weight: 700; color: #111827; }
        .session-card { border: 1px solid #eef0f4; border-radius: 16px; padding: 14px; margin-bottom: 12px; background: #fff; }
        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; z-index: 70; background: #ffffff; border-top: 1px solid #eef0f4; }
        .bottom-nav-inner { max-width: 820px; margin: 0 auto; display: grid; grid-template-columns: repeat(4, 1fr); }
        .bottom-nav a { padding: 9px 4px; text-align: center; color: #64748b; font-size: 11px; text-decoration: none; }
        .bottom-nav a.active { color: #0d6efd; font-weight: 700; }
        .bottom-nav i { display: block; font-size: 20px; margin-bottom: 2px; }
        .btn-touch { min-height: 42px; border-radius: 12px; }
        .badge-soft { background: #eef6ff; color: #0d6efd; }
        .materi-bar { height: 8px; border-radius: 999px; background: #e5e7eb; overflow: hidden; }
        .materi-bar span { display: block; height: 100%; background: #0d6efd; }
        .result-number { font-size: 36px; line-height: 1; }
    </style>
</head>
<body>
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
