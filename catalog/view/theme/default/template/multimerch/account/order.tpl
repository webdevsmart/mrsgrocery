<?php echo $header; ?>
<div class="container">
	<ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	</ul>
	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
		<?php $class = 'col-sm-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
		<?php $class = 'col-sm-9'; ?>
		<?php } else { ?>
		<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<div id="content" class="<?php echo $class; ?> ms-account-order-history"><?php echo $content_top; ?>
			<h1><?php echo $heading_title; ?></h1>

			<?php if ($success) { ?>
				<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
					<button type="button" class="close" data-dismiss="alert">&times;</button>
				</div>
			<?php } ?>

			<?php foreach($orders['orders'] as $order) :?>
				<div class="mm-panel-wrapper ms-account-order-history-panel">
					<div class="panel panel-default">
						<div class="panel-heading order-heading ms-account-order-history-panel-heading">
							<div class="row">
								<span class="col-xs-3">
									<b><?php echo $ms_order_placed ;?> <?php echo $order['date_added'] ;?></b>
									<b><?php echo $ms_status ;?>: <?php echo $order['status'] ;?></b>
								</span>

								<span class="col-xs-5"><?php echo $ms_order_dispatch ;?> <br /><?php echo $order['firstname'];?> <?php echo $order['lastname'] ;?></span>
								<span class="col-xs-2"><?php echo $ms_order_total ;?> <br /><?php echo $order['total'] ;?></span>
								<span class="order-number col-xs-2">
									#<?php echo $order['order_id'] ;?><br />
									<a href="<?php echo $this->url->link('account/order/info', 'order_id=' . $order['order_id'], 'SSL') ;?>">
										<?php echo $ms_order_details ;?>
									</a>
								</span>
							</div>
						</div>
						<div class="panel-body ms-account-order-history-panel-body">
							<?php foreach($orders['products'][$order['order_id']] as $product) :?>
								<div class="product-holder ms-account-order-history-product-holder">
									<div class="row">
										<div class="product-image col-xs-3">
											<a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']) ;?>">
												<img src="<?php echo $product['product']['image'] ;?>"/>
											</a>
										</div>
										<div class="product-info col-xs-5">
											<h4>
												<span class="quantity"><?php echo $product['quantity'] ;?>x</span>
												<a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']) ;?>">
													<?php echo $product['name'] ;?>
												</a>
												<?php if(is_array($product['options']) && !empty($product['options'])) :?>
													<ul class="product-options">
														<?php foreach($product['options'] as $option) :?>
															<li>
																<?php echo $option['name'] ;?>: <b><?php echo $option['value'] ;?></b>
															</li>
														<?php endforeach ;?>
													</ul>
												<?php endif ;?>
											</h4>
											<span class="seller-name">
												<?php echo $ms_order_sold_by ;?> <a href="<?php echo $this->url->link('seller/catalog-seller/profile', 'seller_id=' . $product['seller']['seller_id']) ;?>"><?php echo $product['seller']['ms.nickname'] ;?></a>
											</span>
											<span class="suborder-status">
												<?php echo $ms_order_status ;?> <b><?php echo $this->MsLoader->MsSuborderStatus->getSubStatusName(array('order_status_id' => $product['suborder_status'])); ?></b>
											</span>
										</div>
										<div class="col-xs-2">
											<span class="product-price">
												<?php echo $product['price'] ;?>
											</span>
										</div>
										<div class="order-actions col-md-2">
											<?php if($this->config->get('msconf_reviews_enable') && !$product['review']) :?>
												<a href ="<?php echo $this->url->link('customer/review/create', 'product_id=' . $product['product_id'] . '&order_id=' . $order['order_id'] . '&order_product_id=' . $product['order_product_id']) ;?>" class="action btn btn-primary"><?php echo $ms_order_feedback ;?></a>
											<?php endif ;?>
											<a href ="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']) ;?>" class="action btn btn-default"><?php echo $ms_order_buy_again ;?></a>
											<a href="<?php echo $this->url->link('account/return/add', 'order_id=' . $order['order_id'] . '&product_id=' . $product['product_id'], true) ?>" class="action btn btn-default"><?php echo $ms_order_return ;?></a>
										</div>
									</div>
								</div>
							<?php endforeach ;?>
						</div>
					</div>
				</div>
			<?php endforeach ;?>

			<?php if (empty($orders['orders'])) { ?>
				<p><?php echo $ms_account_no_orders; ?></p>
				<div class="buttons clearfix">
					<div class="pull-right"><a href="<?php echo $this->url->link('account/account', '', 'SSL'); ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
				</div>
			<?php } ?>

			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<?php echo $footer; ?>
