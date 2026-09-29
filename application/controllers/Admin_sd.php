<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * =============================================================================
 * PANEL ADMIN SD K CITRA BANGSA MANDIRI
 * =============================================================================
 * Panel admin terpisah khusus unit SD. Desainnya sama persis dengan admin
 * yayasan, tapi branding dan menunya disesuaikan untuk unit SD.
 *
 * Fitur:
 * - Dashboard (statistik kunjungan + pendaftar baru jenjang SD)
 * - Kelola Profil SD (tabel konten_sd: visi, misi, sambutan, dll)
 * - Login terpisah di /admin-sd/login
 *
 * Hak akses: hanya role 'admin_sd' dan 'administrator'/'default'.
 * =============================================================================
 */
class Admin_sd extends CI_Controller
{
    private $allowed_roles = ['admin_sd', 'administrator', 'default'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_data');
        $this->load->helper('date');
        $this->load->model('visitors_log');
        $ip_address = $this->input->ip_address();
        $this->visitors_log->log_visitor($ip_address);
    }

    /** Periksa akses — redirect ke login kalau belum login atau role salah */
    private function check_access()
    {
        $role = $this->session->userdata('role');
        if (!$role || !in_array($role, $this->allowed_roles, true)) {
            redirect(base_url('admin-sd/login'));
            exit();
        }
    }

    // =========================================================================
    // LOGIN
    // =========================================================================

    /** Halaman login admin SD */
    public function login()
    {
        if (in_array($this->session->userdata('role'), $this->allowed_roles, true)) {
            redirect(base_url('admin-sd'));
            return;
        }

        if ($this->input->method() === 'post') {
            $this->_do_login();
            return;
        }

        $data = [
            'unit_nama'  => 'SD K Citra Bangsa Mandiri',
            'unit_kode'  => 'sd',
            'unit_logo'  => 'sd.png',
            'unit_warna' => '#2980b9',
            'login_url'  => base_url('admin-sd/login'),
        ];
        $this->load->view('admin_sd/login', $data);
    }

    /** Proses login */
    private function _do_login()
    {
        $username = trim((string) $this->input->post('username', TRUE));
        $password = (string) $this->input->post('password', FALSE);

        $user = $this->db->get_where('auth', ['username' => $username])->row_array();

        $hash = !empty($user['password'])
            ? $user['password']
            : '$2y$10$usesomesillystringforsalt0000000000000000000000000000000000';

        if (!empty($user['id_user']) && password_verify($password, $hash)) {
            if (!in_array($user['role'], $this->allowed_roles, true)) {
                $this->session->set_flashdata('error', 'Akun ini tidak memiliki akses ke panel admin SD.');
                redirect(base_url('admin-sd/login'));
                return;
            }

            $this->session->sess_regenerate(TRUE);
            $this->session->set_userdata([
                'id_user'  => $user['id_user'],
                'username' => $user['username'],
                'role'     => $user['role']
            ]);
            redirect(base_url('admin-sd'));
            return;
        }

        $this->session->set_flashdata('error', 'Username atau password salah.');
        redirect(base_url('admin-sd/login'));
    }

    /** Logout */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('admin-sd/login'));
    }

    // =========================================================================
    // DASHBOARD
    // =========================================================================

    public function index()
    {
        $this->check_access();

        $data['menu'] = 'dashboard';
        $data['daily_visits']   = $this->visitors_log->get_daily_visits();
        $data['monthly_visits'] = $this->visitors_log->get_monthly_visits();
        $data['yearly_visits']  = $this->visitors_log->get_yearly_visits();

        $this->load->view('admin_sd/header', $data);
        $this->load->view('admin_sd/index', $data);
        $this->load->view('admin_sd/modals');
        $this->load->view('admin_sd/footer');
    }

    // =========================================================================
    // KELOLA PROFIL SD
    // =========================================================================

    public function profil()
    {
        $this->check_access();
        $this->_ensure_table();

        $data_konten = $this->db->get('konten_sd')->result_array();
        $data = [
            'menu'        => 'profil',
            'data_konten' => $data_konten,
        ];
        $this->load->view('admin_sd/header', $data);
        $this->load->view('admin_sd/profil', $data);
        $this->load->view('admin_sd/modals');
        $this->load->view('admin_sd/footer');
    }

    public function add_konten()
    {
        $this->check_access();
        $this->_ensure_table();

        if ($this->input->method() === 'post') {
            $judul  = htmlspecialchars($this->input->post('judul_konten'));
            $sub    = htmlspecialchars($this->input->post('sub_judul_konten'));
            $isi    = $this->input->post('isi_konten');
            $jenis  = htmlspecialchars($this->input->post('jenis_konten'));

            $existing = $this->db->get_where('konten_sd', ['jenis_konten' => $jenis])->num_rows();
            if ($existing > 0) {
                $this->session->set_flashdata('error', 'Bagian tersebut sudah terisi!');
            } else {
                $this->db->insert('konten_sd', [
                    'judul_konten'     => $judul,
                    'sub_judul_konten' => $sub,
                    'isi_konten'       => $isi,
                    'jenis_konten'     => $jenis,
                ]);
                $this->session->set_flashdata('success', 'Profil berhasil disimpan.');
            }
            redirect('admin-sd/profil');
        }
    }

    public function update_konten()
    {
        $this->check_access();
        $this->_ensure_table();

        if ($this->input->method() === 'post') {
            $id     = (int) $this->input->post('id_konten');
            $judul  = htmlspecialchars($this->input->post('judul_konten'));
            $sub    = htmlspecialchars($this->input->post('sub_judul_konten'));
            $isi    = $this->input->post('isi_konten');
            $jenis  = htmlspecialchars($this->input->post('jenis_konten'));

            $this->db->where('id_konten', $id)->update('konten_sd', [
                'judul_konten'     => $judul,
                'sub_judul_konten' => $sub,
                'isi_konten'       => $isi,
                'jenis_konten'     => $jenis,
            ]);
            $this->session->set_flashdata('success', 'Profil berhasil diperbarui.');
            redirect('admin-sd/profil');
        }
    }

    public function delete_konten()
    {
        $this->check_access();

        $id = (int) $this->input->post('id_konten');
        if ($id) {
            $this->db->where('id_konten', $id)->delete('konten_sd');
            $this->session->set_flashdata('success', 'Data profil berhasil dihapus.');
        }
        redirect('admin-sd/profil');
    }

    // =========================================================================
    // HELPER
    // =========================================================================

    private function _ensure_table()
    {
        if (!$this->db->table_exists('konten_sd')) {
            $this->db->query("
                CREATE TABLE `konten_sd` (
                    `id_konten` INT(11) NOT NULL AUTO_INCREMENT,
                    `judul_konten` VARCHAR(255) NOT NULL,
                    `sub_judul_konten` VARCHAR(255) DEFAULT NULL,
                    `isi_konten` LONGTEXT NOT NULL,
                    `jenis_konten` VARCHAR(50) NOT NULL,
                    PRIMARY KEY (`id_konten`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
        }
    }
}
