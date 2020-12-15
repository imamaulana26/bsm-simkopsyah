<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="<?= base_url('assets/template') ?>/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?= base_url('assets/template') ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('assets/template') ?>/js/adminlte.min.js"></script>
<!-- Datatables BS4 -->
<script src="<?= base_url('assets/template') ?>/plugins/datatables-bs4/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url('assets/template') ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<!-- <script src="<?= base_url('assets/template') ?>/plugins/datatables-rowreorder/js/dataTables.rowReorder.min.js"></script> -->
<script src="<?= base_url('assets/template') ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<!-- Sweetalert2 -->
<script src="<?= base_url('assets/template') ?>/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- bootstrap-selectpicker -->
<script src="<?= base_url('assets/template') ?>/plugins/bootstrap-select/js/bootstrap-select.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
	$(document).ready(function() {
		$('.date').datepicker({
			'format': 'dd M yyyy',
			'todayHighlight': true,
			'autoclose': true,
			'clearBtn': true
		});
		
		$('.invalid-feedback').css('width', 'max-content');
	});

	// function reload_table() {
	// 	$('#tbl_example').DataTable({
	// 		ordering: false,
	// 		rowReorder: {
	// 			selector: 'td:nth-child(2)'
	// 		},
	// 		responsive: true
	// 	});
	// }

	// # check number
	function CheckNumeric() {
		return event.keyCode >= 48 && event.keyCode <= 57;
	}
	// # check number

	// # type number only
	function FormatCurrency(ctrl) {
		//Check if arrow keys are pressed - we want to allow navigation around textbox using arrow keys
		if (event.keyCode == 37 || event.keyCode == 38 || event.keyCode == 39 || event.keyCode == 40) {
			return;
		}

		var val = ctrl.value;

		val = val.replace(/,/g, "")
		ctrl.value = "";
		val += '';
		x = val.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';

		var rgx = /(\d+)(\d{3})/;

		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}

		ctrl.value = x1 + x2;
	}

	function number_format(number, decimals, decPoint, thousandsSep) {
		number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
		var n = !isFinite(+number) ? 0 : +number
		var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
		var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
		var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
		var s = ''

		var toFixedFix = function(n, prec) {
			var k = Math.pow(10, prec)
			return '' + (Math.round(n * k) / k)
				.toFixed(prec)
		}

		// @todo: for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
		}
		if ((s[1] || '').length < prec) {
			s[1] = s[1] || ''
			s[1] += new Array(prec - s[1].length + 1).join('0')
		}

		return s.join(dec)
	}
</script>
