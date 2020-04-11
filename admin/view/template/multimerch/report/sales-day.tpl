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
		</div>
	</div>

	<div class="container-fluid report-content">
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
				<div class="pull-right">
					<div id="reportrange" class="pull-right">
						<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
						<span></span> <b class="caret"></b>
					</div>
				</div>
			</div>

			<div class="panel-body tab-content">
				<div class="tab-pane active">
					<div class="table-responsive">
						<form class="form-inline" action="" method="post" enctype="multipart/form-data" id="form-reports-sales-day">
							<table class="list mmTable table table-bordered table-hover" id="list-reports-sales-day">
								<thead>
									<tr>
										<td><?php echo $ms_report_column_date; ?></td>
										<td><?php echo $ms_report_column_total_sales; ?></td>
										<td><?php echo $ms_report_column_gross; ?></td>
										<td><?php echo $ms_report_column_net_marketplace; ?></td>
										<td><?php echo $ms_report_column_net_seller; ?></td>
										<td><?php echo $ms_report_column_tax; ?></td>
										<td><?php echo $ms_report_column_shipping; ?></td>
										<td><?php echo $ms_report_column_total; ?></td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var msGlobals = {
		token: '<?php echo $token; ?>',
		ms_report_date_range_today: '<?php echo $ms_report_date_range_today; ?>',
		ms_report_date_range_yesterday: '<?php echo $ms_report_date_range_yesterday; ?>',
		ms_report_date_range_last7days: '<?php echo $ms_report_date_range_last7days; ?>',
		ms_report_date_range_last30days: '<?php echo $ms_report_date_range_last30days; ?>',
		ms_report_date_range_thismonth: '<?php echo $ms_report_date_range_thismonth; ?>',
		ms_report_date_range_lastmonth: '<?php echo $ms_report_date_range_lastmonth; ?>',
		ms_report_date_range_custom: '<?php echo $ms_report_date_range_custom; ?>',
		ms_report_date_range_apply: '<?php echo $ms_report_date_range_apply; ?>',
		ms_report_date_range_cancel: '<?php echo $ms_report_date_range_cancel; ?>',
		ms_report_date_range_day_su: '<?php echo $ms_report_date_range_day_su; ?>',
		ms_report_date_range_day_mo: '<?php echo $ms_report_date_range_day_mo; ?>',
		ms_report_date_range_day_tu: '<?php echo $ms_report_date_range_day_tu; ?>',
		ms_report_date_range_day_we: '<?php echo $ms_report_date_range_day_we; ?>',
		ms_report_date_range_day_th: '<?php echo $ms_report_date_range_day_th; ?>',
		ms_report_date_range_day_fr: '<?php echo $ms_report_date_range_day_fr; ?>',
		ms_report_date_range_day_sa: '<?php echo $ms_report_date_range_day_sa; ?>',
		ms_report_date_range_month_jan: '<?php echo $ms_report_date_range_month_jan; ?>',
		ms_report_date_range_month_feb: '<?php echo $ms_report_date_range_month_feb; ?>',
		ms_report_date_range_month_mar: '<?php echo $ms_report_date_range_month_mar; ?>',
		ms_report_date_range_month_apr: '<?php echo $ms_report_date_range_month_apr; ?>',
		ms_report_date_range_month_may: '<?php echo $ms_report_date_range_month_may; ?>',
		ms_report_date_range_month_jun: '<?php echo $ms_report_date_range_month_jun; ?>',
		ms_report_date_range_month_jul: '<?php echo $ms_report_date_range_month_jul; ?>',
		ms_report_date_range_month_aug: '<?php echo $ms_report_date_range_month_aug; ?>',
		ms_report_date_range_month_sep: '<?php echo $ms_report_date_range_month_sep; ?>',
		ms_report_date_range_month_oct: '<?php echo $ms_report_date_range_month_oct; ?>',
		ms_report_date_range_month_nov: '<?php echo $ms_report_date_range_month_nov; ?>',
		ms_report_date_range_month_dec: '<?php echo $ms_report_date_range_month_dec; ?>'
	};
</script>
<?php echo $footer; ?>