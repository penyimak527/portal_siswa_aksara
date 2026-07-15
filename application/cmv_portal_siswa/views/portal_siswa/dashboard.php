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
            <!-- <div class="col-6"> -->
                <div class="col-12 col-md-6 col-lg">
                <div class="p-3 rounded-3 bg-light h-100">
                    <div class="info-label">Sesi Tersedia</div>
                    <div class="info-value fs-4"><?= $ringkasan['sesi_tersedia']; ?></div>
                </div>
            </div>
            <!-- <div class="col-6"> -->
                <div class="col-12 col-md-6 col-lg">
                <div class="p-3 rounded-3 bg-light h-100">
                    <div class="info-label">Sesi Selesai</div>
                    <div class="info-value fs-4"><?= $ringkasan['sesi_selesai']; ?></div>
                </div>
            </div>
            <!-- <div class="col-6"> -->
                <div class="col-12 col-md-6 col-lg">
                <div class="p-3 rounded-3 bg-light h-100">
                    <div class="info-label">Rata-rata Nilai</div>
                    <div class="info-value fs-4"><?= $ringkasan['rata_nilai']; ?>%</div>
                </div>
            </div>
            
                <div class="col-12 col-md-6 col-lg">
                <div class="p-3 rounded-3 bg-light h-100 materi-summary-card" onclick="bukaMateriDashboard('lemah')">
                    <div class="info-label">Materi Lemah</div>
                    <div class="info-value fs-4"><?= $ringkasan['materi_lemah']; ?></div>
                    <div class="small text-muted">Perlu ditingkatkan</div>
                </div>
            </div>
            <!-- <div class="col-6"> -->
                <div class="col-12 col-6 col-lg">
                <div class="p-3 rounded-3 bg-light h-100 materi-summary-card" onclick="bukaMateriDashboard('dikuasai')">
                    <div class="info-label">Materi Dikuasai</div>
                    <div class="info-value fs-4"><?= $ringkasan['materi_dikuasai'] ?? 0; ?></div>
                    <div class="small text-muted">Nilai 70% ke atas</div>
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


<style>
    .materi-summary-card {
        cursor: pointer;
        transition: .15s ease;
    }

    .materi-summary-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, .08);
    }

    .materi-modal-list .card-mapel {
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 14px;
        padding: 12px 14px;
        margin-bottom: 10px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(15, 23, 42, .04);
    }

    .materi-modal-list .keterangan-hari {
        margin: 0 0 6px 0;
        padding: 0;
        font-size: 12px;
        color: #6c757d;
    }

    .materi-modal-list .keterangan-mapel {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .materi-modal-list .keterangan-mapel-kiri {
        flex: 1;
        min-width: 0;
    }

    .materi-modal-list .keterangan-mapel-kanan {
        flex: 0 0 auto;
    }

    .materi-modal-list .judul-mapel {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.35;
    }
</style>

<div class="modal fade" id="modalMateriDashboard" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="judulModalMateriDashboard">Detail Materi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label small mb-1">Filter Mata Pelajaran</label>
                        <select class="form-select form-select-sm" id="filter_materi_mapel" onchange="materiDashboardResult()">
                            <option value="">Semua Mata Pelajaran</option>
                            <?php foreach (($mapel ?? []) as $m): ?>
                                <option value="<?= $m['id']; ?>"><?= $m['nama_mata_pelajaran']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="data_materi_dashboard" class="materi-modal-list"></div>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-center flex-wrap gap-2 mt-2">
                    <ul class="pagination pagination-sm pagination-boxed mb-0" id="pagination_materi_dashboard"></ul>
                    <div class="d-flex align-items-center gap-2">
                        <label for="jumlah_materi_dashboard" class="mb-0">Tampilkan</label>
                        <select class="form-select form-select-sm" id="jumlah_materi_dashboard">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>entri</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
let jenisMateriDashboard = 'lemah';

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

function bukaMateriDashboard(jenis) {
    jenisMateriDashboard = jenis;

    if (jenis == 'dikuasai') {
        $('#judulModalMateriDashboard').text('Materi yang Dikuasai');
    } else {
        $('#judulModalMateriDashboard').text('Materi Lemah / Perlu Ditingkatkan');
    }

    $('#filter_materi_mapel').val('');
    $('#jumlah_materi_dashboard').val('10');
    $('#modalMateriDashboard').modal('show');
    materiDashboardResult();
}

function materiDashboardResult() {
    $.ajax({
        url: '<?= base_url('dashboard/materi_dashboard_result'); ?>',
        type: 'POST',
        dataType: 'JSON',
        data: {
            jenis: jenisMateriDashboard,
            id_mata_pelajaran: $('#filter_materi_mapel').val()
        },
        beforeSend: function () {
            $('#data_materi_dashboard').html(`
                <div class="card-mapel">
                    <div class="keterangan-mapel">
                        <div class="keterangan-mapel-kiri">
                            <h5 class="judul-mapel">Memuat data...</h5>
                        </div>
                    </div>
                </div>
            `);
            $('#pagination_materi_dashboard').empty();
        },
        success: function (res) {
            let data = Array.isArray(res.data) ? res.data : [];
            let html = '';
            let no = 1;

            if (data.length == 0) {
                html += `
                    <div class="card-mapel">
                        <div class="keterangan-mapel">
                            <div class="keterangan-mapel-kiri">
                                <h5 class="judul-mapel">Tidak ada data</h5>
                                <p style="margin: 0; padding: 0; font-size: 12px; margin-bottom: 4px;">
                                    Belum ada materi sesuai filter yang dipilih.
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                data.forEach(function (item) {
                    let persen = parseFloat(item.persen || 0);
                    let statusText = jenisMateriDashboard == 'dikuasai' ? 'Dikuasai' : 'Perlu Ditingkatkan';
                    let statusClass = jenisMateriDashboard == 'dikuasai' ? 'success' : 'warning';

                    html += `
                        <div class="card-mapel">
                            <p class="keterangan-hari">
                                <span>Status: <span class="badge bg-${statusClass}">${statusText}</span></span>
                            </p>
                            <div class="keterangan-mapel">
                                <div class="keterangan-mapel-kiri">
                                    <h5 class="judul-mapel">${no++}. ${escapeHtml(item.nama_materi || '-')}</h5>
                                    <p style="margin: 0; padding: 0; font-size: 12px; margin-bottom: 4px;">
                                        <b>Mata Pelajaran:</b> ${escapeHtml(item.nama_mata_pelajaran || '-')}<br>
                                        <b>Penguasaan:</b> ${Math.round(persen)}%
                                    </p>
                                </div>
                                <div class="keterangan-mapel-kanan">
                                    <span class="badge bg-light text-dark border">${Math.round(persen)}%</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            $('#data_materi_dashboard').html(html);
            let jumlahAwal = parseInt($('#jumlah_materi_dashboard').val());
            pagingMateri($('#data_materi_dashboard .card-mapel'), jumlahAwal);
        },
        error: function () {
            $('#data_materi_dashboard').html(`
                <div class="card-mapel">
                    <div class="keterangan-mapel">
                        <div class="keterangan-mapel-kiri">
                            <h5 class="judul-mapel">Gagal memuat data</h5>
                        </div>
                    </div>
                </div>
            `);
            $('#pagination_materi_dashboard').empty();
        }
    });
}

function pagingMateri($selector, jumlah_tampil = 10) {
    const $pagination = $('#pagination_materi_dashboard');
    const total = $selector.length;
    const pageSize = parseInt(jumlah_tampil) || 10;

    $pagination.empty();

    if (total <= pageSize) {
        $selector.show();
        return;
    }

    if (typeof Pagination === 'function') {
        window.paginationMateriDashboard = new Pagination('#pagination_materi_dashboard', {
            itemsCount: total,
            pageSize: pageSize,
            onPageChange: function (paging) {
                let start = paging.pageSize * (paging.currentPage - 1);
                let end = start + paging.pageSize;

                $selector.hide();
                for (let i = start; i < end; i++) {
                    $selector.eq(i).show();
                }
            }
        });
        return;
    }

    let pageCount = Math.ceil(total / pageSize);

    function showPage(page) {
        let start = pageSize * (page - 1);
        let end = start + pageSize;

        $selector.hide();
        $selector.slice(start, end).show();

        $pagination.find('.page-item').removeClass('active');
        $pagination.find(`[data-page="${page}"]`).closest('.page-item').addClass('active');
    }

    for (let i = 1; i <= pageCount; i++) {
        $pagination.append(`
            <li class="page-item ${i == 1 ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" data-page="${i}">${i}</a>
            </li>
        `);
    }

    $pagination.find('.page-link').on('click', function () {
        showPage(parseInt($(this).data('page')));
    });

    showPage(1);
}

function escapeHtml(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

$('#jumlah_materi_dashboard').on('change', function () {
    pagingMateri($('#data_materi_dashboard .card-mapel'), parseInt($(this).val()));
});

</script>
