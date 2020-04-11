<div id="carousel-<?php echo $module; ?>" class="carousel-<?php echo $module_id; ?> box journal-carousel carousel-category <?php echo implode(' ', $disable_on_classes); ?> <?php echo $bullets ? 'bullets-on' : ''; ?> <?php echo $arrows === 'top' ? 'arrows-top' : ''; ?> <?php echo $show_title_class; ?> <?php echo isset($gutter_on_class) ? $gutter_on_class : ''; ?>" style="<?php echo isset($css) ? $css : ''; ?>">
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
                        <?php foreach ($section['items'] as $item) { ?>
                            <div class="product-grid-item isotope-element swiper-slide <?php echo implode(' ', $item['section_class']); ?> <?php echo $grid_classes; ?>">
                                <div class="product-wrapper" data-respond="start: 150px; end: 300px; interval: 20px;" style="<?php echo $image_bgcolor; ?>">
                                    <?php if (isset($item['thumb'])) { ?>
                                        <div class="image">
                                            <a href="<?php echo $item['href']; ?>" style="<?php echo $image_border_css; ?>">
                                                <img class="first-image" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" src="<?php echo $item['thumb']; ?>" title="<?php echo $item['name']; ?>" alt="<?php echo $item['name']; ?>"/>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <div class="product-details">
                                        <div class="name"><a href="<?php echo $item['href']; ?>"><?php echo $item['name']; ?></a></div>
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

                    if (!Journal.isFlexboxSupported) {
                        Journal.equalHeight($('#carousel-<?php echo $module; ?> .product-grid-item'), '.name');
                    }
                });

                $('#carousel-<?php echo $module; ?> .htabs a.atab[href="#carousel-<?php echo $module; ?>-<?php echo $default_section; ?>"]').click();
            })();
        </script>
    </div>
</div>
