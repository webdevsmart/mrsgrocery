<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ms-custom-field-group-form">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button id="ms-submit-cf-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $this->url->link('multimerch/custom-field', 'token=' . $this->session->data['token'] . '#tab-cf'); ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>

			<h1><?php echo $heading; ?></h1>

			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="container-fluid">
		<div style="display: none" class="alert alert-danger" id="error-holder"><i class="fa fa-exclamation-circle"></i>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading; ?></h3>

				<div class="lang-chooser pull-right">
					<?php foreach ($languages as $language) { ?>
						<img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" width="19" class="select-input-lang <?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_admin_language')) ? 'active' : ''; ?>" data-lang="<?php echo $language['code']; ?>">
					<?php } ?>
				</div>
			</div>

			<div class="panel-body">
				<form id="ms-cf-form" class="form-horizontal">
					<input type="hidden" name="custom_field_id" value="<?php echo $custom_field['custom_field_id']; ?>" />

					<fieldset id="mm_general">
						<legend><?php echo $ms_custom_field_general; ?></legend>

						<div class="form-group required">
							<label class="col-sm-2 control-label"><?php echo $ms_custom_field_name; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "language/{$language['code']}/{$language['code']}.png"; ?>
									<div>
										<div class="lang-img-icon-input"><img src="<?php echo $img?>"></div>
										<input type="text" name="cf_description[<?php echo $language['language_id']; ?>][name]" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" value="<?php echo $custom_field['languages'][$language['language_id']]['name']; ?>" />
									</div>
								<?php } ?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_custom_field_group; ?></label>
							<div class="col-sm-10">
								<select name="custom_field_group_id" class="form-control">
									<?php foreach ($cf_groups as $key => $cf_group) { ?>
										<option value="<?php echo $cf_group['custom_field_group_id']; ?>" <?php echo (int)$custom_field['custom_field_group_id'] == (int)$cf_group['custom_field_group_id'] ? 'selected="selected"' : ''; ?>><?php echo $cf_group['languages'][$this->config->get('config_language_id')]['name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_custom_field_type; ?></label>
							<div class="col-sm-10">
								<select class="form-control" name="type">
									<?php foreach($types as $opt_group => $types) { ?>
										<optgroup label="<?php echo $this->language->get('ms_type_' . $opt_group); ?>">
											<?php foreach($types as $type) { ?>
												<option value="<?php echo $type; ?>" <?php echo isset($custom_field['type']) && $custom_field['type'] == $type ? 'selected="selected"' : ''; ?>><?php echo $this->language->get('ms_type_' . $type); ?></option>
											<?php } ?>
										</optgroup>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_custom_field_note; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "language/{$language['code']}/{$language['code']}.png"; ?>
									<div class="lang-textarea lang-select-field <?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_admin_language')) ? 'active' : ''; ?>" data-lang="<?php echo $language['code'] ?>">
										<div class="lang-img-icon-input"><img src="<?php echo $img?>"></div>
										<textarea name="cf_description[<?php echo $language['language_id']; ?>][note]" class="form-control summernote" rows="5"><?php echo isset($custom_field['languages'][$language['language_id']]['note']) ? $custom_field['languages'][$language['language_id']]['note'] : ''; ?></textarea>
									</div>
								<?php } ?>
							</div>
						</div>

						<div class="form-group" id="cf-required">
							<label class="col-sm-2 control-label"><?php echo $ms_custom_field_required; ?></label>
							<div class="col-sm-10">
								<input type="checkbox" name="required" value="1" <?php if ($custom_field['required']) { ?>checked="checked"<?php } ?> />
							</div>
						</div>

						<div class="form-group" id="cf-validation">
							<label class="col-sm-2 control-label">
								<span data-toggle="tooltip" title="<?php echo $this->language->get('ms_custom_field_validation_tooltip'); ?>"><?php echo $ms_custom_field_validation; ?></span>
							</label>
							<div class="col-sm-10">
								<input type="text" name="validation" value="<?php echo $custom_field['validation']; ?>" placeholder="<?php echo $ms_custom_field_validation; ?>" class="form-control" />
							</div>
						</div>

						<div class="form-group required">
							<label class="col-sm-2 control-label"><?php echo $ms_custom_field_sort_order; ?></label>
							<div class="col-sm-10">
								<input type="text" name="sort_order" value="<?php echo $custom_field['sort_order']; ?>" placeholder="<?php echo $ms_custom_field_sort_order; ?>" class="form-control" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_custom_field_status; ?></label>
							<div class="col-sm-10">
								<select name="status" class="form-control">
									<?php foreach ($statuses as $cname => $cval) { ?>
										<option value="<?php echo $cval; ?>" <?php echo (int)$custom_field['status'] == (int)$cval ? 'selected="selected"' : ''; ?>><?php echo $this->language->get('ms_custom_field_status_' . $cval); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<table id="cf-values" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<td class="text-left mm_req"><?php echo $ms_custom_field_value; ?></td>
									<td class="text-right"><?php echo $ms_sort_order; ?></td>
									<td></td>
								</tr>
							</thead>
							<tbody>
								<tr class="ffSample cf_value">
									<td class="text-left">
										<?php foreach ($languages as $language) { ?>
											<div>
												<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="cf_value[0][description][<?php echo $language['language_id']; ?>][name]" value="" />
											</div>
										<?php } ?>
									</td>
									<td class="text-left">
										<div>
											<input type="text" name="cf_value[0][sort_order]" value="" class="form-control" />
										</div>
									</td>
									<td class="text-center"><a class="icon-remove ms_remove_cf_value" title="<?php echo $ms_delete; ?>"><i class="fa fa-times"></i></a></td>
								</tr>

								<?php if(isset($custom_field['cf_values']) && $custom_field['cf_values']) { ?>
									<?php $i = 1; ?>
									<?php foreach ($custom_field['cf_values'] as $cf_value) { ?>
										<tr class="cf_value">
											<input type="hidden" name="cf_value[<?php echo $i; ?>][custom_field_value_id]" value="<?php echo $cf_value['custom_field_value_id']; ?>" />
											<td class="text-left">
												<?php foreach ($languages as $language) { ?>
													<div>
														<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="cf_value[<?php echo $i; ?>][description][<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($cf_value['description'][$language['language_id']]) ? $cf_value['description'][$language['language_id']]['name'] : ''; ?>" />
													</div>
												<?php } ?>
											</td>
											<td class="text-left">
												<div>
													<input type="text" name="cf_value[<?php echo $i; ?>][sort_order]" value="<?php echo $cf_value['sort_order']; ?>" class="form-control" />
												</div>
											</td>
											<td class="text-center"><a class="icon-remove ms_remove_cf_value" title="<?php echo $ms_delete; ?>"><i class="fa fa-times"></i></a></td>
										</tr>
										<?php $i++; ?>
									<?php } ?>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="2"></td>
									<td class="text-center"><a class="icon-add ms_add_cf_value" title="<?php echo $ms_add; ?>"><i class="fa fa-plus" aria-hidden="true"></i></a></td>
								</tr>
							</tfoot>
						</table>
					</fieldset>
				</form>
			</div>
		</div>
	</div>

	<script>
		var msGlobals = {
			token : "<?php echo $this->session->data['token']; ?>",
			current_language: "<?php echo $this->config->get('config_admin_language') ;?>"
		};
	</script>
</div>
<?php echo $footer; ?>