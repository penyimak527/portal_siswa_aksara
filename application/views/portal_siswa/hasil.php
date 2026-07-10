<div class="card student-card mb-3">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Hasil Pengerjaan</h5>
        <div class="row g-2 small">
            <div class="col-5 text-muted">Nama Sesi</div><div class="col-7 fw-bold"><?= $hasil['nama_sesi']; ?></div>
            <div class="col-5 text-muted">Mata Pelajaran</div><div class="col-7 fw-bold"><?= $hasil['nama_mata_pelajaran']; ?></div>
            <div class="col-5 text-muted">Jenis Pengerjaan</div><div class="col-7 fw-bold"><?= $hasil['jenis_pengerjaan']; ?></div>
            <div class="col-5 text-muted">Tanggal</div><div class="col-7 fw-bold"><?= !empty($hasil['waktu_selesai']) ? date('d-m-Y', strtotime($hasil['waktu_selesai'])) : '-'; ?></div>
            <div class="col-5 text-muted">Status</div><div class="col-7 fw-bold"><?= $hasil['status_pengerjaan']; ?></div>
        </div>
    </div>
</div>

<div class="card student-card mb-3">
    <div class="card-body text-center">
        <div class="text-muted small mb-1">Nilai Akhir</div>
        <div class="fw-bold text-primary result-number"><?= round($hasil['nilai_akhir']); ?>%</div>
        <div class="row g-2 mt-3 text-center">
            <div class="col-4"><div class="p-2 bg-light rounded-3"><div class="small text-muted">Benar</div><b><?= $hasil['jumlah_benar']; ?></b></div></div>
            <div class="col-4"><div class="p-2 bg-light rounded-3"><div class="small text-muted">Salah</div><b><?= $hasil['jumlah_salah']; ?></b></div></div>
            <div class="col-4"><div class="p-2 bg-light rounded-3"><div class="small text-muted">Kosong</div><b><?= $hasil['jumlah_kosong']; ?></b></div></div>
        </div>
        <div class="small text-muted mt-3">Durasi: <b><?= gmdate('H:i:s', (int)$hasil['durasi_detik']); ?></b></div>
    </div>
</div>

<div class="card student-card mb-3">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Analisa Per Materi</h5>
        <?php if (count($analisa_materi) == 0) : ?>
            <div class="alert alert-light border mb-0">Analisa materi belum tersedia.</div>
        <?php endif; ?>
        <?php foreach ($analisa_materi as $m) : ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-semibold"><?= $m['nama_materi']; ?></span>
                    <span class="fw-bold"><?= round($m['persen']); ?>%</span>
                </div>
                <div class="materi-bar"><span style="width: <?= round($m['persen']); ?>%"></span></div>
                <div class="small text-muted mt-1"><?= $m['benar']; ?>/<?= $m['total']; ?> benar</div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if (!$preview_diizinkan) : ?>
    <div class="card student-card mb-3">
        <div class="card-body">
            <h5 class="fw-bold mb-2">Keterangan</h5>
            <p class="text-muted mb-0">
                Preview soal, kunci jawaban, jawaban benar, dan pembahasan hanya dapat dilihat jika sudah diizinkan oleh tentor.
            </p>
        </div>
    </div>
<?php else : ?>
    <div class="card student-card mb-3">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Preview Jawaban</h5>
            <?php foreach ($preview_jawaban as $row) : ?>
                <div class="session-card">
                    <div class="fw-bold mb-2">Soal <?= $row['nomor_soal']; ?></div>
                    <div class="small text-muted mb-1">Materi: <?= $row['nama_materi']; ?></div>
                    <div class="mb-2"><?= $row['pertanyaan']; ?></div>
                    <?php if (!empty($row['gambar_soal'])) : ?>
                        <img src="<?= $row['gambar_soal']; ?>" class="img-fluid rounded mb-2" alt="Gambar Soal">
                    <?php endif; ?>
                    <div class="small">Jawaban Kamu:</div>
                    <div class="fw-bold mb-2"><?= $row['jawaban_siswa_text']; ?></div>
                    <div class="small">Jawaban Benar:</div>
                    <div class="fw-bold mb-2"><?= $row['jawaban_benar_text']; ?></div>
                    <div class="small">Status:</div>
                    <div class="fw-bold mb-2"><?= $row['status_jawaban']; ?></div>
                    <?php if (!empty($row['pembahasan'])) : ?>
                        <div class="alert alert-light border mb-0"><b>Pembahasan:</b><br><?= $row['pembahasan']; ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-touch w-100">Kembali ke Dashboard</a>
