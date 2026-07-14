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
    html,
    body {
        overscroll-behavior-y: none;
    }

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

    .swal2-container {
        z-index: 100000 !important;
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
let backGuardAktif = false;
let lewatiBeforeUnloadParent = false;
let sedangSelesaiPengerjaan = false;
let parentSedangReload = false;
let parentKeluarUrl = sessionStorage.getItem(AKSARA_EXAM_KEY + '_keluar_url') || '';
let parentKeluarLock = false;
let parentKeluarSudahDicatat = false;
let parentPendingAlert = null;
let touchAwalY = 0;
let alertReloadAktif = false;
const AKSARA_LEAVE_ALERT_SESI_KEY = 'aksara_leave_alert_sesi_<?= (int) $sesi['id']; ?>';

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
    aktifkanGuardBackPengerjaan();

    await requestFullscreenAksara();
    bindExamFrameLoad();
    $('#examFrame').attr('src', AKSARA_EXAM_URL);
}

function restorePengerjaanSetelahRefresh() {
    let examStarted = sessionStorage.getItem(AKSARA_EXAM_KEY);
    let examUrl = sessionStorage.getItem(AKSARA_EXAM_URL_KEY);

    if (examStarted === '1' && examUrl) {
        aktifkanGuardBackPengerjaan();
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


function aktifkanGuardBackPengerjaan() {
    if (backGuardAktif) {
        return;
    }

    backGuardAktif = true;
    history.pushState({ aksara_exam: true }, '', window.location.href);
}

function konfirmasiReloadPengerjaan() {
    if (alertReloadAktif) {
        return;
    }

    alertReloadAktif = true;

    Swal.fire({
        title: 'Tidak Bisa Reload Saat Pengerjaan',
        text: 'Anda sedang mengerjakan soal. Halaman tidak dapat direload selama pengerjaan berlangsung.',
        icon: 'warning',
        confirmButtonText: 'Ya',
        allowOutsideClick: false
    }).then(() => {
        alertReloadAktif = false;
    });
}

function kirimPesanKeFrame(type) {
    let frame = document.getElementById('examFrame');

    if (frame && frame.contentWindow) {
        frame.contentWindow.postMessage({ type: type }, window.location.origin);
    }
}

function tampilkanPeringatanKeluarParent(data) {
    if (!data || !data.keluar_halaman) {
        return;
    }

    if (data.keluar_halaman == 1) {
        Swal.fire('Peringatan 1', 'Anda terdeteksi meninggalkan halaman pengerjaan. Jika keluar halaman sebanyak 3 kali, seluruh jawaban akan dihapus.', 'warning');
    } else if (data.keluar_halaman == 2) {
        Swal.fire('Peringatan Terakhir', 'Anda sudah 2 kali meninggalkan halaman pengerjaan. Jika keluar sekali lagi, seluruh jawaban akan dihapus dan timer tetap berjalan.', 'warning');
    } else if (data.keluar_halaman >= 3) {
        kirimPesanKeFrame('AKSARA_EXAM_RESET_JAWABAN');
        Swal.fire('Jawaban Dihapus', 'Jawaban Anda telah dihapus karena meninggalkan halaman pengerjaan sebanyak 3 kali. Timer tetap berjalan.', 'error');
    }
}

function simpanPeringatanKeluarParent(data) {
    if (!data || !data.keluar_halaman) {
        return;
    }

    parentPendingAlert = data;
    sessionStorage.setItem(AKSARA_LEAVE_ALERT_SESI_KEY, JSON.stringify(data));
}

function tampilkanPeringatanKeluarTersimpanParent() {
    let dataAlert = parentPendingAlert ? JSON.stringify(parentPendingAlert) : sessionStorage.getItem(AKSARA_LEAVE_ALERT_SESI_KEY);

    if (!dataAlert) {
        return;
    }

    parentPendingAlert = null;
    sessionStorage.removeItem(AKSARA_LEAVE_ALERT_SESI_KEY);

    try {
        tampilkanPeringatanKeluarParent(JSON.parse(dataAlert));
    } catch (e) {
        console.log('Data peringatan keluar tidak valid:', e);
    }
}

function catatKeluarHalamanParent(callback, simpanAlert = true) {
    if (parentKeluarLock || parentKeluarSudahDicatat || !parentKeluarUrl) {
        if (typeof callback === 'function') {
            callback();
        }
        return;
    }

    parentKeluarLock = true;
    parentKeluarSudahDicatat = true;

    $.ajax({
        url: parentKeluarUrl,
        type: 'POST',
        dataType: 'JSON',
        success: function (data) {
            if (simpanAlert || document.hidden) {
                simpanPeringatanKeluarParent(data);
                if (!document.hidden) {
                    tampilkanPeringatanKeluarTersimpanParent();
                }
            } else {
                tampilkanPeringatanKeluarParent(data);
            }
        },
        complete: function () {
            parentKeluarLock = false;

            if (typeof callback === 'function') {
                callback();
            }
        }
    });
}

$(document).on('keydown', function (event) {
    let key = event.key ? event.key.toLowerCase() : '';
    let isReloadKey = key === 'f5' || ((event.ctrlKey || event.metaKey) && key === 'r');

    if (!isReloadKey) {
        return;
    }

    if (sessionStorage.getItem(AKSARA_EXAM_KEY) !== '1') {
        return;
    }

    event.preventDefault();
    konfirmasiReloadPengerjaan();
});

document.addEventListener('touchstart', function (event) {
    if (sessionStorage.getItem(AKSARA_EXAM_KEY) !== '1' || event.touches.length !== 1) {
        return;
    }

    touchAwalY = event.touches[0].clientY;
}, { passive: true });

document.addEventListener('touchmove', function (event) {
    if (sessionStorage.getItem(AKSARA_EXAM_KEY) !== '1' || event.touches.length !== 1) {
        return;
    }

    let posisiScrollAtas = window.scrollY <= 0 || document.documentElement.scrollTop <= 0;
    let tarikKeBawah = event.touches[0].clientY - touchAwalY;

    if (posisiScrollAtas && tarikKeBawah > 24) {
        event.preventDefault();
        konfirmasiReloadPengerjaan();
    }
}, { passive: false });

window.addEventListener('popstate', function () {
    let examStarted = sessionStorage.getItem(AKSARA_EXAM_KEY);

    if (sedangSelesaiPengerjaan || examStarted !== '1') {
        return;
    }

    history.pushState({ aksara_exam: true }, '', window.location.href);

    Swal.fire({
        title: 'Tidak Bisa Kembali Saat Pengerjaan',
        text: 'Anda sedang mengerjakan soal. Halaman tidak dapat kembali ke sebelumnya selama pengerjaan berlangsung.',
        icon: 'warning',
        confirmButtonText: 'Ya',
        allowOutsideClick: false
    });
});

window.addEventListener('visibilitychange', function () {
    if (sessionStorage.getItem(AKSARA_EXAM_KEY) !== '1' || sedangSelesaiPengerjaan) {
        return;
    }

    if (document.hidden) {
        if (parentSedangReload) {
            return;
        }

        catatKeluarHalamanParent(function () {}, true);
        return;
    }

    parentSedangReload = false;
    parentKeluarSudahDicatat = false;
    tampilkanPeringatanKeluarTersimpanParent();
});

window.addEventListener('message', function (event) {
    if (event.origin !== window.location.origin) {
        return;
    }

    if (!event.data || !event.data.type) {
        return;
    }

    if (event.data.type === 'AKSARA_EXAM_READY') {
        parentKeluarUrl = event.data.keluar_url || '';
        if (parentKeluarUrl) {
            sessionStorage.setItem(AKSARA_EXAM_KEY + '_keluar_url', parentKeluarUrl);
        }
        tampilkanPeringatanKeluarTersimpanParent();
        return;
    }

    if (event.data.type === 'AKSARA_EXAM_BACK_KELUAR_DONE') {
        lewatiBeforeUnloadParent = true;

        sessionStorage.removeItem(AKSARA_EXAM_KEY);
        sessionStorage.removeItem(AKSARA_EXAM_URL_KEY);

        let redirectUrl = event.data.redirect || "<?= base_url('sesi') ?>";
        window.location.href = redirectUrl;
        return;
    }

    if (event.data.type === 'AKSARA_EXAM_FINISHED') {
        sedangSelesaiPengerjaan = true;
        lewatiBeforeUnloadParent = true;

        sessionStorage.removeItem(AKSARA_EXAM_KEY);
        sessionStorage.removeItem(AKSARA_EXAM_URL_KEY);

        let redirectUrl = event.data.redirect || "<?= base_url('riwayat') ?>";

        exitFullscreenAksara(function () {
            window.location.href = redirectUrl;
        });
        return;
    }
});

window.addEventListener('beforeunload', function (event) {
    let examStarted = sessionStorage.getItem(AKSARA_EXAM_KEY);

    if (sedangSelesaiPengerjaan || lewatiBeforeUnloadParent || examStarted !== '1') {
        return;
    }

    parentSedangReload = true;
    event.preventDefault();
    event.returnValue = '';
    return '';
});
</script>
