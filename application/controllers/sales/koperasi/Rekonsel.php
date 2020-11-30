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

		$data['li_bank'] = $this->db->select('id_koperasi, noloan_anggota, nm_anggota, tenor, tgl_pencairan, nom_pencairan as plafond, tgl_ospokok, os_pokok as ospokok')->from('tbl_anggota_channeling')
			->where(['id_koperasi' => $id])
			->get()->result_array();

		$data['li_koperasi'] = $this->db->select('id_koperasi, noloan, nm_anggota, tenor, tgl_pencairan, plafond, tgl_ospokok, ospokok')->from('tbl_rekon_channeling')
			->where(['id_koperasi' => $id])
			->get()->result_array();

		$this->load->view($page, $data);
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

	public function update($id)
	{
		$id = base64_decode($id);
		$rekon = $this->db->get_where('tbl_rekon_channeling', ['id_koperasi' => $id])->result_array();

		$this->db->trans_begin();
		foreach ($rekon as $val) {
			$this->db->update('tbl_anggota_channeling', ['os_pokok' => $val['ospokok'], 'tgl_ospokok' => $val['tgl_ospokok']], ['id_koperasi' => $id]);
		}
		$this->db->update('tbl_koperasi', ['status' => 'Terekonsialisasi', 'tgl_rekon' => date('Y-m-d')], ['id' => $id]);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			redirect(site_url('sales/koperasi-channeling/rekonsel/' . base64_encode($id)));
		} else {
			$this->db->trans_commit();
			redirect(site_url('sales/koperasi-channeling'));
		}
	}

	public function reject($id)
	{
		$id = base64_decode($id);

		$this->db->delete('tbl_rekon_channeling', ['id_koperasi' => $id]);
		$this->db->update('tbl_koperasi', ['status' => 'Belum Terekonsialisasi'], ['id' => $id]);

		redirect(site_url('sales/koperasi-channeling'));
	}
}
