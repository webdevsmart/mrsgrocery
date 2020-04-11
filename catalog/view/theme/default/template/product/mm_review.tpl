<input type="hidden" id="total_reviews" value="<?php echo $total_reviews ;?>">
<?php if($total_reviews > 0) { ?>
	<h3><?php echo $mm_review_comments_title ;?></h3>

	<div class="review-stars row">
		<div class="col-sm-6 col-xs-12 rating-stats">
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

		<div class="col-sm-6 col-xs-12 review-stars-top product-page">
			<div class="ms-ratings main">
				<div class="ms-empty-stars"></div>
				<div class="ms-full-stars" style="width: <?php echo $avg_rating * 20; ?>%"></div>
			</div>
			<span class="rating-summary"><?php echo sprintf($this->language->get('mm_review_rating_summary'), round($avg_rating, 1), $total_reviews, $total_reviews == 1 ? $this->language->get('mm_review_rating_review') : $this->language->get('mm_review_rating_reviews')); ?></span>
		</div>
	</div>

	<div class="cl"></div>

	<div class="data-container reviews">
		<?php foreach($reviews as $review) { ?>
			<div class="review">
				<input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>" />
				<input type="hidden" name="total_review_comments" value="<?php echo $review['total_comments']; ?>">

				<div class="review-header">
					<div style="display:block;">
						<span class="review-author-name"><?php echo isset($review['author']['firstname']) ? $review['author']['firstname'] : $this->language->get('ms_questions_customer_deleted'); ?></span>
						<span class="review-date"><?php echo $ms_on . ' ' . $review['date_created']; ?></span>
					</div>
					<div class="ms-ratings comments">
						<div class="ms-empty-stars"></div>
						<div class="ms-full-stars" style="width: <?php echo $review['rating'] * 20; ?>%"></div>
					</div>
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
