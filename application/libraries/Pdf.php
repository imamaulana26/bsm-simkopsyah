<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once APPPATH . '/third_party/fpdf/fpdf.php';

class Pdf extends FPDF
{
	function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
	{
		parent::__construct($orientation, $unit, $size);
		date_default_timezone_set('Asia/Jakarta');
	}

	// Page header
	function Header()
	{
		global $title;

		// Logo
		$this->Image('./assets/logo-bsm.png', $this->GetPageWidth() - 38, 5, 30);
		$lebar = $this->w;
		$this->SetFont('Times', 'B', 16);
		$w = $this->GetStringWidth($title);
		$this->SetX(($lebar - $w) / 2);
		$this->Cell($w, 9, $title, 0, 1, 'C');
		$this->Ln();
		$this->Line($this->GetX(), $this->GetY(), $this->GetX() + $lebar - 20, $this->GetY());
		$this->Ln(5);
	}

	// Page footer
	function Footer()
	{
		$this->SetY(-15);
		$lebar = $this->w;

		$this->SetFont('Times', '', 12);
		$this->Line($this->GetX(), $this->GetY(), $this->GetX() + $lebar - 20, $this->GetY());
		$this->SetY(-15);
		$this->SetX(0);
		$this->Ln(1);
		$hal = 'page ' . $this->PageNo() . '/{nb}';
		$this->Cell($this->GetStringWidth($hal), 10, $hal);
		$tgl = date('d-m-Y H:i:s');
		$this->Cell($lebar - $this->GetStringWidth($hal) - $this->GetStringWidth($tgl) - 20);
		$this->Cell($this->GetStringWidth($tgl), 10, $tgl);
	}

	function myCell($w, $h, $x, $t, $align = 'L', $wrap = true)
	{
		$height = $h / 3;
		$first = $height + 2;
		$second = ($height * 3) + 3;
		$len = strlen($t);

		if ($wrap === true) {
			if ($len > 14) {
				$txt = str_split($t, 14);
				$this->SetX($x);
				$this->Cell($w, $first, $txt[0], '', '', '');
				$this->SetX($x);
				$this->Cell($w, $second, $txt[1], '', '', '');
				$this->SetX($x);
				$this->Cell($w, $h, '', 'LTRB', 0, $align, 0);
			} else {
				$this->SetX($x);
				$this->Cell($w, $h, $t, 'LTRB', 0, $align, 0);
			}
		} else {
			$this->SetX($x);
			$this->Cell($w, $h, $t, 'LTRB', 0, $align, 0);
		}
	}
}
