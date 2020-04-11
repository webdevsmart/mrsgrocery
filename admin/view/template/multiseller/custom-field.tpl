<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a id="ms-cfg-create" data-toggle="tooltip" title="<?php echo $ms_custom_field_group_create; ?>" class="btn btn-primary" data-referrer="custom_field_group"><i class="fa fa-plus"></i></a>
				<a style="display: none;" id="ms-cfg-delete" data-toggle="tooltip" title="<?php echo $ms_delete; ?>" class="btn btn-danger ms-delete" data-referrer="custom_field_group"><i class="fa fa-trash-o"></i></a>
			</div>

			<h1><?php echo $ms_custom_field_manage; ?></h1>

			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
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
				<ul class="nav nav-tabs ms-custom-fields-topbar">
					<li class="active"><a href="#tab-cfg" data-toggle="tab"><?php echo $ms_custom_field_group; ?></a></li>
					<li><a href="#tab-cf" data-toggle="tab"><?php echo $ms_custom_field; ?></a></li>
				</ul>
			</div>
			<div class="panel-body tab-content">
				<div class="tab-pane active" id="tab-cfg">
					<div class="table-responsive">
						<form class="form-inline" action="" method="post" enctype="multipart/form-data" id="form-custom-field-groups">
							<table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-custom-field-groups">
								<thead>
									<tr>
										<td width="1" style="text-align: center;"><input type="checkbox" onclick="$(document).find('#list-custom-field-groups input[name*=\'selected\']').prop('checked', $(this).prop('checked'));" /></td>
										<td class="large"><?php echo $ms_custom_field_group; ?></td>
										<td class="large"><?php echo $ms_custom_field_cf_count; ?></td>
										<td class="large" id="cfg_status_column"><?php echo $ms_status; ?></td>
										<td class="large"><?php echo $ms_sort_order; ?></td>
										<td class="small"><?php echo $ms_action; ?></td>
									</tr>
									<tr class="filter">
										<td></td>
										<td><input type="text"/></td>
										<td></td>
										<td>
											<select id="cfg_status_select">
												<option></option>
												<?php $msCustomField = new ReflectionClass('MsCustomField'); ?>
												<?php foreach ($msCustomField->getConstants() as $cname => $cval) { ?>
													<?php if (strpos($cname, 'STATUS_') !== FALSE) { ?>
														<option value="<?php echo $cval; ?>"><?php echo $this->language->get('ms_custom_field_status_' . $cval); ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</td>
										<td></td>
										<td></td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</form>
					</div>
				</div>

				<div class="tab-pane" id="tab-cf">
					<div class="table-responsive">
						<form class="form-inline" action="" method="post" enctype="multipart/form-data" id="form-custom-fields">
							<table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-custom-fields">
								<thead>
									<tr>
										<td width="1" style="text-align: center;"><input type="checkbox" onclick="$(document).find('#list-custom-fields input[name*=\'selected\']').prop('checked', $(this).prop('checked'));" /></td>
										<td class="large"><?php echo $ms_custom_field; ?></td>
										<td class="large"><?php echo $ms_custom_field_group; ?></td>
										<td class="large"><?php echo $ms_custom_field_type; ?></td>
										<td class="small" id="cf_status_column"><?php echo $ms_status; ?></td>
										<td class="small"><?php echo $ms_sort_order; ?></td>
										<td class="small"><?php echo $ms_action; ?></td>
									</tr>
									<tr class="filter">
										<td></td>
										<td><input type="text"/></td>
										<td><input type="text"/></td>
										<td></td>
										<td>
											<select id="cf_status_select">
												<option></option>
												<?php $msCustomField = new ReflectionClass('MsCustomField'); ?>
												<?php foreach ($msCustomField->getConstants() as $cname => $cval) { ?>
													<?php if (strpos($cname, 'STATUS_') !== FALSE) { ?>
														<option value="<?php echo $cval; ?>"><?php echo $this->language->get('ms_custom_field_status_' . $cval); ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</td>
										<td></td>
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
</div>
<script type="text/javascript">
	var msGlobals = {
		token: '<?php echo $token; ?>',
		ms_custom_field_group_create: '<?php echo $ms_custom_field_group_create; ?>',
		ms_custom_field_create: '<?php echo $ms_custom_field_create; ?>',
		ms_custom_field_group_confirm_delete: '<?php echo $ms_custom_field_group_confirm_delete; ?>',
		ms_custom_field_group_error_not_selected: '<?php echo $ms_custom_field_group_error_not_selected; ?>',
		ms_custom_field_confirm_delete: '<?php echo $ms_custom_field_confirm_delete; ?>',
		ms_custom_field_error_not_selected: '<?php echo $ms_custom_field_group_error_not_selected; ?>',
	};
</script>
<?php echo $footer; ?>