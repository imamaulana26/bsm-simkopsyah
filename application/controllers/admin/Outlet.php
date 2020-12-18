<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Outlet extends CI_Controller
{
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

		$page = 'admin/v_outlet';
		$qry = $this->db->select('*')->from('tbl_cabang a')
			->join('tbl_area b', 'a.fk_kd_area = b.kd_area', 'left')
			->join('tbl_region c', 'c.id_region = b.fk_id_region', 'left')
			->where(['a.IsDelete' => 0])
			->order_by('c.id_region')->get()->result_array();

		$data['title'] = 'Management Outlet';
		$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('admin/home') . '">Home</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item active">' . $data['title'] . '</li>';
		$data['data'] = $qry;

		$this->load->view($page, $data);
	}

	public function search()
	{
		$data = array();

		$page = 'admin/v_outlet';
		$qry = $this->db->select('*')->from('tbl_cabang a')
		->join('tbl_area b', 'a.fk_kd_area = b.kd_area', 'left')
		->join('tbl_region c', 'c.id_region = b.fk_id_region', 'left')
		->where(['c.id_region' => input('fil_region'), 'b.kd_area' => input('fil_area'), 'a.IsDelete' => 0])->order_by('c.id_region')->get()->result_array();

		$data['title'] = 'Management Outlet';
		$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('admin/home') . '">Home</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item active">' . $data['title'] . '</li>';
		$data['data'] = $qry;

		// $this->session->set_flashdata('region', input('fil_region'));
		// $this->session->set_flashdata('area', input('fil_area'));

		$this->load->view($page, $data);
	}
}
