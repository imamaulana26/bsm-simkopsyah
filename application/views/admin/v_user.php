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
						<div class="col-md mt-2">
							<span class="btn btn-sm btn-primary mr-2" id="btn_tambah" onclick="add_user()">
								<i class="fa fa-fw fa-plus"></i> Tambah User
							</span>
							<button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#upd_modal">
								<i class="fa fa-fw fa-cloud-upload-alt"></i> Upload
							</button>
							<span class="btn btn-sm btn-success float-right" id="btn_filter">
								<i class="fa fa-fw fa-filter"></i>
								<span id="txt_filter">Show Filter</span>
							</span>
						</div>
					</div>
				</div><!-- /.container-fluid -->
			</div><!-- /.content-header -->

			<!-- Main content -->
			<div class="content">
				<div class="container-fluid px-5">
					<div class="row">
						<div class="col-md-12">
							<div class="collapse" id="fm_filter">
								<div class="card">
									<div class="card-body">
										<form method="post" action="<?= site_url('admin/user/search') ?>">
											<div class="form-row">
												<div class="form-group col-md-2">
													<label>Regional</label>
													<select class="form-control selectpicker" name="fil_region" id="fil_region">
													</select>
												</div>
												<div class="form-group col-md-4">
													<label>Area</label>
													<select class="form-control selectpicker" name="fil_area" id="fil_area">
													</select>
												</div>
												<div class="form-group col-md-2" style="margin-top: 32px;">
													<button type="submit" class="btn btn-info"><i class="fa fa-fw fa-search"></i> Search</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-12">
							<div class="card">
								<div class="card-body">
									<table class="table table-bordered table-hover" id="tbl_example">
										<thead>
											<tr>
												<th class="text-center">#</th>
												<th class="text-center">Status</th>
												<th>NIP</th>
												<th>Kode AO</th>
												<th>Nama Lengkap</th>
												<th>Jabatan</th>
												<th>Nama Area</th>
												<!-- <th>Nama Area</th> -->
												<th>Regional</th>
												<th class="text-center" style="width: 100px;">Opsi</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($user as $key => $val) : ?>
												<tr>
													<td class="text-center"><?= $key + 1; ?></td>
													<td class="text-center">
														<input type="checkbox" style="cursor: pointer;" <?= $val['status'] == 0 ? 'checked' : '' ?> data-id="<?= $val['nip'] ?>">
													</td>
													<td><?= $val['nip']; ?></td>
													<td><?= $val['kode_ao']; ?></td>
													<td>
														<p>
															<?= $val['nama']; ?><br>
															<span style="color: #337ab7"><?= $val['email']; ?></span>
														</p>
													</td>
													<td><?= $val['jabatan']; ?></td>
													<td><?= $val['nm_area']; ?></td>
													<!-- <td><?= $val['nm_cabang']; ?></td> -->
													<td><?= $val['nm_region']; ?></td>
													<td class="text-center">
														<a href="void:javascript()" class="btn btn-xs btn-outline-success" title="Sunting" onclick="sunting('<?= $val['nip'] ?>')">
															<i class="fas fa-fw fa-edit"></i>
														</a>
														<a href="void:javascript()" class="btn btn-xs btn-outline-danger" title="Hapus" onclick="hapus('<?= $val['nip'] ?>')">
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

	<!-- Modal -->
	<div class="modal fade in" id="fm_modal" tabindex="-1" aria-labelledby="fm_modal" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="fm_modal_user">
						<input type="hidden" class="form-control" name="id_user" id="id_user">
						<div class="form-group row">
							<label for="nip_user" class="col-sm-3 col-form-label">NIP User</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="nip_user" id="nip_user" onkeypress="return CheckNumeric()">
								<span class="help-text"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="nip_user" class="col-sm-3 col-form-label">Kode AO</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="kd_ao_user" id="kd_ao_user" onkeypress="return CheckNumeric()">
								<span class="help-text"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="nm_user" class="col-sm-3 col-form-label">Nama Lengkap</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="nm_user" id="nm_user">
								<span class="help-text"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="email_user" class="col-sm-3 col-form-label">Email User</label>
							<div class="col-sm-6">
								<div class="input-group">
									<input type="text" class="form-control" name="email_user" id="email_user">
									<div class="input-group-append">
										<span class="input-group-text">@bsm.co.id</span>
									</div>
								</div>
								<span class="help-text"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="jbtn_user" class="col-sm-3 col-form-label">Jabatan</label>
							<div class="col-sm-3">
								<select class="form-control selectpicker" name="jbtn_user" id="jbtn_user">
									<option value="ABBM">ABBM</option>
									<option value="BBRM">BBRM</option>
									<option value="Jr. BBRM">Jr. BBRM</option>
								</select>
								<span class="help-text"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="region_user" class="col-sm-3 col-form-label">Regional</label>
							<div class="col-sm-3">
								<select class="form-control selectpicker" name="region_user" id="region_user">
								</select>
								<span class="help-text"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="area_user" class="col-sm-3 col-form-label">Nama Area</label>
							<div class="col-sm-6">
								<select class="form-control selectpicker" name="area_user" id="area_user">
								</select>
								<span class="help-text"></span>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<span class="btn btn-outline-primary" onclick="fm_submit()"><i class="fa fa-fw fa-save"></i> Simpan</span>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="upd_modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="upd_modalLabel">Upload daftar marketing</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="fm_upload">
					<div class="modal-body">
						<label>1. Download file template</label>
						<p>
							Download template file marketing. File ini memiliki kolom header sesuai data yang diperlukan untuk import marketing.<br>
							<a href="<?= site_url('admin/user/template/') ?>"><i class="fa fa-fw fa-file-alt"></i> Download File Template</a>
						</p>
						<hr>
						<label>2. Input data template</label>
						<p>Input data marketing ke dalam file template yang sudah di download. Pastikan bahwa data marketing sesuai dengan header kolom yang disediakan dalam template.</p>

						<p class="text-danger">PENTING: Dilarang untuk merubah atau menghapus struktur header kolom yang disediakan dalam template upload. Hal ini dilakukan agar proses import bisa berjalan lancar. Tanggal diisi dengan format yyyy-mm-dd.</p>

						<hr>
						<label>3. Import file template</label>
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

	<?php $this->load->view('layout/footer'); ?>

	<script>
		var method = 'add';
		$(document).ready(function() {
			$('.selectpicker').selectpicker();

			list_ro();
			reload_table();
		});

		$(document).on('keydown', 'input', function() {
			$(this).parents().removeClass('is-invalid');
			$(this).removeClass('is-invalid');

			if ($(this).attr('id') == 'email_user') {
				$(this).parent().next().empty();
			} else {
				$(this).css('text-transform', 'uppercase');
				$(this).next().empty();
			}
		});

		$(document).on('change', 'select', function() {
			$(this).parent().removeClass('is-invalid');
			$(this).removeClass('is-invalid');
			$(this).parent().next().removeClass('invalid-feedback');
		});

		$('tbody').on('click', 'input[type="checkbox"]', function() {
			const id = $(this).data('id');

			$.ajax({
				url: "<?= site_url(ucfirst('admin/user/upd_status')) ?>",
				type: "POST",
				dataType: "JSON",
				data: {
					id: id
				},
				success: function(res) {
					Swal.fire({
						title: "Sukses",
						text: res.msg,
						icon: 'success',
						timer: 2000,
						showConfirmButton: false
					}).then((result) => {
						if (result.dismiss === Swal.DismissReason.timer) {
							location.reload();
						}
					});
				}
			});
		});

		$('#fm_modal').on('hidden.bs.modal', function() {
			reset_form();
		})
	</script>

	<script>
		function reset_form() {
			$('#fm_modal_user')[0].reset();
			$('.form-control, .input-group').removeClass('is-invalid');
			$('.form-control').next().removeClass('is-invalid');
			$('.help-text').removeClass('invalid-feedback').empty();
		}

		function add_user() {
			$('#fm_modal').modal('show');
			$('.modal-title').text('Form tambah user')

			$('.selectpicker').selectpicker();
		}

		function list_ro() {
			$.ajax({
				url: '<?= site_url('rest/li_region') ?>',
				type: 'POST',
				dataType: 'JSON',
				success: function(res) {
					var data = res.data;
					var html = '<option selected disabled>-- Please Select --</option>';

					for (let i = 0; i < data.length; i++) {
						html += '<option value="' + data[i].id_region + '">' + data[i].nm_region + '</option>';
					}

					$('#region_user').html(html);
					$('#fil_region').html(html);
					$('.selectpicker').selectpicker('refresh');
				}
			});
		}

		function get_area(key, area) {
			$.ajax({
				url: '<?= site_url('rest/get_area/') ?>' + key,
				type: 'POST',
				dataType: 'JSON',
				success: function(res) {
					var data = res.data;
					var html = '';
					var select = '';

					for (let i = 0; i < data.length; i++) {
						if (data[i].kd_area == area) select = 'selected';
						else select = '';

						html += '<option value="' + data[i].kd_area + '" ' + select + '>' + data[i].nm_area + '</option>';
					}

					$('#area_user').html(html);
					$('.selectpicker').selectpicker('refresh');
				}
			});
		}

		function sunting(key) {
			method = 'update';

			$('#fm_modal').modal('show');
			$('.modal-title').text('Form sunting user');

			$.ajax({
				url: '<?= site_url('admin/user/getUser/') ?>' + key,
				type: 'POST',
				dataType: 'JSON',
				success: function(res) {
					let email = res.email.split('@');

					$('#id_user').val(res.id_user);
					$('#nip_user').val(res.nip);
					$('#kd_ao_user').val(res.kode_ao);
					$('#nm_user').val(res.nama);
					$('#email_user').val(email[0]);
					$('#jbtn_user').val(res.jabatan);
					$('#region_user').val(res.id_region);
					get_area(res.id_region, res.kd_area);
					$('.selectpicker').selectpicker('refresh');
				}
			});
		}

		function hapus(id) {
			Swal.fire({
				title: "Apakah anda yakin?",
				text: "Data yang dihapus tidak bisa dikembalikan!",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Hapus',
				cancelButtonText: 'Tidak'
			}).then((result) => {
				if (result.value) {
					$.ajax({
						url: "<?= site_url('admin/user/delete/') ?>" + id,
						type: "GET",
						dataType: "JSON",
						success: function(data) {
							Swal.fire({
								title: "Sukses",
								text: data.msg,
								icon: 'success',
								timer: 2000,
								showConfirmButton: false
							}).then((result) => {
								if (result.dismiss === Swal.DismissReason.timer) {
									location.reload();
								}
							});
						}
					});
				}
			})
		}

		function fm_submit() {
			var url = '';
			if (method == 'add') url = '<?= site_url('admin/user/insert') ?>';
			else url = '<?= site_url('admin/user/update') ?>';

			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'JSON',
				data: $('#fm_modal_user').serialize(),
				success: function(result) {
					if (result.status == true) {
						Swal.fire({
							title: "Sukses",
							text: result.msg,
							icon: 'success',
							timer: 2000,
							showConfirmButton: false
						}).then((result) => {
							if (result.dismiss === Swal.DismissReason.timer) {
								$('#modal_user').modal('hide');
								location.reload();
							}
						});
					} else {
						for (var i = 0; i < result.inputerror.length; i++) {
							$('[name="' + result.inputerror[i] + '"]').addClass('is-invalid');

							if (result.error[i] == '') {
								$('[name="' + result.inputerror[i] + '"]').parent().addClass('is-invalid');
								$('[name="' + result.inputerror[i] + '"]').parent().next().addClass('invalid-feedback');
							} else if (result.inputerror[i] == 'email_user') {
								$('[name="' + result.inputerror[i] + '"]').parent().addClass('is-invalid');
								$('[name="' + result.inputerror[i] + '"]').parent().next().addClass('invalid-feedback');
								$('[name="' + result.inputerror[i] + '"]').parent().next().text(result.error[i]);
							} else {
								$('[name="' + result.inputerror[i] + '"]').next().addClass('invalid-feedback');
								$('[name="' + result.inputerror[i] + '"]').next().text(result.error[i]);
							}
						}
					}
				}
			});
		}
	</script>

	<script>
		$('#region_user, #fil_region').on('change', function() {
			let key = $(this).val();
			$.ajax({
				url: '<?= site_url('rest/get_area/') ?>' + key,
				type: 'POST',
				dataType: 'JSON',
				success: function(res) {
					var data = res.data;
					var html = '';

					for (let i = 0; i < data.length; i++) {
						html += '<option value="' + data[i].kd_area + '">' + data[i].nm_area + '</option>';
					}

					$('#area_user').html(html);
					$('#fil_area').html(html);
					$('.selectpicker').selectpicker('refresh');
				}
			});
		});

		$('#fm_modal').on('hidden.bs.modal', function(evt) {
			$('#fm_modal_user')[0].reset();

			$('#area_user').html('<option selected disabled>-- Please Select --</option>');
			$('.selectpicker').selectpicker('refresh');
		});

		$('#btn_filter').on('click', function() {
			let status = $('#txt_filter').text().split(' ');
			if (status[0] == 'Show') {
				$('#fm_filter').collapse('show');
				$('#txt_filter').text('Hide Filter');
			} else {
				$('#fm_filter').collapse('hide');
				$('#txt_filter').text('Show Filter');
			}
		});
	</script>
</body>

</html>
