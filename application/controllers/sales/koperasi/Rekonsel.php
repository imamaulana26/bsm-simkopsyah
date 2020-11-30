<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class Rekonsel extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		is_login();
	}

	public function rekon_channeling($id)
	{
		$id = base64_decode($id);
		$page = 'koperasi/rekon_channeling';

		$data['title'] = 'Rekonsialisasi Koperasi Channeling';
		$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('sales/home') . '">Home</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item"><a href="' . site_url('sales/koperasi-channeling') . '">Koperasi</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item active">Rekonsialisasi</li>';

		$data['bank'] = $this->db->select('a.id, count(b.noloan_anggota) as anggota, sum(b.nom_pencairan) as plafond, sum(b.os_pokok) as ospokok, a.tgl_ospokok')->from('tbl_koperasi a')
			->join('tbl_anggota_channeling b', 'a.id = b.id_koperasi', 'left')
			->join('tbl_area c', 'a.kd_area = c.kd_area', 'left')
			->where(['a.id' => $id, 'a.fk_kode_ao' => $this->session->userdata('kd_ao'), 'a.jns_pembiayaan' => 'Channeling', 'a.status' => 'Proses Rekonsialisasi'])
			->get()->row_array();

		$data['koperasi'] = $this->db->select('id_koperasi, count(noloan) as anggota, sum(plafond) as plafond, sum(ospokok) as ospokok, tgl_ospokok')->from('tbl_rekon_channeling')
			->where(['id_koperasi' => $id, 'kode_ao' => $this->session->userdata('kd_ao')])
			->get()->row_array();

		$data['li_bank'] = $this->db->select('id_koperasi, noloan_anggota, nm_anggota, nom_pencairan as plafond, os_pokok as ospokok')->from('tbl_anggota_channeling')
		->where(['id_koperasi' => $id])
		->get()->result_array();

		$data['li_koperasi'] = $this->db->select('id_koperasi, noloan, nm_anggota, plafond, ospokok')->from('tbl_rekon_channeling')
		->where(['id_koperasi' => $id])
			->get()->result_array();

		$this->load->view($page, $data);
	}

	// public function rekon_channeling_backup($id)
	// {
	// 	$page = 'koperasi/rekon_channeling';

	// 	$data['title'] = 'Rekonsialisasi Koperasi Channeling';
	// 	$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('sales/home') . '">Home</a></li>';
	// 	$data['breadcrumb'] .= '<li class="breadcrumb-item"><a href="' . site_url('sales/koperasi-channeling') . '">Koperasi</a></li>';
	// 	$data['breadcrumb'] .= '<li class="breadcrumb-item active">Rekonsialisasi</li>';

	// 	$data['bank'] = $this->db->select('a.id, count(b.noloan_anggota) as anggota, sum(b.nom_pencairan) as plafond, sum(b.os_pokok) as ospokok, a.tgl_ospokok')->from('tbl_koperasi a')
	// 		->join('tbl_anggota_channeling b', 'a.id = b.id_koperasi', 'left')
	// 		->join('tbl_area c', 'a.kd_area = c.kd_area', 'left')
	// 		->where(['a.id' => base64_decode($id), 'a.fk_kode_ao' => $this->session->userdata('kd_ao'), 'a.jns_pembiayaan' => 'Channeling', 'a.status' => 'Proses Rekonsialisasi'])
	// 		->get()->row_array();

	// 	$data['koperasi'] = $this->db->select('id_koperasi, count(noloan) as anggota, sum(plafond) as plafond, sum(ospokok) as ospokok, tgl_ospokok')->from('tbl_rekon_channeling')
	// 		->where(['id_koperasi' => base64_decode($id), 'kode_ao' => $this->session->userdata('kd_ao')])
	// 		->get()->row_array();

	// 	$data['li_bank'] = $this->db->select('b.*')->from('tbl_koperasi a')
	// 		->join('tbl_anggota_channeling b', 'a.id = b.id_koperasi', 'left')
	// 		->join('tbl_area c', 'a.kd_area = c.kd_area', 'left')
	// 		->where(['a.id' => base64_decode($id), 'a.fk_kode_ao' => $this->session->userdata('kd_ao'), 'a.jns_pembiayaan' => 'Channeling', 'a.status' => 'Proses Rekonsialisasi'])
	// 		->get()->result_array();

	// 	$data['li_koperasi'] = $this->db->select('*')->from('tbl_rekon_channeling')
	// 		->where(['id_koperasi' => base64_decode($id), 'kode_ao' => $this->session->userdata('kd_ao')])
	// 		->get()->result_array();

	// 	$this->load->view($page, $data);
	// }
}
