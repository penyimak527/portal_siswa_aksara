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

    <div class="card student-card mb-3">
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
                        <div id="pagination-materi-dikuasai" class="d-flex justify-content-center flex-wrap gap-1 mt-3"></div>
                    </section>

                    <hr>

                    <section>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0"><i class="ri-error-warning-line text-warning me-1"></i>Materi yang Perlu Ditingkatkan</h6>
                            <span id="total-materi-lemah" class="badge bg-warning-subtle text-warning-emphasis">0 materi</span>
                        </div>
                        <div id="list-materi-lemah"></div>
                        <div id="pagination-materi-lemah" class="d-flex justify-content-center flex-wrap gap-1 mt-3"></div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #chart-perkembangan .apexcharts-marker {
        cursor: pointer;
    }
</style>

<script>
    let chartPerkembangan = null;
    let modalMateriBulanan = null;
    let lastChartPointClick = 0;
    let konteksMateriBulanan = {
        bulan: 0,
        jenis_pengerjaan: '',
        page_dikuasai: 1,
        page_lemah: 1
    };

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function bukaModalMateriBulanan(bulan, namaBulan, jenisPengerjaan) {
        konteksMateriBulanan = {
            bulan: Number(bulan),
            jenis_pengerjaan: jenisPengerjaan,
            page_dikuasai: 1,
            page_lemah: 1
        };

        $('#modal-materi-periode').text(namaBulan + ' • ' + jenisPengerjaan);

        const modalEl = document.getElementById('modal-materi-bulanan');
        if (window.bootstrap && bootstrap.Modal) {
            modalMateriBulanan = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalMateriBulanan.show();
        } else if ($.fn.modal) {
            $('#modal-materi-bulanan').modal('show');
        }

        loadMateriBulanan();
    }

    function renderMateriList(items, target, emptyText) {
        if (!items || items.length === 0) {
            $(target).html('<div class="text-center text-muted small py-4 border rounded-3">' + escapeHtml(emptyText) + '</div>');
            return;
        }

        let html = '';
        items.forEach(function(item, index) {
            html += '<div class="border rounded-3 p-3 mb-2">';
            html += '<div class="d-flex justify-content-between gap-3 align-items-start">';
            html += '<div><div class="fw-semibold">' + escapeHtml(item.nama_materi) + '</div>';
            html += '<small class="text-muted">' + escapeHtml(item.jumlah_soal) + ' soal dari ' + escapeHtml(item.jumlah_pengerjaan) + ' pengerjaan</small></div>';
            html += '<div class="text-end flex-shrink-0"><strong>' + escapeHtml(item.persen) + '%</strong><br><small class="text-muted">' + escapeHtml(item.status) + '</small></div>';
            html += '</div>';
            html += '<div class="progress mt-2" style="height:6px"><div class="progress-bar" role="progressbar" style="width:' + Number(item.persen) + '%" aria-valuenow="' + Number(item.persen) + '" aria-valuemin="0" aria-valuemax="100"></div></div>';
            html += '</div>';
        });
        $(target).html(html);
    }

    function renderPagination(info, target, jenis) {
        const totalPage = Number(info.total_page || 1);
        const currentPage = Number(info.page || 1);
        if (Number(info.total || 0) === 0 || totalPage <= 1) {
            $(target).empty();
            return;
        }

        let html = '<button type="button" class="btn btn-sm btn-outline-secondary materi-page" data-jenis="' + jenis + '" data-page="' + (currentPage - 1) + '" ' + (currentPage <= 1 ? 'disabled' : '') + '>Sebelumnya</button>';
        for (let page = 1; page <= totalPage; page++) {
            html += '<button type="button" class="btn btn-sm ' + (page === currentPage ? 'btn-primary' : 'btn-outline-secondary') + ' materi-page" data-jenis="' + jenis + '" data-page="' + page + '">' + page + '</button>';
        }
        html += '<button type="button" class="btn btn-sm btn-outline-secondary materi-page" data-jenis="' + jenis + '" data-page="' + (currentPage + 1) + '" ' + (currentPage >= totalPage ? 'disabled' : '') + '>Selanjutnya</button>';
        $(target).html(html);
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
                bulan: konteksMateriBulanan.bulan,
                page_dikuasai: konteksMateriBulanan.page_dikuasai,
                page_lemah: konteksMateriBulanan.page_lemah
            },
            beforeSend: function() {
                $('#modal-materi-content').addClass('d-none');
                $('#modal-materi-loading').removeClass('d-none');
            },
            success: function(res) {
                if (res.result !== 'true') {
                    $('#list-materi-dikuasai').html('<div class="text-center text-muted small py-4">' + escapeHtml(res.message || 'Data tidak tersedia.') + '</div>');
                    $('#list-materi-lemah').empty();
                    $('#pagination-materi-dikuasai, #pagination-materi-lemah').empty();
                    return;
                }

                $('#modal-materi-periode').text(res.periode.nama_bulan + ' • ' + res.periode.jenis_pengerjaan);
                $('#total-materi-dikuasai').text(res.pagination_dikuasai.total + ' materi');
                $('#total-materi-lemah').text(res.pagination_lemah.total + ' materi');

                renderMateriList(res.materi_dikuasai, '#list-materi-dikuasai', 'Belum ada materi yang masuk kategori dikuasai pada bulan ini.');
                renderMateriList(res.materi_lemah, '#list-materi-lemah', 'Belum ada materi yang perlu ditingkatkan pada bulan ini.');
                renderPagination(res.pagination_dikuasai, '#pagination-materi-dikuasai', 'dikuasai');
                renderPagination(res.pagination_lemah, '#pagination-materi-lemah', 'lemah');
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

    $(document).on('click', '.materi-page', function() {
        if ($(this).prop('disabled')) return;
        const jenis = $(this).data('jenis');
        const page = Number($(this).data('page'));
        if (jenis === 'dikuasai') {
            konteksMateriBulanan.page_dikuasai = page;
        } else {
            konteksMateriBulanan.page_lemah = page;
        }
        loadMateriBulanan();
    });

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

    $('#btn-tampilkan').on('click', loadPerkembangan);
    loadPerkembangan();
</script>
