<?php echo $header; ?>
<div id="container" class="container j-container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="<?php echo $breadcrumb['href']; ?>" itemprop="url"><span itemprop="title"><?php echo $breadcrumb['text']; ?></span></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?><?php echo $column_right; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>">
      <h1 class="heading-title"><?php echo $heading_title; ?></h1>
      <?php echo $content_top; ?>
      <?php if ($thumb || $description) { ?>
      <div class="category-info">
        <?php if ($thumb) { ?>
        <div class="image"><img width="<?php echo $this->journal2->settings->get('config_image_width'); ?>" height="<?php echo $this->journal2->settings->get('config_image_height'); ?>" src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" /></div>
        <?php } ?>
        <?php if ($description) { ?>
        <?php echo $description; ?>
        <?php } ?>
      </div>
      <?php } ?>
      <?php if($this->journal2->settings->get('refine_category') === 'grid' && $this->journal2->settings->get('refine_category_images', array())): ?>
      <div class="refine-images">
        <?php foreach ($this->journal2->settings->get('refine_category_images', array()) as $category): ?>
        <div class="refine-image <?php echo Journal2Utils::getProductGridClasses($this->journal2->settings->get('refine_category_images_per_row'), $this->journal2->settings->get('site_width', 1024), $this->journal2->settings->get('config_columns_count')); ?>">
          <a href="<?php echo $category['href']; ?>"><img style="display: block" width="<?php echo $this->journal2->settings->get('refine_image_width', 175); ?>" height="<?php echo $this->journal2->settings->get('refine_image_height', 175); ?>" src="<?php echo $category['thumb']; ?>" alt="<?php echo $category['name']; ?>"/><span class="refine-category-name"><?php echo $category['name']; ?></span></a>
        </div>
        <?php endforeach; ?>
        <script>
            if (!Journal.isFlexboxSupported) {
                Journal.equalHeight($(".refine-images .refine-image"), '.refine-category-name');
            }
        </script>
      </div>
      <?php endif; ?>
      <?php if($this->journal2->settings->get('refine_category') === 'carousel' && $this->journal2->settings->get('refine_category_images', array())): ?>
        <div id="refine-images">
          <div class="swiper">
          <div class="swiper-container" <?php echo $this->journal2->settings->get('rtl') ? 'dir="rtl"' : ''; ?>>
            <div class="swiper-wrapper">
              <?php foreach ($this->journal2->settings->get('refine_category_images', array()) as $category): ?>
                <div class="refine-image swiper-slide <?php echo Journal2Utils::getProductGridClasses($this->journal2->settings->get('refine_category_images_per_row'), $this->journal2->settings->get('site_width', 1024), $this->journal2->settings->get('config_columns_count')); ?>">
                  <a href="<?php echo $category['href']; ?>"><img style="display: block" width="<?php echo $this->journal2->settings->get('refine_image_width', 175); ?>" height="<?php echo $this->journal2->settings->get('refine_image_height', 175); ?>" src="<?php echo $category['thumb']; ?>" alt="<?php echo $category['name']; ?>"/><span class="refine-category-name"><?php echo $category['name']; ?></span></a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="swiper-pagination"></div>
          </div>
        </div>
      <?php
      $grid = Journal2Utils::getItemGrid($this->journal2->settings->get('refine_category_images_per_row'), $this->journal2->settings->get('site_width', 1024), $this->journal2->settings->get('config_columns_count'));
      $grid = array(
        array(0, (int)$grid['xs']),
        array(470, (int)$grid['sm']),
        array(760, (int)$grid['md']),
        array(980, (int)$grid['lg']),
        array(1100, (int)$grid['xl']),
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
            spaceBetween: parseInt('<?php echo $this->journal2->settings->get('refine_grid_item_spacing', '15'); ?>', 10),
            pagination: $('#refine-images .swiper-pagination'),
            paginationClickable: true,
            nextButton: $('#refine-images .swiper-button-next'),
            prevButton: $('#refine-images .swiper-button-prev'),
            autoplay: <?php echo $this->journal2->settings->get('refine_carousel_autoplay') > 0 ? 4000 : 'false'; ?>,
            autoplayStopOnHover: <?php echo $this->journal2->settings->get('refine_carousel_pause_on_hover') > 0 ? 4000 : 'false'; ?>,
            speed: 400,
            touchEventsTarget: <?php echo $this->journal2->settings->get('refine_carousel_touchdrag')  ? '\'container\'' : 'false'; ?>,
          };

          $('#refine-images .swiper-container').swiper(opts);
        })();
      </script>
      <?php endif; ?>
      <?php if($this->journal2->settings->get('refine_category') === 'text'): ?>
      <?php if ($categories) { ?>
      <h2 class="refine"><?php echo $text_refine; ?></h2>
      <div class="category-list">
        <ul>
          <?php foreach ($categories as $category) { ?>
          <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>
      <?php endif; ?>

      <?php if ($products) { ?>

      <div class="product-filter">
        <div class="display">
          <a onclick="Journal.gridView()" class="grid-view"><?php echo $this->journal2->settings->get("category_grid_view_icon", $button_grid); ?></a>
          <a onclick="Journal.listView()" class="list-view"><?php echo $this->journal2->settings->get("category_list_view_icon", $button_list); ?></a>
        </div>
        <div class="product-compare"><a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a></div>
        <div class="limit"><b><?php echo $text_limit; ?></b>
          <select onchange="location = this.value;">
            <?php foreach ($limits as $limits) { ?>
            <?php if ($limits['value'] == $limit) { ?>
            <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
            <?php } ?>
            <?php } ?>
          </select>
        </div>
        <div class="sort"><b><?php echo $text_sort; ?></b>
          <select onchange="location = this.value;">
            <?php foreach ($sorts as $sorts) { ?>
            <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
            <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
            <?php } ?>
            <?php } ?>
          </select>
        </div>
      </div>

      <div class="row main-products product-grid" data-grid-classes="<?php echo $this->journal2->settings->get('product_grid_classes'); ?> display-<?php echo $this->journal2->settings->get('product_grid_wishlist_icon_display'); ?> <?php echo $this->journal2->settings->get('product_grid_button_block_button'); ?>">
        <?php foreach ($products as $product) { ?>
        <div class="product-grid-item <?php echo $this->journal2->settings->get('product_grid_classes'); ?>">
          <div class="product-thumb product-wrapper <?php echo isset($product['labels']) && is_array($product['labels']) && isset($product['labels']['outofstock']) ? 'outofstock' : ''; ?>">
            <div class="image <?php echo $this->journal2->settings->get('show_countdown', 'never') !== 'never' && isset($product['date_end']) && $product['date_end'] ? 'has-countdown' : ''; ?>">
              <a href="<?php echo $product['href']; ?>" <?php if(isset($product['thumb2']) && $product['thumb2']): ?> class="has-second-image" style="background: url('<?php echo $product['thumb2']; ?>') no-repeat;" <?php endif; ?>>
                  <img class="lazy first-image" width="<?php echo $this->journal2->settings->get('config_image_width'); ?>" height="<?php echo $this->journal2->settings->get('config_image_height'); ?>" src="<?php echo $this->journal2->settings->get('product_dummy_image'); ?>" data-src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
              </a>
              <?php if (isset($product['labels']) && is_array($product['labels'])): ?>
              <?php foreach ($product['labels'] as $label => $name): ?>
              <span class="label-<?php echo $label; ?>"><b><?php echo $name; ?></b></span>
              <?php endforeach; ?>
              <?php endif; ?>
              <?php if($this->journal2->settings->get('product_grid_wishlist_icon_position') === 'image' && $this->journal2->settings->get('product_grid_wishlist_icon_display', '') === 'icon'): ?>
                  <div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_wishlist; ?>"><i class="wishlist-icon"></i><span class="button-wishlist-text"><?php echo $button_wishlist;?></span></a></div>
                  <div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');" class="hint--top" data-hint="<?php echo $button_compare; ?>"><i class="compare-icon"></i><span class="button-compare-text"><?php echo $button_compare;?></span></a></div>
              <?php endif; ?>
            </div>
            <div class="product-details">
              <div class="caption">
                <h4 class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
                <p class="description"><?php echo $product['description']; ?></p>
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
                <?php if ($product['price']) { ?>
                <p class="price">
                  <?php if (!$product['special']) { ?>
                  <?php echo $product['price']; ?>
                  <?php } else { ?>
                  <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new" <?php echo isset($product['date_end']) && $product['date_end'] ? "data-end-date='{$product['date_end']}'" : ""; ?>><?php echo $product['special']; ?></span>
                  <?php } ?>
                  <?php if ($product['tax']) { ?>
                  <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                  <?php } ?>
                </p>
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
      </div>
      <div class="row pagination">
        <div class="col-sm-6 text-left links"><?php echo $pagination; ?></div>
        <div class="col-sm-6 text-right results"><?php echo $results; ?></div>
      </div>
      <?php } ?>
      <?php if (!$categories && !$products) { ?>
      <p><?php echo $text_empty; ?></p>
      <div class="buttons">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary button"><?php echo $button_continue; ?></a></div>
      </div>
      <?php } ?>
      <?php echo $content_bottom; ?></div>
    </div>
    <script>Journal.applyView('<?php echo $this->journal2->settings->get("product_view", "grid"); ?>');</script>
    <?php if ($this->journal2->settings->get('show_countdown', 'never') !== 'never'): ?>
    <script>Journal.enableCountdown();</script>
    <?php endif; ?>
</div>
<?php echo $footer; ?>
