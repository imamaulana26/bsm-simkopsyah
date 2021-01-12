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
						<div class="col-md">
							<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#fm_modal">Tambah Anggota</button>
							<button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#upd_modal">
								<i class="fa fa-fw fa-cloud-upload-alt"></i> Upload
							</button>
						</div>
					</div>

					<!-- Details koperasi -->
					<div class="row">
						<div class="col-md-12">
							<div class="card mt-3">
								<div class="card-body">
									<div class="row">
										<label class="col-md-2">Nama Perusahaan</label>
										<div class="col-md-4">
											<?= $koperasi['nm_perusahaan']; ?>
										</div>
										<label class="col-md-2">Nama Koperasi</label>
										<div class="col-md-4">
											<?= $koperasi['nocif_kop'] . ' - ' . $koperasi['nm_koperasi']; ?>
										</div>
									</div>
									<div class="row">
										<label class="col-md-2">Rek. Pembayaran</label>
										<div class="col-md-4">
											<?= $koperasi['rek_pembayaran']; ?>
										</div>
										<label class="col-md-2">Nama Area</label>
										<div class="col-md-4">
											<?= $koperasi['nm_area']; ?>
										</div>
									</div>
									<div class="row">
										<label class="col-md-2">Tahap Pencairan</label>
										<div class="col-md-4">
											Tahap <?= $koperasi['tahap_pencairan']; ?>
										</div>
										<label class="col-md-2">Nominal Pencairan</label>
										<div class="col-md-4">
											Rp <?= number_format($plafond, 2, '.', ',') ?>
										</div>
									</div>
									<div class="row">
										<label class="col-md-2">Jenis Pembiayaan</label>
										<div class="col-md-4">
											<?= $koperasi['jns_pembiayaan']; ?>
										</div>
										<label class="col-md-2">Outstanding Pokok</label>
										<div class="col-md-4">
											Rp <?= number_format($ospokok, 2, '.', ',') ?>
										</div>
									</div>
									<div class="row">
										<label class="col-md-2">Jumlah Anggota</label>
										<div class="col-md-4">
											<?= count($anggota) . ' Anggota'; ?> <span id="btn_collapse" data-toggle="collapse" data-target="#li_anggota" style="cursor: pointer; color: #007BFF;"><i class="fa fa-fw fa-binoculars"></i></span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- End of Details Koperasi -->

					<div class="row">
						<!-- list anggota koperasi -->
						<div class="col-md-12">
							<div class="collapse" id="li_anggota">
								<div class="card card-primary card-outline">
									<div class="card-header">
										<label>Daftar Anggota</label>
										<span class="btn btn-sm btn-danger float-right ml-2" id="btn_close" data-toggle="collapse" data-target="#li_anggota">Close</span>
										<a href="<?= site_url('sales/koperasi-channeling/export/' . $this->uri->segment(4)) ?>" type="button" class="btn btn-sm btn-secondary float-right">
											<i class="fa fa-fw fa-share"></i> Export
										</a>
									</div>
									<div class="card-body">
										<table class="table table-bordered table-hover nowrap" id="tbl_example" style="width: 100%;">
											<thead>
												<tr>
													<th>#</th>
													<th>Nomor Kontrak</th>
													<th>Nomor CIF</th>
													<th>Nama Anggota</th>
													<th>Jangka Waktu</th>
													<th>Plafond Cair (Rp)</th>
													<th>Outstanding (Rp)</th>
													<!-- <th>Tunggakan (Rp)</th> -->
													<th class="text-center">Opsi</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($anggota as $key => $val) : ?>
													<tr>
														<td><?= $key + 1; ?></td>
														<td><?= $val['noloan_anggota']; ?></td>
														<td><?= $val['nocif_anggota']; ?></td>
														<td><?= $val['nm_anggota']; ?></td>
														<td class="text-center"><?= $val['tenor'] . ' bulan' ?></td>
														<td class="text-right">
															<?= number_format($val['nom_pencairan'], 2, '.', ',') ?>
															<br>
															<small class="text-muted"><?= tgl_indo($val['tgl_pencairan']); ?></small>
														</td>
														<td class="text-right">
															<?= number_format($val['os_pokok'], 2, '.', ',') ?>
															<br>
															<small class="text-muted"><?= tgl_indo($val['tgl_ospokok']); ?></small>
														</td>
														<!-- <td class="text-right">
															<?= number_format($val['tunggakan'], 2, '.', ',') ?>
														</td> -->
														<td class="text-center">
															<span class="btn btn-xs btn-outline-success" title="Sunting" onclick="sunting('<?= base64_encode($val['id']) ?>')"><i class="fas fa-fw fa-edit"></i></span>
															<span class="btn btn-xs btn-outline-danger" title="Hapus" onclick="hapus('<?= base64_encode($val['id']) ?>')"><i class="fas fa-fw fa-trash"></i></span>
														</td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<!-- list anggota koperasi -->

					</div>
				</div><!-- /.container-fluid -->
			</div>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

	</div>
	<!-- ./wrapper -->

	<!-- Modal -->
	<div class="modal fade" id="upd_modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="upd_modalLabel">Upload daftar anggota koperasi</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="fm_upload">
					<div class="modal-body">
						<label>1. Download file template</label>
						<p>
							Download template file daftar anggota koperasi. File ini memiliki kolom header sesuai data yang diperlukan untuk import daftar anggota koperasi.<br>
							<a href="<?= site_url('sales/koperasi-channeling/template/end-user') ?>"><i class="fa fa-fw fa-file-alt"></i> Download File Template</a>
						</p>
						<hr>
						<label>2. Input data template</label>
						<p>Input data daftar anggota koperasi ke dalam file template yang sudah di download. Pastikan bahwa data daftar anggota koperasi sesuai dengan header kolom yang disediakan dalam template.</p>

						<p class="text-danger">PENTING: Dilarang untuk merubah atau menghapus struktur header kolom yang disediakan dalam template upload. Hal ini dilakukan agar proses import bisa berjalan lancar. Tanggal diisi dengan format yyyy-mm-dd.</p>

						<hr>
						<label>3. Import file template</label>
						<!-- <input type="hidden" class="form-control" name="rek_pembayaran" value="<?= $koperasi['rek_pembayaran'] ?>"> -->
						<input type="hidden" class="form-control" name="id" value="<?= $koperasi['id'] ?>">
						<input type="hidden" class="form-control" name="batch" value="<?= $koperasi['tahap_pencairan'] ?>">
						<input type="hidden" class="form-control" name="tgl_os" value="<?= $koperasi['tgl_ospokok'] ?>">
						<input type="hidden" class="form-control" name="tgl_cair" value="<?= $koperasi['tgl_pencairan'] ?>">
						<div class="form-group row">
							<label class="col-md-2 col-form-label">File Upload</label>
							<div class="col-md-6">
								<div class="custom-file">
									<input type="file" class="custom-file-input" id="upd_file" name="upd_file" accept=".csv" required>
									<label class="custom-file-label" for="upd_file">Choose file</label>
								</div>
								<small class="text-muted">Format file yang dapat di import hanya CSV</small>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary" id="btnSubmit">Upload</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="fm_modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="fm_anggota_title">Form Tambah Anggota</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="fm_anggota" autocomplete="off">
						<input type="hidden" class="form-control" id="id" name="id">
						<input type="hidden" class="form-control" id="id_koperasi" name="id_koperasi" value="<?= $koperasi['id'] ?>">
						<!-- <input type="hidden" class="form-control" id="rek_pembayaran" name="rek_pembayaran" value="<?= $koperasi['rek_pembayaran'] ?>"> -->
						<input type="hidden" class="form-control" id="batch" name="batch" value="<?= $koperasi['tahap_pencairan'] ?>">
						<div class="form-group row">
							<label for="noloan" class="col-sm-3 col-form-label">Nomor Kontrak</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="noloan" name="noloan">
								<div class="invalid-feedback" id="noloan-feedback"></div>
							</div>
						</div>
						<div class="form-group row">
							<label for="no_cif" class="col-sm-3 col-form-label">Nomor CIF</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="no_cif" name="no_cif" onkeypress="return CheckNumeric()">
								<div class="invalid-feedback" id="no_cif-feedback"></div>
							</div>
						</div>
						<div class="form-group row">
							<label for="nm_anggota" class="col-sm-3 col-form-label">Nama Anggota</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="nm_anggota" name="nm_anggota">
								<div class="invalid-feedback" id="nm_anggota-feedback"></div>
							</div>
						</div>
						<div class="form-group row">
							<label for="tenor" class="col-sm-3 col-form-label">Jangka Waktu</label>
							<div class="col-sm-1">
								<input type="text" class="form-control" id="tenor" name="tenor" onkeypress="return CheckNumeric()" style="width: 55px;">
								<div class="invalid-feedback" id="tenor-feedback"></div>
							</div>
							<label class="col-sm-2 col-form-label">Bulan</label>
						</div>
						<div class="form-group row">
							<label for="rek_pembayaran" class="col-sm-3 col-form-label">Rekening Pembayaran</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="rek_pembayaran" name="rek_pembayaran">
								<div class="invalid-feedback" id="rek_pembayaran-feedback"></div>
							</div>
						</div>
						<div class="form-group row">
							<label for="tgl_cair" class="col-sm-3 col-form-label">Tgl Pencairan</label>
							<div class="col-sm-3">
								<div class="input-group mb-2">
									<input type="text" class="form-control date" name="tgl_cair" id="tgl_cair" onkeypress="return CheckNumeric()">
									<div class="input-group-append">
										<span class="input-group-text" id="btn_tgl">
											<i class="fa fa-fw fa-calendar"></i>
										</span>
									</div>
									<div class="invalid-feedback" id="tgl_cair-feedback"></div>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label for="tgl_ospokok" class="col-sm-3 col-form-label">Tgl Outstanding</label>
							<div class="col-sm-3">
								<div class="input-group mb-2">
									<input type="text" class="form-control date" name="tgl_ospokok" id="tgl_ospokok" onkeypress="return CheckNumeric()">
									<div class="input-group-append">
										<span class="input-group-text" id="btn_tgl">
											<i class="fa fa-fw fa-calendar"></i>
										</span>
									</div>
									<div class="invalid-feedback" id="tgl_ospokok-feedback"></div>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label for="nom_plafond" class="col-sm-3 col-form-label">Plafond Pencairan</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="nom_plafond" name="nom_plafond" onkeypress="return CheckNumeric()" onkeyup="return FormatCurrency(this)">
								<div class="invalid-feedback" id="nom_plafond-feedback"></div>
							</div>
						</div>
						<div class="form-group row">
							<label for="os_pokok" class="col-sm-3 col-form-label">Outstanding</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="os_pokok" name="os_pokok" onkeypress="return CheckNumeric()" onkeyup="return FormatCurrency(this)">
								<div class="invalid-feedback" id="os_pokok-feedback"></div>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" onclick="save_form()" class="btn btn-primary submit">Submit</button>
				</div>
			</div>
		</div>
	</div>

	<?php $this->load->view('layout/footer'); ?>

	<script>
		let sts_form = "add";
		$(document).on('change', 'input[type="file"]', function(evt) {
			var filename = $(this).val();
			if (filename == undefined || filename == "") {
				$(this).next('.custom-file-label').html('No file chosen');
			} else {
				$(this).next('.custom-file-label').html(evt.target.files[0].name);
			}
		});

		// Check browser support
		if (typeof(Storage) !== "undefined") {
			// menampilkan value
			let sess = sessionStorage.getItem("status");

			if (sess == "show") {
				$('#li_anggota').addClass(sess);
			}
		}

		var exp_err = "<?= $this->session->flashdata('export_err') ?>";
		if (exp_err != '') {
			Swal.fire({
				icon: 'error',
				title: 'Error!',
				text: exp_err
			});
		}

		$('#tbl_example').dataTable({
			'ordering': false,
			'responsive': true
		});

		$('#btn_collapse').on('click', function() {
			let status = "show";

			sessionStorage.setItem("status", status);
		});

		$('#btn_close').on('click', function() {
			sessionStorage.removeItem("status");
		});

		// clear invalid validation
		$('input[type="text"]').on('keypress', function() {
			$(this).removeClass('is-invalid');
			$($(this).attr('id') + '-feedback').empty();
		});

		$('#fm_modal').on('hidden.bs.modal', function() {
			$('#fm_anggota')[0].reset();
			$('#noloan').prop('readonly', false);
			$('input').removeClass('is-invalid');
			$('.invalid-feedback').empty();
		});

		$('#fm_upload').on('submit', function(evt) {
			evt.preventDefault();

			$.ajax({
				url: "<?= site_url('sales/koperasi-channeling/import/end-user') ?>",
				type: "POST",
				data: new FormData(this),
				dataType: 'JSON',
				processData: false,
				contentType: false,
				cache: false,
				timeout: 30000, // 3 menit
				beforeSend: function() {
					$('#btnSubmit').html('<i class="fa fa-fw fa-pulse fa-spinner"></i> Loading');
					$('button').prop('disabled', true);
					$('a').css('pointer-events', 'none');
				},
				success: function(data) {
					if (data.status === true) {
						Swal.fire({
							icon: 'success',
							title: 'Sukses!',
							text: data.msg,
							timer: 5000,
							timerProgressBar: true,
							showConfirmButton: false
						}).then((result) => {
							if (result.dismiss === Swal.DismissReason.timer) {
								location.reload();
							}
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Oops!',
							text: data.msg,
							timer: 5000,
							timerProgressBar: true,
							showConfirmButton: false
						}).then((result) => {
							if (result.dismiss === Swal.DismissReason.timer) {
								location.reload();
							}
						});
					}
				},
				error: function(xhr, textStatus, errorThrown) {
					if (textStatus == "timeout") {
						Swal.fire({
							icon: 'error',
							title: 'Oops!',
							text: 'Request timeout!',
							timer: 5000,
							timerProgressBar: true,
							showConfirmButton: false
						}).then((result) => {
							if (result.dismiss === Swal.DismissReason.timer) {
								location.reload();
							}
						});
					}
				}
			});
		});
	</script>

	<script>
		function save_form() {
			var url = '';
			if (sts_form == 'add') url = '<?= site_url('sales/koperasi-channeling/anggota/insert') ?>';
			else url = '<?= site_url('sales/koperasi-channeling/anggota/update') ?>';

			$.ajax({
				url: url,
				type: "POST",
				dataType: "JSON",
				data: $("#fm_anggota").serialize(),
				// beforeSend: function() {
				// 	$('.submit').prop('disabled', true);
				// 	$('.submit').prop('disabled', true).html('<i class="fa fa-fw fa-pulse fa-spinner"></i> Loading');
				// },
				success: function(res) {
					if (res.status == true) {
						$('#fm_modal').modal('hide');

						Swal.fire({
							icon: res.icon,
							title: res.title,
							text: res.msg,
							timer: 2000,
							// timerProgressBar: true,
							// onBeforeOpen: () => {
							// Swal.showLoading()
							// },
							showConfirmButton: false
						}).then((res) => {
							if (res.dismiss === Swal.DismissReason.timer) {
								location.reload();
							}
						})
					} else {
						for (var i = 0; i < res.inputerror.length; i++) {
							$('[name="' + res.inputerror[i] + '" ]').addClass('is-invalid');
							$('#' + res.inputerror[i] + '-feedback').text(res.error[i]);
						}
					}
				}
			});
		}

		function sunting(id) {
			sts_form = "update";
			$.ajax({
				url: "<?= site_url('sales/koperasi-channeling/anggota/edit/') ?>" + id,
				type: "POST",
				dataType: "JSON",
				success: function(res) {
					$('#fm_modal').modal('show');

					$('#fm_anggota_title').text('Sunting Data Anggota');

					$('#id').val(id);
					$('#noloan').val(res.noloan_anggota).prop('readonly', true);
					$('#no_cif').val(res.nocif_anggota);
					$('#nm_anggota').val(res.nm_anggota);
					$('#tenor').val(res.tenor);
					$('#rek_pembayaran').val(res.fk_rek_pembayaran);
					$('#tgl_cair').val(tglIndo(res.tgl_pencairan));
					$('#tgl_ospokok').val(tglIndo(res.tgl_ospokok));
					$('#nom_plafond').val(new Intl.NumberFormat().format(res.nom_pencairan));
					$('#os_pokok').val(new Intl.NumberFormat().format(res.os_pokok));
				}
			});
		}

		function hapus(id) {
			Swal.fire({
				title: 'Hapus data ini?',
				text: "Data yang dihapus tidak bisa dikembalikan lagi!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Hapus',
				cancelButtonText: 'Tidak'
			}).then((result) => {
				if (result.value) {
					$.ajax({
						url: "<?= site_url('sales/koperasi-channeling/anggota/delete/') ?>" + id,
						type: "POST",
						dataType: "JSON",
						data: {
							'key': '<?= $this->uri->segment(3) ?>'
						},
						success: function(res) {
							Swal.fire({
								icon: res.icon,
								title: res.title,
								text: res.msg,
								timer: 2000,
								timerProgressBar: true,
								// onBeforeOpen: () => {
								// Swal.showLoading()
								// },
								showConfirmButton: false
							}).then((result) => {
								if (result.dismiss === Swal.DismissReason.timer) {
									location.reload();
								}
							})
						}
					});
				}
			})
		}

		function tglIndo(tgl) {
			var exp = tgl.split('-');

			var arrBln = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

			return exp[2] + ' ' + arrBln[exp[1] - 1] + ' ' + exp[0];
		}
	</script>
