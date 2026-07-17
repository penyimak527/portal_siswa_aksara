<?php
class Login extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('M_login', 'model');
        $this->load->model('M_helper', 'helper_model');
    }

    public function index()
    {
        if (($this->session->userdata('siswa')['logged_in'] ?? null) == true) {
            redirect('dashboard');
        }

        $data['title'] = 'Login Siswa';
        $this->load->view('login', $data);
    }

    public function masuk()
    {
        $nis = $this->input->post('nis');
        $password = $this->input->post('password');
        $result = $this->model->login($nis, $password);

        if ($result['status']) {
            $this->session->set_userdata('siswa', $result['data']);
            redirect('dashboard');
        }

        $this->session->set_flashdata('message', '<div class="alert alert-danger rounded-3">' . $result['message'] . '</div>');
        redirect('/');
    }

    public function keluar()
    {
        $this->session->unset_userdata('siswa');
        redirect('/');
    }
}
?>
