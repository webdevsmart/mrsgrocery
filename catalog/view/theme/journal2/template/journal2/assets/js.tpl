Journal.notificationTimer = parseInt('<?php echo $this->journal2->settings->get('notification_hide'); ?>', 10);

Journal.quickviewText = '<?php echo $this->journal2->settings->get('quickview_button_text'); ?>';

Journal.scrollToTop = parseInt('<?php echo $this->journal2->settings->get('scroll_to_top', '1'); ?>', 10);

Journal.searchInDescription = <?php echo $this->journal2->settings->get('search_autocomplete_include_description', '1') === '1' ? 'true' : 'false'; ?>;

Journal.galleryZoom = <?php echo $this->journal2->settings->get('pp_gallery_zoom', 'on') === 'on' ? 'true' : 'false'; ?>;
Journal.galleryThumb = <?php echo $this->journal2->settings->get('pp_gallery_thumbs', 'on') === 'on' ? 'true' : 'false'; ?>;
Journal.galleryThumbHide = <?php echo $this->journal2->settings->get('pp_gallery_thumbs_hide', 'on') === 'off' ? 'true' : 'false'; ?>;
Journal.galleryThumbWidth = parseInt('<?php echo $this->journal2->settings->get('pp_gallery_thumbs_width', '100'); ?>', 10);
Journal.galleryThumbHeight = parseInt('<?php echo $this->journal2->settings->get('pp_gallery_thumbs_height', '100'); ?>', 10);
Journal.galleryThumbSpacing = parseInt('<?php echo $this->journal2->settings->get('pp_gallery_thumbs_spacing', '7'); ?>', 10);
Journal.galleryBarsDelay = parseInt('<?php echo $this->journal2->settings->get('hide_gallery_bars_after', '5000'); ?>', 10);

Journal.infiniteScrollStatus = <?php echo $this->journal2->settings->get('infinite_scroll', '1') ? 'true' : 'false'; ?>;
Journal.infiniteScrollLoadingText = '<?php echo str_replace("'","\\'", $this->journal2->settings->get('infinite_scroll_loading_text', 'Loading')); ?>';
Journal.infiniteScrollNoMoreItemsText = '<?php echo str_replace("'","\\'", $this->journal2->settings->get('infinite_scroll_no_more_items_text', 'No items left.')); ?>';
Journal.infiniteScrollLoadMoreItemsText = '<?php echo str_replace("'","\\'", $this->journal2->settings->get('infinite_scroll_load_more_items_text', 'Load more')); ?>';
Journal.infiniteScrollLoadMoreItemsOffset = parseInt('<?php echo str_replace("'","\\'", $this->journal2->settings->get('infinite_scroll_load_more_items_offset', '0')); ?>', 10);
Journal.hasCountdownEnabled = <?php echo $this->journal2->settings->get('show_countdown', 'never') !== 'never' ? 'true' : 'false'; ?>;
Journal.hasStickyScroll = <?php echo $this->journal2->settings->get('sticky_header_hide', '1'); ?>;

Journal.BASE_HREF = 'url(' + $('base').attr('href') + ')';

$(document).ready(function () {

if ($('html').hasClass('filter-on-mobile')) {
    $('.journal-sf').before($('<div class="open-filter" style="display:none"><?php echo $this->journal2->settings->get('filter_mobile_text', 'Product Filters'); ?></div>'));

    $('.open-filter').on('click', function () {
        var $filter = $('.journal-sf .box').first();
        if ($filter.hasClass('is-visible')) {
            $('.side-column .journal-sf .box').slideUp(300);
            $('.sf-reset').fadeOut(200);
            $filter.removeClass('is-visible');
        } else {
            $('.side-column .journal-sf .box').slideDown(300);
            $('.sf-reset').fadeIn(200);
            $filter.addClass('is-visible');
        }
    });
}

Journal.productPage();

$(".product-page .rating a").click(function() {
    $('html, body').animate({
        scrollTop: $("#tabs").offset().top - 50
    }, 600);
});

<?php /* enable sticky header */ ?>
<?php if (!$this->journal2->html_classes->hasClass('mobile') && (($this->journal2->settings->get('sticky_header', '1') === '1' && Journal2Utils::getDevice() === 'desktop') || ($this->journal2->settings->get('sticky_header_tablet', '1') === '1' && Journal2Utils::getDevice() === 'tablet'))):?>
Journal.enableStickyHeader(<?php echo $this->journal2->settings->get('boxed_top_spacing', 0);?>);
<?php endif; ?>

<?php if($this->journal2->settings->get('mobile_menu_cart_display', 'on') === 'off'): ?>
$('header').addClass('menu-cart-off');
<?php endif; ?>

<?php if($this->journal2->settings->get('sticky_menu_phone_2', 'on') === 'on'): ?>
$(window).scroll( function() {
    if ($(window).scrollTop() > $('.journal-cart').offset().top){
        $('header').addClass('phone-sticky-cart');
    }
    else{
        $('header').removeClass('phone-sticky-cart');
    }

    if ($(window).scrollTop() > $('.journal-cart').offset().top + 40){
        $('header').addClass('phone-sticky-menu');
    }
    else{
        $('header').removeClass('phone-sticky-menu');
    }
});
<?php endif; ?>


<?php if($this->journal2->settings->get('product_page_tabs_position', 'on') === 'off'): ?>
$('.product-tabs').insertAfter('.product-info');
<?php endif; ?>

<?php if($this->journal2->settings->get('product_page_auto_update_price', '1') === '1'): ?>
Journal.enableProductOptions();
Journal.updatePrice = true;
<?php endif; ?>

<?php if (!Journal2Cache::$mobile_detect->isMobile()): ?>
Journal.enableSideBlocks();
<?php endif; ?>

<?php /* enable search autocomplete */ ?>
<?php if ((Journal2Utils::getDevice() === 'desktop' && $this->journal2->settings->get('search_autocomplete', '1') == '1') ||
    (Journal2Utils::getDevice() === 'tablet' && $this->journal2->settings->get('search_autocomplete_tablet', '1') == '1') ||
    (Journal2Utils::getDevice() === 'phone' && $this->journal2->settings->get('search_autocomplete_phone', '1') == '1')): ?>
Journal.searchAutoComplete();
<?php endif; ?>

<?php /* enable quickview */ ?>
<?php if ($this->journal2->settings->get('quickview_status') == '1' && !Journal2Cache::$mobile_detect->isMobile() && !Journal2Cache::$mobile_detect->isTablet() && !$this->journal2->html_classes->hasClass("ie8")): ?>
Journal.enableQuickView();
Journal.quickViewStatus = true;
<?php else: ?>
Journal.quickViewStatus = false;
<?php endif; ?>

<?php /* enable cloudzoom */ ?>
<?php if ($this->journal2->settings->get('product_page_cloud_zoom') == '1' && !Journal2Cache::$mobile_detect->isMobile() && !Journal2Cache::$mobile_detect->isTablet()): ?>
if ($('html').hasClass('product-page') || $('html').hasClass('quickview')) {
    Journal.enableCloudZoom('<?php echo $this->journal2->settings->get('product_page_cloud_zoom_inner', '0') === '0' ? 'standard' : 'inner' ?>');
}
<?php endif; ?>

<?php /* enable product page gallery */ ?>
<?php if ($this->journal2->settings->get('product_page_gallery') === '1'):?>
Journal.productPageGallery();
<?php else: ?>
$('.product-info .image a').css('cursor','default').click(function(){
    return false;
});
<?php endif; ?>


<?php if ((Journal2Utils::getDevice() === 'desktop' && $this->journal2->settings->get('scroll_top', '1') == '1') ||
    (Journal2Utils::getDevice() === 'tablet' && $this->journal2->settings->get('scroll_top_tablet', '1') == '1') ||
    (Journal2Utils::getDevice() === 'phone' && $this->journal2->settings->get('scroll_top_phone', '1') == '1')): ?>
$(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
            $('.scroll-top').fadeIn(200);
        } else {
            $('.scroll-top').fadeOut(200);
    }
});

$('.scroll-top').click(function () {
    $('html, body').animate({scrollTop: 0}, 700);
});
<?php endif; ?>



$('#top-modules > .hide-on-mobile').parent().addClass('hide-on-mobile');
$('#bottom-modules > .hide-on-mobile').parent().addClass('hide-on-mobile');

$('#top-modules .gutter-on').parent().addClass('gutter');
$('#bottom-modules .gutter-on').parent().addClass('gutter');

if (Journal.infiniteScrollStatus && $('.main-products').length) {
    Journal.infiniteScroll();
}

$(window).resize();

Journal.init();
});

/* Custom JS */
<?php echo $this->journal2->settings->get('custom_js'); ?>
