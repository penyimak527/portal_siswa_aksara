<?php
class Profil extends CI_Controller
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
        $data['title'] = 'Profil Siswa';
        $data['siswa'] = $this->model->siswa();
        $data['tahun_ajaran'] = $this->model->tahun_ajaran_aktif();

        $this->load->view('template_siswa/header', $data);
        $this->load->view('portal_siswa/profil', $data);
        $this->load->view('template_siswa/footer');
    }

    public function update_password()
    {
        $result = $this->model->update_password(
            $this->input->post('password_lama'),
            $this->input->post('password_baru'),
            $this->input->post('konfirmasi_password')
        );

        $this->json_output([
            'result' => !empty($result['status']) ? 'true' : 'false',
            'message' => $result['message'] ?? (!empty($result['status']) ? 'Password berhasil diperbarui.' : 'Password gagal diperbarui.')
        ]);
    }
}
?>
