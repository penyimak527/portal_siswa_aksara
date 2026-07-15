<?php
class Perkembangan extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('M_portal_siswa', 'model');
    }

    public function index()
    {
        if (($this->session->userdata('siswa')['logged_in'] ?? null) == null) {
            redirect('/');
        }

        $data['title'] = 'Perkembangan Siswa';
        $data['kelas'] = $this->model->kelas_riwayat_result();
        $data['mapel'] = $this->model->mapel_result();

        $this->load->view('template_siswa/header', $data);
        $this->load->view('portal_siswa/perkembangan', $data);
        $this->load->view('template_siswa/footer');
    }

    public function materi_bulanan_result()
    {
        $data = $this->model->materi_bulanan_result();

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    public function perkembangan_result()
    {
        $data = $this->model->perkembangan_result();

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }
}
?>
