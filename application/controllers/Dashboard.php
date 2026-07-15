<?php
class Dashboard extends CI_Controller
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

    public function index()
    {
        $data['title'] = 'Dashboard Siswa';
        $data['siswa'] = $this->model->siswa();
        $data['tahun_ajaran'] = $this->model->tahun_ajaran_aktif();
        $data['ringkasan'] = $this->model->ringkasan_dashboard();
        $data['mapel'] = $this->model->mapel_result();
        $data['sesi_tersedia'] = $this->model->sesi_tersedia(2);
        $data['riwayat_terbaru'] = $this->model->riwayat_terbaru(2);

        $this->load->view('template_siswa/header', $data);
        $this->load->view('portal_siswa/dashboard', $data);
        $this->load->view('template_siswa/footer');
    }

    public function materi_dashboard_result()
    {
        $data = $this->model->materi_dashboard_result();

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }
}
?>
