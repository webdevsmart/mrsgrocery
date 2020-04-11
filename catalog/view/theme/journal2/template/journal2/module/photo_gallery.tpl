<div id="journal-gallery-<?php echo $module; ?>" class="journal-gallery-<?php echo $module_id; ?> box journal-gallery <?php echo implode(' ', $disable_on_classes); ?> <?php echo $carousel ? 'journal-carousel' : ''; ?> <?php echo $carousel && isset($bullets) ? 'has-bullets' : ''; ?> <?php echo $carousel && isset($arrows) && $arrows === 'top' ? 'arrows-top' : ''; ?>" style="<?php echo isset($css) ? $css : ''; ?>">
    <div>
        <?php if ($title): ?>
            <div class="box-heading"><?php echo $title; ?></div>
        <?php endif; ?>
        <div class="box-content">
            <?php if ($carousel): ?>
                <div class="swiper">
                    <div class="swiper-container" <?php echo $this->journal2->settings->get('rtl') ? 'dir="rtl"' : ''; ?>>
                        <div class="swiper-wrapper">
                            <?php $index = 0;
                            foreach ($images as $image): ?>
                                <div class="gallery-thumb <?php echo $grid_classes; ?> <?php echo $carousel ? 'swiper-slide' : ''; ?>" style="<?php echo !$carousel && $index >= $thumbs_limit ? 'display: none' : ''; ?>">
                                    <a href="<?php echo $image['image']; ?>" style="<?php echo $image_border; ?>; " data-thumb="<?php echo $image['thumb']; ?>" class="swipebox" title="<?php echo $image['name']; ?>">
                                        <div class="item-hover"></div>
                                        <img width="<?php echo $thumbs_width; ?>" height="<?php echo $thumbs_height; ?>" src="<?php echo $image['thumb']; ?>" alt="<?php echo $image['name']; ?>"/>
                                    </a>
                                </div>
                                <?php $index++; endforeach; ?>
                        </div>
                    </div>
                    <?php if (isset($arrows) && $arrows !== 'none'): ?>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    <?php endif; ?>
                </div>
                <?php if (isset($bullets) && $bullets): ?>
                    <div class="swiper-pagination"></div>
                <?php endif; ?>
            <?php else: ?>
                <?php $index = 0;
                foreach ($images as $image): ?>
                    <div class="gallery-thumb <?php echo $grid_classes; ?>" style="<?php echo !$carousel && $index >= $thumbs_limit ? 'display: none' : ''; ?>">
                        <a href="<?php echo $image['image']; ?>" style="<?php echo $image_border; ?>; " data-thumb="<?php echo $image['thumb']; ?>" class="swipebox" title="<?php echo $image['name']; ?>">
                            <div class="item-hover"></div>
                            <img src="<?php echo $image['thumb']; ?>" width="<?php echo $thumbs_width; ?>" height="<?php echo $thumbs_height; ?>" alt="<?php echo $image['name']; ?>"/>
                        </a>
                    </div>
                    <?php $index++; endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    <?php if ($carousel): ?>
    <?php
    $grid = array(
        array(0, $is_column ? 3 : (int)$grid['xs']),
        array(470, $is_column ? 3 : (int)$grid['sm']),
        array(760, $is_column ? 3 : (int)$grid['md']),
        array(980, $is_column ? 3 : (int)$grid['lg']),
        array(1100, $is_column ? 3 : (int)$grid['xl']),
    );
    ?>
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
            spaceBetween: 15,
            pagination: <?php echo isset($bullets) && $bullets ? '$(\'#journal-gallery-' . $module . ' .swiper-pagination\')' : 'false'; ?>,
            paginationClickable: true,
            nextButton: <?php echo isset($arrows) && $arrows !== 'none' ? '$(\'#journal-gallery-' . $module . ' .swiper-button-next\')' : 'false'; ?>,
            prevButton: <?php echo isset($arrows) && $arrows !== 'none' ? '$(\'#journal-gallery-' . $module . ' .swiper-button-prev\')' : 'false'; ?>,
            autoplay: <?php echo $autoplay ? $autoplay : 0; ?>,
            autoplayStopOnHover: <?php echo $pause_on_hover ? 'true' : 'false'; ?>,
            speed: <?php echo $slide_speed; ?>,
            touchEventsTarget: <?php echo $touch_drag ? '\'container\'' : 'false'; ?>,
            onInit: function (swiper) {
                var imgs = [
                    <?php foreach ($images as $image): ?>
                    { src: '<?php echo $image['image']; ?>', thumb: '<?php echo $image['thumb']; ?>', subHtml: '<?php echo $image['name']; ?>' },
                    <?php endforeach; ?>
                ];

                $('#journal-gallery-<?php echo $module; ?> a').click(function (e) {
                    e.preventDefault();

                    var index = -1;
                    var href = $(this).attr('href');
                    $.map(imgs, function (img, i) {
                        if (img['src'] === href) {
                            index = i;
                        }
                    });

                    $(this).lightGallery({
                        thumbnail: <?php echo $this->journal2->settings->get('gallery_thumbs', 'on') === 'on' ? 'true' : 'false'; ?>,
                        showThumbByDefault: <?php echo $this->journal2->settings->get('gallery_thumbs_hide', 'on') === 'off' ? 'true' : 'false'; ?>,
                        thumbWidth: parseInt('<?php echo $this->journal2->settings->get('gallery_thumbs_width', '100'); ?>', 10),
                        thumbContHeight: parseInt('<?php echo $this->journal2->settings->get('gallery_thumbs_height', '100'); ?>', 10),
                        thumbMargin: parseInt('<?php echo $this->journal2->settings->get('gallery_thumbs_spacing', '7'); ?>', 10),
                        hideBarsDelay: parseInt('<?php echo $this->journal2->settings->get('hide_gallery_bars_after', '5000'); ?>', 10),
                        dynamic: true,
                        dynamicEl: imgs,
                        download: false,
                        actualSize: false,
                        index: index
                    })
                });
            }
        };

        $('#journal-gallery-<?php echo $module; ?> .swiper-container').swiper(opts);
    })();
    <?php else: ?>
    (function () {
        var imgs = [
            <?php foreach ($images as $image): ?>
            { src: '<?php echo $image['image']; ?>', thumb: '<?php echo $image['thumb']; ?>', subHtml: '<?php echo str_replace("'", "\\'", $image['name']); ?>' },
            <?php endforeach; ?>
        ];

        $('#journal-gallery-<?php echo $module; ?> a').click(function (e) {
            e.preventDefault();

            var index = $(this).index('#journal-gallery-<?php echo $module; ?> a');

            $(this).lightGallery({
                thumbnail: <?php echo $this->journal2->settings->get('gallery_thumbs', 'on') === 'on' ? 'true' : 'false'; ?>,
                showThumbByDefault: <?php echo $this->journal2->settings->get('gallery_thumbs_hide', 'on') === 'off' ? 'true' : 'false'; ?>,
                thumbWidth: parseInt('<?php echo $this->journal2->settings->get('gallery_thumbs_width', '100'); ?>', 10),
                thumbContHeight: parseInt('<?php echo $this->journal2->settings->get('gallery_thumbs_height', '100'); ?>', 10),
                thumbMargin: parseInt('<?php echo $this->journal2->settings->get('gallery_thumbs_spacing', '7'); ?>', 10),
                hideBarsDelay: parseInt('<?php echo $this->journal2->settings->get('hide_gallery_bars_after', '5000'); ?>', 10),
                dynamic: true,
                dynamicEl: imgs,
                download: false,
                actualSize: false,
                index: index
            })
        });
    })();
    <?php endif; ?>
</script>
