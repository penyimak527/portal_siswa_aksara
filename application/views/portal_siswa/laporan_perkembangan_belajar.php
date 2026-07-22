<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perkembangan Belajar Siswa</title>
    <style>
        @page { margin: 18mm 15mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #000;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }
        .header { text-align: center; }
        .header .lembaga { font-size: 13px; font-weight: bold; }
        .header .judul { margin-top: 3px; font-size: 14px; font-weight: bold; }
        .header .periode, .header .tanggal { margin-top: 2px; }
        .separator { border-top: 1px solid #000; margin: 9px 0 12px; }
        .section { margin-bottom: 14px; page-break-inside: auto; }
        .section-title {
            margin: 0 0 5px;
            padding-bottom: 4px;
            border-bottom: 1px solid #000;
            font-size: 10px;
            font-weight: bold;
            page-break-after: avoid;
        }
        .identity { width: 100%; border-collapse: collapse; }
        .identity td { padding: 2px 0; vertical-align: top; }
        .identity .label { width: 125px; }
        .identity .colon { width: 12px; }
        table.report { width: 100%; border-collapse: collapse; }
        table.report thead { display: table-header-group; }
        table.report tr { page-break-inside: avoid; }
        table.report th, table.report td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
        }
        table.report th { text-align: center; font-weight: bold; }
        .center { text-align: center; }
        .right { text-align: right; }
        .indicator { width: 62%; }
        .chart-block { page-break-inside: avoid; }
        .chart-wrap {
            width: 100%;
            padding: 0;
            page-break-inside: avoid;
        }
        .chart-image {
            display: block;
            width: 100%;
            height: auto;
        }
        .approval {
            width: 100%;
            margin-top: 18px;
            page-break-inside: avoid;
        }
        .approval td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .approval-title {
            margin-top: 14px;
            font-size: 10px;
            font-weight: bold;
        }
        .signature-space {
            height: 54px;
        }
        .signature-line {
            display: inline-block;
            min-width: 165px;
            padding-top: 2px;
            border-top: 1px solid #000;
        }
        .empty { text-align: center; padding: 12px; }
    </style>
</head>
<body>
<?php
    $nama_siswa = $siswa['nama_siswa'] ?? ($siswa['nama'] ?? '-');
    $nis = $siswa['nis'] ?? ($siswa['nisn'] ?? '-');
    $kelas = $ringkasan['kelas'] ?? ($siswa['nama_kelas'] ?? '-');
    $mapel = $ringkasan['mata_pelajaran'] ?? 'Semua Mata Pelajaran';

    $format_nilai = function ($nilai) {
        $nilai = (float) $nilai;
        return ((float) ((int) $nilai) === $nilai) ? (string) ((int) $nilai) : number_format($nilai, 2, ',', '.');
    };
?>

<div class="header">
    <div class="lembaga">AKSARA</div>
    <div class="judul">LAPORAN PERKEMBANGAN BELAJAR SISWA</div>
    <div class="periode">Semester <?= htmlspecialchars($semester, ENT_QUOTES, 'UTF-8'); ?> Tahun Ajaran <?= htmlspecialchars($tahun_ajaran, ENT_QUOTES, 'UTF-8'); ?></div>
    <div class="tanggal">Tanggal Cetak: <?= htmlspecialchars($tanggal_cetak, ENT_QUOTES, 'UTF-8'); ?></div>
</div>
<div class="separator"></div>

<div class="section">
    <div class="section-title">IDENTITAS SISWA</div>
    <table class="identity">
        <tr><td class="label">Nama Siswa</td><td class="colon">:</td><td><?= htmlspecialchars($nama_siswa, ENT_QUOTES, 'UTF-8'); ?></td></tr>
        <tr><td class="label">NIS</td><td class="colon">:</td><td><?= htmlspecialchars($nis, ENT_QUOTES, 'UTF-8'); ?></td></tr>
        <tr><td class="label">Kelas</td><td class="colon">:</td><td><?= htmlspecialchars($kelas, ENT_QUOTES, 'UTF-8'); ?></td></tr>
        <tr><td class="label">Semester</td><td class="colon">:</td><td><?= htmlspecialchars($semester, ENT_QUOTES, 'UTF-8'); ?></td></tr>
        <tr><td class="label">Tahun Ajaran</td><td class="colon">:</td><td><?= htmlspecialchars($tahun_ajaran, ENT_QUOTES, 'UTF-8'); ?></td></tr>
        <tr><td class="label">Mata Pelajaran</td><td class="colon">:</td><td><?= htmlspecialchars($mapel, ENT_QUOTES, 'UTF-8'); ?></td></tr>
    </table>
</div>

<div class="section">
    <div class="section-title">RINGKASAN PERKEMBANGAN</div>
    <table class="report">
        <thead><tr><th style="width:35px">No</th><th>Komponen</th><th style="width:180px">Hasil</th></tr></thead>
        <tbody>
            <tr><td class="center">1</td><td>Jumlah Sesi Dikerjakan</td><td><?= (int) ($ringkasan['jumlah_sesi'] ?? 0); ?> sesi</td></tr>
            <tr><td class="center">2</td><td>Rata-rata Hasil Belajar</td><td><?= $format_nilai($ringkasan['rata_rata'] ?? 0); ?>%</td></tr>
            <tr><td class="center">3</td><td>Nilai Tertinggi</td><td><?= $format_nilai($ringkasan['nilai_tertinggi'] ?? 0); ?>%</td></tr>
            <tr><td class="center">4</td><td>Nilai Terendah</td><td><?= $format_nilai($ringkasan['nilai_terendah'] ?? 0); ?>%</td></tr>
            <tr><td class="center">5</td><td>Status Perkembangan</td><td><?= htmlspecialchars($ringkasan['status_perkembangan'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td></tr>
        </tbody>
    </table>
</div>

<div class="section">
    <div class="section-title">PERKEMBANGAN PER MATA PELAJARAN</div>
    <table class="report">
        <thead><tr><th style="width:35px">No</th><th>Mata Pelajaran</th><th style="width:85px">Jumlah Sesi</th><th style="width:80px">Rata-rata</th><th style="width:120px">Capaian</th></tr></thead>
        <tbody>
        <?php if (empty($perkembangan_mapel)): ?>
            <tr><td colspan="5" class="empty">Tidak ada data mata pelajaran.</td></tr>
        <?php else: ?>
            <?php foreach ($perkembangan_mapel as $index => $row): ?>
                <tr>
                    <td class="center"><?= $index + 1; ?></td>
                    <td><?= htmlspecialchars($row['nama_mata_pelajaran'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="center"><?= (int) ($row['jumlah_sesi'] ?? 0); ?></td>
                    <td class="center"><?= $format_nilai($row['rata_rata'] ?? 0); ?>%</td>
                    <td><?= htmlspecialchars($row['capaian'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="section">
    <div class="section-title">INDIKATOR CAPAIAN MATA PELAJARAN</div>
    <table class="report indicator">
        <thead><tr><th>Rentang Nilai</th><th>Capaian</th></tr></thead>
        <tbody>
            <tr><td class="center">80% - 100%</td><td>Dikuasai</td></tr>
            <tr><td class="center">60% - 79%</td><td>Cukup</td></tr>
            <tr><td class="center">0% - 59%</td><td>Perlu Ditingkatkan</td></tr>
        </tbody>
    </table>
</div>

<div class="section">
    <div class="section-title">PERKEMBANGAN MATERI</div>
    <table class="report">
        <thead><tr><th style="width:35px">No</th><th style="width:150px">Mata Pelajaran</th><th>Materi</th><th style="width:70px">Hasil</th><th style="width:120px">Capaian</th></tr></thead>
        <tbody>
        <?php if (empty($perkembangan_materi)): ?>
            <tr><td colspan="5" class="empty">Tidak ada data materi.</td></tr>
        <?php else: ?>
            <?php foreach ($perkembangan_materi as $index => $row): ?>
                <tr>
                    <td class="center"><?= $index + 1; ?></td>
                    <td><?= htmlspecialchars($row['nama_mata_pelajaran'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['nama_materi'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="center"><?= $format_nilai($row['hasil'] ?? 0); ?>%</td>
                    <td><?= htmlspecialchars($row['capaian'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="section">
    <div class="section-title">INDIKATOR CAPAIAN MATERI</div>
    <table class="report indicator">
        <thead><tr><th>Rentang Nilai</th><th>Capaian</th></tr></thead>
        <tbody>
            <tr><td class="center">80% - 100%</td><td>Dikuasai</td></tr>
            <tr><td class="center">60% - 79%</td><td>Cukup</td></tr>
            <tr><td class="center">0% - 59%</td><td>Perlu Ditingkatkan</td></tr>
        </tbody>
    </table>
</div>

<div class="section chart-block">
    <div class="section-title">GRAFIK PERKEMBANGAN NILAI</div>

    <?php if (empty($grafik_bulan)): ?>
        <div class="empty">Tidak ada data grafik.</div>
    <?php else: ?>
        <?php
            /*
             * Grafik dibuat sebagai SVG lalu dipasang melalui data URI.
             * Cara ini lebih stabil pada Dompdf dibandingkan SVG inline.
             */
            $width = 720;
            $height = 245;
            $left = 42;
            $right = 15;
            $top = 14;
            $bottom = 42;

            $plot_width = $width - $left - $right;
            $plot_height = $height - $top - $bottom;
            $jumlah = count($grafik_bulan);
            $step_x = $jumlah > 1 ? $plot_width / ($jumlah - 1) : 0;

            $svg = '';
            $svg .= '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">';
            $svg .= '<rect x="0" y="0" width="' . $width . '" height="' . $height . '" fill="#ffffff"/>';

            // Label dan garis sumbu Y.
            for ($nilai_y = 0; $nilai_y <= 100; $nilai_y += 20) {
                $y = $top + $plot_height - (($nilai_y / 100) * $plot_height);

                $svg .= '<text x="' . ($left - 10) . '" y="' . ($y + 3) . '" font-family="DejaVu Sans" font-size="9" text-anchor="end" fill="#000">'
                    . $nilai_y
                    . '</text>';
            }

            // Sumbu utama.
            $svg .= '<line x1="' . $left . '" y1="' . $top . '" x2="' . $left . '" y2="' . ($top + $plot_height) . '" stroke="#000" stroke-width="1"/>';
            $svg .= '<line x1="' . $left . '" y1="' . ($top + $plot_height) . '" x2="' . ($width - $right) . '" y2="' . ($top + $plot_height) . '" stroke="#000" stroke-width="1"/>';

            $points = [];

            foreach ($grafik_bulan as $index => $item) {
                $x = $jumlah > 1
                    ? $left + ($index * $step_x)
                    : $left + ($plot_width / 2);

                $nilai = max(0, min(100, (float) ($item['nilai'] ?? 0)));
                $y = $top + $plot_height - (($nilai / 100) * $plot_height);

                $points[] = round($x, 2) . ',' . round($y, 2);
            }

            if (count($points) > 1) {
                $svg .= '<polyline points="' . implode(' ', $points) . '" fill="none" stroke="#000" stroke-width="1.4"/>';
            }

            foreach ($grafik_bulan as $index => $item) {
                $x = $jumlah > 1
                    ? $left + ($index * $step_x)
                    : $left + ($plot_width / 2);

                $nilai = max(0, min(100, (float) ($item['nilai'] ?? 0)));
                $y = $top + $plot_height - (($nilai / 100) * $plot_height);

                $label = htmlspecialchars((string) ($item['label'] ?? '-'), ENT_QUOTES, 'UTF-8');

                // Ambil hanya nama bulan agar mendekati contoh.
                $bagian_label = explode(' ', $label);
                $label_bulan = $bagian_label[0] ?? $label;

                $svg .= '<circle cx="' . round($x, 2) . '" cy="' . round($y, 2) . '" r="3.2" fill="#000"/>';
                $svg .= '<text x="' . round($x, 2) . '" y="' . ($top + $plot_height + 20) . '" font-family="DejaVu Sans" font-size="8" text-anchor="middle" fill="#000">'
                    . $label_bulan
                    . '</text>';
            }

            $svg .= '</svg>';

            $chart_src = 'data:image/svg+xml;base64,' . base64_encode($svg);
        ?>

        <div class="chart-wrap">
            <img
                src="<?= $chart_src; ?>"
                alt="Grafik Perkembangan Nilai"
                class="chart-image"
            >
        </div>
    <?php endif; ?>
</div>

<div class="approval-title">PENGESAHAN</div>

<table class="approval">
    <tr>
        <td>Tentor Aksara</td>
        <td>Orang Tua / Wali</td>
    </tr>
    <tr>
        <td class="signature-space"></td>
        <td class="signature-space"></td>
    </tr>
    <tr>
        <td>
            <span class="signature-line">( Nama Terang )</span>
        </td>
        <td>
            <span class="signature-line">( Nama Terang )</span>
        </td>
    </tr>
</table>

</body>
</html>