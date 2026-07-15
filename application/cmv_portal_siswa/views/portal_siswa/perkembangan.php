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
    <div class="card student-card mb-3">
        <div class="card-body py-3">
            <div class="row g-0 text-center">
                <div class="col-3 border-end"><small class="text-muted d-block">Rata-rata Semester</small><strong id="rata-rata">0</strong></div>
                <div class="col-3 border-end"><small class="text-muted d-block">Nilai Awal</small><strong id="nilai-awal">-</strong></div>
                <div class="col-3 border-end"><small class="text-muted d-block">Nilai Terbaru</small><strong id="nilai-terbaru">-</strong></div>
                <div class="col-3"><small class="text-muted d-block">Perubahan</small><strong id="tren">-</strong></div>
            </div>
        </div>
    </div>

    <div class="card student-card mb-3">
        <div class="card-body">
            <h5 class="fw-bold mb-1">Perkembangan Nilai per Bulan</h5>
            <p class="text-muted small mb-3">Nilai merupakan rata-rata seluruh pengerjaan pada setiap bulan dalam semester yang dipilih.</p>
            <div id="chart-perkembangan"></div>
        </div>
    </div>

    <div class="card student-card mb-3">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Kemampuan Materi</h5>
            <div id="materi-list"></div>
        </div>
    </div>

    <div class="card student-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Pengerjaan Terbaru</h5>
                <a href="<?= base_url('riwayat'); ?>" class="small fw-semibold">Lihat Riwayat</a>
            </div>
            <div id="pengerjaan-list"></div>
        </div>
    </div>
</div>

<div id="perkembangan-empty" class="card student-card d-none">
    <div class="card-body text-center py-5">
        <i class="ri-line-chart-line fs-1 text-muted"></i>
        <h5 class="fw-bold mt-2">Belum Ada Data</h5>
        <p class="text-muted mb-3">Belum ada pengerjaan yang sesuai dengan filter yang dipilih.</p>
        <a href="<?= base_url('sesi'); ?>" class="btn btn-primary btn-touch">Lihat Sesi</a>
    </div>
</div>

<script>
    let chartPerkembangan = null;

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
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

                chartPerkembangan = new ApexCharts(document.querySelector('#chart-perkembangan'), {
                    chart: {
                        type: 'line',
                        height: 320,
                        toolbar: { show: false },
                        zoom: { enabled: false }
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
                    markers: { size: 5, hover: { size: 7 } },
                    dataLabels: { enabled: false },
                    legend: { position: 'top', horizontalAlign: 'right' },
                    noData: { text: 'Belum ada data pada semester ini' },
                    tooltip: {
                        shared: true,
                        intersect: false,
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

                            return html + '</div>';
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
