<div id="journal-blog-posts-<?php echo $module; ?>" class="journal-blog-posts-<?php echo $module_id; ?> box post-module <?php echo $heading_title ? '' : 'no-heading'; ?> <?php echo implode(' ', $disable_on_classes); ?> <?php echo $carousel ? 'journal-carousel' : ''; ?> <?php echo $carousel && $bullets ? 'bullets-on' : ''; ?> <?php echo $carousel && $arrows === 'top' ? 'arrows-top' : ''; ?> <?php echo isset($gutter_on_class) ? $gutter_on_class : ''; ?>" style="<?php echo isset($css) ? $css : ''; ?>">
    <div>
        <?php if ($heading_title): ?>
            <div class="box-heading"><?php echo $heading_title; ?></div>
        <?php endif; ?>
        <div class="box-post box-content posts <?php echo $display === 'list' ? 'blog-list-view' : ''; ?>">
            <?php if ($carousel): ?>
                <div class="swiper">
                    <div class="swiper-container" <?php echo $this->journal2->settings->get('rtl') ? 'dir="rtl"' : ''; ?>>
                        <div class="swiper-wrapper">
                            <?php foreach ($posts as $post): ?>
                                <div class="post-item <?php echo $grid_classes; ?> <?php echo $carousel ? 'swiper-slide' : ''; ?>">
                                    <div class="post-wrapper">
                                        <?php if ($post['image']): ?>
                                            <a class="post-image" href="<?php echo $post['href']; ?>"><img src="<?php echo $post['image']; ?>" alt="<?php echo $post['name']; ?>"/></a>
                                        <?php endif; ?>
                                        <div class="post-item-details post-content-align<?php echo $content_align; ?>">
                                            <h2><a href="<?php echo $post['href']; ?>"><?php echo $post['name']; ?></a></h2>
                                            <div class="comment-date">
                                                <span class="p-author"><?php echo $post['author']; ?></span>
                                                <span class="p-date"><?php echo $post['date']; ?></span>
                                                <span class="p-comment"><?php echo $post['comments']; ?></span>
                                            </div>
                                            <?php if ($post['description'] !== false): ?>
                                                <div class="post-text"><span><?php echo $post['description']; ?></span></div>
                                            <?php endif; ?>
                                            <div class="post-button">
                                                <a class="post-view-more button" href="<?php echo $post['href']; ?>"><i class="post-button-left-icon"></i><?php echo $this->journal2->settings->get('blog_button_read_more', 'Read More'); ?><i class="post-button-right-icon"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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
                <?php foreach ($posts as $post): ?>
                    <div class="post-item <?php echo $grid_classes; ?> <?php echo $carousel ? 'swiper-slide' : ''; ?>">
                        <div class="post-wrapper">
                            <?php if ($post['image']): ?>
                                <a class="post-image" href="<?php echo $post['href']; ?>"><img src="<?php echo $post['image']; ?>" alt="<?php echo $post['name']; ?>"/></a>
                            <?php endif; ?>
                            <div class="post-item-details" style="text-align:<?php echo $content_align; ?>">
                                <h2><a href="<?php echo $post['href']; ?>"><?php echo $post['name']; ?></a></h2>
                                <div class="comment-date">
                                    <span class="p-author"><?php echo $post['author']; ?></span>
                                    <span class="p-date"><?php echo $post['date']; ?></span>
                                    <span class="p-comment"><?php echo $post['comments']; ?></span>
                                </div>
                                <?php if ($post['description'] !== false): ?>
                                    <div class="post-text"><span><?php echo $post['description']; ?></span></div>
                                <?php endif; ?>
                                <div class="post-button">
                                    <a class="post-view-more button" href="<?php echo $post['href']; ?>"><i class="post-button-left-icon"></i><?php echo $this->journal2->settings->get('blog_button_read_more', 'Read More'); ?><i class="post-button-right-icon"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    <?php if ($carousel): ?>
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
            pagination: <?php echo $bullets ? '$(\'#journal-blog-posts-' . $module . ' .swiper-pagination\')' : 'false'; ?>,
            paginationClickable: true,
            nextButton: <?php echo $arrows !== 'none' ? '$(\'#journal-blog-posts-' . $module . ' .swiper-button-next\')' : 'false'; ?>,
            prevButton: <?php echo $arrows !== 'none' ? '$(\'#journal-blog-posts-' . $module . ' .swiper-button-prev\')' : 'false'; ?>,
            autoplay: <?php echo $autoplay ? $autoplay : 0; ?>,
            speed: <?php echo $slide_speed; ?>,
            touchEventsTarget: <?php echo $touch_drag ? '\'container\'' : 'false'; ?>
        };

        $('#journal-blog-posts-<?php echo $module; ?> .swiper-container').swiper(opts);
    })();
    <?php endif; ?>
    if (!Journal.isFlexboxSupported) {
        Journal.equalHeight($("#journal-blog-posts-<?php echo $module; ?> .post-wrapper"), '.post-item-details h2 a');
        <?php if ($show_description): ?>
        Journal.equalHeight($("#journal-blog-posts-<?php echo $module; ?> .post-wrapper"), '.post-text span');
        <?php endif; ?>
    }
</script>

