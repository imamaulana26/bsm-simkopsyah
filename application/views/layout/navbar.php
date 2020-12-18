<!-- Navbar -->
<nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
	<div class="container-fluid px-5">
		<a href="" class="navbar-brand">
			<img src="<?= base_url() . 'assets/template' ?>/img/logo-bsm.png" alt="logo-bsm" class="brand-image">
			<span class="brand-text font-weight-light">|</span>
		</a>

		<button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse order-3" id="navbarCollapse">
			<!-- Left navbar links -->
			<ul class="navbar-nav">
				<?php $akses = $this->session->userdata('role'); ?>
				<li class="nav-item">
					<a href="<?= site_url($akses . '/home') ?>" class="nav-link">Home</a>
				</li>
				<?php if ($akses == 'admin') { ?>
					<li class="nav-item dropdown">
						<a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Module</a>
						<ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
							<li><a href="<?= site_url('admin/user') ?>" class="dropdown-item">Management User</a></li>
							<li><a href="<?= site_url('admin/outlet') ?>" class="dropdown-item">Management Outlet</a></li>
						</ul>
					</li>
				<?php } else { ?>
					<li class="nav-item dropdown">
						<a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Koperasi</a>
						<ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow mt-2">
							<li><a href="<?= site_url($akses . '/koperasi-eksekuting') ?>" class="dropdown-item">Eksekuting</a></li>
							<li><a href="<?= site_url($akses . '/koperasi-channeling') ?>" class="dropdown-item">Channeling</a></li>
						</ul>
					</li>
				<?php } ?>

				<!-- <li class="nav-item dropdown">
					<a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Dropdown</a>
					<ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
						<li><a href="#" class="dropdown-item">Some action </a></li>
						<li><a href="#" class="dropdown-item">Some other action</a></li>

						<li class="dropdown-divider"></li>

						<li class="dropdown-submenu dropdown-hover">
							<a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Hover for action</a>
							<ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
								<li>
									<a tabindex="-1" href="#" class="dropdown-item">level 2</a>
								</li>

								<li class="dropdown-submenu">
									<a id="dropdownSubMenu3" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">level 2</a>
									<ul aria-labelledby="dropdownSubMenu3" class="dropdown-menu border-0 shadow">
										<li><a href="#" class="dropdown-item">3rd level</a></li>
										<li><a href="#" class="dropdown-item">3rd level</a></li>
									</ul>
								</li>

								<li><a href="#" class="dropdown-item">level 2</a></li>
								<li><a href="#" class="dropdown-item">level 2</a></li>
							</ul>
						</li>
					</ul>
				</li> -->
			</ul>
		</div>

		<!-- Right navbar links -->
		<ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
			<li class="nav-item">
				<a href="#" class="nav-link" data-toggle="dropdown"><?= ucfirst($this->session->userdata('nama')) ?></a>
				<div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
					<a href="<?= site_url('auth/logout') ?>" class="dropdown-item">
						<i class="fas fa-sign-out-alt"></i> Logout
					</a>
				</div>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="dropdown" href="#" id="profileImage">
					<?php $role = $this->session->userdata('role');
					$name = $this->session->userdata('nama');
					$alias = '';
					$len_name = explode(' ', $name);
					if ($role == 'admin') {
						$alias = 'AD';
					} else {
						if (count($len_name) > 1) {
							$alias = ucfirst(substr($len_name[0], 0, 1));
							$alias .= ucfirst(substr($len_name[1], 0, 1));
						} else {
							$alias = strtoupper(substr($name, 0, 2));
						}
					} ?>
					<p><?= $alias; ?></p>
				</a>
				<div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
					<a href="<?= site_url('auth/logout') ?>" class="dropdown-item">
						<i class="fas fa-sign-out-alt"></i> Logout
					</a>
				</div>
			</li>
		</ul>
	</div>
</nav>
<!-- /.navbar -->
