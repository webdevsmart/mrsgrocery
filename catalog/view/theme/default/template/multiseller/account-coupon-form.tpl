<?php echo $header; ?>
<div class="container">
	<ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	</ul>

	<?php if (isset($success) && $success) { ?>
		<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
	<?php } ?>

	<div class="alert alert-danger" id="error-holder" style="display: none;"></div>

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
				<h1><?php echo $heading; ?></h1>

				<form id="ms-new-coupon" class="tab-content ms-coupon">
					<input type="hidden" name="coupon[coupon_id]" value="<?php echo $coupon['coupon_id']; ?>" />

					<fieldset id="mm_general">
						<legend><?php echo $ms_seller_account_coupon_general; ?></legend>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_seller_account_coupon_name; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="coupon[name]" value="<?php echo isset($coupon['name']) ? $coupon['name'] : ''; ?>" />
								<p class="ms-note"><?php echo $ms_seller_account_coupon_name_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_seller_account_coupon_code; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="coupon[code]" value="<?php echo isset($coupon['code']) ? $coupon['code'] : ''; ?>" maxlength="12" />
								<p class="ms-note"><?php echo $ms_seller_account_coupon_code_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_seller_account_coupon_value; ?></label>
							<div class="col-sm-10">
								<select name="coupon[type]" class="form-control ms-coupon-type-select">
									<option value="<?php echo (int)MsCoupon::TYPE_DISCOUNT_PERCENT; ?>" <?php if(isset($coupon['type']) && (int)$coupon['type'] === (int)MsCoupon::TYPE_DISCOUNT_PERCENT) { ?>selected="selected"<?php } ?>><?php echo ${'ms_seller_account_coupon_type_' . MsCoupon::TYPE_DISCOUNT_PERCENT}; ?></option>
									<option value="<?php echo (int)MsCoupon::TYPE_DISCOUNT_FIXED; ?>" <?php if(isset($coupon['type']) && (int)$coupon['type'] === (int)MsCoupon::TYPE_DISCOUNT_FIXED) { ?>selected="selected"<?php } ?>><?php echo ${'ms_seller_account_coupon_type_' . MsCoupon::TYPE_DISCOUNT_FIXED}; ?></option>
								</select>

								<div class="input-group">
									<?php if ($this->currency->getSymbolLeft($this->config->get('config_currency'))) { ?>
										<span class="input-group-addon ms-coupon-type-fixed left-symbol"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
									<?php } ?>

									<input type="text" class="form-control" name="coupon[value]" value="<?php echo isset($coupon['value']) ? $coupon['value'] : ''; ?>" />

									<?php if ($this->currency->getSymbolRight($this->config->get('config_currency'))) { ?>
										<span class="input-group-addon ms-coupon-type-fixed right-symbol"><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
									<?php } ?>

									<span class="input-group-addon ms-coupon-type-pct right-symbol">%</span>
								</div>

								<p class="ms-note"><?php echo $ms_seller_account_coupon_value_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_seller_account_coupon_status; ?></label>
							<div class="col-sm-10">
								<select name="coupon[status]" id="select-coupon-status" class="form-control">
									<option value="<?php echo (int)MsCoupon::STATUS_ACTIVE; ?>" <?php if(isset($coupon['status']) && (int)$coupon['status'] === (int)MsCoupon::STATUS_ACTIVE) { ?>selected="selected"<?php } ?>><?php echo ${'ms_seller_account_coupon_status_' . (int)MsCoupon::STATUS_ACTIVE}; ?></option>
									<option value="<?php echo (int)MsCoupon::STATUS_DISABLED; ?>" <?php if(isset($coupon['status']) && (int)$coupon['status'] === (int)MsCoupon::STATUS_DISABLED) { ?>selected="selected"<?php } ?>><?php echo ${'ms_seller_account_coupon_status_' . (int)MsCoupon::STATUS_DISABLED}; ?></option>
								</select>
								<p class="ms-note"><?php echo $ms_seller_account_coupon_status_note; ?></p>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend><?php echo $ms_seller_account_coupon_restrictions; ?></legend>

						<div class="form-group">
							<label class="mm_label col-sm-2"><?php echo $ms_seller_account_coupon_products; ?></label>
							<div class="col-sm-10">
								<?php if (!empty($seller_products)) { ?>
									<select name="coupon[flag_include_products]" class="form-control flag-include-select">
										<option value="include" <?php if(!empty($coupon['products']['include'])) { ?>selected="selected"<?php } ?>><?php echo $ms_seller_account_coupon_products_include; ?></option>
										<option value="exclude" <?php if(!empty($coupon['products']['exclude'])) { ?>selected="selected"<?php } ?>><?php echo $ms_seller_account_coupon_products_exclude; ?></option>
									</select>

									<input type="text" id="coupon_products" placeholder="<?php echo $this->language->get('ms_seller_account_coupon_products_placeholder'); ?>" />

									<?php if(!empty($coupon['products'])) { ?>
										<?php foreach($coupon['products'] as $key => $products) { ?>
											<?php foreach($products as $product) { ?>
												<input type="hidden" name="coupon[products][<?php echo $key; ?>][]" value="<?php echo $product['product_id']; ?>" data-name="<?php echo $product['name']; ?>" />
											<?php } ?>
										<?php } ?>
									<?php } ?>

									<p class="ms-note"><?php echo $ms_seller_account_coupon_products_note; ?></p>
								<?php } else { ?>
									<p class="ms-no-items"><?php echo $ms_seller_account_coupon_products_empty; ?></p>
								<?php } ?>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2"><?php echo $ms_seller_account_coupon_categories; ?></label>
							<div class="col-sm-10">
								<?php if ($this->config->get('msconf_allow_seller_categories')) { ?>
									<?php if (!empty($seller_categories)) { ?>
										<select name="coupon[flag_include_ms_categories]" class="form-control flag-include-select">
											<option value="include" <?php if(!empty($coupon['ms_categories']['include'])) { ?>selected="selected"<?php } ?>><?php echo $ms_seller_account_coupon_categories_include; ?></option>
											<option value="exclude" <?php if(!empty($coupon['ms_categories']['exclude'])) { ?>selected="selected"<?php } ?>><?php echo $ms_seller_account_coupon_categories_exclude; ?></option>
										</select>

										<input type="text" id="coupon_ms_categories" placeholder="<?php echo $this->language->get('ms_seller_account_coupon_categories_placeholder'); ?>" />

										<?php if(!empty($coupon['ms_categories'])) { ?>
											<?php foreach($coupon['ms_categories'] as $key => $ms_categories) { ?>
												<?php foreach($ms_categories as $ms_category) { ?>
													<input type="hidden" name="coupon[ms_categories][<?php echo $key; ?>][]" value="<?php echo $ms_category['ms_category_id']; ?>" data-name="<?php echo $ms_category['name']; ?>" />
												<?php } ?>
											<?php } ?>
										<?php } ?>

										<p class="ms-note"><?php echo $ms_seller_account_coupon_categories_note; ?></p>
									<?php } else { ?>
										<p class="ms-no-items"><?php echo $ms_seller_account_coupon_categories_empty; ?></p>
									<?php } ?>
								<?php } else { ?>
									<?php if (!empty($marketplace_categories)) { ?>
										<select name="coupon[flag_include_oc_categories]" class="form-control flag-include-select">
											<option value="include" <?php if(!empty($coupon['oc_categories']['include'])) { ?>selected="selected"<?php } ?>><?php echo $ms_seller_account_coupon_categories_include; ?></option>
											<option value="exclude" <?php if(!empty($coupon['oc_categories']['exclude'])) { ?>selected="selected"<?php } ?>><?php echo $ms_seller_account_coupon_categories_exclude; ?></option>
										</select>

										<input type="text" id="coupon_oc_categories" placeholder="<?php echo $this->language->get('ms_seller_account_coupon_categories_placeholder'); ?>" />

										<?php if(!empty($coupon['oc_categories'])) { ?>
											<?php foreach($coupon['oc_categories'] as $key => $oc_categories) { ?>
												<?php foreach($oc_categories as $oc_category) { ?>
													<input type="hidden" name="coupon[oc_categories][<?php echo $key; ?>][]" value="<?php echo $oc_category['oc_category_id']; ?>" data-name="<?php echo $oc_category['name']; ?>" />
												<?php } ?>
											<?php } ?>
										<?php } ?>

										<p class="ms-note"><?php echo $ms_seller_account_coupon_categories_note; ?></p>
									<?php } else { ?>
										<p class="ms-no-items"><?php echo $ms_seller_account_coupon_categories_empty; ?></p>
									<?php } ?>
								<?php } ?>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2"><?php echo $ms_seller_account_coupon_max_uses; ?></label>
							<div class="col-sm-10">
								<div class="input-group col-xs-12 col-sm-12 col-md-12 col-lg-5 max-uses-input">
									<span class="input-group-addon"><?php echo $ms_seller_account_coupon_max_uses_total; ?></span>
									<input type="text" class="form-control" name="coupon[max_uses]" value="<?php echo isset($coupon['max_uses']) ? $coupon['max_uses'] : ''; ?>" />
								</div>
								<div class="col-lg-2"></div>
								<div class="input-group col-xs-12 col-sm-12 col-md-12 col-lg-5 max-uses-customer-input">
									<span class="input-group-addon"><?php echo $ms_seller_account_coupon_max_uses_customer; ?></span>
									<input type="text" class="form-control" name="coupon[max_uses_customer]" value="<?php echo isset($coupon['max_uses_customer']) ? $coupon['max_uses_customer'] : ''; ?>" />
								</div>
								<p class="ms-note"><?php echo $ms_seller_account_coupon_max_uses_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2"><?php echo $ms_seller_account_coupon_min_order_total; ?></label>
							<div class="col-sm-10">
								<div class="input-group">
									<?php if ($this->currency->getSymbolLeft($this->config->get('config_currency'))) { ?>
										<span class="input-group-addon"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
									<?php } ?>
									<input type="text" class="form-control" name="coupon[min_order_total]" value="<?php echo isset($coupon['min_order_total']) ? $coupon['min_order_total'] : ''; ?>" />
									<?php if ($this->currency->getSymbolRight($this->config->get('config_currency'))) { ?>
										<span class="input-group-addon"><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
									<?php } ?>
								</div>
								<p class="ms-note"><?php echo $ms_seller_account_coupon_min_order_total_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2"><?php echo $ms_seller_account_coupon_date_period; ?></label>
							<div class="col-sm-10">
								<div class="input-group date col-xs-12 col-sm-12 col-md-12 col-lg-5">
									<span class="input-group-addon"><?php echo $ms_seller_account_coupon_date_start; ?></span>
									<input type="text" name="coupon[date_start]" value="<?php echo isset($coupon['date_start']) ? $coupon['date_start'] : ''; ?>" placeholder="<?php echo $this->language->get('ms_seller_account_coupon_date_placeholder'); ?>" data-date-format="YYYY-MM-DD" class="form-control" />
									<span class="input-group-btn">
										<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
									</span>
								</div>
								<div class="col-lg-2"></div>
								<div class="input-group date col-xs-12 col-sm-12 col-md-12 col-lg-5">
									<span class="input-group-addon"><?php echo $ms_seller_account_coupon_date_end; ?></span>
									<input type="text" name="coupon[date_end]" value="<?php echo !empty($coupon['date_end']) ? $coupon['date_end'] : ''; ?>" placeholder="<?php echo $this->language->get('ms_seller_account_coupon_date_placeholder'); ?>" data-date-format="YYYY-MM-DD" class="form-control" />
									<span class="input-group-btn">
										<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
									</span>
								</div>
								<p class="ms-note"><?php echo $ms_seller_account_coupon_date_period_note; ?></p>
							</div>
						</div>
					</fieldset>
				</form>

				<div class="buttons">
					<div class="pull-left"><a href="<?php echo $back; ?>"><span><?php echo $ms_button_cancel; ?></span></a></div>
					<div class="pull-right"><a class="btn btn-primary ms-spinner" id="ms-submit-button"><span><?php echo $ms_button_submit; ?></span></a></div>
				</div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>

<script>
	var msGlobals = {
		formError: '<?php echo htmlspecialchars($ms_error_form_submit_error, ENT_QUOTES, "UTF-8"); ?>',
		msconf_allow_seller_categories: '<?php echo (int)$this->config->get("msconf_allow_seller_categories"); ?>',
		ms_coupon_type_pct: '<?php echo (int)MsCoupon::TYPE_DISCOUNT_PERCENT; ?>',
		ms_coupon_type_fixed: '<?php echo (int)MsCoupon::TYPE_DISCOUNT_FIXED; ?>',
		ms_seller_account_coupon_categories_placeholder: '<?php echo $this->language->get("ms_seller_account_coupon_categories_placeholder"); ?>',
		ms_seller_account_coupon_categories_placeholder_products_specified: '<?php echo $this->language->get("ms_seller_account_coupon_categories_placeholder_products_specified"); ?>'
	};
</script>
<?php echo $footer; ?>
