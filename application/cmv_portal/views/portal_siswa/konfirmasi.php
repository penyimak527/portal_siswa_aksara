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
            <a href="<?= base_url('pengerjaan/mulai/' . $sesi['id']) ?>" id="btnMulai" class="btn btn-primary btn-touch w-50 disabled">Mulai Mengerjakan</a>
        </div>
    </div>
</div>

<script>
function toggleMulai() {
    if ($('#setuju').is(':checked')) {
        $('#btnMulai').removeClass('disabled');
    } else {
        $('#btnMulai').addClass('disabled');
    }
}
</script>
