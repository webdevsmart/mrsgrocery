<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<h1><?php echo $ms_catalog_products_heading; ?></h1>
			<div class="pull-right">
				<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
				<button style="display: none;" type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger ms-delete" id="delete-seller-product" data-referrer="product"><i class="fa fa-trash-o"></i></button>
			</div>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="container-fluid">
		<?php if ($error_warning) { ?>
			<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
				<button type="button" class="close" data-dismiss="alert">&times;</button>
			</div>
		<?php } ?>

		<?php if (isset($success) && $success) { ?>
			<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
				<button type="button" class="close" data-dismiss="alert">&times;</button>
			</div>
		<?php } ?>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $ms_catalog_products_heading; ?></h3>
			</div>
			<div class="panel-body">
				<div class=" page-header row">
					<form id="bulk" method="post" enctype="multipart/form-data" class="form-inline" style="display:inline">
						<select name="bulk_product_status" class="form-control">
							<option><?php echo $ms_catalog_products_bulk; ?></option>
							<?php $msProduct = new ReflectionClass('MsProduct'); ?>
							<?php foreach ($msProduct->getConstants() as $cname => $cval) { ?>
								<?php if (strpos($cname, 'STATUS_') !== FALSE AND $cval != 6) { ?>
									<option value="<?php echo $cval; ?>"><?php echo $this->language->get('ms_product_status_' . $cval); ?></option>
								<?php } ?>
							<?php } ?>
						</select>
						<button type="button" data-toggle="tooltip" title="" class="btn btn-primary" id="ms-bulk-apply" data-original-title="<?php echo $ms_apply; ?>"><i class="fa  fa-check"></i></button>
					</form>
					<form id="bulk_sel" method="post" enctype="multipart/form-data" class="form-inline" style="display:inline">
						<select name="seller_id" id="seller_id" class="form-control">
							<option value="0"><?php echo $ms_catalog_products_bulk_seller; ?></option>
							<?php if ($sellers) { ?>
								<?php foreach ($sellers as $cval) { ?>
									<option value="<?php echo $cval['seller_id']; ?>"><?php echo $cval['ms.nickname']; ?></option>
								<?php } ?>
							<?php } ?>
						</select>
						<button type="button" data-toggle="tooltip" title="" class="btn btn-primary" id="ms-bulk-sel-apply" data-original-title="<?php echo $ms_apply; ?>"><i class="fa  fa-check"></i></button>
					</form>
				</div>

				<div class="table-responsive">
					<form class="form-inline" action="" method="post" enctype="multipart/form-data" id="form">
						<table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-products">
							<thead>
								<tr>
									<td width="1" style="text-align: center;"><input type="checkbox" onclick="$(document).find('#list-products input[name*=\'selected\']').prop('checked', $(this).prop('checked'));" /></td>
									<td class="tiny"></td>
									<td class="large"><?php echo $ms_product; ?></td>
									<td class="large"><?php echo $ms_seller; ?></td>
									<td class="small"><?php echo $ms_price ;?></td>
									<td class="small"><?php echo $ms_quantity ;?></td>
									<td class="medium" id="status_column"><?php echo $ms_status; ?></td>
									<td class="medium"><?php echo $ms_date_modified; ?></td>
									<td class="medium"><?php echo $ms_date_created; ?></td>
									<td class="large"><?php echo $ms_action; ?></td>
								</tr>
								<tr class="filter">
									<td></td>
									<td></td>
									<td><input type="text"/></td>
									<td><input type="text"/></td>
									<td></td>
									<td></td>
									<td>
										<select id="status_select">
											<option></option>
											<?php foreach ($msProduct->getConstants() as $cname => $cval) { ?>
												<?php if (strpos($cname, 'STATUS_') !== FALSE) { ?>
													<option value="<?php echo $cval; ?>"><?php echo $this->language->get('ms_product_status_' . $cval); ?></option>
												<?php } ?>
											<?php } ?>
										</select>
									</td>
									<td><input type="text" class="input-date-datepicker"/></td>
									<td><input type="text" class="input-date-datepicker"/></td>
									<td></td>
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

<script type="text/javascript">
var msGlobals = {
	token: '<?php echo $token; ?>'
};

$(document).ready(function() {
	 $('#list-products').dataTable( {
		"sAjaxSource": "index.php?route=multimerch/product/getTableData&token=<?php echo $token; ?>",
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "image", "bSortable": false },
			{ "mData": "name" },
			{ "mData": "seller" },
			{ "mData": "price"},
			{ "mData": "quantity"},
			{ "mData": "status" },
			{ "mData": "date_modified" },
			{ "mData": "date_added", "visible": false },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right" }
		],
		"initComplete": function(settings, json) {
			var api = this.api();
			var statusColumn = api.column('#status_column');

			$('#status_select').change( function() {
				statusColumn.search( $(this).val() ).draw();
		   });
		}
	});
	
	$("#ms-bulk-sel-apply").click(function() {
		if ($('#form tbody input:checkbox:checked').length == 0)
			return;
			var seller_id = $("#seller_id").val();
			var data  = $('#form,#product_message').serialize();
			$('#ms-bulk-sel-apply').find('i').switchClass( "fa-check", "fa-spinner fa-spin", 0, "linear" );
			$.ajax({
				type: "POST",
				//async: false,
				dataType: "json",
				url: 'index.php?route=multimerch/product/jxProductSeller&seller_id='+ seller_id +'&token=<?php echo $token; ?>',
				data: data,
				complete: function(jsonData) {
					window.location.reload();
				}
			});
	}); 

	$("#ms-bulk-apply").click(function() {
		if ($('#form tbody input:checkbox:checked').length == 0)
			return;
		
		if ($("#bulk_mail").is(":checked")) {
			$('<div />').html('<p>Optional note to the sellers:</p><textarea style="width:100%; height:70%" id="product_message" name="product_message"></textarea>').dialog({
				resizable: false,
				dialogClass: "msBlack",
				width: 600,
				height: 300,
				title: 'Change product status',
				modal: true,
				buttons: [
					{
					id: "button-submit",
					text: "Submit",
						click: function() {
							var data  = $('#form,#product_message,#bulk').serialize();
							var dialog = $(this);
							$('#button-submit').find('i').switchClass( "fa-check", "fa-spinner fa-spin", 0, "linear" );
							$('#button-submit,#button-cancel').remove();
							$.ajax({
								type: "POST",
								//async: false,
								dataType: "json",
								url: 'index.php?route=multimerch/product/jxProductStatus&token=<?php echo $token; ?>',
								data: data,
								success: function(jsonData) {
									window.location.reload();
								}
							});
						}
					},
					{
					id: "button-cancel",
					text: "Cancel",
						click: function() {
							$(this).dialog("close");
						}
					}
				]
			});
		} else {
			var data  = $('#form,#product_message,#bulk').serialize();
			$('#ms-bulk-apply').find('i').switchClass( "fa-check", "fa-spinner fa-spin", 0, "linear" );
			$.ajax({
				type: "POST",
				//async: false,
				dataType: "json",
				url: 'index.php?route=multimerch/product/jxProductStatus&token=<?php echo $token; ?>',
				data: data,
				complete: function(jsonData) {
					window.location.reload();
				}
			});
		}
	});

	$(document).on('click', '#list-products input:checkbox', function() {
		var selected_products = $('#list-products').children('tbody').find('input:checkbox:checked');
		if(selected_products.length > 0) {
			$('.page-header button.btn-danger').show();
		} else {
			$('.page-header button.btn-danger').hide();
		}
	});
});
</script>
<?php echo $footer; ?>