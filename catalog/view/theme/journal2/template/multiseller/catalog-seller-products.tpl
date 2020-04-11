<?php echo $header; ?>
<div class="container ms-catalog-seller-products">
	<ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li> <a href="<?php echo $breadcrumb['href']; ?>"> <?php echo $breadcrumb['text']; ?> </a> </li>
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


		<aside id="column-left" class="col-sm-3 hidden-xs">
			<!-- mm catalog seller products avatar block start -->
			<div class="mm_box mm_description">
				<div class="info-box">
					<a class="avatar-box thumbnail" href="<?php echo $seller['href']; ?>"><img src="<?php echo $seller['thumb']; ?>" /></a>
					<div>
						<ul class="list-unstyled">
							<li><h3 class="sellersname"><?php echo $seller['nickname']; ?></h3></li>
							<li><?php echo $seller['settings']['slr_company'] ;?></li>
							<li><a target="_blank" href="<?php echo $seller['settings']['slr_website'] ;?>"><?php echo $seller['settings']['slr_website'] ;?></a></li>
							<li><?php echo trim($seller['settings']['slr_city'] . ', ' . $seller['settings']['slr_country'], ',') ;?></li>
						</ul>
					</div>
				</div>
				<a href="<?php echo $seller['href']; ?>" class="btn btn-default btn-block" style="clear: both">
					<span><?php echo $ms_catalog_seller_profile; ?></span>
				</a>
			</div>
			<!-- mm catalog seller products avatar block end -->
			
			<div class="list-group">
				<a href="<?php echo $this->url->link('seller/catalog-seller/products', 'seller_id=' . $this->request->get['seller_id']); ?>" class="list-group-item"><b><?php echo $ms_all_products; ?></b></a>

				<?php foreach($seller['ms_categories'] as $category) { ?>
					<a href="<?php echo $category['href'] ;?>" class="list-group-item <?php echo isset($this->request->get['ms_category_id']) && $category['category_id'] == $this->request->get['ms_category_id'] ? 'active' : ''; ?>"><?php echo $category['name'] ;?><span class="badge"><?php echo $category['total'] ;?></span></a>
					<?php if(isset($category['childs'])) { ?>
						<?php foreach($category['childs'] as $child) { ?>
							<a href="<?php echo $child['href']; ?>" class="list-group-item <?php echo isset($this->request->get['ms_category_id']) && $child['category_id'] == $this->request->get['ms_category_id'] ? 'active' : ''; ?>">&nbsp;&nbsp;&nbsp;- <?php echo $child['name']; ?><span class="badge"><?php echo $child['total'] ;?></span></a>
							<?php if(isset($child['childs'])) { ?>
								<?php foreach($child['childs'] as $child_2) { ?>
									<a href="<?php echo $child_2['href']; ?>" class="list-group-item <?php echo isset($this->request->get['ms_category_id']) && $child_2['category_id'] == $this->request->get['ms_category_id'] ? 'active' : ''; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <?php echo $child_2['name']; ?><span class="badge"><?php echo $child_2['total'] ;?></span></a>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			</div>

		</aside>
		<div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
			<?php if (!empty($seller['products'])) { ?>
				<div class="row">
					<div class="product-filter">
						<div class="limit"><b><?php echo $text_limit; ?></b>
						  <select onchange="location = this.value;">
							<?php foreach ($limits as $limits) { ?>
							<?php if ($limits['value'] == $limit) { ?>
							<option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
							<?php } else { ?>
							<option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
							<?php } ?>
							<?php } ?>
						  </select>
						</div>
						<div class="sort"><b><?php echo $text_sort; ?></b>
						  <select onchange="location = this.value;">
							<?php foreach ($sorts as $sorts) { ?>
							<?php if ($sorts['value'] == $sort . '-' . $order) { ?>
							<option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
							<?php } else { ?>
							<option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
							<?php } ?>
							<?php } ?>
						  </select>
						</div>
					</div>
					<div class="cl"></div>
				</div>
			   <div class="row main-products product-grid" data-grid-classes="<?php echo $this->journal2->settings->get('product_grid_classes'); ?> display-<?php echo $this->journal2->settings->get('product_grid_wishlist_icon_display'); ?> <?php echo $this->journal2->settings->get('product_grid_button_block_button'); ?>">
					<?php foreach ($seller['products'] as $product) { ?>
					<div class="product-grid-item <?php echo $this->journal2->settings->get('product_grid_classes'); ?>">
						<div class="product-thumb product-wrapper <?php echo isset($product['labels']) && is_array($product['labels']) && isset($product['labels']['outofstock']) ? 'outofstock' : ''; ?>">
							<div class="image <?php echo $this->journal2->settings->get('show_countdown', 'never') !== 'never' && isset($product['date_end']) && $product['date_end'] ? 'has-countdown' : ''; ?>">
								<a href="<?php echo $product['href']; ?>" <?php if(isset($product['thumb2']) && $product['thumb2']): ?> class="has-second-image" style="background: url('<?php echo $product['thumb2']; ?>') no-repeat;" <?php endif; ?>>
								<img class="lazy first-image" width="<?php echo $this->journal2->settings->get('config_image_width'); ?>" height="<?php echo $this->journal2->settings->get('config_image_height'); ?>" src="<?php echo $this->journal2->settings->get('product_dummy_image'); ?>" data-src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
								</a>
								<?php if (isset($product['labels']) && is_array($product['labels'])): ?>
								<?php foreach ($product['labels'] as $label => $name): ?>
								<span class="label-<?php echo $label; ?>"><b><?php echo $name; ?></b></span>
								<?php endforeach; ?>
								<?php endif; ?>
								<?php if($this->journal2->settings->get('product_grid_wishlist_icon_position') === 'image' && $this->journal2->settings->get('product_grid_wishlist_icon_display', '') === 'icon'): ?>
								<div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_wishlist; ?>"><i class="wishlist-icon"></i><span class="button-wishlist-text"><?php echo $button_wishlist;?></span></a></div>
								<div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_compare; ?>"><i class="compare-icon"></i><span class="button-compare-text"><?php echo $button_compare;?></span></a></div>
								<?php endif; ?>
							</div>
							<div class="product-details">
								<div class="caption">
									<h4 class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
									<p class="description"><?php echo $product['description']; ?></p>
									<?php if ($product['rating']) { ?>
									<div class="rating">
										<?php for ($i = 1; $i <= 5; $i++) { ?>
										<?php if ($product['rating'] < $i) { ?>
										<span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
										<?php } else { ?>
										<span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>
										<?php } ?>
										<?php } ?>
									</div>
									<?php } ?>
									<?php if ($product['price']) { ?>
									<p class="price">
										<?php if (!$product['special']) { ?>
										<?php echo $product['price']; ?>
										<?php } else { ?>
										<span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new" <?php echo isset($product['date_end']) && $product['date_end'] ? "data-end-date='{$product['date_end']}'" : ""; ?>><?php echo $product['special']; ?></span>
										<?php } ?>
										<?php if ($product['tax']) { ?>
										<span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
										<?php } ?>
									</p>
									<?php } ?>
								</div>
								<div class="button-group">
									<?php if (Journal2Utils::isEnquiryProduct($this, $product)): ?>
									<div class="cart enquiry-button">
										<a href="javascript:Journal.openPopup('<?php echo $this->journal2->settings->get('enquiry_popup_code'); ?>', '<?php echo $product['product_id']; ?>');" data-clk="addToCart('<?php echo $product['product_id']; ?>');" class="button hint--top" data-hint="<?php echo $this->journal2->settings->get('enquiry_button_text'); ?>"><?php echo $this->journal2->settings->get('enquiry_button_icon') . '<span class="button-cart-text">' . $this->journal2->settings->get('enquiry_button_text') . '</span>'; ?></a>
									</div>
									<?php else: ?>
									<div class="cart <?php echo isset($product['labels']) && is_array($product['labels']) && isset($product['labels']['outofstock']) ? 'outofstock' : ''; ?>">
										<a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button hint--top" data-hint="<?php echo $button_cart; ?>"><i class="button-left-icon"></i><span class="button-cart-text"><?php echo $button_cart; ?></span><i class="button-right-icon"></i></a>
									</div>
									<?php endif; ?>
									<div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_wishlist; ?>"><i class="wishlist-icon"></i><span class="button-wishlist-text"><?php echo $button_wishlist;?></span></a></div>
									<div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_compare; ?>"><i class="compare-icon"></i><span class="button-compare-text"><?php echo $button_compare;?></span></a></div>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="row">
					<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
					<div class="col-sm-6 text-right"><?php echo $results; ?></div>
				</div>
			<?php } else { ?>
				<p><?php echo $ms_catalog_seller_products_empty; ?></p>
			<?php } ?>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<?php echo $footer; ?>