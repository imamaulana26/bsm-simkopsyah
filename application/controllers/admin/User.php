<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
	private $_table = 'tbl_user';

	function __construct()
	{
		parent::__construct();
		is_login();
	}

	private function validasi()
	{
		$data = array();
		$data['inputerror'] = array();
		$data['error'] = array();
		$data['status'] = true;

		if (input('nip_user') == '') {
			$data['inputerror'][] = 'nip_user';
			$data['error'][] = 'NIP user harus diisi';
			$data['status'] = false;
		} else if (strlen(input('nip_user')) < 9) {
			$data['inputerror'][] = 'nip_user';
			$data['error'][] = 'NIP user tidak boleh kurang dari 9 digit';
			$data['status'] = false;
		}

		if (input('kd_ao_user') == '') {
			$data['inputerror'][] = 'kd_ao_user';
			$data['error'][] = 'Kode AO harus diisi';
			$data['status'] = false;
		} else if (strlen(input('kd_ao_user')) < 8) {
			$data['inputerror'][] = 'kd_ao_user';
			$data['error'][] = 'Kode AO tidak boleh kurang dari 8 digit';
			$data['status'] = false;
		}

		if (input('nm_user') == '') {
			$data['inputerror'][] = 'nm_user';
			$data['error'][] = 'Nama lengkap harus diisi';
			$data['status'] = false;
		} else if (!preg_match('/^[A-Z ]+$/', strtoupper(input('nm_user')))) {
			$data['inputerror'][] = 'nm_user';
			$data['error'][] = 'Nama lengkap tidak valid, harus alphabet';
			$data['status'] = false;
		}

		if (input('email_user') == '') {
			$data['inputerror'][] = 'email_user';
			$data['error'][] = 'Email harus diisi';
			$data['status'] = false;
		} else if (!preg_match('/^[a-z0-9]+$/', input('email_user'))) {
			$data['inputerror'][] = 'email_user';
			$data['error'][] = 'Email tidak valid';
			$data['status'] = false;
		}

		$cmb = array('jbtn_user', 'region_user', 'area_user');
		foreach ($cmb as $cmb) {
			if (input($cmb) == '') {
				$data['inputerror'][] = $cmb;
				$data['error'][] = '';
				$data['status'] = false;
			}
		}

		if ($data['status'] === false) {
			echo json_encode($data);
			exit();
		}
	}

	public function index()
	{
		$data = array();

		$page = 'admin/v_user';
		$qry = $this->db->select('*')->from($this->_table . ' a')
			->join('tbl_cabang b', 'a.outlet = b.kd_cabang', 'left')
			->join('tbl_area c', 'c.kd_area = b.fk_kd_area', 'left')
			->join('tbl_region d', 'd.id_region = c.fk_id_region', 'left')
			->where(['a.IsDelete' => 0, 'a.role_id >' => 1])
			->order_by('d.id_region')->get()->result_array();

		$data['title'] = 'Management User';
		$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('admin/home') . '">Home</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item active">' . $data['title'] . '</li>';
		$data['user'] = $qry;

		$this->load->view($page, $data);
	}

	public function search()
	{
		$data = array();

		$page = 'admin/v_user';
		$qry = $this->db->select('*')->from($this->_table . ' a')
			->join('tbl_cabang b', 'a.outlet = b.kd_cabang', 'left')
			->join('tbl_area c', 'c.kd_area = b.fk_kd_area', 'left')
			->join('tbl_region d', 'd.id_region = c.fk_id_region', 'left')
			->where(['b.fk_id_region' => input('fil_region'), 'b.fk_kd_area' => input('fil_area'), 'a.IsDelete' => 0])->order_by('d.id_region')->get()->result_array();

		$data['title'] = 'Management User';
		$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('admin/home') . '">Home</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item active">' . $data['title'] . '</li>';
		$data['user'] = $qry;

		$this->load->view($page, $data);
	}

	public function getUser($key)
	{
		$data = $this->db->select('*')->from($this->_table . ' a')
			->join('tbl_cabang b', 'a.outlet = b.kd_cabang', 'left')
			->join('tbl_area c', 'c.kd_area = b.fk_kd_area', 'left')
			->join('tbl_region d', 'd.id_region = c.fk_id_region', 'left')
			->where(['a.nip' => $key])->get()->row_array();

		echo json_encode($data);
		exit;
	}

	public function insert()
	{
		$this->validasi();

		$data = array(
			'nip' => input('nip_user'),
			'kode_ao' => input('kd_ao_user'),
			'nama' => input('nm_user'),
			'email' => input('email_user') . '@bsm.co.id',
			'outlet' => substr(input('area_user'), 0, -1)
		);

		$this->db->update($this->_table, $data);
		echo json_encode(['status' => true, 'msg' => 'Data user telah berhasil disimpan!']);
		exit;
	}

	public function update()
	{
		$this->validasi();

		$key = input('id_user');
		$data = array(
			'nip' => input('nip_user'),
			'kode_ao' => input('kd_ao_user'),
			'nama' => input('nm_user'),
			'email' => input('email_user') . '@bsm.co.id',
			'outlet' => substr(input('area_user'), 0, -1)
		);

		$this->db->update($this->_table, $data, ['id_user' => $key]);
		echo json_encode(['status' => true, 'msg' => 'Data user telah berhasil diperbarui!']);
		exit;
	}

	public function delete($key)
	{
		$this->db->update($this->_table, ['IsDelete' => 1], ['nip' => $key]);
		echo json_encode(['status' => true, 'msg' => 'Data user telah berhasil dihapus!']);
		exit;
	}

	public function upd_status()
	{
		$id = input('id');

		$result = $this->db->get_where('tbl_user', ['nip' => $id, 'status' => '1']);
		if ($result->num_rows() > 0) {
			$msg = 'User telah berhasil di aktifkan!';
			$this->db->update('tbl_user', ['status' => '0'], ['nip' => $id]);
		} else {
			$msg = 'User telah berhasil di non-aktifkan!';
			$this->db->update('tbl_user', ['status' => '1'], ['nip' => $id]);
		}

		echo json_encode(['msg' => $msg]);
		exit;
	}
}
