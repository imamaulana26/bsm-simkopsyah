<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rest extends CI_Controller
{
	public function li_region()
	{
		$data = $this->db->get('tbl_region')->result_array();

		echo json_encode(['status' => true, 'data' => $data]);
		exit;
	}

	public function li_area()
	{
		$data = $this->db->get('tbl_area')->result_array();

		echo json_encode(['status' => true, 'data' => $data]);
		exit;
	}

	public function get_area($key)
	{
		$data = $this->db->get_where('tbl_area', ['fk_id_region' => $key])->result_array();

		echo json_encode(['status' => true, 'data' => $data]);
		exit;
	}
}
