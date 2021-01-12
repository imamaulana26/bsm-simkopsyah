<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		// is_login();
	}

	public function index()
	{
		$page = 'sales/v_dashboard';

		$data['title'] = 'Dashboard';

		$sql_1 = "select * from tbl_koperasi a ";
		$sql_1 .= "left join (select id_koperasi, max(ficmisDate) as ficmisDate, count(distinct(noloan_anggota)) as anggota from tbl_anggota_channeling group by id_koperasi) b on a.id = b.id_koperasi ";
		$sql_1 .= "left join tbl_area c on a.kd_area = c.kd_area ";
		$sql_1 .= "where a.fk_kode_ao = '" . $_SESSION['kd_ao'] . "' and a.jns_pembiayaan = 'Channeling' ";
		$sql_1 .= "order by a.id asc ";
		$data['channeling'] = $this->db->query($sql_1)->result_array();

		$sql_2 = "select * from tbl_koperasi a ";
		$sql_2 .= "left join (select id_koperasi, max(ficmisDate) as ficmisDate, count(distinct(noloan_anggota)) as anggota from tbl_anggota_channeling group by id_koperasi) b on a.id = b.id_koperasi ";
		$sql_2 .= "left join tbl_area c on a.kd_area = c.kd_area ";
		$sql_2 .= "where a.fk_kode_ao = '" . $_SESSION['kd_ao'] . "' and a.jns_pembiayaan = 'Eksekuting' ";
		$sql_2 .= "order by a.id asc ";
		$data['eksekuting'] = $this->db->query($sql_2)->result_array();

		$this->load->view($page, $data);
	}
}
