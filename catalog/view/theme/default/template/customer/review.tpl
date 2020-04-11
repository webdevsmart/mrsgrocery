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
            <div class="panel panel-default">
                <div class="panel-heading feedback-heading">
                    <div class="row">
                        <div class="col-md-10 sub-table">
                            <div class="sub-row">
                                <span class="sub-table-heading"><?php echo $ms_customer_product_rate_heading ;?></span>
                            </div>
                        </div>
						<span class="order-number col-md-2">
							#<?php echo $order_id ;?><br />
							<a href="<?php echo $this->url->link('account/order/info', 'order_id=' . $order_id, 'SSL') ;?>">
								<?php echo $ms_order_details ;?>
							</a>
						</span>
                    </div>
                </div>
                <div class="panel-body">
                    <?php foreach($products as $product) :?>
                        <?php if($product['order_product_id'] == $order_product_id) :?>
                            <div class="product-holder">
                                <div class="row">
                                    <div class="product-image col-md-3">
                                        <a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']) ;?>">
                                            <img src="<?php echo $product['product']['image'] ;?>"/>
                                        </a>
                                    </div>
                                    <div class="product-info col-md-5">
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
                                            <?php echo $ms_order_sold_by ;?> <a href="<?php echo $this->url->link('seller/catalog-seller/profile', 'seller_id=' . $product['seller']['seller_id']) ;?>"><?php echo $product['seller']['ms.nickname'] ;?></a>
                                        </span>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="product-price">
                                            <?php echo $product['price'] ;?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- `Rate` section -->
                            <form action="<?php echo $this->url->link('customer/review/jxSubmitReview', '', 'SSL') ;?>" id="form-rate" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>" />
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id'] ;?>">
                                <input type="hidden" name="order_id" value="<?php echo $product['order_id'] ;?>">
                                <input type="hidden" name="order_product_id" value="<?php echo $product['order_product_id'] ;?>">

                                <div class="alert alert-danger form-rate-error" style="display: none;"></div>

                                <div class="form-rate-rating-stars">
                                    <label for="rating-input-xs" class="control-label mm_req"><?php echo $ms_customer_product_rate_stars_label ;?></label>
                                    <input id="rating-input-xs" name="rating" class="rating" data-min="0" data-max="5" data-step="1" data-size="xs" value="<?php echo (!empty($product['review'])) ? $product['review'][0]['rating'] : 0 ;?>">
                                </div>

                                <div class="row">
                                    <div class="form-rate-comment col-lg-9 col-md-8 col-sm-12 col-xs-12">
                                        <label for="rating-comment" class="mm_req"><?php echo $ms_customer_product_rate_comments ;?></label>
                                        <textarea class="form-control" rows="10" id="rating-comment" name="rating_comment" placeholder="<?php echo $ms_customer_product_rate_comments_placeholder ;?>" maxlength="5000"><?php echo (!empty($product['review'])) ? $product['review'][0]['comment'] : '' ;?></textarea>
                                        <span class="rating-comment-note"></span>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                                        <div class="form-rate-upload dragndrop" id="ms-image-dragndrop">
                                            <p class="mm_drophere"><?php echo $ms_customer_product_rate_drag_drop_here; ?></p>
                                            <p class="mm_or"><?php echo $ms_or; ?></p>
                                            <a class="btn btn-default" href="#" id="ms-image"><span><?php echo $ms_select_files; ?></span></a>
                                            <p class="mm_allowed"><?php echo sprintf($ms_customer_product_rate_drag_drop_allowed, $this->config->get('msconf_allowed_image_types')); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <?php if(isset($review['attachments']) && !empty($review['attachments'])) { ?>
                                        <div class="ms-image col-sm-12" id="image-holder">
                                            <?php foreach($review['attachments'] as $attachment) { ?>
                                                <div class="image-holder">
                                                    <input type="hidden" name="images[]" value="<?php echo $attachment['name'] ;?>"/>
                                                    <img src="<?php echo $attachment['thumb'] ;?>"/>
                                                    <span class="ms-remove"><i class="fa fa-times"></i></span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="ms-image hidden col-sm-12" id="image-holder"></div>
                                    <?php } ?>
                                </div>

                                <div class="ms-progress progress"></div>

                                <div class="form-rate-buttons">
                                    <button type="submit" form="form-rate" title="<?php echo $ms_customer_product_rate_btn_submit; ?>" class="btn btn-primary form-rate-submit"><?php echo $ms_customer_product_rate_btn_submit; ?></button>
                                    <a href="<?php echo $this->url->link('account/order', '', 'SSL') ;?>"><?php echo $ms_button_cancel; ?></a>
                                </div>
                            </form>
                            <!-- End `rate` section -->
                        <?php endif ;?>
                    <?php endforeach ;?>
                </div>
            </div>
            <?php echo $content_bottom; ?>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>

<script type="text/javascript">
    var msGlobals = {
        ms_customer_product_rate_characters_left: '<?php echo $ms_customer_product_rate_characters_left; ?>',
        ms_customer_product_rate_form_error: '<?php echo $ms_customer_product_rate_form_error; ?>'
    };
</script>

<?php echo $footer; ?>
