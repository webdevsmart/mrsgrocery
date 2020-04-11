// Get seller reviews
$(function() {
	// Load reviews container at the product page. Reviews at the seller's profile page are loaded automatically
	if ($("#ms_product_reviews").length > 0) {
		var product_id = $(document).find('input[name="product_id"]').val();
		$("#ms_product_reviews").load("index.php?route=multimerch/product_review&product_id=" + product_id);
	}

	// Create pagination for reviews
	setTimeout(function() {
		var reviews = [];
		$.each($('.review') || $(), function (key, review) {
			reviews.push($(review)[0].outerHTML);
		});

		if($('#reviews-pag').length > 0)
			createPagination('#reviews-pag', reviews);

		setTimeout(function() {
			popupImages();
			toggleComments();
		});
	}, 1500);

	function createPagination(name, sources) {
		var container = $(name);

		container.pagination({
			dataSource: sources,
			pageSize: 5,
			className: 'paginationjs-theme-blue paginationjs-small',
			hideWhenLessThanOnePage: true,
			showPrevious: false,
			showNext: false,
			callback: function(response){
				container.prev().html(response);

				// Call again here for correctly working popups and replies
				popupImages();
				toggleComments();
			}
		});

		return container;
	}

	$(document).on('click', '.review .read-more a', function() {
		var totalHeight = 0;

		var $p  = $(this).parent();
		var $up = $p.closest('div');
		var $ps = $up.find("p:not('.read-more')");

		$ps.each(function() {
			totalHeight += $(this).outerHeight();
		});

		$up.css({
			"height": $up.height(),
			"max-height": 9999
		}).animate({
			"height": totalHeight
		});

		$p.fadeOut();
		$p.siblings('.read-less').fadeIn();

		return false;
	 });

	$(document).on('click', '.review .read-less a', function() {
		var $p  = $(this).parent();
		var $up = $p.closest('div');

		$up.css({
			"height": $up.height()
		}).animate({
			"height": 140
		});

		$p.fadeOut();
		$p.siblings('.read-more').fadeIn();

		return false;
	});

	function toggleComments() {
		$.each($('.review'), function(key, item) {
			var $review = $(item);
			var $review_comments = $review.find('.review-comments');
			var review_id = $review.find('input[name="review_id"]').val();
			var total_comments = $review.find('input[name="total_review_comments"]').val();

			if(total_comments > 0) {
				$review_comments.load('index.php?route=multimerch/product_review/jxGetReviewComments&review_id=' + parseInt(review_id));

				setTimeout(function () {
					var comments = [];
					$.each($review_comments.find('.review-comment'), function (k, it) {
						comments.push($(it)[0].outerHTML);
					});

					if($('#review-comments-pag-' + review_id).length > 0)
						createPagination('#review-comments-pag-' + review_id, comments);
				}, 100);
			}
		});
	}

	function popupImages() {
		$.each($('.review-thumbnails'), function(key, item) {
			$(item).magnificPopup({
				type: 'image',
				delegate: 'a',
				gallery: {
					enabled: true
				}
			});
		});
	}

	$(document).on('click', '.review #comment-btn', function(e) {
		e.preventDefault();

		var $review = $(this).closest('.review');
		var $review_comments = $review.find('.review-comments');
		var review_id = $review.find('input[name="review_id"]').val();
		var $form = $(this).closest('form');

		$.ajax({
			type: 'post',
			dataType: 'json',
			url: "index.php?route=multimerch/product_review/jxAddReviewComment",
			data: {review_id: review_id, text: $form.find('textarea[name="text"]').val()},
			success: function (json) {
				if(json.errors) {
					for(var i = 0, length = json.errors.length; i < length; i++) {
						console.error(json.errors[i]);
					}
				} else if(json.success) {
					$review.find('.review-comments').load('index.php?route=multimerch/product_review/jxGetReviewComments&review_id=' + parseInt(review_id));

					setTimeout(function() {
						var comments = [];
						$.each($review_comments.find('.review-comment'), function(key, item) {
							comments.push($(item)[0].outerHTML);
						});

						if($('#review-comments-pag-' + review_id).length > 0)
							createPagination('#review-comments-pag-' + review_id, comments);
					}, 100);
				}
			}
		});
	});
});