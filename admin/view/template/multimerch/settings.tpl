<?php echo $header; ?><?php echo $column_left; ?>
<!-- MultiMerch settings page -->
<div id="content" class="ms-settings">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button id="saveSettings" type="submit" form="form-store" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $ms_settings_heading; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
    <?php if (isset($error_htaccess)) { ?>
        <div class="alert alert-warning ms_alert">
            <?php echo $error_htaccess; ?>
        </div>
    <?php } ?>
    <?php if (isset($error_vendor_shipping_methods)) { ?>
    <div class="alert alert-warning ms_alert">
        <?php echo $error_vendor_shipping_methods; ?>
    </div>
    <?php } ?>
  <?php if (isset($error_warning)) { ?>
	  <div class="alert alert-danger ms_alert"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
		<button type="button" class="close" data-dismiss="alert">&times;</button>
	  </div>
  <?php } ?>
  <?php if (isset($success)) { ?>
	  <div class="alert alert-success ms_alert"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
		<button type="button" class="close" data-dismiss="alert">&times;</button>
	  </div>
  <?php } ?>

    <div id="error" style="display: none" class="alert alert-danger ms_alert"></div>

  <div class="mm_container">
	  <div class="sidebar">
		  <ul>
			  <!-- mm admin multiseller settings sidebar start -->
			  <li class="active"><a href="#tab-seller" data-toggle="tab"><?php echo $ms_config_tab_sellers; ?></a></li>
			  <li><a href="#tab-productform" data-toggle="tab"><?php echo $ms_config_tab_products; ?></a></li>
			  <li><a href="#tab-shipping" data-toggle="tab"><?php echo $ms_config_shipping; ?></a></li>
			  <li><a href="#tab-order" data-toggle="tab"><?php echo $ms_config_orders; ?></a></li>
			  <li><a href="#tab-miscellaneous" data-toggle="tab"><?php echo $ms_config_miscellaneous; ?></a></li>
			  <li><a href="#tab-seo" data-toggle="tab"><?php echo $ms_seo; ?></a></li>
			  <li><a href="#tab-updates" data-toggle="tab"><?php echo $ms_config_updates; ?></a></li>
			  <li style="<?php echo isset($this->request->get['show_deprecated']) ? '' : 'display: none;'; ?>"><a href="#tab-deprecated" data-toggle="tab"><?php echo $ms_config_deprecated; ?></a></li>
			  <!-- mm admin multiseller settings sidebar end -->
		  </ul>
	  </div>

	  <div class="content">


        <form id="settings" method="post" enctype="multipart/form-data">
			<div class="tab-content">
				<!-- BEGIN SELLER TAB -->
				<div id="tab-seller" class="tab-pane active">
					<h4><?php echo $ms_config_general; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_seller_validation; ?></label>
						<div class="col-sm-9">
							<div class="selectcontainer">
								<span class="arrow"></span>
								<select class="form-control" name="msconf_seller_validation">
								  <option value="1" <?php if($msconf_seller_validation == 1) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_none; ?></option>
								  <!--<option value="2" <?php if($msconf_seller_validation == 2) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_activation; ?></option>-->
								  <option value="3" <?php if($msconf_seller_validation == 3) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_approval; ?></option>
								</select>
							</div>

							<div class="comment"><?php echo $ms_config_seller_validation_note; ?></div>
						</div>
					</div>

					<h4><?php echo $ms_config_badge_title; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $text_enabled; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" class="settings_overlay_switch" data-settings-overlay="#badge_overlay_block" name="msconf_badge_enabled" value="1" <?php if($msconf_badge_enabled == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" class="settings_overlay_switch" data-settings-overlay="#badge_overlay_block" name="msconf_badge_enabled" value="0" <?php if($msconf_badge_enabled == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_badge_enable_note; ?></div>
						</div>
					</div>

					<div id="badge_overlay_block" class="<?php echo (isset($msconf_badge_enabled) && $msconf_badge_enabled == 1) ? '' : 'overlay_block'; ?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php echo $ms_config_badge_size; ?></label>
                            <div class="col-sm-9 control-inline">
                                <input class="form-control-mini" type="text" name="msconf_badge_width" value="<?php echo $msconf_badge_width; ?>" size="3" />
                                x
                                <input class="form-control-mini" type="text" name="msconf_badge_height" value="<?php echo $msconf_badge_height; ?>" size="3" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php echo $ms_config_badge_manage; ?></label>
                            <div class="col-sm-9 control-inline">
                                <a target="_blank" href="<?php echo $this->url->link('multimerch/badge', 'token=' . $this->session->data['token'], 'SSL'); ?>"><button type="button" class="btn btn-primary pull-left"><i class="fa fa-gears"></i> <?php echo $ms_config_badge_manage; ?></button></a>
                            </div>
                        </div>
                    </div>

					<h4><?php echo $ms_config_sl_title; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $text_enabled; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" class="settings_overlay_switch" data-settings-overlay="#sl_overlay_block" name="msconf_sl_status" value="1" <?php if($msconf_sl_status == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" class="settings_overlay_switch" data-settings-overlay="#sl_overlay_block" name="msconf_sl_status" value="0" <?php if($msconf_sl_status == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_sl_enable_note; ?></div>
						</div>
					</div>

					<div id="sl_overlay_block" class="<?php echo (isset($msconf_sl_status) && $msconf_sl_status == 1) ? '' : 'overlay_block'; ?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php echo $ms_sl_icon_size; ?></label>
                            <div class="col-sm-9 control-inline">
                                <input class="form-control-mini" type="text" name="msconf_sl_icon_width" value="<?php echo $msconf_sl_icon_width; ?>" size="3" />
                                x
                                <input class="form-control-mini" type="text" name="msconf_sl_icon_height" value="<?php echo $msconf_sl_icon_height; ?>" size="3" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php echo $ms_sl_manage; ?></label>
                            <div class="col-sm-9 control-inline">
                                <a target="_blank" href="<?php echo $this->url->link('multimerch/social_link', 'token=' . $this->session->data['token'], 'SSL'); ?>"><button type="button" class="btn btn-primary pull-left"><i class="fa fa-gears"></i> <?php echo $ms_sl_manage; ?></button></a>
                            </div>
                        </div>
                    </div>

					<h4><?php echo $ms_config_miscellaneous; ?></h4>
                    <div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_nickname_rules; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_nickname_rules" value="0" <?php if ($msconf_nickname_rules == 0) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_nickname_rules_alnum; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_nickname_rules" value="1" <?php if ($msconf_nickname_rules == 1) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_nickname_rules_ext; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_nickname_rules" value="2" <?php if ($msconf_nickname_rules == 2) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_nickname_rules_utf; ?></label>
							<div class="comment"><?php echo $ms_config_nickname_rules_note; ?></div>
						</div>
					</div>
				</div>
				<!-- END SELLER TAB -->

			 	<!-- BEGIN PRODUCT FORM TAB -->
			 	<div id="tab-productform" class="tab-pane">
					<h4><?php echo $ms_config_general; ?></h4>
					<!-- @todo: Make fallback -->

                    <div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allow_free_products; ?></label>
						<div class="col-sm-9">
							<div class="selectcontainer">
								<label class="radio-inline"><input type="radio" name="msconf_allow_free_products" value="1" <?php if($msconf_allow_free_products == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
								<label class="radio-inline"><input type="radio" name="msconf_allow_free_products" value="0" <?php if($msconf_allow_free_products == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allow_digital_products; ?></label>
						<div class="col-sm-9">
							<div class="selectcontainer">
								<label class="radio-inline"><input type="radio" name="msconf_allow_digital_products" value="1" <?php if($msconf_allow_digital_products == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
								<label class="radio-inline"><input type="radio" name="msconf_allow_digital_products" value="0" <?php if($msconf_allow_digital_products == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
								<div class="comment"><?php echo $ms_config_allow_digital_products_note; ?></div>
							</div>
						</div>
					</div>

					<h4><?php echo $ms_config_product_categories; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allow_seller_categories; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_allow_seller_categories" value="1" <?php if($msconf_allow_seller_categories == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_allow_seller_categories" value="0" <?php if($msconf_allow_seller_categories == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_allow_seller_categories_note; ?></div>
						</div>
					</div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $ms_config_allow_multiple_categories; ?></label>
                        <div class="col-sm-9">
                            <label class="radio-inline"><input type="radio" name="msconf_allow_multiple_categories" value="1" <?php if($msconf_allow_multiple_categories == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
                            <label class="radio-inline"><input type="radio" name="msconf_allow_multiple_categories" value="0" <?php if($msconf_allow_multiple_categories == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
                            <div class="comment"><?php echo $ms_config_allow_multiple_categories_note; ?></div>
                        </div>
                    </div>

                    <h4><?php echo $ms_config_product_attributes_options; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allow_attributes; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_allow_seller_attributes" value="1" <?php if($msconf_allow_seller_attributes == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_allow_seller_attributes" value="0" <?php if($msconf_allow_seller_attributes == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_allow_attributes_note; ?></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allow_options; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" class="settings_overlay_switch" data-settings-overlay="#options_overlay_block" name="msconf_allow_seller_options" value="1" <?php if($msconf_allow_seller_options == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" class="settings_overlay_switch" data-settings-overlay="#options_overlay_block" name="msconf_allow_seller_options" value="0" <?php if($msconf_allow_seller_options == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_allow_options_note; ?></div>
						</div>
					</div>


                    <div id="options_overlay_block" class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allowed_option_types; ?></label>
						<div class="col-sm-9">
							<div class="well well-sm" style="height: 150px; overflow: auto;">
								<?php foreach($allowed_seller_option_types as $opt_group => $types) { ?>
									<?php foreach($types as $type) { ?>
										<input type="checkbox" name="msconf_allowed_seller_option_types[<?php echo $opt_group; ?>][]" value="<?php echo $type; ?>" <?php if (isset($msconf_allowed_seller_option_types[$opt_group]) && in_array($type, $msconf_allowed_seller_option_types[$opt_group])) { ?>checked="checked"<?php } ?> /> <?php echo $this->language->get('ms_config_option_type_' . $type); ?><br>
									<?php } ?>
								<?php } ?>
							</div>
							<div class="comment"><?php echo $ms_config_allowed_option_types_note; ?></div>
						</div>
					</div>

					<h4><?php echo $ms_config_product_fields; ?></h4>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $ms_config_product_included_fields; ?></label>
                        <div class="col-sm-9">
                          <div class="well well-sm" style="height: 300px; overflow: auto;">
                            <?php foreach ($product_included_fields as $field_code => $field_name) { ?>
                              <input type="checkbox" name="msconf_product_included_fields[]" value="<?php echo $field_code; ?>" <?php if (isset($msconf_product_included_fields) && in_array($field_code, $msconf_product_included_fields)) { ?>checked="checked"<?php } ?> /> <?php echo $field_name; ?><br>
                            <?php } ?>
                          </div>
							<div class="comment"><?php echo $ms_config_product_included_fields_note; ?></div>
                        </div>
                    </div>

					<!-- @todo: Change to 'min > 1' if `image` product field is enabled, otherwise - 0 to 25 -->
					<h4><?php echo $ms_config_limits; ?></h4>
					<div class="form-group">
						  <label class="col-sm-3 control-label"><?php echo $ms_config_images_limits; ?></label>
						  <div class="col-sm-9 control-inline">
						    <span><?php echo $ms_config_min; ?></span> <input class="form-control-mini" type="text" name="msconf_images_limits[]" value="<?php echo $msconf_images_limits[0]; ?>" size="3" />
						    <span><?php echo $ms_config_max; ?></span> <input class="form-control-mini" type="text" name="msconf_images_limits[]" value="<?php echo $msconf_images_limits[1]; ?>" size="3" />
							  <div class="comment"><?php echo $ms_config_images_limits_note; ?></div>
						  </div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_downloads_limits; ?></label>
						<div class="col-sm-9 control-inline">
						    <span><?php echo $ms_config_min; ?></span> <input class="form-control-mini" type="text" name="msconf_downloads_limits[]" value="<?php echo $msconf_downloads_limits[0]; ?>" size="3" />
                            <span><?php echo $ms_config_max; ?></span> <input class="form-control-mini" type="text" name="msconf_downloads_limits[]" value="<?php echo $msconf_downloads_limits[1]; ?>" size="3" />
							<div class="comment"><?php echo $ms_config_downloads_limits_note; ?></div>
						</div>
					</div>

                    <h4><?php echo $ms_config_import; ?></h4>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $ms_config_import_enable; ?></label>
                        <div class="col-sm-9">
                            <label class="radio-inline"><input type="radio" class="settings_overlay_switch" data-settings-overlay=".import_settings_block" name="msconf_import_enable" value="1" <?php if($msconf_import_enable == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
                            <label class="radio-inline"><input type="radio" class="settings_overlay_switch" data-settings-overlay=".import_settings_block" name="msconf_import_enable" value="0" <?php if($msconf_import_enable == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
                            <div class="comment"><?php echo $ms_config_import_enable_note; ?></div>
                        </div>
                    </div>
                    <div class="form-group import_settings_block">
                        <label class="col-sm-3 control-label"><?php echo $ms_config_import_category_type; ?></label>
                        <div class="col-sm-9">
                            <label class="radio-inline"><input type="radio" name="msconf_import_category_type" value="0" <?php if($msconf_import_category_type == 0) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_import_category_type_all_categories; ?></label>
                            <label class="radio-inline"><input type="radio" name="msconf_import_category_type" value="1" <?php if($msconf_import_category_type == 1) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_import_category_type_categories_levels; ?></label>
                            <div class="comment"><?php echo $ms_config_import_category_type_note; ?></div>
                        </div>
                    </div>
				</div>
				<!-- END PRODUCT FORM TAB -->

				<!-- BEGIN MISCELLANEOUS TAB -->
			 	<div id="tab-miscellaneous" class="tab-pane">
					<h4><?php echo $ms_config_finances; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_fee_priority; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_fee_priority" value="1" <?php if($msconf_fee_priority == 1) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_fee_priority_catalog; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_fee_priority" value="2" <?php if($msconf_fee_priority == 2) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_fee_priority_vendor; ?></label>
							<div class="comment"><?php echo $ms_config_fee_priority_note; ?></div>
						</div>
					</div>

                    <h4><?php echo $ms_config_reviews; ?></h4>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $ms_config_reviews_enable; ?></label>
                        <div class="col-sm-9">
                            <label class="radio-inline"><input type="radio" name="msconf_reviews_enable" value="1" <?php if($msconf_reviews_enable == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
                            <label class="radio-inline"><input type="radio" name="msconf_reviews_enable" value="0" <?php if($msconf_reviews_enable == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
                            <div class="comment"><?php echo $ms_config_reviews_enable_note; ?></div>
                        </div>
                    </div>

                    <h4><?php echo $ms_config_product_questions; ?></h4>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $ms_config_allow_question ;?></label>
                        <div class="col-sm-9">
                            <div class="selectcontainer">
                                <label class="radio-inline"><input type="radio" name="msconf_allow_questions" value="1" <?php if($msconf_allow_questions == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
                                <label class="radio-inline"><input type="radio" name="msconf_allow_questions" value="0" <?php if($msconf_allow_questions == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
                                <div class="comment"><?php echo $ms_config_allow_question_note; ?></div>
                            </div>
                        </div>
                    </div>

                    <h4><?php echo $mmes_messaging; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $mmess_config_enable; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="mmess_conf_enable" value="1" <?php if($mmess_conf_enable == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="mmess_conf_enable" value="0" <?php if($mmess_conf_enable == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
						</div>
					</div>

					<h4><?php echo $ms_config_coupon; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_coupon_allow; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_allow_seller_coupons" value="1" <?php if($msconf_allow_seller_coupons == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_allow_seller_coupons" value="0" <?php if($msconf_allow_seller_coupons == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
						</div>
					</div>

                    <h4><?php echo $ms_config_sellers_map; ?></h4>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $ms_config_sellers_map_api_key; ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="msconf_google_api_key" value="<?php echo $msconf_google_api_key; ?>" />
                            <input type="hidden" name="msconf_google_api_key_old" value="<?php echo $msconf_google_api_key; ?>" />
                            <div class="comment"><?php echo $ms_config_sellers_map_api_key_note; ?></div>
                        </div>
                    </div>

                    <h4><?php echo $ms_config_logging; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_logging_level; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_logging_level" value="<?php echo \MultiMerch\Logger\Logger::LEVEL_ERROR; ?>" <?php if($msconf_logging_level == \MultiMerch\Logger\Logger::LEVEL_ERROR) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_logging_level_error; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_logging_level" value="<?php echo \MultiMerch\Logger\Logger::LEVEL_INFO; ?>" <?php if($msconf_logging_level == \MultiMerch\Logger\Logger::LEVEL_INFO) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_logging_level_info; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_logging_level" value="<?php echo \MultiMerch\Logger\Logger::LEVEL_DEBUG; ?>" <?php if($msconf_logging_level == \MultiMerch\Logger\Logger::LEVEL_DEBUG) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_logging_level_debug; ?></label>
							<div class="comment"><?php echo $ms_config_logging_level_note; ?></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_logging_filename; ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="msconf_logging_filename" value="<?php echo $msconf_logging_filename; ?>" />
							<div class="comment"><?php echo $ms_config_logging_filename_note; ?></div>
						</div>
					</div>
				</div>
				<!-- END MISCELLANEOUS TAB -->

				<!-- BEGIN SHIPPING TAB -->
				<div id="tab-shipping" class="tab-pane">
					<h4><?php echo $ms_config_general; ?></h4>

                    <div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_shipping_type; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" class="settings_overlay_switch overlay_show" data-settings-overlay=".shipping_overlay_block" name="msconf_shipping_type" value="1" <?php if($msconf_shipping_type == 1) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_enable_store_shipping; ?></label>
							<label class="radio-inline"><input type="radio" class="settings_overlay_switch overlay_hide" data-settings-overlay=".shipping_overlay_block" name="msconf_shipping_type" value="2" <?php if($msconf_shipping_type == 2) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_enable_vendor_shipping; ?></label>
							<label class="radio-inline"><input type="radio" class="settings_overlay_switch overlay_show" data-settings-overlay=".shipping_overlay_block" name="msconf_shipping_type" value="0" <?php if($msconf_shipping_type == 0) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_disable_shipping; ?></label>
							<div class="comment"><?php echo $ms_config_shipping_type_note; ?></div>
						</div>
					</div>

                    <div class="shipping_overlay_block">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php echo $ms_config_vendor_shipping_type; ?></label>
                            <div class="col-sm-9">
                                <label class="radio-inline"><input type="radio" name="msconf_vendor_shipping_type" value="1" <?php if($msconf_vendor_shipping_type == 1) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_vendor_shipping_combined; ?></label>
                                <label class="radio-inline"><input type="radio" name="msconf_vendor_shipping_type" value="2" <?php if($msconf_vendor_shipping_type == 2) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_vendor_shipping_per_product; ?></label>
                                <label class="radio-inline"><input type="radio" name="msconf_vendor_shipping_type" value="3" <?php if($msconf_vendor_shipping_type == 3) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_vendor_shipping_both; ?></label>
                                <div class="comment"><?php echo $ms_config_vendor_shipping_type_note; ?></div>
                            </div>
                        </div>

                        <div id="shipping_methods_times" class="form-group">
                            <label class="col-sm-3 control-label"><?php echo $ms_config_shipping_delivery_times; ?></label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-sm table-hover <?php echo empty($shipping_delivery_times) ? 'hidden' : '' ;?>" id="delivery-times">
                                            <thead>
                                            <tr>
                                                <?php foreach ($languages as $language) { ?>
                                                <td class="text-center col-sm-4"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" width="19" class="select-input-lang" data-lang="<?php echo $language['code']; ?>"></td>
                                                <?php } ?>
                                                <td class="col-sm-1"></td>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php foreach ($shipping_delivery_times as $delivery_time_id => $delivery_time_desc) { ?>
                                            <tr>
                                                <input type="hidden" class="delivery_time_id" value="<?php echo $delivery_time_id; ?>" />
                                                <?php foreach ($languages as $language) { ?>
                                                <td class="text-center editable-time" data-lang-id="<?php echo $language['language_id'] ;?>">
                                                    <?php echo isset($delivery_time_desc[$language['language_id']]) ? $delivery_time_desc[$language['language_id']] : '' ;?>
                                                </td>
                                                <?php } ?>

                                                <td class="text-center"><a class="icon-remove mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>
                                            </tr>
                                            <?php } ?>
                                            </tbody>

                                            <div class="comment <?php echo !empty($shipping_delivery_times) ? '' : 'hidden' ;?>"><?php echo $ms_config_shipping_delivery_time_comment; ?></div>
                                        </table>
                                    </div>
                                </div>

                                <div class="comment"><?php echo $ms_config_shipping_delivery_times_note; ?></div>

                                <div style="padding-top: 10px;">
                                    <a class="btn btn-default addDeliveryTime"><?php echo $ms_config_shipping_delivery_time_add_btn; ?></a>
                                </div>

                                <div class="row addDeliveryTimeForm hidden">
                                    <div class="col-sm-12">
                                        <?php foreach($languages as $language) { ?>
                                        <div style="display: block; margin-bottom: 5px;">
                                            <input type="text" class="form-control" name="delivery_time_<?php echo $language['language_id'] ;?>" value="" placeholder=""/>
                                            <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" width="19" class="select-input-lang" data-lang="<?php echo $language['code']; ?>">
                                        </div>
                                        <?php } ?>
                                        <input type="button" id="add-delivery-time" class="btn btn-primary pull-left" style="margin: 10px 0 5px 0;" value="<?php echo $button_save; ?>"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
				<!-- END SHIPPING TAB -->

				<!-- BEGIN SEO TAB -->
				<div id="tab-seo" class="tab-pane">
					<h4><?php echo $ms_seo_urls; ?></h4>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?php echo $ms_use_seo_urls; ?></label>
						<div class="col-sm-8">
							<label class="radio-inline"><input type="radio" name="msconf_config_seo_url_enable" value="1" <?php if($msconf_config_seo_url_enable == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_config_seo_url_enable" value="0" <?php if($msconf_config_seo_url_enable == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_use_seo_urls_note; ?></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?php echo $ms_config_sellers_slug; ?></label>
						<div class="col-sm-8">
							<?php echo $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG ?>
							<input type="text" name="msconf_sellers_slug" value="<?php echo isset($msconf_sellers_slug) ? $msconf_sellers_slug : 'sellers' ; ?>" />
							<?php echo $ms_config_sellers_slug_; ?>
							<input type="text" name="msconf_store_slug" value="<?php echo isset($msconf_store_slug) ? $msconf_store_slug : 'store' ; ?>" />/
							<div class="comment"><?php echo sprintf($ms_config_sellers_slug_note, $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG); ?></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?php echo $ms_config_products_slug; ?></label>
						<div class="col-sm-8">
							<?php echo $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG ?>
							<input type="text" name="msconf_products_slug" value="<?php echo isset($msconf_products_slug) ? $msconf_products_slug : '' ; ?>" />/
							<div class="comment"><?php echo sprintf($ms_config_products_slug_note, $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG); ?></div>
						</div>
					</div>
				</div>
				<!-- END SEO TAB -->

				<!-- BEGIN UPDATES TAB -->
				<div id="tab-updates" class="tab-pane">
					<h4><?php echo $ms_config_updates_license_info; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_updates_license_key; ?></label>
						<div class="col-sm-9">
							<input style="float:left;margin-right:10px;" class="form-control" type="text" name="msconf_license_key" value="<?php echo $msconf_license_key; ?>" />
							<button id="ms-activate-license" type="button" class="btn btn-primary pull-left"><i class="fa fa-gears"></i> <?php echo $ms_config_updates_license_activate; ?></button>
							<div class="cl"></div>
							<div class="comment"><?php echo $ms_config_updates_license_key_note; ?></div>
							<div class="cl"></div>
							<div id="license-info-holder" class="alert alert-warning" style="display: none;">
								<?php echo $ms_config_updates_updates_not_activated; ?>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_updates_updates; ?></label>
						<div class="col-sm-9 control-inline">
							<div id="updates-info-holder" class="alert alert-warning">
								<?php echo $ms_config_updates_updates_not_activated; ?>
							</div>
							<div class="ms-changelog" style="display: none;"></div>
						</div>
					</div>
				</div>
				<!-- END UPDATES TAB -->

				<!-- BEGIN ORDERS TAB -->
				<div id="tab-order" class="tab-pane">
					<h4><?php echo $ms_config_order_states; ?></h4>
					<div class="comment for-header"><?php echo $ms_config_order_states_note; ?></div>

					<?php $msOrderData = new ReflectionClass('MsOrderData'); ?>
					<?php foreach ($msOrderData->getConstants() as $cname => $cval) { ?>
						<?php if (in_array($cname, array('STATE_PENDING', 'STATE_PROCESSING', 'STATE_COMPLETED'))) { ?>
							<div class="form-group">
								<label class="col-sm-3 control-label"><?php echo $this->language->get('ms_config_order_state_' . $cval); ?></label>
								<div class="col-sm-9">
									<input type="text" id="oc_order_statuses_<?php echo $cval; ?>" />
									<div class="comment"><?php echo $this->language->get('ms_config_order_state_note_' . $cval); ?></div>

									<?php foreach ($oc_order_states as $state_id => $statuses) { ?>
										<?php if ((int)$state_id === (int)$cval) { ?>
											<?php foreach ($statuses as $status) { ?>
												<input type="hidden" name="msconf_order_state[<?php echo $state_id; ?>][]" value="<?php echo $status['id']; ?>" data-name="<?php echo $status['name']; ?>" data-type="oc" />
											<?php } ?>
										<?php } ?>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
					<?php } ?>

					<h4><?php echo $ms_config_suborder_states; ?></h4>
					<div class="comment for-header"><?php echo $ms_config_suborder_states_note; ?></div>

					<?php $msSuborder = new ReflectionClass('MsSuborder'); ?>
					<?php foreach ($msSuborder->getConstants() as $cname => $cval) { ?>
						<?php if (in_array($cname, array('STATE_PENDING', 'STATE_PROCESSING', 'STATE_COMPLETED'))) { ?>
							<div class="form-group">
								<label class="col-sm-3 control-label"><?php echo $this->language->get('ms_config_suborder_state_' . $cval); ?></label>
								<div class="col-sm-9">
									<input type="text" id="ms_suborder_statuses_<?php echo $cval; ?>" />
									<div class="comment"><?php echo $this->language->get('ms_config_suborder_state_note_' . $cval); ?></div>

									<?php foreach ($ms_suborder_states as $state_id => $statuses) { ?>
										<?php if ((int)$state_id === (int)$cval) { ?>
											<?php foreach ($statuses as $status) { ?>
												<input type="hidden" name="msconf_suborder_state[<?php echo $state_id; ?>][]" value="<?php echo $status['id']; ?>" data-name="<?php echo $status['name']; ?>" data-type="ms" />
											<?php } ?>
										<?php } ?>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
					<?php } ?>

					<h4><?php echo $ms_config_order_statuses; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_suborder_status_default; ?></label>
						<div class="col-sm-9">
							<div class="selectcontainer">
								<span class="arrow"></span>
								<select class="form-control" name="msconf_suborder_default_status">
									<?php foreach($suborder_statuses as $suborder_status) { ?>
										<option value="<?php echo $suborder_status['ms_suborder_status_id']; ?>" <?php if($suborder_status['ms_suborder_status_id'] == $this->config->get('msconf_suborder_default_status')) { ?> selected="selected" <?php } ?>><?php echo $suborder_status['name']; ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="comment"><?php echo $ms_config_suborder_status_default_note; ?></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_order_status_credit; ?></label>
						<div class="col-sm-9">
							<input type="text" id="oc_order_credit_statuses" />
							<div class="comment"><?php echo $ms_config_order_status_credit_note; ?></div>

							<?php if(isset($msconf_credit_order_statuses['oc'])) { ?>
								<?php foreach ($order_statuses as $status) { ?>
									<?php if(in_array($status['order_status_id'], $msconf_credit_order_statuses['oc'])) { ?>
										<input type="hidden" name="msconf_credit_order_statuses[oc][]" value="<?php echo $status['order_status_id']; ?>" data-name="<?php echo $status['name']; ?>" data-type="oc" />
									<?php } ?>
								<?php } ?>
							<?php } ?>

							<?php if(isset($msconf_credit_order_statuses['ms'])) { ?>
								<?php foreach ($suborder_statuses as $status) { ?>
									<?php if(in_array($status['ms_suborder_status_id'], $msconf_credit_order_statuses['ms'])) { ?>
										<input type="hidden" name="msconf_credit_order_statuses[ms][]" value="<?php echo $status['ms_suborder_status_id']; ?>" data-name="<?php echo $status['name']; ?>" data-type="ms" />
									<?php } ?>
								<?php } ?>
							<?php } ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_order_status_debit; ?></label>
						<div class="col-sm-9">
							<input type="text" id="oc_order_debit_statuses" />
							<div class="comment"><?php echo $ms_config_order_status_debit_note; ?></div>

							<?php if(isset($msconf_debit_order_statuses['oc'])) { ?>
								<?php foreach ($order_statuses as $status) { ?>
									<?php if(in_array($status['order_status_id'], $msconf_debit_order_statuses['oc'])) { ?>
										<input type="hidden" name="msconf_debit_order_statuses[oc][]" value="<?php echo $status['order_status_id']; ?>" data-name="<?php echo $status['name']; ?>" data-type="oc" />
									<?php } ?>
								<?php } ?>
							<?php } ?>

							<?php if(isset($msconf_debit_order_statuses['ms'])) { ?>
								<?php foreach ($suborder_statuses as $status) { ?>
									<?php if(in_array($status['ms_suborder_status_id'], $msconf_debit_order_statuses['ms'])) { ?>
										<input type="hidden" name="msconf_debit_order_statuses[ms][]" value="<?php echo $status['ms_suborder_status_id']; ?>" data-name="<?php echo $status['name']; ?>" data-type="ms" />
									<?php } ?>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
				</div>
				<!-- END ORDERS TAB -->

				<!-- BEGIN DEPRECATED TAB -->
				<div id="tab-deprecated" class="tab-pane">
					<!-- Sellers tab -->
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_seller_terms_page; ?></label>
						<div class="col-sm-9">
							<div class="selectcontainer">
								<span class="arrow"></span>
								<select class="form-control" name="msconf_seller_terms_page">
									<option value="0"><?php echo $text_none; ?></option>
									<?php foreach ($informations as $information) { ?>
										<?php if ($information['information_id'] == $msconf_seller_terms_page) { ?>
											<option value="<?php echo $information['information_id']; ?>" selected="selected"><?php echo $information['title']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $information['information_id']; ?>"><?php echo $information['title']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
							<div class="comment"><?php echo $ms_config_seller_terms_page_note; ?></div>
						</div>
					</div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $ms_config_change_group; ?></label>
                        <div class="col-sm-9">
                            <label class="radio-inline"><input type="radio" name="msconf_change_group" value="1" <?php if ($msconf_change_group == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
                            <label class="radio-inline"><input type="radio" name="msconf_change_group" value="0" <?php if ($msconf_change_group == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
                            <div class="comment"><?php echo $ms_config_change_group_note; ?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $mxt_disqus_comments_enable; ?></label>
                        <div class="col-sm-9">
                            <label class="radio-inline"><input type="radio" class="settings_overlay_switch" data-settings-overlay="#disqus_overlay_block" name="mxtconf_disqus_enable" value="1" <?php if($mxtconf_disqus_enable == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
                            <label class="radio-inline"><input type="radio" class="settings_overlay_switch" data-settings-overlay="#disqus_overlay_block" name="mxtconf_disqus_enable" value="0" <?php if($mxtconf_disqus_enable == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
                        </div>
                    </div>

                    <div id="disqus_overlay_block" class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $mxt_disqus_comments_shortname; ?></label>
                        <div class="col-sm-9 control-inline">
                            <input class="form-control" type="text" name="mxtconf_disqus_shortname" value="<?php echo $mxtconf_disqus_shortname; ?>" size="10" />
                        </div>
                    </div>

					<!-- @todo: own analytics system for sellers -->
					<h4><?php echo $mxt_google_analytics; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $mxt_google_analytics_enable; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="mxtconf_ga_seller_enable" value="1" <?php if($mxtconf_ga_seller_enable == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="mxtconf_ga_seller_enable" value="0" <?php if($mxtconf_ga_seller_enable == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
						</div>
					</div>

					<!-- @todo: do not allow by default -->
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_seller_change_nickname; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_change_seller_nickname" value="1" <?php if ($msconf_change_seller_nickname == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_change_seller_nickname" value="0" <?php if ($msconf_change_seller_nickname == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_seller_change_nickname_note; ?></div>
						</div>
					</div>

					<!-- Product tab -->
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_minmax_product_price; ?></label>
						<div class="col-sm-9 control-inline">
							<span><?php echo $ms_config_min; ?></span> <input class="form-control-mini" type="text" name="msconf_minimum_product_price" value="<?php echo $msconf_minimum_product_price; ?>" size="4"/>
							<span><?php echo $ms_config_max; ?></span> <input class="form-control-mini" type="text" name="msconf_maximum_product_price" value="<?php echo $msconf_maximum_product_price; ?>" size="4"/>
							<div class="comment"><?php echo $ms_config_minmax_product_price_note; ?></div>
						</div>
					</div>

					<h4><?php echo $ms_config_file_types; ?></h4>
					<!-- @todo: Change by default to 'png,jpg,jpeg' -->
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allowed_image_types; ?></label>
						<div class="col-sm-9">
							<input class="form-control" type="text" name="msconf_allowed_image_types" value="<?php echo $msconf_allowed_image_types; ?>" />
							<div class="comment"><?php echo $ms_config_allowed_image_types_note; ?></div>
						</div>
					</div>

					<!-- @todo: Change by default to OC file types -->
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allowed_download_types; ?></label>
						<div class="col-sm-9">
							<input class="form-control" type="text" name="msconf_allowed_download_types" value="<?php echo $msconf_allowed_download_types; ?>" />
							<div class="comment"><?php echo $ms_config_allowed_download_types_note; ?></div>
						</div>
					</div>

					<h4><?php echo $ms_config_miscellaneous; ?></h4>
					<!-- @todo: Check functionality -->
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_rte_whitelist; ?></label>
						<div class="col-sm-9">
							<input class="form-control" type="text" name="msconf_rte_whitelist" value="<?php echo $msconf_rte_whitelist; ?>" />
							<div class="comment"><?php echo $ms_config_rte_whitelist_note; ?></div>
						</div>
					</div>

					<div class="form-group" style="display:none">
						<label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $ms_config_enable_rte_note; ?>"><?php echo $ms_config_enable_rte; ?></span></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_enable_rte" value="1" <?php if($msconf_enable_rte == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?>
							</label>
							<label class="radio-inline"><input type="radio" name="msconf_enable_rte" value="0" <?php if($msconf_enable_rte == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?>
							</label>
						</div>
					</div>

					<!-- Miscellaneous tab -->
					<!-- @todo: OC settings by default -->
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_msg_allowed_file_types; ?></label>
						<div class="col-sm-9">
							<input class="form-control" type="text" name="msconf_msg_allowed_file_types" value="<?php echo $msconf_msg_allowed_file_types; ?>" />
							<div class="comment"><?php echo $ms_config_msg_allowed_file_types_note; ?></div>
						</div>
					</div>

					<!-- @todo: see reaction -->
					<h4><?php echo $ms_config_image_sizes; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_seller_avatar_image_size; ?></label>
						<div class="col-sm-9 control-inline">
							<div class="row">
								<span class="col-sm-3"><?php echo $ms_config_seller_avatar_image_size_seller_profile; ?></span>
								<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_seller_avatar_seller_profile_image_width" value="<?php echo $msconf_seller_avatar_seller_profile_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_seller_avatar_seller_profile_image_height" value="<?php echo $msconf_seller_avatar_seller_profile_image_height; ?>" size="3" /></span>
							</div>

							<div class="row">
								<span class="col-sm-3"><?php echo $ms_config_seller_avatar_image_size_seller_list; ?></span>
								<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_seller_avatar_seller_list_image_width" value="<?php echo $msconf_seller_avatar_seller_list_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_seller_avatar_seller_list_image_height" value="<?php echo $msconf_seller_avatar_seller_list_image_height; ?>" size="3" />							</span>
							</div>

							<div class="row">
								<span class="col-sm-3"><?php echo $ms_config_seller_avatar_image_size_product_page; ?></span>
								<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_seller_avatar_product_page_image_width" value="<?php echo $msconf_seller_avatar_product_page_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_seller_avatar_product_page_image_height" value="<?php echo $msconf_seller_avatar_product_page_image_height; ?>" size="3" /></span>
							</div>

							<div class="row">
								<span class="col-sm-3"><?php echo $ms_config_seller_avatar_image_size_seller_dashboard; ?></span>
								<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_seller_avatar_dashboard_image_width" value="<?php echo $msconf_seller_avatar_dashboard_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_seller_avatar_dashboard_image_height" value="<?php echo $msconf_seller_avatar_dashboard_image_height; ?>" size="3" /></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_seller_banner_size; ?></label>
						<div class="col-sm-9 control-inline">
							<div class="row">
								<span class="col-sm-3"></span>
								<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_product_seller_banner_width" value="<?php echo $msconf_product_seller_banner_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_product_seller_banner_height" value="<?php echo $msconf_product_seller_banner_height; ?>" size="3" /></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_image_preview_size; ?></label>
						<div class="col-sm-9 control-inline">
							<div class="row">
								<span class="col-sm-3"><?php echo $ms_config_image_preview_size_seller_avatar; ?></span>
								<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_preview_seller_avatar_image_width" value="<?php echo $msconf_preview_seller_avatar_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_preview_seller_avatar_image_height" value="<?php echo $msconf_preview_seller_avatar_image_height; ?>" size="3" /></span>
							</div>

							<div class="row">
								<span class="col-sm-3"><?php echo $ms_config_image_preview_size_product_image; ?></span>
								<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_preview_product_image_width" value="<?php echo $msconf_preview_product_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_preview_product_image_height" value="<?php echo $msconf_preview_product_image_height; ?>" size="3" /></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_product_image_size; ?></label>
						<div class="col-sm-9 control-inline">
							<div class="row">
								<span class="col-sm-3"><?php echo $ms_config_product_image_size_seller_profile; ?></span>
								<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_product_seller_profile_image_width" value="<?php echo $msconf_product_seller_profile_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_product_seller_profile_image_height" value="<?php echo $msconf_product_seller_profile_image_height; ?>" size="3" /></span>
							</div>

							<div class="row">
								<span class="col-sm-3"><?php echo $ms_config_product_image_size_seller_products_list; ?></span>
								<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_product_seller_products_image_width" value="<?php echo $msconf_product_seller_products_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_product_seller_products_image_height" value="<?php echo $msconf_product_seller_products_image_height; ?>" size="3" /></span>
							</div>

							<div class="row">
								<span class="col-sm-3"><?php echo $ms_config_product_image_size_seller_products_list_account; ?></span>
								<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_product_seller_product_list_seller_area_image_width" value="<?php echo $msconf_product_seller_product_list_seller_area_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_product_seller_product_list_seller_area_image_height" value="<?php echo $msconf_product_seller_product_list_seller_area_image_height; ?>" size="3" /></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_uploaded_image_size; ?></label>
						<div class="col-sm-9 control-inline">
							<div class="row">
								<span class="col-sm-3"><?php echo $ms_config_min; ?></span>
								<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_min_uploaded_image_width" value="<?php echo $msconf_min_uploaded_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_min_uploaded_image_height" value="<?php echo $msconf_min_uploaded_image_height; ?>" size="3" /></span>
							</div>

							<div class="row">
								<span class="col-sm-3"><?php echo $ms_config_max; ?></span>
								<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_max_uploaded_image_width" value="<?php echo $msconf_max_uploaded_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_max_uploaded_image_height" value="<?php echo $msconf_max_uploaded_image_height; ?>" size="3" /></span>
							</div>
							<div class="comment"><?php echo $ms_config_uploaded_image_size_note; ?></div>
						</div>
					</div>
				</div>
				<!-- END DEPRECATED TAB -->
			</div>
			</form>
	  </div>
  </div>
</div>

<script>
$(function() {
	$("#saveSettings").click(function() {
		$("#error").hide();
		$("#error").html('');
		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=module/multimerch/savesettings&token=<?php echo $token; ?>',
			data: $('#settings').serialize(),
			success: function(jsonData) {
				if (jsonData.errors) {
					for (error in jsonData.errors) {
						if (!jsonData.errors.hasOwnProperty(error)) {
							continue;
						}
						$("#error").append('<p>'+jsonData.errors[error]+'</p>');
					}
					$("#error").show();
				} else {
					window.location.reload();
				}
			}
		});
	});

	$(document).on('click', '#add-delivery-time', function() {
		var delivery_times = [];

		$.each($(document).find('input[name^=delivery_time]'), function(key, item) {
			var language_id = parseInt($(item).attr('name').split('_').slice(-1).pop());
			delivery_times.push({
				language_id: language_id,
				name: $(item).val()
			});
		});

		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multimerch/shipping-method/jxSaveDeliveryTime&token=<?php echo $token; ?>',
			data: {names: delivery_times},
			success: function(jsonData) {
				if(jsonData.errors) {
					console.error(jsonData.errors);
				} else {
					var html = '';
					html += '<tr>';
					html += '<input type="hidden" class="delivery_time_id" value="' + jsonData['delivery_time_id'] + '" />';

					$.each(jsonData['delivery_time_names'], function(key, item) {
						html += '<td class="text-center editable-time" data-lang-id="' + item['language_id'] + '">' + item['name'] + '</td>';
					});

					html += '<td class="text-center"><a class="icon-remove mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>';
					html += '</tr>';

					$('#delivery-times > tbody').append(html);
					$('#delivery-times').removeClass('hidden');
					$('#delivery-times').siblings('.comment').removeClass('hidden');

					$(document).find('input[name^=delivery_time]').map(function() {
						$(this).val("");
					});
				}
			}
		});
	});

	$('#delivery-times').delegate('.mm_remove', 'click', function() {
		$(this).closest('tr').remove();

		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multimerch/shipping-method/jxDeleteDeliveryTime&token=<?php echo $token; ?>',
			data: {id : $(this).closest('tr').find('.delivery_time_id').val()},
			success: function(jsonData) {
				// console.log(jsonData);
				if ($('#delivery-times tbody tr').length < 1) {
					$('#delivery-times').addClass('hidden');
					$('#delivery-times').siblings('.comment').addClass('hidden');
				}
			}
		});
	});

	// edit delivery times
	var delivery_time_original_value;

	$(document).on('dblclick', '#delivery-times .editable-time', function () {
		delivery_time_original_value = $(this).text();
		$(this).text("");
		$('<input type="text" class="form-control" value="' + $.trim(delivery_time_original_value) + '" style="width: 75%; float: left;"/>').appendTo(this).select();
		$('<button class="btn btn-primary pull-left"><i class="fa fa-check" aria-hidden="true"></i></button>').appendTo(this);
	});

	$(document).on('click', '#delivery-times .editable-time > button', function (e) {
		e.preventDefault();
		var data = {
			name: $(this).siblings('input[type="text"]').val(),
			delivery_time_id: $(this).closest('tr').find('.delivery_time_id').val(),
			language_id: $(this).closest('td').data('lang-id')
		};

		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multimerch/shipping-method/jxEditDeliveryTime&token=<?php echo $token; ?>',
			data: data,
			success: function(jsonData) {
				//console.log(jsonData);
			}
		});

		$(this).parent().text($(this).siblings('input[type="text"]').val() || delivery_time_original_value);
		$(this).remove();
	});

    $(document).on('click', '.addDeliveryTime', function() {
		$('.addDeliveryTimeForm').removeClass('hidden');
		$(this).addClass('hidden');
	});

	$(document).on('change', 'input[name="msconf_shipping_type"]', function() {
		if($(this).val() == 2) {
			$('.vendor_shipping_type').removeClass('hidden');
			$('.vendor_delivery_times').removeClass('hidden');
		} else {
			$('.vendor_shipping_type').addClass('hidden');
			$('.vendor_delivery_times').addClass('hidden');
		}
	});

	$(document).on('change', 'input[name="msconf_allow_seller_options"]', function() {
		if($(this).val() == 1) {
			$('.allowed_option_types').removeClass('hidden');
		} else {
			$('.allowed_option_types').addClass('hidden');
		}
	});

	if ($('input[name="msconf_shipping_type"]:checked').val() == 2 && $('input[name="msconf_vendor_shipping_type"]:checked').val() == 2){
		$('#shipping_methods_times').addClass('required');
	}else{
		$('#shipping_methods_times').removeClass('required');
	}

	$(document).on('change', 'input[name="msconf_shipping_type"], input[name="msconf_vendor_shipping_type"]', function() {
		if ($('input[name="msconf_shipping_type"]:checked').val() == 2 && $('input[name="msconf_vendor_shipping_type"]:checked').val() == 2){
			$('#shipping_methods_times').addClass('required');
		}else{
			$('#shipping_methods_times').removeClass('required');
		}
	});

	$('.sidebar a').on('shown.bs.tab', function(event){
		// Open certain tab if it is passed in url
		window.location.hash = event.target.hash;
		window.scrollTo(0, 0);

		var $updates_info_holder = $('#updates-info-holder');
		var $ms_changelog = $('.ms-changelog');
		var license_key = "<?php echo $this->config->get('msconf_license_key'); ?>";
		var license_activated = "<?php echo $this->config->get('msconf_license_activated'); ?>";

		// remove alert classes from updates info holder when clicking on another tabs
		if(license_activated != 0) {
			$updates_info_holder.removeClass().empty();
			$ms_changelog.empty().hide();
		}

		if($(event.target).attr('href') == '#tab-updates' && license_key && license_activated != 0) {
			$.ajax({
				type: "GET",
				dataType: 'json',
				beforeSend: function() {
					$updates_info_holder.removeClass('alert alert-warning').empty();
					$updates_info_holder.append('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');
					$ms_changelog.empty().hide();
				},
				url: 'index.php?route=module/multimerch/jxCheckUpdates&token=<?php echo $token; ?>',
				success: function(json) {
					var info_type = (json.error) ? 'danger' : (json.success ? 'info' : '');
					var info_msg = (json.error) ? json.error : (json.success ? json.success : '');

					// On license error show activate button and reset license
					if(json.error_license) {
						info_type = 'danger';
						info_msg = json.error_license;

						$.ajax({
							type: "GET",
							dataType: 'json',
							url: 'index.php?route=module/multimerch/jxResetLicense&token=<?php echo $token; ?>',
							success: function(json) {
								if(json.success) {
									$('#ms-activate-license').show();
								}
							}
						});
					}

					// If MM needs update and changelog exists
					if(json.changelog) {
						$ms_changelog.html(json.changelog).show();
					}

					$updates_info_holder.addClass('alert alert-' + info_type).html(info_msg);
				}
			});
		}
	});

	$(document).on('click', '#ms-activate-license', function() {
		var $button = $(this);
		var $license_info_holder = $('#license-info-holder');
		var license_key = $(document).find('input[name="msconf_license_key"]').val();

		$.ajax({
			type: "POST",
			dataType: 'json',
			data: {license_key: license_key},
			beforeSend: function() {
				$button.attr('disabled', true);
				$button.find('i').switchClass("fa-gears", "fa-spinner fa-spin", 0, "linear");
				$license_info_holder.removeClass().empty().hide();
			},
			url: 'index.php?route=module/multimerch/jxActivate&token=<?php echo $token; ?>',
			success: function(json) {
				$button.find('i').switchClass("fa-spinner fa-spin", "fa-gears", 0, "linear");

				var info_type = (json.error) ? 'danger' : (json.success ? 'success' : '');
				var info_msg = (json.error) ? json.error : (json.success ? json.success : '');

				if(json.success) {
					$('#ms-activate-license').hide();
				} else {
					$button.attr('disabled', false);
				}

				$license_info_holder.addClass('alert alert-' + info_type).html(info_msg).show();
			}
		});
	});

	var possible_oc_statuses = function() {
		var arr = [];
		$.ajax({
			type: 'GET',
			url: 'index.php?route=multimerch/suborder-status/jxAutocompleteOrderStatus&token=<?php echo $token; ?>&type=oc',
			dataType: 'json',
			success: function(json) {
				$.map(json, function(item) {
					arr.push(item.name);
				});
			}
		});

		return arr;
	}();

	var possible_ms_statuses = function() {
		var arr = [];
		$.ajax({
			type: 'GET',
			url: 'index.php?route=multimerch/suborder-status/jxAutocompleteOrderStatus&token=<?php echo $token; ?>&type=ms',
			dataType: 'json',
			success: function(json) {
				$.map(json, function(item) {
					arr.push(item.name);
				});
			}
		});

		return arr;
	}();

	var possible_oc_ms_statuses = function() {
		var arr = [];
		$.ajax({
			type: 'GET',
			url: 'index.php?route=multimerch/suborder-status/jxAutocompleteOrderStatus&token=<?php echo $token; ?>&type=oc_ms',
			dataType: 'json',
			success: function(json) {
				$.map(json, function(item) {
					if($.inArray(item.name, arr) === -1)
						arr.push(item.name);
				});
			}
		});

		return arr;
	}();

	$.widget("ui.autocomplete", $.ui.autocomplete, {
		_renderMenu: function(ul, items) {
			var self = this,
				currentStatusType = "";

			$.each(items, function(index, item) {
				if (item.status_type && item.status_type !== currentStatusType) {
					ul.append( "<li class='dropdown-header' style='pointer-events:none; font-size: 14px; border-bottom: 1px dotted #b2b2b2;'>" + item.status_type + "</li>" );
					currentStatusType = item.status_type;
				}

				if(item.status_id)
					self._renderItemData(ul, item);
				else
					ul.append( "<li class='no-results' style='pointer-events:none; color: #b2b2b2; font-size: 14px;'>" + item.label + "</li>" );
			});
		}
	});

	function initStatusFields(type) {
		var field_name = '',
			config_name = '',
			status_type = '',
			possible_statuses = [],
			selected_status_type = 'oc';

		// Check if event is
		var delete_event_fired = false;

		switch (type) {
			case 'oc':
				field_name = 'oc_order_statuses';
				config_name = 'msconf_order_state';
				status_type = 'oc';
				selected_status_type = 'oc';
				possible_statuses = possible_oc_statuses;
				break;

			case 'ms':
				field_name = 'ms_suborder_statuses';
				config_name = 'msconf_suborder_state';
				status_type = 'ms';
				selected_status_type = 'ms';
				possible_statuses = possible_ms_statuses;
				break;

			case 'credit':
				field_name = 'oc_order_credit_statuses';
				config_name = 'msconf_credit_order_statuses';
				status_type = 'oc_ms';
				possible_statuses = possible_oc_ms_statuses;

				break;

			case 'debit':
				field_name = 'oc_order_debit_statuses';
				config_name = 'msconf_debit_order_statuses';
				status_type = 'oc_ms';
				possible_statuses = possible_oc_ms_statuses;
				break;

			default:
				console.error('TagEditor can not be initialized: Incorrect type passed!');
				break;
		}

		$.map($('input[id^="' + field_name + '"'), function(status_field) {
			var $this = $(status_field);
			var state_id = $this.attr('id').split('_').splice(-1);
			var initial_tags = [];
			var hidden_field_name = config_name + '[' + state_id + '][]';

			if($.inArray(type, ['credit', 'debit']) !== -1) {
				hidden_field_name = config_name;
			}

			$.map($(document).find('input[name^="' + hidden_field_name + '"]'), function(initial_status) {
				initial_tags.push($(initial_status).data('name') + '_' + $(initial_status).data('type'));
			});

			$this.tagEditor({
				autocomplete: {
					delay: 250,
					minLength: 0,
					source: function (request, response) {
						var selected_oc_statuses = [],
							selected_ms_statuses = [];

						var selected_statuses_container = $(document).find('input[name^="' + config_name + '"]');

						// Unify selected statuses container for credit and debit order statuses
						if (config_name === 'msconf_credit_order_statuses' || config_name === 'msconf_debit_order_statuses') {
							selected_statuses_container = $(document).find('input[name^="msconf_credit_order_statuses"],input[name^="msconf_debit_order_statuses"]');
						}

						$.map(selected_statuses_container, function(selected_status) {
							if($(selected_status).data('type') === 'oc') {
								selected_oc_statuses.push($(selected_status).val());
							} else {
								selected_ms_statuses.push($(selected_status).val());
							}
						});

						var jxUrl = 'index.php?route=multimerch/suborder-status/jxAutocompleteOrderStatus&token=<?php echo $token; ?>';
						jxUrl += '&term=' + encodeURIComponent(request.term);
						jxUrl += '&type=' + encodeURIComponent(status_type);
						jxUrl += '&limit=10';

						if(selected_oc_statuses.length > 0)
							jxUrl += '&selected_oc=' + encodeURIComponent(selected_oc_statuses.join(','));

						if(selected_ms_statuses.length > 0)
							jxUrl += '&selected_ms=' + encodeURIComponent(selected_ms_statuses.join(','));

						$.ajax({
							type: 'GET',
							url: jxUrl,
							dataType: 'json',
							success: function(json) {
								if(json.length) {
									response($.map(json, function(item) {
										return {
											status_type: item.status_type,
											status_type_code: item.status_type_code,
											label: item.name,
											value: item.name,
											status_id: item.status_id
										};
									}));
								} else {
									response([{ label: "<?php echo $text_no_results; ?>" }]);
								}
							}
						});
					},
					select: function(event, ui) {
						if(typeof(ui.item.status_id) !== 'undefined') {
							selected_status_type = ui.item.status_type_code;

							$this.after('<input type="hidden" name="' + hidden_field_name + ($.inArray(type, ['credit', 'debit']) !== -1 ? '[' + selected_status_type + '][]' : '') + '" value="' + ui.item.status_id + '" data-name="' + ui.item.value + '" data-type="' + selected_status_type + '" />');
						}
					}
				},
				initialTags: initial_tags,
				beforeTagSave: function(field, editor, tags, tag, val) {
					if($.inArray(val, possible_statuses) === -1) {
						return false;
					}
				},
				onChange: function(field, editor, tags) {
					if(!delete_event_fired) {
						$(editor).find('li:last').addClass(selected_status_type);
						$(editor).find('li:last > .tag-editor-tag').append('<input type="hidden" value="' + selected_status_type + '" />');
					} else {
						delete_event_fired = false;
					}
				},
				beforeTagDelete: function(field, editor, tags, val) {
					var html = $.parseHTML(val);

					var status_hidden_field = $(field).siblings('input[name^="' + hidden_field_name + '"][data-name="' + $(html[0]).text() + '"][data-type="' + $(html[1]).val() + '"]');
					if(status_hidden_field.length) {
						delete_event_fired = true;
						status_hidden_field.remove();
					}
				},
				removeDuplicates: false,
				forceLowercase: false,
				placeholder: $.inArray(type, ['oc', 'credit', 'debit']) !== -1 ? '<?php echo $ms_config_order_status_autocomplete; ?>' : '<?php echo $ms_config_suborder_status_autocomplete; ?>'
			});

			$('li', $(status_field).tagEditor('getTags')[0].editor).each(function(){
				var li = $(this);
				var tag = li.find('.tag-editor-tag').text().split('_');

				if(typeof tag !== 'undefined') {
					li.find('.tag-editor-tag').html(tag[0] + '<input type="hidden" value="' + tag[1] + '" />');
					li.addClass(tag[1]);
				}
			});
		});
	}

	initStatusFields('oc');
	initStatusFields('ms');
	initStatusFields('credit');
	initStatusFields('debit');

	// Open tab passed in url
	var url = document.location.toString();
	if (url.match('#')) {
		$('.sidebar ul a[href="#' + url.split('#')[1] + '"]').tab('show');
	}

	$(document).on('keyup', 'input[name="msconf_sellers_slug"], input[name="msconf_products_slug"]', function() {
		$("#error").hide();
		$("#error").html('');
		if ($('input[name="msconf_sellers_slug"]').val() == $('input[name="msconf_products_slug"]').val()){
            $("#error").append('<p><?php echo $ms_settings_error_vendor_duplicate_seo_slug; ?></p>');
            $("#error").show();
		}
	});
});
</script>

<?php echo $footer; ?>
</div>