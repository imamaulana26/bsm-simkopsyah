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
						<div class="col-md mt-3">
							<span class="btn btn-primary btn-sm" id="btn_fm_koperasi" onclick="add_koperasi()">Tambah Koperasi</span>
							<button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#upd_modal">
								<i class="fa fa-fw fa-cloud-upload-alt"></i> Upload
							</button>
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
								<div class="card-header">
									<a href="<?= site_url('sales/koperasi/channeling/temp_rekonsel') ?>" class="btn btn-sm btn-link float-right"> Template Rekonsialisasi</a>
									<!-- <a href="#" class="btn btn-sm btn-link float-right"> Rekonsialisasi (<?= $list_rekon['rekon'] < 0 ? 0 : $list_rekon['rekon']; ?>)</a> -->
								</div>
								<div class="card-body">
									<table class="table table-bordered table-hover display nowrap" id="tbl_example">
										<thead>
											<tr>
												<th>#</th>
												<th>Rek. Pembayaran</th>
												<th>Nomor CIF</th>
												<th>Nama Koperasi</th>
												<th>Nama Area</th>
												<th>Nom Pencairan</th>
												<th>Outstanding</th>
												<th class="text-center">Status</th>
												<th class="text-center" style="width: 100px;">Opsi</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($list_koperasi as $key => $val) : ?>
												<tr>
													<td><?= $key + 1; ?></td>
													<td><?= $val['rek_pembayaran']; ?></td>
													<td><?= $val['nocif_kop']; ?></td>
													<td>
														<?= $val['nm_koperasi']; ?>
														<br>
														<small>Tahap <?= $val['tahap_pencairan'] ?> - <?= $val['anggota'] ?> anggota</small>
													</td>
													<td><?= $val['nm_area']; ?></td>
													<td class="text-right">
														<?= number_format($val['plafond'], 2, '.', ',') ?>
														<br>
														<small><?= $val['tgl_pencairan'] == null ? '-' : tgl_indo($val['tgl_pencairan']) ?></small>
													</td>
													<td class="text-right">
														<?= number_format($val['ospokok'], 2, '.', ',') ?>
														<br>
														<small><?= $val['tgl_ospokok'] == null ? '-' : tgl_indo($val['tgl_ospokok']) ?></small>
													</td>
													<td class="text-center">
														<?php if ($val['status'] == 'Belum Terekonsialisasi') : ?>
															<small class="text-warning"><?= $val['status']; ?></small><br>
															<span class="btn btn-sm btn-link" onclick="btn_rekon('<?= $val['id'] ?>')">Upload Rekonsialisasi</span>
														<?php elseif ($val['status'] == 'Proses Rekonsialisasi') : ?>
															<a href="<?= site_url('sales/koperasi-channeling/rekonsel/' . base64_encode($val['id'])) ?>" class="btn btn-sm btn-link">
																<i class="fa fa-fw fa-xs fa-sync"></i> Rekonsialisasi
															</a>
														<?php else : ?>
															<small class="text-success">
																<i class="fa fa-fw fa-check"></i> <?= $val['status']; ?>
															</small><br>
															<small><?= tgl_indo($val['tgl_rekon']); ?></small>
														<?php endif; ?>
														<!-- <span class="badge <?= $val['anggota'] > 0 ? 'badge-info' : 'badge-danger' ?>"><?= $val['anggota'], ' anggota'; ?></span> -->
													</td>
													<td class="text-center">
														<a href="<?= site_url('sales/koperasi-channeling/details/' . base64_encode($val['id'])) ?>" class="btn btn-xs btn-outline-info" title="Anggota"><i class="fas fa-fw fa-users"></i></a>
														<a href="javascript:void(0);" class="btn btn-xs btn-outline-warning" title="Detail" onclick="detail('<?= $val['id'] ?>')">
															<i class="fas fa-fw fa-info-circle"></i>
														</a>
														<a href="javascript:void(0);" class="btn btn-xs btn-outline-success" title="Sunting" onclick="sunting('<?= $val['id'] ?>')">
															<i class="fas fa-fw fa-edit"></i>
														</a>
														<a href="javascript:void(0);" class="btn btn-xs btn-outline-danger" title="Hapus" onclick="hapus('<?= $val['id'] ?>')">
															<i class="fas fa-fw fa-trash"></i>
														</a>
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

	<!-- modal upload -->
	<div class="modal fade" id="upd_modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="upd_modalLabel">Upload daftar koperasi</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="fm_upload">
					<div class="modal-body">
						<label>1. Download file template</label>
						<p>
							Download template file daftar koperasi. File ini memiliki kolom header sesuai data yang diperlukan untuk import daftar koperasi.<br>
							<a href="<?= site_url('sales/koperasi-channeling/template') ?>"><i class="fa fa-fw fa-file-alt"></i> Download File Template</a>
						</p>
						<hr>
						<label>2. Input data template</label>
						<p>Input data daftar koperasi ke dalam file template yang sudah di download. Pastikan bahwa data daftar koperasi sesuai dengan header kolom yang disediakan dalam template.</p>

						<p class="text-danger">PENTING: Dilarang untuk merubah atau menghapus struktur header kolom yang disediakan dalam template upload. Hal ini dilakukan agar proses import bisa berjalan lancar.</p>

						<hr>
						<label>3. Import file template</label>
						<p>Format file yang dapat di import hanya CSV.</p>

						<input type="hidden" class="form-control" name="kode_ao" value="<?= $_SESSION['kd_ao'] ?>">
						<div class="form-group row">
							<label class="col-md-2 col-form-label">File Upload</label>
							<div class="col-md-6">
								<div class="custom-file">
									<input type="file" class="custom-file-input" name="upd_file" accept=".csv" required>
									<label class="custom-file-label" for="upd_file">Choose file</label>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary submit">Upload</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- end of modal upload -->

	<!-- modal upload -->
	<div class="modal fade" id="rekon_modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="rekon_modalLabel">Upload data rekonsialisasi</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="fm_rekon">
					<div class="modal-body">
						<label>Import file template rekonsialisasi</label>
						<p>Format file yang dapat di import hanya CSV.</p>

						<input type="hidden" class="form-control" name="kode_ao" value="<?= $_SESSION['kd_ao'] ?>">
						<input type="hidden" class="form-control" name="id_koperasi" id="id_koperasi">
						<input type="hidden" class="form-control" name="rek_pemb" id="rek_pemb">
						<input type="hidden" class="form-control" name="batch" id="batch">
						<div class="form-group row">
							<label class="col-md-2 col-form-label">File Upload</label>
							<div class="col-md-6">
								<div class="custom-file">
									<input type="file" class="custom-file-input" name="upd_file" accept=".csv" required>
									<label class="custom-file-label" for="upd_file">Choose file</label>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary submit">Upload</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- end of modal upload -->

	<!-- modal form -->
	<div class="modal fade" id="formModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="formModalLabel"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="fm_modal" autocomplete="off">
						<input type="hidden" class="form-control" id="id" name="id">
						<div class="form-group row">
							<label for="rek_pembayaran" class="col-sm-3 col-form-label">Rek. Pembayaran</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="rek_pembayaran" name="rek_pembayaran">
								<div class="invalid-feedback" id="rek_pembayaran-feedback"></div>
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
							<label for="nm_koperasi" class="col-sm-3 col-form-label">Nama Koperasi</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="nm_koperasi" name="nm_koperasi">
								<div class="invalid-feedback" id="nm_koperasi-feedback"></div>
							</div>
						</div>
						<div class="form-group row">
							<label for="tgl_cair" class="col-sm-3 col-form-label">Tgl Pencarain</label>
							<div class="col-sm-3">
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fa fa-fw fa-calendar-alt"></i></div>
									</div>
									<input type="text" class="form-control date" id="tgl_cair" name="tgl_cair">
									<div class="invalid-feedback" id="tgl_cair-feedback"></div>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label for="thp_cair" class="col-sm-3 col-form-label">Tahap Pencarain</label>
							<div class="col-sm-1">
								<input type="text" class="form-control" id="thp_cair" name="thp_cair" onkeypress="return CheckNumeric()" style="width: 55px;">
								<div class="invalid-feedback" id="thp_cair-feedback"></div>
							</div>
						</div>
						<div class="form-group row">
							<label for="nm_area" class="col-sm-3 col-form-label">Nama Area</label>
							<div class="col-sm-5">
								<select name="nm_area" id="nm_area" class="form-control selectpicker" data-live-search="true"></select>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary submit" onclick="save_form()">Submit</button>
				</div>
			</div>
		</div>
	</div>
	<!-- end of modal form -->

	<!-- modal detail -->
	<div class="modal fade" id="detailModal" tabindex="-1">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="detailModalLabel"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="card mt-3">
								<div class="card-body"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end of modal detail -->

	<?php $this->load->view('layout/footer'); ?>

	<script>
		let sts_form = 'add';
		$(document).ready(function() {
			// reload_table();
			area();
			$('.btn-link').removeClass('fa-spinner');
		});

		$(document).on('change', 'input[type="file"]', function(evt) {
			var filename = $(this).val();
			if (filename == undefined || filename == "") {
				$(this).next('.custom-file-label').html('No file chosen');
			} else {
				$(this).next('.custom-file-label').html(evt.target.files[0].name);
			}
		});

		$('#tbl_example').dataTable({
			'ordering': false,
			'responsive': true
		});

		// clear invalid validation
		$('input[type="text"]').on('keypress', function() {
			$(this).removeClass('is-invalid');
			$($(this).attr('id') + '-feedback').empty();
		});

		$('.date').on('change', function() {
			$(this).removeClass('is-invalid');
			$($(this).attr('id') + '-feedback').empty();
		});

		$('#formModal').on('hidden.bs.modal', function() {
			$('#fm_modal')[0].reset();
			$('#noloan').prop('readonly', false);
			$('input').removeClass('is-invalid');
			$('.invalid-feedback').empty();
		});

		$('#fm_upload').on('submit', function(evt) {
			evt.preventDefault();

			$.ajax({
				url: "<?= site_url('sales/koperasi-channeling/import') ?>",
				type: "POST",
				data: new FormData(this),
				dataType: 'JSON',
				processData: false,
				contentType: false,
				cache: false,
				timeout: 50000,
				beforeSend: function() {
					$('.submit').html('<i class="fa fa-fw fa-pulse fa-spinner"></i> Loading');
					$('button').prop('disabled', true);
					$('a').css('pointer-events', 'none');
				},
				success: function(data) {
					if (data.status === true) {
						Swal.fire({
							icon: 'success',
							title: 'Sukses!',
							text: data.msg,
							timer: 2000,
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
							timer: 2000,
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

		$('#fm_rekon').on('submit', function(evt) {
			evt.preventDefault();

			$.ajax({
				url: "<?= site_url('sales/koperasi/channeling/import_rekon') ?>",
				type: "POST",
				data: new FormData(this),
				dataType: 'JSON',
				processData: false,
				contentType: false,
				cache: false,
				timeout: 50000,
				beforeSend: function() {
					$('.submit').html('<i class="fa fa-fw fa-pulse fa-spinner"></i> Loading');
					$('button').prop('disabled', true);
					$('a').css('pointer-events', 'none');
				},
				success: function(data) {
					if (data.status === true) {
						Swal.fire({
							icon: 'success',
							title: 'Sukses!',
							text: data.msg,
							timer: 3000,
							timerProgressBar: true,
							showConfirmButton: false
						}).then((result) => {
							if (result.dismiss === Swal.DismissReason.timer) {
								location.reload();
								// location.href = '<?= site_url('sales/koperasi/rekonsel/rekon_channeling/') ?>' + $('#id_koperasi').val();
							}
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Oops!',
							text: data.msg,
							timer: 3000,
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
		function add_koperasi() {
			$('#formModal').modal('show');

			$('.modal-title').text('Tambah Data Koperasi');
		}

		function area() {
			$.ajax({
				url: '<?= site_url('rest/li_area') ?>',
				type: 'post',
				dataType: 'json',
				success: function(res) {
					var data = res.data;
					var html = '<option value="" selected disabled>-- Please Select --</option>';

					for (let i = 0; i < data.length; i++) {
						html += '<option value="' + data[i].kd_area + '">' + data[i].nm_area + '</option>';
					}

					$('#nm_area').html(html);
					$('.selectpicker').selectpicker('refresh');
				}
			});
		}

		function save_form() {
			var url = '';
			if (sts_form == 'add') url = '<?= site_url('sales/koperasi-channeling/insert') ?>';
			else url = '<?= site_url('sales/koperasi-channeling/update') ?>';

			$.ajax({
				url: url,
				type: 'post',
				dataType: 'json',
				data: $('#fm_modal').serialize(),
				// beforeSend: function() {
				// 	$('.btn-secondary').prop('disabled', true);
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
							$('.invalid-feedback').css('width', 'max-content');
						}
					}
				}
			});
		}

		function sunting(params) {
			sts_form = 'update';
			$.ajax({
				url: '<?= site_url('sales/koperasi-channeling/find/') ?>' + params,
				type: 'get',
				dataType: 'json',
				success: function(res) {
					$('#formModal').modal('show');
					$('.modal-title').text('Sunting Data Koperasi');

					$('#id').val(res.id);
					$('#rek_pembayaran').val(res.rek_pembayaran).prop('readonly', true);
					$('#no_cif').val(res.nocif_kop);
					$('#nm_koperasi').val(res.nm_koperasi);
					$('#thp_cair').val(res.tahap_pencairan);
					$('#tgl_cair').val(tglIndo(res.tgl_pencairan));
					$('#nm_area').val(res.kd_area);

					$('.selectpicker').selectpicker('refresh');
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
						url: "<?= site_url('sales/koperasi-channeling/delete/') ?>" + id,
						type: "POST",
						dataType: "JSON",
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

		function detail(params) {
			$.ajax({
				url: '<?= site_url('sales/koperasi-channeling/') ?>' + params,
				type: 'get',
				dataType: 'json',
				success: function(res) {
					var html = '';
					$('#detailModal').modal('show');
					$('#detailModalLabel').text('Detail Koperasi');

					html += `<div class="row">
									<label class="col-md-2">Rek. Pembayaran</label>
									<div class="col-md-3">
										` + res.koperasi.rek_pembayaran + `
									</div>
									<label class="col-md-2">Nama Koperasi</label>
									<div class="col-md-3">
										` + res.koperasi.nocif_kop + ` - ` + res.koperasi.nm_koperasi + `
									</div>
								</div>`;

					html += `<div class="row">
									<label class="col-md-2">Tahap Pencairan</label>
									<div class="col-md-3">
										Tahap ` + res.koperasi.tahap_pencairan + `
									</div>
									<label class="col-md-2">Nama Area</label>
									<div class="col-md-3">
										` + res.koperasi.nm_area + `
									</div>
								</div>`;

					html += `<div class="row">
									<label class="col-md-2">Jenis Pembiayaan</label>
									<div class="col-md-3">
										` + res.koperasi.jns_pembiayaan + `
									</div>
									<label class="col-md-2">Nominal Pencairan</label>
									<div class="col-md-3">
										Rp ` + number_format(res.koperasi.nom_pencairan, 2, '.', ',') + `
									</div>
								</div>`;

					// html += `<div class="row">
					// 				<label class="col-md-2">Tunggakan</label>
					// 				<div class="col-md-3">
					// 					Rp ` + number_format(res.koperasi.tunggakan, 2, '.', ',') + `
					// 				</div>
					// 				<label class="col-md-2">Outstanding Pokok</label>
					// 				<div class="col-md-3">
					// 					Rp ` + number_format(res.koperasi.os_pokok, 2, '.', ',') + `
					// 				</div>
					// 			</div>`;

					html += `<div class="row">
									<label class="col-md-2">Jumlah Anggota</label>
									<div class="col-md-3">
										` + res.anggota.length + ` Anggota</span>
									</div>
									<label class="col-md-2">Outstanding Pokok</label>
									<div class="col-md-3">
										Rp ` + number_format(res.koperasi.os_pokok, 2, '.', ',') + `
									</div>
								</div>`;

					$('#detailModal .modal-body').html(html);
				}
			});
		}

		function btn_rekon(id) {
			$('#rekon_modal').modal('show');

			$.ajax({
				url: '<?= site_url('sales/koperasi/channeling/get_koperasi/') ?>' + id,
				type: 'post',
				dataType: 'json',
				success: function(data) {
					$('#id_koperasi').val(data.koperasi.id);
					$('#batch').val(data.koperasi.tahap_pencairan);
					$('#rek_pemb').val(data.koperasi.rek_pembayaran);
				}
			});
		}
	</script>
</body>

</html>
