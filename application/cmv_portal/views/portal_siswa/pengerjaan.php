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
        .portal-exam-page {
            --aksara-yellow: #f2ad0d;
            --aksara-yellow-dark: #c98600;
            --aksara-yellow-hover: #d99a00;
            --aksara-yellow-soft: #fff6dd;
            --aksara-black: #050505;
            --aksara-white: #ffffff;
            --aksara-dark: #111827;
            --aksara-border: #eef0f4;
            --aksara-green: #10b981;
            --aksara-green-soft: #ecfdf5;
            --aksara-blue: #2563eb;
            --aksara-blue-soft: #eff6ff;
            --aksara-purple-soft: #f5f3ff;
            background: #ffffff;
            padding-bottom: 24px;
        }

        .portal-exam-page .student-shell {
            max-width: 820px;
            margin: 0 auto;
        }

        .portal-exam-page .sticky-exam {
            position: sticky;
            top: 0;
            z-index: 99;
            background: #ffffff;
            border-bottom: 0;
            box-shadow: 0 6px 18px rgba(5, 5, 5, .05);
        }

        .portal-exam-page .sticky-exam h6 {
            color: var(--aksara-black);
        }

        .portal-exam-page .student-card {
            border: 1px solid var(--aksara-border);
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
            overflow: hidden;
            background: #fff;
        }

        /*
         * Pilihan jawaban sengaja dibuat netral.
         * Portal boleh colorful, tetapi area jawaban jangan warna-warni
         * agar fokus siswa tetap ke isi soal dan opsi jawaban.
         */
        .portal-exam-page .answer-option {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 12px;
            margin-bottom: 10px;
            background: #ffffff;
            color: var(--aksara-dark);
            display: block;
        }

        .portal-exam-page .answer-option:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            color: var(--aksara-dark);
        }

        .portal-exam-page .answer-option.active {
            background: #ffffff;
            border-color: var(--aksara-yellow);
            color: var(--aksara-dark);
            box-shadow: 0 0 0 2px rgba(242, 173, 13, .14);
        }

        .portal-exam-page .question-panel {
            display: none;
        }

        .portal-exam-page .question-panel.active {
            display: block;
        }

        .portal-exam-page .question-number {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: 1px solid #dee2e6;
            background: #fff;
            font-weight: 700;
        }

        .portal-exam-page .question-number.active,
        .portal-exam-page .question-number.answered.active {
            background: var(--aksara-blue);
            color: #fff;
            border-color: var(--aksara-blue);
        }

        .portal-exam-page .question-number.answered:not(.active) {
            background: var(--aksara-green);
            color: #fff;
            border-color: var(--aksara-green);
        }

        .portal-exam-page .exam-legend {
            display: inline-block;
            padding: .35em .65em;
            border-radius: .375rem;
            font-size: .75em;
            font-weight: 700;
            line-height: 1;
        }

        .portal-exam-page .exam-legend-green {
            background: var(--aksara-green);
            color: #fff;
        }

        .portal-exam-page .exam-legend-blue {
            background: var(--aksara-blue);
            color: #fff;
        }

        .portal-exam-page .exam-legend-white {
            background: #fff;
            color: var(--aksara-dark);
            border: 1px solid #dee2e6;
        }

        .portal-exam-page .btn-touch {
            min-height: 42px;
            border-radius: 12px;
        }

        .portal-exam-page .btn-primary {
            background: var(--aksara-yellow);
            border-color: var(--aksara-yellow);
            color: var(--aksara-white);
            font-weight: 800;
        }

        .portal-exam-page .btn-primary:hover,
        .portal-exam-page .btn-primary:focus {
            background: var(--aksara-yellow-hover);
            border-color: var(--aksara-yellow-hover);
            color: #fff;
        }

        .portal-exam-page .btn-success {
            background: var(--aksara-green);
            border-color: var(--aksara-green);
            font-weight: 800;
        }

        .portal-exam-page .btn-success:hover,
        .portal-exam-page .btn-success:focus {
            background: #059669;
            border-color: #059669;
            color: #fff;
        }

        .portal-exam-page .badge.bg-danger {
            background: var(--aksara-black) !important;
            color: #fff;
            border: 1px solid var(--aksara-yellow);
        }

        .portal-exam-page .badge.bg-primary,
        .portal-exam-page .badge.bg-primary-subtle,
        .portal-exam-page .badge.text-primary {
            background-color: var(--aksara-yellow-soft) !important;
            color: #a16207 !important;
        }

        .portal-exam-page .question-image {
            max-height: 260px;
            object-fit: contain;
            background: #fff;
            border: 1px solid rgba(242, 173, 13, .25);
        }
    </style>
</head>
<body class="portal-exam-page">
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
                    <span class="exam-legend exam-legend-green">Hijau</span> sudah dijawab,
                    <span class="exam-legend exam-legend-blue">Biru</span> sedang dibuka,
                    <span class="exam-legend exam-legend-white">Putih</span> belum dijawab.
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
        const AKSARA_LAST_QUESTION_KEY = 'aksara_last_question_<?= (int) $pengerjaan['id']; ?>';

        $(document).ready(function () {
            jalanTimer();
            tandaiTerjawab(false);
            updateOptionActive();

            let lastIndex = sessionStorage.getItem(AKSARA_LAST_QUESTION_KEY);
            if (lastIndex !== null && !isNaN(parseInt(lastIndex))) {
                let indexRestore = parseInt(lastIndex);
                if (indexRestore >= 0 && indexRestore < totalSoal) {
                    bukaSoal(indexRestore, false);
                }
            }
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

        function bukaSoal(index, simpanPosisi = true) {
            index = parseInt(index, 10);
            if (isNaN(index) || index < 0 || index >= totalSoal) {
                index = 0;
            }

            indexSoal = index;
            $('.question-panel').removeClass('active');
            $('.question-panel').eq(index).addClass('active');
            $('.question-number').removeClass('active');
            $('.question-number').eq(index).addClass('active');

            if (simpanPosisi) {
                sessionStorage.setItem(AKSARA_LAST_QUESTION_KEY, index);
            }

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
                    // Swal.fire({
                    //     title: 'Waktu Habis',
                    //     text: 'Jawaban Anda otomatis dikumpulkan oleh sistem.',
                    //     icon: 'warning',
                    //     confirmButtonText: 'Lihat Hasil',
                    //     allowOutsideClick: false
                    // }).then(() => {
                        kumpulkanJawaban('Waktu Habis');
                    // });
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

function konfirmasiKeluarPengerjaan(urlTujuan) {
    if (sedangSubmit) {
        window.location.href = urlTujuan;
        return;
    }

    Swal.fire({
        title: 'Keluar dari Pengerjaan?',
        text: 'Jika keluar dari halaman pengerjaan, sistem akan mencatat aktivitas keluar halaman.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Tetap Mengerjakan',
        reverseButtons: true,
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            sedangSubmit = true;

            if (navigator.sendBeacon) {
                navigator.sendBeacon('<?= base_url('pengerjaan/keluar_halaman/' . $pengerjaan['id']) ?>');
            }

            window.location.href = urlTujuan;
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
        title: status === 'Waktu Habis' ? 'Waktu Habis' : 'Menyimpan jawaban...',
        text: status === 'Waktu Habis' ? 'Jawaban Anda otomatis dikumpulkan oleh sistem.' : 'Mohon tunggu sebentar.',
        icon: status === 'Waktu Habis' ? 'warning' : undefined,
        allowOutsideClick: false,
        showConfirmButton: false,

        didOpen: () => {
            Swal.showLoading();
        }
    });
},
                // beforeSend: function () {
                //     Swal.fire({
                //         title: 'Menyimpan jawaban...',
                //         text: 'Mohon tunggu sebentar.',
                //         allowOutsideClick: false,
                //         didOpen: () => {
                //             Swal.showLoading();
                //         }
                //     });
                // },
                success: function (res) {
                  if (res.result == 'true') {
    sessionStorage.removeItem(AKSARA_LAST_QUESTION_KEY);
    let redirectUrl = res.redirect || '<?= base_url('pengerjaan/hasil/' . $pengerjaan['id']) ?>';

    if (window.parent && window.parent !== window) {
        window.parent.postMessage({
            type: 'AKSARA_EXAM_FINISHED',
            redirect: redirectUrl
        }, window.location.origin);
    } else {
        exitFullscreenAksara(function () {
            window.location.href = redirectUrl;
        });
    }
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

window.addEventListener('beforeunload', function (event) {
    if (sedangSubmit) {
        return;
    }

    /*
     * Untuk refresh / close tab browser, SweetAlert tidak bisa dipakai.
     * Browser hanya mengizinkan alert bawaan beforeunload.
     */
    event.preventDefault();
    event.returnValue = '';

    return '';
});
window.addEventListener('pagehide', function () {
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
        async function requestFullscreenAksara() {
    let elem = document.documentElement;

    try {
        if (document.fullscreenElement || document.webkitFullscreenElement) {
            return;
        }

        if (elem.requestFullscreen) {
            await elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            await elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            await elem.msRequestFullscreen();
        }
    } catch (e) {
        console.log('Fullscreen tidak aktif:', e);
    }
}

function exitFullscreenAksara(callback) {
    let selesai = function () {
        if (typeof callback === 'function') {
            callback();
        }
    };

    try {
        if (document.fullscreenElement && document.exitFullscreen) {
            document.exitFullscreen().then(selesai).catch(selesai);
        } else if (document.webkitFullscreenElement && document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
            setTimeout(selesai, 300);
        } else {
            selesai();
        }
    } catch (e) {
        selesai();
    }
}
    </script>
</body>
</html>
