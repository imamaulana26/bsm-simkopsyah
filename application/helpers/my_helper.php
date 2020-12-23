<?php

function is_login()
{
	$ci = get_instance();
	$role = $ci->session->userdata('role');

	if($role != null){
		if ($role != $ci->uri->segment(1)) {
			session_destroy();
			redirect(site_url('auth'));
		}
	} else {
		session_destroy();
		redirect(site_url('auth'));
	}
}

function validateDate($date, $format = 'Y-m-d')
{
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}

function input($var)
{
	$ci = get_instance();
	$input = htmlentities(strip_tags(trim($ci->input->post($var, true))));
	return $input;
}

function tag_input($type = '', $name = '', $id, $value = '', $string = null)
{
	$input = "<input type='" . $type . "' class='form-control' name='" . $name . "' id='" . $id . "' value='" . $value . "' $string>";
	return $input;
}

function tgl_indo($tgl)
{
	$exp = explode('-', $tgl);
	$arr_bln = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

	$d = $exp[2];
	$m = $arr_bln[(int) $exp[1]];
	$y = $exp[0];

	$tgl = $d . ' ' . substr($m, 0, 3) . ' ' . $y;
	return $tgl;
}

function tgl_db($tgl)
{
	$exp = explode(' ', $tgl);
	$arr_bln = array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

	$d = $exp[0];
	$m = array_search($exp[1], $arr_bln) > 9 ? array_search($exp[1], $arr_bln) : '0' . array_search($exp[1], $arr_bln);
	$y = $exp[2];

	$date = $y . '-' . $m . '-' . $d;
	return $date;
}

function text_slug($str = '')
{
	$text = trim($str);

	if (empty($text)) return '';

	$text = preg_replace("/[^a-zA-Z0-9\-\s]+/", "", $text);
	$text = strtolower(trim($text));
	$text = str_replace(' ', '-', $text);
	$text = $text_ori = preg_replace('/\-{2,}/', '-', $text);

	return $text;
}

function slug_text($slug = '')
{
	$slug = trim($slug);
	if (empty($slug)) return '';
	$slug = str_replace('-', ' ', $slug);
	$slug = ucwords($slug);
	return $slug;
}

function check_user($id)
{
	$ci = get_instance();

	$result = $ci->db->get_where('tbl_user', ['nip' => $id, 'status' => '0']);
	if ($result->num_rows() > 0) {
		return "checked='checked'";
	}
}

function penyebut($nilai)
{
	$nilai = abs($nilai);
	$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
	$temp = "";
	if ($nilai < 12) {
		$temp = " " . $huruf[$nilai];
	} else if ($nilai < 20) {
		$temp = penyebut($nilai - 10) . " belas";
	} else if ($nilai < 100) {
		$temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
	} else if ($nilai < 200) {
		$temp = " seratus" . penyebut($nilai - 100);
	} else if ($nilai < 1000) {
		$temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
	} else if ($nilai < 2000) {
		$temp = " seribu" . penyebut($nilai - 1000);
	} else if ($nilai < 1000000) {
		$temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
	} else if ($nilai < 1000000000) {
		$temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
	} else if ($nilai < 1000000000000) {
		$temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
	} else if ($nilai < 1000000000000000) {
		$temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
	}
	return $temp;
}

function terbilang($nilai)
{
	if ($nilai < 0) {
		$hasil = "minus " . trim(penyebut($nilai));
	} else {
		$hasil = trim(penyebut($nilai));
	}
	return $hasil;
}
