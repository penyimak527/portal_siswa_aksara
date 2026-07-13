<div class="card student-card mb-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-12 col-md-4">
    <select id="id_kelas" class="form-select rounded-3">
        <option value="">Pilih Kelas</option>
        <?php foreach (($kelas ?? []) as $k): ?>
            <option value="<?= $k['id']; ?>"><?= $k['nama_jenjang']; ?> <?= $k['nama_kelas']; ?></option>
        <?php endforeach; ?>
    </select>
</div>
            <div class="col-6 col-md-4">
                <select id="mapel" class="form-select rounded-3">
                    <option value="">Pilih Mata Pelajaran</option>
                    <?php foreach ($mapel as $m): ?>
                        <option value="<?= $m['id']; ?>"><?= $m['nama_mata_pelajaran']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-4">
                <select id="jenis" class="form-select rounded-3">
                    <option value="">Pilih Jenis Pengerjaan</option>
                    <option value="Bimbel">Bimbel</option>
                    <option value="Rumah">Rumah</option>
                </select>
            </div>
        </div>
        <button type="button" class="btn btn-primary w-100 btn-touch mt-2" onclick="riwayat_result()">Filter
            Riwayat</button>
    </div>
</div>

<div class="card student-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Daftar Riwayat</h5>
            <small class="text-muted" id="total_riwayat">0 data</small>
        </div>
        <div id="data_riwayat"></div>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center flex-wrap gap-2 mt-2">
            <ul class="pagination pagination-sm pagination-boxed mb-0" id="pagination"></ul>

            <div class="d-flex align-items-center gap-2">
                <label for="dt-length-0" class="mb-0">Tampilkan</label>
                <select class="form-select form-select-sm" id="dt-length-0">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entri</span>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        riwayat_result();
        $('#dt-length-0').on('change', function () {
            applyPagingRiwayat();
        });
        // $('#tahun_ajaran, #mapel, #jenis').on('change', function () {
        //     riwayat_result();
        // });
    });

    function escapeHtml(text) {
        return $('<div/>').text(text ?? '').html();
    }

    function formatTanggal(tanggal) {
        if (!tanggal) return '-';
        let str = String(tanggal);
        if (str.indexOf(' ') >= 0) {
            return str.split(' ')[0];
        }
        return str;
    }

    function riwayat_result() {
        $.ajax({
            url: '<?= base_url('riwayat/result'); ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                id_kelas: $('#id_kelas').val(),
                mapel: $('#mapel').val(),
                jenis: $('#jenis').val()
            },
            success: function (res) {
                if (res.result != 'true') {
                    $('#data_riwayat').html('<div class="alert alert-light border mb-0">Belum ada riwayat pengerjaan.</div>');
                    $('#total_riwayat').text('0 data');
                    $('#pagination').empty();
                    return;
                }
                render_riwayat(res.data || []);
            },
            error: function () {
                $('#data_riwayat').html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat riwayat.</div>');
                $('#total_riwayat').text('0 data');
                $('#pagination').empty();
            }
        });
    }

    function render_riwayat(rows) {
        let html = '';
        $('#total_riwayat').text(rows.length + ' data');

        if (!rows || rows.length == 0) {
            $('#data_riwayat').html('<div class="alert alert-light border mb-0">Belum ada riwayat pengerjaan.</div>');
            $('#pagination').empty();
            return;
        }

        rows.forEach(function (row, i) {
            let nilai = parseFloat(row.nilai_akhir || 0);
            html += `
                <div class="session-card riwayat-row">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div class="fw-bold">${i + 1}. ${escapeHtml(row.nama_sesi)}</div>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">${Math.round(nilai)}%</span>
                    </div>
                    <div class="small mb-3">
                    <div>Kelas: <b>${escapeHtml(row.nama_jenjang || '-')} ${escapeHtml(row.nama_kelas || '-')}</b></div>
                    <div>Mata Pelajaran: <b>${escapeHtml(row.nama_mata_pelajaran)}</b></div>
                    <div>Jenis Pengerjaan: <b>${escapeHtml(row.jenis_pengerjaan)}</b></div>
                        <div>Tanggal: <b>${escapeHtml(formatTanggal(row.waktu_selesai))}</b></div>
                        <div>Status: <b>${escapeHtml(row.status_pengerjaan)}</b></div>
                    </div>
                    <a href="<?= base_url('pengerjaan/hasil/'); ?>${row.id}" class="btn btn-outline-primary btn-touch w-100">Detail Hasil</a>
                </div>
            `;
        });

        $('#data_riwayat').html(html);
        applyPagingRiwayat();
    }
    function applyPagingRiwayat() {
        let jumlah = parseInt($('#dt-length-0').val()) || 10;
        paging($('#data_riwayat .riwayat-row'), jumlah);
    }

    function paging($selector, jumlah_tampil = 10) {
        $('#pagination').empty();

        if (!$selector || $selector.length === 0) {
            return;
        }

        window.tp = new Pagination('#pagination', {
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

</script>