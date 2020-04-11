<div class="box"><div class="box-content list-group"><ul>
	<span class="list-group-item"><b><?php echo $text_my_account; ?></b></span>
	<?php if (!$logged) { ?>
<li>
		<a href="<?php echo $login; ?>" class="list-group-item"><?php echo $text_login; ?></a>
</li>
<li>
		<a href="<?php echo $register; ?>" class="list-group-item"><?php echo $text_register; ?></a>
</li>
<li>
		<a href="<?php echo $forgotten; ?>" class="list-group-item"><?php echo $text_forgotten; ?></a>
</li>
	<?php } else { ?>
<li>
		<a href="<?php echo $account; ?>" class="list-group-item"><?php echo $ms_account_overview; ?></a>
</li>
<li>
		<a href="<?php echo $edit; ?>" class="list-group-item"><?php echo $text_edit; ?></a>
</li>
<li>
		<a href="<?php echo $password; ?>" class="list-group-item"><?php echo $text_password; ?></a>
</li>
<li>
		<a href="<?php echo $newsletter; ?>" class="list-group-item"><?php echo $text_newsletter; ?></a>
</li>
<li>
		<a href="<?php echo $logout; ?>" class="list-group-item"><?php echo $ms_account_logout; ?></a>
</li>
	<?php } ?>
</ul></div>
</div>

<?php if ($logged) { ?>
	<div class="box"><div class="box-content list-group"><ul>
		<span class="list-group-item"><b><?php echo $ms_account_customer; ?></b></span>
<li>
		<a href="<?php echo $address; ?>" class="list-group-item"><?php echo $text_address; ?></a>
</li>
<li>
		<a href="<?php echo $wishlist; ?>" class="list-group-item"><?php echo $text_wishlist; ?></a>
</li>
<li>
		<a href="<?php echo $order; ?>" class="list-group-item"><?php echo $text_order; ?></a>
</li>
		<?php if ($this->config->get('mmess_conf_enable') == 1) { ?>
<li>
			<a class="list-group-item" href="<?php echo $this->url->link('account/msconversation', '', 'SSL'); ?>"><?php echo $ms_account_messages ;?></a>
</li>
		<?php } ?>
<li>
		<a href="<?php echo $download; ?>" class="list-group-item"><?php echo $text_download; ?></a>
</li>
<li>
		<a href="<?php echo $recurring; ?>" class="list-group-item"><?php echo $text_recurring; ?></a>
</li>
<li>
		<a href="<?php echo $reward; ?>" class="list-group-item"><?php echo $text_reward; ?></a>
</li>
<li>
		<a href="<?php echo $return; ?>" class="list-group-item"><?php echo $text_return; ?></a>
</li>
<li>
		<a href="<?php echo $transaction; ?>" class="list-group-item"><?php echo $text_transaction; ?></a>
</li>
</ul></div>
	</div>
	<div class="box"><div class="box-content list-group"><ul>
		<span class="list-group-item"><b><?php echo $ms_seller ;?></b></span>
		<?php if ($ms_seller_created && $this->MsLoader->MsSeller->getStatus($this->customer->getId()) == MsSeller::STATUS_ACTIVE) { ?>
<li>
			<a class="list-group-item" href="<?php echo $this->url->link('seller/account-dashboard', '', 'SSL') ;?>"><?php echo $ms_account_dashboard ;?></a>
</li>
<li>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-order', '', 'SSL'); ?>"><?php echo $ms_account_orders ;?></a>
</li>
<li>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-product', '', 'SSL'); ?>"><?php echo $ms_account_products ;?></a>
</li>
<li>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-transaction', '', 'SSL'); ?>"><?php echo $ms_account_transactions ;?></a>
</li>
<li>
			<!--<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-payment', '', 'SSL'); ?>"><?php echo $ms_account_payments ;?></a>-->
</li>
<li>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-payment-request', '', 'SSL'); ?>"><?php echo $ms_account_payment_requests ;?></a>
</li>

			<?php if ($this->config->get('msconf_allow_seller_coupons')) { ?>
<li>
				<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-coupon', '', 'SSL'); ?>"><?php echo $ms_seller_account_coupon; ?></a>
</li>
			<?php } ?>

<li>
			<a href="#reports-sub" class="list-group-item" data-toggle="collapse"><?php echo $ms_report_report; ?> <i class="fa fa-caret-down"></i></a>
</li>
			<div><div class="box-content collapse list-group-submenu" id="reports-sub"><ul>
<li>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/sales', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_sales_list; ?></a>
</li>
<li>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/sales-day', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_sales_day; ?></a>
</li>
<li>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/sales-month', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_sales_month; ?></a>
</li>
<li>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/sales-product', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_sales_product; ?></a>
</li>
<li>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/finances-transaction', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_finances_transaction; ?></a>
</li>
<li>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/finances-payment', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_finances_payment; ?></a>
</li>
<li>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/report/finances-payout', '', 'SSL') ;?>" data-parent="#reports-sub"><?php echo $ms_report_finances_payout; ?></a>
</li>
</ul></div>
			</div>

<li>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-profile', '', 'SSL'); ?>"><?php echo $ms_account_profile ;?></a>
</li>

			<?php if ($this->config->get('msconf_reviews_enable')) { ?>
<li>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/account-review', '', 'SSL'); ?>"><?php echo $ms_account_reviews; ?></a>
</li>
			<?php } ?>

			<?php if ($this->config->get('msconf_allow_questions')) { ?>
<li>
				<a class="list-group-item" href="<?php echo $this->url->link('seller/account-question', '', 'SSL'); ?>"><?php echo $ms_account_questions; ?></a>
</li>
			<?php } ?>

			<?php if ($this->config->get('mmess_conf_enable') == 1) { ?>
<li>
				<a class="list-group-item" href="<?php echo $this->url->link('account/msconversation', '', 'SSL'); ?>"><?php echo $ms_account_messages ;?></a>
</li>
			<?php } ?>
<li>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-setting', '', 'SSL'); ?>"><?php echo $ms_account_settings ;?></a>
</li>
		<?php } else if ($ms_seller_created && $this->MsLoader->MsSeller->getStatus($this->customer->getId()) == MsSeller::STATUS_UNPAID) { ?>
<li>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-payment-request', '', 'SSL'); ?>"><?php echo $ms_account_payment_requests ;?></a>
</li>
<li>
			<!--<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-payment', '', 'SSL'); ?>"><?php echo $ms_account_payments ;?></a>-->
</li>
<li>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-profile', '', 'SSL'); ?>"><?php echo $ms_account_profile ;?></a>
</li>
<li>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-setting', '', 'SSL'); ?>"><?php echo $ms_account_settings ;?></a>
</li>
		<?php } else { ?>
<li>
			<a class="list-group-item" href= "<?php echo $this->url->link('seller/account-profile', '', 'SSL'); ?>"><?php echo $ms_account_sellerinfo_new ;?></a>
</li>
		<?php } ?>
</ul></div>
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