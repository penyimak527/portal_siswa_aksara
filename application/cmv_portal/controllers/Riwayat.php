<?php
class Riwayat extends CI_Controller
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

    public function index()
    {
        $data['title'] = 'Riwayat Pengerjaan';
        $data['mapel'] = $this->model->mapel_result();
        $data['tahun_ajaran'] = $this->model->tahun_ajaran_result();

        $this->load->view('template_siswa/header', $data);
        $this->load->view('portal_siswa/riwayat', $data);
        $this->load->view('template_siswa/footer');
    }

    public function result()
    {
        $data = $this->model->riwayat_result();
        $this->json_output([
            'result' => 'true',
            'data' => $data,
            'message' => count($data) > 0 ? 'Data riwayat berhasil dimuat.' : 'Belum ada riwayat pengerjaan.'
        ]);
    }
}
?>
