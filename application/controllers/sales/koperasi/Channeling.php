<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class Channeling extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		is_login();
	}

	public function index()
	{
		$page = 'koperasi/channeling';

		$data['title'] = 'Daftar Koperasi Channeling';
		$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('sales/home') . '">Home</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item active">Koperasi</li>';

		$data['list_koperasi'] = $this->db->select('a.*, c.nm_area, count(b.nocif_anggota) as anggota, sum(b.nom_pencairan) as plafond, sum(b.os_pokok) as ospokok, sum(b.tunggakan) as tunggakan')->from('tbl_koperasi a')
			->join('tbl_anggota_channeling b', 'a.id = b.id_koperasi', 'left')
			->join('tbl_area c', 'a.kd_area = c.kd_area', 'left')
			->where(['a.fk_kode_ao' => $this->session->userdata('kd_ao'), 'a.jns_pembiayaan' => 'Channeling'])
			->group_by('a.rek_pembayaran, a.tahap_pencairan')->order_by('a.id', 'asc')
			->get()->result_array();

		$data['list_rekon'] = $this->db->select('count(distinct(id_koperasi)) as rekon')->from('tbl_rekon_channeling')
			->where(['kode_ao' => $this->session->userdata('kd_ao')])
			->get()->row_array();

		foreach ($data['list_koperasi'] as $val) {
			$this->db->update(
				'tbl_koperasi',
				[
					'nom_pencairan' => $val['plafond'],
					'os_pokok' => $val['ospokok'],
					'tunggakan' => $val['tunggakan']
				],
				['id' => $val['id']]
			);
		}

		$this->load->view($page, $data);
	}

	// module controller koperasi
	private function _validasi_koperasi()
	{
		$data = array();
		$data['inputerror'] = array();
		$data['error'] = array();
		$data['status'] = true;

		if (input('rek_pembayaran') == '') {
			$data['inputerror'][] = 'rek_pembayaran';
			$data['error'][] = 'Rek. pembayaran harus diisi';
			$data['status'] = false;
		} else if (strlen(input('rek_pembayaran')) != 10) {
			$data['inputerror'][] = 'rek_pembayaran';
			$data['error'][] = 'No kontrak tidak valid';
			$data['status'] = false;
		}

		if (input('no_cif') == '') {
			$data['inputerror'][] = 'no_cif';
			$data['error'][] = 'No CIF harus diisi';
			$data['status'] = false;
		} else if (strlen(input('no_cif')) < 8) {
			$data['inputerror'][] = 'no_cif';
			$data['error'][] = 'No CIF tidak valid';
			$data['status'] = false;
		}

		if (input('nm_koperasi') == '') {
			$data['inputerror'][] = 'nm_koperasi';
			$data['error'][] = 'Nama koperasi harus diisi';
			$data['status'] = false;
		}
		// else if (!preg_match('/^[a-zA-Z ]+$/', input('nm_koperasi'))) {
		// 	$data['inputerror'][] = 'nm_koperasi';
		// 	$data['error'][] = 'Nama koperasi tidak valid';
		// 	$data['status'] = false;
		// }

		if (input('thp_cair') == '') {
			$data['inputerror'][] = 'thp_cair';
			$data['error'][] = 'Tahap pencairan harus diisi';
			$data['status'] = false;
		} else if (!preg_match('/^[0-9]+$/', input('thp_cair'))) {
			$data['inputerror'][] = 'thp_cair';
			$data['error'][] = 'Tahap pencairan tidak valid';
			$data['status'] = false;
		}

		if (input('tgl_cair') == '') {
			$data['inputerror'][] = 'tgl_cair';
			$data['error'][] = 'Tgl cair harus diisi';
			$data['status'] = false;
		}

		if (input('nm_area') == '') {
			$data['inputerror'][] = 'nm_area';
			$data['error'][] = '';
			$data['status'] = false;
		}

		if ($data['status'] === false) {
			echo json_encode($data);
			exit();
		}
	}

	public function find($id)
	{
		$data = $this->db->get_where('tbl_koperasi', ['id' => $id])->row_array();

		echo json_encode($data);
		exit;
	}

	public function get_koperasi($key)
	{
		$data['koperasi'] = $this->db->select('a.*, b.nm_area')->from('tbl_koperasi a')
			->join('tbl_area b', 'a.kd_area = b.kd_area', 'left')
			->where(['a.id' => $key])->get()->row_array();
		$data['anggota'] = $this->db->get_where(
			'tbl_anggota_channeling',
			[
				'fk_rek_pembayaran' => $data['koperasi']['rek_pembayaran'],
				'tgl_pencairan' => $data['koperasi']['tgl_pencairan']
			]
		)->result_array();

		echo json_encode($data);
		exit;
	}

	public function insert()
	{
		$this->_validasi_koperasi();

		$data = array(
			'rek_pembayaran' => input('rek_pembayaran'),
			'nocif_kop' => input('no_cif'),
			'nm_koperasi' => input('nm_koperasi'),
			'tahap_pencairan' => input('thp_cair'),
			'kd_area' => input('nm_area'),
			'jns_pembiayaan' => 'Channeling',
			'tgl_pencairan' => tgl_db(input('tgl_cair')),
			'createDate' => date('Y-m-d H:i:s')
		);

		$cek = $this->db->get_where('tbl_koperasi', ['noloan_kop' => input('noloan')]);
		if ($cek->num_rows() > 0) {
			$status = array(
				'status' => true,
				'icon' => 'error',
				'title' => 'Kesalahan',
				'msg' => 'Data anggota sudah ada'
			);
		} else {
			$this->db->insert('tbl_koperasi', $data);

			$status = array(
				'status' => true,
				'icon' => 'success',
				'title' => 'Sukses',
				'msg' => 'Data anggota berhasil disimpan'
			);
		}

		echo json_encode($status);
		die;
	}

	public function update()
	{
		$this->_validasi_koperasi();

		$key = input('id');
		$data = array(
			'rek_pembayaran' => input('rek_pembayaran'),
			'nocif_kop' => input('no_cif'),
			'nm_koperasi' => input('nm_koperasi'),
			'tahap_pencairan' => input('thp_cair'),
			'kd_area' => input('nm_area'),
			'jns_pembiayaan' => 'Channeling',
			'tgl_pencairan' => tgl_db(input('tgl_cair')),
			'updateDate' => date('Y-m-d H:i:s')
		);

		$this->db->update('tbl_koperasi', $data, ['id' => $key]);

		$status = array(
			'status' => true,
			'icon' => 'success',
			'title' => 'Sukses',
			'msg' => 'Data anggota berhasil disimpan'
		);
		echo json_encode($status);
		exit;
	}

	public function delete($id)
	{
		$cek = $this->db->get_where('tbl_koperasi', ['id' => $id])->row_array();

		$this->db->trans_start();
		$this->db->delete('tbl_koperasi', ['id' => $id]);
		$this->db->delete('tbl_anggota_channeling', ['fk_rek_pembayaran' => $cek['rek_pembayaran'], 'tgl_pencairan' => $cek['tgl_pencairan']]);
		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			$status = array(
				'status' => false,
				'icon' => 'error',
				'title' => 'Kesalahan',
				'msg' => 'Data koperasi gagal dihapus'
			);
		} else {
			$status = array(
				'status' => true,
				'icon' => 'success',
				'title' => 'Sukses',
				'msg' => 'Data koperasi berhasil dihapus'
			);
		}

		echo json_encode($status);
		exit;
	}
	// end of module controller koperasi



	// module controller anggota koperasi
	private function _validasi_anggota()
	{
		$data = array();
		$data['inputerror'] = array();
		$data['error'] = array();
		$data['status'] = true;

		if (input('noloan') == '') {
			$data['inputerror'][] = 'noloan';
			$data['error'][] = 'No kontrak harus diisi';
			$data['status'] = false;
		} else if (substr(strtoupper(input('noloan')), 0, 2) == 'LD') {
			if (strlen(input('noloan')) != 12) {
				$data['inputerror'][] = 'noloan';
				$data['error'][] = 'No kontrak tidak valid';
				$data['status'] = false;
			}
		} else {
			if (strlen(input('noloan')) != 10) {
				$data['inputerror'][] = 'noloan';
				$data['error'][] = 'No kontrak tidak valid';
				$data['status'] = false;
			}
		}

		if (input('no_cif') == '') {
			$data['inputerror'][] = 'no_cif';
			$data['error'][] = 'No CIF harus diisi';
			$data['status'] = false;
		} else if (strlen(input('no_cif')) < 8) {
			$data['inputerror'][] = 'no_cif';
			$data['error'][] = 'No CIF tidak valid';
			$data['status'] = false;
		}

		if (input('nm_anggota') == '') {
			$data['inputerror'][] = 'nm_anggota';
			$data['error'][] = 'Nama anggota harus diisi';
			$data['status'] = false;
		} else if (!preg_match('/^[a-zA-Z ]+$/', input('nm_anggota'))) {
			$data['inputerror'][] = 'nm_anggota';
			$data['error'][] = 'Nama anggota tidak valid';
			$data['status'] = false;
		}

		if (input('tgl_cair') == '') {
			$data['inputerror'][] = 'tgl_cair';
			$data['error'][] = 'Tgl pencairan harus diisi';
			$data['status'] = false;
		}

		if (input('tenor') == '') {
			$data['inputerror'][] = 'tenor';
			$data['error'][] = 'Tenor harus diisi';
			$data['status'] = false;
		} else if (!preg_match('/^[0-9]+$/', input('tenor'))) {
			$data['inputerror'][] = 'tenor';
			$data['error'][] = 'Tenor tidak valid';
			$data['status'] = false;
		}

		if (input('nom_plafond') == '') {
			$data['inputerror'][] = 'nom_plafond';
			$data['error'][] = 'Plafond pencairan harus diisi';
			$data['status'] = false;
		} else if (!preg_match('/^[0-9,.]+$/', str_replace(',', '', input('nom_plafond')))) {
			$data['inputerror'][] = 'nom_plafond';
			$data['error'][] = 'Plafond pencairan tidak valid';
			$data['status'] = false;
		}

		if (input('os_pokok') == '') {
			$data['inputerror'][] = 'os_pokok';
			$data['error'][] = 'OS pokok harus diisi';
			$data['status'] = false;
		} else if (!preg_match('/^[0-9,.]+$/', str_replace(',', '', input('os_pokok')))) {
			$data['inputerror'][] = 'os_pokok';
			$data['error'][] = 'OS pokok tidak valid';
			$data['status'] = false;
		}

		if ($data['status'] === false) {
			echo json_encode($data);
			exit();
		}
	}

	public function upd_nominal($id)
	{
		$cek_sum = $this->db->select('sum(nom_pencairan) as plafond, sum(os_pokok) as ospokok, sum(tunggakan) as tunggakan')
			->from('tbl_anggota_channeling')
			->where(['id_koperasi' => $id])
			->get()->row_array();
		$this->db->update(
			'tbl_koperasi',
			[
				'nom_pencairan' => $cek_sum['plafond'],
				'os_pokok' => $cek_sum['ospokok'],
				'tunggakan' => $cek_sum['tunggakan']
			],
			['id' => $id]
		);
	}

	public function details($key)
	{
		$id = base64_decode($key);
		// $batch = substr($decrypt, -8);
		// $no_rek = substr($decrypt, 0, strlen($decrypt) - strlen($batch));
		// $tgl = substr($batch, 0, 4) . '-' . substr($batch, 4, 2) . '-' . substr($batch, -2);

		$page = 'koperasi/detail_channeling';

		$data['title'] = 'Details Koperasi';
		$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('sales/home') . '">Home</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item"><a href="' . site_url('sales/koperasi-channeling') . '">Koperasi</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item active">Details</li>';

		$this->db->select('a.*, b.nm_area')->from('tbl_koperasi a')->join('tbl_area b', 'a.kd_area = b.kd_area', 'left')->where(['a.id' => $id]);
		$data['koperasi'] = $this->db->get()->row_array();
		$data['anggota'] = $this->db->get_where('tbl_anggota_channeling', ['id_koperasi' => $id])->result_array();

		$qry = $this->db->select('sum(nom_pencairan) as plafond, sum(os_pokok) as ospokok, sum(tunggakan) as tunggakan')->from('tbl_anggota_channeling')->where(['id_koperasi' => $id])->get()->row_array();
		$data['plafond'] = $qry['plafond'];
		$data['ospokok'] = $qry['ospokok'];
		$data['tunggakan'] = $qry['tunggakan'];

		// var_dump($data); die;

		$this->load->view($page, $data);
	}

	public function get_anggota($id)
	{
		$data = $this->db->get_where('tbl_anggota_channeling', ['id' => base64_decode($id)])->row_array();

		echo json_encode($data);
		exit;
	}

	public function save_anggota()
	{
		$this->_validasi_anggota();

		$data = array(
			'fk_noloan_kop' => base64_decode(input('noloan_kop')),
			'noloan_anggota' => input('noloan'),
			'nocif_anggota' => input('no_cif'),
			'nm_anggota' => input('nm_anggota'),
			'tenor' => input('tenor'),
			'tgl_pencairan' => tgl_db(input('tgl_cair')),
			'nom_pencairan' => str_replace(',', '', input('nom_plafond')),
			'os_pokok' => str_replace(',', '', input('os_pokok')),
			'tunggakan' => str_replace(',', '', input('tunggakan')),
			'createDate' => date('Y-m-d H:i:s')
		);

		$cek = $this->db->get_where('tbl_anggota', ['noloan_anggota' => input('noloan')]);
		if ($cek->num_rows() > 0) {
			$status = array(
				'status' => true,
				'icon' => 'error',
				'title' => 'Kesalahan',
				'msg' => 'Data anggota sudah ada'
			);
		} else {
			$this->db->insert('tbl_anggota', $data);

			$status = array(
				'status' => true,
				'icon' => 'success',
				'title' => 'Sukses',
				'msg' => 'Data anggota berhasil disimpan'
			);
		}

		echo json_encode($status);
		die;
	}

	public function update_anggota()
	{
		$this->_validasi_anggota();

		$key = base64_decode(input('id'));
		$data = array(
			'nocif_anggota' => input('no_cif'),
			'nm_anggota' => input('nm_anggota'),
			'tenor' => input('tenor'),
			'tgl_pencairan' => tgl_db(input('tgl_cair')),
			'nom_pencairan' => str_replace(',', '', input('nom_plafond')),
			'os_pokok' => str_replace(',', '', input('os_pokok')),
			'tunggakan' => str_replace(',', '', input('tunggakan')),
			'updateDate' => date('Y-m-d H:i:s')
		);

		$this->db->update('tbl_anggota_channeling', $data, ['id' => $key]);

		$status = array(
			'status' => true,
			'icon' => 'success',
			'title' => 'Sukses',
			'msg' => 'Data anggota berhasil disimpan'
		);
		echo json_encode($status);
		exit;
	}

	public function delete_anggota($id)
	{
		$cek = $this->db->get_where('tbl_anggota_channeling', ['id' => base64_decode($id)]);
		if ($cek->num_rows() > 0) {
			$this->db->delete('tbl_anggota_channeling', ['id' => base64_decode($id)]);

			$status = array(
				'status' => true,
				'icon' => 'success',
				'title' => 'Sukses',
				'msg' => 'Data anggota berhasil dihapus'
			);

			echo json_encode($status);
			exit;
		} else {
			$status = array(
				'status' => false,
				'icon' => 'error',
				'title' => 'Kesalahan',
				'msg' => 'Data anggota gagal dihapus'
			);

			echo json_encode($status);
			exit;
		}
	}
	// end of module controller anggota koperasi



	// template Excel
	// public function template()
	// {
	// 	$spreadsheet = new Spreadsheet();
	// 	$sheet = $spreadsheet->getActiveSheet();

	// 	$sheet->setCellValue('A1', 'REK_PEMBAYARAN');
	// 	$sheet->setCellValue('B1', 'NOMORCIF');
	// 	$sheet->setCellValue('C1', 'NAMA_KOPERASI');
	// 	$sheet->setCellValue('D1', 'KD_CABANG');
	// 	$sheet->setCellValue('E1', 'TAHAP_CAIR');

	// 	$writer = new Xlsx($spreadsheet);

	// 	$filename = 'template-channeling'; // set filename for excel file to be exported

	// 	header('Content-Type: application/vnd.ms-excel'); // generate excel file
	// 	header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
	// 	header('Cache-Control: max-age=0');

	// 	$writer->save('php://output');	// download file 
	// }

	// public function temp_enduser()
	// {
	// 	$spreadsheet = new Spreadsheet();
	// 	$sheet = $spreadsheet->getActiveSheet();

	// 	$sheet->setCellValue('A1', 'FICMISDATE');
	// 	$sheet->setCellValue('B1', 'NOLOAN');
	// 	$sheet->setCellValue('C1', 'NOMORCIF');
	// 	$sheet->setCellValue('D1', 'NAMA_ANGGOTA');
	// 	$sheet->setCellValue('E1', 'TGL_PENCAIRAN');
	// 	$sheet->setCellValue('F1', 'TGL_OSPOKOK');
	// 	$sheet->setCellValue('G1', 'TENOR');
	// 	$sheet->setCellValue('H1', 'NOM_PENCAIRAN');
	// 	$sheet->setCellValue('I1', 'OSPOKOK');
	// 	$sheet->setCellValue('J1', 'TUNGGAKAN');

	// 	$writer = new Xlsx($spreadsheet);

	// 	$filename = 'temp-enduser-channeling'; // set filename for excel file to be exported

	// 	header('Content-Type: application/vnd.ms-excel'); // generate excel file
	// 	header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
	// 	header('Cache-Control: max-age=0');

	// 	$writer->save('php://output');	// download file 
	// }

	// // Export excel
	// public function export($key)
	// {
	// 	$kop = $this->db->get_where('tbl_koperasi', ['noloan_kop' => base64_decode($key)])->row_array();
	// 	$area = $this->db->get_where('tbl_area', ['kd_area' => $kop['kd_area']])->row_array();
	// 	$cek = $this->db->get_where('tbl_anggota', ['fk_noloan_kop' => base64_decode($key)]);
	// 	$data = $cek->result_array();

	// 	if ($cek->num_rows() > 0) {
	// 		$column = 10;
	// 		$spreadsheet = new Spreadsheet();

	// 		$spreadsheet->setActiveSheetIndex(0)
	// 			->setCellValue('A3', 'Nomor Kontrak')
	// 			->setCellValue('A4', 'Tahap Pencairan')
	// 			->setCellValue('A5', 'Jenis Pembiayaan')
	// 			->setCellValue('A6', 'Nominal Pencairan')
	// 			->setCellValue('D3', 'Nama Koperasi')
	// 			->setCellValue('D4', 'Nama Area')
	// 			->setCellValue('D5', 'Tujuan Pembiayaan')
	// 			->setCellValue('D6', 'Outstanding Pokok');

	// 		$spreadsheet->setActiveSheetIndex(0)
	// 			->setCellValue('B3', $kop['noloan_kop'])
	// 			->setCellValue('B4', 'Tahap ' . $kop['tahap_pencairan'])
	// 			->setCellValue('B5', $kop['jns_pembiayaan'])
	// 			->setCellValue('B6', 'Rp ' . number_format($kop['nom_pencairan'], 2, '.', ','))
	// 			->setCellValue('E3', $kop['nocif_kop'] . ' - ' . $kop['nm_koperasi'])
	// 			->setCellValue('E4', $area['nm_area'])
	// 			->setCellValue('E5', $kop['tujuan_pembiayaan'])
	// 			->setCellValue('E6', 'Rp ' . number_format($kop['os_pokok'], 2, '.', ','));

	// 		$spreadsheet->setActiveSheetIndex(0)
	// 			->setCellValue('A' . ($column - 1), 'NOLOAN')
	// 			->setCellValue('B' . ($column - 1), 'NOMORCIF')
	// 			->setCellValue('C' . ($column - 1), 'NAMA_ANGGOTA')
	// 			->setCellValue('D' . ($column - 1), 'TGL_PENCAIRAN')
	// 			->setCellValue('E' . ($column - 1), 'TGL_OSPOKOK')
	// 			->setCellValue('F' . ($column - 1), 'TENOR')
	// 			->setCellValue('G' . ($column - 1), 'NOM_PENCAIRAN')
	// 			->setCellValue('H' . ($column - 1), 'OSPOKOK');

	// 		foreach ($data as $dt) {
	// 			$tgl_cair = explode('-', $dt['tgl_pencairan']);
	// 			$tgl_os = explode('-', $dt['tgl_ospokok']);

	// 			$spreadsheet->setActiveSheetIndex(0)
	// 				->setCellValue('A' . $column, $dt['noloan_anggota'])
	// 				->setCellValue('B' . $column, $dt['nocif_anggota'])
	// 				->setCellValue('C' . $column, $dt['nm_anggota'])
	// 				->setCellValue('D' . $column, $tgl_cair[2] . '/' . $tgl_cair[1] . '/' . $tgl_cair[0])
	// 				->setCellValue('E' . $column, $tgl_os[2] . '/' . $tgl_os[1] . '/' . $tgl_os[0])
	// 				->setCellValue('F' . $column, $dt['tenor'])
	// 				->setCellValue('G' . $column, $dt['nom_pencairan'])
	// 				->setCellValue('H' . $column, $dt['os_pokok']);

	// 			$column++;
	// 		}

	// 		// tulis dalam format .xlsx
	// 		$writer = new Xlsx($spreadsheet);
	// 		$fileName = 'export-data-' . str_replace(' ', '-', strtolower($kop['nm_koperasi']));

	// 		// Redirect hasil generate xlsx ke web client
	// 		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	// 		header('Content-Disposition: attachment;filename=' . $fileName . '.xlsx');
	// 		header('Cache-Control: max-age=0');

	// 		$writer->save('php://output');
	// 	} else {
	// 		$this->session->set_flashdata('export_err', 'Tidak ada data untuk di Export');
	// 		echo "<script>window.history.back();</script>";
	// 	}
	// }

	// template CSV
	public function template()
	{
		$filename = 'template-channeling'; // set filename for csv file to be exported

		$nm_column = array(
			'REK_PEMBAYARAN',
			'NOMORCIF',
			'NAMA_KOPERASI',
			'KD_CABANG',
			'TGL_CAIR',
			'TAHAP_CAIR'
		);

		$csv_header = '';
		foreach ($nm_column as $key => $col) {
			$csv_header .= $col . '|';
		}
		$csv_header .= "\n";

		$csv_row = '7081935337|79207999|KOPEDANA|ID0010520|2020-08-30|1|';
		$csv_row .= "\n";

		/* Download as CSV File */
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename . '.csv');
		echo $csv_header . $csv_row;
		exit;
	}

	public function temp_enduser()
	{
		$filename = 'temp-enduser-channeling'; // set filename for excel file to be exported

		$nm_column = array(
			'FICMISDATE',
			'NOLOAN',
			'NOMORCIF',
			'NAMA_ANGGOTA',
			'TGL_PENCAIRAN',
			'TGL_OSPOKOK',
			'TENOR',
			'NOM_PENCAIRAN',
			'OSPOKOK',
			'TUNGGAKAN'
		);

		$csv_header = '';
		foreach ($nm_column as $key => $col) {
			$csv_header .= $col . '|';
		}
		$csv_header .= "\n";

		$csv_row = '2020-10-31|LD1635789799|79207999|KOPEDANA|2020-08-12|2020-10-31|35|30000000|1570222.83|0|';
		$csv_row .= "\n";

		/* Download as CSV File */
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename . '.csv');
		echo $csv_header . $csv_row;
		exit;
	}

	public function import()
	{
		$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$status = true;
		$kode_ao = input('kode_ao');

		if (isset($_FILES['upd_file']['name']) && in_array($_FILES['upd_file']['type'], $file_mimes)) {
			$arr_file = explode('.', $_FILES['upd_file']['name']);
			$extension = end($arr_file);

			if ('csv' == $extension) {
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
			}

			$spreadsheet = $reader->load($_FILES['upd_file']['tmp_name']);

			$sheetData = $spreadsheet->getActiveSheet()->toArray();

			$data = array();

			for ($i = 1; $i < count($sheetData); $i++) {
				$data['fk_kode_ao'] = $kode_ao;
				$data['jns_pembiayaan'] = 'Channeling';

				if ($sheetData[$i][0] == '' || strlen($sheetData[$i][0]) != 10) {
					$status = false;
				} else {
					$data['rek_pembayaran'] = $sheetData[$i][0];
				}

				if ($sheetData[$i][1] == '' || strlen($sheetData[$i][1]) != 8) {
					$status = false;
				} else {
					$data['nocif_kop'] = $sheetData[$i][1];
				}

				if ($sheetData[$i][2] == '' || !preg_match('/^[a-zA-Z ]+$/', $sheetData[$i][2])) {
					$status = false;
				} else {
					$data['nm_koperasi'] = $sheetData[$i][2];
				}

				$area = $this->db->get_where('tbl_cabang', ['kd_cabang' => $sheetData[$i][3]])->row_array();
				if ($sheetData[$i][3] == '' || substr(strtoupper($sheetData[$i][3]), 0, 2) != 'ID') {
					$status = false;
				} else {
					if (strlen($sheetData[$i][3]) != 9 || $area['fk_kd_area'] == null) {
						$status = false;
					} else {
						$data['kd_area'] = $area['fk_kd_area'];
					}
				}

				if ($sheetData[$i][4] == '' || !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $sheetData[$i][4])) {
					$status = false;
				} else {
					$data['tgl_pencairan'] = $sheetData[$i][4];
				}

				if ($sheetData[$i][5] == '' || !is_numeric($sheetData[$i][5])) {
					$status = false;
				} else {
					$data['tahap_pencairan'] = $sheetData[$i][5];
				}

				if ($status === false) {
					$msg = 'Terjadi kesalahan saat proses upload, periksa kembali data file upload Anda!';
				} else {
					$this->db->insert('tbl_koperasi', $data);

					$msg = 'Anda telah berhasil mengimport ' . $i . ' daftar koperasi!';
				}
			}
		} else {
			$status = false;
			$msg = 'Format file invalid!';
		}

		echo json_encode(['status' => $status, 'msg' => $msg]);
		exit;
		// redirect(site_url('sales/koperasi-channeling'));
	}

	public function import_enduser()
	{
		$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$status = true;
		$noloan_kop = input('noloan_kop');

		if (isset($_FILES['upd_file']['name']) && in_array($_FILES['upd_file']['type'], $file_mimes)) {
			$arr_file = explode('.', $_FILES['upd_file']['name']);
			$extension = end($arr_file);

			if ('csv' == $extension) {
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
			}

			$spreadsheet = $reader->load($_FILES['upd_file']['tmp_name']);

			$sheetData = $spreadsheet->getActiveSheet()->toArray();

			$data = array();
			$data['fk_rek_pembayaran'] = $noloan_kop;
			$data['id_koperasi'] = input('id');

			for ($i = 1; $i < count($sheetData); $i++) {
				if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $sheetData[$i][0])) {
					$status = false;
				} else {
					$data['ficmisDate'] = $sheetData[$i][0];
				}

				if (strlen($sheetData[$i][1]) != 12 || substr(strtoupper($sheetData[$i][1]), 0, 2) != 'LD') {
					$status = false;
				} else {
					$data['noloan_anggota'] = $sheetData[$i][1];
				}

				if (strlen($sheetData[$i][2]) != 8) {
					$status = false;
				} else {
					$data['nocif_anggota'] = $sheetData[$i][2];
				}

				if (!preg_match('/^[a-zA-Z ]+$/', $sheetData[$i][3])) {
					$status = false;
				} else {
					$data['nm_anggota'] = $sheetData[$i][3];
				}

				if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $sheetData[$i][4])) {
					$status = false;
				} else {
					$data['tgl_pencairan'] = $sheetData[$i][4];
				}

				if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $sheetData[$i][5])) {
					$status = false;
				} else {
					$data['tgl_ospokok'] = $sheetData[$i][5];
				}

				if (!is_numeric($sheetData[$i][6])) {
					$status = false;
				} else {
					$data['tenor'] = $sheetData[$i][6];
				}

				if (!is_float($sheetData[$i][7])) {
					$status = false;
				} else {
					$data['nom_Pencairan'] = $sheetData[$i][7];
				}

				if (!is_float($sheetData[$i][8])) {
					$status = false;
				} else {
					$data['os_pokok'] = $sheetData[$i][8];
				}

				if (!is_float($sheetData[$i][9])) {
					$status = false;
				} else {
					$data['tunggakan'] = $sheetData[$i][9];
				}

				if ($status === false) {
					$msg = 'Terjadi kesalahan saat proses upload, periksa kembali data file upload Anda!';

					$this->db->delete('tbl_anggota_channeling', ['id_koperasi' => input('id')]);
				} else {
					$this->db->insert('tbl_anggota_channeling', $data);
					$this->db->update(
						'tbl_koperasi',
						[
							'tgl_pencairan' => $data['tgl_pencairan'],
							'tgl_ospokok' => $data['tgl_ospokok']
						],
						['id' => input('id')]
					);

					$msg = 'Anda telah berhasil mengimport ' . $i . ' daftar anggota koperasi!';
				}

				// var_dump($data);
			}
			// die;
		} else {
			$status = false;
			$msg = 'Format file invalid!';
		}

		echo json_encode(['status' => $status, 'msg' => $msg]);
		exit;
		// redirect(site_url('sales/koperasi/channeling/details/' . base64_encode($noloan_kop)));
	}

	// Export CSV
	public function export($key)
	{
		$decode = base64_decode($key);
		$tgl = substr($decode, -8);
		$rek = substr($decode, 0, strlen($decode) - strlen($tgl));
		$tgl = substr($tgl, 0, 4) . '-' . substr($tgl, 4, 2) . '-' . substr($tgl, -2);

		$kop = $this->db->get_where('tbl_koperasi', ['rek_pembayaran' => $rek, 'tgl_pencairan' => $tgl])->row_array();
		$area = $this->db->get_where('tbl_area', ['kd_area' => $kop['kd_area']])->row_array();
		$cek = $this->db->get_where('tbl_anggota_channeling', ['id_koperasi' => $kop['id']]);
		$data = $cek->result_array();

		if ($cek->num_rows() > 0) {
			$filename = 'export-data-' . str_replace(' ', '-', strtolower($kop['nm_koperasi']));

			$nm_column = array(
				'FICMISDATE',
				'NOLOAN',
				'NOMORCIF',
				'NAMA_ANGGOTA',
				'TGL_PENCAIRAN',
				'TGL_OSPOKOK',
				'TENOR',
				'NOM_PENCAIRAN',
				'OSPOKOK',
				'TUNGGAKAN'
			);

			$csv_header = '';
			foreach ($nm_column as $key => $col) {
				$csv_header .= $col . '|';
			}
			$csv_header .= "\n";

			$csv_row = '';
			foreach ($data as $val) {
				$csv_row .= $val['ficmisDate'] . '|';
				$csv_row .= $val['noloan_anggota'] . '|';
				$csv_row .= $val['nocif_anggota'] . '|';
				$csv_row .= $val['nm_anggota'] . '|';
				$csv_row .= $val['tgl_pencairan'] . '|';
				$csv_row .= $val['tgl_ospokok'] . '|';
				$csv_row .= $val['tenor'] . '|';
				$csv_row .= $val['nom_pencairan'] . '|';
				$csv_row .= $val['os_pokok'] . '|';
				$csv_row .= $val['tunggakan'] . '|';
				$csv_row .= "\n";
			}
			$csv_row .= "\n";

			/* Download as CSV File */
			header('Content-type: application/csv');
			header('Content-Disposition: attachment; filename=' . $filename . '.csv');
			echo $csv_header . $csv_row;
			exit;
		} else {
			$this->session->set_flashdata('export_err', 'Tidak ada data untuk di Export');
			echo "<script>window.history.back();</script>";
		}
	}



	// rekonsialisasi
	public function temp_rekonsel()
	{
		$filename = 'template-rekon-channeling'; // set filename for csv file to be exported

		$nm_column = array(
			'REK_PEMBAYARAN',
			'NOLOAN',
			'NAMA_ANGGOTA',
			'BATCH',
			'TENOR',
			'PLAFOND',
			'TGL_OSPOKOK',
			'OSPOKOK'
		);

		$csv_header = '';
		foreach ($nm_column as $key => $col) {
			$csv_header .= $col . '|';
		}
		$csv_header .= "\n";

		$csv_row = '7081935337|LD1826138904|Hary Sudaryanto|1|12|50000000.00|2019-12-12|99037645.71|';
		$csv_row .= "\n";

		/* Download as CSV File */
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename . '.csv');
		echo $csv_header . $csv_row;
		exit;
	}

	public function import_rekon()
	{
		$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$status = true;

		if (isset($_FILES['upd_file']['name']) && in_array($_FILES['upd_file']['type'], $file_mimes)) {
			$arr_file = explode('.', $_FILES['upd_file']['name']);
			$extension = end($arr_file);

			if ('csv' == $extension) {
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
			}

			$spreadsheet = $reader->load($_FILES['upd_file']['tmp_name']);

			$sheetData = $spreadsheet->getActiveSheet()->toArray();

			$data = array();
			$data['kode_ao'] = input('kode_ao');
			$data['id_koperasi'] = input('id_koperasi');

			$cek = $this->db->get_where('tbl_anggota_channeling', ['id_koperasi' => input('id_koperasi')])->num_rows();

			if ((count($sheetData) - 1) > $cek) {
				$status = false;
				$msg = 'Terjadi kesalahan, jumlah end-user koperasi melebihi end-user bank';
			} else {
				for ($i = 1; $i < count($sheetData); $i++) {
					$msg = 'Terjadi kesalahan pada ';

					if ($sheetData[$i][0] != input('rek_pemb')) {
						$status = false;
						$msg .= 'kolom ' . $sheetData[0][0] . ' baris ' . $i;
						break;
					} else {
						$data['rek_pembayaran'] = $sheetData[$i][0];
					}

					if (strlen($sheetData[$i][1]) != 12 || substr(strtoupper($sheetData[$i][1]), 0, 2) != 'LD') {
						$status = false;
						$msg .= 'kolom ' . $sheetData[0][1] . ' baris' . $i;
						break;
					} else {
						$data['noloan'] = $sheetData[$i][1];
					}

					if (!preg_match('/^[a-zA-Z ]+$/', $sheetData[$i][2])) {
						$status = false;
						$msg .= 'kolom ' . $sheetData[0][2] . ' baris' . $i;
						break;
					} else {
						$data['nm_anggota'] = $sheetData[$i][2];
					}

					if (!is_numeric($sheetData[$i][3])) {
						$status = false;
						$msg .= 'kolom ' . $sheetData[0][3] . ' baris' . $i;
						break;
					} else {
						$data['batch'] = $sheetData[$i][3];
					}

					if (!is_numeric($sheetData[$i][4])) {
						$status = false;
						$msg .= 'kolom ' . $sheetData[0][4] . ' baris' . $i;
						break;
					} else {
						$data['tenor'] = $sheetData[$i][4];
					}

					if (!is_float($sheetData[$i][5])) {
						$status = false;
						$msg .= 'kolom ' . $sheetData[0][5] . ' baris' . $i;
						break;
					} else {
						$data['plafond'] = $sheetData[$i][5];
					}

					if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $sheetData[$i][6])) {
						$status = false;
						$msg .= 'kolom ' . $sheetData[0][6] . ' baris' . $i;
						break;
					} else {
						$data['tgl_ospokok'] = $sheetData[$i][6];
					}

					if (!is_float($sheetData[$i][7])) {
						$status = false;
						$msg .= 'kolom ' . $sheetData[0][7] . ' baris' . $i;
						break;
					} else {
						$data['ospokok'] = $sheetData[$i][7];
					}

					if ($status === false) {
						// $msg = 'Terjadi kesalahan saat proses upload, periksa kembali data file upload Anda!';

						$this->db->delete('tbl_rekon_channeling', ['id_koperasi' => input('id_koperasi')]);
					} else {
						$this->db->insert('tbl_rekon_channeling', $data);
						$this->db->update('tbl_koperasi', ['status' => 'Proses Rekonsialisasi'], ['id' => input('id_koperasi')]);

						$msg = 'Data Rekonsialisasi telah berhasil di-upload';
					}

					// var_dump($status);
				}
			}
			// die;
		} else {
			$status = false;
			$msg = 'Format file invalid!';
		}

		echo json_encode(['status' => $status, 'msg' => $msg]);
		exit;
	}
}
