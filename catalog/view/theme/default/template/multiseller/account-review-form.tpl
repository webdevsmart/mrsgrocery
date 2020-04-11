<?php echo $header; ?>
<div class="container">

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
			<div class="mm_dashboard review">
				<h1><?php echo $heading; ?></h1>

				<form id="ms-new-review" class="tab-content ms-review form-horizontal">
					<input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>" />

					<fieldset id="mm_general">
						<legend><?php echo $ms_account_editreview_review; ?></legend>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_account_editreview_product; ?></label>
							<div class="col-sm-10">
								<a href="<?php echo $this->url->link('product/product', 'product_id=' . $review['product_id']); ?>" target="_blank"><?php echo $review['product_name']; ?></a>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_account_editreview_order; ?></label>
							<div class="col-sm-10">
								<a href="<?php echo $this->url->link('seller/account-order/viewOrder', 'order_id=' . $review['order_id']); ?>" target="_blank">#<?php echo $full_order_id; ?></a>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_account_editreview_customer; ?></label>
							<div class="col-sm-10">
								<p><?php echo $review_customer; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_account_editreview_review; ?></label>
							<div class="col-sm-10">
								<p><?php echo $review['comment']; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_account_editreview_rating; ?></label>
							<div class="col-sm-10">
								<div class="ms-ratings main">
									<div class="ms-empty-stars"></div>
									<div class="ms-full-stars" style="width: <?php echo $review['rating'] * 20; ?>%;"></div>
								</div>
							</div>
						</div>
					</fieldset>

					<?php if ($review['attachments']) { ?>
						<fieldset id="mm_images">
							<legend><?php echo $ms_account_editreview_customer_images; ?></legend>

							<div class="form-group">
								<label class="col-sm-2 control-label"><?php echo $ms_account_editreview_images; ?></label>
								<div class="col-sm-10">
									<ul class="thumbnails">
										<?php foreach ($review['attachments'] as $attachment) { ?>
											<li class="image-additional"><a class="thumbnail" href="<?php echo $attachment['fullsize']; ?>" title="<?php echo $ms_account_editreview_customer_images; ?>"> <img src="<?php echo $attachment['thumb']; ?>" title="<?php echo $ms_account_editreview_customer_images; ?>" alt="<?php echo $ms_account_editreview_customer_images; ?>" /></a></li>
										<?php } ?>
									</ul>
								</div>
							</div>
						</fieldset>
					<?php } ?>

					<fieldset id="mm_comments">
						<legend><?php echo $ms_account_editreview_your_response; ?></legend>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_account_editreview_response; ?></label>
							<div class="col-sm-10 review-comments"></div>
						</div>
					</fieldset>
				</form>

				<div class="buttons">
					<div class="pull-left"><a class="btn btn-default" href="<?php echo $back; ?>"><span><?php echo $ms_button_back; ?></span></a></div>
				</div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<?php echo $footer; ?>
