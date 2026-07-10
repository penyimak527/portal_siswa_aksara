<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Pengerjaan Soal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= base_url(); ?>assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #f6f8fb; padding-bottom: 24px; }
        .student-shell { max-width: 820px; margin: 0 auto; }
        .sticky-exam { position: sticky; top: 0; z-index: 99; background: #fff; border-bottom: 1px solid #e5e7eb; }
        .student-card { border: 0; border-radius: 18px; box-shadow: 0 8px 24px rgba(15, 23, 42, .06); }
        .answer-option { border: 1px solid #e5e7eb; border-radius: 14px; padding: 12px; margin-bottom: 10px; background: #fff; display: block; }
        .answer-option.active { border-color: #0d6efd; background: #eef6ff; }
        .question-panel { display: none; }
        .question-panel.active { display: block; }
        .question-number { width: 38px; height: 38px; border-radius: 12px; border: 1px solid #dee2e6; background: #fff; font-weight: 700; }
        .question-number.active { background: #0d6efd; color: #fff; border-color: #0d6efd; }
        .question-number.answered { background: #198754; color: #fff; border-color: #198754; }
        .btn-touch { min-height: 42px; border-radius: 12px; }
        .question-image { max-height: 260px; object-fit: contain; background: #f8fafc; border: 1px solid #eef0f4; }
    </style>
</head>
<body>
    <div class="sticky-exam">
        <div class="student-shell px-3 py-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-0"><?= $pengerjaan['nama_sesi']; ?></h6>
                <small class="text-muted"><?= $pengerjaan['nama_mata_pelajaran']; ?> | <?= $pengerjaan['jenis_pengerjaan']; ?></small>
            </div>
            <div class="badge bg-danger fs-6" id="timer">00:00:00</div>
        </div>
    </div>

    <main class="student-shell px-3 py-3">
        <div class="card student-card mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Navigasi Soal</h6>
                <div class="d-flex flex-wrap gap-2" id="navigasi-soal">
                    <?php foreach ($soal as $index => $s) : ?>
                        <button type="button" class="question-number <?= $index == 0 ? 'active' : '' ?>" onclick="bukaSoal(<?= $index; ?>)"><?= $index + 1; ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="small mt-3 text-muted">
                    <span class="badge bg-success">Hijau</span> sudah dijawab,
                    <span class="badge bg-primary">Biru</span> sedang dibuka,
                    <span class="badge bg-light text-dark border">Putih</span> belum dijawab.
                </div>
            </div>
        </div>

        <form id="form-jawaban">
            <input type="hidden" name="status_pengerjaan" id="status_pengerjaan" value="Selesai">
            <?php foreach ($soal as $index => $s) : ?>
                <?php $jawab = $jawaban_tersimpan[$s['id']] ?? null; ?>
                <div class="question-panel <?= $index == 0 ? 'active' : '' ?>" data-index="<?= $index; ?>" data-id-soal="<?= $s['id']; ?>">
                    <div class="card student-card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-primary-subtle text-primary">Soal <?= $index + 1; ?> dari <?= count($soal); ?></span>
                                <span class="badge bg-light text-dark border">
                                    <?= $s['tipe_soal'] == 'pg' ? 'Pilihan Ganda' : ($s['tipe_soal'] == 'pg_kompleks' ? 'Pilihan Ganda Kompleks' : 'Benar / Salah'); ?>
                                </span>
                            </div>

                            <div class="small text-muted mb-2">Materi: <b><?= $s['nama_materi']; ?></b></div>
                            <h5 class="fw-bold mb-3"><?= $s['pertanyaan']; ?></h5>

                            <?php if (!empty($s['gambar_soal'])) : ?>
                                <img src="<?= $s['gambar_soal']; ?>" class="img-fluid rounded mb-3 question-image w-100" alt="Gambar Soal">
                            <?php endif; ?>

                            <?php if ($s['tipe_soal'] == 'pg') : ?>
                                <?php foreach ($s['pilihan'] as $p) : ?>
                                    <?php $checked = ((string)$jawab === (string)$p['label']) ? 'checked' : ''; ?>
                                    <label class="answer-option">
                                        <input type="radio" name="jawaban[<?= $s['id']; ?>]" value="<?= $p['label']; ?>" onchange="tandaiTerjawab()" <?= $checked; ?>>
                                        <b><?= $p['label']; ?>.</b> <?= $p['isi']; ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php elseif ($s['tipe_soal'] == 'pg_kompleks') : ?>
                                <?php $jawab_arr = is_array($jawab) ? $jawab : []; ?>
                                <?php foreach ($s['pilihan'] as $p) : ?>
                                    <?php $checked = in_array($p['label'], $jawab_arr) ? 'checked' : ''; ?>
                                    <label class="answer-option">
                                        <input type="checkbox" name="jawaban[<?= $s['id']; ?>][]" value="<?= $p['label']; ?>" onchange="tandaiTerjawab()" <?= $checked; ?>>
                                        <b><?= $p['label']; ?>.</b> <?= $p['isi']; ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <?php $jawab_arr = is_array($jawab) ? $jawab : []; ?>
                                <?php foreach ($s['pernyataan'] as $p) : ?>
                                    <div class="answer-option">
                                        <div class="fw-bold mb-2"><?= $p['label']; ?>. <?= $p['teks']; ?></div>
                                        <label class="me-3">
                                            <input type="radio" name="jawaban[<?= $s['id']; ?>][<?= $p['id']; ?>]" value="Benar" onchange="tandaiTerjawab()" <?= (($jawab_arr[$p['id']] ?? '') == 'Benar') ? 'checked' : ''; ?>> Benar
                                        </label>
                                        <label>
                                            <input type="radio" name="jawaban[<?= $s['id']; ?>][<?= $p['id']; ?>]" value="Salah" onchange="tandaiTerjawab()" <?= (($jawab_arr[$p['id']] ?? '') == 'Salah') ? 'checked' : ''; ?>> Salah
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-light border w-50 btn-touch" onclick="sebelumnya()">Sebelumnya</button>
                <button type="button" class="btn btn-primary w-50 btn-touch" onclick="selanjutnya()">Selanjutnya</button>
            </div>
            <button type="button" class="btn btn-success w-100 btn-touch" onclick="konfirmasiKumpulkan()">Kumpulkan</button>
        </form>
    </main>

    <script>
        let indexSoal = 0;
        let totalSoal = <?= count($soal); ?>;
        let waktuMulai = new Date('<?= date('c', strtotime($pengerjaan['waktu_mulai'])); ?>').getTime();
        let durasiDetik = <?= (int)$pengerjaan['durasi_menit']; ?> * 60;
        let intervalTimer;
        let sedangSubmit = false;
        let lockKeluar = false;

        $(document).ready(function () {
            jalanTimer();
            tandaiTerjawab(false);
            updateOptionActive();
        });

        $(document).on('change', 'input[type="radio"], input[type="checkbox"]', function () {
            updateOptionActive();
        });

        function updateOptionActive() {
            $('.answer-option').each(function () {
                if ($(this).find('input:checked').length > 0) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });
        }

        function bukaSoal(index) {
            indexSoal = index;
            $('.question-panel').removeClass('active');
            $('.question-panel').eq(index).addClass('active');
            $('.question-number').removeClass('active');
            $('.question-number').eq(index).addClass('active');
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        function sebelumnya() {
            if (indexSoal > 0) bukaSoal(indexSoal - 1);
        }

        function selanjutnya() {
            if (indexSoal < totalSoal - 1) bukaSoal(indexSoal + 1);
        }

        function tandaiTerjawab(simpan = true) {
            $('.question-panel').each(function (index) {
                let adaJawaban = $(this).find('input:checked').length > 0;
                if (adaJawaban) {
                    $('.question-number').eq(index).addClass('answered');
                } else {
                    $('.question-number').eq(index).removeClass('answered');
                }
            });
            if (simpan) simpanJawabanSementara();
        }

        function simpanJawabanSementara() {
            $.ajax({
                url: '<?= base_url('pengerjaan/simpan_jawaban/' . $pengerjaan['id']) ?>',
                type: 'POST',
                data: $('#form-jawaban').serialize()
            });
        }

        function jalanTimer() {
            intervalTimer = setInterval(function () {
                let sekarang = new Date().getTime();
                let berjalan = Math.floor((sekarang - waktuMulai) / 1000);
                let sisa = durasiDetik - berjalan;

                if (sisa <= 0) {
                    clearInterval(intervalTimer);
                    $('#timer').text('00:00:00');
                    $('#status_pengerjaan').val('Waktu Habis');
                    Swal.fire({
                        title: 'Waktu Habis',
                        text: 'Jawaban Anda otomatis dikumpulkan oleh sistem.',
                        icon: 'warning',
                        confirmButtonText: 'Lihat Hasil',
                        allowOutsideClick: false
                    }).then(() => {
                        kumpulkanJawaban('Waktu Habis');
                    });
                    return;
                }

                let jam = String(Math.floor(sisa / 3600)).padStart(2, '0');
                let menit = String(Math.floor((sisa % 3600) / 60)).padStart(2, '0');
                let detik = String(sisa % 60).padStart(2, '0');
                $('#timer').text(`${jam}:${menit}:${detik}`);
            }, 1000);
        }

        function konfirmasiKumpulkan() {
            let dijawab = $('.question-number.answered').length;
            let belum = totalSoal - dijawab;
            Swal.fire({
                title: 'Kumpulkan Jawaban?',
                html: `Jumlah Soal: <b>${totalSoal}</b><br>Sudah Dijawab: <b>${dijawab}</b><br>Belum Dijawab: <b>${belum}</b><br><br>Apakah Anda yakin ingin mengumpulkan jawaban?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Kumpulkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    kumpulkanJawaban('Selesai');
                }
            });
        }


        function kumpulkanJawaban(status) {
            if (sedangSubmit) return;
            sedangSubmit = true;
            $('#status_pengerjaan').val(status || 'Selesai');

            $.ajax({
                url: '<?= base_url('pengerjaan/kumpulkan/' . $pengerjaan['id']) ?>',
                type: 'POST',
                dataType: 'JSON',
                data: $('#form-jawaban').serialize(),
                beforeSend: function () {
                    Swal.fire({
                        title: 'Menyimpan jawaban...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (res) {
                    if (res.result == 'true') {
                        window.location.href = res.redirect || '<?= base_url('pengerjaan/hasil/' . $pengerjaan['id']) ?>';
                    } else {
                        sedangSubmit = false;
                        Swal.fire('Gagal', res.message || 'Jawaban gagal dikumpulkan.', 'error');
                    }
                },
                error: function () {
                    sedangSubmit = false;
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengumpulkan jawaban.', 'error');
                }
            });
        }

        document.addEventListener('visibilitychange', function () {
            if (document.hidden && !sedangSubmit) {
                catatKeluarHalaman();
            }
        });

        window.addEventListener('beforeunload', function () {
            if (!sedangSubmit && navigator.sendBeacon) {
                navigator.sendBeacon('<?= base_url('pengerjaan/keluar_halaman/' . $pengerjaan['id']) ?>');
            }
        });

        function catatKeluarHalaman() {
            if (lockKeluar) return;
            lockKeluar = true;
            setTimeout(() => lockKeluar = false, 1500);

            $.ajax({
                url: '<?= base_url('pengerjaan/keluar_halaman/' . $pengerjaan['id']) ?>',
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if (data.keluar_halaman == 1) {
                        Swal.fire('Peringatan 1', 'Anda terdeteksi meninggalkan halaman pengerjaan. Jika keluar halaman sebanyak 3 kali, seluruh jawaban akan dihapus.', 'warning');
                    } else if (data.keluar_halaman == 2) {
                        Swal.fire('Peringatan Terakhir', 'Anda sudah 2 kali meninggalkan halaman pengerjaan. Jika keluar sekali lagi, seluruh jawaban akan dihapus.', 'warning');
                    } else if (data.keluar_halaman >= 3) {
                        $('input').prop('checked', false);
                        updateOptionActive();
                        tandaiTerjawab(false);
                        bukaSoal(0);
                        Swal.fire('Jawaban Dihapus', 'Jawaban Anda telah dihapus karena meninggalkan halaman pengerjaan sebanyak 3 kali. Timer tetap berjalan.', 'error');
                    }
                }
            });
        }
    </script>
</body>
</html>
