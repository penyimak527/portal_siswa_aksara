<?= $this->session->flashdata('message'); ?>

<div class="card student-card mb-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-6">
                <input type="text" id="search" class="form-control rounded-3" placeholder="Cari sesi ...">
            </div>
            <div class="col-6">
                <select id="mapel" class="form-select rounded-3">
                    <option value="">Pilih Mata Pelajaran</option>
                    <?php foreach ($mapel as $m): ?>
                        <option value="<?= $m['id']; ?>"><?= $m['nama_mata_pelajaran']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="button" class="btn btn-primary w-100 btn-touch mt-2" onclick="sesi_result()">Cari /
            Filter</button>
    </div>
</div>

<div class="card student-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Daftar Sesi</h5>
            <small class="text-muted" id="total_sesi">0 sesi</small>
        </div>
        <div id="data_sesi"></div>
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
    let adaTunggakan = <?= !empty($ada_tunggakan) ? 'true' : 'false'; ?>;

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
                    Silahkan hubungi admin bimbel untuk informasi lebih lanjut
                </div>
            `,
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

    $(document).ready(function () {
        sesi_result();
        $('#search').on('keyup', function () {
            sesi_result();
        });
        $('#dt-length-0').on('change', function () {
            const jumlah = parseInt($(this).val()) || 10;
            paging($('#data_sesi .sesi-row'), jumlah);
        });
        // $('#mapel').on('change', function () {
        //     sesi_result();
        // });
    });

    function escapeHtml(text) {
        return $('<div/>').text(text ?? '').html();
    }

    function sesi_result() {
        $.ajax({
            url: '<?= base_url('sesi/result'); ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                search: $('#search').val(),
                mapel: $('#mapel').val()
            },
            success: function (res) {
                if (res.result != 'true') {
                    $('#data_sesi').html('<div class="alert alert-light border mb-0">Data sesi tidak bisa dimuat.</div>');
                    $('#total_sesi').text('0 sesi');
                    $('#pagination').empty();
                    return;
                }

                adaTunggakan = res.ada_tunggakan == 'true';
                render_sesi(res.data || []);
            },
            error: function () {
                $('#data_sesi').html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat sesi.</div>');
                $('#total_sesi').text('0 sesi');
                $('#pagination').empty();
            }
        });
    }

    function render_sesi(rows) {
        let html = '';
        $('#total_sesi').text(rows.length + ' sesi');

        if (!rows || rows.length == 0) {
            $('#data_sesi').html('<div class="alert alert-light border mb-0">Tidak ada sesi yang bisa dikerjakan.</div>');
            $('#pagination').empty();
            return;
        }

        rows.forEach(function (row, i) {
            let tombol = '';
            if (adaTunggakan) {
                tombol = `<button type="button" class="btn btn-secondary btn-touch w-100" onclick="cekAksesSesi('${row.id}')">Terkunci</button>`;
            } else {
                // let teksTombol = row.jenis_pengerjaan === 'Rumah' ? 'Kerjakan Latihan Rumah' : 'Mulai Kerjakan';
                tombol = `<button type="button" class="btn btn-primary btn-touch w-100" onclick="cekAksesSesi('${row.id}')">Mulai Kerjakan</button>`;
            }
            // <span class="badge bg-primary-subtle text-primary rounded-pill">${escapeHtml(row.label_pengerjaan || row.jenis_pengerjaan)}</span>
            html += `
                <div class="session-card sesi-row">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <div class="fw-bold">${i + 1}. ${escapeHtml(row.nama_sesi)}</div>
                            <span class="badge bg-success-subtle text-success mt-1">Bisa dikerjakan</span>
                        </div>
                        
                    </div>
                    <div class="small mb-3">
                        <div>Mata Pelajaran: <b>${escapeHtml(row.nama_mata_pelajaran)}</b></div>
                        <div>Kategori: <b>${escapeHtml(row.nama_kategori_soal)}</b></div>
                        <div>Jadwal: <b>${escapeHtml(row.tanggal_mulai)}, ${escapeHtml(row.jam_mulai)} - ${escapeHtml(row.jam_selesai)}</b></div>
                        <div>Durasi Timer: <b>${escapeHtml(row.durasi_timer)} menit</b></div>
                        <div>Jumlah Soal: <b>${escapeHtml(row.jumlah_soal)}</b></div>
                    </div>
                    ${tombol}
                </div>
            `;
        });

        $('#data_sesi').html(html);
        let jumlah_awal = parseInt($('#dt-length-0').val()) || 10;
        paging($('#data_sesi .sesi-row'), jumlah_awal);
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