<?php if(!empty($sellers)) { ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title"><a href="#collapse-coupon" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion"><?php echo $ms_cart_coupon_heading; ?> <i class="fa fa-caret-down"></i></a></h4>
		</div>
		<div id="collapse-coupon" class="panel-collapse collapse">
			<div class="panel-body">
				<form id="ms-coupons" class="tab-content">
					<label class="col-sm-2 control-label"><?php echo $ms_cart_coupon_field_label; ?></label>
					<div class="input-group">
						<select name="seller_id_to_code" class="form-control ms-seller-select">
							<?php foreach ($sellers as $seller) { ?>
								<option value="<?php echo $seller['seller_id'] . '-' . $seller['coupon']; ?>"><?php echo $seller['nickname']; ?></option>
							<?php } ?>
						</select>
						<span class="input-group-addon" style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>
						<input type="text" name="coupons[seller_id]" value="" placeholder="<?php echo $ms_cart_coupon_field_placeholder; ?>" class="form-control" />
						<span class="input-group-btn">
							<input type="button" value="<?php echo $ms_cart_coupon_button_apply; ?>" id="ms-button-coupon" data-loading-text="<?php echo $text_loading; ?>"  class="btn btn-primary" />
						</span>
					</div>
				</form>
			</div>

		</div>
	</div>

	<script type="text/javascript">
		$(function() {
			$('.ms-seller-select').on('change', function() {
				var $this = $(this);
				var seller_id = $this.val().split('-')[0];
				var code = $this.val().split('-')[1];
				var coupon_input = $(document).find('input[name^="coupons"]');

				coupon_input.attr('name', 'coupons[' + seller_id + ']');
				coupon_input.val(code);
			});
			$('.ms-seller-select').trigger('change');

			$('#ms-button-coupon').on('click', function() {
				$.ajax({
					url: 'index.php?route=multimerch/cart_coupon/apply',
					type: 'post',
					data: $('#ms-coupons').serialize(),
					dataType: 'json',
					beforeSend: function() {
						$('#ms-button-coupon').button('loading');
					},
					complete: function() {
						$('#ms-button-coupon').button('reset');
					},
					success: function(json) {
						$('.alert').remove();

						if (json['error']) {
							$('.breadcrumb').after('<div class="alert alert-danger">' + json['error'] + '</div>');

							$('html, body').animate({ scrollTop: 0 }, 'slow');
						}

						if (json['redirect']) {
							location = json['redirect'];
						}
					}
				});
			});
		});
	</script>
<?php } ?>

