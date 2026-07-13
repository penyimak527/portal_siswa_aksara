<?php
if ($this->session->userdata('admin')['level'] == 'Tentor') {
	?>
	<div class="card">
		<div class="card-header border-bottom border-dashed d-flex align-items-center justify-content-between">
			<h4 class="header-title">Data Kelas & Siswa</h4>
		</div>
		<div class="card-body">
			<?php
			foreach ($data_kelas as $dk):
				$id_kelas = $dk['id_kelas'];
				$res_siswa = $this->db->query("SELECT
																		 a.*
																		 FROM siswa a
																		 WHERE a.id_kelas = '$id_kelas'
																		")->result_array();
				?>
				<div class="col-md-12">
					<div class="card border border-secondary">
						<div class="card-body text-secondary">
							<h5 class="card-title text-secondary"><?php echo $dk['nama_jenjang'] ?>
								<?php echo $dk['nama_kelas'] ?>
							</h5>
							<div class="table-responsive">
								<table class="table mb-0 table-sm">
									<thead>
										<tr>
											<th>#</th>
											<th>NIS</th>
											<th>Siswa</th>
											<th>Alamat</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$no = 0;
										foreach ($res_siswa as $rs):
											$no++;
											?>
											<tr>
												<th scope="row"><?php echo $no; ?></th>
												<td><?php echo $rs['nis']; ?></td>
												<td><?php echo $rs['nama_siswa'] ?></td>
												<td><?php echo $rs['alamat'] ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
} else {
	?>
	<div class="row row-cols-1 row-cols-md-3 g-4">
		<div class="col">
			<div class="card">
				<div class="d-flex card-header justify-content-between align-items-center">
					<div>
						<h4 class="header-title"> Siswa Aktif</h4>
					</div>
				</div>
				<div class="card-body pt-0">
					<div class="d-flex align-items-end gap-2 justify-content-between">
						<div class="text-end flex-shrink-0">
							<div id="chart-one" data-colors="#ff5b5b,#F6F7FB"></div>
						</div>
						<div class="text-end">
							<h3 class="fw-semibold"><?= $total_siswa_aktif ?></h3>
							<p class="text-muted mb-0">Total Siswa</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card">
				<div class="d-flex card-header justify-content-between align-items-center">
					<div>
						<h4 class="header-title">Total Daftar Awal</h4>
					</div>
				</div>
				<div class="card-body pt-0">
					<div class="d-flex align-items-end gap-2 justify-content-between">
						<div class="text-end flex-shrink-0">
							<div id="chart-daftar" data-colors="#1208D2FF,#F6F7FB"></div>
						</div>
						<div class="text-end">
							<h3 class="fw-semibold">Rp. <?= number_format($daftar_awal, 0, ',', '.') ?></h3>
							<p class="text-muted mb-0">Daftar Awal Periode Tahun <?php echo $tahun_sekarang?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card">
				<div class="d-flex card-header justify-content-between align-items-center">
					<div>
						<h4 class="header-title">Total Daftar Ulang</h4>
					</div>
				</div>
				<div class="card-body pt-0">
					<div class="d-flex align-items-end gap-2 justify-content-between">
						<div class="text-end flex-shrink-0">
							<div id="chart-ulang" data-colors="#FF1971FF,#F6F7FB"></div>
						</div>
						<div class="text-end">
							<h3 class="fw-semibold">Rp. <?= number_format($daftar_ulang, 0, ',', '.') ?></h3>
							<p class="text-muted mb-0">Daftar Ulang Periode Tahun <?php echo $tahun_sekarang?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="card">
		<div class="card-header border-bottom border-dashed d-flex align-items-center justify-content-between">
			<h4 class="header-title">Dashboard Pembayaran Tagihan</h4>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-2">
					<div class="mb-3">
						<select id="filter_periode_bulan" name="periode_bulan" class="form-control"
							onclick="tagihan_pembayaran()">
							<!-- <option value="">Pilih Bulan</option> -->
							<?php
							$bulan = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
							$jlh_bln = count($bulan);
							$no = 0;
							for ($c = 0; $c < $jlh_bln; $c += 1) {
								$no++;
								$no_pas = sprintf("%02s", $no);
								$selected = ($no_pas == $bulan_sekarang) ? 'selected' : '';
								// echo '<option value="' . $no_pas . '" > ' . $bulan[$c] . '</option>';
								        echo "<option value='$no_pas' $selected>$bulan[$c]</option>";

							}
							?>
						</select>
					</div>
				</div>
				<div class="col-md-2">
					<div class="mb-3">
						<select id="filter_periode_tahun" name="periode_tahun" class="form-control"
							onclick="tagihan_pembayaran()">
							<!-- <option value="">Pilih Tahun</option> -->
							<?php
							$now = date('Y');
							for ($a = 2025; $a <= $now; $a++) {
								$periode_tahun_selected = ($a == $now) ? 'selected' : '';
								// echo '<option value="' . $a . '">' . $a . '</option>';
								echo "<option value='$a' $periode_tahun_selected>$a</option>";
							}
							?>
						</select>
					</div>
				</div>
			</div>
			<!-- isi -->
			<div class="row row-cols-1 row-cols-md-2 g-4">
				<div class="col">
					<div class="card border">
						<div class="d-flex card-header justify-content-between align-items-center">
							<div>
								<h4 class="header-title">Pembayaran Lunas</h4>
							</div>
						</div>
						<div class="card-body pt-0">
							<div class="d-flex align-items-end gap-2 justify-content-between">
								<div class="text-end flex-shrink-0">
									<div id="chart-three" data-colors="#ff5b5b,#F6F7FB"></div>
								</div>
								<div class="text-end">
									<h3 class="fw-semibold" id="sudah_lunas">Rp. 0
										<!-- Rp. <= number_format($total_lunas, 0, ',', '.') ?> -->
									</h3>
									<p class="text-muted mb-0">Total Lunas</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card border">
						<div class="d-flex card-header justify-content-between align-items-center">
							<div>
								<h4 class="header-title"> Pembayaran Belum Lunas</h4>
							</div>
						</div>
						<div class="card-body pt-0">
							<div class="d-flex align-items-end gap-2 justify-content-between">
								<div class="text-end flex-shrink-0">
									<div id="chart-five" data-colors="#CBD208FF,#F6F7FB"></div>
								</div>
								<div class="text-end">
									<h3 class="fw-semibold" id="belum_lunas">Rp. 0
										<!-- Rp. <= number_format($total_belum, 0, ',', '.') ?> -->
									</h3>
									<p class="text-muted mb-0">Total Belum Lunas</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- <div class="col border">
					<div class="card">
						<div class="d-flex card-header justify-content-between align-items-center">
							<div>
								<h4 class="header-title"> Pembayaran Cicilan</h4>
							</div>
						</div>
						<div class="card-body pt-0">
							<div class="d-flex align-items-end gap-2 justify-content-between">
								<div class="text-end flex-shrink-0">
									<div id="chart-four" data-colors="#280909FF,#F6F7FB"></div>
								</div>
								<div class="text-end">
									<h3 class="fw-semibold">Rp. <= number_format($total_cicilan, 0, ',' , '.' ) ?>
									</h3>
									<p class="text-muted mb-0">Total Cicilan</p>
								</div>
							</div>
						</div>
					</div>
				</div> -->
			</div>
		</div>
	</div>

	<div class="card">
		<div class="card-header border-bottom border-dashed d-flex align-items-center justify-content-between">
			<h4 class="header-title">Tagihan Jatuh Tempo</h4>
		</div>
		<div class="card-body">
			<div id="card-container">
				<?php
				$bulanIndo = [
					'Jan' => 'Januari',
					'Feb' => 'Februari',
					'Mar' => 'Maret',
					'Apr' => 'April',
					'May' => 'Mei',
					'Jun' => 'Juni',
					'Jul' => 'Juli',
					'Aug' => 'Agustus',
					'Sep' => 'September',
					'Oct' => 'Oktober',
					'Nov' => 'November',
					'Dec' => 'Desember',
				];

				foreach ($jatuh_tempo as $item):
					if ($item['status'] === 'Belum' || $item['status'] === 'Sudah Bayar'):

						// Nominal yang ditampilkan
						$nominal_bayar = ($item['status'] === 'Sudah Bayar')
							? (int) $item['nominal_bayar']
							: (int) $item['total_harga_pertemuan'];

						// Parsing tanggal (coba format d-m-Y dulu; fallback ke strtotime biasa)
						$dt = DateTime::createFromFormat('d-m-Y', $item['tanggal']);
						if (!$dt) {
							$dt = date_create($item['tanggal']);
						}

						$bulanInggris = $dt ? $dt->format('M') : date('M', strtotime($item['tanggal']));
						$tahun = $dt ? $dt->format('Y') : date('Y', strtotime($item['tanggal']));
						$bulan = $bulanIndo[$bulanInggris] ?? $bulanInggris;

						// Link tagihan
						$link_tagihan = base_url('admin/administrasi/tagihan_pembayaran/tagihan_online/' . $item['id_siswa']);

						// Format angka Indonesia
						$bayar_tagihan = number_format($nominal_bayar, 0, ',', '.');

						// Susun pesan WA
						$nama_siswa = $item['nama_siswa'] ?? '';
						$nama_kelas = $item['nama_kelas'] ?? '';
						$hp_wali_raw = $item['hp_wali'] ?? '';

						// Normalkan nomor WA menjadi digit saja (opsional)
						$phone = preg_replace('/\D+/', '', $hp_wali_raw);

						$text = "Haloo {$nama_siswa},\n\n"
							. "Izin mengingatkan, untuk tagihan bimbel di Aksara Course\n"
							. "Nama Siswa: {$nama_siswa}\n"
							. "Kelas: {$nama_kelas}\n"
							. "Bulan: {$bulan} {$tahun}\n"
							. "Biaya: Rp. {$bayar_tagihan}\n\n"
							. "Best Regards,\nAksara Course\n"
							. "Cek Nota Tagihan:\n{$link_tagihan}";

						// Encode untuk parameter URL
						$waText = rawurlencode($text);
						$link = "https://api.whatsapp.com/send?phone={$phone}&text={$waText}";
						?>
						<div class="card-mapel">
							<p class="keterangan-hari">
								<span>Bulan : <?= htmlspecialchars($bulan) ?> 			<?= htmlspecialchars($tahun) ?></span>
							</p>
							<div class="keterangan-mapel">
								<div class="keterangan-mapel-kiri">
									<h5 class="judul-mapel"><?= htmlspecialchars($nama_siswa) ?></h5>
									<p class="keterangan-jam-mapel">
										Nominal Bayar: Rp. <?= number_format($nominal_bayar, 0, ',', '.') ?>
									</p>
								</div>
								<div class="keterangan-mapel-kanan">
									<a href="<?= $link ?>" type="button" class="btn btn-sm btn-info" style="width:100%;">
										<i class="ri-whatsapp-fill me-1"></i> Kirim Tagihan
									</a>
								</div>
							</div>
						</div>
						<?php
					endif;
				endforeach;
				?>
			</div>
		</div>
	</div>
	<?php
}
?>
<script>
	function formatRupiah(angka) {
    return 'Rp. ' + new Intl.NumberFormat('id-ID').format(angka);
}

	function tagihan_pembayaran() {
		let bulan = $('select[name="periode_bulan"]').val();
		let tahun = $('select[name="periode_tahun"]').val();
		$.ajax({
			url: "<?= base_url() ?>dashboard/tagihan_pembayaran_card",
			data: { periode_bulan: bulan, periode_tahun: tahun },
			type: "POST",
			dataType: "JSON",
			success: function (data) {
			$('#sudah_lunas').text(formatRupiah(data.total_lunas));
			$('#belum_lunas').text(formatRupiah(data.total_belum));

			}
		})
	}
	document.addEventListener("DOMContentLoaded", function () {
		const defaultColors = ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"];

		function renderRadialChart(selector, seriesValue) {
			const dataColors = document.querySelector(selector).dataset.colors;
			const colors = dataColors ? dataColors.split(",") : defaultColors;

			const options = {
				series: [seriesValue],
				chart: {
					type: "radialBar",
					height: 81,
					width: 81,
					sparkline: {
						enabled: false
					}
				},
				plotOptions: {
					radialBar: {
						offsetY: 0,
						hollow: {
							margin: 0,
							size: "50%"
						},
						dataLabels: {
							name: {
								show: false
							},
							value: {
								offsetY: 5,
								fontSize: "14px",
								fontWeight: "600",
								formatter: function (val) {
									return val;
								}
							}
						}
					}
				},
				grid: {
					padding: {
						top: -18,
						bottom: -20,
						left: -20,
						right: -20
					}
				},
				colors: colors
			};

			new ApexCharts(document.querySelector(selector), options).render();
		}

		// renderRadialChart("#chart-one", <= $total_siswa_aktif ?>);
		// renderRadialChart("#chart-three", <= number_format($total_lunas, 0, ',', '.') ?>);
		// renderRadialChart("#chart-four", <= number_format($total_cicilan, 0, ',', '.') ?>);
		// renderRadialChart("#chart-five", <= number_format($total_belum, 0, ',', '.') ?>);
		// renderRadialChart("#chart-daftar", <= number_format($daftar_awal, 0, ',', '.') ?>);
		// renderRadialChart("#chart-ulang", <= number_format($daftar_ulang, 0, ',', '.') ?>);
		tagihan_pembayaran();
	});
</script>