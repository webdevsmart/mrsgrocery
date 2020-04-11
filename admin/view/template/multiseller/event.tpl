<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ms-events-page">
	<div class="page-header">
		<div class="container-fluid">
			<h1><?php echo $ms_event_heading; ?></h1>
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
				<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $ms_event_heading; ?></h3>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="list table table-bordered table-hover" style="text-align: center" id="list-events">
						<thead>
							<tr>
								<td class="large" id="event_type_column"><?php echo $ms_event_column_event; ?></a></td>
								<td><?php echo $ms_event_column_description; ?></a></td>
								<td class="large"><?php echo $ms_date; ?></a></td>
							</tr>
							<tr class="filter">
								<td>
									<select id="event_type_select">
										<option></option>
										<?php $msEvent = new ReflectionClass('\MultiMerch\Event\Event'); ?>

										<optgroup label="<?php echo $this->language->get('ms_event_product'); ?>">
											<?php foreach ($msEvent->getConstants() as $cname => $cval) { ?>
												<?php if (strpos($cname, 'PRODUCT_') !== FALSE) { ?>
													<option value="<?php echo $cval; ?>"><?php echo $this->language->get('ms_event_type_' . $cval); ?></option>
												<?php } ?>
											<?php } ?>
										</optgroup>

										<optgroup label="<?php echo $this->language->get('ms_event_seller'); ?>">
											<?php foreach ($msEvent->getConstants() as $cname => $cval) { ?>
												<?php if (strpos($cname, 'SELLER_') !== FALSE) { ?>
													<option value="<?php echo $cval; ?>"><?php echo $this->language->get('ms_event_type_' . $cval); ?></option>
												<?php } ?>
											<?php } ?>
										</optgroup>

										<optgroup label="<?php echo $this->language->get('ms_event_customer'); ?>">
											<?php foreach ($msEvent->getConstants() as $cname => $cval) { ?>
												<!-- Temporary block customer edit event -->
												<?php if (strpos($cname, 'CUSTOMER_') !== FALSE && $cname !== 'CUSTOMER_MODIFIED') { ?>
													<option value="<?php echo $cval; ?>"><?php echo $this->language->get('ms_event_type_' . $cval); ?></option>
												<?php } ?>
											<?php } ?>
										</optgroup>

										<optgroup label="<?php echo $this->language->get('ms_event_order'); ?>">
											<?php foreach ($msEvent->getConstants() as $cname => $cval) { ?>
												<?php if (strpos($cname, 'ORDER_') !== FALSE) { ?>
													<option value="<?php echo $cval; ?>"><?php echo $this->language->get('ms_event_type_' . $cval); ?></option>
												<?php } ?>
											<?php } ?>
										</optgroup>
									</select>
								</td>
								<td></td>
								<td><input type="text" class="input-date-datepicker"/></td>
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
	$(document).ready(function() {
		$('#list-events').dataTable( {
			"sAjaxSource": "index.php?route=multimerch/event/getTableData&token=<?php echo $token; ?>",
			"aoColumns": [
				{ "mData": "event_type" },
				{ "mData": "description", "bSortable": false },
				{ "mData": "date_created" }
			],
			"initComplete": function(settings, json) {
				var api = this.api();
				var eventTypeColumn = api.column('#event_type_column');

				$('#event_type_select').change( function() {
					eventTypeColumn.search( $(this).val() ).draw();
				});
			},
			"aaSorting":  [[2,'desc']]
		});
	});
</script>
<?php echo $footer; ?>