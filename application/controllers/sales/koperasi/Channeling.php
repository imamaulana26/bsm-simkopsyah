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
		$page = 'koperasi/channeling/home';

		$data['title'] = 'Daftar Koperasi Channeling';
		$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('sales/home') . '">Home</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item active">Koperasi</li>';

		$sql = "select * from tbl_koperasi a ";
		$sql .= "left join (select id_koperasi, max(ficmisDate) as ficmisDate, count(distinct(noloan_anggota)) as anggota from tbl_anggota_channeling group by id_koperasi) b on a.id = b.id_koperasi ";
		$sql .= "left join tbl_area c on a.kd_area = c.kd_area ";
		$sql .= "where a.fk_kode_ao = '" . $_SESSION['kd_ao'] . "' and a.jns_pembiayaan = 'Channeling' ";
		$sql .= "order by a.id asc ";
		$data['list_koperasi'] = $this->db->query($sql)->result_array();

		$data['list_rekon'] = $this->db->select('count(distinct(id_koperasi)) as rekon')->from('tbl_rekon_channeling')
			->where(['kode_ao' => $this->session->userdata('kd_ao')])
			->get()->row_array();

		$data['li_perusahaan'] = $this->db->select('distinct(nm_perusahaan)')->from('tbl_koperasi')->where(['fk_kode_ao' => $_SESSION['kd_ao']])->order_by('nm_perusahaan asc')->get()->result_array();

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
			$data['error'][] = 'Rek. pembayaran tidak valid';
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

		if (input('nm_perusahaan') == '') {
			$data['inputerror'][] = 'nm_perusahaan';
			$data['error'][] = 'Nama perusahaan harus diisi';
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

		// if (input('tgl_cair') == '') {
		// 	$data['inputerror'][] = 'tgl_cair';
		// 	$data['error'][] = 'Tgl cair harus diisi';
		// 	$data['status'] = false;
		// }

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
		$data['koperasi'] = $this->db->select('a.*, b.nm_area, count(distinct(rekon_date)) as rekon')->from('tbl_koperasi a')
			->join('tbl_area b', 'a.kd_area = b.kd_area', 'left')
			->join('tbl_rekon_channeling c', 'a.id = c.id_koperasi', 'left')
			->where(['a.id' => $key])->get()->row_array();

		$qry = "select id_koperasi, max(ficmisDate) as ficmisDate, count(distinct(noloan_anggota)) as anggota, nom_pencairan, os_pokok from tbl_anggota_channeling where id_koperasi = " . $data['koperasi']['id'] . " group by id_koperasi";
		$data['anggota'] = $this->db->query($qry)->row_array();

		echo json_encode($data);
		exit;
	}

	public function insert()
	{
		$this->_validasi_koperasi();

		$data = array(
			'fk_kode_ao' => $_SESSION['kd_ao'],
			'rek_pembayaran' => input('rek_pembayaran'),
			'nocif_kop' => input('no_cif'),
			'nm_koperasi' => input('nm_koperasi'),
			'nm_perusahaan' => input('nm_perusahaan'),
			'tahap_pencairan' => input('thp_cair'),
			'kd_area' => input('nm_area'),
			'jns_pembiayaan' => 'Channeling',
			'status' => 'Belum Terekonsiliasi',
			// 'tgl_pencairan' => tgl_db(input('tgl_cair')),
			'createDate' => date('Y-m-d H:i:s')
		);

		$cek = $this->db->get_where('tbl_koperasi', ['id' => input('id')]);
		if ($cek->num_rows() > 0) {
			$status = array(
				'status' => true,
				'icon' => 'error',
				'title' => 'Kesalahan',
				'msg' => 'Data koperasi sudah ada'
			);
		} else {
			$this->db->insert('tbl_koperasi', $data);

			$status = array(
				'status' => true,
				'icon' => 'success',
				'title' => 'Sukses',
				'msg' => 'Data koperasi berhasil disimpan'
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
			'nm_perusahaan' => input('nm_perusahaan'),
			'tahap_pencairan' => input('thp_cair'),
			'kd_area' => input('nm_area'),
			'jns_pembiayaan' => 'Channeling',
			// 'tgl_pencairan' => tgl_db(input('tgl_cair')),
			'updateDate' => date('Y-m-d H:i:s')
		);

		$this->db->update('tbl_koperasi', $data, ['id' => $key]);

		$status = array(
			'status' => true,
			'icon' => 'success',
			'title' => 'Sukses',
			'msg' => 'Data koperasi berhasil disimpan'
		);
		echo json_encode($status);
		exit;
	}

	public function delete($id)
	{
		$cek = $this->db->get_where('tbl_koperasi', ['id' => $id])->row_array();

		$this->db->trans_start();
		$this->db->delete('tbl_koperasi', ['id' => $id]);
		$this->db->delete('tbl_anggota_channeling', ['id_koperasi' => $id, 'batch' => $cek['tahap_pencairan']]);
		$this->db->delete('tbl_rekon_channeling', ['id_koperasi' => $id, 'batch' => $cek['tahap_pencairan']]);
		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			$status = array(
				'status' => false,
				'icon' => 'error',
				'title' => 'Kesalahan',
				'msg' => 'Data gagal dihapus'
			);
		} else {
			$status = array(
				'status' => true,
				'icon' => 'success',
				'title' => 'Sukses',
				'msg' => 'Data berhasil dihapus'
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

		if (input('tgl_ospokok') == '') {
			$data['inputerror'][] = 'tgl_ospokok';
			$data['error'][] = 'Tgl outstanding harus diisi';
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
			$data['error'][] = 'Outstanding harus diisi';
			$data['status'] = false;
		} else if (!preg_match('/^[0-9,.]+$/', str_replace(',', '', input('os_pokok')))) {
			$data['inputerror'][] = 'os_pokok';
			$data['error'][] = 'Outstanding tidak valid';
			$data['status'] = false;
		}

		if ($data['status'] === false) {
			echo json_encode($data);
			exit();
		}
	}

	public function upd_nominal($id)
	{
		$qry = "select tgl_pencairan, sum(nom_pencairan) as plafond, tgl_ospokok, sum(os_pokok) as ospokok from tbl_anggota_channeling where id_koperasi = '" . $id . "' and ficmisDate in (select max(ficmisDate) from tbl_anggota_channeling where id_koperasi = '" . $id . "')";
		$cek_sum = $this->db->query($qry)->row_array();
		$cek_kop = $this->db->get_where('tbl_koperasi', ['id' => $id])->row_array();

		$data = array(
			'tgl_pencairan' => $cek_sum['tgl_pencairan'],
			'nom_pencairan' => $cek_sum['plafond'],
			'tgl_ospokok' => $cek_sum['tgl_ospokok'],
			'os_pokok' => $cek_sum['ospokok']
		);

		if ($cek_kop['status'] == 'Update Outstanding') {
			$data['status'] = 'Belum Terekonsiliasi';
		}

		$this->db->update('tbl_koperasi', $data, ['id' => $id]);
	}

	public function details($key)
	{
		$id = base64_decode($key);
		$page = 'koperasi/channeling/detail';

		$data['title'] = 'Details Koperasi';
		$data['breadcrumb'] = '<li class="breadcrumb-item"><a href="' . site_url('sales/home') . '">Home</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item"><a href="' . site_url('sales/koperasi-channeling') . '">Koperasi</a></li>';
		$data['breadcrumb'] .= '<li class="breadcrumb-item active">Details</li>';

		$this->db->select('a.*, b.nm_area')->from('tbl_koperasi a')->join('tbl_area b', 'a.kd_area = b.kd_area', 'left')->where(['a.id' => $id]);
		$data['koperasi'] = $this->db->get()->row_array();

		$sql = "select * from tbl_anggota_channeling where id_koperasi = '" . $id . "' and ficmisDate in (select max(ficmisDate) from tbl_anggota_channeling where id_koperasi = '" . $id . "')";
		$data['anggota'] = $this->db->query($sql)->result_array();

		$qry = "select tgl_pencairan, sum(nom_pencairan) as plafond, tgl_ospokok, sum(os_pokok) as ospokok from tbl_anggota_channeling where id_koperasi = '" . $id . "' and ficmisDate in (select max(ficmisDate) from tbl_anggota_channeling where id_koperasi = '" . $id . "')";
		$cek_sum = $this->db->query($qry)->row_array();
		$data['plafond'] = $cek_sum['plafond'];
		$data['ospokok'] = $cek_sum['ospokok'];

		$this->upd_nominal($id);

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
			'id_koperasi' => input('id_koperasi'),
			'fk_rek_pembayaran' => input('rek_pembayaran'),
			'noloan_anggota' => input('noloan'),
			'nocif_anggota' => input('no_cif'),
			'nm_anggota' => input('nm_anggota'),
			'tenor' => input('tenor'),
			'batch' => input('batch'),
			'tgl_pencairan' => tgl_db(input('tgl_cair')),
			'tgl_ospokok' => tgl_db(input('tgl_ospokok')),
			'nom_pencairan' => str_replace(',', '', input('nom_plafond')),
			'os_pokok' => str_replace(',', '', input('os_pokok')),
			'ficmisdate' => tgl_db(input('tgl_ospokok')),
			'createDate' => date('Y-m-d H:i:s')
		);

		$cek = $this->db->get_where('tbl_anggota_channeling', ['noloan_anggota' => input('noloan')]);
		$cek_kop = $this->db->get_where('tbl_koperasi', ['id' => $data['id_koperasi']])->row_array();

		if ($cek->num_rows() > 0) {
			$status = array(
				'status' => true,
				'icon' => 'error',
				'title' => 'Kesalahan',
				'msg' => 'Data anggota sudah ada'
			);
		} else {
			$this->db->insert('tbl_anggota_channeling', $data);
			if ($cek_kop['tgl_ospokok'] == null) {
				$this->db->update('tbl_koperasi', ['tgl_ospokok' => $data['tgl_ospokok']], ['id' => $data['id_koperasi']]);
			}

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
			'tgl_ospokok' => tgl_db(input('tgl_ospokok')),
			'nom_pencairan' => str_replace(',', '', input('nom_plafond')),
			'os_pokok' => str_replace(',', '', input('os_pokok')),
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



	// template CSV
	public function template()
	{
		$filename = 'template-channeling'; // set filename for csv file to be exported

		$nm_column = array(
			'REK_PEMBAYARAN',
			'NOMORCIF',
			'NAMA_KOPERASI',
			'KD_CABANG',
			'TAHAP_CAIR',
			'NAMA_PERUSAHAAN'
		);

		$csv_header = '';
		foreach ($nm_column as $key => $col) {
			$csv_header .= $col . '|';
		}
		$csv_header .= "\n";

		$csv_row = '7081935337|79207999|KOPEDANA|ID0010520|1|PT CAHAYA ABADI|';
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
			'TENOR',
			'TGL_PENCAIRAN',
			'NOM_PENCAIRAN',
			'TGL_OSPOKOK',
			'OSPOKOK',
			// 'TUNGGAKAN'
		);

		$csv_header = '';
		foreach ($nm_column as $key => $col) {
			$csv_header .= $col . '|';
		}
		$csv_header .= "\n";

		// $csv_row = '2020-10-31|LD1635789799|79207999|KOPEDANA|2020-08-12|2020-10-31|35|30000000|1570222.83|0|';
		$csv_row = '2020-10-31|LD1635789799|79207999|KOPEDANA|35|2018-09-30|30000000.00|2020-10-31|1570222.83|';
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

			$result = array();
			$data = array();

			for ($i = 1; $i < count($sheetData); $i++) {
				$data['fk_kode_ao'] = $kode_ao;
				$data['jns_pembiayaan'] = 'Channeling';

				$msg = 'Terjadi kesalahan, ';

				if ($sheetData[$i][0] == '' || strlen($sheetData[$i][0]) != 10) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][0] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$data['rek_pembayaran'] = $sheetData[$i][0];
				}

				if ($sheetData[$i][1] == '' || strlen($sheetData[$i][1]) != 8) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][1] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$data['nocif_kop'] = $sheetData[$i][1];
				}

				if ($sheetData[$i][2] == '' || !preg_match('/^[a-zA-Z ]+$/', $sheetData[$i][2])) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][2] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$data['nm_koperasi'] = $sheetData[$i][2];
				}

				$area = $this->db->get_where('tbl_cabang', ['kd_cabang' => $sheetData[$i][3]])->row_array();
				if ($sheetData[$i][3] == '' || substr(strtoupper($sheetData[$i][3]), 0, 2) != 'ID') {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][3] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					if (strlen($sheetData[$i][3]) != 9 || $area['fk_kd_area'] == null) {
						$status = false;
						$msg .= 'format pada kolom ' . $sheetData[0][3] . ' baris ' . $i . ' tidak sesuai';
						break;
					} else {
						$data['kd_area'] = $area['fk_kd_area'];
					}
				}

				if ($sheetData[$i][4] == '' || !is_numeric($sheetData[$i][4])) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][4] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$data['tahap_pencairan'] = $sheetData[$i][4];
				}

				if ($sheetData[$i][5] == '' || !preg_match('/^[a-zA-Z ]+$/', $sheetData[$i][5])) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][5] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$data['nm_perusahaan'] = $sheetData[$i][5];
				}

				$data['status'] = 'Belum Terekonsiliasi';

				$result[] = $data;
			}

			if ($status === FALSE) {
				foreach ($result as $val) {
					$where = array(
						'nocif_kop' => $val['nocif_kop'],
						'rek_pembayaran' => $val['rek_pembayaran'],
						'tahap_pencairan' => $val['tahap_pencairan']
					);
					$cek = $this->db->get_where('tbl_koperasi', $where)->num_rows();
					if ($cek == 0) {
						$this->db->delete('tbl_koperasi', $where);
					}
				}
			} else {
				$this->db->insert_batch('tbl_koperasi', $result);

				$msg = 'Anda telah berhasil mengimport ' . ($i - 1) . ' daftar koperasi!';
			}
		} else {
			$status = false;
			$msg = 'Format file invalid!';
		}

		echo json_encode(['status' => $status, 'msg' => $msg]);
		exit;
	}

	public function import_enduser()
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

			$result = array();
			$data = array();
			$data['fk_rek_pembayaran'] = input('rek_pembayaran');
			$data['id_koperasi'] = input('id');
			$data['batch'] = input('batch');

			$cek_kop = $this->db->get_where('tbl_anggota_channeling', ['id_koperasi' => input('id'), 'batch' => input('batch')])->num_rows();

			for ($i = 1; $i < count($sheetData); $i++) {
				$msg = 'Terjadi kesalahan, ';

				if (validateDate($sheetData[$i][0]) === false) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][0] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					if ($i > 1 && $sheetData[$i][0] != $sheetData[($i - 1)][0]) {
						$status = false;
						$msg .= 'value pada kolom ' . $sheetData[0][0] . ' baris ' . $i . ' tidak sesuai dengan sebelumnya';
						break;
					} else {
						if (strtotime(input('tgl_os')) < strtotime($sheetData[$i][0])) {
							$data['ficmisDate'] = $sheetData[$i][0];
						} else {
							$status = false;
							$msg .= 'value pada kolom ' . $sheetData[0][0] . ' harus lebih besar dari tanggal ' . input('tgl_os');
							break;
						}
					}
				}

				if (strlen($sheetData[$i][1]) != 12 || substr(strtoupper($sheetData[$i][1]), 0, 2) != 'LD') {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][1] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$cek_loan = $this->db->get_where('tbl_anggota_channeling', ['id_koperasi' => input('id'), 'batch' => input('batch'), 'noloan_anggota' => $sheetData[$i][1]])->num_rows();
					if ($cek_loan > 0 || $cek_kop == 0) {
						$data['noloan_anggota'] = $sheetData[$i][1];
					} else {
						$status = false;
						$msg .= 'value ' . $sheetData[$i][1] . ' pada kolom ' . $sheetData[0][1] . ' tidak sesuai dengan data anggota';
						break;
					}
				}

				if (strlen($sheetData[$i][2]) != 8) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][2] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$cek_cif = $this->db->get_where('tbl_anggota_channeling', ['id_koperasi' => input('id'), 'batch' => input('batch'), 'nocif_anggota' => $sheetData[$i][2]])->num_rows();
					if ($cek_cif > 0 || $cek_kop == 0) {
						$data['nocif_anggota'] = $sheetData[$i][2];
					} else {
						$status = false;
						$msg .= 'value ' . $sheetData[$i][2] . ' pada kolom ' . $sheetData[0][2] . ' tidak sesuai dengan data anggota';
						break;
					}
				}

				if (!preg_match('/^[a-zA-Z ]+$/', $sheetData[$i][3])) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][3] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$data['nm_anggota'] = $sheetData[$i][3];
				}

				if (!is_numeric($sheetData[$i][4])) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][4] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$cek_tenor = $this->db->get_where('tbl_anggota_channeling', ['id_koperasi' => input('id'), 'batch' => input('batch'), 'tenor' => $sheetData[$i][4]])->num_rows();
					if ($cek_tenor > 0 || $cek_kop == 0) {
						$data['tenor'] = $sheetData[$i][4];
					} else {
						$status = false;
						$msg .= 'value ' . $sheetData[$i][4] . ' pada kolom ' . $sheetData[0][4] . ' tidak sesuai dengan data anggota';
						break;
					}
				}

				if (validateDate($sheetData[$i][5]) === false) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][5] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$data['tgl_pencairan'] = $sheetData[$i][5];
				}
				// else {
				// 	if ($i > 1 && $sheetData[$i][5] != $sheetData[($i - 1)][5]) {
				// 		$status = false;
				// 		$msg .= 'value pada kolom ' . $sheetData[0][5] . ' baris ' . $i . ' tidak sesuai dengan sebelumnya';
				// 		break;
				// 	} else {
				// 		if (input('tgl_cair') == '' || input('tgl_cair') == $sheetData[$i][5]) {
				// 			$data['tgl_pencairan'] = $sheetData[$i][5];
				// 		} else {
				// 			$status = false;
				// 			$msg .= 'value pada kolom ' . $sheetData[0][5] . ' tidak sesuai dengan data anggota';
				// 			break;
				// 		}
				// 	}
				// }

				if (!is_float($sheetData[$i][6])) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][6] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$data['nom_Pencairan'] = $sheetData[$i][6];
				}

				if (validateDate($sheetData[$i][7]) === false) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][7] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$data['tgl_ospokok'] = $sheetData[$i][7];
				}

				if (!is_float($sheetData[$i][8])) {
					$status = false;
					$msg .= 'format pada kolom ' . $sheetData[0][8] . ' baris ' . $i . ' tidak sesuai';
					break;
				} else {
					$data['os_pokok'] = $sheetData[$i][8];
				}

				$result[] = $data;
				// var_dump($data);
			}
			// var_dump($result);
			// die;

			if ($status === FALSE) {
				$cek = $this->db->get_where('tbl_anggota_channeling', ['id_koperasi' => input('id')])->num_rows();
				if ($cek == 0) {
					$this->db->delete('tbl_anggota_channeling', ['id_koperasi' => input('id')]);
				}
			} else {
				$this->db->trans_start();
				$this->db->insert_batch('tbl_anggota_channeling', $result);
				$this->db->update(
					'tbl_koperasi',
					[
						// 'tgl_pencairan' => $result[0]['tgl_pencairan'],
						'tgl_ospokok' => $result[0]['tgl_ospokok']
					],
					['id' => input('id')]
				);
				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$msg = 'Data gagal di upload';
				} else {
					$this->db->trans_commit();
					$msg = 'Anda telah berhasil mengimport ' . ($i - 1) . ' daftar anggota koperasi!';
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

	// Export CSV
	public function export($key)
	{
		// $decode = base64_decode($key);
		// $tgl = substr($decode, -8);
		// $rek = substr($decode, 0, strlen($decode) - strlen($tgl));
		// $tgl = substr($tgl, 0, 4) . '-' . substr($tgl, 4, 2) . '-' . substr($tgl, -2);

		$kop = $this->db->get_where('tbl_koperasi', ['id' => base64_decode($key)])->row_array();
		// $area = $this->db->get_where('tbl_area', ['kd_area' => $kop['kd_area']])->row_array();
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
				// 'TUNGGAKAN'
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
				// $csv_row .= $val['tunggakan'] . '|';
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



	// rekonsiliasi
	public function temp_rekonsel()
	{
		$filename = 'template-rekon-channeling'; // set filename for csv file to be exported

		$nm_column = array(
			'REK_PEMBAYARAN',
			'NOLOAN',
			'NAMA_ANGGOTA',
			'BATCH',
			'TENOR',
			'TGL_PENCAIRAN',
			'PLAFOND',
			'TGL_OSPOKOK',
			'OSPOKOK'
		);

		$csv_header = '';
		foreach ($nm_column as $key => $col) {
			$csv_header .= $col . '|';
		}
		$csv_header .= "\n";

		$csv_row = '7081935337|LD1826138904|Hary Sudaryanto|1|12|2019-10-12|50000000.00|2019-12-12|99037645.71|';
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

			$result = array();
			$data = array();
			$data['kode_ao'] = input('kode_ao');
			$data['id_koperasi'] = input('id_koperasi');

			$cek = $this->db->query('select id_koperasi, max(ficmisDate) as ficmisDate, count(distinct(noloan_anggota)) as anggota, tenor, tgl_pencairan, nom_pencairan, tgl_ospokok, os_pokok from tbl_anggota_channeling where id_koperasi = "' . input('id_koperasi') . '" group by id_koperasi')->row_array();
			$cek_loan = $this->db->get_where('tbl_anggota_channeling', ['id_koperasi' => input('id_koperasi'), 'ficmisDate' => $cek['ficmisDate']])->result_array();

			if ((count($sheetData) - 1) > $cek['anggota']) {
				$status = false;
				$msg = 'Terjadi kesalahan, jumlah data anggota tidak sesuai dengan anggota koperasi';
			} else {
				for ($i = 1; $i < count($sheetData); $i++) {
					$msg = 'Terjadi kesalahan pada ';

					if ($sheetData[$i][0] != input('rek_pemb')) {
						$status = false;
						$msg .= 'value pada kolom ' . $sheetData[0][0] . ' baris ke-' . $i . ' tidak sesuai dengan data anggota';
						break;
					} else {
						$data['rek_pembayaran'] = $sheetData[$i][0];
					}

					if (strlen($sheetData[$i][1]) != 12 || substr(strtoupper($sheetData[$i][1]), 0, 2) != 'LD') {
						$status = false;
						$msg .= 'format pada kolom ' . $sheetData[0][1] . ' baris ' . $i . ' tidak sesuai';
						break;
					} else {
						if ($sheetData[$i][1] != $cek_loan[$i - 1]['noloan_anggota']) {
							$status = false;
							$msg .= 'value pada kolom ' . $sheetData[0][1] . ' baris ke-' . $i . ' tidak sesuai dengan data anggota';
							break;
						} else {
							$data['noloan'] = $sheetData[$i][1];
						}
					}

					if (!preg_match('/^[a-zA-Z ]+$/', $sheetData[$i][2])) {
						$status = false;
						$msg .= 'format pada kolom ' . $sheetData[0][2] . ' baris ' . $i . ' tidak sesuai';
						break;
					} else {
						$data['nm_anggota'] = $sheetData[$i][2];
					}

					if (!is_numeric($sheetData[$i][3])) {
						$status = false;
						$msg .= 'format pada kolom ' . $sheetData[0][3] . ' baris ' . $i . ' tidak sesuai';
						break;
					} else {
						if ($sheetData[$i][3] != input('batch')) {
							$status = false;
							$msg .= 'value pada kolom ' . $sheetData[0][3] . ' baris ke-' . $i . ' tidak sesuai dengan data anggota';
							break;
						} else {
							$data['batch'] = $sheetData[$i][3];
						}
					}

					if (!is_numeric($sheetData[$i][4])) {
						$status = false;
						$msg .= 'format pada kolom ' . $sheetData[0][4] . ' baris ' . $i . ' tidak sesuai';
						break;
					} else {
						if ($sheetData[$i][4] != $cek_loan[$i - 1]['tenor']) {
							$status = false;
							$msg .= 'value pada kolom ' . $sheetData[0][4] . ' baris ke-' . $i . ' tidak sesuai dengan data anggota';
							break;
						} else {
							$data['tenor'] = $sheetData[$i][4];
						}
					}

					if (validateDate($sheetData[$i][5]) === false) {
						$status = false;
						$msg .= 'format pada kolom ' . $sheetData[0][5] . ' baris ' . $i . ' tidak sesuai';
						break;
					} else {
						if ($sheetData[$i][5] != $cek['tgl_pencairan']) {
							$status = false;
							$msg .= 'value pada kolom ' . $sheetData[0][5] . ' baris ke-' . $i . ' tidak sesuai dengan data anggota';
							break;
						} else {
							$data['tgl_pencairan'] = $sheetData[$i][5];
						}
					}

					if (!is_double($sheetData[$i][6])) {
						$status = false;
						$msg .= 'format pada kolom ' . $sheetData[0][6] . ' baris ' . $i . ' tidak sesuai';
						break;
					} else {
						$data['plafond'] = $sheetData[$i][6];
					}

					// if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $sheetData[$i][7])) {
					if (validateDate($sheetData[$i][7]) === false) {
						$status = false;
						$msg .= 'format pada kolom ' . $sheetData[0][7] . ' baris ' . $i . ' tidak sesuai';
						break;
					} else {
						$data['tgl_ospokok'] = $sheetData[$i][7];
					}

					if (!is_double($sheetData[$i][8])) {
						$status = false;
						$msg .= 'format pada kolom ' . $sheetData[0][8] . ' baris ' . $i . ' tidak sesuai';
						break;
					} else {
						$data['ospokok'] = $sheetData[$i][8];
					}

					$result[] = $data;
					// var_dump($status);
				}

				if ($status === FALSE) {
					$cek = $this->db->get_where('tbl_rekon_channeling', ['id_koperasi' => input('id_koperasi')])->num_rows();
					if ($cek == 0) {
						$this->db->delete('tbl_rekon_channeling', ['id_koperasi' => input('id_koperasi')]);
					}
				} else {
					$this->db->trans_start();
					$this->db->insert_batch('tbl_rekon_channeling', $result);
					$this->db->update(
						'tbl_koperasi',
						[
							'status' => 'Proses Rekonsiliasi'
						],
						['id' => input('id_koperasi')]
					);
					$this->db->trans_complete();

					if ($this->db->trans_status() === FALSE) {
						$this->db->trans_rollback();
						$msg = 'Data gagal di upload';
					} else {
						$this->db->trans_commit();
						$msg = 'Data Rekonsiliasi telah berhasil di upload!';
					}
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
