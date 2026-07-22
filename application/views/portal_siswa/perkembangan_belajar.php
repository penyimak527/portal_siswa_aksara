<div class="mb-3">
    <h4 class="fw-bold mb-1">Perkembangan Belajar</h4>
    <p class="text-muted mb-0">Lihat rangkuman perubahan nilai dan kemampuan materi berdasarkan tahun ajaran, kelas, dan mata pelajaran.</p>
</div>

<div class="card student-card mb-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold">Tahun Ajaran</label>
                <select id="filter-tahun-ajaran" class="form-select">
                    <option value="">Pilih Tahun Ajaran</option>
                    <?php foreach ($tahun_ajaran as $row): ?>
                        <option value="<?= htmlspecialchars($row['tahun_ajaran'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($row['tahun_ajaran'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold">Kelas</label>
                <select id="filter-kelas" class="form-select">
                    <option value="">Pilih Kelas</option>
                    <?php foreach ($kelas as $row): ?>
                        <option value="<?= (int) $row['id']; ?>">
                            <?= htmlspecialchars(trim(($row['nama_jenjang'] ?? '') . ' ' . ($row['nama_kelas'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold">Mata Pelajaran</label>
                <select id="filter-mapel" class="form-select">
                    <option value="">Semua Mata Pelajaran</option>
                    <?php foreach ($mapel as $row): ?>
                        <option value="<?= (int) $row['id']; ?>">
                            <?= htmlspecialchars($row['nama_mata_pelajaran'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- <button type="button" id="btn-tampilkan" class="btn btn-primary btn-touch w-100 mt-3">
            Tampilkan Perkembangan
        </button> -->
        <div class="row g-2 mt-3">
    <div class="col-12 col-md-6">
        <button type="button" id="btn-tampilkan" class="btn btn-outline-primary btn-touch w-100">
            Tampilkan Perkembangan
        </button>
    </div>

    <div class="col-12 col-md-6">
        <button type="button" id="btn-laporan" class="btn btn-primary btn-touch w-100">
            Download Laporan
        </button>
    </div>
</div>

<form id="form-laporan"
      action="<?= base_url('laporan_perkembangan_belajar/download'); ?>"
      method="POST"
      class="d-none">
    <input type="hidden" name="tahun_ajaran" id="laporan-tahun-ajaran">
    <input type="hidden" name="id_kelas" id="laporan-id-kelas">
    <input type="hidden" name="id_mata_pelajaran" id="laporan-id-mata-pelajaran">
</form>
    </div>
</div>

<div id="perkembangan-content" class="d-none">
    <div class="card student-card mb-3">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Ringkasan Perkembangan</h5>
            <div class="row g-2">
                <div class="col-6 col-lg-3">
                    <div class="summary-box"><span>Periode</span><strong id="ringkasan-periode">-</strong></div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="summary-box"><span>Kelas</span><strong id="ringkasan-kelas">-</strong></div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="summary-box"><span>Mata Pelajaran</span><strong id="ringkasan-mapel">-</strong></div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="summary-box"><span>Jumlah Sesi</span><strong id="ringkasan-jumlah-sesi">0</strong></div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="summary-box"><span>Rata-rata Nilai</span><strong id="ringkasan-rata-rata">0</strong></div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="summary-box"><span>Nilai Tertinggi</span><strong id="ringkasan-tertinggi">0</strong></div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="summary-box"><span>Nilai Terendah</span><strong id="ringkasan-terendah">0</strong></div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="summary-box"><span>Status Perkembangan</span><strong id="ringkasan-status">-</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card student-card mb-3">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Chart Perkembangan Nilai</h5>
                    <p class="text-muted small mb-0">Klik titik pada chart untuk melihat detail singkat.</p>
                </div>
                <div class="chart-mode" role="group" aria-label="Mode chart">
                    <input type="radio" class="btn-check" name="mode_chart" id="mode-sesi" value="sesi" checked>
                    <label class="btn btn-outline-primary" for="mode-sesi">Per Sesi</label>
                    <input type="radio" class="btn-check" name="mode_chart" id="mode-bulan" value="bulan">
                    <label class="btn btn-outline-primary" for="mode-bulan">Rangkuman per Bulan</label>
                </div>
            </div>

            <div class="chart-scroll">
                <div id="chart-perkembangan"></div>
            </div>
        </div>
    </div>

    <div class="card student-card mb-3">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Analisa Singkat</h5>
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="analysis-box analysis-success">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="ri-checkbox-circle-line"></i>
                            <h6 class="fw-bold mb-0">Materi Dikuasai</h6>
                        </div>
                        <div id="materi-dikuasai"></div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="analysis-box analysis-warning">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="ri-error-warning-line"></i>
                            <h6 class="fw-bold mb-0">Materi Perlu Ditingkatkan</h6>
                        </div>
                        <div id="materi-lemah"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="perkembangan-empty" class="card student-card d-none">
    <div class="card-body text-center py-5">
        <i class="ri-line-chart-line fs-1 text-muted"></i>
        <h5 class="fw-bold mt-2">Belum Ada Data</h5>
        <p id="empty-message" class="text-muted mb-3">Belum ada data perkembangan untuk filter yang dipilih.</p>
        <a href="<?= base_url('sesi'); ?>" class="btn btn-primary btn-touch">Lihat Sesi</a>
    </div>
</div>

<div class="modal fade" id="modal-detail-sesi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold">Detail Sesi</h5>
                    <small class="text-muted">Ringkasan hasil pengerjaan</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="detail-sesi-loading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted small mt-2 mb-0">Memuat detail sesi...</p>
                </div>
                <div id="detail-sesi-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-detail-bulan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 id="detail-bulan-title" class="modal-title fw-bold">Detail Bulan</h5>
                    <small class="text-muted">Daftar sesi yang selesai dikerjakan</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="detail-bulan-loading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted small mt-2 mb-0">Memuat detail bulan...</p>
                </div>

                <div id="detail-bulan-content" class="d-none">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="summary-box">
                                <span>Rata-rata Bulan</span>
                                <strong id="detail-bulan-rata-rata">0%</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="summary-box">
                                <span>Jumlah Sesi</span>
                                <strong id="detail-bulan-jumlah-sesi">0</strong>
                            </div>
                        </div>
                    </div>

                    <div id="detail-bulan-list"></div>

                    <div id="detail-bulan-paging" class="d-flex flex-column flex-md-row justify-content-between align-items-center flex-wrap gap-2 mt-3">
                        <ul class="pagination pagination-sm pagination-boxed mb-0" id="pagination-detail-bulan"></ul>

                        <div class="d-flex align-items-center gap-2">
                            <label for="detail-bulan-length" class="mb-0">Tampilkan</label>
                            <select class="form-select form-select-sm" id="detail-bulan-length">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span>entri</span>
                        </div>
                    </div>
                </div>

                <div id="detail-bulan-empty" class="text-muted text-center py-4 d-none">
                    Tidak ada sesi pada bulan ini.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .summary-box {
        height: 100%;
        padding: 14px;
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 14px;
        background: #fff;
    }

    .summary-box span {
        display: block;
        margin-bottom: 5px;
        color: #6c757d;
        font-size: 12px;
    }

    .summary-box strong {
        display: block;
        font-size: 15px;
        line-height: 1.35;
        word-break: break-word;
    }

    .chart-mode {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .chart-mode .btn {
        min-height: 42px;
        display: flex;
        align-items: center;
    }

    .chart-scroll {
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 5px;
    }

    #chart-perkembangan {
        min-width: 680px;
        min-height: 340px;
    }

    #chart-perkembangan .apexcharts-marker {
        cursor: pointer;
    }

    .analysis-box {
        height: 100%;
        padding: 16px;
        border-radius: 14px;
        border: 1px solid rgba(15, 23, 42, .08);
    }

    .analysis-success {
        background: rgba(25, 135, 84, .04);
    }

    .analysis-warning {
        background: rgba(255, 193, 7, .07);
    }

    .analysis-item {
        padding: 11px 0;
        border-bottom: 1px solid rgba(15, 23, 42, .08);
    }

    .analysis-item:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .analysis-progress {
        height: 6px;
        margin-top: 7px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(15, 23, 42, .08);
    }

    .analysis-progress span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: currentColor;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(15, 23, 42, .08);
    }

    .detail-row:last-child {
        border-bottom: 0;
    }

    .detail-row span:first-child {
        color: #6c757d;
    }

    .detail-row strong {
        text-align: right;
    }

    .session-detail-card {
        padding: 13px;
        margin-bottom: 10px;
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 13px;
    }

    @media (max-width: 575.98px) {
        .chart-mode {
            width: 100%;
        }

        .chart-mode .btn {
            flex: 1;
            justify-content: center;
            font-size: 13px;
        }
    }
</style>

<script>
    let chartPerkembangan = null;
    let dataPerkembangan = null;
    let modalDetailSesi = null;
    let modalDetailBulan = null;
    let paginationDetailBulan = null;
    let lastDetailClick = 0;

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function formatNilai(value) {
        const nilai = Number(value || 0);
        return Number.isInteger(nilai) ? nilai : nilai.toFixed(2);
    }

    function renderMateri(items, target, emptyText) {
        if (!Array.isArray(items) || items.length === 0) {
            $(target).html('<p class="text-muted small mb-0">' + escapeHtml(emptyText) + '</p>');
            return;
        }

        let html = '';
        items.forEach(function(item) {
            const persen = Math.max(0, Math.min(100, Number(item.persen || 0)));
            html += `
                <div class="analysis-item">
                    <div class="d-flex justify-content-between gap-3">
                        <div>
                            <div class="fw-semibold">${escapeHtml(item.nama_materi || '-')}</div>
                            <small class="text-muted">${escapeHtml(item.jumlah_soal || 0)} soal</small>
                        </div>
                        <strong>${formatNilai(persen)}%</strong>
                    </div>
                    <div class="analysis-progress"><span style="width:${persen}%"></span></div>
                </div>
            `;
        });
        $(target).html(html);
    }

    function tampilkanModalSesi() {
        const modalEl = document.getElementById('modal-detail-sesi');

        if (window.bootstrap && bootstrap.Modal) {
            modalDetailSesi = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalDetailSesi.show();
            return;
        }

        if ($.fn.modal) {
            $('#modal-detail-sesi').modal('show');
        }
    }

    function tampilkanModalBulan() {
        const modalEl = document.getElementById('modal-detail-bulan');

        if (window.bootstrap && bootstrap.Modal) {
            modalDetailBulan = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalDetailBulan.show();
            return;
        }

        if ($.fn.modal) {
            $('#modal-detail-bulan').modal('show');
        }
    }

    function pagingDetailBulan($selector, jumlahTampil = 10) {
        const $pagination = $('#pagination-detail-bulan');
        $pagination.empty();

        if (!$selector || $selector.length === 0) {
            $('#detail-bulan-paging').addClass('d-none');
            return;
        }

        $('#detail-bulan-paging').removeClass('d-none');

        const pageSize = parseInt(jumlahTampil, 10) || 10;

        paginationDetailBulan = new Pagination('#pagination-detail-bulan', {
            itemsCount: $selector.length,
            pageSize: pageSize,
            onPageChange: function(paging) {
                const start = paging.pageSize * (paging.currentPage - 1);
                const end = start + paging.pageSize;

                $selector.hide();
                $selector.slice(start, end).show();
            }
        });
    }

    function loadDetailSesi(idPengerjaan) {
        tampilkanModalSesi();

        $.ajax({
            url: '<?= base_url('perkembangan_belajar/perkembangan_detail_result'); ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                jenis_detail: 'sesi',
                id_pengerjaan: idPengerjaan
            },
            beforeSend: function() {
                $('#detail-sesi-content').empty();
                $('#detail-sesi-loading').removeClass('d-none');
            },
            success: function(res) {
                if (res.result !== 'true') {
                    $('#detail-sesi-content').html(
                        '<p class="text-muted text-center mb-0">' +
                        escapeHtml(res.message || 'Detail sesi tidak ditemukan.') +
                        '</p>'
                    );
                    return;
                }

                const item = res.detail || {};

                $('#detail-sesi-content').html(`
                    <div class="detail-row"><span>Nama Sesi</span><strong>${escapeHtml(item.nama_sesi || '-')}</strong></div>
                    <div class="detail-row"><span>Mata Pelajaran</span><strong>${escapeHtml(item.nama_mata_pelajaran || '-')}</strong></div>
                    <div class="detail-row"><span>Jenis Pengerjaan</span><strong>${escapeHtml(item.jenis_pengerjaan || '-')}</strong></div>
                    <div class="detail-row"><span>Tanggal</span><strong>${escapeHtml(item.tanggal || '-')}</strong></div>
                    <div class="detail-row"><span>Nilai</span><strong>${formatNilai(item.nilai)}%</strong></div>
                `);
            },
            error: function() {
                $('#detail-sesi-content').html(
                    '<p class="text-danger text-center mb-0">Detail sesi tidak dapat dimuat.</p>'
                );
            },
            complete: function() {
                $('#detail-sesi-loading').addClass('d-none');
            }
        });
    }

    function loadDetailBulan(periode, label) {
        tampilkanModalBulan();

        $.ajax({
            url: '<?= base_url('perkembangan_belajar/perkembangan_detail_result'); ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                jenis_detail: 'bulan',
                periode: periode,
                tahun_ajaran: $('#filter-tahun-ajaran').val(),
                id_kelas: $('#filter-kelas').val(),
                id_mata_pelajaran: $('#filter-mapel').val()
            },
            beforeSend: function() {
                $('#detail-bulan-title').text('Detail Bulan ' + label);
                $('#detail-bulan-content').addClass('d-none');
                $('#detail-bulan-empty').addClass('d-none').empty();
                $('#detail-bulan-list').empty();
                $('#pagination-detail-bulan').empty();
                $('#detail-bulan-loading').removeClass('d-none');
            },
            success: function(res) {
                if (res.result !== 'true') {
                    $('#detail-bulan-empty')
                        .removeClass('d-none')
                        .text(res.message || 'Detail bulan tidak ditemukan.');
                    return;
                }

                const daftarSesi = Array.isArray(res.daftar_sesi) ? res.daftar_sesi : [];

                $('#detail-bulan-rata-rata').text(formatNilai(res.rata_rata) + '%');
                $('#detail-bulan-jumlah-sesi').text(res.jumlah_sesi || 0);

                if (daftarSesi.length === 0) {
                    $('#detail-bulan-empty')
                        .removeClass('d-none')
                        .text('Tidak ada sesi pada bulan ini.');
                    return;
                }

                let html = '';

                daftarSesi.forEach(function(item, index) {
                    html += `
                        <div class="session-detail-card detail-bulan-row">
                            <div class="fw-bold mb-1">${index + 1}. ${escapeHtml(item.nama_sesi || '-')}</div>
                            <div class="small text-muted mb-2">
                                ${escapeHtml(item.nama_mata_pelajaran || '-')} ·
                                ${escapeHtml(item.jenis_pengerjaan || '-')}
                            </div>
                            <div class="d-flex justify-content-between gap-3 small">
                                <span>${escapeHtml(item.tanggal || '-')}</span>
                                <strong>${formatNilai(item.nilai)}%</strong>
                            </div>
                        </div>
                    `;
                });

                $('#detail-bulan-list').html(html);
                $('#detail-bulan-content').removeClass('d-none');
                $('#detail-bulan-length').val('10');

                pagingDetailBulan(
                    $('#detail-bulan-list .detail-bulan-row'),
                    $('#detail-bulan-length').val()
                );
            },
            error: function() {
                $('#detail-bulan-empty')
                    .removeClass('d-none')
                    .text('Detail bulan tidak dapat dimuat.');
            },
            complete: function() {
                $('#detail-bulan-loading').addClass('d-none');
            }
        });
    }

    function renderChart() {
        if (!dataPerkembangan) return;

        const mode = $('input[name="mode_chart"]:checked').val();
        const items = mode === 'bulan' ? dataPerkembangan.chart_bulan : dataPerkembangan.chart_sesi;
        const categories = items.map(item => item.label);
        const values = items.map(item => Number(item.nilai));

        if (chartPerkembangan) {
            chartPerkembangan.destroy();
        }

        function bukaDetailChart(dataPointIndex) {
            const now = Date.now();
            if (now - lastDetailClick < 300) return;

            const item = items[dataPointIndex];
            if (!item) return;

            lastDetailClick = now;
            if (mode === 'bulan') {
                loadDetailBulan(item.periode, item.label);
            } else {
                loadDetailSesi(item.id_pengerjaan);
            }
        }

        chartPerkembangan = new ApexCharts(document.querySelector('#chart-perkembangan'), {
            chart: {
                type: 'line',
                height: 330,
                toolbar: { show: false },
                zoom: { enabled: false },
                events: {
                    markerClick: function(event, chartContext, config) {
                        bukaDetailChart(config.dataPointIndex);
                    },
                    dataPointSelection: function(event, chartContext, config) {
                        bukaDetailChart(config.dataPointIndex);
                    }
                }
            },
            series: [{
                name: mode === 'bulan' ? 'Rata-rata Bulan' : 'Nilai Sesi',
                data: values
            }],
            xaxis: {
                categories: categories,
                labels: {
                    rotate: -35,
                    trim: false
                }
            },
            yaxis: {
                min: 0,
                max: 100,
                tickAmount: 5,
                title: { text: 'Nilai' }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 6,
                hover: { size: 8 }
            },
            dataLabels: { enabled: false },
            tooltip: {
                shared: false,
                intersect: true,
                custom: function({ dataPointIndex }) {
                    const item = items[dataPointIndex] || {};
                    if (mode === 'bulan') {
                        return `<div class="p-2 small"><strong>${escapeHtml(item.label || '-')}</strong><br>Rata-rata: ${formatNilai(item.nilai)}%<br>Jumlah sesi: ${escapeHtml(item.jumlah_sesi || 0)}<br><span class="text-muted">Klik untuk melihat daftar sesi.</span></div>`;
                    }

                    return `<div class="p-2 small" style="max-width:240px"><strong>${escapeHtml(item.nama_sesi || '-')}</strong><br>${escapeHtml(item.mata_pelajaran || '-')}<br>${escapeHtml(item.jenis_pengerjaan || '-')} · ${escapeHtml(item.tanggal || '-')}<br>Nilai: ${formatNilai(item.nilai)}%<br><span class="text-muted">Klik untuk melihat detail.</span></div>`;
                }
            },
            noData: { text: 'Belum ada data perkembangan' }
        });

        chartPerkembangan.render();
    }

    function loadPerkembangan() {
        const tahunAjaran = $('#filter-tahun-ajaran').val();
        const idKelas = $('#filter-kelas').val();

        if (!tahunAjaran || !idKelas) {
            Swal.fire('Perhatian', 'Tahun ajaran dan kelas wajib dipilih.', 'warning');
            return;
        }

        $.ajax({
            url: '<?= base_url('perkembangan_belajar/perkembangan_result'); ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                tahun_ajaran: tahunAjaran,
                id_kelas: idKelas,
                id_mata_pelajaran: $('#filter-mapel').val()
            },
            // beforeSend: function() {
            //     $('#btn-tampilkan').prop('disabled', true).text('Memuat...');
            // },
            success: function(res) {
                if (res.result !== 'true') {
                    dataPerkembangan = null;
                    if (chartPerkembangan) {
                        chartPerkembangan.destroy();
                        chartPerkembangan = null;
                    }
                    $('#perkembangan-content').addClass('d-none');
                    $('#empty-message').text(res.message || 'Belum ada data perkembangan untuk filter yang dipilih.');
                    $('#perkembangan-empty').removeClass('d-none');
                    return;
                }

                dataPerkembangan = res;
                const ringkasan = res.ringkasan || {};

                $('#perkembangan-empty').addClass('d-none');
                $('#perkembangan-content').removeClass('d-none');
                $('#ringkasan-periode').text(ringkasan.periode || '-');
                $('#ringkasan-kelas').text(ringkasan.kelas || '-');
                $('#ringkasan-mapel').text(ringkasan.mata_pelajaran || '-');
                $('#ringkasan-jumlah-sesi').text(ringkasan.jumlah_sesi || 0);
                $('#ringkasan-rata-rata').text(formatNilai(ringkasan.rata_rata) + '%');
                $('#ringkasan-tertinggi').text(formatNilai(ringkasan.nilai_tertinggi) + '%');
                $('#ringkasan-terendah').text(formatNilai(ringkasan.nilai_terendah) + '%');
                $('#ringkasan-status').text(ringkasan.status_perkembangan || '-');

                renderMateri(res.materi_dikuasai, '#materi-dikuasai', 'Belum ada materi yang masuk kategori dikuasai.');
                renderMateri(res.materi_perlu_ditingkatkan, '#materi-lemah', 'Belum ada materi yang perlu ditingkatkan.');
                renderChart();
            },
            error: function() {
                Swal.fire('Gagal', 'Data perkembangan tidak dapat dimuat.', 'error');
            },
            complete: function() {
                $('#btn-tampilkan').prop('disabled', false).text('Tampilkan Perkembangan');
            }
        });
    }


    function downloadLaporan() {
        const tahunAjaran = $('#filter-tahun-ajaran').val();
        const idKelas = $('#filter-kelas').val();
        const idMataPelajaran = $('#filter-mapel').val();

        if (!tahunAjaran || !idKelas) {
            Swal.fire('Perhatian', 'Tahun ajaran dan kelas wajib dipilih.', 'warning');
            return;
        }

        $('#laporan-tahun-ajaran').val(tahunAjaran);
        $('#laporan-id-kelas').val(idKelas);
        $('#laporan-id-mata-pelajaran').val(idMataPelajaran);

        $('#form-laporan').submit();
    }

    $(document).ready(function () {
        $(document).on('change', '#detail-bulan-length', function () {
            pagingDetailBulan(
                $('#detail-bulan-list .detail-bulan-row'),
                $(this).val()
            );
        });

        $('#modal-detail-bulan').on('hidden.bs.modal', function () {
            $('#detail-bulan-list').empty();
            $('#pagination-detail-bulan').empty();
            $('#detail-bulan-content').addClass('d-none');
            $('#detail-bulan-empty').addClass('d-none').empty();
            paginationDetailBulan = null;
        });

        $('input[name="mode_chart"]').on('change', function () {
            renderChart();
        });

        $('#btn-tampilkan').on('click', function () {
            loadPerkembangan();
        });

        $('#btn-laporan').on('click', function () {
            downloadLaporan();
        });

        if ($('#filter-tahun-ajaran option').length > 1) {
            $('#filter-tahun-ajaran').prop('selectedIndex', 1);
        }

        if ($('#filter-kelas option').length > 1) {
            $('#filter-kelas').prop('selectedIndex', 1);
        }

        if ($('#filter-tahun-ajaran').val() && $('#filter-kelas').val()) {
            loadPerkembangan();
        }
    });
</script>