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
		<div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
			<h1 class="heading-title"><?php echo $heading_title; ?></h1>

			<?php foreach($orders['orders'] as $order) :?>
				<div class="panel panel-default panel-order">
					<div class="panel-heading order-heading">
						<div class="row">
						<span class="xl-30"><?php echo $ms_order_placed ;?> <br /><?php echo $order['date_added'] ;?></b></span>

						<span class="xl-40"><?php echo $ms_order_dispatch ;?> <br /><?php echo $order['firstname'];?> <?php echo $order['lastname'] ;?></span>
						<span class="xl-10"><?php echo $ms_order_total ;?> <br /><?php echo $order['total'] ;?></span>
						<span class="order-number xl-20 xs-100">
							#<?php echo $order['order_id'] ;?><br />
							<a href="<?php echo $this->url->link('account/order/info', 'order_id=' . $order['order_id'], 'SSL') ;?>">
								<?php echo $ms_order_details ;?>
							</a>
						</span>
						</div>
					</div>
					<div class="panel-body">
						<?php foreach($orders['products'][$order['order_id']] as $product) :?>
                            <div class="product-holder">
                                <div class="row">
                                    <div class="product-image xl-30 xs-100">
                                        <a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']) ;?>">
                                            <img src="<?php echo $product['product']['image'] ;?>"/>
                                        </a>
                                    </div>
                                    <div class="xl-40 xs-100">
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
                                            <?php echo $ms_order_sold_by ;?> <a href="<?php echo $this->url->link('seller/catalog-seller/profile', 'seller_id=' . $product['seller']['seller_id']) ;?>"><?php echo $product['seller']['name'] ;?></a>
                                        </span>
                                    </div>
                                    <div class="xl-10 xs-100">
                                        <span class="product-price">
                                            <?php echo $product['price'] ;?>
                                        </span>
                                    </div>
                                    <div class="order-actions xl-20 xs-100">
										<?php if($this->config->get('msconf_reviews_enable') && !$product['review']) :?>
											<a href ="<?php echo $this->url->link('customer/review/create', 'product_id=' . $product['product_id'] . '&order_id=' . $order['order_id'] . '&order_product_id=' . $product['order_product_id']) ;?>" class="action btn btn-primary"><?php echo $ms_order_feedback ;?></a>
										<?php endif ;?>
                                        <a href ="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']) ;?>" class="action btn btn-default pull-right"><?php echo $ms_order_buy_again ;?></a>
                                        <a href="<?php echo $this->url->link('account/return/add', 'order_id=' . $order['order_id'] . '&product_id=' . $product['product_id'], true) ?>" class="action btn btn-link pull-right"><?php echo $ms_order_return ;?></a>
                                    </div>
                                </div>
                            </div>
						<?php endforeach ;?>
					</div>
				</div>
			<?php endforeach ;?>

			<?php if (empty($orders['orders'])) { ?>
				<p><?php echo $ms_account_no_orders; ?></p>
				<div class="buttons">
					<div class="pull-right"><a href="<?php echo $this->url->link('account/account', '', 'SSL'); ?>" class="btn btn-primary button"><?php echo $button_continue; ?></a></div>
				</div>
			<?php } ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<?php echo $footer; ?>
