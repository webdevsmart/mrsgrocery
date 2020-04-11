<div class="list-group">
	<span class="list-group-item"><b><?php echo $text_my_account; ?></b></span>
	<?php if (!$logged) { ?>
		<a href="<?php echo $login; ?>" class="list-group-item"><?php echo $text_login; ?></a>
		<a href="<?php echo $register; ?>" class="list-group-item"><?php echo $text_register; ?></a>
		<a href="<?php echo $forgotten; ?>" class="list-group-item"><?php echo $text_forgotten; ?></a>
	<?php } else { ?>
		<a href="<?php echo $account; ?>" class="list-group-item"><?php echo $ms_account_overview; ?></a>
		<a href="<?php echo $edit; ?>" class="list-group-item"><?php echo $text_edit; ?></a>
		<a href="<?php echo $password; ?>" class="list-group-item"><?php echo $text_password; ?></a>
		<a href="<?php echo $newsletter; ?>" class="list-group-item"><?php echo $text_newsletter; ?></a>
		<a href="<?php echo $logout; ?>" class="list-group-item"><?php echo $ms_account_logout; ?></a>
	<?php } ?>
</div>

<?php if ($logged) { ?>
	<div class="list-group">
		<span class="list-group-item"><b><?php echo $ms_account_customer; ?></b></span>
		<a href="<?php echo $address; ?>" class="list-group-item"><?php echo $text_address; ?></a>
		<a href="<?php echo $wishlist; ?>" class="list-group-item"><?php echo $text_wishlist; ?></a>
		<a href="<?php echo $order; ?>" class="list-group-item"><?php echo $text_order; ?></a>
		<?php if ($this->config->get('mmess_conf_enable') == 1) { ?>
			<a class="list-group-item" href="<?php echo $this->url->link('account/msconversation', '', 'SSL'); ?>"><?php echo $ms_account_messages ;?></a>
		<?php } ?>
		<a href="<?php echo $download; ?>" class="list-group-item"><?php echo $text_download; ?></a>
		<a href="<?php echo $recurring; ?>" class="list-group-item"><?php echo $text_recurring; ?></a>
		<a href="<?php echo $reward; ?>" class="list-group-item"><?php echo $text_reward; ?></a>
		<a href="<?php echo $return; ?>" class="list-group-item"><?php echo $text_return; ?></a>
		<a href="<?php echo $transaction; ?>" class="list-group-item"><?php echo $text_transaction; ?></a>
	</div>
	<div class="list-group">
		<span class="list-group-item"><b><?php echo $ms_seller ;?></b></span>
		<?php if ($ms_seller_created && $this->MsLoader->MsSeller->getStatus($this->customer->getId()) == MsSeller::STATUS_ACTIVE) { ?>
			<a class="list-group-item" href="<?php echo $this->url->link('seller/account-dashboard', '', 'SSL') ;?>"><?php echo $ms_account_dashboard ;?></a>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-order', '', 'SSL'); ?>"><?php echo $ms_account_orders ;?></a>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-product', '', 'SSL'); ?>"><?php echo $ms_account_products ;?></a>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-transaction', '', 'SSL'); ?>"><?php echo $ms_account_transactions ;?></a>
			<!--<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-payment', '', 'SSL'); ?>"><?php echo $ms_account_payments ;?></a>-->
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-payment-request', '', 'SSL'); ?>"><?php echo $ms_account_payment_requests ;?></a>

			<?php if ($this->config->get('msconf_allow_seller_coupons')) { ?>
				<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-coupon', '', 'SSL'); ?>"><?php echo $ms_seller_account_coupon; ?></a>
			<?php } ?>

			<a href="#reports-sub" class="list-group-item" data-toggle="collapse"><?php echo $ms_report_report; ?> <i class="fa fa-caret-down"></i></a>
			<div class="collapse list-group-submenu" id="reports-sub">
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/sales', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_sales_list; ?></a>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/sales-day', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_sales_day; ?></a>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/sales-month', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_sales_month; ?></a>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/sales-product', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_sales_product; ?></a>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/finances-transaction', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_finances_transaction; ?></a>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/finances-payment', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_finances_payment; ?></a>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/finances-payout', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_finances_payout; ?></a>
			</div>

			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-profile', '', 'SSL'); ?>"><?php echo $ms_account_profile ;?></a>

			<?php if ($this->config->get('msconf_reviews_enable')) { ?>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/account-review', '', 'SSL'); ?>"><?php echo $ms_account_reviews; ?></a>
			<?php } ?>

			<?php if ($this->config->get('msconf_allow_questions')) { ?>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/account-question', '', 'SSL'); ?>"><?php echo $ms_account_questions; ?></a>
			<?php } ?>

			<?php if ($this->config->get('mmess_conf_enable') == 1) { ?>
				<a class="list-group-item" href="<?php echo $this->url->link('account/msconversation', '', 'SSL'); ?>"><?php echo $ms_account_messages ;?></a>
			<?php } ?>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-setting', '', 'SSL'); ?>"><?php echo $ms_account_settings ;?></a>
		<?php } else if ($ms_seller_created && $this->MsLoader->MsSeller->getStatus($this->customer->getId()) == MsSeller::STATUS_UNPAID) { ?>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-payment-request', '', 'SSL'); ?>"><?php echo $ms_account_payment_requests ;?></a>
			<!--<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-payment', '', 'SSL'); ?>"><?php echo $ms_account_payments ;?></a>-->
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-profile', '', 'SSL'); ?>"><?php echo $ms_account_profile ;?></a>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-setting', '', 'SSL'); ?>"><?php echo $ms_account_settings ;?></a>
		<?php } else { ?>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-profile', '', 'SSL'); ?>"><?php echo $ms_account_sellerinfo_new ;?></a>
		<?php } ?>
	</div>
<?php } ?>

<script>
	$(document).ready(function() {
		var items = $('#column-left .list-group a');
		for(var i = 0; i < items.length; i++) {
			var url = $(items[i]).attr('href');
			if(url == window.location.href) {
				$(items[i]).addClass('active');
			}
		}
	});
</script>