<div class="card student-card mb-3">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Konfirmasi Pengerjaan</h5>
        <div class="session-card mb-0">
            <div class="row g-2 small">
                <div class="col-5 text-muted">Nama Sesi</div><div class="col-7 fw-bold"><?= $sesi['nama_sesi']; ?></div>
                <div class="col-5 text-muted">Mata Pelajaran</div><div class="col-7 fw-bold"><?= $sesi['nama_mata_pelajaran']; ?></div>
                <div class="col-5 text-muted">Jenis Pengerjaan</div><div class="col-7 fw-bold"><?= $sesi['label_pengerjaan'] ?? $sesi['jenis_pengerjaan']; ?></div>
                <div class="col-5 text-muted">Kategori</div><div class="col-7 fw-bold"><?= $sesi['nama_kategori_soal']; ?></div>
                <div class="col-5 text-muted">Jumlah Soal</div><div class="col-7 fw-bold"><?= $sesi['jumlah_soal']; ?></div>
                <div class="col-5 text-muted">Durasi Timer</div><div class="col-7 fw-bold"><?= $sesi['durasi_timer']; ?> menit</div>
            </div>
        </div>
    </div>
</div>

<div class="card student-card">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Peraturan Pengerjaan</h5>
        <ol class="small ps-3">
            <li>Kerjakan soal dengan teliti.</li>
            <li>Timer akan berjalan setelah tombol mulai ditekan.</li>
            <li>Jika waktu habis, jawaban otomatis dikumpulkan.</li>
            <li>Jangan keluar dari halaman pengerjaan.</li>
            <li>Jika keluar halaman 1 kali, sistem memberi peringatan pertama.</li>
            <li>Jika keluar halaman 2 kali, sistem memberi peringatan terakhir.</li>
            <li>Jika keluar halaman 3 kali, seluruh jawaban akan dihapus.</li>
            <li>Timer tetap berjalan dan tidak akan diulang.</li>
            <!-- <li>Siswa hanya dapat mengerjakan sesi ini 1 kali.</li> -->
             <li>Siswa hanya dapat mengerjakan sesi ini maksimal 2 kali: pertama Bimbel, kedua Rumah.</li>
        </ol>

        <div class="form-check p-2 ps-4 border rounded-3 bg-light">
            <input class="form-check-input " type="checkbox" id="setuju" onchange="toggleMulai()">
            <label class="form-check-label fw-semibold" for="setuju">Saya sudah membaca dan menyetujui aturan pengerjaan.</label>
        </div>

        <div class="d-flex gap-2 mt-3">
            <a href="<?= base_url('sesi') ?>" class="btn btn-light border btn-touch w-50">Kembali</a>
            <!-- <a href="<= base_url('pengerjaan/mulai/' . $sesi['id']) ?>" id="btnMulai" class="btn btn-primary btn-touch w-50 disabled">Mulai Mengerjakan</a> -->
            <a href="javascript:void(0)" id="btnMulai" class="btn btn-primary btn-touch w-50 disabled" onclick="mulaiPengerjaanFullscreen()">Mulai Mengerjakan</a>
        </div>
    </div>
</div>
<style>
    #examFullscreenShell {
        display: none;
        position: fixed;
        inset: 0;
        background: #f6f8fb;
        z-index: 99999;
    }

    #examFullscreenShell.active {
        display: block;
    }

    #examFrame {
        width: 100%;
        height: 100%;
        border: 0;
        background: #fff;
    }

    .exam-loading {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f6f8fb;
        z-index: 2;
        font-weight: 700;
    }
</style>
<div id="examFullscreenShell">
    <div class="exam-loading" id="examLoading">Memuat halaman pengerjaan...</div>
    <iframe id="examFrame" src=""></iframe>
</div>
<script>
function toggleMulai() {
    if ($('#setuju').is(':checked')) {
        $('#btnMulai').removeClass('disabled');
    } else {
        $('#btnMulai').addClass('disabled');
    }
}

async function requestFullscreenAksara() {
    let elem = document.getElementById('examFullscreenShell');

    try {
        if (document.fullscreenElement || document.webkitFullscreenElement) {
            return true;
        }

        if (elem.requestFullscreen) {
            await elem.requestFullscreen();
            return true;
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
            return true;
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
            return true;
        }
    } catch (e) {
        console.log('Fullscreen tidak aktif:', e);
    }

    return false;
}

function exitFullscreenAksara(callback) {
    let selesai = function () {
        $('#examFrame').attr('src', '');
        $('#examFullscreenShell').removeClass('active');
        $('#examLoading').show();

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

async function mulaiPengerjaanFullscreen() {
    if ($('#btnMulai').hasClass('disabled')) {
        return;
    }

    $('#btnMulai').addClass('disabled').html('Memulai...');
    $('#examFullscreenShell').addClass('active');
    $('#examLoading').show();

    await requestFullscreenAksara();

    /*
     * Jangan pakai window.location.href,
     * karena pindah halaman utama membuat fullscreen keluar.
     * Halaman pengerjaan dibuka di iframe agar parent tetap fullscreen.
     */
    $('#examFrame').attr('src', "<?= base_url('pengerjaan/mulai/' . $sesi['id']) ?>");

    $('#examFrame').on('load', function () {
        $('#examLoading').hide();
    });
}

/*
 * Menerima pesan dari halaman pengerjaan.php ketika siswa selesai.
 */
window.addEventListener('message', function (event) {
    if (event.origin !== window.location.origin) {
        return;
    }

    if (!event.data || event.data.type !== 'AKSARA_EXAM_FINISHED') {
        return;
    }

    let redirectUrl = event.data.redirect || "<?= base_url('riwayat') ?>";

    exitFullscreenAksara(function () {
        window.location.href = redirectUrl;
    });
});
</script>