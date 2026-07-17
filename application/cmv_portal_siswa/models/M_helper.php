<?php
class M_helper extends CI_Model
{
    public function tanggal_indo($tanggal)
    {
        if ($tanggal == '' || $tanggal == null || $tanggal == '0000-00-00') {
            return '-';
        }

        $bulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $tanggal)) {
            $pecah = explode('-', $tanggal);
            return $pecah[0] . ' ' . ($bulan[$pecah[1]] ?? $pecah[1]) . ' ' . $pecah[2];
        }

        $tgl = date('d', strtotime($tanggal));
        $bln = date('m', strtotime($tanggal));
        $thn = date('Y', strtotime($tanggal));
        return $tgl . ' ' . ($bulan[$bln] ?? $bln) . ' ' . $thn;
    }

    public function tanggal_jam_indo($datetime)
    {
        if ($datetime == '' || $datetime == null || $datetime == '0000-00-00 00:00:00') {
            return '-';
        }

        return $this->tanggal_indo(date('Y-m-d', strtotime($datetime))) . ' ' . date('H:i', strtotime($datetime));
    }

    public function rupiah($angka)
    {
        return number_format((float) $angka, 0, ',', '.');
    }

    public function tahun_ajaran_sekarang()
    {
        $tahun = (int) date('Y');
        $bulan = (int) date('m');
        if ($bulan >= 7) {
            return $tahun . '/' . ($tahun + 1);
        }
        return ($tahun - 1) . '/' . $tahun;
    }
}
?>
