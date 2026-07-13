<div id="alert-password"></div>

<div class="card student-card mb-3">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Profil Siswa</h5>
        <div class="row g-2 small">
            <div class="col-5 text-muted">Nama Siswa</div><div class="col-7 fw-bold"><?= $siswa['nama_siswa'] ?? '-'; ?></div>
            <div class="col-5 text-muted">NIS</div><div class="col-7 fw-bold"><?= $siswa['nis'] ?? '-'; ?></div>
            <div class="col-5 text-muted">Kelas</div><div class="col-7 fw-bold"><?= $siswa['nama_kelas'] ?? '-'; ?></div>
            <div class="col-5 text-muted">Tahun Ajaran</div><div class="col-7 fw-bold"><?= $tahun_ajaran; ?></div>
        </div>
    </div>
</div>

<div class="card student-card">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Ganti Password</h5>
        <form id="form-password">
            <div class="mb-3">
                <label class="form-label fw-semibold">Password Lama</label>
                <input type="password" name="password_lama" class="form-control rounded-3" placeholder="Masukkan password lama ..." required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password Baru</label>
                <input type="password" name="password_baru" class="form-control rounded-3" placeholder="Masukkan password baru ..." required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                <input type="password" name="konfirmasi_password" class="form-control rounded-3" placeholder="Ulangi password baru ..." required>
            </div>
            <button type="submit" id="btn-password" class="btn btn-primary btn-touch w-100">Simpan Password</button>
        </form>
    </div>
</div>

<script>
    $('#form-password').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: '<?= base_url('profil/update_password'); ?>',
            type: 'POST',
            dataType: 'JSON',
            data: $('#form-password').serialize(),
            beforeSend: function () {
                $('#btn-password').prop('disabled', true).text('Menyimpan...');
                $('#alert-password').html('');
            },
            success: function (res) {
                if (res.result == 'true') {
                    Swal.fire('Berhasil', res.message || 'Password berhasil diperbarui.', 'success');
                    $('#form-password')[0].reset();
                } else {
                    Swal.fire('Gagal', res.message || 'Password gagal diperbarui.', 'error');
                }
            },
            error: function () {
                Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan password.', 'error');
            },
            complete: function () {
                $('#btn-password').prop('disabled', false).text('Simpan Password');
            }
        });
    });
</script>
