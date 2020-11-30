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

					<div class="row">
						<div class="col-md">
							<span class="btn btn-primary btn-sm mr-2" id="btn_fm_koperasi" onclick="add_koperasi()">Tambah Koperasi</span>
						</div>
					</div>
				</div><!-- /.container-fluid -->
			</div>
			<!-- /.content-header -->

			<!-- Main content -->
			<div class="content">
				<div class="container-fluid px-5">
					<div class="row">
						<div class="col-md">
							<div class="card card-primary card-outline">
								<div class="card-body">
									<table class="table table-bordered table-hover" id="tbl_example">
										<thead>
											<tr>
												<th>#</th>
												<th>No Kontrak</th>
												<th>No CIF</th>
												<th>Nama Koperasi</th>
												<th>Nom Pencairan</th>
												<th>Outstanding</th>
												<!-- <th class="text-center">Tgl Outstanding</th> -->
												<th class="text-center">Jml Anggota</th>
												<th class="text-center" style="width: 100px;">Opsi</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($list_koperasi as $key => $val) : ?>
												<tr>
													<td><?= $key + 1; ?></td>
													<td><?= $val['noloan_kop']; ?></td>
													<td><?= $val['nocif_kop']; ?></td>
													<td>
														<?= $val['nm_koperasi']; ?>
														<div class="dropdown-divider"></div>
														Tahap <?= $val['tahap_pencairan'] ?>
													</td>
													<td class="text-right"><?= number_format($val['nom_pencairan'], 0, '.', ',') ?></td>
													<td class="text-right"><?= number_format($val['os_pokok'], 0, '.', ',') ?></td>
													<!-- <td class="text-center"><?= tgl_indo(substr($val['createDate'], 0, 10)) ?></td> -->
													<td class="text-center">
														<span class="badge <?= $val['anggota'] > 0 ? 'badge-info' : 'badge-danger' ?>"><?= $val['anggota'], ' anggota'; ?></span>
													</td>
													<td class="text-center">
														<a href="<?= site_url('sales/koperasi/' . $val['noloan_kop']) ?>" class="btn btn-xs btn-outline-info" title="Details"><i class="fas fa-fw fa-eye"></i></a>
														<a href="#" class="btn btn-xs btn-outline-success" title="Sunting"><i class="fas fa-fw fa-edit"></i></a>
														<a href="#" class="btn btn-xs btn-outline-danger" title="Hapus"><i class="fas fa-fw fa-trash"></i></a>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div><!-- /.container-fluid -->
			</div><!-- /.content -->
		</div><!-- /.content-wrapper -->
	</div><!-- ./wrapper -->

	<!-- Modal -->
	<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="formModalLabel">Modal title</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					...
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Save changes</button>
				</div>
			</div>
		</div>
	</div>

	<?php $this->load->view('layout/footer'); ?>

	<script>
		$(document).ready(function() {
			reload_table();
		});

		function add_koperasi() {
			$('#formModal').modal('show');
		}
	</script>
</body>

</html>
