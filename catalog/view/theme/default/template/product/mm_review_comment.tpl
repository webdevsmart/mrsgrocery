<div class="row">
	<div class="col-sm-1"></div>
	<div class="data-container col-sm-11">
		<?php if (!empty($comments)) { ?>
			<?php foreach($comments as $comment) { ?>
				<div class="review-comment">
					<input type="hidden" name="review_comment_id" value="<?php echo $comment['comment_id']; ?>" />
					<div class="header">
						<?php echo $mm_review_seller_response; ?>
						<span class="date"><?php echo $comment['date_created']; ?></span>
					</div>
					<div class="body">
						<?php echo nl2br($comment['text']); ?>
					</div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<div style="font-size: 14px;">
				<?php echo $mm_review_no_comments; ?>
			</div>
		<?php } ?>
	</div>

	<div class="col-sm-11" id="review-comments-pag-<?php echo $review['review_id']; ?>" style="float: right;"></div>
</div>

<?php if($this->MsLoader->MsProduct->productOwnedBySeller($review['product_id'], $this->customer->getId()) && empty($comments)) { ?>
	<div class="row" style="margin: 10px -15px;">
		<?php if(!$is_logged) { ?>
			<div class="col-sm-1"></div>
			<div class="col-sm-11" style="font-size: 14px;">
				<?php echo $mm_review_comments_error_signin; ?>
			</div>
		<?php } else { ?>
			<form id="ms-review-comment-form" class="ms-form form-horizontal">
				<div class="col-sm-1"></div>
				<div class="col-sm-11">
					<textarea class="form-control" rows="3" cols="50" name="text" placeholder="<?php echo $mm_review_comments_textarea_placeholder; ?>"></textarea>
					<div class="buttons text-left">
						<button type="button" class="btn btn-primary" id="comment-btn"><?php echo $mm_review_comments_post_message; ?></button>
					</div>
				</div>
			</form>
		<?php } ?>
	</div>
<?php } ?>
