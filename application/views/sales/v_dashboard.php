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
					<!-- Info boxes -->
					<div class="row">
						<div class="col-12 col-sm-6 col-md-3">
							<div class="info-box">
								<span class="info-box-icon bg-info elevation-1"><i class="fas fa-cog"></i></span>

								<div class="info-box-content">
									<span class="info-box-text">CPU Traffic</span>
									<span class="info-box-number">
										10
										<small>%</small>
									</span>
								</div>
								<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
						<!-- /.col -->
						<div class="col-12 col-sm-6 col-md-3">
							<div class="info-box mb-3">
								<span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>

								<div class="info-box-content">
									<span class="info-box-text">Likes</span>
									<span class="info-box-number">41,410</span>
								</div>
								<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
						<!-- /.col -->

						<!-- fix for small devices only -->
						<div class="clearfix hidden-md-up"></div>

						<div class="col-12 col-sm-6 col-md-3">
							<div class="info-box mb-3">
								<span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

								<div class="info-box-content">
									<span class="info-box-text">Sales</span>
									<span class="info-box-number">760</span>
								</div>
								<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
						<!-- /.col -->
						<div class="col-12 col-sm-6 col-md-3">
							<div class="info-box mb-3">
								<span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

								<div class="info-box-content">
									<span class="info-box-text">New Members</span>
									<span class="info-box-number">2,000</span>
								</div>
								<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
						<!-- /.col -->
					</div>
					<!-- /.row -->

					<div class="row">
						<div class="col-md-6 col-sm">
							<div class="card">
								<div class="card-header">Daftar Koperasi Channeling</div>
								<div class="card-body">
									<table class="table table-bordered table-hover" id="tbl_example">
										<thead>
											<tr>
												<td>#</td>
												<td>Nama Koperasi</td>
												<td>Plafond Cair (Rp)</td>
												<td>Outstanding (Rp)</td>
											</tr>
										</thead>
										<tbody>
											<?php if (!empty($channeling)) {
												foreach ($channeling as $key => $chan) { ?>
													<tr>
														<td><?= $key + 1; ?></td>
														<td>
															<a href="<?= site_url('sales/koperasi-channeling/details/' . base64_encode($chan['id'])) ?>"><?= $chan['nm_koperasi'] ?></a><br>
															<small>Tahap <?= $chan['tahap_pencairan'] ?> - <?= $chan['anggota'] == 0 ? 0 : $chan['anggota'] ?> anggota</small>
														</td>
														<td class="text-right">
															<?= number_format($chan['nom_pencairan'], 2, '.', ',') ?>
														</td>
														<td class="text-right">
															<?= number_format($chan['os_pokok'], 2, '.', ',') ?>
															<br>
															<small><?= $chan['tgl_ospokok'] == null ? '-' : tgl_indo($chan['tgl_ospokok']) ?></small>
														</td>
													</tr>
												<?php }
											} else { ?>
												<tr>
													<td colspan="4" class="text-center">Data tidak tersedia</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm">
							<div class="card">
								<div class="card-header">Daftar Koperasi Eksekuting</div>
								<div class="card-body">
									<table class="table table-bordered table-hover" id="tbl_example">
										<thead>
											<tr>
												<td>#</td>
												<td>Nama Koperasi</td>
												<td>Plafond Cair (Rp)</td>
												<td>Outstanding (Rp)</td>
											</tr>
										</thead>
										<tbody>
											<?php if (!empty($eksekuting)) {
												foreach ($eksekuting as $key => $eks) { ?>
													<tr>
														<td><?= $key + 1; ?></td>
														<td>
															<a href="<?= site_url('sales/koperasi-eksekuting/details/' . base64_encode($eks['id'])) ?>"><?= $eks['nm_koperasi'] ?></a><br>
															<small>Tahap <?= $eks['tahap_pencairan'] ?> - <?= $eks['anggota'] == 0 ? 0 : $eks['anggota'] ?> anggota</small>
														</td>
														<td class="text-right">
															<?= number_format($eks['nom_pencairan'], 2, '.', ',') ?>
														</td>
														<td class="text-right">
															<?= number_format($eks['os_pokok'], 2, '.', ',') ?>
															<br>
															<small><?= $eks['tgl_ospokok'] == null ? '-' : tgl_indo($eks['tgl_ospokok']) ?></small>
														</td>
													</tr>
												<?php }
											} else { ?>
												<tr>
													<td colspan="4" class="text-center">Data tidak tersedia</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
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
			'responsive': true
		});
	</script>
</body>

</html>
