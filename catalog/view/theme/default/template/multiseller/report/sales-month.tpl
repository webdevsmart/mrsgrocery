<?php echo $header; ?>
<div class="container">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
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
				<h1 class="pull-left"><i class="fa fa-list"></i><?php echo $ms_report_sales_month; ?></h1>

				<div id="reportrange" class="pull-right">
					<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
					<span></span> <b class="caret"></b>
				</div>

				<div id="tab-report-sales">
					<div class="table-responsive">
						<table class="mm_dashboard_table table table-borderless table-hover" id="list-report-sales-month">
							<thead>
								<tr>
									<td><?php echo $ms_report_column_date_month; ?></td>
									<td><?php echo $ms_report_column_total_sales; ?></td>
									<td><?php echo $ms_report_column_gross; ?></td>
									<td><?php echo $ms_report_column_net_marketplace; ?></td>
									<td><?php echo $ms_report_column_net_seller; ?></td>
									<td><?php echo $ms_report_column_tax; ?></td>
									<td><?php echo $ms_report_column_shipping; ?></td>
									<td><?php echo $ms_report_column_total; ?></td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>

<script type="text/javascript">
	var msGlobals = {
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