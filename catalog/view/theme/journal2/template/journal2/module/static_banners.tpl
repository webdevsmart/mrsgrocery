<div id="static-banners-<?php echo $module; ?>" class="static-banners-<?php echo $module_id; ?> box static-banners <?php echo $carousel ? 'journal-carousel' : ''; ?> <?php echo implode(' ', $disable_on_classes); ?> <?php echo isset($gutter_on_class) ? $gutter_on_class : ''; ?>" style="<?php echo isset($css) ? $css : ''; ?>">
    <div>
        <?php if ($title): ?>
            <div class="box-heading"><?php echo $title; ?></div>
        <?php endif; ?>
        <div class="box-content">
            <?php if ($carousel): ?>
                <div class="swiper">
                    <div class="swiper-container" <?php echo $this->journal2->settings->get('rtl') ? 'dir="rtl"' : ''; ?>>
                        <div class="swiper-wrapper">
                            <?php foreach ($sections as $section): ?>
                                <div class="static-banner swiper-slide">
                                    <?php if ($section['link']): ?>
                                        <a href="<?php echo $section['link']; ?>" <?php echo $section['target']; ?>> <span class="banner-overlay" style="<?php echo $image_border; ?>; <?php if ($bgcolor): ?> background-color: <?php echo $bgcolor; ?> <?php endif; ?>"><?php echo $icon; ?></span><img style="<?php echo $image_border; ?>" src="<?php echo $section['image']; ?>" width="<?php echo $section['image_width']; ?>" height="<?php echo $section['image_height']; ?>" alt="<?php echo $section['image_title']; ?>"/></a>
                                    <?php else: ?>
                                        <img style="<?php echo $image_border; ?>" src="<?php echo $section['image']; ?>" alt="<?php echo $section['image_title']; ?>" width="<?php echo $section['image_width']; ?>" height="<?php echo $section['image_height']; ?>"/>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if ($arrows): ?>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    <?php endif; ?>
                </div>
                <?php if ($bullets): ?>
                    <div class="swiper-pagination"></div>
                <?php endif; ?>
            <?php else: ?>
                <?php foreach ($sections as $section): ?>
                    <div class="static-banner <?php echo $grid_classes; ?>">
                        <?php if ($section['link']): ?>
                            <a href="<?php echo $section['link']; ?>" <?php echo $section['target']; ?>> <span class="banner-overlay" style="<?php echo $image_border; ?>; <?php if ($bgcolor): ?> background-color: <?php echo $bgcolor; ?> <?php endif; ?>"><?php echo $icon; ?></span><img style="<?php echo $image_border; ?>" src="<?php echo $section['image']; ?>" width="<?php echo $section['image_width']; ?>" height="<?php echo $section['image_height']; ?>" alt="<?php echo $section['image_title']; ?>"/></a>
                        <?php else: ?>
                            <img style="<?php echo $image_border; ?>" src="<?php echo $section['image']; ?>" alt="<?php echo $section['image_title']; ?>" width="<?php echo $section['image_width']; ?>" height="<?php echo $section['image_height']; ?>"/>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if ($carousel): ?>
    <script>
        (function () {
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

            var opts = {
                slidesPerView: grid[4][1],
                slidesPerGroup: grid[4][1],
                breakpoints: breakpoints,
                spaceBetween: 20,
                pagination: <?php echo $bullets ? '$(\'#static-banners-' . $module . ' .swiper-pagination\')' : 'false'; ?>,
                paginationClickable: true,
                nextButton: <?php echo $arrows ? '$(\'#static-banners-' . $module . ' .swiper-button-next\')' : 'false'; ?>,
                prevButton: <?php echo $arrows ? '$(\'#static-banners-' . $module . ' .swiper-button-prev\')' : 'false'; ?>,
                autoplay: <?php echo $autoplay ? $autoplay : 0; ?>,
                speed: <?php echo $slide_speed; ?>,
                touchEventsTarget: <?php echo $touch_drag ? '\'container\'' : 'false'; ?>
            };

            $('#static-banners-<?php echo $module; ?> .swiper-container').swiper(opts);
        })();
    </script>
<?php endif; ?>