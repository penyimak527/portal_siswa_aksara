<div class="card student-card student-card-soft mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <img src="<?= base_url(); ?>assets/user.png" onerror="this.style.display='none'" width="58" height="58"
                class="rounded-circle bg-white p-1" alt="Siswa">
            <div>
                <div class="text-muted small">Halo,</div>
                <h4 class="fw-bold mb-1"><?= $siswa['nama_siswa'] ?? '-'; ?></h4>
                <div class="small text-muted">Kelas: <?= $siswa['nama_kelas'] ?? '-'; ?> &bull; Tahun Ajaran:
                    <?= $tahun_ajaran; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card student-card mb-3">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Ringkasan</h5>
        <div class="row g-2">
            <div class="col-6">
                <div class="p-3 rounded-3 bg-light">
                    <div class="info-label">Sesi Tersedia</div>
                    <div class="info-value fs-4"><?= $ringkasan['sesi_tersedia']; ?></div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 rounded-3 bg-light">
                    <div class="info-label">Sesi Selesai</div>
                    <div class="info-value fs-4"><?= $ringkasan['sesi_selesai']; ?></div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 rounded-3 bg-light">
                    <div class="info-label">Rata-rata Nilai</div>
                    <div class="info-value fs-4"><?= $ringkasan['rata_nilai']; ?>%</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 rounded-3 bg-light">
                    <div class="info-label">Materi Lemah</div>
                    <div class="info-value fs-4"><?= $ringkasan['materi_lemah']; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card student-card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Sesi Soal Tersedia</h5>
            <a href="<?= base_url('sesi') ?>" class="small fw-semibold">Lihat semua</a>
        </div>

        <?php if (count($sesi_tersedia) == 0): ?>
            <div class="alert alert-light border mb-0">Belum ada sesi soal yang tersedia saat ini.</div>
        <?php endif; ?>

        <?php foreach ($sesi_tersedia as $i => $row): ?>
            <div class="session-card">
                <div class="d-flex justify-content-between gap-2 align-items-start mb-2">
                    <div>
                        <div class="fw-bold"><?= ($i + 1) . '. ' . $row['nama_sesi']; ?></div>
                        <!-- <div class="text-muted small"><= $row['nama_mata_pelajaran']; ?> &bull; <= $row['jenis_pengerjaan']; ?></div> -->
                        <div class="text-muted small"><?= $row['nama_mata_pelajaran']; ?> &bull;
                            <?= $row['label_pengerjaan'] ?? $row['jenis_pengerjaan']; ?>
                        </div>
                    </div>
                    <span class="badge badge-soft rounded-pill"><?= $row['durasi_timer']; ?> menit</span>
                </div>
                <div class="small mb-3">
                    <div>Mapel: <b><?= $row['nama_mata_pelajaran']; ?></b></div>
                    <div>Jadwal: <b><?= $row['tanggal_mulai']; ?>, <?= $row['jam_mulai']; ?> -
                            <?= $row['jam_selesai']; ?></b></div>
                    <div>Jumlah Soal: <b><?= $row['jumlah_soal']; ?></b></div>
                </div>
                <button type="button" class="btn btn-primary btn-touch w-100" onclick="cekAksesSesi('<?= (int) $row['id']; ?>')">Mulai
                    Kerjakan</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="card student-card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Riwayat Nilai Terbaru</h5>
            <a href="<?= base_url('riwayat') ?>" class="small fw-semibold">Lihat riwayat</a>
        </div>

        <?php if (count($riwayat_terbaru) == 0): ?>
            <div class="alert alert-light border mb-0">Belum ada riwayat pengerjaan.</div>
        <?php endif; ?>

        <?php foreach ($riwayat_terbaru as $i => $row): ?>
            <div class="session-card">
                <div class="fw-bold"><?= ($i + 1) . '. ' . $row['nama_sesi']; ?></div>
                <!-- <div class="small text-muted mb-2"><= $row['nama_mata_pelajaran']; ?> - <= $row['jenis_pengerjaan']; ?></div> -->
                <div class="small text-muted mb-2"><?= $row['nama_mata_pelajaran']; ?> &bull;
                    <?= $row['label_pengerjaan'] ?? $row['jenis_pengerjaan']; ?>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Nilai: <b><?= round($row['nilai_akhir']); ?>%</b></div>
                        <div class="small">Tanggal: <b><?= date('d-m-Y', strtotime($row['waktu_selesai'])); ?></b></div>
                    </div>
                    <a href="<?= base_url('pengerjaan/hasil/' . $row['id']) ?>"
                        class="btn btn-sm btn-outline-primary rounded-pill">Detail</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function cekAksesSesi(idSesi) {
    if (!idSesi) {
        return;
    }

    $.ajax({
        url: '<?= base_url('sesi/cek_akses'); ?>',
        type: 'POST',
        dataType: 'JSON',
        data: {
            id_sesi: idSesi
        },
        beforeSend: function () {
            Swal.fire({
                title: 'Memeriksa akses...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (res) {
            Swal.close();
            if (res.ada_tunggakan == 'true') {
                Swal.fire({
        title: 'Akses Sesi Ditolak',
        html: `
            <div class="text-center">
                Maaf, sesi soal baru belum dapat diakses karena masih terdapat<br>
                pembayaran yang belum diselesaikan.
                <br><br>
                Silahkan hubungi admin bimbel untuk informasi lebih lanjut.
            </div>`,
        icon: 'warning',
        confirmButtonText: 'Kembali',
        allowOutsideClick: false,
        allowEscapeKey: false
    });
                return;
            }

            if (res.result == 'true') {
                window.location.href = res.redirect;
                return;
            }

            Swal.fire({
                title: 'Sesi Tidak Dapat Diakses',
                text: res.message || 'Sesi belum dapat dikerjakan.',
                icon: 'warning',
                confirmButtonText: 'Kembali'
            });
        },
        error: function () {
            Swal.fire({
                title: 'Gagal',
                text: 'Terjadi kesalahan saat memeriksa akses sesi.',
                icon: 'error',
                confirmButtonText: 'Kembali'
            });
        }
    });
}
</script>
