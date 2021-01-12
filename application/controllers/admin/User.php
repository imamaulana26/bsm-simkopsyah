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



	public function template()
	{
		$filename = 'template-marketing'; // set filename for csv file to be exported

		$nm_column = array(
			'NIP',
			'KODE_AO',
			'NAMA_LENGKAP',
			'EMAIL',
			'JABATAN',
			'KD_AREA',
			'DATE'
		);

		$csv_header = '';
		foreach ($nm_column as $key => $col) {
			$csv_header .= $col . '|';
		}
		$csv_header .= "\n";

		$csv_row = '108074909|91002593|JUNAIDI|junaidi4909@bsm.co.id|BBRM|ID0010011|2019-01-31|' . "\n";
		$csv_row = '108475212|91002539|RAFLI WINALDY|rwinaldy@bsm.co.id|Jr. BBRM|ID0010023|2019-01-31|' . "\n";
		$csv_row .= "\n";

		/* Download as CSV File */
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename . '.csv');
		echo $csv_header . $csv_row;
		exit;
	}

	public function upload_user()
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
			$cek_tgl_os = $this->db->get_where('tbl_koperasi', ['id' => input('id_koperasi')])->row_array();

			if ((count($sheetData) - 1) > $cek['anggota']) {
				$status = false;
				$msg = 'Terjadi kesalahan, jumlah data anggota tidak sesuai dengan anggota koperasi';
			} else {
				for ($i = 1; $i < count($sheetData); $i++) {
					$msg = 'Terjadi kesalahan pada ';

					// if ($sheetData[$i][0] != $cek_loan[$i - 1]['fk_rek_pembayaran']) {
					// 	$status = false;
					// 	$msg .= 'value pada kolom ' . $sheetData[0][0] . ' baris ke-' . $i . ' tidak sesuai dengan data anggota';
					// 	break;
					// } else {
					// 	$data['rek_pembayaran'] = $sheetData[$i][0];
					// }
					if (array_search($sheetData[$i][0], array_column($cek_loan, 'fk_rek_pembayaran')) === false) {
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
						// if ($sheetData[$i][1] != $cek_loan[$i - 1]['noloan_anggota']) {
						// 	$status = false;
						// 	$msg .= 'value pada kolom ' . $sheetData[0][1] . ' baris ke-' . $i . ' tidak sesuai dengan data anggota';
						// 	break;
						// } else {
						// 	$data['noloan'] = $sheetData[$i][1];
						// }
						if (array_search($sheetData[$i][1], array_column($cek_loan, 'noloan_anggota')) === false) {
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
						if (array_search($sheetData[$i][4], array_column($cek_loan, 'tenor')) === false) {
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
						if (array_search($sheetData[$i][5], array_column($cek_loan, 'tgl_pencairan')) === false) {
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
						if (array_search($sheetData[$i][6], array_column($cek_loan, 'nom_pencairan')) === false) {
							$status = false;
							$msg .= 'value pada kolom ' . $sheetData[0][4] . ' baris ke-' . $i . ' tidak sesuai dengan data anggota';
							break;
						} else {
							$data['plafond'] = $sheetData[$i][6];
						}
					}

					// if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $sheetData[$i][7])) {
					if (validateDate($sheetData[$i][7]) === false) {
						$status = false;
						$msg .= 'format pada kolom ' . $sheetData[0][7] . ' baris ' . $i . ' tidak sesuai';
						break;
					} else {
						if ($sheetData[$i][7] != $cek_tgl_os['tgl_ospokok']) {
							$status = false;
							$msg .= 'format pada kolom ' . $sheetData[0][7] . ' baris ' . $i . ' tidak sesuai dengan data koperasi';
							break;
						} elseif ($i > 1) {
							if ($sheetData[$i][7] != $sheetData[($i - 1)][7]) {
								$status = false;
								$msg .= 'format pada kolom ' . $sheetData[0][7] . ' baris ' . $i . ' tidak sesuai dengan baris sebelumnya';
								break;
							}
						} else {
							$data['tgl_ospokok'] = $sheetData[$i][7];
						}
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
