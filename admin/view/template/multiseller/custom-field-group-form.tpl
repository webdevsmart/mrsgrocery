<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ms-custom-field-group-form">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button id="ms-submit-cfg-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $this->url->link('multimerch/custom-field', 'token=' . $this->session->data['token'] . '#tab-cfg'); ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
				<form id="ms-cfg-form" class="form-horizontal">
					<input type="hidden" name="custom_field_group_id" value="<?php echo $custom_field_group['custom_field_group_id']; ?>" />

					<fieldset id="mm_general">
						<legend><?php echo $ms_custom_field_general; ?></legend>

						<div class="form-group required">
							<label class="col-sm-2 control-label"><?php echo $ms_custom_field_name; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "language/{$language['code']}/{$language['code']}.png"; ?>
									<div>
										<div class="lang-img-icon-input"><img src="<?php echo $img?>"></div>
										<input type="text" name="cfg_description[<?php echo $language['language_id']; ?>][name]" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" value="<?php echo $custom_field_group['languages'][$language['language_id']]['name']; ?>" />
									</div>
								<?php } ?>
							</div>
						</div>

						<div class="form-group required">
							<label class="col-sm-2 control-label"><?php echo $ms_custom_field_location; ?></label>
							<div class="col-sm-10">
								<div class="well well-sm" style="height: 150px; overflow: auto;">
									<?php foreach ($locations as $cname => $cval) { ?>
										<input type="checkbox" name="cfg_locations[]" value="<?php echo $cval; ?>" <?php if (isset($custom_field_group['locations']) && in_array($cval, $custom_field_group['locations'])) { ?>checked="checked"<?php } ?> /> <?php echo $this->language->get('ms_custom_field_location_' . $cval); ?><br>
									<?php } ?>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_custom_field_note; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "language/{$language['code']}/{$language['code']}.png"; ?>
									<div class="lang-textarea lang-select-field <?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_admin_language')) ? 'active' : ''; ?>" data-lang="<?php echo $language['code'] ?>">
										<div class="lang-img-icon-input"><img src="<?php echo $img?>"></div>
										<textarea name="cfg_description[<?php echo $language['language_id']; ?>][note]" class="form-control summernote" rows="5"><?php echo isset($custom_field_group['languages'][$language['language_id']]['note']) ? $custom_field_group['languages'][$language['language_id']]['note'] : ''; ?></textarea>
									</div>
								<?php } ?>
							</div>
						</div>

						<div class="form-group required">
							<label class="col-sm-2 control-label" for="input-sort-order"><?php echo $ms_custom_field_sort_order; ?></label>
							<div class="col-sm-10">
								<input type="text" name="sort_order" value="<?php echo $custom_field_group['sort_order']; ?>" placeholder="<?php echo $ms_custom_field_sort_order; ?>" id="input-sort-order" class="form-control" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="cfg-status"><?php echo $ms_custom_field_status; ?></label>
							<div class="col-sm-10">
								<select name="status" id="cfg-status" class="form-control">
									<?php foreach ($statuses as $cname => $cval) { ?>
										<option value="<?php echo $cval; ?>" <?php echo (int)$custom_field_group['status'] == (int)$cval ? 'selected="selected"' : ''; ?>><?php echo $this->language->get('ms_custom_field_status_' . $cval); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
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