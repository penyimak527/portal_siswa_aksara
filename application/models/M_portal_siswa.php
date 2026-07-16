<?php
class M_portal_siswa extends CI_Model
{
    private function session_siswa()
    {
        return $this->session->userdata('siswa') ?: [];
    }

    public function siswa()
    {
        $sess = $this->session_siswa();
        $id_siswa = $sess['id_siswa'] ?? 0;

        $sql = "SELECT
                    s.*,
                    k.nama_kelas
                FROM siswa s
                LEFT JOIN kelas k ON k.id = s.id_kelas
                WHERE s.id = ?
                LIMIT 1";
        return $this->db->query($sql, [$id_siswa])->row_array();
    }

    private function tabel_jawaban($jenis_pengerjaan)
    {
        return strtolower((string) $jenis_pengerjaan) === 'rumah'
            ? 'siswa_jawaban_rumah'
            : 'siswa_jawaban_bimbel';
    }

    private function sesi_masuk_jadwal($sesi)
    {
        $mulai = strtotime(str_replace('/', '-', $sesi['tanggal_mulai']) . ' ' . $sesi['jam_mulai']);
        $selesai = strtotime(str_replace('/', '-', $sesi['tanggal_selesai']) . ' ' . $sesi['jam_selesai']);
        $now = time();

        return $now >= $mulai && $now <= $selesai;
    }

    private function status_attempt_sesi($id_sesi, $id_siswa = null)
    {
        $id_siswa = $id_siswa ?: $this->current_id_siswa();

        $rows = $this->db->query("
        SELECT id, jenis_pengerjaan, status_pengerjaan
        FROM siswa_pengerjaan
        WHERE id_sesi_soal = ?
        AND id_siswa = ?
        AND status_pengerjaan IN ('Proses','Selesai','Waktu Habis')
        ORDER BY id ASC
    ", [$id_sesi, $id_siswa])->result_array();

        $has_bimbel = false;
        $has_rumah = false;
        $proses = null;

        foreach ($rows as $row) {
            if ($row['status_pengerjaan'] == 'Proses') {
                $proses = $row;
            }

            if ($row['jenis_pengerjaan'] == 'Bimbel') {
                $has_bimbel = true;
            }

            if ($row['jenis_pengerjaan'] == 'Rumah') {
                $has_rumah = true;
            }
        }

        if ($proses) {
            return [
                'boleh' => true,
                'lanjut' => true,
                'id_pengerjaan' => $proses['id'],
                'jenis_pengerjaan' => $proses['jenis_pengerjaan'],
                'urutan' => $proses['jenis_pengerjaan'] == 'Rumah' ? 2 : 1,
                'message' => 'Melanjutkan pengerjaan.'
            ];
        }

        if (!$has_bimbel) {
            return [
                'boleh' => true,
                'lanjut' => false,
                'id_pengerjaan' => null,
                'jenis_pengerjaan' => 'Bimbel',
                'urutan' => 1,
                'message' => 'Pengerjaan pertama.'
            ];
        }

        if (!$has_rumah) {
            return [
                'boleh' => true,
                'lanjut' => false,
                'id_pengerjaan' => null,
                'jenis_pengerjaan' => 'Rumah',
                'urutan' => 2,
                'message' => 'Pengerjaan kedua.'
            ];
        }

        return [
            'boleh' => false,
            'lanjut' => false,
            'id_pengerjaan' => null,
            'jenis_pengerjaan' => '',
            'urutan' => 2,
            'message' => 'Sesi ini sudah dikerjakan maksimal 2 kali.'
        ];
    }

    public function tahun_ajaran_aktif()
    {
        $row = $this->db->query("SELECT tahun_ajaran FROM soal_sesi WHERE status_hapus IS NULL ORDER BY id DESC LIMIT 1")->row_array();
        if (!empty($row['tahun_ajaran'])) {
            return $row['tahun_ajaran'];
        }

        $tahun = (int) date('Y');
        $bulan = (int) date('m');
        return $bulan >= 7 ? $tahun . '/' . ($tahun + 1) : ($tahun - 1) . '/' . $tahun;
    }
    public function kelas_riwayat_result()
    {
        $id_siswa = $this->current_id_siswa();

        return $this->db->query("
        SELECT DISTINCT 
            k.id,
            k.nama_kelas,
            j.nama_jenjang
        FROM siswa_pengerjaan ps
        LEFT JOIN kelas k ON k.id = ps.id_kelas
        INNER JOIN jenjang j ON k.id_jenjang = j.id
        WHERE ps.id_siswa = ?
        AND ps.id_kelas IS NOT NULL
        ORDER BY k.nama_kelas ASC
    ", [$id_siswa])->result_array();
    }

    private function current_id_siswa()
    {
        $sess = $this->session_siswa();
        return (int) ($sess['id_siswa'] ?? 0);
    }

    private function current_id_kelas()
    {
        $sess = $this->session_siswa();
        return (int) ($sess['id_kelas'] ?? 0);
    }

    public function mapel_result()
    {
        return $this->db->query("SELECT id, nama_mata_pelajaran FROM mata_pelajaran WHERE status_aktif = '1' ORDER BY nama_mata_pelajaran ASC")->result_array();
    }

    public function ada_tunggakan_bulan_lalu($id_siswa = null)
    {
        $id_siswa = $id_siswa ?: $this->current_id_siswa();
        $bulan = (int) date('m');
        $tahun = (int) date('Y');

        $sql = "SELECT COUNT(*) AS total
                FROM pembayaran p
                INNER JOIN pendaftaran_paket pp ON pp.id = p.id_pendaftaran_paket
                INNER JOIN siswa s ON s.id = pp.id_siswa
                WHERE pp.id_siswa = ?
                AND pp.status_aktif = '1'
                AND COALESCE(p.status, '') <> 'Lunas'
                AND p.periode_bulan IS NOT NULL
                AND p.periode_bulan != ''
                AND p.periode_tahun IS NOT NULL
                AND p.periode_tahun != ''
                AND (
                    CAST(p.periode_tahun AS UNSIGNED) < ?
                    OR (
                        CAST(p.periode_tahun AS UNSIGNED) = ?
                        AND CAST(p.periode_bulan AS UNSIGNED) <= ?
                    )
                )";
        $row = $this->db->query($sql, [$id_siswa, $tahun, $tahun, $bulan])->row_array();
        return ((int) ($row['total'] ?? 0)) > 0;
    }

    public function cek_akses()
    {
        $id_sesi = (int) $this->input->post('id_sesi');

        if ($id_sesi <= 0) {
            return [
                'status' => false,
                'result' => 'false',
                'ada_tunggakan' => 'false',
                'message' => 'Sesi soal tidak ditemukan.'
            ];
        }

        if ($this->ada_tunggakan_bulan_lalu()) {
            return [
                'status' => false,
                'result' => 'false',
                'ada_tunggakan' => 'true',
                'message' => 'Maaf, sesi soal baru belum dapat diakses karena masih terdapat pembayaran yang belum diselesaikan. Silahkan hubungi admin bimbel untuk informasi lebih lanjut.'
            ];
        }

        if ($this->siswa_sudah_mengerjakan($id_sesi)) {
            return [
                'status' => false,
                'result' => 'false',
                'ada_tunggakan' => 'false',
                'message' => 'Sesi ini sudah dikerjakan maksimal 2 kali.'
            ];
        }

        $sesi = $this->sesi_detail($id_sesi);
        if (!$sesi) {
            return [
                'status' => false,
                'result' => 'false',
                'ada_tunggakan' => 'false',
                'message' => 'Sesi soal tidak ditemukan atau belum dapat dikerjakan.'
            ];
        }

        return [
            'status' => true,
            'result' => 'true',
            'ada_tunggakan' => 'false',
            'message' => 'Sesi dapat diakses.',
            'redirect' => base_url('sesi/konfirmasi/' . $id_sesi)
        ];
    }

    private function filter_sesi_where(&$where, &$params)
    {
        $search = trim($this->input->get_post('search'));
        $mapel = trim($this->input->get_post('mapel'));

        if ($search != '') {
            $where[] = "(ss.nama_sesi LIKE ? OR mp.nama_mata_pelajaran LIKE ? OR ks.nama_kategori_soal LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($mapel != '') {
            $where[] = "ss.id_mata_pelajaran = ?";
            $params[] = $mapel;
        }
    }

    public function sesi_tersedia($limit = null)
    {
        $id_siswa = $this->current_id_siswa();
        $id_kelas = $this->current_id_kelas();

        $where = [
            "ss.status_hapus IS NULL",
            "ssk.id_kelas = ?"
        ];

        $params = [$id_kelas];
        $this->filter_sesi_where($where, $params);

        $sql = "SELECT
                ss.*,
                mp.nama_mata_pelajaran,
                ks.nama_kategori_soal,
                p.nama_pegawai AS nama_guru,
                COUNT(DISTINCT so.id) AS jumlah_soal
            FROM soal_sesi ss
            INNER JOIN soal_sesi_kelas ssk ON ssk.id_sesi_soal = ss.id
            LEFT JOIN mata_pelajaran mp ON mp.id = ss.id_mata_pelajaran
            LEFT JOIN soal_kategori ks ON ks.id = ss.id_kategori_soal
            LEFT JOIN pegawai p ON p.id = ss.id_guru_pengampu
            LEFT JOIN soal so ON so.id_naskah_soal = ss.id_naskah_soal 
                AND so.status_aktif = '1' 
                AND so.status_hapus IS NULL
            WHERE " . implode(' AND ', $where) . "
            GROUP BY ss.id
            ORDER BY STR_TO_DATE(CONCAT(ss.tanggal_mulai, ' ', ss.jam_mulai), '%d-%m-%Y %H:%i') ASC";

        $rows = $this->db->query($sql, $params)->result_array();

        $data = [];

        foreach ($rows as $row) {
            $attempt = $this->status_attempt_sesi($row['id'], $id_siswa);

            if (!$attempt['boleh']) {
                continue;
            }

            // Bimbel wajib aktif dan wajib ikut jadwal.
// Kalau sesi nonaktif / jadwal habis dan belum Bimbel, jangan tampil.
            if ($attempt['jenis_pengerjaan'] == 'Bimbel' && !$attempt['lanjut']) {
                if ((string) ($row['status_aktif'] ?? '0') !== '1') {
                    continue;
                }

                if (!$this->sesi_masuk_jadwal($row)) {
                    continue;
                }
            }

            // Rumah boleh walaupun jadwal sudah habis, asalkan sesi masih aktif.
            $row['jenis_pengerjaan'] = $attempt['jenis_pengerjaan'];
            $row['urutan_pengerjaan'] = $attempt['urutan'];
            $row['id_pengerjaan_proses'] = $attempt['id_pengerjaan'];
            $row['label_pengerjaan'] = $attempt['jenis_pengerjaan'] == 'Rumah'
                ? 'Latihan Rumah'
                : 'Pengerjaan Bimbel';

            $data[] = $row;

            if ($limit !== null && count($data) >= (int) $limit) {
                break;
            }
        }

        return $data;
    }

    public function sesi_detail($id_sesi)
    {
        $id_kelas = $this->current_id_kelas();

        $sql = "SELECT
                ss.*,
                mp.nama_mata_pelajaran,
                ks.nama_kategori_soal,
                p.nama_pegawai AS nama_guru,
                COUNT(DISTINCT so.id) AS jumlah_soal
            FROM soal_sesi ss
            INNER JOIN soal_sesi_kelas ssk ON ssk.id_sesi_soal = ss.id
            LEFT JOIN mata_pelajaran mp ON mp.id = ss.id_mata_pelajaran
            LEFT JOIN soal_kategori ks ON ks.id = ss.id_kategori_soal
            LEFT JOIN pegawai p ON p.id = ss.id_guru_pengampu
            LEFT JOIN soal so ON so.id_naskah_soal = ss.id_naskah_soal 
                AND so.status_aktif = '1' 
                AND so.status_hapus IS NULL
            WHERE ss.id = ?
            AND ssk.id_kelas = ?
            AND ss.status_hapus IS NULL
            GROUP BY ss.id
            LIMIT 1";

        $row = $this->db->query($sql, [$id_sesi, $id_kelas])->row_array();

        if (!$row) {
            return null;
        }

        $attempt = $this->status_attempt_sesi($id_sesi);
        if (!$attempt['boleh']) {
            return null;
        }

        if ($attempt['jenis_pengerjaan'] == 'Bimbel' && !$attempt['lanjut']) {
            if ((string) ($row['status_aktif'] ?? '0') !== '1') {
                return null;
            }

            if (!$this->sesi_masuk_jadwal($row)) {
                return null;
            }
        }

        $row['jenis_pengerjaan'] = $attempt['jenis_pengerjaan'];
        $row['urutan_pengerjaan'] = $attempt['urutan'];
        $row['id_pengerjaan_proses'] = $attempt['id_pengerjaan'];
        $row['label_pengerjaan'] = $attempt['jenis_pengerjaan'] == 'Rumah' ? 'Latihan Rumah' : 'Pengerjaan Bimbel';
        return $row;
    }

    public function siswa_sudah_mengerjakan($id_sesi)
    {
        $attempt = $this->status_attempt_sesi($id_sesi);
        return !$attempt['boleh'];
    }
    private function materi_dashboard_rows($id_mata_pelajaran = '')
    {
        $id_siswa = $this->current_id_siswa();

        $where = [
            "ps.id_siswa = ?",
            "ps.status_pengerjaan IN ('Selesai','Waktu Habis')",
            "m.id IS NOT NULL"
        ];
        $params = [$id_siswa];

        if ($id_mata_pelajaran !== '' && $id_mata_pelajaran !== null) {
            $where[] = "ss.id_mata_pelajaran = ?";
            $params[] = $id_mata_pelajaran;
        }

        $sql = "SELECT
                    m.id AS id_materi,
                    m.nama_materi,
                    ss.id_mata_pelajaran,
                    COALESCE(mp.nama_mata_pelajaran, '-') AS nama_mata_pelajaran,
                    ROUND((SUM(COALESCE(CAST(js.nilai AS DECIMAL(10,2)), 0)) /
                        NULLIF(SUM(COALESCE(CAST(so.bobot_nilai AS DECIMAL(10,2)), 0)), 0)) * 100, 0) AS persen,
                    ROUND(SUM(COALESCE(CAST(js.nilai AS DECIMAL(10,2)), 0)), 2) AS nilai,
                    ROUND(SUM(COALESCE(CAST(so.bobot_nilai AS DECIMAL(10,2)), 0)), 2) AS bobot
                FROM (
                    SELECT id_pengerjaan, id_soal, nilai
                    FROM siswa_jawaban_bimbel

                    UNION ALL

                    SELECT id_pengerjaan, id_soal, nilai
                    FROM siswa_jawaban_rumah
                ) js
                INNER JOIN siswa_pengerjaan ps ON ps.id = js.id_pengerjaan
                INNER JOIN soal_sesi ss ON ss.id = ps.id_sesi_soal
                INNER JOIN soal so ON so.id = js.id_soal AND so.status_hapus IS NULL
                LEFT JOIN materi m ON m.id = so.id_materi
                LEFT JOIN mata_pelajaran mp ON mp.id = ss.id_mata_pelajaran
                WHERE " . implode(' AND ', $where) . "
                GROUP BY m.id, m.nama_materi, ss.id_mata_pelajaran, mp.nama_mata_pelajaran
                ORDER BY persen ASC, m.nama_materi ASC";

        $rows = $this->db->query($sql, $params)->result_array();

        foreach ($rows as &$row) {
            $persen = (float) ($row['persen'] ?? 0);
            $persen = max(0, min(100, $persen));
            $row['persen'] = round($persen, 0);
            $row['status_materi'] = $persen < 70 ? 'Perlu Ditingkatkan' : 'Dikuasai';
        }
        unset($row);

        return $rows;
    }

     public function ringkasan_dashboard()
    {
        $id_siswa = $this->current_id_siswa();
        $sesi_tersedia = count($this->sesi_tersedia());

        $row = $this->db->query("SELECT
                COUNT(*) AS sesi_selesai,
                COALESCE(AVG(CAST(nilai_akhir AS DECIMAL(10,2))), 0) AS rata_nilai
            FROM siswa_pengerjaan
            WHERE id_siswa = ?
            AND status_pengerjaan IN ('Selesai','Waktu Habis')", [$id_siswa])->row_array();

        $materi = $this->materi_dashboard_rows();
        $materi_lemah = 0;
        $materi_dikuasai = 0;

        foreach ($materi as $m) {
            if ((float) ($m['persen'] ?? 0) < 70) {
                $materi_lemah++;
            } else {
                $materi_dikuasai++;
            }
        }

        return [
            'sesi_tersedia' => $sesi_tersedia,
            'sesi_selesai' => (int) ($row['sesi_selesai'] ?? 0),
            'rata_nilai' => round((float) ($row['rata_nilai'] ?? 0), 0),
            'materi_lemah' => $materi_lemah,
            'materi_dikuasai' => $materi_dikuasai
        ];
    }

    public function materi_dashboard_result()
    {
        $jenis = trim((string) $this->input->post('jenis'));
        $id_mata_pelajaran = trim((string) $this->input->post('id_mata_pelajaran'));

        if (!in_array($jenis, ['lemah', 'dikuasai'], true)) {
            return [
                'result' => 'false',
                'message' => 'Jenis materi tidak valid.',
                'data' => []
            ];
        }

        $rows = $this->materi_dashboard_rows($id_mata_pelajaran);
        $data = [];

        foreach ($rows as $row) {
            $persen = (float) ($row['persen'] ?? 0);

            if ($jenis == 'lemah' && $persen < 70) {
                $data[] = $row;
            }

            if ($jenis == 'dikuasai' && $persen >= 70) {
                $data[] = $row;
            }
        }

        if ($jenis == 'dikuasai') {
            usort($data, function ($a, $b) {
                if ((float) $a['persen'] == (float) $b['persen']) {
                    return strcmp($a['nama_materi'], $b['nama_materi']);
                }
                return (float) $b['persen'] <=> (float) $a['persen'];
            });
        }

        return [
            'result' => 'true',
            'message' => count($data) > 0 ? 'Data materi berhasil dimuat.' : 'Tidak ada data materi.',
            'data' => $data
        ];
    }

    
    public function riwayat_terbaru($limit = 5)
    {
        return $this->riwayat_result($limit);
    }

    public function riwayat_result($limit = null)
    {
        $id_siswa = $this->current_id_siswa();
        $id_kelas = trim($this->input->get_post('id_kelas'));
        $mapel = trim($this->input->get_post('mapel'));
        $jenis = trim($this->input->get_post('jenis'));

        $where = ["ps.id_siswa = ?", "ps.status_pengerjaan IN ('Selesai','Waktu Habis')"];
        $params = [$id_siswa];

        if ($id_kelas != '') {
            $where[] = "ps.id_kelas = ?";
            $params[] = $id_kelas;
        }
        if ($mapel != '') {
            $where[] = "ss.id_mata_pelajaran = ?";
            $params[] = $mapel;
        }
        if ($jenis != '') {
            $where[] = "ps.jenis_pengerjaan = ?";
            $params[] = $jenis;
        }

        $sql = "SELECT
                    ps.*,
                    ss.nama_sesi,
                    ps.jenis_pengerjaan,
                    ss.tahun_ajaran,
                     k.nama_kelas,
                     j.nama_jenjang,
                    mp.nama_mata_pelajaran,
                    ks.nama_kategori_soal
                FROM siswa_pengerjaan ps
                INNER JOIN soal_sesi ss ON ss.id = ps.id_sesi_soal
                LEFT JOIN kelas k ON k.id = ps.id_kelas
                INNER JOIN jenjang j ON k.id_jenjang = j.id
                LEFT JOIN mata_pelajaran mp ON mp.id = ss.id_mata_pelajaran
                LEFT JOIN soal_kategori ks ON ks.id = ss.id_kategori_soal
                WHERE " . implode(' AND ', $where) . "
                ORDER BY ps.waktu_selesai DESC, ps.id DESC";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
        }
        return $this->db->query($sql, $params)->result_array();
    }

    public function tahun_ajaran_result()
    {
        return $this->db->query("SELECT DISTINCT tahun_ajaran FROM soal_sesi WHERE tahun_ajaran IS NOT NULL AND tahun_ajaran != '' ORDER BY tahun_ajaran DESC")->result_array();
    }

    public function mulai_pengerjaan($id_sesi)
    {
        $id_siswa = $this->current_id_siswa();
        $id_kelas = $this->current_id_kelas();
        $sesi = $this->sesi_detail($id_sesi);

        if (!$sesi) {
            return ['status' => false, 'message' => 'Sesi soal tidak ditemukan.'];
        }

        // if ((string) $sesi['status_aktif'] !== '1') {
        //     return ['status' => false, 'message' => 'Sesi soal sudah tidak aktif.'];
        // }

        if ($this->ada_tunggakan_bulan_lalu($id_siswa)) {
            return [
                'status' => false,
                'message' => 'Maaf, sesi soal baru belum dapat diakses karena masih terdapat pembayaran yang belum diselesaikan. Silakan hubungi admin bimbel untuk menyelesaikan pembayaran.',
                'locked' => true
            ];
        }

        $attempt = $this->status_attempt_sesi($id_sesi, $id_siswa);

        if (!$attempt['boleh']) {
            return ['status' => false, 'message' => $attempt['message']];
        }

        if ($attempt['lanjut'] && !empty($attempt['id_pengerjaan'])) {
            return [
                'status' => true,
                'id_pengerjaan' => $attempt['id_pengerjaan'],
                'message' => 'Melanjutkan pengerjaan.'
            ];
        }

        $jenis_pengerjaan = $attempt['jenis_pengerjaan'];

        if ($jenis_pengerjaan == 'Bimbel') {
            if ((string) ($sesi['status_aktif'] ?? '0') !== '1') {
                return ['status' => false, 'message' => 'Sesi soal sudah tidak aktif.'];
            }

            if (!$this->sesi_masuk_jadwal($sesi)) {
                return ['status' => false, 'message' => 'Jadwal pengerjaan Bimbel sudah berakhir.'];
            }
        }

        $data = [
            'id_sesi_soal' => $id_sesi,
            'id_siswa' => $id_siswa,
            'id_kelas' => $id_kelas,
            'tahun_ajaran' => $sesi['tahun_ajaran'],
            'jenis_pengerjaan' => $jenis_pengerjaan,
            'waktu_mulai' => date('d-m-Y H:i:s'),
            'durasi_menit' => (int) $sesi['durasi_timer'],
            'jumlah_soal' => (int) $sesi['jumlah_soal'],
            'status_pengerjaan' => 'Proses',
            'created_at' => date('d-m-Y H:i:s'),
            'updated_at' => date('d-m-Y H:i:s'),
            'preview_diizinkan' => 0
        ];

        $this->db->insert('siswa_pengerjaan', $data);

        return [
            'status' => true,
            'id_pengerjaan' => $this->db->insert_id(),
            'message' => 'Pengerjaan dimulai.'
        ];
    }

    public function pengerjaan_detail($id_pengerjaan)
    {
        $sql = "SELECT
                    ps.*,
                    ss.nama_sesi,
                    ss.id_naskah_soal,
                    ss.tanggal_mulai,
                    ss.tanggal_selesai,
                    ss.jam_mulai,
                    ss.jam_selesai,
                    mp.nama_mata_pelajaran,
                    ks.nama_kategori_soal
                FROM siswa_pengerjaan ps
                INNER JOIN soal_sesi ss ON ss.id = ps.id_sesi_soal
                LEFT JOIN mata_pelajaran mp ON mp.id = ss.id_mata_pelajaran
                LEFT JOIN soal_kategori ks ON ks.id = ss.id_kategori_soal
                WHERE ps.id = ?
                AND ps.id_siswa = ?
                LIMIT 1";
        return $this->db->query($sql, [$id_pengerjaan, $this->current_id_siswa()])->row_array();
    }

    public function soal_pengerjaan($id_pengerjaan)
    {
        $pengerjaan = $this->pengerjaan_detail($id_pengerjaan);
        if (!$pengerjaan) {
            return [];
        }

        $soal = $this->db->query("SELECT
                    so.*,
                    m.nama_materi
                FROM soal so
                LEFT JOIN materi m ON m.id = so.id_materi
                WHERE so.id_naskah_soal = ?
                AND so.status_aktif = '1'
                AND so.status_hapus IS NULL
                ORDER BY CAST(so.nomor_soal AS UNSIGNED) ASC, so.id ASC", [$pengerjaan['id_naskah_soal']])->result_array();

        foreach ($soal as $i => $row) {
            $jawaban = $this->db->query("SELECT * FROM soal_jawaban WHERE id_soal = ? ORDER BY urutan ASC, id ASC", [$row['id']])->result_array();
            $soal[$i]['pilihan'] = [];
            $soal[$i]['pernyataan'] = [];
            foreach ($jawaban as $j) {
                if ($row['tipe_soal'] == 'benar_salah') {
                    $soal[$i]['pernyataan'][] = [
                        'id' => $j['id'],
                        'label' => $j['label_jawaban'],
                        'teks' => $j['isi_jawaban'],
                        'kunci' => $j['kunci_jawaban'] == '1' ? 'Benar' : 'Salah'
                    ];
                } else {
                    $soal[$i]['pilihan'][] = [
                        'id' => $j['id'],
                        'label' => $j['label_jawaban'],
                        'isi' => $j['isi_jawaban'],
                        'kunci' => $j['kunci_jawaban']
                    ];
                }
            }
        }

        return $soal;
    }

    public function jawaban_tersimpan($id_pengerjaan)
    {
        $pengerjaan = $this->pengerjaan_detail($id_pengerjaan);
        if (!$pengerjaan) {
            return [];
        }

        $tabel = $this->tabel_jawaban($pengerjaan['jenis_pengerjaan']);

        $rows = $this->db->query("SELECT id_soal, jawaban_siswa FROM {$tabel} WHERE id_pengerjaan = ?", [$id_pengerjaan])->result_array();

        $data = [];
        foreach ($rows as $row) {
            $decoded = json_decode($row['jawaban_siswa'], true);
            $data[$row['id_soal']] = $decoded;
        }

        return $data;
    }

    public function simpan_jawaban($id_pengerjaan, $jawaban)
    {
        $pengerjaan = $this->pengerjaan_detail($id_pengerjaan);
        if (!$pengerjaan || $pengerjaan['status_pengerjaan'] != 'Proses') {
            return false;
        }

        $tabel = $this->tabel_jawaban($pengerjaan['jenis_pengerjaan']);

        if (!is_array($jawaban)) {
            $jawaban = [];
        }
        $soal = $this->soal_pengerjaan($id_pengerjaan);
        $id_soal_aktif = [];

        foreach ($soal as $s) {
            $id_soal_aktif[] = (int) $s['id'];
        }
        foreach ($id_soal_aktif as $id_soal) {
            if (!array_key_exists($id_soal, $jawaban) && !array_key_exists((string) $id_soal, $jawaban)) {
                $this->db->delete($tabel, [
                    'id_pengerjaan' => $id_pengerjaan,
                    'id_soal' => $id_soal
                ]);
            }
        }

        foreach ($jawaban as $id_soal => $isi) {
            $id_soal = (int) $id_soal;

            /*
             * Jika array kosong, anggap dikosongkan.
             */
            if (is_array($isi) && count($isi) == 0) {
                $this->db->delete($tabel, [
                    'id_pengerjaan' => $id_pengerjaan,
                    'id_soal' => $id_soal
                ]);
                continue;
            }

            $json = json_encode($isi, JSON_UNESCAPED_UNICODE);

            $cek = $this->db->get_where($tabel, [
                'id_pengerjaan' => $id_pengerjaan,
                'id_soal' => $id_soal
            ])->row_array();

            $data = [
                'id_pengerjaan' => $id_pengerjaan,
                'id_sesi_soal' => $pengerjaan['id_sesi_soal'],
                'id_siswa' => $pengerjaan['id_siswa'],
                'id_soal' => $id_soal,
                'jawaban_siswa' => $json,
                'updated_at' => date('d-m-Y H:i:s')
            ];

            if ($cek) {
                $this->db->where('id', $cek['id']);
                $this->db->update($tabel, $data);
            } else {
                $data['created_at'] = date('d-m-Y H:i:s');
                $this->db->insert($tabel, $data);
            }
        }

        return true;
    }
    public function catat_keluar_halaman($id_pengerjaan)
    {
        $pengerjaan = $this->pengerjaan_detail($id_pengerjaan);
        if (!$pengerjaan || $pengerjaan['status_pengerjaan'] != 'Proses') {
            return ['keluar_halaman' => 0, 'reset_jawaban' => false];
        }

        $keluar = (int) $pengerjaan['keluar_halaman'] + 1;
        $data = [
            'keluar_halaman' => $keluar,
            'updated_at' => date('d-m-Y H:i:s')
        ];

        if ($keluar >= 3) {
            $data['reset_jawaban'] = (int) $pengerjaan['reset_jawaban'] + 1;

            $tabel = $this->tabel_jawaban($pengerjaan['jenis_pengerjaan']);
            $this->db->delete($tabel, ['id_pengerjaan' => $id_pengerjaan]);
        }

        $this->db->where('id', $id_pengerjaan);
        $this->db->update('siswa_pengerjaan', $data);

        return ['keluar_halaman' => $keluar, 'reset_jawaban' => $keluar >= 3];
    }

    private function kunci_soal($id_soal)
    {
        $rows = $this->db->query("SELECT * FROM soal_jawaban WHERE id_soal = ? ORDER BY urutan ASC, id ASC", [$id_soal])->result_array();
        $kunci = [];
        foreach ($rows as $row) {
            if ($row['kunci_jawaban'] == '1') {
                $kunci[] = $row['label_jawaban'];
            }
        }
        return $kunci;
    }

    private function hitung_nilai_soal($soal, $jawaban)
    {
        $bobot = (float) $soal['bobot_nilai'];
        if ($jawaban === null || $jawaban === '' || (is_array($jawaban) && count($jawaban) == 0)) {
            if ($soal['tipe_soal'] == 'benar_salah') {
                $rows = $this->db->query("SELECT * FROM soal_jawaban WHERE id_soal = ? ORDER BY urutan ASC, id ASC ", [$soal['id']])->result_array();

                $kunci = [];
                foreach ($rows as $row) {
                    $kunci[$row['id']] = $row['kunci_jawaban'] == '1' ? 'Benar' : 'Salah';
                }

                return [0, 'Kosong', $kunci];
            }

            $kunci = $this->kunci_soal($soal['id']);

            return [0, 'Kosong', $kunci];
        }

        if ($soal['tipe_soal'] == 'pg') {
            $kunci = $this->kunci_soal($soal['id']);
            $jawab = is_array($jawaban) ? reset($jawaban) : $jawaban;
            $benar = isset($kunci[0]) && (string) $jawab === (string) $kunci[0];
            return [$benar ? $bobot : 0, $benar ? 'Benar' : 'Salah', $kunci];
        }

        if ($soal['tipe_soal'] == 'pg_kompleks') {
            $kunci = $this->kunci_soal($soal['id']);
            $jawab = is_array($jawaban) ? $jawaban : [$jawaban];
            $score = 0;
            foreach ($jawab as $j) {
                if (in_array($j, $kunci)) {
                    $score++;
                } else {
                    $score--;
                }
            }
            if ($score < 0) {
                $score = 0;
            }
            $total_kunci = max(count($kunci), 1);
            $nilai = ($score / $total_kunci) * $bobot;
            if ($nilai > $bobot) {
                $nilai = $bobot;
            }
            $status = $nilai == $bobot ? 'Benar' : ($nilai > 0 ? 'Sebagian benar' : 'Salah');
            return [$nilai, $status, $kunci];
        }

        // Benar/Salah: jawaban berbentuk [id_jawaban => Benar/Salah]
        $rows = $this->db->query("SELECT * FROM soal_jawaban WHERE id_soal = ? ORDER BY urutan ASC, id ASC", [$soal['id']])->result_array();
        $total = count($rows);
        $benar = 0;
        $kunci = [];
        foreach ($rows as $row) {
            $kunci[$row['id']] = $row['kunci_jawaban'] == '1' ? 'Benar' : 'Salah';
            if (isset($jawaban[$row['id']]) && $jawaban[$row['id']] == $kunci[$row['id']]) {
                $benar++;
            }
        }

        if ($total == 0) {
            return [0, 'Kosong', $kunci];
        }
        $nilai = ($benar / $total) * $bobot;
        $status = $benar == $total ? 'Benar' : ($benar > 0 ? 'Sebagian benar' : 'Salah');
        return [$nilai, $status, $kunci];
    }

    public function kumpulkan_jawaban($id_pengerjaan, $jawaban, $status = 'Selesai')
    {
        $pengerjaan = $this->pengerjaan_detail($id_pengerjaan);
        if (!$pengerjaan) {
            return ['status' => false, 'message' => 'Data pengerjaan tidak ditemukan.'];
        }

        if ($pengerjaan['status_pengerjaan'] != 'Proses') {
            return ['status' => true, 'id_pengerjaan' => $id_pengerjaan, 'message' => 'Pengerjaan sudah selesai.'];
        }

        $this->simpan_jawaban($id_pengerjaan, $jawaban);
        $jawaban_db = $this->jawaban_tersimpan($id_pengerjaan);
        $soal = $this->soal_pengerjaan($id_pengerjaan);
        $tabel = $this->tabel_jawaban($pengerjaan['jenis_pengerjaan']);
        $total_bobot = 0;
        $total_nilai = 0;
        $jumlah_benar = 0;
        $jumlah_salah = 0;
        $jumlah_kosong = 0;

        foreach ($soal as $s) {
            $id_soal = $s['id'];
            $jawab = array_key_exists($id_soal, $jawaban_db) ? $jawaban_db[$id_soal] : null;
            list($nilai_soal, $status_jawaban, $kunci) = $this->hitung_nilai_soal($s, $jawab);

            $total_bobot += (float) $s['bobot_nilai'];
            $total_nilai += $nilai_soal;

            if ($status_jawaban == 'Kosong') {
                $jumlah_kosong++;
            } elseif ($status_jawaban == 'Benar') {
                $jumlah_benar++;
            } else {
                $jumlah_salah++;
            }

            $this->db->where(['id_pengerjaan' => $id_pengerjaan, 'id_soal' => $id_soal]);
            $this->db->update($tabel, [
                'jawaban_benar' => json_encode($kunci, JSON_UNESCAPED_UNICODE),
                'status_jawaban' => $status_jawaban,
                'nilai' => $nilai_soal,
                'updated_at' => date('d-m-Y H:i:s')
            ]);

            if ($jawab === null) {
                $this->db->insert($tabel, [
                    'id_pengerjaan' => $id_pengerjaan,
                    'id_sesi_soal' => $pengerjaan['id_sesi_soal'],
                    'id_siswa' => $pengerjaan['id_siswa'],
                    'id_soal' => $id_soal,
                    'jawaban_siswa' => json_encode('', JSON_UNESCAPED_UNICODE),
                    'jawaban_benar' => json_encode($kunci, JSON_UNESCAPED_UNICODE),
                    'status_jawaban' => $status_jawaban,
                    'nilai' => $nilai_soal,
                    'created_at' => date('d-m-Y H:i:s'),
                    'updated_at' => date('d-m-Y H:i:s')
                ]);
            }
        }

        $nilai_akhir = $total_bobot > 0 ? round(($total_nilai / $total_bobot) * 100, 2) : 0;
        $waktu_mulai = strtotime($pengerjaan['waktu_mulai']);
        $durasi_detik = max(time() - $waktu_mulai, 0);

        $this->db->where('id', $id_pengerjaan);
        $this->db->update('siswa_pengerjaan', [
            'waktu_selesai' => date('d-m-Y H:i:s'),
            'durasi_detik' => $durasi_detik,
            'jumlah_soal' => count($soal),
            'jumlah_benar' => $jumlah_benar,
            'jumlah_salah' => $jumlah_salah,
            'jumlah_kosong' => $jumlah_kosong,
            'nilai_akhir' => $nilai_akhir,
            'status_pengerjaan' => $status,
            'updated_at' => date('d-m-Y H:i:s')
        ]);

        return ['status' => true, 'id_pengerjaan' => $id_pengerjaan, 'message' => 'Jawaban berhasil dikumpulkan.'];
    }

    public function analisa_materi_hasil($id_pengerjaan)
    {
        $pengerjaan = $this->pengerjaan_detail($id_pengerjaan);
        if (!$pengerjaan) {
            return [];
        }

        $tabel = $this->tabel_jawaban($pengerjaan['jenis_pengerjaan']);
        $sql = "SELECT
                m.id,
                m.nama_materi,
                SUM(CAST(js.nilai AS DECIMAL(10,2))) AS nilai,
                SUM(CAST(so.bobot_nilai AS DECIMAL(10,2))) AS bobot,
                ROUND((SUM(CAST(js.nilai AS DECIMAL(10,2))) / NULLIF(SUM(CAST(so.bobot_nilai AS DECIMAL(10,2))), 0)) * 100, 0) AS persen,
                SUM(CASE WHEN js.status_jawaban = 'Benar' THEN 1 ELSE 0 END) AS benar,
                COUNT(js.id) AS total
            FROM {$tabel} js
            INNER JOIN soal so ON so.id = js.id_soal
            INNER JOIN materi m ON m.id = so.id_materi
            WHERE js.id_pengerjaan = ?
            GROUP BY m.id
            ORDER BY persen DESC";

        return $this->db->query($sql, [$id_pengerjaan])->result_array();
    }

    public function preview_diizinkan($id_pengerjaan)
    {
        $row = $this->pengerjaan_detail($id_pengerjaan);
        return (string) ($row['preview_diizinkan'] ?? '0') === '1';
    }

    private function label_jawaban_text($id_soal, $jawaban)
    {
        if ($jawaban === null || $jawaban === '') {
            return '-';
        }
        $map = [];
        $rows = $this->db->query("SELECT * FROM soal_jawaban WHERE id_soal = ? ORDER BY urutan ASC, id ASC", [$id_soal])->result_array();
        foreach ($rows as $r) {
            $map[$r['label_jawaban']] = $r['label_jawaban'] . '. ' . $r['isi_jawaban'];
            $map[$r['id']] = $r['label_jawaban'] . '. ' . $r['isi_jawaban'];
        }

        if (is_array($jawaban)) {
            $out = [];
            foreach ($jawaban as $k => $v) {
                if (is_array($v)) {
                    continue;
                }
                if ($v === 'Benar' || $v === 'Salah') {
                    $out[] = ($map[$k] ?? $k) . ' = ' . $v;
                } else {
                    $out[] = $map[$v] ?? $v;
                }
            }
            return count($out) ? implode(', ', $out) : '-';
        }

        return $map[$jawaban] ?? $jawaban;
    }


    private function benar_salah_preview_items($id_soal, $jawaban)
    {
        $jawaban = is_array($jawaban) ? $jawaban : [];

        $rows = $this->db->query("SELECT * FROM soal_jawaban WHERE id_soal = ? ORDER BY urutan ASC, id ASC", [$id_soal])->result_array();
        $data = [];

        foreach ($rows as $idx => $row) {
            $id_jawaban = (string) ($row['id'] ?? '');
            $nilai = '-';

            if (isset($jawaban[$row['id']])) {
                $nilai = $jawaban[$row['id']];
            } elseif (isset($jawaban[$id_jawaban])) {
                $nilai = $jawaban[$id_jawaban];
            }

            if ($nilai === null || $nilai === '') {
                $nilai = '-';
            }

            $data[] = [
                'nomor' => $idx + 1,
                'teks' => $row['isi_jawaban'] ?? '-',
                'jawaban' => $nilai
            ];
        }

        return $data;
    }

    public function preview_jawaban($id_pengerjaan)
    {
        $pengerjaan = $this->pengerjaan_detail($id_pengerjaan);
        if (!$pengerjaan) {
            return [];
        }

        $tabel = $this->tabel_jawaban($pengerjaan['jenis_pengerjaan']);

        $rows = $this->db->query("SELECT
                js.*,
                so.nomor_soal,
                so.pertanyaan,
                so.gambar_soal,
                so.pembahasan,
                so.tipe_soal,
                m.nama_materi
            FROM {$tabel} js
            INNER JOIN soal so ON so.id = js.id_soal
            LEFT JOIN materi m ON m.id = so.id_materi
            WHERE js.id_pengerjaan = ?
            ORDER BY CAST(so.nomor_soal AS UNSIGNED) ASC, so.id ASC", [$id_pengerjaan])->result_array();

        foreach ($rows as $i => $row) {
            $jawab = json_decode($row['jawaban_siswa'], true);
            $kunci = json_decode($row['jawaban_benar'], true);
            $rows[$i]['jawaban_siswa_text'] = $this->label_jawaban_text($row['id_soal'], $jawab);
            $rows[$i]['jawaban_benar_text'] = $this->label_jawaban_text($row['id_soal'], $kunci);
            $rows[$i]['jawaban_siswa_items'] = [];
            $rows[$i]['jawaban_benar_items'] = [];
            if (($row['tipe_soal'] ?? '') == 'benar_salah') {
                $rows[$i]['jawaban_siswa_items'] = $this->benar_salah_preview_items($row['id_soal'], $jawab);
                $rows[$i]['jawaban_benar_items'] = $this->benar_salah_preview_items($row['id_soal'], $kunci);
            }
        }

        return $rows;
    }

    public function perkembangan_result()
    {
        $id_siswa = $this->current_id_siswa();
        $id_kelas = (int) $this->input->post('id_kelas');
        $semester = trim((string) $this->input->post('semester'));
        $id_mata_pelajaran = (int) $this->input->post('id_mata_pelajaran');
        $jenis_pengerjaan = trim((string) $this->input->post('jenis_pengerjaan'));

        if ($id_siswa <= 0) {
            return ['result' => 'false', 'status' => false, 'message' => 'Sesi login siswa tidak ditemukan.'];
        }

        if (!in_array($semester, ['Ganjil', 'Genap'], true)) {
            return ['result' => 'false', 'status' => false, 'message' => 'Silakan pilih semester Ganjil atau Genap.'];
        }

        if (!in_array($jenis_pengerjaan, ['', 'Bimbel', 'Rumah'], true)) {
            return ['result' => 'false', 'status' => false, 'message' => 'Jenis pengerjaan tidak valid.'];
        }

        $tanggal_sql = "STR_TO_DATE(ps.waktu_selesai, '%d-%m-%Y %H:%i:%s')";
        $nilai_sql = "CAST(NULLIF(ps.nilai_akhir, '') AS DECIMAL(10,2))";

        $where = [
            'ps.id_siswa = ?',
            "ps.status_pengerjaan IN ('Selesai', 'Waktu Habis', 'Selesai karena timer habis')",
            "{$tanggal_sql} IS NOT NULL",
            "{$nilai_sql} IS NOT NULL"
        ];
        $params = [$id_siswa];

        if ($id_kelas > 0) {
            $where[] = 'ps.id_kelas = ?';
            $params[] = $id_kelas;
        }

        if ($semester === 'Ganjil') {
            $where[] = "MONTH({$tanggal_sql}) BETWEEN 7 AND 12";
            $bulan_semester = [7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        } else {
            $where[] = "MONTH({$tanggal_sql}) BETWEEN 1 AND 6";
            $bulan_semester = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni'];
        }

        if ($id_mata_pelajaran > 0) {
            $where[] = 'ss.id_mata_pelajaran = ?';
            $params[] = $id_mata_pelajaran;
        }

        if ($jenis_pengerjaan !== '') {
            $where[] = 'ps.jenis_pengerjaan = ?';
            $params[] = $jenis_pengerjaan;
        }

        $where_sql = implode(' AND ', $where);

        $agregat_bulan = $this->db->query("SELECT
                    MONTH({$tanggal_sql}) AS bulan,
                    ps.jenis_pengerjaan,
                    ROUND(AVG({$nilai_sql}), 2) AS rata_rata,
                    ROUND(MIN({$nilai_sql}), 2) AS terendah,
                    ROUND(MAX({$nilai_sql}), 2) AS tertinggi,
                    COUNT(ps.id) AS jumlah
                FROM siswa_pengerjaan ps
                INNER JOIN soal_sesi ss ON ss.id = ps.id_sesi_soal
                WHERE {$where_sql}
                GROUP BY MONTH({$tanggal_sql}), ps.jenis_pengerjaan
                ORDER BY MONTH({$tanggal_sql}) ASC", $params)->result_array();

        if (empty($agregat_bulan)) {
            return [
                'result' => 'false',
                'status' => false,
                'message' => 'Belum ada data perkembangan sesuai filter.',
                'ringkasan' => ['rata_rata' => 0, 'nilai_awal' => '-', 'nilai_terbaru' => '-', 'tren' => 'Belum cukup data'],
                'grafik' => [],
                'materi' => [],
                'terbaru' => []
            ];
        }

        $lookup = [];
        foreach ($agregat_bulan as $row) {
            $lookup[(int) $row['bulan']][$row['jenis_pengerjaan']] = $row;
        }

        $grafik = [];
        foreach ($bulan_semester as $nomor_bulan => $nama_bulan) {
            $bimbel = $lookup[$nomor_bulan]['Bimbel'] ?? null;
            $rumah = $lookup[$nomor_bulan]['Rumah'] ?? null;
            $grafik[] = [
                'bulan' => $nomor_bulan,
                'label' => $nama_bulan,
                'bimbel' => $bimbel ? round((float) $bimbel['rata_rata'], 2) : null,
                'rumah' => $rumah ? round((float) $rumah['rata_rata'], 2) : null,
                'jumlah_bimbel' => $bimbel ? (int) $bimbel['jumlah'] : 0,
                'jumlah_rumah' => $rumah ? (int) $rumah['jumlah'] : 0,
                'tertinggi_bimbel' => $bimbel ? round((float) $bimbel['tertinggi'], 2) : null,
                'terendah_bimbel' => $bimbel ? round((float) $bimbel['terendah'], 2) : null,
                'tertinggi_rumah' => $rumah ? round((float) $rumah['tertinggi'], 2) : null,
                'terendah_rumah' => $rumah ? round((float) $rumah['terendah'], 2) : null
            ];
        }

        $nilai_ringkasan = [];
        foreach ($grafik as $row) {
            if ($jenis_pengerjaan === 'Bimbel') {
                if ($row['bimbel'] !== null) $nilai_ringkasan[] = $row['bimbel'];
            } elseif ($jenis_pengerjaan === 'Rumah') {
                if ($row['rumah'] !== null) $nilai_ringkasan[] = $row['rumah'];
            } else {
                $nilai_bulan = array_values(array_filter([$row['bimbel'], $row['rumah']], function ($nilai) {
                    return $nilai !== null;
                }));
                if (!empty($nilai_bulan)) $nilai_ringkasan[] = array_sum($nilai_bulan) / count($nilai_bulan);
            }
        }

        $rata_rata = round(array_sum($nilai_ringkasan) / count($nilai_ringkasan), 0);
        $nilai_awal = round($nilai_ringkasan[0], 0);
        $nilai_terbaru = round($nilai_ringkasan[count($nilai_ringkasan) - 1], 0);
        $selisih = $nilai_terbaru - $nilai_awal;
        $tren = count($nilai_ringkasan) < 2 ? 'Belum cukup data' : ($selisih > 0 ? 'Naik ' . $selisih . ' poin' : ($selisih < 0 ? 'Turun ' . abs($selisih) . ' poin' : 'Tetap'));

        $jawaban_source = "(
            SELECT id_pengerjaan, id_soal, nilai, 'Bimbel' AS jenis_pengerjaan FROM siswa_jawaban_bimbel
            UNION ALL
            SELECT id_pengerjaan, id_soal, nilai, 'Rumah' AS jenis_pengerjaan FROM siswa_jawaban_rumah
        )";

        $materi_where = $where;
        $materi_where[] = 'm.id IS NOT NULL';
        $materi_where_sql = implode(' AND ', $materi_where);

        $materi = $this->db->query("SELECT
                    m.id,
                    m.nama_materi,
                    ROUND((SUM(COALESCE(CAST(js.nilai AS DECIMAL(10,2)), 0)) /
                        NULLIF(SUM(CAST(so.bobot_nilai AS DECIMAL(10,2))), 0)) * 100, 2) AS persen
                FROM {$jawaban_source} js
                INNER JOIN siswa_pengerjaan ps ON ps.id = js.id_pengerjaan AND ps.jenis_pengerjaan = js.jenis_pengerjaan
                INNER JOIN soal_sesi ss ON ss.id = ps.id_sesi_soal
                INNER JOIN soal so ON so.id = js.id_soal AND so.status_hapus IS NULL
                LEFT JOIN materi m ON m.id = so.id_materi
                WHERE {$materi_where_sql}
                GROUP BY m.id, m.nama_materi
                ORDER BY persen DESC, m.nama_materi ASC", $params)->result_array();

        foreach ($materi as &$row) {
            $persen = max(0, min(100, (float) ($row['persen'] ?? 0)));
            $row['persen'] = round($persen, 0);
            // $row['status'] = $persen >= 86 ? 'Sangat Baik' : ($persen >= 76 ? 'Baik' : ($persen >= 60 ? 'Cukup' : 'Perlu Ditingkatkan'));
            $row['status'] = $persen >= 70 ? 'Dikuasai' : 'Perlu Ditingkatkan';
        }
        unset($row);

        $terbaru = $this->db->query("SELECT
                    ss.nama_sesi,
                    ps.jenis_pengerjaan,
                    ROUND({$nilai_sql}, 0) AS nilai_akhir,
                    DATE_FORMAT({$tanggal_sql}, '%d-%m-%Y') AS tanggal
                FROM siswa_pengerjaan ps
                INNER JOIN soal_sesi ss ON ss.id = ps.id_sesi_soal
                WHERE {$where_sql}
                ORDER BY {$tanggal_sql} DESC, ps.id DESC
                LIMIT 5", $params)->result_array();

        return [
            'result' => 'true',
            'status' => true,
            'message' => 'Data perkembangan berhasil dimuat.',
            'ringkasan' => [
                'rata_rata' => $rata_rata,
                'nilai_awal' => $nilai_awal,
                'nilai_terbaru' => $nilai_terbaru,
                'tren' => $tren
            ],
            'grafik' => $grafik,
            'materi' => $materi,
            'terbaru' => $terbaru
        ];
    }


    public function update_password($password_lama, $password_baru, $konfirmasi)
    {
        if (!$this->db->field_exists('password_siswa', 'siswa')) {
            return [
                'status' => false,
                'message' => 'Kolom password_siswa belum ada.'
            ];
        }

        $password_lama = trim($password_lama);
        $password_baru = trim($password_baru);
        $konfirmasi = trim($konfirmasi);

        if ($password_lama == '' || $password_baru == '' || $konfirmasi == '') {
            return [
                'status' => false,
                'message' => 'Semua field password wajib diisi.'
            ];
        }

        if ($password_baru !== $konfirmasi) {
            return [
                'status' => false,
                'message' => 'Password baru dan konfirmasi password tidak sama.'
            ];
        }

        $siswa = $this->siswa();
        if (!$siswa) {
            return [
                'status' => false,
                'message' => 'Data siswa tidak ditemukan.'
            ];
        }

        $password_db = $siswa['password_siswa'] ?? '';
        if ($password_db == '') {
            return [
                'status' => false,
                'message' => 'Password siswa belum diatur. Silakan hubungi admin.'
            ];
        }

        if (!password_verify($password_lama, $password_db)) {
            return [
                'status' => false,
                'message' => 'Password lama tidak sesuai.'
            ];
        }

        $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);

        $data = [
            'password_siswa' => $hashed_password
        ];

        if ($this->db->field_exists('password_siswa_text', 'siswa')) {
            $data['password_siswa_text'] = $password_baru;
        }

        $this->db->trans_begin();

        $this->db->where('id', $siswa['id']);
        $this->db->update('siswa', $data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Password gagal diperbarui.'
            ];
        }

        $this->db->trans_commit();

        return [
            'status' => true,
            'message' => 'Password berhasil diperbarui.'
        ];
    }
     public function materi_bulanan_result()
    {
        $id_siswa = $this->current_id_siswa();
        $id_kelas = (int) $this->input->post('id_kelas');
        $semester = trim((string) $this->input->post('semester'));
        $id_mata_pelajaran = (int) $this->input->post('id_mata_pelajaran');
        $jenis_pengerjaan = trim((string) $this->input->post('jenis_pengerjaan'));
        $bulan = (int) $this->input->post('bulan');
        $page_dikuasai = max(1, (int) $this->input->post('page_dikuasai'));
        $page_lemah = max(1, (int) $this->input->post('page_lemah'));
        $limit = 5;

        if ($id_siswa <= 0) {
            return ['result' => 'false', 'status' => false, 'message' => 'Sesi login siswa tidak ditemukan.'];
        }

        if (!in_array($semester, ['Ganjil', 'Genap'], true)) {
            return ['result' => 'false', 'status' => false, 'message' => 'Semester tidak valid.'];
        }

        if (!in_array($jenis_pengerjaan, ['Bimbel', 'Rumah'], true)) {
            return ['result' => 'false', 'status' => false, 'message' => 'Jenis pengerjaan tidak valid.'];
        }

        $bulan_valid = $semester === 'Ganjil' ? [7, 8, 9, 10, 11, 12] : [1, 2, 3, 4, 5, 6];
        if (!in_array($bulan, $bulan_valid, true)) {
            return ['result' => 'false', 'status' => false, 'message' => 'Bulan tidak sesuai dengan semester yang dipilih.'];
        }

        $nama_bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $tanggal_sql = "STR_TO_DATE(ps.waktu_selesai, '%d-%m-%Y %H:%i:%s')";
        $where = [
            'ps.id_siswa = ?',
            "ps.status_pengerjaan IN ('Selesai', 'Waktu Habis', 'Selesai karena timer habis')",
            "{$tanggal_sql} IS NOT NULL",
            "MONTH({$tanggal_sql}) = ?",
            'ps.jenis_pengerjaan = ?'
        ];
        $params = [$id_siswa, $bulan, $jenis_pengerjaan];

        if ($id_kelas > 0) {
            $where[] = 'ps.id_kelas = ?';
            $params[] = $id_kelas;
        }

        if ($id_mata_pelajaran > 0) {
            $where[] = 'ss.id_mata_pelajaran = ?';
            $params[] = $id_mata_pelajaran;
        }

        $where[] = 'm.id IS NOT NULL';
        $where_sql = implode(' AND ', $where);
        $tabel_jawaban = $jenis_pengerjaan === 'Rumah' ? 'siswa_jawaban_rumah' : 'siswa_jawaban_bimbel';

        $rows = $this->db->query("SELECT
                    m.id,
                    m.nama_materi,
                    ROUND((SUM(COALESCE(CAST(js.nilai AS DECIMAL(10,2)), 0)) /
                        NULLIF(SUM(CAST(so.bobot_nilai AS DECIMAL(10,2))), 0)) * 100, 2) AS persen,
                    COUNT(DISTINCT ps.id) AS jumlah_pengerjaan,
                    COUNT(js.id) AS jumlah_soal
                FROM {$tabel_jawaban} js
                INNER JOIN siswa_pengerjaan ps ON ps.id = js.id_pengerjaan
                INNER JOIN soal_sesi ss ON ss.id = ps.id_sesi_soal
                INNER JOIN soal so ON so.id = js.id_soal AND so.status_hapus IS NULL
                INNER JOIN materi m ON m.id = so.id_materi
                WHERE {$where_sql}
                GROUP BY m.id, m.nama_materi
                ORDER BY persen DESC, m.nama_materi ASC", $params)->result_array();

        $dikuasai = [];
        $lemah = [];
        foreach ($rows as $row) {
            $persen = max(0, min(100, (float) ($row['persen'] ?? 0)));
            $item = [
                'id' => (int) $row['id'],
                'nama_materi' => $row['nama_materi'] ?? '-',
                'persen' => round($persen, 0),
                // 'status' => $persen >= 86 ? 'Sangat Baik' : ($persen >= 76 ? 'Baik' : ($persen >= 60 ? 'Cukup' : 'Perlu Ditingkatkan')),
                'status' => $persen >= 70 ? 'Dikuasai' : 'Perlu Ditingkatkan',
                'jumlah_pengerjaan' => (int) ($row['jumlah_pengerjaan'] ?? 0),
                'jumlah_soal' => (int) ($row['jumlah_soal'] ?? 0)
            ];

            if ($persen >= 70) {
                $dikuasai[] = $item;
            } else {
                $lemah[] = $item;
            }
        }

        usort($dikuasai, function ($a, $b) {
            if ($a['persen'] === $b['persen']) return strcmp($a['nama_materi'], $b['nama_materi']);
            return $b['persen'] <=> $a['persen'];
        });
        usort($lemah, function ($a, $b) {
            if ($a['persen'] === $b['persen']) return strcmp($a['nama_materi'], $b['nama_materi']);
            return $a['persen'] <=> $b['persen'];
        });

        $paginate = function (array $items, int $page) use ($limit) {
            $total = count($items);
            $total_page = max(1, (int) ceil($total / $limit));
            $page = min(max(1, $page), $total_page);
            return [
                'data' => array_slice($items, ($page - 1) * $limit, $limit),
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'total_page' => $total_page
                ]
            ];
        };

        $hasil_dikuasai = $paginate($dikuasai, $page_dikuasai);
        $hasil_lemah = $paginate($lemah, $page_lemah);

        return [
            'result' => 'true',
            'status' => true,
            'message' => 'Data kemampuan materi berhasil dimuat.',
            'periode' => [
                'bulan' => $bulan,
                'nama_bulan' => $nama_bulan[$bulan],
                'semester' => $semester,
                'jenis_pengerjaan' => $jenis_pengerjaan
            ],
            'materi_dikuasai' => $hasil_dikuasai['data'],
            'pagination_dikuasai' => $hasil_dikuasai['pagination'],
            'materi_lemah' => $hasil_lemah['data'],
            'pagination_lemah' => $hasil_lemah['pagination']
        ];
    }
}
?>