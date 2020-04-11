<?php echo $header; ?>
<div class="container ms-catalog-seller-profile">
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
		<div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
			<div class="row">

				<!-- banner -->
				<div class="top-banner <?php echo $class; ?>">
					<?php if ($this->config->get('msconf_enable_seller_banner') && isset($seller['banner'])) { ?>
						<img src="<?php echo $seller['banner']; ?>" title="<?php echo $seller['nickname']; ?>" alt="<?php echo $seller['nickname']; ?>" /></a>
					<?php } ?>
				</div>

				<!-- left column -->
				<?php if ($column_left && $column_right) { ?>
				<?php $class = 'col-sm-6'; ?>
				<?php } elseif ($column_left || $column_right) { ?>
				<?php $class = 'col-sm-6'; ?>
				<?php } else { ?>
				<?php $class = 'col-sm-8'; ?>
				<?php } ?>
				<div class="<?php echo $class; ?> seller-data">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab-description" data-toggle="tab"><?php echo $tab_description; ?></a></li>
						<?php if($this->config->get('msconf_reviews_enable')) { ?>
							<li><a href="#tab-review" data-toggle="tab"><?php echo sprintf($this->language->get('tab_review'), $total_reviews); ?></a></li>
						<?php } ?>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="tab-description">
                            <?php if (!empty(trim($seller['description']))) { ?>
                            <div class="seller-description">
								<?php echo $seller['description'] ;?>
								<hr>
							</div>
                            <?php } ?>
							<?php if (!empty($seller['products'])) { ?>
								<div class="mm_head">
									<h3><?php echo $ms_catalog_seller_profile_featured_products ;?></h3>
									<div id="search" style="display: none" class="input-group">
										<form action="index.php" method="get">
											<input type="hidden" name="route" value="seller/catalog-seller/products">
											<input type="hidden" name="seller_id" value="<?php echo $seller['seller_id'] ;?>">
											<input type="text" name="search" value="" placeholder="<?php echo $ms_catalog_seller_profile_search ;?>" class="form-control input-lg">
										<span class="input-group-btn">
											<button class="btn btn-default btn-lg"><i class="fa fa-search"></i></button>
										</span>
										</form>
									</div>
									<div class="cl"></div>
								</div>
								<div class="row">
									<?php foreach ($seller['products'] as $product) { ?>
									<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
										<div class="product-thumb transition">
											<div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a></div>
											<div class="caption">
												<h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
											</div>
											<div class="button-group">
												<a href="<?php echo $product['href']; ?>"><button type="button" class="btn btn-main btn-block"><span><?php echo $ms_view; ?></span></button></a>
											</div>
										</div>
									</div>
									<?php } ?>
								</div>
							<?php } ?>
							<!-- end products -->
						</div>

						<?php if($this->config->get('msconf_reviews_enable')) { ?>
							<div class="tab-pane" id="tab-review">
								<input type="hidden" id="total_reviews" value="<?php echo $total_reviews ;?>">

								<?php if($total_reviews > 0) { ?>
									<h3><?php echo $mm_review_comments_title ;?></h3>

									<div class="review-stars">
										<div class="row">
											<div class="col-sm-6 col-xs-12">
												<div class="review-stars-top">
													<div class="ms-ratings main">
														<div class="ms-empty-stars"></div>
														<div class="ms-full-stars" style="width: <?php echo $avg_rating * 20; ?>%"></div>
													</div>
													<span class="rating-summary"><?php echo sprintf($this->language->get('mm_review_rating_summary'), $avg_rating, $total_reviews, $total_reviews == 1 ? $this->language->get('mm_review_rating_review') : $this->language->get('mm_review_rating_reviews')); ?></span>
												</div>

												<div class="rating-stats">
													<?php foreach($rating_stats as $star => $info) { ?>
													<div class="rating-row">
														<span><?php echo sprintf($mm_review_stats_stars, $star) ;?></span>
														<div class="progress">
															<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $info['percentage'] ;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $info['percentage'] ;?>%;"></div>
														</div>
														<span><?php echo $info['votes'] ;?></span>
													</div>
													<?php } ?>
												</div>
											</div>

											<div class="col-sm-6 col-xs-12">
												<h5><?php echo $mm_review_seller_profile_history ;?></h5>
												<div class="review-history table-responsive">
													<table class="table table-condensed">
														<thead>
														<tr>
															<th></th>
															<th><?php echo $mm_review_one_month ;?></th>
															<th><?php echo $mm_review_three_months ;?></th>
															<th><?php echo $mm_review_six_months ;?></th>
															<th><?php echo $mm_review_twelve_months ;?></th>
														</tr>
														</thead>
														<tbody>
															<?php foreach($feedback_history as $key => $history) { ?>
																<tr>
																	<td><?php echo $key == 'positive' ? $mm_review_seller_profile_history_positive : ( $key == 'neutral' ? $mm_review_seller_profile_history_neutral : $mm_review_seller_profile_history_negative) ;?></td>
																	<td><?php echo $history['one_month'] ;?></td>
																	<td><?php echo $history['three_months'] ;?></td>
																	<td><?php echo $history['six_months'] ;?></td>
																	<td><?php echo $history['twelve_months'] ;?></td>
																</tr>
															<?php } ?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>

									<div class="cl"></div>

									<div class="data-container reviews">
										<?php foreach($reviews as $review) { ?>
											<div class="review">
												<input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>" />
												<input type="hidden" name="total_review_comments" value="<?php echo $review['total_comments']; ?>">

												<div class="review-header">
													<span class="review-author-name"><?php echo isset($review['author']['firstname']) ? $review['author']['firstname'] : $this->language->get('ms_questions_customer_deleted'); ?></span>
													<span class="review-date"><?php echo $ms_on . ' ' . $review['date_created']; ?></span>
													<div class="ms-ratings comments">
														<div class="ms-empty-stars"></div>
														<div class="ms-full-stars" style="width: <?php echo $review['rating'] * 20; ?>%"></div>
													</div>
													<a href="<?php echo $review['product']['href']; ?>" class="product-name"><?php echo $review['product']['name']; ?><span class="prod-price"><?php echo $review['product']['price']; ?></span></a>
												</div>
												<div class="review-body">
													<p><?php echo $review['comment']; ?></p>

													<?php if(strlen(preg_replace('/[^a-zA-Z]/', '', $review['comment'])) > 700) { ?>
														<p class="read-more">
															<a href="#" role="button"><i class="fa fa-angle-double-down" aria-hidden="true"></i> <?php echo $this->language->get('ms_expand'); ?></a>
														</p>
														<p class="read-less">
															<a href="#" role="button"><i class="fa fa-angle-double-up" aria-hidden="true"></i> <?php echo $this->language->get('ms_collapse'); ?></a>
														</p>
													<?php } ?>
												</div>
												<div class="review-footer">
													<ul class="review-thumbnails">
														<?php foreach($review['attachments'] as $attachment) { ?>
															<li class="image-additional"><a class="thumbnail" href="<?php echo $attachment['fullsize']; ?>" title="Attachment"><img class="review-img" src="<?php echo $attachment['thumb']; ?>"/></a></li>
														<?php } ?>
													</ul>
												</div>
												<div class="review-comments expanded"></div>
											</div>
										<?php } ?>
									</div>

									<div id="reviews-pag"></div>
								<?php } else { ?>
									<h3><?php echo $mm_review_no_reviews ;?></h3>
								<?php } ?>
							</div>
						<?php } ?>
					</div>

					<?php if ($this->config->get('mxtconf_disqus_enable') == 1) { ?>
					<!-- mm catalog seller profile disqus comments start -->
					<div class="row">
						<div class="col-xs-12">
							<h3><?php echo $mxt_disqus_comments ?></h3>
							<div class="tab-pane" id="tab-disqus-comments">
								<div id="disqus_thread"></div>
								<script>
								/**
								* RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
								* LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables
								*/

								var disqus_config = function () {
								this.page.url = '<?php echo $disqus_url; ?>';
								this.page.identifier = '<?php echo $disqus_identifier; ?>';
								};

								(function() { // DON'T EDIT BELOW THIS LINE
								var d = document, s = d.createElement('script');

								s.src = '//<?php echo $this->config->get('mxtconf_disqus_shortname') ?>.disqus.com/embed.js';

								s.setAttribute('data-timestamp', +new Date());
								(d.head || d.body).appendChild(s);
								})();
								</script>
								<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>
							</div>
						</div>
					</div>
					<!-- mm catalog seller profile disqus comments end -->
					<?php } ?>

					<?php if ($this->config->get('mxtconf_ga_seller_enable') == 1) { ?>
					<!-- mm catalog seller profile google analytics code start -->
					<script>
					  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

					  ga('create', '<?php echo $seller['settings']['slr_ga_tracking_id'] ?>', 'auto');
					  ga('send', 'pageview');
					</script>
					<!-- mm catalog seller profile google analytics code end -->
					<?php } ?>
				</div>

				<!-- right column -->
				<?php if ($column_left && $column_right) { ?>
				<?php $class = 'col-sm-6'; ?>
				<?php } elseif ($column_left || $column_right) { ?>
				<?php $class = 'col-sm-6'; ?>
				<?php } else { ?>
				<?php $class = 'col-sm-4'; ?>
				<?php } ?>
				<div class="<?php echo $class; ?>">
					<!-- mm catalog seller profile avatar block start -->
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
							<span><?php echo $ms_catalog_seller_profile_view_products; ?></span>
						</a>
					</div>
					<!-- mm catalog seller profile avatar block end -->

					<!-- mm catalog seller profile info block start -->
					<div class="mm_box mm_info">
						<ul class="mm_stats">
							<li><b><?php echo $ms_account_member_since ;?></b> <?php echo $seller['created'] ;?></li>
							<li><b><?php echo $ms_catalog_seller_profile_total_sales ;?>:</b> <?php echo $seller['total_sales'] ;?></li>
							<li><b><?php echo $ms_catalog_seller_profile_total_products ;?>: </b><?php echo $seller['total_products'] ;?></li>
							<?php if($this->config->get('msconf_reviews_enable')) { ?>
								<li>
									<b class="profile-rating"><?php echo $ms_catalog_seller_profile_rating ;?>: </b>
									<div class="ms-ratings main">
										<div class="ms-empty-stars"></div>
										<div class="ms-full-stars" style="width: <?php echo $avg_rating * 20; ?>%"></div>
									</div>
									<span><?php echo sprintf($this->data['ms_catalog_seller_profile_total_reviews'], $total_reviews, $total_reviews == 1 ? $this->language->get('mm_review_rating_review') : $this->language->get('mm_review_rating_reviews')); ?></span>
								</li>
							<?php } ?>
						</ul>
					</div>
					<!-- mm catalog seller profile info block end -->

					<!-- mm catalog seller profile badges start -->
					<?php if($this->config->get('msconf_badge_enabled') && isset($seller['badges']) && !empty($seller['badges'])) :?>
						<div class='mm_box mm_badges'>
							<?php foreach($seller['badges'] as $badge) { ?>
								<img src="<?php echo $badge['image']; ?>" title="<?php echo $badge['description']; ?>" />
							<?php } ?>
						</div>
					<?php endif; ?>
					<!-- mm catalog seller profile badges end -->

					<!-- mm catalog seller profile social start -->
					<?php if ($this->config->get('msconf_sl_status') && !empty($seller['social_links'])) { ?>
						<div class='mm_box mm_social_holder'>
							<div class="ms-social-links">
								<ul>
									<?php foreach($seller['social_links'] as $link) { ?>
										<?php if($this->MsLoader->MsHelper->isValidUrl($link['channel_value'])) { ?>
											<li><a target="_blank" href="<?php echo $this->MsLoader->MsHelper->addScheme($link['channel_value']); ?>"><img src="<?php echo $link['image']; ?>" /></a></li>
										<?php } ?>
									<?php } ?>
								</ul>
							</div>
						</div>
					<?php } ?>
					<!-- mm catalog seller profile social end -->

					<!-- mm catalog seller profile messaging start -->
					<?php if ($this->config->get('mmess_conf_enable')) { ?>
						<?php if ((!$this->customer->getId()) || ($this->customer->getId() != $seller['seller_id'])) { ?>
							<?php echo $contactForm; ?>
							<div class="mm_box mm_messages">
								<div class="contact">
									<?php if ($this->customer->getId()) { ?>
										<div class="button-group">
										<button type="button" class="btn btn-default btn-block ms-sellercontact" data-toggle="modal" data-target="#contactDialog"><span><?php echo $ms_catalog_product_contact; ?></span></button>
									</div>
									<?php } else { ?>
										<?php echo sprintf($this->language->get('ms_sellercontact_signin'), $this->url->link('account/login', '', 'SSL'), $seller['nickname']); ?>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
					<!-- mm catalog seller profile messaging end -->
				</div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<?php echo $footer; ?>