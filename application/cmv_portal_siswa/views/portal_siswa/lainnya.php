<div class="mb-3">
    <p class="text-muted mb-0">Akses laporan perkembangan dan pengaturan akun.</p>
</div>

<div class="card student-card mb-3">
    <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;">
            <i class="ri-line-chart-line fs-4"></i>
        </div>
        <div class="flex-grow-1">
            <h5 class="fw-bold mb-1">Perkembangan Siswa</h5>
            <p class="text-muted small mb-0">Lihat grafik perkembangan hasil belajar setiap semester.</p>
        </div>
        <a href="<?= base_url('perkembangan'); ?>" class="btn btn-outline-primary btn-touch px-3">Buka</a>
    </div>
</div>

<div class="card student-card mb-3">
    <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;">
            <i class="ri-user-settings-line fs-4"></i>
        </div>
        <div class="flex-grow-1">
            <h5 class="fw-bold mb-1">Profil</h5>
            <p class="text-muted small mb-0">Lihat informasi siswa dan ubah password akun.</p>
        </div>
        <a href="<?= base_url('profil'); ?>" class="btn btn-outline-primary btn-touch px-3">Buka</a>
    </div>
</div>

<div class="pt-2">
    <button type="button" onclick="logout()" class="btn btn-outline-danger btn-touch w-100">
        <i class="ri-logout-box-r-line me-1"></i> Keluar dari Akun
    </button>
</div>
