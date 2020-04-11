<div class="journal-simple-slider-<?php echo $module_id; ?> <?php echo $nav_on_hover; ?> journal-simple-slider box <?php echo implode(' ', $disable_on_classes); ?>" style="<?php echo $width; ?>; max-height:<?php echo $height; ?>px">
    <div id="journal-simple-slider-<?php echo $module; ?>">
        <div class="swiper-container" <?php echo $this->journal2->settings->get('rtl') ? 'dir="rtl"' : ''; ?>>
            <div class="swiper-wrapper">
                <?php foreach ($slides as $slide): ?>

                    <?php if ($slide['link']): ?>
                        <div class="item swiper-slide"><a href="<?php echo $slide['link']; ?>" <?php echo $slide['target']; ?>><img width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" src="<?php echo $slide['image']; ?>" alt="<?php echo $slide['name']; ?>"/></a></div>
                    <?php else: ?>
                        <div class="item swiper-slide">
                            <img width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" src="<?php echo $slide['image']; ?>" alt="<?php echo $slide['name']; ?>"/>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if ($bullets): ?>
            <div class="swiper-pagination"></div>
        <?php endif; ?>
        <?php if ($arrows): ?>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        <?php endif; ?>
    </div>
    <script>
        (function () {
            var opts = $.parseJSON('<?php echo json_encode($js_options); ?>');
            if (opts.pagination) {
                opts.pagination = $('#journal-simple-slider-<?php echo $module; ?> .swiper-pagination');
            }
            $('#journal-simple-slider-<?php echo $module; ?> .swiper-container').swiper(opts);
        })();
    </script>
</div>

