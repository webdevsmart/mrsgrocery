<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ms-orders-page">
	<div class="page-header">
		<div class="container-fluid">
			<h1><?php echo $ms_order_heading; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="container-fluid">
		<div class="panel panel-default">
			<div class="panel-heading">
				<ul class="nav nav-tabs ms-attributes-topbar">
					<li class="active"><a href="#tab-order" data-toggle="tab"><?php echo $ms_order_tab_orders; ?></a></li>
					<li><a href="#tab-suborder" data-toggle="tab"><?php echo $ms_order_tab_suborders; ?></a></li>
				</ul>
			</div>
			<div class="panel-body tab-content">
				<div class="tab-pane active" id="tab-order">
					<div class="table-responsive">
						<table class="list table table-bordered table-hover" style="text-align: center" id="list-orders">
							<thead>
							<tr>
								<td class="medium"><?php echo $ms_order_column_date_added; ?></a></td>
								<td class="small"><?php echo $ms_order_column_order_id; ?></a></td>
								<td class="large"><?php echo $ms_order_column_order_status; ?></td>
								<td class="large"><?php echo $ms_order_column_order_customer; ?></a></td>
								<td><?php echo $ms_order_column_vendor_statuses; ?></a></td>
								<td><?php echo $ms_order_column_order_total; ?></a></td>
								<td class="small"><?php echo $ms_order_column_action; ?></td>
							</tr>
							<tr class="filter">
								<td><input type="text" class="input-date-datepicker" /></td>
								<td><input type="text" /></td>
								<td><input type="text" /></td>
								<td><input type="text" /></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
				<div class="tab-pane" id="tab-suborder">
					<div class="table-responsive">
						<table class="list table table-bordered table-hover" style="text-align: center" id="list-suborders">
							<thead>
							<tr>
								<td class="medium"><?php echo $ms_order_column_date_added; ?></a></td>
								<td class="small"><?php echo $ms_order_column_order_id; ?></a></td>
								<td class="small"><?php echo $ms_order_column_suborder_id; ?></a></td>
								<td class="large"><?php echo $ms_order_column_order_status; ?></td>
								<td class="large"><?php echo $ms_order_column_order_customer; ?></a></td>
								<td class="large"><?php echo $ms_order_column_order_vendor; ?></a></td>
								<td><?php echo $ms_order_column_order_total; ?></a></td>
								<td class="small"><?php echo $ms_order_column_action; ?></td>
							</tr>
							<tr class="filter">
								<td></td>
								<td><input type="text" /></td>
								<td></td>
								<td><input type="text" /></td>
								<td><input type="text" /></td>
								<td><input type="text" /></td>
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
</div>

<script type="text/javascript">
	$(function() {
		$('#list-orders').dataTable( {
			"sAjaxSource": "index.php?route=multimerch/order/getOrderTableData&token=<?php echo $token; ?>",
			"aoColumns": [
				{ "mData": "date_added" },
				{ "mData": "order_id" },
				{ "mData": "order_status" },
				{ "mData": "customer" },
				{ "mData": "suborders", "bSortable": false },
				{ "mData": "total" },
				{ "mData": "actions", "bSortable": false }
			],
			"aaSorting":  [[0,'desc']]
		});

		$('#list-suborders').dataTable( {
			"sAjaxSource": "index.php?route=multimerch/order/getSubOrderTableData&token=<?php echo $token; ?>",
			"aoColumns": [
				{ "mData": "date_added" },
				{ "mData": "order_id" },
				{ "mData": "suborder_id", "bSortable": false },
				{ "mData": "status" },
				{ "mData": "customer" },
				{ "mData": "seller" },
				{ "mData": "total", "bSortable": false },
				{ "mData": "actions", "bSortable": false }
			],
			"aaSorting":  [[0,'desc']]
		});
	});
</script>
<?php echo $footer; ?>