<div id="cs-<?php echo $module; ?>" class="cs-<?php echo $module_id; ?> box custom-sections section-product <?php echo implode(' ', $disable_on_classes); ?> <?php echo $single_class; ?> <?php echo $show_title_class; ?> <?php echo isset($gutter_on_class) ? $gutter_on_class : ''; ?>" style="<?php echo isset($css) ? $css : ''; ?>">
    <?php if ($spacing): ?>
        <style>
            #cs-<?php echo $module; ?> .product-grid-item {
                margin-bottom: <?php echo $spacing; ?>px;
            }

            #cs-<?php echo $module; ?> .product-wrapper {
                margin-right: <?php echo $spacing; ?>px;
            }

            #cs-<?php echo $module; ?> .product-grid, #content #cs-<?php echo $module; ?> .box-product {
                margin-right: -<?php echo $spacing; ?>px;
            }
        </style>
    <?php endif; ?>
    <?php if ($show_title): ?>
    <div class="box-heading box-sections box-block">
        <ul>
            <?php foreach ($sections as $section): ?>
            <?php if ($section['is_link']): ?>
            <li><a href="<?php echo $section['url']; ?>" <?php echo $section['target']; ?>><?php echo $section['section_name']; ?></a></li>
            <?php else: ?>
            <?php if (!count($section['items'])) continue; ?>
            <li><a href="javascript:;" data-option-value="section-<?php echo $section['section_class']; ?>"><?php echo $section['section_name']; ?></a></li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    <div class="box-content">
        <div class="product-grid">
            <?php foreach ($items as $product) { ?>
            <div class="product-grid-item isotope-element <?php echo implode(' ', $product['section_class']); ?> <?php echo $grid_classes; ?> display-<?php echo $this->journal2->settings->get('product_grid_wishlist_icon_display');?> <?php echo $this->journal2->settings->get('product_grid_button_block_button');?> <?php echo $product['classes']; ?>">
                <div class="product-wrapper <?php echo isset($product['labels']) && is_array($product['labels']) && isset($product['labels']['outofstock']) ? 'outofstock' : ''; ?>" style="<?php echo $image_bgcolor; ?>">
                    <?php if (isset($product['thumb'])) { ?>
                    <div class="image <?php echo $this->journal2->settings->get('show_countdown', 'never') !== 'never' && isset($product['date_end']) && $product['date_end'] ? 'has-countdown' : ''; ?>">
                        <a href="<?php echo $product['href']; ?>" <?php if(isset($product['thumb2']) && $product['thumb2']): ?> class="has-second-image" style="<?php echo $image_border_css; ?>;background: url('<?php echo $product['thumb2']; ?>') no-repeat;" <?php else: echo $image_border_css; endif; ?>>
                            <img class="first-image" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
                        </a>
                        <?php foreach ($product['labels'] as $label => $name): ?>
                        <span class="label-<?php echo $label; ?>"><b><?php echo $name; ?></b></span>
                        <?php endforeach; ?>
                        <?php if($this->journal2->settings->get('product_grid_wishlist_icon_position') === 'image' && $this->journal2->settings->get('product_grid_wishlist_icon_display', '') === 'icon'): ?>
                            <div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_wishlist; ?>"><i class="wishlist-icon"></i><span class="button-wishlist-text"><?php echo $button_wishlist;?></span></a></div>
                            <div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_compare; ?>"><i class="compare-icon"></i><span class="button-compare-text"><?php echo $button_compare;?></span></a></div>
                        <?php endif; ?>
                    </div>
                    <?php } ?>
                    <div class="product-details">
                        <div class="caption">
                        <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
                        <div class="description"><?php echo $product['description']; ?></div>
                        <?php if ($product['price']) { ?>
                        <div class="price">
                            <?php if (!$product['special']) { ?>
                            <?php echo $product['price']; ?>
                            <?php } else { ?>
                            <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new" <?php echo isset($product['date_end']) && $product['date_end'] ? "data-end-date='{$product['date_end']}'" : ""; ?>><?php echo $product['special']; ?></span>
                            <?php } ?>
                            <?php if ($product['tax']) { ?>
                            <hr>
                            <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                            <?php } ?>
                        </div>
                        <?php } ?>
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
                        </div>
                        <div class="button-group">
                        <?php if (Journal2Utils::isEnquiryProduct($this, $product)): ?>
                        <div class="cart enquiry-button">
                            <a href="javascript:Journal.openPopup('<?php echo $this->journal2->settings->get('enquiry_popup_code'); ?>', '<?php echo $product['product_id']; ?>');" data-clk="addToCart('<?php echo $product['product_id']; ?>');" class="button hint--top" data-hint="<?php echo $this->journal2->settings->get('enquiry_button_text'); ?>"><?php echo $this->journal2->settings->get('enquiry_button_icon') . '<span class="button-cart-text">' . $this->journal2->settings->get('enquiry_button_text') . '</span>'; ?></a>
                        </div>
                        <?php else: ?>
                        <div class="cart <?php echo isset($product['labels']) && is_array($product['labels']) && isset($product['labels']['outofstock']) ? 'outofstock' : ''; ?>">
                            <a onclick="addToCart('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');" class="button hint--top" data-hint="<?php echo $button_cart; ?>"><i class="button-left-icon"></i><span class="button-cart-text"><?php echo $button_cart; ?></span><i class="button-right-icon"></i></a>
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
    </div>
    <script>
        (function(){
            var $isotope = $('#cs-<?php echo $module; ?> .product-grid');
            var $filters = $('#cs-<?php echo $module; ?> .box-heading a[data-option-value]');
            var default_section = '<?php echo count($sections) > 1 ? $default_section : ''; ?>';

            $isotope.each(function () {
                Journal.equalHeight($(this).find('.product-grid-item'), '.name');
                Journal.equalHeight($(this).find('.product-grid-item'), '.description');
                Journal.equalHeight($(this).find('.product-grid-item'), '.price');
            });

            $('#cs-<?php echo $module; ?> img').imagesLoaded(function () {
                $isotope.isotope({
                    itemSelector: '.isotope-element',
                    initLayout: default_section === ''
                });
            });

            $filters.click(function () {
                var $this = $(this);
                if ($this.hasClass('selected')) {
                    return false;
                }
                $filters.removeClass('selected');
                $this.addClass('selected');
                $isotope.isotope({
                    filter: '.' + $this.attr('data-option-value')
                })
            });

            if (default_section !== '') {
                $('#cs-<?php echo $module; ?> .box-heading a[data-option-value="section-' + default_section + '"]').click();
            }

            <?php /* enable countdown */ ?>
            <?php if ($this->journal2->settings->get('show_countdown', 'never') !== 'never'): ?>
            $('#cs-<?php echo $module; ?> .product-grid-item > div').each(function () {
                var $new = $(this).find('.price-new');
                if ($new.length && $new.attr('data-end-date')) {
                    $(this).find('.image').append('<div class="countdown"></div>');
                }
                Journal.countdown($(this).find('.countdown'), $new.attr('data-end-date'));
            });
            <?php endif; ?>
        }());
    </script>
</div>
