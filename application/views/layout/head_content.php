<div class="row mb-2">
	<div class="col-sm-6">
		<h3 class="m-0 text-dark"><?= $title; ?></h3>
	</div><!-- /.col -->
	<div class="col-sm-6">
		<?php if (isset($breadcrumb)) : ?>
			<ol class="breadcrumb float-sm-right">
				<?= $breadcrumb; ?>
			</ol>
		<?php endif; ?>
	</div><!-- /.col -->
</div><!-- /.row -->
