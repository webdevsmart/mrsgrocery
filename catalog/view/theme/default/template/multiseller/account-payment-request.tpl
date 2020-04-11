<?php echo $header; ?>
<div class="container">
	<ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	</ul>
	<?php if (isset($success) && $success) { ?>
		<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
	<?php } ?>

	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
			<?php $class = 'col-sm-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
			<?php $class = 'col-sm-9'; ?>
		<?php } else { ?>
			<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<div id="content" class="<?php echo $class; ?> ms-account-dashboard"><?php echo $content_top; ?>
			<div class="mm_dashboard">
				<h1>
					<i class="fa fa-usd"></i><?php echo $ms_pg_payment_requests; ?>
					<div class="pull-right">
						<button style="display: none;" id="ms-pay" type="submit" form="ms-payment-requests" data-toggle="tooltip" title="<?php echo $ms_button_pay; ?>" class="btn btn-primary"><i class="fa fa-money" style="font-size: inherit; padding: 0; overflow: initial"></i></button>
					</div>
				</h1>

				<div class="error"></div>

				<!-- PAYMENT REQUESTS -->
				<div class="table-responsive">
					<!--<form class="form-inline" action="" method="post" enctype="multipart/form-data" id="form">-->
					<form class="form-inline" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data"  id="ms-payment-requests">

						<table class="mm_dashboard_table table table-borderless table-hover" style="text-align: center" id="list-payment-requests">
							<thead class="sss">
								<tr>
									<td width="25" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'payment_requests\']').prop('checked', $(this).prop('checked'));"></td>
									<td class="mm_size_tiny"><?php echo $ms_pg_request_column_invoice; ?></td>
									<td class="medium"><?php echo $ms_type; ?></td>
									<td class="large"><?php echo $ms_description; ?></td>
									<td class="mm_size_tiny"><?php echo $ms_pg_request_column_payment; ?></td>
									<td class="small"><?php echo $ms_status; ?></td>
									<td class="small"><?php echo $ms_date_created; ?></td>
									<td class="small"><?php echo $ms_amount; ?></td>
								</tr>

								<tr class="filter">
									<td></td>
									<td><input type="text" /></td>
									<td></td>
									<td><input type="text"/></td>
									<td><input type="text" /></td>
									<td></td>
									<td><input type="text" class="input-date-datepicker"/></td>
									<td><input type="text"/></td>
								</tr>
							</thead>

							<tbody></tbody>
						</table>
					</form>
				</div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>

	<script>
		$(function() {
			$('#list-payment-requests').dataTable( {
				"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-payment-request/getTableData",
				"aoColumns": [
					{ "mData": "checkbox", "bSortable": false },
					{ "mData": "request_id" },
					{ "mData": "request_type" },
					{ "mData": "description", "bSortable": false },
					{ "mData": "payment" },
					{ "mData": "request_status" },
					{ "mData": "date_created" },
					{ "mData": "amount" }
				],
				"aaSorting":  [[6,'desc']]
			});
		});

		$(document).on('click', '#list-payment-requests input:checkbox', function() {
			var selected_invoices = $('#list-payment-requests').children('tbody').find('input:checkbox:checked');
			if(selected_invoices.length > 0) {
				$('#ms-pay').show();
			} else {
				$('#ms-pay').hide();
			}
		});

		$("#ms-pay").click(function(e) {
			e.preventDefault();
			if($("#ms-payment-requests tbody input:checkbox:checked").length == 0) {
				var html = '';
				html += '<div class="cl"></div>';
				html += '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ';
				html += '<?php echo htmlspecialchars($ms_pg_request_error_select_payment_request, ENT_QUOTES, "UTF-8"); ?>';
				html += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
				html += '</div>';
				$(".error").html(html);
			} else {
				$(".error").html('');
				$("#ms-payment-requests").submit();
			}
		});

		$(document).on('click', '.paymentInfoControl', function(e) {
			e.preventDefault();
		});
	</script>
</div>
<?php echo $footer; ?>