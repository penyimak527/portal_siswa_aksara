<div class="mb-3">
    <p class="text-muted mb-0">Pantau perubahan nilai berdasarkan kelas, semester, mata pelajaran, dan jenis pengerjaan.</p>
</div>

<div class="card student-card mb-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">Kelas</label>
                <select id="filter-kelas" class="form-select">
                    <option value="">Semua Kelas</option>
                    <?php foreach ($kelas as $row): ?>
                        <option value="<?= (int) $row['id']; ?>"><?= htmlspecialchars(($row['nama_jenjang'] ?? '') . ' ' . ($row['nama_kelas'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">Semester</label>
                <select id="filter-semester" class="form-select">
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">Mata Pelajaran</label>
                <select id="filter-mapel" class="form-select">
                    <option value="">Semua Mata Pelajaran</option>
                    <?php foreach ($mapel as $row): ?>
                        <option value="<?= (int) $row['id']; ?>"><?= htmlspecialchars($row['nama_mata_pelajaran'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">Jenis Pengerjaan</label>
                <select id="filter-jenis" class="form-select">
                    <option value="">Semua</option>
                    <option value="Bimbel">Bimbel</option>
                    <option value="Rumah">Rumah</option>
                </select>
            </div>
        </div>
        <button type="button" id="btn-tampilkan" class="btn btn-primary btn-touch w-100 mt-3">Tampilkan Perkembangan</button>
    </div>
</div>

<div id="perkembangan-content" class="d-none">
    <!-- <div class="card student-card mb-3">
        <div class="card-body py-3">
            <div class="row g-0 text-center">
                <div class="col-3 border-end"><small class="text-muted d-block">Rata-rata Semester</small><strong id="rata-rata">0</strong></div>
                <div class="col-3 border-end"><small class="text-muted d-block">Nilai Awal</small><strong id="nilai-awal">-</strong></div>
                <div class="col-3 border-end"><small class="text-muted d-block">Nilai Terbaru</small><strong id="nilai-terbaru">-</strong></div>
                <div class="col-3"><small class="text-muted d-block">Perubahan</small><strong id="tren">-</strong></div>
            </div>
        </div>
    </div> -->

    <!-- <div class="card student-card mb-3"> -->
        <div class="card student-card chart-card-perkembangan mb-3">
        <div class="card-body">
            <h5 class="fw-bold mb-1">Perkembangan Nilai per Bulan</h5>
            <p class="text-muted small mb-3">Nilai merupakan rata-rata seluruh pengerjaan pada setiap bulan dalam semester yang dipilih.</p>
            <div id="chart-perkembangan"></div>
        </div>
    </div>

    <!-- <div class="card student-card mb-3">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Kemampuan Materi</h5>
            <div id="materi-list"></div>
        </div>
    </div>-->
    <!-- <div class="card student-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Pengerjaan Terbaru</h5>
                <a href="<?= base_url('riwayat'); ?>" class="small fw-semibold">Lihat Riwayat</a>
            </div>
            <div id="pengerjaan-list"></div>
        </div>
    </div> -->
</div>

<div id="perkembangan-empty" class="card student-card d-none">
    <div class="card-body text-center py-5">
        <i class="ri-line-chart-line fs-1 text-muted"></i>
        <h5 class="fw-bold mt-2">Belum Ada Data</h5>
        <p class="text-muted mb-3">Belum ada pengerjaan yang sesuai dengan filter yang dipilih.</p>
        <a href="<?= base_url('sesi'); ?>" class="btn btn-primary btn-touch">Lihat Sesi</a>
    </div>
</div>


<div class="modal fade" id="modal-materi-bulanan" tabindex="-1" aria-labelledby="modalMateriBulananLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold" id="modalMateriBulananLabel">Detail Kemampuan Materi</h5>
                    <small id="modal-materi-periode" class="text-muted"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="modal-materi-loading" class="text-center py-5 d-none">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="text-muted small mt-2 mb-0">Memuat kemampuan materi...</p>
                </div>

                <div id="modal-materi-content">
                    <section class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0"><i class="ri-checkbox-circle-line text-success me-1"></i>Materi yang Dikuasai</h6>
                            <span id="total-materi-dikuasai" class="badge bg-success-subtle text-success">0 materi</span>
                        </div>
                        <div id="list-materi-dikuasai"></div>
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center flex-wrap gap-2 mt-2">
                            <ul class="pagination pagination-sm pagination-boxed mb-0" id="pagination_dikuasai"></ul>

                            <div class="d-flex align-items-center gap-2">
                                <label for="dt-length-dikuasai" class="mb-0">Tampilkan</label>
                                <select class="form-select form-select-sm" id="dt-length-dikuasai">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span>entri</span>
                            </div>
                        </div>
                    </section>

                    <hr>

                    <section>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0"><i class="ri-error-warning-line text-warning me-1"></i>Materi yang Perlu Ditingkatkan</h6>
                            <span id="total-materi-lemah" class="badge bg-warning-subtle text-warning-emphasis">0 materi</span>
                        </div>
                        <div id="list-materi-lemah"></div>
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center flex-wrap gap-2 mt-2">
                            <ul class="pagination pagination-sm pagination-boxed mb-0" id="pagination_lemah"></ul>

                            <div class="d-flex align-items-center gap-2">
                                <label for="dt-length-lemah" class="mb-0">Tampilkan</label>
                                <select class="form-select form-select-sm" id="dt-length-lemah">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span>entri</span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .chart-card-perkembangan,
    .chart-card-perkembangan .card-body,
    #chart-perkembangan,
    #chart-perkembangan .apexcharts-canvas {
        overflow: visible !important;
    }

    .chart-card-perkembangan {
        position: relative;
        z-index: 2;
    }

    #chart-perkembangan {
        position: relative;
        min-height: 340px;
    }

    #chart-perkembangan .apexcharts-marker {
        cursor: pointer;
    }

    #chart-perkembangan .apexcharts-tooltip {
        z-index: 99999 !important;
        white-space: normal !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .14) !important;
    }

    #modal-materi-bulanan .card-mapel {
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 14px;
        padding: 12px 14px;
        margin-bottom: 10px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(15, 23, 42, .04);
    }

    #modal-materi-bulanan .keterangan-hari {
        margin: 0 0 6px 0;
        padding: 0;
        font-size: 12px;
        color: #6c757d;
    }

    #modal-materi-bulanan .keterangan-mapel {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    #modal-materi-bulanan .keterangan-mapel-kiri {
        flex: 1;
        min-width: 0;
    }

    #modal-materi-bulanan .keterangan-mapel-kanan {
        flex: 0 0 auto;
    }

    #modal-materi-bulanan .judul-mapel {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.35;
    }

    @media (max-width: 575.98px) {
        #chart-perkembangan .apexcharts-tooltip {
            max-width: 240px !important;
        }
    }
</style>

<script>
    let chartPerkembangan = null;
    let modalMateriBulanan = null;
    let lastChartPointClick = 0;
    let konteksMateriBulanan = {
        bulan: 0,
        jenis_pengerjaan: ''
    };

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function bukaModalMateriBulanan(bulan, namaBulan, jenisPengerjaan) {
        konteksMateriBulanan = {
            bulan: Number(bulan),
            jenis_pengerjaan: jenisPengerjaan
        };

        $('#modal-materi-periode').text(namaBulan + ' • ' + jenisPengerjaan);
        $('#dt-length-dikuasai').val('10');
        $('#dt-length-lemah').val('10');

        const modalEl = document.getElementById('modal-materi-bulanan');
        if (window.bootstrap && bootstrap.Modal) {
            modalMateriBulanan = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalMateriBulanan.show();
        } else if ($.fn.modal) {
            $('#modal-materi-bulanan').modal('show');
        }

        loadMateriBulanan();
    }

    function renderMateriList(items, target, emptyText, jenis) {
        if (!items || items.length === 0) {
            $(target).html('<div class="card-mapel"><div class="keterangan-mapel"><div class="keterangan-mapel-kiri"><h5 class="judul-mapel">Tidak ada data</h5><p style="margin:0; padding:0; font-size:12px; margin-bottom:4px;">' + escapeHtml(emptyText) + '</p></div></div></div>');
            return;
        }

        let html = '';
        let statusClass = jenis === 'dikuasai' ? 'success' : 'warning';
        let no = 1;

        items.forEach(function(item) {
            let persen = Math.round(Number(item.persen || 0));
            html += `
                <div class="card-mapel">
                    <p class="keterangan-hari">
                        <span>Status: <span class="badge bg-${statusClass}">${escapeHtml(item.status || '-')}</span></span>
                    </p>
                    <div class="keterangan-mapel">
                        <div class="keterangan-mapel-kiri">
                            <h5 class="judul-mapel">${no++}. ${escapeHtml(item.nama_materi || '-')}</h5>
                            <p style="margin: 0; padding: 0; font-size: 12px; margin-bottom: 4px;">
                                <b>Jumlah Soal:</b> ${escapeHtml(item.jumlah_soal || 0)}<br>
                                <b>Jumlah Pengerjaan:</b> ${escapeHtml(item.jumlah_pengerjaan || 0)}<br>
                                <b>Penguasaan:</b> ${persen}%
                            </p>
                        </div>
                        <div class="keterangan-mapel-kanan">
                            <span class="badge bg-light text-dark border">${persen}%</span>
                        </div>
                    </div>
                </div>
            `;
        });

        $(target).html(html);
    }

    function applyPagingMateriDikuasai() {
        let jumlah = parseInt($('#dt-length-dikuasai').val()) || 10;
        paging($('#list-materi-dikuasai .card-mapel'), jumlah, '#pagination_dikuasai');
    }

    function applyPagingMateriLemah() {
        let jumlah = parseInt($('#dt-length-lemah').val()) || 10;
        paging($('#list-materi-lemah .card-mapel'), jumlah, '#pagination_lemah');
    }

    function paging($selector, jumlah_tampil = 10, paginationSelector = '#pagination') {
        $(paginationSelector).empty();

        if (!$selector || $selector.length === 0) {
            return;
        }

        new Pagination(paginationSelector, {
            itemsCount: $selector.length,
            pageSize: parseInt(jumlah_tampil),
            onPageChange: function (paging) {
                let start = paging.pageSize * (paging.currentPage - 1);
                let end = start + paging.pageSize;
                let $rows = $selector;

                $rows.hide();

                for (let i = start; i < end; i++) {
                    $rows.eq(i).show();
                }
            }
        });
    }

    function loadMateriBulanan() {
        $.ajax({
            url: '<?= base_url('perkembangan/materi_bulanan_result'); ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                id_kelas: $('#filter-kelas').val(),
                semester: $('#filter-semester').val(),
                id_mata_pelajaran: $('#filter-mapel').val(),
                jenis_pengerjaan: konteksMateriBulanan.jenis_pengerjaan,
                bulan: konteksMateriBulanan.bulan
            },
            beforeSend: function() {
                $('#modal-materi-content').addClass('d-none');
                $('#modal-materi-loading').removeClass('d-none');
            },
            success: function(res) {
                if (res.result !== 'true') {
                    $('#list-materi-dikuasai').html('<div class="text-center text-muted small py-4">' + escapeHtml(res.message || 'Data tidak tersedia.') + '</div>');
                    $('#list-materi-lemah').empty();
                    $('#pagination_dikuasai, #pagination_lemah').empty();
                    return;
                }

                $('#modal-materi-periode').text(res.periode.nama_bulan + ' • ' + res.periode.jenis_pengerjaan);

                let materiDikuasai = Array.isArray(res.materi_dikuasai) ? res.materi_dikuasai : [];
                let materiLemah = Array.isArray(res.materi_lemah) ? res.materi_lemah : [];

                $('#total-materi-dikuasai').text(materiDikuasai.length + ' materi');
                $('#total-materi-lemah').text(materiLemah.length + ' materi');

                renderMateriList(materiDikuasai, '#list-materi-dikuasai', 'Belum ada materi yang masuk kategori dikuasai pada bulan ini.', 'dikuasai');
                renderMateriList(materiLemah, '#list-materi-lemah', 'Belum ada materi yang perlu ditingkatkan pada bulan ini.', 'lemah');

                applyPagingMateriDikuasai();
                applyPagingMateriLemah();
            },
            error: function() {
                $('#list-materi-dikuasai').html('<div class="text-center text-danger small py-4">Data kemampuan materi tidak dapat dimuat.</div>');
                $('#list-materi-lemah').empty();
            },
            complete: function() {
                $('#modal-materi-loading').addClass('d-none');
                $('#modal-materi-content').removeClass('d-none');
            }
        });
    }


    function loadPerkembangan() {
        $.ajax({
            url: '<?= base_url('perkembangan/perkembangan_result'); ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                id_kelas: $('#filter-kelas').val(),
                semester: $('#filter-semester').val(),
                id_mata_pelajaran: $('#filter-mapel').val(),
                jenis_pengerjaan: $('#filter-jenis').val()
            },
            beforeSend: function () {
                $('#btn-tampilkan').prop('disabled', true).text('Memuat...');
            },
            success: function (res) {
                if (res.result != 'true') {
                    $('#perkembangan-content').addClass('d-none');
                    $('#perkembangan-empty').removeClass('d-none');
                    return;
                }

                const data = res;
                $('#perkembangan-empty').addClass('d-none');
                $('#perkembangan-content').removeClass('d-none');
                $('#rata-rata').text(data.ringkasan.rata_rata);
                $('#nilai-awal').text(data.ringkasan.nilai_awal);
                $('#nilai-terbaru').text(data.ringkasan.nilai_terbaru);
                $('#tren').text(data.ringkasan.tren);

                if (chartPerkembangan) chartPerkembangan.destroy();

                const jenisFilter = $('#filter-jenis').val();
                const series = [];

                if (jenisFilter === '' || jenisFilter === 'Bimbel') {
                    series.push({
                        name: 'Bimbel',
                        data: data.grafik.map(item => item.bimbel === null ? null : Number(item.bimbel))
                    });
                }

                if (jenisFilter === '' || jenisFilter === 'Rumah') {
                    series.push({
                        name: 'Rumah',
                        data: data.grafik.map(item => item.rumah === null ? null : Number(item.rumah))
                    });
                }

                function handleChartPointClick(config) {
                    const now = Date.now();
                    if (now - lastChartPointClick < 250) return;

                    const seriesIndex = Number(config.seriesIndex);
                    const dataPointIndex = Number(config.dataPointIndex);
                    if (Number.isNaN(seriesIndex) || Number.isNaN(dataPointIndex)) return;

                    const item = data.grafik[dataPointIndex];
                    const seri = series[seriesIndex];
                    if (!item || !seri) return;

                    const nilaiTitik = seri.data[dataPointIndex];
                    if (nilaiTitik === null || typeof nilaiTitik === 'undefined') return;

                    lastChartPointClick = now;
                    bukaModalMateriBulanan(item.bulan, item.label, seri.name);
                }

                chartPerkembangan = new ApexCharts(document.querySelector('#chart-perkembangan'), {
                    chart: {
                        type: 'line',
                        height: 320,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        events: {
                            markerClick: function(event, chartContext, config) {
                                handleChartPointClick(config);
                            },
                            dataPointSelection: function(event, chartContext, config) {
                                handleChartPointClick(config);
                            }
                        }
                    },
                    series: series,
                    xaxis: {
                        categories: data.grafik.map(item => item.label),
                        labels: { rotate: 0, trim: false }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        tickAmount: 5,
                        title: { text: 'Nilai' }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3,
                        connectNulls: false
                    },
                    markers: {
                        size: 6,
                        hover: { size: 8 },
                        discrete: []
                    },
                    dataLabels: { enabled: false },
                    legend: { position: 'top', horizontalAlign: 'right' },
                    noData: { text: 'Belum ada data pada semester ini' },
                  tooltip: {
    shared: false,
    intersect: true,
    followCursor: true,
    fixed: {
        enabled: false
    },
                        custom: function({dataPointIndex}) {
                            const item = data.grafik[dataPointIndex];
                            let html = '<div class="p-2 small" style="min-width:190px"><strong>' + escapeHtml(item.label) + '</strong>';

                            if (jenisFilter === '' || jenisFilter === 'Bimbel') {
                                html += '<hr class="my-1"><strong>Bimbel</strong><br>';
                                html += item.bimbel === null
                                    ? 'Belum ada pengerjaan'
                                    : 'Rata-rata: ' + escapeHtml(item.bimbel) + '<br>Jumlah: ' + escapeHtml(item.jumlah_bimbel) + ' pengerjaan<br>Tertinggi: ' + escapeHtml(item.tertinggi_bimbel) + '<br>Terendah: ' + escapeHtml(item.terendah_bimbel);
                            }

                            if (jenisFilter === '' || jenisFilter === 'Rumah') {
                                html += '<hr class="my-1"><strong>Rumah</strong><br>';
                                html += item.rumah === null
                                    ? 'Belum ada pengerjaan'
                                    : 'Rata-rata: ' + escapeHtml(item.rumah) + '<br>Jumlah: ' + escapeHtml(item.jumlah_rumah) + ' pengerjaan<br>Tertinggi: ' + escapeHtml(item.tertinggi_rumah) + '<br>Terendah: ' + escapeHtml(item.terendah_rumah);
                            }

                            return html + '<hr class="my-1"><span class="text-muted">Klik titik untuk melihat materi.</span></div>';
                        }
                    }
                });
                chartPerkembangan.render();

                let materiHtml = '';
                (data.materi || []).forEach(function (item) {
                    materiHtml += '<div class="mb-3"><div class="d-flex justify-content-between gap-2 mb-1"><span class="fw-semibold">' + escapeHtml(item.nama_materi) + '</span><span class="small">' + escapeHtml(item.persen) + '% · ' + escapeHtml(item.status) + '</span></div><div class="materi-bar"><span style="width:' + Number(item.persen) + '%"></span></div></div>';
                });
                $('#materi-list').html(materiHtml || '<p class="text-muted small mb-0">Data materi belum tersedia.</p>');

                let riwayatHtml = '';
                (data.terbaru || []).forEach(function (item) {
                    riwayatHtml += '<div class="d-flex justify-content-between align-items-center py-2 border-bottom"><div class="pe-2"><div class="fw-semibold">' + escapeHtml(item.nama_sesi) + '</div><small class="text-muted">' + escapeHtml(item.tanggal) + ' · ' + escapeHtml(item.jenis_pengerjaan) + '</small></div><strong>' + escapeHtml(item.nilai_akhir) + '</strong></div>';
                });
                $('#pengerjaan-list').html(riwayatHtml || '<p class="text-muted small mb-0">Belum ada data.</p>');
            },
            error: function () {
                Swal.fire('Gagal', 'Data perkembangan tidak dapat dimuat.', 'error');
            },
            complete: function () {
                $('#btn-tampilkan').prop('disabled', false).text('Tampilkan Perkembangan');
            }
        });
    }

    $('#dt-length-dikuasai').on('change', function () {
        applyPagingMateriDikuasai();
    });

    $('#dt-length-lemah').on('change', function () {
        applyPagingMateriLemah();
    });

    $('#btn-tampilkan').on('click', loadPerkembangan);
    loadPerkembangan();
</script>
