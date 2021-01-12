<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<?php $this->load->view('layout/header'); ?>

<body class="hold-transition layout-top-nav">
	<div class="wrapper">
		<?php $this->load->view('layout/navbar'); ?>

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<div class="content-header">
				<div class="container-fluid px-5">
					<?php $this->load->view('layout/head_content'); ?>
				</div><!-- /.container-fluid -->
			</div>
			<!-- /.content-header -->

			<!-- Main content -->
			<div class="content">
				<div class="container-fluid px-5">
					<form action="<?= site_url('sales/koperasi/rekonsel/result') ?>" method="POST">
						<input type="hidden" name="id_kop" id="id_kop" value="<?= $rekon[0]['id_koperasi'] ?>">
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">Tgl Outstanding</label>
							<div class="col-sm-4">
								<select name="bln_rekon" id="bln_rekon" class="form-control">
									<?php foreach ($rekon as $val) {
										$select = '';
										if ($_SESSION['tgl_rekon'] == $val['tgl_ospokok']) $select = 'selected'; ?>
										<option value="<?= $val['tgl_ospokok'] ?>" <?= $select; ?>><?= tgl_indo($val['tgl_ospokok']) ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-sm">
								<button type="submit" class="btn btn-default"><i class="fa fa-fw fa-search"></i> Lihat</button>
								<?php if (isset($bank)) : ?>
									<a href="<?= site_url('report/cetak/' . base64_encode($rekon[0]['id_koperasi']) . '/' . base64_encode($_SESSION['tgl_rekon'])) ?>" target="_blank" class="btn btn-info float-right"><i class="fa fa-fw fa-print"></i> Cetak</a>
								<?php endif; ?>
							</div>
						</div>
					</form>

					<?php if (isset($bank)) : ?>
						<div class="row">
							<div class="col-md-6">
								<div class="card mt-3">
									<div class="card-header">
										Pihak Bank
									</div>
									<div class="card-body">
										<div class="row">
											<label class="col-md-4">Plafond</label>
											<div class="col-md">
												<?= 'Rp. ' . number_format($bank['plafond'], 2, '.', ','); ?>
											</div>
										</div>
										<div class="row">
											<label class="col-md-4">O/S <?= substr(tgl_indo($bank['tgl_ospokok']), -8) ?></label>
											<div class="col-md">
												<?= 'Rp. ' . number_format($bank['ospokok'], 2, '.', ',') ?>
											</div>
										</div>
										<div class="row">
											<label class="col-md-4">End User</label>
											<div class="col-md">
												<?= $bank['anggota'] . ' anggota' ?>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="card mt-3">
									<div class="card-header">
										Pihak Koperasi
									</div>
									<div class="card-body">
										<div class="row">
											<label class="col-md-4">Plafond</label>
											<div class="col-md">
												<?= 'Rp. ' . number_format($koperasi['plafond'], 2, '.', ','); ?>
												<?php if ($koperasi['plafond'] < $bank['plafond']) : ?>
													<i class="fa fa-fw fa-caret-down text-red"></i>
													<small class="text-red">(<?= number_format($koperasi['plafond'] - $bank['plafond'], 2, '.', ','); ?>)</small>
												<?php endif; ?>
												<?php if ($koperasi['plafond'] > $bank['plafond']) : ?>
													<i class="fa fa-fw fa-caret-up text-success"></i>
													<small class="text-success">(<?= number_format($koperasi['plafond'] - $bank['plafond'], 2, '.', ','); ?>)</small>
												<?php endif; ?>
											</div>
										</div>
										<div class="row">
											<label class="col-md-4">O/S <?= substr(tgl_indo($koperasi['tgl_ospokok']), -8) ?></label>
											<div class="col-md">
												<?= 'Rp. ' . number_format($koperasi['ospokok'], 2, '.', ',') ?>
												<?php if ($koperasi['ospokok'] < $bank['ospokok']) : ?>
													<?php if (abs($koperasi['ospokok'] - $bank['ospokok']) > 1000) : ?>
														<i class="fa fa-fw fa-caret-down text-red"></i>
														<small class="text-red">(<?= number_format($koperasi['ospokok'] - $bank['ospokok'], 2, '.', ','); ?>)</small>
													<?php endif; ?>
												<?php endif; ?>

												<?php if ($koperasi['ospokok'] > $bank['ospokok']) : ?>
													<i class="fa fa-fw fa-caret-up text-success"></i>
													<small class="text-success">(<?= number_format($koperasi['ospokok'] - $bank['ospokok'], 2, '.', ','); ?>)</small>
												<?php endif; ?>
											</div>
										</div>
										<div class="row">
											<label class="col-md-4">End User</label>
											<div class="col-md">
												<?= $koperasi['anggota'] . ' anggota' ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title text-bold">Resume</h5><br>
										<ul class="list-unstyled mt-2">
											<?php $msg = 'Data bank dengan data nasabah koperasi ';
											if (($bank['anggota'] != $koperasi['anggota']) || ($bank['ospokok'] != $koperasi['ospokok'])) {
												$msg .= '<b>tidak sesuai</b> sebagai berikut :';
											} else {
												$msg .= '<b>telah sesuai</b>.';
											} ?>
											<li><?= $msg; ?></li>
											<li>
												<ul>
													<span id="display"></span>
												</ul>
											</li>
										</ul>
										<?php if (($bank['anggota'] != $koperasi['anggota']) || ($bank['ospokok'] != $koperasi['ospokok'])) { ?>
											<h5 class="card-title text-bold">Rekomendasi</h5><br>
											<p>Apabila ditemukan selisih pada data koperasi dengan data bank maka harap segera dilakukan <b>Pelunasan</b>.</p>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md">
								<div class="card">
									<div class="card-body">
										<table class="table table-hover">
											<thead style="white-space: nowrap;">
												<tr>
													<th rowspan="2" style="width: 10px;">#</th>
													<th colspan="2" style="border-right: 2px solid #dee2e6;"></th>
													<th colspan="3" class="text-center" style="border-right: 2px solid #dee2e6;">Pihak Bank</th>
													<th colspan="3" class="text-center">Pihak Koperasi</th>
												</tr>
												<tr>
													<th>Nomor Loan</th>
													<th style="border-right: 2px solid #dee2e6;">Nama Anggota</th>
													<th class="text-center">Sisa Tenor</th>
													<th class="text-center">Plafond</th>
													<th class="text-center" style="border-right: 2px solid #dee2e6;">Outstanding</th>
													<th class="text-center">Sisa Tenor</th>
													<th class="text-center">Plafond</th>
													<th class="text-center">Outstanding</th>
												</tr>
											</thead>
											<tbody>
												<?php $kolom = array_column($li_koperasi, 'noloan');
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

												$plafond_kop = array_sum(array_column($li_koperasi, 'plafond'));
												$os_kop = array_sum(array_column($li_koperasi, 'ospokok'));
												foreach ($li_bank as $key => $val) {
													$cari = array_search($val['noloan_anggota'], $kolom);
													$sisa_tenor = (date('Y', strtotime($val['tgl_ospokok'])) - date('Y', strtotime($val['tgl_pencairan']))) * 12 + (date('m', strtotime($val['tgl_ospokok'])) - date('m', strtotime($val['tgl_pencairan'])));

													$plafond_bank += $val['plafond'];
													$os_bank += $val['ospokok'];

													echo '<tr>';
													echo '<td>' . ($key + 1) . '</td>';
													echo '<td>' . $val['noloan_anggota'] . '</td>';
													echo '<td style="border-right: 2px solid #dee2e6;">' . $val['nm_anggota'] . '</td>';
													echo '<td class="text-center">' . ($val['tenor'] - $sisa_tenor) . ' bulan</td>';
													echo '<td class="text-center">' . number_format($val['plafond'], 2, '.', ',') . '</td>';
													echo '<td class="text-center" style="border-right: 2px solid #dee2e6;">' . number_format($val['ospokok'], 2, '.', ',') . '</td>';

													if ($cari !== false) {
														$tenor_sisa = (date('Y', strtotime($li_koperasi[$cari]['tgl_ospokok'])) - date('Y', strtotime($li_koperasi[$cari]['tgl_pencairan']))) * 12 + (date('m', strtotime($li_koperasi[$cari]['tgl_ospokok'])) - date('m', strtotime($li_koperasi[$cari]['tgl_pencairan'])));

														echo '<td class="text-center">' . ($li_koperasi[$cari]['tenor'] - $tenor_sisa) . ' bulan';
														if (($li_koperasi[$cari]['tenor'] - $tenor_sisa) < ($val['tenor'] - $sisa_tenor)) {
															$tenor_minus++;
															echo '<i class="fa fa-fw fa-caret-down" style="color: red"></i><br>';
															echo '<small class="text-red">(' . (($li_koperasi[$cari]['tenor'] - $tenor_sisa) - ($val['tenor'] - $sisa_tenor)) . ' bulan)</small>';
														}
														if (($li_koperasi[$cari]['tenor'] - $tenor_sisa) > ($val['tenor'] - $sisa_tenor)) {
															$tenor_plus++;
															echo '<i class="fa fa-fw fa-caret-up" style="color: green"></i><br>';
															echo '<small class="text-success">' . (($li_koperasi[$cari]['tenor'] - $tenor_sisa) - ($val['tenor'] - $sisa_tenor)) . ' bulan</small>';
														}
														echo '</td>';

														echo '<td class="text-center">';
														echo number_format($li_koperasi[$cari]['plafond'], 2, '.', ',');
														if ($li_koperasi[$cari]['plafond'] < $val['plafond']) {
															$plafond_minus++;
															echo '<i class="fa fa-fw fa-caret-down" style="color: red"></i><br>';
															echo '<small class="text-red">(' . number_format($li_koperasi[$cari]['plafond'] - $val['plafond'], 2, '.', ',') . ')</small>';
														}
														if ($li_koperasi[$cari]['plafond'] > $val['plafond']) {
															$plafond_plus++;
															echo '<i class="fa fa-fw fa-caret-up" style="color: green"></i><br>';
															echo '<small class="text-success">' . number_format($li_koperasi[$cari]['plafond'] - $val['plafond'], 2, '.', ',') . '</small>';
														}
														echo '</td>';

														echo '<td class="text-center">';
														echo number_format($li_koperasi[$cari]['ospokok'], 2, '.', ',');
														if ($li_koperasi[$cari]['ospokok'] < $val['ospokok']) {
															$os_minus++;
															echo '<i class="fa fa-fw fa-caret-down" style="color: red"></i><br>';
															echo '<small class="text-red">(' . number_format($li_koperasi[$cari]['ospokok'] - $val['ospokok'], 2, '.', ',') . ')</small>';
														}
														if ($li_koperasi[$cari]['ospokok'] > $val['ospokok']) {
															$os_plus++;
															echo '<i class="fa fa-fw fa-caret-up" style="color: green"></i><br>';
															echo '<small class="text-success">' . number_format($li_koperasi[$cari]['ospokok'] - $val['ospokok'], 2, '.', ',') . '</small>';
														}
														echo '</td>';
													} else {
														$null++;
														// $selisih++;
														// $selisih_tenor++;
														echo '<td class="text-center">#N/A</td>';
														echo '<td class="text-center">#N/A</td>';
														echo '<td class="text-center">#N/A</td>';
													}
													echo '</tr>';
												} ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div><!-- /.container-fluid -->
			</div>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

	</div>
	<!-- ./wrapper -->

	<?php $this->load->view('layout/footer'); ?>

	<script>
		$('.table').dataTable({
			'ordering': false,
			'searching': false,
			'info': false,
			'lengthChange': false
		});

		$(document).ready(function() {
			if (<?= $null; ?> != 0) {
				$('#display').append('<li>Terdapat ' + <?= $null; ?> + ' nasabah koperasi tidak ditemukan pada data bank.</li>');
			}

			if (<?= ($plafond_minus + $plafond_plus) ?> != 0) {
				$('#display').append('<li>Terdapat ' + <?= $plafond_minus + $plafond_plus ?> + ' nasabah dengan plafond di koperasi tidak sesuai dengan plafond bank.</li>');
			}

			if (<?= $os_minus + $os_plus ?> != 0) {
				$('#display').append('<li>Terdapat ' + <?= $os_minus + $os_plus ?> + ' nasabah dengan outstanding di koperasi tidak sesuai dengan outstanding bank.</li>');
			}

			if (<?= $tenor_minus ?> != 0) {
				$('#display').append('<li>Terdapat ' + <?= $tenor_minus ?> + ' nasabah dengan sisa tenor di koperasi lebih kecil dari sisa tenor bank.</li>');
			}

			if (<?= $tenor_plus ?> != 0) {
				$('#display').append('<li>Terdapat ' + <?= $tenor_plus ?> + ' nasabah dengan sisa tenor di koperasi lebih besar dari sisa tenor bank.</li>');
			}
		});
	</script>
