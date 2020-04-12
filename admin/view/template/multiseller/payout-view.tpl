<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ms-payout">
	<div class="page-header">
		<div class="container-fluid">
			<h1><?php echo $heading; ?></h1>

			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>

			<div class="pull-right">
				<a id="ms-pay" style="display: none;" data-toggle="tooltip" title="<?php echo $ms_button_pay; ?>" class="btn btn-primary"><i class="fa fa-paypal"></i></a>
				<a href="<?php echo $back_action; ?>" data-toggle="tooltip" title="<?php echo $ms_back; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="alert alert-danger" style="display: <?php echo (isset($error_warning) && $error_warning) ? 'block' : 'none'; ?>;"><i class="fa fa-exclamation-circle"></i><?php if (isset($error_warning) && $error_warning) { echo $error_warning; } ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>

		<?php if (isset($success) && $success) { ?>
			<div class="alert alert-success" style="display: <?php echo (isset($success) && $success) ? 'block' : 'none'; ?>;"><i class="fa fa-check-circle"></i> <?php if(isset($success) && $success) { echo $success; } ?>
				<button type="button" class="close" data-dismiss="alert">&times;</button>
			</div>
		<?php } ?>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading; ?></h3>
			</div>

			<div class="panel-body">
				<div class="table-responsive">
					<table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-invoices">
						<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$(document).find('#list-invoices input[name*=\'selected\']').prop('checked', $(this).prop('checked'));" /></td>
							<td class="small"><?php echo $ms_payout_invoice . ' ' . $ms_id; ?></td>
							<td><?php echo $ms_seller; ?></td>
							<td class="large"><?php echo $ms_amount; ?></td>
							<td class="large"><?php echo $ms_status; ?></td>
						</tr>
						<tr class="filter">
							<td></td>
							<td><input type="text"/></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
	var msGlobals = {
		token: '<?php echo $token; ?>',
		payout_id: '<?php echo $payout_id; ?>'
	};
</script>
<?php echo $footer; ?>