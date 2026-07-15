<?php
class Sesi extends CI_Controller
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
        $data['title'] = 'Sesi Tersedia';
        $data['siswa'] = $this->model->siswa();
        $data['mapel'] = $this->model->mapel_result();
        $data['ada_tunggakan'] = $this->model->ada_tunggakan_bulan_lalu();

        $this->load->view('template_siswa/header', $data);
        $this->load->view('portal_siswa/sesi', $data);
        $this->load->view('template_siswa/footer');
    }

    public function result()
    {
        $data = $this->model->sesi_tersedia();
        $data = array(
            'result' => 'true',
            'data' => $data,
            'ada_tunggakan' => $this->model->ada_tunggakan_bulan_lalu() ? 'true' : 'false',
            'message' => count($data) > 0 ? 'Data sesi berhasil dimuat.' : 'Tidak ada sesi yang bisa dikerjakan.'
        );
         $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    public function cek_akses()
    {
        $data = $this->model->cek_akses();
         $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    public function akses_ditolak()
    {
        $data['title'] = 'Akses Sesi Ditolak';
        $this->load->view('template_siswa/header', $data);
        $this->load->view('portal_siswa/akses_ditolak', $data);
        $this->load->view('template_siswa/footer');
    }

    public function konfirmasi($id_sesi = null)
    {
        if (!$id_sesi) {
            redirect('sesi');
        }

        if ($this->model->ada_tunggakan_bulan_lalu()) {
            redirect('sesi/akses_ditolak');
        }

        if ($this->model->siswa_sudah_mengerjakan($id_sesi)) {
            $this->session->set_flashdata('message', '<div class="alert alert-warning rounded-3">Sesi ini sudah dikerjakan maksimal 2 kali.</div>');
            redirect('sesi');
        }

        $data['title'] = 'Konfirmasi Pengerjaan';
        $data['sesi'] = $this->model->sesi_detail($id_sesi);

        if (!$data['sesi']) {
            show_404();
        }

        $this->load->view('template_siswa/header', $data);
        $this->load->view('portal_siswa/konfirmasi', $data);
        $this->load->view('template_siswa/footer');
    }
}
?>