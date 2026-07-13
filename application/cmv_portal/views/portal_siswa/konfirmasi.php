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
        background: #ffffff;
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
        background: #ffffff;
        z-index: 2;
        font-weight: 700;
        padding: 20px;
        text-align: center;
    }

    .exam-loading-card {
        max-width: 360px;
        width: 100%;
        background: #ffffff;
        border: 1px solid rgba(242, 173, 13, .35);
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .10);
    }
</style>
<div id="examFullscreenShell">
    <div class="exam-loading" id="examLoading">Memuat halaman pengerjaan...</div>
    <iframe id="examFrame" src=""></iframe>
</div>
<script>
const AKSARA_EXAM_KEY = 'aksara_exam_started_<?= (int) $sesi['id']; ?>';
const AKSARA_EXAM_URL_KEY = AKSARA_EXAM_KEY + '_url';
const AKSARA_EXAM_URL = "<?= base_url('pengerjaan/mulai/' . $sesi['id']) ?>";
let examFrameLoadBound = false;

$(document).ready(function () {
    restorePengerjaanSetelahRefresh();
    bindExamFrameLoad();
});

function toggleMulai() {
    if ($('#setuju').is(':checked')) {
        $('#btnMulai').removeClass('disabled');
    } else {
        $('#btnMulai').addClass('disabled');
    }
}

function bindExamFrameLoad() {
    if (examFrameLoadBound) {
        return;
    }

    examFrameLoadBound = true;
    $('#examFrame').on('load', function () {
        $('#examLoading').hide();

        /*
         * Setelah refresh, sistem tetap mencoba fullscreen otomatis.
         * Jika browser menolak, tidak menampilkan tombol fullscreen.
         */
        autoRequestFullscreenAfterRefresh();
    });
}

async function requestFullscreenAksara() {
    let elem = document.getElementById('examFullscreenShell');

    try {
        if (document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement) {
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
        console.log('Fullscreen otomatis ditolak browser:', e);
    }

    return false;
}

async function autoRequestFullscreenAfterRefresh() {
    try {
        if (document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement) {
            $('#examLoading').hide();
            return;
        }

        await requestFullscreenAksara();
    } catch (e) {
        console.log('Fullscreen otomatis setelah refresh ditolak browser:', e);
    }

    /*
     * Jika fullscreen gagal, pengerjaan tetap lanjut tanpa tombol fullscreen.
     */
    $('#examLoading').hide();
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
        } else if (document.msFullscreenElement && document.msExitFullscreen) {
            document.msExitFullscreen();
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
    $('#examLoading').html('<div class="exam-loading-card">Memuat halaman pengerjaan...</div>').show();

    sessionStorage.setItem(AKSARA_EXAM_KEY, '1');
    sessionStorage.setItem(AKSARA_EXAM_URL_KEY, AKSARA_EXAM_URL);

    await requestFullscreenAksara();
    bindExamFrameLoad();
    $('#examFrame').attr('src', AKSARA_EXAM_URL);
}

function restorePengerjaanSetelahRefresh() {
    let examStarted = sessionStorage.getItem(AKSARA_EXAM_KEY);
    let examUrl = sessionStorage.getItem(AKSARA_EXAM_URL_KEY);

    if (examStarted === '1' && examUrl) {
        $('#examFullscreenShell').addClass('active');
        $('#examLoading').html(`
            <div class="exam-loading-card">
                <div class="fw-bold mb-2">Memuat ulang pengerjaan...</div>
                <div class="small text-muted">Mohon tunggu sebentar.</div>
            </div>
        `).show();

        bindExamFrameLoad();
        $('#examFrame').attr('src', examUrl);
    }
}

window.addEventListener('message', function (event) {
    if (event.origin !== window.location.origin) {
        return;
    }

    if (!event.data || event.data.type !== 'AKSARA_EXAM_FINISHED') {
        return;
    }

    sessionStorage.removeItem(AKSARA_EXAM_KEY);
    sessionStorage.removeItem(AKSARA_EXAM_URL_KEY);

    let redirectUrl = event.data.redirect || "<?= base_url('riwayat') ?>";

    exitFullscreenAksara(function () {
        window.location.href = redirectUrl;
    });
});
</script>