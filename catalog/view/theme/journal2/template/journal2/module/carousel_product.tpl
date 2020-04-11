<div id="carousel-<?php echo $module; ?>" class="carousel-<?php echo $module_id; ?> box journal-carousel carousel-product <?php echo implode(' ', $disable_on_classes); ?> <?php echo $show_title_class; ?> <?php echo $bullets ? 'bullets-on' : ''; ?> <?php echo $arrows === 'top' ? 'arrows-top' : ''; ?> <?php echo isset($gutter_on_class) ? $gutter_on_class : ''; ?>" style="<?php echo isset($css) ? $css : ''; ?>">
    <div>
        <div class="htabs box-heading <?php echo $single_class; ?>" style="<?php echo $show_title ? '' : 'display: none;'; ?>">
            <?php $index = 0;
            foreach ($sections as $section): ?>
                <?php if ($section['is_link']): ?>
                    <a href="<?php echo $section['url']; ?>" <?php echo $section['target']; ?>><?php echo $section['section_name']; ?></a>
                <?php else: ?>
                    <?php if (!count($section['items'])) continue; ?>
                    <a href="#carousel-<?php echo $module; ?>-<?php echo $index++; ?>" class="atab"><?php echo $section['section_name']; ?></a>
                <?php endif; ?>
                <?php endforeach; ?>
        </div>
        <?php $index = 0;
        foreach ($sections as $section): ?>
            <?php if (!count($section['items'])) continue; ?>
            <div id="carousel-<?php echo $module; ?>-<?php echo $index++; ?>" class="tab-content box-content">
                <div class="swiper">
                <div class="swiper-container" <?php echo $this->journal2->settings->get('rtl') ? 'dir="rtl"' : ''; ?>>
                    <div class="swiper-wrapper">
                        <?php foreach ($section['items'] as $product) { ?>
                            <div class="product-grid-item swiper-slide display-<?php echo $this->journal2->settings->get('product_grid_wishlist_icon_display'); ?> <?php echo $this->journal2->settings->get('product_grid_button_block_button'); ?> <?php echo $product['classes']; ?> <?php echo $grid_classes; ?>">
                                <div class="product-wrapper <?php echo isset($product['labels']) && is_array($product['labels']) && isset($product['labels']['outofstock']) ? 'outofstock' : ''; ?>" style="<?php echo $image_bgcolor; ?>">
                                    <?php if (isset($product['thumb'])) { ?>
                                        <div class="image <?php echo $this->journal2->settings->get('show_countdown', 'never') !== 'never' && isset($product['date_end']) && $product['date_end'] ? 'has-countdown' : ''; ?>">
                                            <a href="<?php echo $product['href']; ?>" <?php if (isset($product['thumb2']) && $product['thumb2']): ?> class="has-second-image" style="<?php echo $image_border_css; ?>;background: url('<?php echo $product['thumb2']; ?>') no-repeat;" <?php else: echo 'style="' . $image_border_css . '"'; endif; ?>>
                                                <img class="first-image" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>"/>
                                            </a>
                                            <?php foreach ($product['labels'] as $label => $name): ?>
                                                <span class="label-<?php echo $label; ?>"><b><?php echo $name; ?></b></span>
                                            <?php endforeach; ?>
                                            <?php if ($this->journal2->settings->get('product_grid_wishlist_icon_position') === 'image' && $this->journal2->settings->get('product_grid_wishlist_icon_display', '') === 'icon'): ?>
                                                <div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_wishlist; ?>"><i class="wishlist-icon"></i><span class="button-wishlist-text"><?php echo $button_wishlist; ?></span></a></div>
                                                <div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_compare; ?>"><i class="compare-icon"></i><span class="button-compare-text"><?php echo $button_compare; ?></span></a></div>
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
                                                        <span class="price-tax"><?php echo $text_tax; ?><?php echo $product['tax']; ?></span>
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
                                            <div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_wishlist; ?>"><i class="wishlist-icon"></i><span class="button-wishlist-text"><?php echo $button_wishlist; ?></span></a></div>
                                            <div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_compare; ?>"><i class="compare-icon"></i><span class="button-compare-text"><?php echo $button_compare; ?></span></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php if ($arrows !== 'none'): ?>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                <?php endif; ?>
                </div>
                <?php if ($bullets): ?>
                    <div class="swiper-pagination"></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

        <script>
            (function () {
                $('#carousel-<?php echo $module; ?> .htabs a.atab').tabs();

                var grid = $.parseJSON('<?php echo json_encode($grid); ?>');

                var breakpoints = {
                    470: {
                        slidesPerView: grid[0][1],
                        slidesPerGroup: grid[0][1]
                    },
                    760: {
                        slidesPerView: grid[1][1],
                        slidesPerGroup: grid[1][1]
                    },
                    980: {
                        slidesPerView: grid[2][1],
                        slidesPerGroup: grid[2][1]
                    },
                    1220: {
                        slidesPerView: grid[3][1],
                        slidesPerGroup: grid[3][1]
                    }
                };

                var current = null;

                $('#carousel-<?php echo $module; ?> .htabs a.atab').click(function () {
                    if (this === current) {
                        return;
                    }

                    current = this;
                    
                    var opts = {
                        slidesPerView: grid[4][1],
                        slidesPerGroup: grid[4][1],
                        breakpoints: breakpoints,
                        spaceBetween: parseInt('<?php echo $spacing; ?>', 10),
                        pagination: <?php echo $bullets ? '$($(this).attr(\'href\') + \' .swiper-pagination\')' : 'false'; ?>,
                        paginationClickable: true,
                        nextButton: <?php echo $arrows !== 'none' ? '$($(this).attr(\'href\') + \' .swiper-button-next\')' : 'false'; ?>,
                        prevButton: <?php echo $arrows !== 'none' ? '$($(this).attr(\'href\') + \' .swiper-button-prev\')' : 'false'; ?>,
                        autoplay: <?php echo $autoplay ? $autoplay : 0; ?>,
                        autoplayStopOnHover: <?php echo $pause_on_hover ? 'true' : 'false'; ?>,
                        speed: <?php echo $slide_speed; ?>,
                        touchEventsTarget: <?php echo $touch_drag ? '\'container\'' : 'false'; ?>,
                        preventClicks: false,
                        preventClicksPropagation: false
                    };

                    $($(this).attr('href') + ' .swiper-container').swiper(opts);

                    <?php if ($this->journal2->settings->get('show_countdown', 'never') !== 'never'): ?>
                    $($(this).attr('href') + ' .product-grid-item > div').each(function () {
                        var $new = $(this).find('.price-new');
                        if ($new.length && $new.attr('data-end-date')) {
                            $(this).find('.image').append('<div class="countdown"></div>');
                        }
                        Journal.countdown($(this).find('.countdown'), $new.attr('data-end-date'));
                    });
                    <?php endif; ?>

                    if (!Journal.isFlexboxSupported) {
                        Journal.equalHeight($('#carousel-<?php echo $module; ?> .product-grid-item'), '.name');
                        Journal.equalHeight($('#carousel-<?php echo $module; ?> .product-grid-item'), '.description');
                    }
                });

                $('#carousel-<?php echo $module; ?> .htabs a.atab[href="#carousel-<?php echo $module; ?>-<?php echo $default_section; ?>"]').click();
            })();
        </script>
    </div>
</div>
