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
		$page = 'koperasi/channeling/rekonsel';

		$cek = $this->db->get_where('tbl_koperasi', ['id' => $id, 'status' => 'Proses Rekonsiliasi'])->num_rows();
		if ($cek > 0) {
			$data['title'] = 'Rekonsiliasi Koperasi Channeling';
			$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('sales/home') . '">Home</a></li>';
			$data['breadcrumb'] .= '<li class="breadcrumb-item"><a href="' . site_url('sales/koperasi-channeling') . '">Koperasi</a></li>';
			$data['breadcrumb'] .= '<li class="breadcrumb-item active">Rekonsiliasi</li>';

			$qry_bank = "select id_koperasi as id, count(noloan_anggota) as anggota, sum(nom_pencairan) as plafond, tgl_ospokok, sum(os_pokok) as ospokok from tbl_anggota_channeling ";
			$qry_bank .= "where id_koperasi = " . $id . " and ficmisDate = (select max(ficmisDate) as ficmisDate from tbl_anggota_channeling where id_koperasi = " . $id . ")";
			$data['bank'] = $this->db->query($qry_bank)->row_array();

			$data['koperasi'] = $this->db->select('id_koperasi, count(noloan) as anggota, sum(plafond) as plafond, sum(ospokok) as ospokok, tgl_ospokok')->from('tbl_rekon_channeling')
				->where(['id_koperasi' => $id, 'kode_ao' => $_SESSION['kd_ao'], 'rekon_date' => '0000-00-00'])
				->get()->row_array();

			$qry_li_bank = "select id_koperasi, noloan_anggota, nm_anggota, tenor, tgl_pencairan, nom_pencairan as plafond, tgl_ospokok, os_pokok as ospokok from tbl_anggota_channeling ";
			$qry_li_bank .= "where id_koperasi = " . $id . " and ficmisDate = (select max(ficmisDate) as ficmisDate from tbl_anggota_channeling where id_koperasi = " . $id . ")";
			$data['li_bank'] = $this->db->query($qry_li_bank)->result_array();

			$data['li_koperasi'] = $this->db->select('id_koperasi, noloan, nm_anggota, tenor, tgl_pencairan, plafond, tgl_ospokok, ospokok')->from('tbl_rekon_channeling')
				->where(['id_koperasi' => $id, 'kode_ao' => $_SESSION['kd_ao'], 'rekon_date' => '0000-00-00'])
				->get()->result_array();

			$this->load->view($page, $data);
		} else {
			return redirect(site_url('sales/koperasi-channeling'));
		}
	}

	public function export($id)
	{
		$id = base64_decode($id);

		$li_bank = $this->db->select('id_koperasi, noloan_anggota, nm_anggota, tenor, tgl_pencairan, nom_pencairan as plafond, tgl_ospokok, os_pokok as ospokok')->from('tbl_anggota_channeling')
			->where(['id_koperasi' => $id])->get();

		$li_koperasi = $this->db->select('id_koperasi, noloan, nm_anggota, tenor, tgl_pencairan, plafond, tgl_ospokok, ospokok, rekon_date')->from('tbl_rekon_channeling')
			->where(['id_koperasi' => $id])->get();

		if ($li_bank->num_rows() > 0 && $li_koperasi->num_rows() > 0) {
			$kop = $this->db->get_where('tbl_koperasi', ['id' => $id])->row_array();
			$dt_bank = $li_bank->result_array();
			$dt_koperasi = $li_koperasi->result_array();

			$filename = 'export-rekonsel-' . str_replace(' ', '-', strtolower($kop['nm_koperasi']));

			$nm_column = array(
				'REKON_DATE',
				'NOLOAN',
				'NAMA_ANGGOTA',
				'TGL_OSPOKOK',
				'SISA_TENOR',
				'PLAFOND',
				'OSPOKOK',
				'SISA_TENOR',
				'PLAFOND',
				'OSPOKOK'
			);

			$csv_header = '';
			foreach ($nm_column as $key => $col) {
				$csv_header .= $col . '|';
			}
			$csv_header .= "\n";

			$csv_row = '';
			$rekon_date = $dt_koperasi[0]['rekon_date'];
			$kolom = array_column($dt_koperasi, 'noloan');

			foreach ($dt_bank as $key => $val) {
				$cari = array_search($val['noloan_anggota'], $kolom);

				$csv_row .= $rekon_date . '|';
				$csv_row .= $val['noloan_anggota'] . '|';
				$csv_row .= $val['nm_anggota'] . '|';
				$csv_row .= $val['tgl_ospokok'] . '|';
				$csv_row .= $val['tenor'] . '|';
				$csv_row .= $val['plafond'] . '|';
				$csv_row .= $val['ospokok'] . '|';
				if ($cari !== false) {
					$csv_row .= $dt_koperasi[$cari]['tgl_ospokok'] . '|';
					$csv_row .= $dt_koperasi[$cari]['tenor'] . '|';
					$csv_row .= $dt_koperasi[$cari]['plafond'] . '|';
					$csv_row .= $dt_koperasi[$cari]['ospokok'] . '|';
				} else {
					$csv_row .= '0|';
					$csv_row .= '0|';
					$csv_row .= '0|';
					$csv_row .= '0|';
				}
				$csv_row .= "\n";
			}

			// var_dump($csv_row); die;

			/* Download as CSV File */
			header('Content-type: application/csv');
			header('Content-Disposition: attachment; filename=' . $filename . '.csv');
			echo $csv_header . $csv_row;
			exit;
		}
	}

	public function update()
	{
		$this->db->trans_start();
		$this->db->update('tbl_koperasi', ['tgl_rekon' => date('Y-m-d'), 'status' => 'Terekonsiliasi'], ['id' => input('id')]);
		$this->db->update('tbl_rekon_channeling', ['rekon_date' => date('Y-m-d')], ['id_koperasi' => input('id'), 'tgl_ospokok' => input('tgl_os')]);
		$this->db->update('tbl_anggota_channeling', ['tgl_rekon' => date('Y-m-d')], ['id_koperasi' => input('id'), 'tgl_ospokok' => input('tgl_os')]);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$this->session->set_flashdata('warning', 'Data Gagal Terekonsiliasi.');
		} else {
			$this->db->trans_commit();
			$this->session->set_flashdata('success', 'Data Berhasil Terekonsiliasi.');
		}

		redirect(site_url('sales/koperasi-channeling'));
	}

	public function reject($id)
	{
		$this->db->delete('tbl_rekon_channeling', ['id_koperasi' => $id, 'rekon_date' => '0000-00-00']);
		$this->db->update('tbl_koperasi', ['status' => 'Belum Terekonsiliasi'], ['id' => $id]);

		echo json_encode(['status' => true]); exit;	
	}


	// history hasil rekonsel
	public function rekap($id)
	{
		$data['title'] = 'Rekap Rekonsiliasi End User';
		$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('sales/home') . '">Home</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item"><a href="' . site_url('sales/koperasi-channeling') . '">Koperasi</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item active">Rekap</li>';

		$data['rekon'] = $this->db->select('id_koperasi, rekon_date, tgl_ospokok')->from('tbl_rekon_channeling')->where(['id_koperasi' => $id, 'rekon_date !=' => '0000-00-00'])->group_by('id_koperasi, rekon_date')->get()->result_array();

		$this->load->view('koperasi/channeling/rekonsel_report', $data);
	}

	public function result()
	{
		$id = input('id_kop');
		$tgl = input('bln_rekon');
		$page = 'koperasi/channeling/rekonsel_report';
		$this->session->set_flashdata('tgl_rekon', $tgl);

		// $data = $this->db->get_where('tbl_rekon_channeling', ['id_kopersai' => $id, 'tgl_ospokok' => $tgl])->num_rows();
		$data['title'] = 'Rekap Rekonsiliasi End User';
		$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('sales/home') . '">Home</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item"><a href="' . site_url('sales/koperasi-channeling') . '">Koperasi</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item active">Rekap</li>';

		$qry_bank = "select id_koperasi as id, count(noloan_anggota) as anggota, sum(nom_pencairan) as plafond, tgl_ospokok, sum(os_pokok) as ospokok from tbl_anggota_channeling ";
		$qry_bank .= "where tgl_ospokok = '" . $tgl . "' and id_koperasi = '" . $id . "'";
		$data['bank'] = $this->db->query($qry_bank)->row_array();

		$data['koperasi'] = $this->db->select('id_koperasi, count(noloan) as anggota, sum(plafond) as plafond, sum(ospokok) as ospokok, tgl_ospokok')->from('tbl_rekon_channeling')
			->where(['id_koperasi' => $id, 'kode_ao' => $_SESSION['kd_ao'], 'tgl_ospokok' => $tgl])
			->get()->row_array();

		$qry_li_bank = "select id_koperasi, noloan_anggota, nm_anggota, tenor, tgl_pencairan, nom_pencairan as plafond, tgl_ospokok, os_pokok as ospokok from tbl_anggota_channeling ";
		$qry_li_bank .= "where tgl_ospokok = '" . $tgl . "' and id_koperasi = '" . $id . "'";
		$data['li_bank'] = $this->db->query($qry_li_bank)->result_array();

		$data['li_koperasi'] = $this->db->select('id_koperasi, noloan, nm_anggota, tenor, tgl_pencairan, plafond, tgl_ospokok, ospokok')->from('tbl_rekon_channeling')
			->where(['id_koperasi' => $id, 'kode_ao' => $_SESSION['kd_ao'], 'tgl_ospokok' => $tgl])
			->get()->result_array();

		$data['rekon'] = $this->db->select('id_koperasi, rekon_date, tgl_ospokok')->from('tbl_rekon_channeling')->where(['id_koperasi' => $id, 'rekon_date !=' => '0000-00-00'])->group_by('id_koperasi, rekon_date')->get()->result_array();

		$this->load->view($page, $data);
	}
}
