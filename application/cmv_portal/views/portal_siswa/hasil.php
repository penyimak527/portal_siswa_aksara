<?php
if (!function_exists('portal_preview_h')) {
    function portal_preview_h($text)
    {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
    if (!function_exists('portal_preview_quill')) {
        function portal_preview_quill($html)
        {
            $html = (string) $html;

            // Hapus elemen dan atribut berbahaya, tetapi pertahankan format dasar Quill.
            $html = preg_replace('#<(script|style|iframe|object|embed|form)[^>]*>.*?</\1>#is', '', $html);
            $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
            $html = preg_replace('/javascript\s*:/i', '', $html);

            return strip_tags($html, '<p><br><strong><b><em><i><u><s><ol><ul><li><blockquote><h1><h2><h3><a><span>');
        }
    }
}
?>

<style>
    .preview-bs-list {
        margin-bottom: 8px;
    }

    .preview-bs-item {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 8px 10px;
        margin-bottom: 7px;
        background: #ffffff;
    }

    .preview-bs-statement {
        font-weight: 600;
        color: #27324a;
        line-height: 1.45;
        margin-bottom: 4px;
    }

    .preview-bs-value {
        font-size: 12px;
        color: #64748b;
    }

    .preview-bs-value b {
        color: #27324a;
    }

    .preview-pembahasan-box {
        border: 1px solid #e9ecef;
        background: #f8fafc;
        border-radius: 10px;
        padding: 10px 12px;
        line-height: 1.45;
        color: #27324a;
        font-size: 14px;
        word-break: break-word;
    }

    .preview-pembahasan-box p {
        margin: 0 0 4px;
    }

    .preview-pembahasan-box p:last-child {
        margin-bottom: 0;
    }

    .preview-pembahasan-box ul,
    .preview-pembahasan-box ol {
        margin: 2px 0 4px;
        padding-left: 22px;
    }

    .preview-pembahasan-box li {
        margin-bottom: 2px;
    }

    .preview-pembahasan-box blockquote {
        margin: 4px 0;
        padding-left: 10px;
        border-left: 3px solid #cbd5e1;
    }
</style>

<div class="card student-card mb-3">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Hasil Pengerjaan</h5>
        <div class="row g-2 small">
            <div class="col-5 text-muted">Nama Sesi</div>
            <div class="col-7 fw-bold"><?= $hasil['nama_sesi']; ?></div>
            <div class="col-5 text-muted">Mata Pelajaran</div>
            <div class="col-7 fw-bold"><?= $hasil['nama_mata_pelajaran']; ?></div>
            <div class="col-5 text-muted">Jenis Pengerjaan</div>
            <div class="col-7 fw-bold"><?= $hasil['jenis_pengerjaan']; ?></div>
            <div class="col-5 text-muted">Tanggal</div>
            <div class="col-7 fw-bold">
                <?= !empty($hasil['waktu_selesai']) ? date('d-m-Y', strtotime($hasil['waktu_selesai'])) : '-'; ?></div>
            <div class="col-5 text-muted">Status</div>
            <div class="col-7 fw-bold"><?= $hasil['status_pengerjaan']; ?></div>
        </div>
    </div>
</div>

<div class="card student-card mb-3">
    <div class="card-body text-center">
        <div class="text-muted small mb-1">Nilai Akhir</div>
        <div class="fw-bold text-primary result-number"><?= round($hasil['nilai_akhir']); ?>%</div>
        <div class="row g-2 mt-3 text-center">
            <div class="col-4">
                <div class="p-2 bg-light rounded-3">
                    <div class="small text-muted">Benar</div><b><?= $hasil['jumlah_benar']; ?></b>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 bg-light rounded-3">
                    <div class="small text-muted">Salah</div><b><?= $hasil['jumlah_salah']; ?></b>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 bg-light rounded-3">
                    <div class="small text-muted">Kosong</div><b><?= $hasil['jumlah_kosong']; ?></b>
                </div>
            </div>
        </div>
        <div class="small text-muted mt-3">Durasi: <b><?= gmdate('H:i:s', (int) $hasil['durasi_detik']); ?></b></div>
    </div>
</div>

<div class="card student-card mb-3">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Analisa Per Materi</h5>
        <?php if (count($analisa_materi) == 0): ?>
            <div class="alert alert-light border mb-0">Analisa materi belum tersedia.</div>
        <?php endif; ?>
        <?php foreach ($analisa_materi as $m): ?>
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

<?php if (!$preview_diizinkan): ?>
    <div class="card student-card mb-3">
        <div class="card-body">
            <h5 class="fw-bold mb-2">Keterangan</h5>
            <p class="text-muted mb-0">
                Preview soal, kunci jawaban, jawaban benar, dan pembahasan hanya dapat dilihat jika sudah diizinkan oleh
                tentor.
            </p>
        </div>
    </div>
<?php else: ?>
    <div class="card student-card mb-3">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Preview Jawaban</h5>
            <?php foreach ($preview_jawaban as $row): ?>
                <div class="session-card">
                    <div class="fw-bold mb-2">Soal <?= $row['nomor_soal']; ?></div>
                    <div class="small text-muted mb-1">Materi: <?= $row['nama_materi']; ?></div>
                    <div class="mb-2"><?= $row['pertanyaan']; ?></div>
                    <?php if (!empty($row['gambar_soal'])): ?>
                        <img src="<?= $row['gambar_soal']; ?>" class="img-fluid rounded mb-2" alt="Gambar Soal">
                    <?php endif; ?>
                    <div class="small">Jawaban Kamu:</div>
                    <?php if (($row['tipe_soal'] ?? '') == 'benar_salah' && !empty($row['jawaban_siswa_items'])): ?>
                        <div class="preview-bs-list">
                            <?php foreach ($row['jawaban_siswa_items'] as $item): ?>
                                <div class="preview-bs-item">
                                    <div class="preview-bs-statement">
                                        <?= (int) ($item['nomor'] ?? 0); ?>. <?= portal_preview_h($item['teks'] ?? '-'); ?>
                                    </div>
                                    <div class="preview-bs-value">
                                        Jawaban Kamu: <b><?= portal_preview_h($item['jawaban'] ?? '-'); ?></b>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="fw-bold mb-2"><?= $row['jawaban_siswa_text']; ?></div>
                    <?php endif; ?>

                    <div class="small">Jawaban Benar:</div>
                    <?php if (($row['tipe_soal'] ?? '') == 'benar_salah' && !empty($row['jawaban_benar_items'])): ?>
                        <div class="preview-bs-list">
                            <?php foreach ($row['jawaban_benar_items'] as $item): ?>
                                <div class="preview-bs-item">
                                    <div class="preview-bs-statement">
                                        <?= (int) ($item['nomor'] ?? 0); ?>. <?= portal_preview_h($item['teks'] ?? '-'); ?>
                                    </div>
                                    <div class="preview-bs-value">
                                        Jawaban Benar: <b><?= portal_preview_h($item['jawaban'] ?? '-'); ?></b>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="fw-bold mb-2"><?= $row['jawaban_benar_text']; ?></div>
                    <?php endif; ?>
                    <div class="small">Status:</div>
                    <div class="fw-bold mb-2"><?= $row['status_jawaban']; ?></div>
                    <?php if (!empty($row['pembahasan'])): ?>
                        <div class="mt-3">
                            <div class="small text-muted mb-1">Pembahasan:</div>
                            <div class="preview-pembahasan-box">
                                <?= portal_preview_quill($row['pembahasan']); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-touch w-100">Kembali ke Dashboard</a>