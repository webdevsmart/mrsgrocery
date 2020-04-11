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
				<a id="ms-create-payout" style="display: none;" data-toggle="tooltip" title="<?php echo $ms_payout_seller_list_generate; ?>" class="btn btn-primary"><?php echo $ms_payout_seller_list_generate; ?></a>
			</div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="alert alert-danger" style="display: <?php echo (isset($error_warning) && $error_warning) ? 'block' : 'none'; ?>;"><i class="fa fa-exclamation-circle"></i><?php if (isset($error_warning) && $error_warning) { echo $error_warning; } ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>

		<div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <?php echo $ms_payout_seller_list_info; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>

		<?php if (isset($success) && $success) { ?>
			<div class="alert alert-success" style="display: <?php echo (isset($success) && $success) ? 'block' : 'none'; ?>;"><i class="fa fa-check-circle"></i> <?php if(isset($success) && $success) { echo $success; } ?>
				<button type="button" class="close" data-dismiss="alert">&times;</button>
			</div>
		<?php } ?>

		<div class="panel panel-default">
			<form class="form-inline" action="<?php echo $generate_action; ?>" method="post" enctype="multipart/form-data" id="payout-form">
				<div class="panel-heading">
					<ul class="nav nav-tabs ms-payouts-topbar">
						<li class="active"><a href="#tab-payout-list" data-toggle="tab"><?php echo $ms_payout_all_payouts; ?></a></li>
						<li><a href="#tab-payout-new" data-toggle="tab"><?php echo $ms_payout_seller_list_generate; ?></a></li>
					</ul>
					<div class="date-filter"></div>
				</div>

				<div class="panel-body tab-content">
					<div class="tab-pane active" id="tab-payout-list">
						<div class="table-responsive">
							<table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-payouts">
								<thead>
									<tr>
										<td class="small"><?php echo $ms_payout_payout . ' ' . $ms_id; ?></td>
										<td class="large"><?php echo $ms_date_created; ?></td>
										<td class="large"><?php echo $ms_payout_date_payout_period; ?></td>
										<td><?php echo $ms_payout_seller_list_payout_name; ?></td>
										<td class="small"><?php echo $ms_action; ?></td>
									</tr>
									<tr class="filter">
										<td><input type="text"/></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>

					<div class="tab-pane" id="tab-payout-new">
						<div class="table-responsive">
							<table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-sellers">
								<thead>
									<tr>
										<td width="1" style="text-align: center;"><input type="checkbox" onclick="$(document).find('#list-sellers input[name*=\'selected\']').prop('checked', $(this).prop('checked'));" /></td>
										<td class="large"><?php echo $ms_seller; ?></td>
										<td class="medium"><?php echo $ms_balance; ?></td>
										<td class="medium"><?php echo $ms_date_last_paid; ?></td>
									</tr>
									<tr class="filter">
										<td></td>
										<td><input type="text"/></td>
										<td></td>
										<td></td>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</form>
		</div>

	</div>
</div>
<script type="text/javascript">
	var msGlobals = {
		token: '<?php echo $token; ?>',
		ms_invoice: '<?php echo $ms_payout_invoice; ?>',
		ms_id: '<?php echo $ms_id; ?>',
		ms_seller: '<?php echo $ms_seller; ?>',
		ms_amount: '<?php echo $ms_amount; ?>',
		ms_status: '<?php echo $ms_status; ?>',
		ms_error_seller_notselected: '<?php echo htmlspecialchars($ms_pg_request_error_seller_notselected, ENT_QUOTES, "UTF-8"); ?>',
		ms_payout_seller_list_refresh: '<?php echo $ms_payout_seller_list_refresh; ?>'
	};
</script>
<?php echo $footer; ?>