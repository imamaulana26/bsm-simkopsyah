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
										</div>
									</div>
									<div class="row">
										<label class="col-md-4">O/S <?= substr(tgl_indo($koperasi['tgl_ospokok']), -8) ?></label>
										<div class="col-md">
											<?= 'Rp. ' . number_format($koperasi['ospokok'], 2, '.', ',') ?>
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
								<div class="card-header">
									<div class="dropdown float-right">
										<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Action
										</button>
										<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
											<a class="dropdown-item" href="#">Export CSV</a>
											<a class="dropdown-item" href="#">Update Outstanding</a>
											<a class="dropdown-item" href="#">Reject Rekonsialisasi</a>
										</div>
									</div>
								</div>
								<div class="card-body">
									<table class="table table-hover display" style="width:100%">
										<thead>
											<tr>
												<th rowspan="2" style="width: 10px;">#</th>
												<th colspan="2" style="border-right: 2px solid #dee2e6;"></th>
												<th colspan="2" class="text-center" style="border-right: 2px solid #dee2e6;">Pihak Bank</th>
												<th colspan="2" class="text-center">Pihak Koperasi</th>
											</tr>
											<tr>
												<th>Nomor Loan</th>
												<th style="border-right: 2px solid #dee2e6;">Nama Anggota</th>
												<th class="text-center">Plafond</th>
												<th class="text-center" style="border-right: 2px solid #dee2e6;">Outstanding</th>
												<th class="text-center">Plafond</th>
												<th class="text-center">Outstanding</th>
											</tr>
										</thead>
										<tbody>
											<?php $kolom = array_column($li_koperasi, 'noloan');
											$plafond_bank = 0;
											$os_bank = 0;
											$plafond_kop = array_sum(array_column($li_koperasi, 'plafond'));
											$os_kop = array_sum(array_column($li_koperasi, 'ospokok'));
											foreach ($li_bank as $key => $val) {
												$cari = array_search($val['noloan_anggota'], $kolom);

												$plafond_bank += $val['plafond'];
												$os_bank += $val['ospokok'];

												echo '<tr>';
												echo '<td>' . ($key + 1) . '</td>';
												echo '<td>' . $val['noloan_anggota'] . '</td>';
												echo '<td style="border-right: 2px solid #dee2e6;">' . $val['nm_anggota'] . '</td>';
												echo '<td class="text-center">' . number_format($val['plafond'], 2, '.', ',') . '</td>';
												echo '<td class="text-center" style="border-right: 2px solid #dee2e6;">' . number_format($val['ospokok'], 2, '.', ',') . '</td>';

												if ($cari !== false) {
													echo '<td class="text-center">';
													echo number_format($li_koperasi[$cari]['plafond'], 2, '.', ',');
													if ($li_koperasi[$cari]['plafond'] < $val['plafond']) {
														echo '<i class="fa fa-fw fa-caret-down" style="color: red"></i><br>';
														echo '<small class="text-red">(' . number_format($li_koperasi[$cari]['plafond'] - $val['plafond'], 2, '.', ',') . ')</small>';
													}
													if ($li_koperasi[$cari]['plafond'] > $val['plafond']) {
														echo '<i class="fa fa-fw fa-caret-up" style="color: green"></i><br>';
														echo '<small class="text-success">' . number_format($li_koperasi[$cari]['plafond'] - $val['plafond'], 2, '.', ',') . '</small>';
													}
													echo '</td>';

													echo '<td class="text-center">';
													echo number_format($li_koperasi[$cari]['ospokok'], 2, '.', ',');
													if ($li_koperasi[$cari]['ospokok'] < $val['ospokok']) {
														echo '<i class="fa fa-fw fa-caret-down" style="color: red"></i><br>';
														echo '<small class="text-red">(' . number_format($li_koperasi[$cari]['ospokok'] - $val['ospokok'], 2, '.', ',') . ')</small>';
													}
													if ($li_koperasi[$cari]['ospokok'] > $val['ospokok']) {
														echo '<i class="fa fa-fw fa-caret-up" style="color: green"></i><br>';
														echo '<small class="text-success">' . number_format($li_koperasi[$cari]['ospokok'] - $val['ospokok'], 2, '.', ',') . '</small>';
													}
													echo '</td>';
												} else {
													echo '<td class="text-center">#N/A</td>';
													echo '<td class="text-center">#N/A</td>';
												}
												echo '</tr>';
											} ?>
										</tbody>
										<tfoot style="background-color: #dee2e6;">
											<tr>
												<th>&nbsp;</th>
												<th colspan="2">Subtotal</th>
												<th class="text-center"><?= number_format($plafond_bank, 2, '.', ','); ?></th>
												<th class="text-center"><?= number_format($os_bank, 2, '.', ','); ?></th>
												<th class="text-center">
													<?= number_format($plafond_kop, 2, '.', ','); ?>
													<?php if ($plafond_kop < $plafond_bank) {
														echo '<i class="fa fa-fw fa-caret-down" style="color: red"></i><br>';
														echo '<small class="text-red">(' . number_format($plafond_kop - $plafond_bank, 2, '.', ',') . ')</small>';
													}
													if ($plafond_kop > $plafond_bank) {
														echo '<i class="fa fa-fw fa-caret-up" style="color: green"></i><br>';
														echo '<small class="text-success">' . number_format($plafond_kop - $plafond_bank, 2, '.', ',') . '</small>';
													} ?>
												</th>
												<th class="text-center">
													<?= number_format($os_kop, 2, '.', ','); ?>
													<?php if ($os_kop < $os_bank) {
														echo '<i class="fa fa-fw fa-caret-down" style="color: red"></i><br>';
														echo '<small class="text-red">(' . number_format($os_kop - $os_bank, 2, '.', ',') . ')</small>';
													}
													if ($os_kop > $os_bank) {
														echo '<i class="fa fa-fw fa-caret-up" style="color: green"></i><br>';
														echo '<small class="text-success">' . number_format($os_kop - $os_bank, 2, '.', ',') . '</small>';
													} ?>
												</th>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
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
	</script>
