<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<h1><?php echo $heading; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>

			<div class="pull-right">
				<a id="ms-confirm-payout" style="display: none;" data-toggle="tooltip" title="<?php echo $ms_confirm; ?>" class="btn btn-success"><i class="fa fa-check"></i></a>
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<div class="alert alert-danger <?php echo (isset($error_warning) && $error_warning) ? '' : 'hidden'; ?>"><i class="fa fa-exclamation-circle"></i>
			<?php echo (isset($error_warning) && $error_warning) ? $error_warning : ''; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading; ?></h3>
			</div>
			<div class="panel-body">
				<form method="post" enctype="multipart/form-data" id="payout-confirm-form" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_payout_date_payout_period; ?></label>
						<div class="col-sm-10">
							<input type="hidden" name="date_payout_period" value="<?php echo $date_payout_period; ?>" />
							<p style="padding-top: 8px;"><?php echo sprintf($ms_payout_date_payout_period_until, $date_payout_period); ?></p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_payout_selected_sellers; ?></label>
						<div class="col-sm-10">
							<?php if (!empty($sellers)) { ?>
								<ul class="col-sm-6 list-group sellers">
									<?php foreach($sellers as $seller) { ?>
										<li class="list-group-item">
											<input type="hidden" name="sellers[<?php echo $seller['seller_id']; ?>]" value="<?php echo $seller['amount']; ?>" />
											<?php echo $seller['seller_name']; ?>
											<span class="pull-right"><?php echo $seller['amount_formatted']; ?></span>
										</li>
									<?php } ?>
									<li class="list-group-item">
										<strong class="pull-right"><?php echo $ms_total . ': ' . $total_amount_formatted; ?></strong>
									</li>
								</ul>
							<?php } else { ?>
								<div class="alert alert-danger"><?php echo $ms_payout_error_no_sellers; ?></div>
							<?php } ?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	var msGlobals = {
		token: '<?php echo $token; ?>'
	};
</script>
<?php echo $footer; ?>