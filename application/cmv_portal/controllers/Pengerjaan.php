<?php
class Pengerjaan extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('M_portal_siswa', 'model');
        $this->cek_login();
    }

    private function cek_login()
    {
        if (($this->session->userdata('siswa')['logged_in'] ?? null) == null) {
            redirect('/');
        }
    }

    private function json_output($data)
    {
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    public function mulai($id_sesi = null)
    {
        if (!$id_sesi) {
            redirect('sesi');
        }

        $result = $this->model->mulai_pengerjaan($id_sesi);
        if (!$result['status']) {
            if (!empty($result['locked'])) {
                redirect('sesi/akses_ditolak');
            }
            $this->session->set_flashdata('message', '<div class="alert alert-warning rounded-3">' . $result['message'] . '</div>');
            redirect('sesi');
        }

        redirect('pengerjaan/soal/' . $result['id_pengerjaan']);
    }

    public function soal($id_pengerjaan = null)
    {
        if (!$id_pengerjaan) {
            redirect('sesi');
        }

        $data['title'] = 'Pengerjaan Soal';
        $data['pengerjaan'] = $this->model->pengerjaan_detail($id_pengerjaan);
        if (!$data['pengerjaan']) {
            show_404();
        }

        if ($data['pengerjaan']['status_pengerjaan'] != 'Proses') {
            redirect('pengerjaan/hasil/' . $id_pengerjaan);
        }

        $data['soal'] = $this->model->soal_pengerjaan($id_pengerjaan);
        $data['jawaban_tersimpan'] = $this->model->jawaban_tersimpan($id_pengerjaan);
        $this->load->view('portal_siswa/pengerjaan', $data);
    }

    public function simpan_jawaban($id_pengerjaan = null)
    {
        $jawaban = $this->input->post('jawaban');
        $ok = $this->model->simpan_jawaban($id_pengerjaan, $jawaban);
        $this->json_output([
            'result' => $ok ? 'true' : 'false',
            'message' => $ok ? 'Jawaban tersimpan' : 'Jawaban gagal disimpan'
        ]);
    }

    public function keluar_halaman($id_pengerjaan = null)
    {
        $result = $this->model->catat_keluar_halaman($id_pengerjaan);
        $this->json_output([
            'result' => 'true',
            'keluar_halaman' => (int)$result['keluar_halaman'],
            'reset_jawaban' => $result['reset_jawaban'] ? 'true' : 'false'
        ]);
    }

    public function kumpulkan($id_pengerjaan = null)
    {
        if (!$id_pengerjaan) {
            $this->json_output([
                'result' => 'false',
                'message' => 'Data pengerjaan tidak ditemukan.'
            ]);
        }

        $jawaban = $this->input->post('jawaban');
        $status = $this->input->post('status_pengerjaan') == 'Waktu Habis' ? 'Waktu Habis' : 'Selesai';
        $result = $this->model->kumpulkan_jawaban($id_pengerjaan, $jawaban, $status);

        $this->json_output([
            'result' => !empty($result['status']) ? 'true' : 'false',
            'message' => $result['message'] ?? (!empty($result['status']) ? 'Jawaban berhasil dikumpulkan.' : 'Jawaban gagal dikumpulkan.'),
            'id_pengerjaan' => $id_pengerjaan,
            'redirect' => base_url('pengerjaan/hasil/' . $id_pengerjaan)
        ]);
    }

    public function hasil($id_pengerjaan = null)
    {
        if (!$id_pengerjaan) {
            redirect('riwayat');
        }

        $data['title'] = 'Hasil Pengerjaan';
        $data['hasil'] = $this->model->pengerjaan_detail($id_pengerjaan);
        if (!$data['hasil']) {
            show_404();
        }

        $data['analisa_materi'] = $this->model->analisa_materi_hasil($id_pengerjaan);
        $data['preview_diizinkan'] = $this->model->preview_diizinkan($id_pengerjaan);
        $data['preview_jawaban'] = $this->model->preview_jawaban($id_pengerjaan);

        $this->load->view('template_siswa/header', $data);
        $this->load->view('portal_siswa/hasil', $data);
        $this->load->view('template_siswa/footer');
    }
}
?>
