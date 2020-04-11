<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ms-review-form">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="<?php echo $this->url->link('multimerch/review', 'token=' . $this->session->data['token']); ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>

			<h1><?php echo $heading; ?></h1>

			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<div style="display: none" class="alert alert-danger" id="error-holder"><i class="fa fa-exclamation-circle"></i>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading; ?></h3>
			</div>

			<div class="panel-body">
				<form id="ms-review-form" class="form-horizontal">
					<input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>" />

					<fieldset id="mm_general">
						<legend><?php echo $ms_review_general; ?></legend>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_review_edit_product; ?></label>
							<div class="col-sm-10">
								<a href="<?php echo $this->url->link('catalog/product/edit', 'product_id=' . $review['product_id'] . '&token=' . $this->session->data['token']); ?>" target="_blank"><?php echo $review['product_name']; ?></a>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_review_edit_order; ?></label>
							<div class="col-sm-10">
								<a href="<?php echo $this->url->link('sale/order/info', 'order_id=' . $review['order_id'] . '&token=' . $this->session->data['token']); ?>" target="_blank">#<?php echo $full_order_id; ?></a>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_review_edit_customer; ?></label>
							<div class="col-sm-10">
								<a href="<?php echo $this->url->link('customer/customer/edit', 'customer_id=' . $review['author_id'] . '&token=' . $this->session->data['token']); ?>" target="_blank"><?php echo $customer; ?></a>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_review_edit_review; ?></label>
							<div class="col-sm-10">
								<p><?php echo $review['comment']; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_review_edit_rating; ?></label>
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
							<legend><?php echo $ms_review_edit_customer_images; ?></legend>

							<div class="form-group">
								<label class="col-sm-2 control-label"><?php echo $ms_review_edit_images; ?></label>
								<div class="col-sm-10">
									<ul class="attachments list-group" style="list-style: none; padding-left: 0;">
										<?php foreach ($review['attachments'] as $review_attachment_id => $attachment) { ?>
										<li class="list-group-item">
											<input type="hidden" name="review_attachment_id" value="<?php echo $review_attachment_id; ?>" />
											<a href="<?php echo $this->url->link('multimerch/review/jxDownloadAttachment', 'filename=' . $attachment['attachment'] . '&token=' . $this->session->data['token'], true); ?>"><i class="fa fa-file-o" aria-hidden="true"></i> <?php echo basename($attachment['attachment']); ?></a>
											<span class="ms-remove-image"><i class="fa fa-times"></i></span>
										</li>
										<?php } ?>
									</ul>
								</div>
							</div>
						</fieldset>
					<?php } ?>

					<fieldset id="mm_comments">
						<legend><?php echo $ms_review_edit_seller_response; ?></legend>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_review_edit_response; ?></label>
							<div class="col-sm-10 review-comments">
								<div class="row">
									<div class="data-container col-sm-12">
										<?php if (!empty($review['comments'])) { ?>
											<?php foreach($review['comments'] as $comment) { ?>
												<div class="review-comment">
													<input type="hidden" name="review_comment_id" value="<?php echo $comment['comment_id']; ?>" />
													<div class="header">
														<?php echo ucwords($comment['author']); ?>
														<span class="date"><?php echo $comment['date_created']; ?></span>
														<span class="ms-remove-comment"><i class="fa fa-times"></i></span>
													</div>
													<div class="body">
														<?php echo nl2br($comment['text']); ?>
													</div>
												</div>
											<?php } ?>
										<?php } else { ?>
											<div style="font-size: 14px;">
												<?php echo $ms_review_no_comments; ?>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>

	<style>

	</style>

	<script>
		var msGlobals = {
			token : "<?php echo $this->session->data['token']; ?>"
		};
	</script>
</div>
<?php echo $footer; ?>