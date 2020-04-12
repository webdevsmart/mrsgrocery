<div class="checkout-content checkout-shipping-methods">
	<?php if ($error_no_shipping_methods) { ?>
		<div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <?php echo $error_no_shipping_methods; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
	<?php } ?>

	<h2 class="secondary-title"><?php echo $this->journal2->settings->get('one_page_lang_ship_method', 'Shipping Method'); ?></h2>

	<div class="panel-body"></div>

	<div class="clearfix"></div>

	<?php if(!empty($cart_products)) { ?>
		<div class="shm-title">
			<span><?php echo $mm_checkout_shipping_products_title; ?></span>
		</div>
		<div class="cart-products table-responsive">
			<table class="table">
				<?php foreach($cart_products as $seller_id => $shipping_rules) { ?>
					<tbody style="display:block; border-bottom: 1px #eeeeee solid;">
						<tr>
							<td colspan="4">
								<?php if(MsLoader::getInstance()->MsSeller->getSellerNickname($seller_id)) { ?>
									<h3><?php echo $ms_seller;?> <?php echo MsLoader::getInstance()->MsSeller->getSellerNickname($seller_id); ?></h3>
								<?php } else { ?>
									<h3><?php echo $ms_store_owner;?></h3>
								<?php } ?>
							</td>
						</tr>

						<?php if(isset($shipping_rules['fixed_shipping']) && !empty($shipping_rules['fixed_shipping'])) { ?>
							<!-- Products having fixed shipping rate -->

							<?php foreach($shipping_rules['fixed_shipping'] as $product) { ?>
								<tr>
									<td class="col-sm-4">
										<div class="product-info">
											<div>
												<strong><?php echo $product['name']; ?></strong>
											</div>
											<div>
												<ul>
													<?php foreach($product['options'] as $option) { ?>
														<li><?php echo $option['name'] . ': ' . $option['value']; ?></li>
													<?php } ?>
												</ul>
											</div>
											<div>
												<span><?php echo $mm_checkout_shipping_products_price; ?></span>
												<?php echo $product['price_formatted']; ?>
											</div>
											<div>
												<span><?php echo $mm_checkout_shipping_products_quantity; ?></span>
												<span><?php echo $product['quantity']; ?></span>
											</div>
										</div>
									</td>
									<td class="col-sm-4 sm-select">
										<div class="shipping-method-select" style="padding: 20px 0 0 20px;">
											<?php if($product['shipping_methods']['free_shipping'] || !$product['shipping_required']) { ?>
												<strong><?php echo !$product['shipping_required'] ? $mm_checkout_shipping_not_required : $mm_checkout_shipping_method_free; ?></strong>

												<input type="hidden" name="free_shipping[<?php echo $product['product_id']; ?>]" value="1" />
											<?php } else if(!empty($product['shipping_methods']) && !empty($product['shipping_methods']['locations'])) { ?>
												<strong><?php echo $mm_checkout_shipping_method_title_shot; ?></strong>

												<?php foreach($product['shipping_methods']['locations'] as $location) { ?>
													<div class="radio">
														<label>
															<?php if (isset($this->session->data['ms_cart_product_shipping']['fixed'][$product['product_id']][$product['cart_id']]['shipping_method_id'])) { ?>
																<input type="radio" name="fixed_shipping_method[<?php echo $product['product_id']; ?>][<?php echo $product['cart_id']; ?>]" value="<?php echo $location['mspl.location_id']; ?>" <?php echo ((int)$location['mspl.location_id'] === (int)$this->session->data['ms_cart_product_shipping']['fixed'][$product['product_id']][$product['cart_id']]['location_id']) ? 'checked="checked"' : ''; ?> />
															<?php } else { ?>
																<input type="radio" name="fixed_shipping_method[<?php echo $product['product_id']; ?>][<?php echo $product['cart_id']; ?>]" value="<?php echo $location['mspl.location_id']; ?>" <?php echo ($location === reset($product['shipping_methods']['locations'])) ? 'checked="checked"' : ''; ?> />
															<?php } ?>

															<?php echo $location['shipping_method_name']; ?> (<?php echo $location['delivery_time_name']; ?>)
														</label>
														<span class="inline cost"><?php echo $location['total_cost_formatted']; ?></span>
													</div>
												<?php } ?>
											<?php } else { ?>
												<strong><?php echo $mm_checkout_shipping_not_available; ?></strong>

												<input type="hidden" name="no_shipping_method[<?php echo $product['product_id']; ?>]" value="0" />
											<?php } ?>
										</div>
									</td>
									<td class="col-sm-3"></td>
								</tr>
							<?php } ?>
						<?php } ?>

						<?php if(isset($shipping_rules['combined_shipping']) && !empty($shipping_rules['combined_shipping'])) { ?>
							<!-- Products on which combined shipping is applied -->

							<?php foreach($shipping_rules['combined_shipping'] as $product) { ?>
								<tr>

									<td class="col-sm-4">
										<div class="product-info">
											<div>
												<strong><?php echo $product['name']; ?></strong>
											</div>
											<div>
												<ul>
													<?php foreach($product['options'] as $option) { ?>
														<li><?php echo $option['name'] . ': ' . $option['value']; ?></li>
													<?php } ?>
												</ul>
											</div>
											<div>
												<span><?php echo $mm_checkout_shipping_products_price; ?></span>
												<?php echo $product['price_formatted']; ?>
											</div>
											<div>
												<span><?php echo $mm_checkout_shipping_products_quantity; ?></span>
												<span><?php echo $product['quantity']; ?></span>
											</div>
										</div>
									</td>

									<?php if($product === reset($shipping_rules['combined_shipping'])) { ?>
										<td class="col-sm-4 sm-select" rowspan="<?php echo count($shipping_rules['combined_shipping']); ?>">
											<div class="shipping-method-select" style="padding: 20px 0 0 20px;">
												<?php if(isset($shipping_rules['seller_combined_shipping_methods'])) { ?>
													<strong><?php echo $mm_checkout_shipping_method_title_shot; ?></strong>

													<?php foreach($shipping_rules['seller_combined_shipping_methods'] as $method) { ?>
														<div class="radio">
															<label>
																<?php if (isset($this->session->data['ms_cart_product_shipping']['combined'][$product['product_id']]['shipping_method_id'])) { ?>
																	<input type="radio" name="combined_shipping_method[<?php echo $seller_id; ?>]" value="<?php echo $method['seller_shipping_location_id'] . '-' . $shipping_rules['combined_product_ids']; ?>" <?php echo ((int)$method['seller_shipping_location_id'] === (int)$this->session->data['ms_cart_product_shipping']['combined'][$product['product_id']]['location_id']) ? 'checked="checked"' : ''; ?> />
																<?php } else { ?>
																	<input type="radio" name="combined_shipping_method[<?php echo $seller_id; ?>]" value="<?php echo $method['seller_shipping_location_id'] . '-' . $shipping_rules['combined_product_ids']; ?>" <?php echo $method === reset($shipping_rules['seller_combined_shipping_methods']) ? 'checked="checked"' : ''; ?> />
																<?php } ?>

																<?php echo $method['shipping_method_name']; ?> (<?php echo $method['delivery_time_name']; ?>)
															</label>
															<span class="inline cost"><?php echo $method['total_cost_formatted']; ?></span>
														</div>
													<?php } ?>
												<?php } else if(isset($shipping_rules['combined_products_maxweight_exceeded'])) { ?>
													<strong><?php echo $shipping_rules['combined_products_maxweight_exceeded']; ?></strong>
												<?php } else if(isset($shipping_rules['combined_products_minweight_not_exceeded'])) { ?>
													<strong><?php echo $shipping_rules['combined_products_minweight_not_exceeded']; ?></strong>
												<?php } else { ?>
													<strong><?php echo $mm_checkout_shipping_not_available; ?></strong>

													<input type="hidden" name="no_shipping_method[<?php echo $product['product_id']; ?>]" value="0" />
												<?php } ?>
											</div>
										</td>
									<?php } ?>

									<td class="col-sm-3"></td>
								</tr>
							<?php } ?>
						<?php } ?>

						<?php if(isset($shipping_rules['digital']) && !empty($shipping_rules['digital'])) { ?>
							<!-- Products that don't require shipping (digital products) -->

							<?php foreach($shipping_rules['digital'] as $product) { ?>
								<tr>

									<td class="col-sm-4">
										<div class="product-info">
											<div>
												<strong><?php echo $product['name']; ?></strong>
											</div>
											<div>
												<ul>
													<?php foreach($product['options'] as $option) { ?>
														<li><?php echo $option['name'] . ': ' . $option['value']; ?></li>
													<?php } ?>
												</ul>
											</div>
											<div>
												<span><?php echo $mm_checkout_shipping_products_price; ?></span>
												<?php echo $product['price_formatted']; ?>
											</div>
											<div>
												<span><?php echo $mm_checkout_shipping_products_quantity; ?></span>
												<span><?php echo $product['quantity']; ?></span>
											</div>
										</div>
									</td>

									<td class="col-sm-4 sm-select">
										<div class="shipping-method-select">
											<strong><?php echo $mm_checkout_shipping_digital_products; ?></strong>
											<input type="hidden" name="digital[<?php echo $product['product_id']; ?>]" value="1" />
										</div>
									</td>

									<td class="col-sm-3"></td>
								</tr>
							<?php } ?>
						<?php } ?>

						<!-- Product not available for shipping -->
						<?php if(isset($shipping_rules['no_shipping']) && !empty($shipping_rules['no_shipping'])) { ?>
							<?php foreach($shipping_rules['no_shipping'] as $product) { ?>
								<tr>

									<td class="col-sm-4">
										<div class="product-info">
											<div>
												<strong><?php echo $product['name']; ?></strong>
											</div>
											<div>
												<ul>
													<?php foreach($product['options'] as $option) { ?>
														<li><?php echo $option['name'] . ': ' . $option['value']; ?></li>
													<?php } ?>
												</ul>
											</div>
											<div>
												<span><?php echo $mm_checkout_shipping_products_price; ?></span>
												<?php echo $product['price_formatted']; ?>
											</div>
											<div>
												<span><?php echo $mm_checkout_shipping_products_quantity; ?></span>
												<span><?php echo $product['quantity']; ?></span>
											</div>
										</div>
									</td>
									<td class="col-sm-4 sm-select">
										<div class="shipping-method-select">
											<input type="hidden" name="no_shipping_method[<?php echo $product['product_id']; ?>]" value="0" />
											<strong><?php echo $mm_checkout_shipping_not_available; ?></strong>
										</div>
									</td>
									<td class="col-sm-3"></td>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				<?php } ?>
			</table>
		</div>
	<?php } ?>
</div>