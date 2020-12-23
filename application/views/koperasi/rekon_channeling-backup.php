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
						<div class="col-md-5">
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

						<div class="col-md">
							<span class="btn btn-info btn-block" style="margin-top: 115px;" onclick="rekon('<?= $bank['id'] ?>')">Rekonsiliasi</span>
						</div>

						<div class="col-md-5">
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
						<div class="col-md-5">
							<div class="card">
								<div class="card-body">
									<table class="table table-hover">
										<thead>
											<tr>
												<th>#</th>
												<th>Nama</th>
												<th>O/S</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($li_bank as $key => $val) : ?>
												<tr>
													<td><?= ($key + 1) ?></td>
													<td><?= $val['nm_anggota'] ?></td>
													<td><?= number_format($val['os_pokok'], 2, '.', ',') ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="col-md-5 offset-2">
							<div class="card">
								<div class="card-body">
									<table class="table table-hover">
										<thead>
											<tr>
												<th>#</th>
												<th>Nama</th>
												<th>O/S</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($li_koperasi as $key => $val) : ?>
												<tr>
													<td><?= ($key + 1) ?></td>
													<td><?= $val['nm_anggota'] ?></td>
													<td><?= number_format($val['ospokok'], 2, '.', ',') ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
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
		});
	</script>
