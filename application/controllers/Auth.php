<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->helper(array('cookie', 'string'));
	}

	public function index()
	{
		$cookie = get_cookie('remember');
		$sess = $this->session->userdata('nip');

		if ($cookie != '' && $sess != '') {
			$data = $this->db->get_where('tbl_user', ['cookies' => $cookie])->row_array();

			$role = array(1 => 'admin', 2 => 'staff', 3 => 'sales');

			redirect(site_url($role[$data['role_id']] . '/home'));
		} else {
			$page = 'v_login';
			$data = array(
				'title' => 'BSM - SIMKOPSYAH | Log In'
			);

			$this->load->view($page, $data);
		}
	}

	public function login()
	{
		$remember = input('remember');
		$username = input('username');
		$password = md5(input('password'));
		$auth = $this->db->get_where('tbl_user', ['nip' => $username]);
		$cek = $auth->row_array();

		$db_time = date_create($cek['last_login']);
		$time = date_create();
		$diff = date_diff($db_time, $time);

		$msg = '';

		if ($auth->num_rows() > 0) {
			if ($cek['password'] == $password) {
				if ($cek['IsDelete'] == 0 && $cek['status'] == 0) {
					if ($cek['is_active'] == 0) { // user tidak sedang login
						if ($remember) {
							$key = random_string('alnum', 64);
							set_cookie('remember', $key, 3600 * 24 * 1); // set expired 1 hari kedepan
						} else {
							$this->db->update('tbl_user', ['cookies' => null], ['nip' => $username]);
						}

						$sess = array(
							'nip' => $cek['nip'],
							'kd_ao' => $cek['kode_ao'],
							'nama' => $cek['nama'],
							'email' => $cek['email'],
							'jabatan' => $cek['jabatan']
						);

						if ($cek['role_id'] == 1) {
							$sess['role'] = 'admin';
						} else if ($cek['role_id'] == 2) {
							$sess['role'] = 'staff';
						} else {
							$sess['role'] = 'sales';
						}

						$this->session->set_userdata($sess);

						$this->db->update('tbl_user', ['is_active' => 1, 'cookies' => $key], ['nip' => $username]);

						if ($cek['role_id'] == 1) {
							redirect(site_url('admin/home'));
						} else if ($cek['role_id'] == 2) {
							redirect(site_url('staff/home'));
						} else {
							redirect(site_url('sales/home'));
						}
					} else { // user sedang login
						// var_dump($diff->i . ' min - ' . $diff->h . ' hour - ' . $diff->d . ' day'); die;
						if ($diff->i >= 30 || $diff->h > 1 || $diff->d) {
							$this->db->update('tbl_user', ['is_active' => 0], ['nip' => $username]);
						}
						$msg = 'Akun sedang digunakan, silahkan coba lagi!';
						$this->session->set_userdata('msg', $msg);
						$this->index();
					}
				} else {
					$msg = 'Akun sudah tidak aktif!';
					$this->session->set_userdata('msg', $msg);
					$this->index();
				}
			} else {
				$msg = 'Username atau password salah!';
				$this->session->set_userdata('msg', $msg);
				$this->index();
			}
		} else {
			$msg = 'Akun tidak ditemukan!';
			$this->session->set_userdata('msg', $msg);
			$this->index();
		}
	}

	public function logout()
	{
		$this->db->update('tbl_user', ['is_active' => 0, 'last_login' => date('Y-m-d H:i:s'), 'cookies' => null], ['nip' => $this->session->userdata('nip')]);

		session_destroy();
		delete_cookie('remember');
		redirect(site_url('auth'));
	}
}
