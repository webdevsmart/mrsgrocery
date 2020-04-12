<?php echo $header; ?>
<div id="container" class="container j-container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="<?php echo $breadcrumb['href']; ?>" itemprop="url"><span itemprop="title"><?php echo $breadcrumb['text']; ?></span></a></li>
    <?php } ?>
  </ul>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content" class="blog-post">
    <h1 class="heading-title"><?php echo $heading_title; ?></h1><?php echo $content_top; ?>

    <div class="post-stats comment-date">
        <?php if ($post_author): ?>
        <span class="p-posted"><?php echo $this->journal2->settings->get('blog_posted_by_text', 'Posted by'); ?></span>
        <span class="p-author"><?php echo $post_author; ?></span>
        <?php endif; ?>
        <span class="p-date"><?php echo date($date_format_short, strtotime($post_date)); ?></span>
        <span class="p-comment"><?php echo count($comments); ?> <?php echo $this->journal2->settings->get('blog_comment_text', 'Comment(s)'); ?></span>
        <?php if ($post_categories): ?>
        <span class="p-category"><?php foreach($post_categories as $category): ?><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a><span>,</span><?php endforeach; ?></span><?php endif; ?>
    </div>
    
    <div class="post-details">
        <?php echo $post_content; ?>

        <?php if ($post_tags): ?>
        <div class="tags">
            <b><?php echo $text_tags; ?></b>
        <?php foreach ($post_tags as $tag): ?>
            <a href="<?php echo $tag['href']; ?>"><?php echo $tag['name']; ?></a>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($this->journal2->settings->get('config_blog_settings.share_this') && (!Journal2Cache::$mobile_detect->isMobile() || (Journal2Cache::$mobile_detect->isMobile() && !$this->journal2->settings->get('share_buttons_disable_on_mobile', 1))) && count($this->journal2->settings->get('config_share_buttons', array()))): ?>
        <div class="social share-this <?php echo $this->journal2->settings->get('share_buttons_disable_on_mobile', 1) ? 'hide-on-mobile' : ''; ?>">
            <div class="social-loaded">
                <script type="text/javascript">var switchTo5x=true;</script>
                <script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
                <script type="text/javascript">stLight.options({publisher: "<?php echo $this->journal2->settings->get('share_buttons_account_key'); ?>", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
                <?php foreach ($this->journal2->settings->get('config_share_buttons', array()) as $item): ?>
                <span class="<?php echo $item['class'] . $this->journal2->settings->get('share_buttons_style'); ?>" displayText="<?php echo $this->journal2->settings->get('share_buttons_style') ? $item['name'] : ''; ?>"></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($allow_comments): ?>
    <div class="comments">
        <?php if (count($comments)): ?>
        <h3><?php echo count($comments); ?> <?php echo $this->journal2->settings->get('blog_comment_text', 'Comment(s)'); ?></h3>
        <?php endif; ?>
        <?php foreach ($comments as $comment): ?>
        <div class="comment" data-comment-id="<?php echo $comment['comment_id']; ?>" data-has-form="false">
            <a name="c<?php echo $comment['comment_id']; ?>"></a>
            <?php echo Journal2Utils::gravatar($comment['email'], $default_author_image, 70, ''); ?>

            <div class="main-comment">
                <div class="user-name"><?php echo $comment['name']; ?>:</div>
                <div class="user-date"><?php echo date($date_format_short, strtotime($comment['date'])); ?>, <span class="user-time"><?php echo date($time_format, strtotime($comment['date'])); ?></span><?php if ($comment['website']): ?>, <span class="user-site"><a href="<?php echo $comment['website']; ?>" target="_blank" rel="nofollow"><?php echo preg_replace('#^https?://#', '', $comment['website']); ?></a></span><?php endif; ?></div>
                <a class="button reply-btn"><?php echo $this->journal2->settings->get('blog_reply_button', 'Reply'); ?></a>
                <p><?php echo $comment['comment']; ?></p>
            </div>

            <?php foreach ($comment['replies'] as $reply): ?>
            <div class="reply">
                <?php echo Journal2Utils::gravatar($reply['email'], $default_author_image, 70, ''); ?>

                <div class="comment">
                    <div class="user-name"><?php echo $reply['name']; ?>:</div>
                    <div class="user-date"><?php echo date($date_format_short, strtotime($reply['date'])); ?>, <span class="user-time"><?php echo date($time_format, strtotime($reply['date'])); ?></span></div>
                    <p><?php echo $reply['comment']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <div class="post-comment">
            <h3><?php echo $this->journal2->settings->get('blog_leave_comment_text', 'Leave a Comment'); ?></h3>
            <div class="comment-form">
                <form>
                    <div>
                        <input type="text" name="name" placeholder="<?php echo $this->journal2->settings->get('blog_form_name', 'Name'); ?>" value="<?php echo $default_name; ?>" />
                        <input type="text" name="email" placeholder="<?php echo $this->journal2->settings->get('blog_form_email', 'Email'); ?>" value="<?php echo $default_email; ?>" />
                        <input type="text" name="website" placeholder="<?php echo $this->journal2->settings->get('blog_form_website', 'Website'); ?>"/>
                    </div>
                    <div>
                        <textarea name="comment" placeholder="<?php echo $this->journal2->settings->get('blog_form_comment', 'Comment'); ?>"></textarea>
                    </div>
                    <a class="button comment-submit"><?php echo $this->journal2->settings->get('blog_form_submit', 'Submit'); ?></a>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($related_products): ?>
    <div class="box related-products <?php echo $carousel ? 'journal-carousel' : ''; ?> <?php echo $carousel && $this->journal2->settings->get('related_products_carousel_arrows') === 'top' ? 'arrows-top' : ''; ?>">
        <div>
            <div class="box-heading"><?php echo $tab_related; ?></div>
            <div class="box-content">
            <?php if ($carousel): ?>
            <div class="swiper">
            <div class="swiper-container" <?php echo $this->journal2->settings->get('rtl') ? 'dir="rtl"' : ''; ?>>
            <div class="swiper-wrapper">
            <?php endif; ?>
                <?php foreach ($related_products as $product) { ?>
                <div class="product-grid-item <?php echo $grid_classes; ?> <?php echo $carousel ? 'swiper-slide' : ''; ?> display-<?php echo $this->journal2->settings->get('product_grid_wishlist_icon_display'); ?> <?php echo $this->journal2->settings->get('product_grid_button_block_button'); ?>">
                    <div class="product-wrapper">
                        <?php if ($product['thumb']) { ?>
                        <div class="image <?php echo $this->journal2->settings->get('show_countdown', 'never') !== 'never' && isset($product['date_end']) && $product['date_end'] ? 'has-countdown' : ''; ?>">
                            <a href="<?php echo $product['href']; ?>" <?php if(isset($product['thumb2']) && $product['thumb2']): ?> class="has-second-image" style="background: url('<?php echo $product['thumb2']; ?>') no-repeat;" <?php endif; ?>>
                            <img class="first-image" src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
                            </a>
                            <?php if (isset($product['labels']) && is_array($product['labels'])): ?>
                            <?php foreach ($product['labels'] as $label => $name): ?>
                            <span class="label-<?php echo $label; ?>"><b><?php echo $name; ?></b></span>
                            <?php endforeach; ?>
                            <?php if($this->journal2->settings->get('product_grid_wishlist_icon_position') === 'image' && $this->journal2->settings->get('product_grid_wishlist_icon_display', '') === 'icon'): ?>
                            <div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_wishlist; ?>"><i class="wishlist-icon"></i><span class="button-wishlist-text"><?php echo $button_wishlist;?></span></a></div>
                            <div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_compare; ?>"><i class="compare-icon"></i><span class="button-compare-text"><?php echo $button_compare;?></span></a></div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <?php } ?>
                        <div class="product-details">
                            <div class="caption">
                            <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
                            <?php if ($product['price']) { ?>
                            <div class="price">
                                <?php if (!$product['special']) { ?>
                                <?php echo $product['price']; ?>
                                <?php } else { ?>
                                <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new" <?php echo isset($product['date_end']) && $product['date_end'] ? "data-end-date='{$product['date_end']}'" : ""; ?>><?php echo $product['special']; ?></span>
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
                                <a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button hint--top" data-hint="<?php echo $button_cart; ?>"><i class="button-left-icon"></i><span class="button-cart-text"><?php echo $button_cart; ?></span><i class="button-right-icon"></i></a>
                            </div>
                            <?php endif; ?>
                            <div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_wishlist; ?>"><i class="wishlist-icon"></i><span class="button-wishlist-text"><?php echo $button_wishlist;?></span></a></div>
                            <div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_compare; ?>"><i class="compare-icon"></i><span class="button-compare-text"><?php echo $button_compare;?></span></a></div>
                        </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            <?php if ($carousel): ?>
            </div>
            </div>
                <?php if ($this->journal2->settings->get('related_products_carousel') && $this->journal2->settings->get('related_products_carousel_arrows') !== 'none'): ?>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                <?php endif; ?>
            <?php endif; ?>
            </div>
            <?php if ($this->journal2->settings->get('related_products_carousel') && $this->journal2->settings->get('related_products_carousel_bullets')): ?>
                <div class="swiper-pagination"></div>
            <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php echo $content_bottom; ?>
</div>
<?php if ($carousel): ?>
<?php
    $grid = Journal2Utils::getItemGrid($this->journal2->settings->get('config_blog_settings.related_products_per_row.value'), $this->journal2->settings->get('site_width', 1024), $this->journal2->settings->get('config_columns_count', 0));
    $grid = array(
        array(0, (int)$grid['xs']),
        array(470, (int)$grid['sm']),
        array(760, (int)$grid['md']),
        array(980, (int)$grid['lg']),
        array(1100, (int)$grid['xl'])
    );
?>
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
            spaceBetween: parseInt('<?php echo $this->journal2->settings->get('product_grid_item_spacing', '15'); ?>', 10),
            pagination: <?php echo $this->journal2->settings->get('related_products_carousel_bullets') ? '$(\'.related-products .swiper-pagination\')' : 'false'; ?>,
            paginationClickable: true,
            nextButton: <?php echo $this->journal2->settings->get('related_products_carousel_arrows') !== 'none' ? '$(\'.related-products .swiper-button-next\')' : 'false'; ?>,
            prevButton: <?php echo $this->journal2->settings->get('related_products_carousel_arrows') !== 'none' ? '$(\'.related-products .swiper-button-prev\')' : 'false'; ?>,
            autoplay: <?php echo $this->journal2->settings->get('related_products_carousel_autoplay') > 0 ? 4000 : 'false'; ?>,
            autoplayStopOnHover: <?php echo $this->journal2->settings->get('related_products_carousel_pause_on_hover') ? 'true' : 'false'; ?>,
            speed: 400,
            touchEventsTarget: <?php echo $this->journal2->settings->get('related_products_carousel_touchdrag')  ? '\'container\'' : 'false'; ?>,
        };

        $('.related-products .swiper-container').swiper(opts);
    })();
</script>
<?php endif; ?>
<script>
    function generateComment($form, cls, $appendTo, callback) {
        $form.find('.has-error').removeClass('has-error');
        $.post('index.php?route=journal2/blog/comment&post_id=<?php echo $post_id; ?>', $form.serializeArray(), function (response) {
            if (response.status === 'success') {

                if (response.data) {
                    var html = '';
                    html += '<div class="' + cls + '" data-comment-id="' + response.data.comment_id + '" data-has-form="false">';
                    html += response.data.avatar;
                    html += '<div class="main-comment">';
                    html += '<div class="user-name">' + response.data.name + ':</div>';
                    html += '<div class="user-date">' + response.data.date + ', <span class="user-time">' + response.data.time + '</span>';
                    if (response.data.website) {
                        html += ', <span class="user-site"><a href="' + response.data.href + '" target="_blank">' + response.data.website + '</a></span>';
                    }
                    html += '</div>';
                    html += '<a class="button reply-btn"><?php echo $this->journal2->settings->get('blog_reply_button', 'Reply'); ?></a>';
                    html += '<p>' + response.data.comment + '</p>';
                    html += '</div>';

                    $appendTo.before(html);
                }

                callback && callback(response.message);
            }

            if (response.status === 'error') {
                $.each(response.errors, function (i, field) {
                    $form.find('[name="' + field + '"]').addClass('has-error');
                });
            }
        }, 'json');
    }

    $('.reply-btn').live('click', function () {
        var $comment = $(this).closest('.comment');
        if ($comment.attr('data-has-form') === 'false') {
            var $form = $('.post-comment form').clone();
            $form.find('.has-error').removeClass('has-error');
            $form.append('<input type="hidden" name="parent_id" value="' + $comment.attr('data-comment-id') + '" />');
            $form.find('a').removeClass('comment-submit').addClass('reply-submit');
            $comment.append('<div class="reply-form"><h3><?php echo $this->journal2->settings->get('blog_leave_reply_text', 'Leave a Reply'); ?></h3><div class="comment-form"><div><form>' + $form.html() + '</form></div></div></div>');
            $comment.attr('data-has-form', 'true');
        } else {
            $comment.find('.reply-form').remove();
            $comment.attr('data-has-form', 'false');
        }
    });


    $('form .comment-submit').live('click', function () {
        var $form = $(this).closest('form');
        var $comment_form = $('.post-comment');
        generateComment($form, 'comment', $comment_form, function (message) {
            $comment_form.before('<div class="success">' + message + '</div>');
            setTimeout(function () {
                $('.comments .success').fadeOut(400);
            }, 2000);
            $form[0].reset();
        });
    });

    $('form .reply-submit').live('click', function () {
        var $form = $(this).closest('form');
        var $reply_form = $(this).closest('.comment').find('.reply-form');
        generateComment($form, 'reply', $reply_form, function (message) {
            $reply_form.before('<div class="success">' + message + '</div>');
            setTimeout(function () {
                $('.comments .success').fadeOut(400);
            }, 2000);
            $form.closest('.comment').attr('data-has-form', 'false');
            $reply_form.remove();
        });
    });
</script>
</div>
<?php echo $footer; ?>
