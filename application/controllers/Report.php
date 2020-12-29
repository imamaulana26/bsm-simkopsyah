<?php

use phpDocumentor\Reflection\Types\Integer;

defined('BASEPATH') or exit('No direct script access allowed');
class Report extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('Pdf'); // MEMANGGIL LIBRARY YANG KITA BUAT TADI
	}

	function cetak($id, $tgl_rekon)
	{
		global $title;
		$id = base64_decode($id);
		$tgl_rekon = base64_decode($tgl_rekon);

		$nm_kop = $this->db->get_where('tbl_koperasi', ['id' => $id])->row_array();

		$qry_bank = "select id_koperasi as id, count(noloan_anggota) as anggota, sum(nom_pencairan) as plafond, tgl_ospokok, sum(os_pokok) as ospokok from tbl_anggota_channeling ";
		$qry_bank .= "where tgl_rekon = '" . $tgl_rekon . "' and id_koperasi = '" . $id . "'";
		$bank = $this->db->query($qry_bank)->row_array();

		$koperasi = $this->db->select('id_koperasi, count(noloan) as anggota, sum(plafond) as plafond, sum(ospokok) as ospokok, tgl_ospokok')->from('tbl_rekon_channeling')
			->where(['id_koperasi' => $id, 'kode_ao' => $_SESSION['kd_ao'], 'rekon_date' => $tgl_rekon])
			->get()->row_array();

		$qry_li_bank = "select id_koperasi, noloan_anggota, nm_anggota, tenor, tgl_pencairan, nom_pencairan as plafond, tgl_ospokok, os_pokok as ospokok from tbl_anggota_channeling ";
		$qry_li_bank .= "where tgl_rekon = '" . $tgl_rekon . "' and id_koperasi = '" . $id . "'";
		$li_bank = $this->db->query($qry_li_bank)->result_array();

		$li_koperasi = $this->db->select('id_koperasi, noloan, nm_anggota, tenor, tgl_pencairan, plafond, tgl_ospokok, ospokok')->from('tbl_rekon_channeling')
			->where(['id_koperasi' => $id, 'kode_ao' => $_SESSION['kd_ao'], 'rekon_date' => $tgl_rekon])
			->get()->result_array();

		$pdf = new PDF('L');
		$title = 'Berita Acara Rekonsiliasi Koperasi Channeling';
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFont('Times', '', 12);
		$pdf->Cell(40, 7, 'Assalamu`alaikum Warahmatullahi Wabarakatuh', 0, 1);
		$pdf->Cell(40, 7, 'Berikut ini Kami sampaikan hasil Rekonsiliasi Koperasi Channeling dengan hasil sebagai berikut :', 0, 1);
		$pdf->Ln();

		$pdf->Cell(40, 7, 'Nama Perusahaan', 0, 0, 'L');
		$pdf->Cell(5, 7, ':', 0, 0, 'C');
		$pdf->Cell(40, 7, $nm_kop['nm_perusahaan'], 0, 1, 'L');
		$pdf->Cell(40, 7, 'Nama Koperasi', 0, 0, 'L');
		$pdf->Cell(5, 7, ':', 0, 0, 'C');
		$pdf->Cell(40, 7, $nm_kop['nm_koperasi'], 0, 1, 'L');
		$pdf->Cell(40, 7, 'Tahap Pencairan', 0, 0, 'L');
		$pdf->Cell(5, 7, ':', 0, 0, 'C');
		$pdf->Cell(40, 7, $nm_kop['tahap_pencairan'] . ' (' . ucwords(terbilang($nm_kop['tahap_pencairan'])) . ')', 0, 1, 'L');
		$pdf->Cell(40, 7, 'Tanggal Rekonsiliasi', 0, 0, 'L');
		$pdf->Cell(5, 7, ':', 0, 0, 'C');
		$pdf->Cell(40, 7, tgl_indo($tgl_rekon), 0, 1, 'L');
		$pdf->Ln();

		$pdf->Cell($pdf->GetPageWidth() / 2, 7, 'Pihak Bank', 0, 0);
		$pdf->Cell($pdf->GetPageWidth() / 2, 7, 'Pihak Koperasi', 0, 1);
		$pdf->Cell(40, 7, 'Plafond', 0, 0);
		$pdf->Cell(5, 7, ':', 0, 0, 'C');
		$pdf->Cell($pdf->GetPageWidth() / 2 - 45, 7, 'Rp ' . number_format($bank['plafond'], 2, '.', ','), 0, 0);
		$pdf->Cell(40, 7, 'Plafond', 0, 0);
		$pdf->Cell(5, 7, ':', 0, 0, 'C');
		$pdf->Cell($pdf->GetPageWidth() / 2 - 45, 7, 'Rp ' . number_format($koperasi['plafond'], 2, '.', ','), 0, 1);

		$pdf->Cell(40, 7, 'O/S ' . substr(tgl_indo($bank['tgl_ospokok']), -8), 0, 0);
		$pdf->Cell(5, 7, ':', 0, 0, 'C');
		$pdf->Cell($pdf->GetPageWidth() / 2 - 45, 7, 'Rp ' . number_format($bank['ospokok'], 2, '.', ','), 0, 0);
		$pdf->Cell(40, 7, 'O/S ' . substr(tgl_indo($koperasi['tgl_ospokok']), -8), 0, 0);
		$pdf->Cell(5, 7, ':', 0, 0, 'C');
		$cond = '';
		if ($bank['ospokok'] > $koperasi['ospokok']) {
			$cond .= '(-' . (number_format($bank['ospokok'] - $koperasi['ospokok'], 2, '.', ',')) . ')';
		}
		$pdf->Cell($pdf->GetPageWidth() / 2 - 45, 7, 'Rp ' . number_format($koperasi['ospokok'], 2, '.', ',') . ' ' . $cond, 0, 1);

		$pdf->Cell(40, 7, 'End User', 0, 0);
		$pdf->Cell(5, 7, ':', 0, 0, 'C');
		$pdf->Cell($pdf->GetPageWidth() / 2 - 45, 7, $bank['anggota'] . ' anggota', 0, 0);
		$pdf->Cell(40, 7, 'End User', 0, 0);
		$pdf->Cell(5, 7, ':', 0, 0, 'C');
		$pdf->Cell($pdf->GetPageWidth() / 2 - 45, 7, $koperasi['anggota'] . ' anggota', 0, 1);
		$pdf->Ln();

		$pdf->Cell(88, 7, '', 0, 0);
		$pdf->Cell(95, 7, 'Pihak Bank', 1, 0, 'C');
		$pdf->Cell(95, 7, 'Pihak Koperasi', 1, 0, 'C');
		$pdf->Ln();

		$w = array(8, 35, 45, 25, 35, 35, 25, 35, 35);
		$header = array('#', 'Nomor Loan', 'Nama Anggota', 'Sisa Tenor', 'Plafond', 'Outstanding', 'Sisa Tenor', 'Plafond', 'Outstanding');
		for ($i = 0; $i < count($header); $i++) {
			$pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
		}
		$pdf->Ln();

		$kolom = array_column($li_koperasi, 'noloan');
		$plafond_kop = array_sum(array_column($li_koperasi, 'plafond'));
		$os_kop = array_sum(array_column($li_koperasi, 'ospokok'));

		$null = 0;
		$selisih = 0;
		$plafond_bank = 0;
		$os_bank = 0;

		$tenor_minus = 0;
		$tenor_plus = 0;
		$plafond_minus = 0;
		$plafond_plus = 0;
		$os_minus = 0;
		$os_plus = 0;

		foreach ($li_bank as $key => $val) {
			$cari = array_search($val['noloan_anggota'], $kolom);
			$sisa_tenor = (date('Y', strtotime($val['tgl_ospokok'])) - date('Y', strtotime($val['tgl_pencairan']))) * 12 + (date('m', strtotime($val['tgl_ospokok'])) - date('m', strtotime($val['tgl_pencairan'])));

			$x = $pdf->GetX();
			$pdf->myCell($w[0], 13, $x, $key + 1, 'C');

			$x = $pdf->GetX();
			$pdf->myCell($w[1], 13, $x, $val['noloan_anggota']);

			$x = $pdf->GetX();
			$pdf->myCell($w[2], 13, $x, $val['nm_anggota'], 'J');

			$x = $pdf->GetX();
			$pdf->myCell($w[3], 13, $x, ($val['tenor'] - $sisa_tenor) . ' bulan', 'C');

			$x = $pdf->GetX();
			$pdf->myCell($w[4], 13, $x, number_format($val['plafond'], 2, '.', ','), 'C', false);

			$x = $pdf->GetX();
			$pdf->myCell($w[5], 13, $x, number_format($val['ospokok'], 2, '.', ','), 'C', false);

			if ($cari !== false) {
				$tenor_sisa = (date('Y', strtotime($li_koperasi[$cari]['tgl_ospokok'])) - date('Y', strtotime($li_koperasi[$cari]['tgl_pencairan']))) * 12 + (date('m', strtotime($li_koperasi[$cari]['tgl_ospokok'])) - date('m', strtotime($li_koperasi[$cari]['tgl_pencairan'])));

				$x = $pdf->GetX();
				$pdf->myCell($w[3], 13, $x, ($li_koperasi[$cari]['tenor'] - $tenor_sisa) . ' bulan', 'C');
				if (($li_koperasi[$cari]['tenor'] - $tenor_sisa) < ($val['tenor'] - $sisa_tenor)) {
					$tenor_minus++;
				}
				if (($li_koperasi[$cari]['tenor'] - $tenor_sisa) > ($val['tenor'] - $sisa_tenor)) {
					$tenor_plus++;
				}

				$x = $pdf->GetX();
				$pdf->myCell($w[4], 13, $x, number_format($li_koperasi[$cari]['plafond'], 2, '.', ','), 'C', false);
				if ($li_koperasi[$cari]['plafond'] < $val['plafond']) {
					$plafond_minus++;
				}
				if ($li_koperasi[$cari]['plafond'] > $val['plafond']) {
					$plafond_plus++;
				}

				$x = $pdf->GetX();
				$pdf->myCell($w[5], 13, $x, number_format($li_koperasi[$cari]['ospokok'], 2, '.', ','), 'C', false);
				if ($li_koperasi[$cari]['ospokok'] < $val['ospokok']) {
					$os_minus++;
				}
				if ($li_koperasi[$cari]['ospokok'] > $val['ospokok']) {
					$os_plus++;
				}
			} else {
				$null++;
				$x = $pdf->GetX();
				$pdf->myCell($w[3], 13, $x, '#N/A', 'C', false);
				$x = $pdf->GetX();
				$pdf->myCell($w[4], 13, $x, '#N/A', 'C', false);
				$x = $pdf->GetX();
				$pdf->myCell($w[5], 13, $x, '#N/A', 'C', false);
			}

			$pdf->Ln();
		}
		$pdf->AddPage();

		if (($bank['anggota'] != $koperasi['anggota']) || ($bank['ospokok'] != $koperasi['ospokok'])) {
			$msg = 'tidak sesuai, dengan catatan sebagai berikut :';
		} else {
			$msg = 'telah sesuai.';
		}

		$text = 'Bahwa berdasarkan data diatas, sesuai perhitungan dan pencatatan antara PT BANK SYARIAH MANDIRI dengan ' . strtoupper($nm_kop['nm_koperasi']) . ' menerangkan bahwa data tersebut telah di rekonsiliasi dan ' . $msg;

		$exp = explode(' ', $text);
		$str_1 = '';
		for ($i = 0; $i < 17; $i++) {
			$str_1 .= ' ' . $exp[$i];
		}

		$pdf->Cell($pdf->GetPageWidth() - 20, 7, trim($str_1), 0, 1);
		$pdf->Cell($pdf->GetPageWidth() - 20, 7, trim(substr($text, strlen($str_1))), 0, 1);
		if ((int) $null > 0) {
			$pdf->SetX($pdf->GetX() + 5);
			$pdf->Cell($pdf->GetPageWidth() - 20, 7, chr(149) . ' Terdapat ' . (int) $null . ' nasabah koperasi tidak ditemukan pada data bank.', 0, 1);
		}

		if ((int) ($plafond_minus + $plafond_plus) > 0) {
			$pdf->SetX($pdf->GetX() + 5);
			$pdf->Cell($pdf->GetPageWidth() - 20, 7, chr(149) . ' Terdapat ' . (int) ($plafond_minus + $plafond_plus) . ' nasabah dengan plafond di koperasi tidak sesuai dengan plafond bank.', 0, 1);
		}

		if ((int) ($os_minus + $os_plus) > 0) {
			$pdf->SetX($pdf->GetX() + 5);
			$pdf->Cell($pdf->GetPageWidth() - 20, 7, chr(149) . ' Terdapat ' . (int) ($os_minus + $os_plus) . ' nasabah dengan outstanding di koperasi tidak sesuai dengan outstanding bank.', 0, 1);
		}

		if ((int) $tenor_minus > 0) {
			$pdf->SetX($pdf->GetX() + 5);
			$pdf->Cell($pdf->GetPageWidth() - 20, 7, chr(149) . ' Terdapat ' . (int) $tenor_minus . ' nasabah dengan sisa tenor di koperasi lebih kecil dari sisa tenor bank.', 0, 1);
		}

		if ((int) $tenor_plus > 0) {
			$pdf->SetX($pdf->GetX() + 5);
			$pdf->Cell($pdf->GetPageWidth() - 20, 7, chr(149) . ' Terdapat ' . (int) $tenor_plus . ' nasabah dengan sisa tenor di koperasi lebih besar dari sisa tenor bank.', 0, 1);
		}
		$pdf->Ln();

		$pdf->Cell($pdf->GetPageWidth() - 20, 7, 'Demikian berita acara ini dibuat dengan sebenarnya dan untuk dipergunakan sebagaimana mestinya.', 0, 1);
		$pdf->Ln();

		$pdf->Cell(30, 7, '', 0, 0);
		$arr_bln = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
		$pdf->Cell(30, 7, ', ' . date('d') . ' ' . $arr_bln[date('m')] . ' ' . date('Y'), 0, 1);

		$pdf->Cell(($pdf->GetPageWidth() - 20) / 2, 7, 'Membuat,', 0, 0);
		$pdf->Cell(($pdf->GetPageWidth() - 20) / 2, 7, 'Mengetahui,', 0, 1);
		$pdf->Cell(($pdf->GetPageWidth() - 20) / 2, 7, 'PT BANK SYARIAH MANDIRI', 0, 0);
		$pdf->Cell(($pdf->GetPageWidth() - 20) / 2, 7, $nm_kop['nm_koperasi'], 0, 1);
		$pdf->Ln(25);
		$pdf->Cell(70, 7, $_SESSION['nama'], 0, 1);
		$pdf->SetFont('Times', 'i', 12);
		$pdf->Cell(70, 7, $_SESSION['jabatan'], 0, 0);
		// $pdf->Cell(30, 7, $_SESSION['jabatan'], 0, 1);
		$pdf->Ln();

		// $pdf->SetFont('Times', '', 12);
		// $pdf->Cell(30, 7, 'Mengetahui,', 0, 1);
		// $pdf->Cell(30, 7, $nm_kop['nm_koperasi'], 0, 1);

		$pdf->Output();
	}
}
