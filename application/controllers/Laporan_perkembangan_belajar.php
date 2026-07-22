<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_perkembangan_belajar extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function download()
    {
        $session_siswa = $this->session->userdata('siswa') ?: [];

        if (($session_siswa['logged_in'] ?? null) == null) {
            redirect('/');
        }

        $id_siswa = (int) ($session_siswa['id_siswa'] ?? 0);
        $tahun_ajaran = trim((string) $this->input->post('tahun_ajaran', true));
        $id_kelas = (int) $this->input->post('id_kelas', true);
        $id_mata_pelajaran = (int) $this->input->post('id_mata_pelajaran', true);

        if ($id_siswa <= 0) {
            show_error('Sesi login siswa tidak ditemukan.', 401, 'Sesi Tidak Ditemukan');
            return;
        }

        if ($tahun_ajaran === '' || $id_kelas <= 0) {
            show_error('Tahun ajaran dan kelas wajib dipilih.', 400, 'Filter Laporan Tidak Lengkap');
            return;
        }

        $tanggal_sql = "STR_TO_DATE(ps.waktu_selesai, '%d-%m-%Y %H:%i:%s')";
        $nilai_sql = "CAST(NULLIF(ps.nilai_akhir, '') AS DECIMAL(10,2))";

        $where = [
            'ps.id_siswa = ?',
            'ps.tahun_ajaran = ?',
            'ps.id_kelas = ?',
            "ps.status_pengerjaan IN ('Selesai', 'Waktu Habis', 'Selesai karena timer habis')",
            "{$tanggal_sql} IS NOT NULL",
            "{$nilai_sql} IS NOT NULL"
        ];

        $params = [
            $id_siswa,
            $tahun_ajaran,
            $id_kelas
        ];

        if ($id_mata_pelajaran > 0) {
            $where[] = 'ss.id_mata_pelajaran = ?';
            $params[] = $id_mata_pelajaran;
        }

        $where_sql = implode(' AND ', $where);

        $siswa = $this->db->query("SELECT
                s.*,
                k.nama_kelas,
                j.nama_jenjang
            FROM siswa s
            LEFT JOIN kelas k ON k.id = ?
            LEFT JOIN jenjang j ON j.id = k.id_jenjang
            WHERE s.id = ?
            LIMIT 1", [$id_kelas, $id_siswa])->row_array();

        if (empty($siswa)) {
            show_error('Data siswa tidak ditemukan.', 404, 'Laporan Tidak Tersedia');
            return;
        }

        $pengerjaan = $this->db->query("SELECT
                ps.id,
                ps.jenis_pengerjaan,
                ROUND({$nilai_sql}, 2) AS nilai,
                DATE_FORMAT({$tanggal_sql}, '%Y-%m') AS periode,
                DATE_FORMAT({$tanggal_sql}, '%d-%m-%Y') AS tanggal,
                ss.nama_sesi,
                mp.nama_mata_pelajaran
            FROM siswa_pengerjaan ps
            INNER JOIN soal_sesi ss ON ss.id = ps.id_sesi_soal
            LEFT JOIN mata_pelajaran mp ON mp.id = ss.id_mata_pelajaran
            WHERE {$where_sql}
            ORDER BY {$tanggal_sql} ASC, ps.id ASC", $params)->result_array();

        if (empty($pengerjaan)) {
            show_error(
                'Belum ada data perkembangan untuk filter yang dipilih.',
                404,
                'Laporan Tidak Tersedia'
            );
            return;
        }

        $daftar_nilai = [];
        foreach ($pengerjaan as $row) {
            $daftar_nilai[] = (float) ($row['nilai'] ?? 0);
        }

        $nilai_awal = reset($daftar_nilai);
        $nilai_akhir = end($daftar_nilai);
        $selisih = $nilai_akhir - $nilai_awal;

        if (count($daftar_nilai) < 2) {
            $status_perkembangan = 'Belum cukup data';
        } elseif (abs($selisih) <= 2) {
            $status_perkembangan = 'Stabil';
        } elseif ($selisih > 0) {
            $status_perkembangan = 'Meningkat';
        } else {
            $status_perkembangan = 'Menurun';
        }

        $kelas = trim(
            ($siswa['nama_jenjang'] ?? '') . ' ' .
            ($siswa['nama_kelas'] ?? '')
        );

        $nama_mata_pelajaran = 'Semua Mata Pelajaran';
        if ($id_mata_pelajaran > 0) {
            $mapel = $this->db->query("SELECT nama_mata_pelajaran
                FROM mata_pelajaran
                WHERE id = ?
                LIMIT 1", [$id_mata_pelajaran])->row_array();

            $nama_mata_pelajaran = $mapel['nama_mata_pelajaran'] ?? '-';
        }

        $ringkasan = [
            'periode' => $tahun_ajaran,
            'kelas' => $kelas,
            'mata_pelajaran' => $nama_mata_pelajaran,
            'jumlah_sesi' => count($daftar_nilai),
            'rata_rata' => round(array_sum($daftar_nilai) / count($daftar_nilai), 2),
            'nilai_tertinggi' => round(max($daftar_nilai), 2),
            'nilai_terendah' => round(min($daftar_nilai), 2),
            'status_perkembangan' => $status_perkembangan
        ];

        $perkembangan_mapel = $this->db->query("SELECT
                mp.id,
                mp.nama_mata_pelajaran,
                COUNT(ps.id) AS jumlah_sesi,
                ROUND(AVG({$nilai_sql}), 2) AS rata_rata
            FROM siswa_pengerjaan ps
            INNER JOIN soal_sesi ss ON ss.id = ps.id_sesi_soal
            INNER JOIN mata_pelajaran mp ON mp.id = ss.id_mata_pelajaran
            WHERE {$where_sql}
            GROUP BY mp.id, mp.nama_mata_pelajaran
            ORDER BY mp.nama_mata_pelajaran ASC", $params)->result_array();

        foreach ($perkembangan_mapel as $key => $row) {
            $perkembangan_mapel[$key]['capaian'] = $this->capaian(
                (float) ($row['rata_rata'] ?? 0)
            );
        }

        $jawaban_source = "(
            SELECT id_pengerjaan, id_soal, nilai, 'Bimbel' AS jenis_pengerjaan
            FROM siswa_jawaban_bimbel

            UNION ALL

            SELECT id_pengerjaan, id_soal, nilai, 'Rumah' AS jenis_pengerjaan
            FROM siswa_jawaban_rumah
        )";

        $materi_where = $where;
        $materi_where[] = 'm.id IS NOT NULL';
        $materi_where_sql = implode(' AND ', $materi_where);

        $perkembangan_materi = $this->db->query("SELECT
                mp.nama_mata_pelajaran,
                m.id,
                m.nama_materi,
                COUNT(js.id_soal) AS jumlah_soal,
                ROUND(
                    (
                        SUM(COALESCE(CAST(js.nilai AS DECIMAL(10,2)), 0)) /
                        NULLIF(SUM(CAST(so.bobot_nilai AS DECIMAL(10,2))), 0)
                    ) * 100,
                    2
                ) AS hasil
            FROM {$jawaban_source} js
            INNER JOIN siswa_pengerjaan ps
                ON ps.id = js.id_pengerjaan
                AND ps.jenis_pengerjaan = js.jenis_pengerjaan
            INNER JOIN soal_sesi ss ON ss.id = ps.id_sesi_soal
            INNER JOIN mata_pelajaran mp ON mp.id = ss.id_mata_pelajaran
            INNER JOIN soal so
                ON so.id = js.id_soal
                AND so.status_hapus IS NULL
            INNER JOIN materi m ON m.id = so.id_materi
            WHERE {$materi_where_sql}
            GROUP BY
                mp.id,
                mp.nama_mata_pelajaran,
                m.id,
                m.nama_materi
            ORDER BY
                mp.nama_mata_pelajaran ASC,
                m.nama_materi ASC", $params)->result_array();

        foreach ($perkembangan_materi as $key => $row) {
            $hasil = max(0, min(100, (float) ($row['hasil'] ?? 0)));

            $perkembangan_materi[$key]['hasil'] = round($hasil, 2);
            $perkembangan_materi[$key]['capaian'] = $this->capaian($hasil);
        }

        $bulan_indonesia = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $kelompok_bulan = [];
        foreach ($pengerjaan as $row) {
            $periode = (string) ($row['periode'] ?? '');

            if ($periode === '') {
                continue;
            }

            if (!isset($kelompok_bulan[$periode])) {
                $kelompok_bulan[$periode] = [];
            }

            $kelompok_bulan[$periode][] = (float) ($row['nilai'] ?? 0);
        }

        $grafik_bulan = [];
        foreach ($kelompok_bulan as $periode => $nilai_bulan) {
            [$tahun, $bulan] = explode('-', $periode);

            $grafik_bulan[] = [
                'periode' => $periode,
                'label' => ($bulan_indonesia[$bulan] ?? $bulan) . ' ' . $tahun,
                'nilai' => round(array_sum($nilai_bulan) / count($nilai_bulan), 2),
                'jumlah_sesi' => count($nilai_bulan)
            ];
        }

        $data = [
            'siswa' => $siswa,
            'ringkasan' => $ringkasan,
            'perkembangan_mapel' => $perkembangan_mapel,
            'perkembangan_materi' => $perkembangan_materi,
            'grafik_bulan' => $grafik_bulan,
            'tahun_ajaran' => $tahun_ajaran,
            'semester' => $this->semester_laporan($grafik_bulan),
            'tanggal_cetak' => $this->tanggal_indonesia(date('Y-m-d'))
        ];

        $html = $this->load->view(
            'portal_siswa/laporan_perkembangan_belajar',
            $data,
            true
        );

        // Load Dompdf langsung dari library yang sudah tersedia di project.
        require_once APPPATH . 'libraries/dompdf/autoload.inc.php';

        $dompdf = new \Dompdf\Dompdf([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

$nama_siswa_file = preg_replace( '/[^A-Za-z0-9_\-]/', '_', $siswa['nama_siswa'] ?? 'Siswa');
$tahun_ajaran_file = str_replace('/', '-', $tahun_ajaran);
$filename = 'Laporan_perkembangan_belajar_' . $nama_siswa_file . '_' . $tahun_ajaran_file . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    private function capaian($nilai)
    {
        if ($nilai >= 80) {
            return 'Dikuasai';
        }

        if ($nilai >= 60) {
            return 'Cukup';
        }

        return 'Perlu Ditingkatkan';
    }

    private function semester_laporan($grafik_bulan)
    {
        $ada_ganjil = false;
        $ada_genap = false;

        foreach ($grafik_bulan as $row) {
            $periode = (string) ($row['periode'] ?? '');
            $bulan = (int) substr($periode, 5, 2);

            if ($bulan >= 7 && $bulan <= 12) {
                $ada_ganjil = true;
            }

            if ($bulan >= 1 && $bulan <= 6) {
                $ada_genap = true;
            }
        }

        if ($ada_ganjil && $ada_genap) {
            return 'Ganjil dan Genap';
        }

        return $ada_genap ? 'Genap' : 'Ganjil';
    }

    private function tanggal_indonesia($tanggal)
    {
        $bulan = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $timestamp = strtotime($tanggal);

        return date('d', $timestamp) . ' ' .
            $bulan[(int) date('n', $timestamp)] . ' ' .
            date('Y', $timestamp);
    }

}