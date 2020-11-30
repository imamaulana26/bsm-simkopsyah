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
		$page = 'admin/v_dashboard';

		$data['title'] = 'Dashboard';

		$this->load->view($page, $data);
	}
}
