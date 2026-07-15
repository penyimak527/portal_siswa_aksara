<?php
class Lainnya extends CI_Controller
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
        $data['title'] = 'Lainnya';

        $this->load->view('template_siswa/header', $data);
        $this->load->view('portal_siswa/lainnya', $data);
        $this->load->view('template_siswa/footer');
    }
}
?>
