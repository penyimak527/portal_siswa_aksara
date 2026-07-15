<?php
class M_login extends CI_Model
{
   public function login($nis, $password)
{
    $nis = trim($nis);
    $password = trim($password);

    if ($nis == '' || $password == '') {
        return [
            'status' => false,
            'message' => 'NIS dan password wajib diisi.'
        ];
    }

    $sql = "SELECT
                s.id,
                s.id_jenjang,
                s.id_kelas,
                s.nis,
                s.password_siswa,
                s.nama_siswa,
                s.status_aktif,
                k.nama_kelas
            FROM siswa s
            LEFT JOIN kelas k ON k.id = s.id_kelas
            WHERE s.nis = ?
            LIMIT 1";

    $row = $this->db->query($sql, [$nis])->row_array();

    if (!$row) {
        return [
            'status' => false,
            'message' => 'NIS atau password salah.'
        ];
    }

    if ((string)($row['status_aktif'] ?? '') !== '1') {
        return [
            'status' => false,
            'message' => 'Akun siswa tidak aktif. Silakan hubungi admin.'
        ];
    }

    $password_db = $row['password_siswa'] ?? '';

    if ($password_db == '') {
        return [
            'status' => false,
            'message' => 'Password siswa belum diatur. Silakan hubungi admin.'
        ];
    }

    if (!password_verify($password, $password_db)) {
        return [
            'status' => false,
            'message' => 'NIS atau password salah.'
        ];
    }

    return [
        'status' => true,
        'message' => 'Login berhasil.',
        'data' => [
            'logged_in' => true,
            'id_siswa' => $row['id'],
            'id_jenjang' => $row['id_jenjang'],
            'id_kelas' => $row['id_kelas'],
            'nis' => $row['nis'],
            'nama_siswa' => $row['nama_siswa'],
            'nama_kelas' => $row['nama_kelas'] ?? '-'
        ]
    ];
}
}
?>
