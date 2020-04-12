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
    <div id="content" class="product-page-content" itemscope itemtype="http://schema.org/Product">
      <?php if ($this->journal2->settings->get('product_page_title_position', 'top') === 'top'): ?>
      <h1 class="heading-title" itemprop="name"><?php echo $heading_title; ?></h1>
      <?php endif; ?>
      <?php echo $content_top; ?>
      <div class="row product-info <?php echo $this->journal2->settings->get('split_ratio'); ?>">
        <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } elseif ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-8'; ?>
        <?php } ?>
        <div class="left">
          <?php if ($thumb) { ?>
          <div class="image">
            <?php if (isset($labels) && is_array($labels)): ?>
            <?php foreach ($labels as $label => $name): ?>
            <span class="label-<?php echo $label; ?>"><b><?php echo $name; ?></b></span>
            <?php endforeach; ?>
            <?php endif; ?>
            <a href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>"><img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image" data-largeimg="<?php echo $popup; ?>" itemprop="image"  /></a>
          </div>
          <?php if($this->journal2->settings->get('product_page_gallery')): ?>
          <div class="gallery-text"><span><?php echo $this->journal2->settings->get('product_page_gallery_text'); ?></span></div>
          <?php endif; ?>

          <?php } ?>
          <?php if ($images) { ?>
          <div id="product-gallery" class="image-additional <?php echo $this->journal2->settings->get('product_page_gallery_carousel') ? 'journal-carousel' : 'image-additional-grid'; ?>">
            <?php if ($this->journal2->settings->get('product_page_gallery_carousel')): ?>
            <div class="swiper">
            <div class="swiper-container" <?php echo $this->journal2->settings->get('rtl') ? 'dir="rtl"' : ''; ?>>
            <div class="swiper-wrapper">
            <?php endif; ?>
                <?php if ($thumb) { ?>
                <a class="swiper-slide" style="<?php echo $this->journal2->settings->get('product_page_gallery_carousel') ? ('width: ' . 100 / $this->journal2->settings->get('product_page_additional_width', 5) . '%') : ''; ?>" href="<?php echo isset($popup_fixed) ? $popup_fixed : $popup; ?>" title="<?php echo $heading_title; ?>"><img src="<?php echo isset($thumb_fixed) ? $thumb_fixed : $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>"/></a>
                <?php } ?>
                <?php foreach ($images as $image) { ?>
                <a class="swiper-slide" style="<?php echo $this->journal2->settings->get('product_page_gallery_carousel') ? ('width: ' . 100 / $this->journal2->settings->get('product_page_additional_width', 5) . '%') : ''; ?>" href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" itemprop="image"/></a>
                <?php } ?>
            <?php if ($this->journal2->settings->get('product_page_gallery_carousel')): ?>
            </div>
            </div>
            <?php if ($this->journal2->settings->get('product_page_gallery_carousel_arrows') != 'never'): ?>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
          <?php if ($this->journal2->settings->get('product_page_gallery_carousel')): ?>
          <script>
            (function () {
                var opts = {
                    slidesPerView: parseInt('<?php echo $this->journal2->settings->get('product_page_additional_width', 5) ?>', 10),
                    slidesPerGroup: parseInt('<?php echo $this->journal2->settings->get('product_page_additional_width', 5) ?>', 10),
                    spaceBetween: parseInt('<?php echo $this->journal2->settings->get('product_page_additional_spacing', 10) ?>', 10),
                    <?php if ($this->journal2->settings->get('product_page_gallery_carousel_arrows') != 'never'): ?>
                    nextButton: $('#product-gallery .swiper-button-next'),
                    prevButton: $('#product-gallery .swiper-button-prev'),
                    <?php endif; ?>
                    autoplay: <?php echo $this->journal2->settings->get('product_page_gallery_carousel_autoplay') ? (int)$this->journal2->settings->get('product_page_gallery_carousel_transition_delay', 4000) : 'false'; ?>,
                    speed: <?php echo (int)$this->journal2->settings->get('product_page_gallery_carousel_transition_speed', 400); ?>,
                    touchEventsTarget: <?php echo $this->journal2->settings->get('product_page_gallery_carousel_touchdrag')  ? '\'container\'' : 'false'; ?>,
                };

                $('#product-gallery .swiper-container').swiper(opts);
            })();
          </script>
          <?php endif; ?>
          <?php } ?>
          <?php foreach ($this->journal2->settings->get('additional_product_description_image', array()) as $tab): ?>
          <div class="journal-custom-tab">
            <?php if ($tab['has_icon']): ?>
            <div class="block-icon block-icon-left" style="<?php echo $tab['icon_css']; ?>"><?php echo $tab['icon']; ?></div>
            <?php endif; ?>
            <?php if ($tab['name']): ?>
            <h3><?php echo $tab['name']; ?></h3>
            <?php endif; ?>
            <?php echo $tab['content']; ?>
          </div>
          <?php endforeach; ?>
          <div class="image-gallery" style="display: none !important;">
            <?php if ($thumb) { ?>
            <a href="<?php echo $popup; ?>" data-original="<?php echo isset($original) ? $original : $popup; ?>" title="<?php echo $heading_title; ?>" class="swipebox"><img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
            <?php } ?>
            <?php if ($images) { ?>
            <?php foreach ($images as $image) { ?>
            <a href="<?php echo $image['popup']; ?>" data-original="<?php echo isset($image['original']) ? $image['original'] : $image['popup']; ?>" title="<?php echo $heading_title; ?>" class="swipebox"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
            <?php } ?>
            <?php } ?>
          </div>
          <?php if ($this->journal2->settings->get('share_buttons_status') && (!Journal2Cache::$mobile_detect->isMobile() || (Journal2Cache::$mobile_detect->isMobile() && !$this->journal2->settings->get('share_buttons_disable_on_mobile', 1))) && $this->journal2->settings->get('share_buttons_position') === 'left' && count($this->journal2->settings->get('config_share_buttons', array()))): ?>
          <div class="social share-this <?php echo $this->journal2->settings->get('share_buttons_disable_on_mobile', 1) ? 'hide-on-mobile' : ''; ?>">
            <div class="social-loaded">
              <script type="text/javascript">var switchTo5x=true;</script>
              <script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
              <script type="text/javascript">stLight.options({publisher: "<?php echo $this->journal2->settings->get('share_buttons_account_key'); ?>", doNotHash: true, doNotCopy: true, hashAddressBar: false});</script>
              <?php foreach ($this->journal2->settings->get('config_share_buttons', array()) as $item): ?>
              <span class="<?php echo $item['class'] . $this->journal2->settings->get('share_buttons_style'); ?>" displayText="<?php echo $this->journal2->settings->get('share_buttons_style') ? $item['name'] : ''; ?>"></span>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
          <meta itemprop="description" content="<?php echo $this->journal2->settings->get('product_description'); ?>" />
          <div class="product-tabs">
            <?php if ($this->journal2->settings->get('share_buttons_status') && (!Journal2Cache::$mobile_detect->isMobile() || (Journal2Cache::$mobile_detect->isMobile() && !$this->journal2->settings->get('share_buttons_disable_on_mobile', 1))) && $this->journal2->settings->get('share_buttons_position') === 'bottom' && count($this->journal2->settings->get('config_share_buttons', array()))): ?>
            <div class="social share-this <?php echo $this->journal2->settings->get('share_buttons_disable_on_mobile', 1) ? 'hide-on-mobile' : ''; ?>">
              <div class="social-loaded">
                <script type="text/javascript">var switchTo5x=true;</script>
                <script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
                <script type="text/javascript">stLight.options({publisher: "<?php echo $this->journal2->settings->get('share_buttons_account_key'); ?>", doNotHash: true, doNotCopy: true, hashAddressBar: false});</script>
                <?php foreach ($this->journal2->settings->get('config_share_buttons', array()) as $item): ?>
                <span class="<?php echo $item['class'] . $this->journal2->settings->get('share_buttons_style'); ?>" displayText="<?php echo $this->journal2->settings->get('share_buttons_style') ? $item['name'] : ''; ?>"></span>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
          <ul id="tabs" class="nav nav-tabs htabs">
            <?php $is_active = true; ?>
            <?php if (trim($description) && !$this->journal2->settings->get('hide_product_description')) { ?>
            <li <?php if ($is_active) { echo 'class="active"'; $is_active = false; } ;?>><a href="#tab-description" data-toggle="tab"><?php echo $tab_description; ?></a></li>
            <?php } ?>
            <?php if ($attribute_groups) { ?>
            <li <?php if ($is_active) { echo 'class="active"'; $is_active = false; } ;?>><a href="#tab-specification" data-toggle="tab"><?php echo $tab_attribute; ?></a></li>
            <?php } ?>
            <?php if ($review_status) { ?>
            <li <?php if ($is_active) { echo 'class="active"'; $is_active = false; } ;?>><a href="#tab-review" data-toggle="tab"><?php echo $tab_review; ?></a></li>
            <?php } ?>
            <?php $index = 0; foreach ($this->journal2->settings->get('additional_product_tabs', array()) as $tab): $index++; ?>
            <li <?php if ($is_active) { echo 'class="active"'; $is_active = false; } ;?>><a href="#additional-product-tab-<?php echo $index; ?>" data-toggle="tab"><?php echo $tab['name']; ?></a></li>
            <?php endforeach; ?>
          </ul>
          <div class="tabs-content">
            <?php $is_active = true; ?>
            <?php if (trim($description) && !$this->journal2->settings->get('hide_product_description')) { ?>
            <div class="tab-pane tab-content <?php if ($is_active) { echo 'active'; $is_active = false; } ;?>" id="tab-description"><?php echo $description; ?></div>
            <?php } ?>
            <?php if ($attribute_groups) { ?>
            <div class="tab-pane tab-content <?php if ($is_active) { echo 'active'; $is_active = false; } ;?>" id="tab-specification">
              <table class="table table-bordered attribute">
                <?php foreach ($attribute_groups as $attribute_group) { ?>
                <thead>
                  <tr>
                    <td colspan="2"><strong><?php echo $attribute_group['name']; ?></strong></td>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($attribute_group['attribute'] as $attribute) { ?>
                  <tr>
                    <td><?php echo $attribute['name']; ?></td>
                    <td><?php echo $attribute['text']; ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
                <?php } ?>
              </table>
            </div>
            <?php } ?>
            <?php if ($review_status) { ?>
            <div class="tab-pane tab-content <?php if ($is_active) { echo 'active'; $is_active = false; } ;?>" id="tab-review" <?php if ($rating): ?>itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"<?php endif; ?>>
                <?php if ($rating): ?>
                <meta itemprop="ratingValue" content="<?php echo $rating; ?>" />
                <meta itemprop="reviewCount" content="<?php echo $this->journal2->settings->get('product_num_reviews'); ?>" />
                <meta itemprop="bestRating" content="5" />
                <meta itemprop="worstRating" content="1" />
                <?php endif; ?>
              <form class="form-horizontal" id="form-review">
                <div id="review"></div>
                <h2 id="review-title"><?php echo $text_write; ?></h2>
                <?php if ($review_guest) { ?>
                <div class="form-group required">
                  <div class="col-sm-12">
                    <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                    <input type="text" name="name" value="<?php echo version_compare(VERSION, '2.2', '<') ? '' : $customer_name; ?>" id="input-name" class="form-control" />
                  </div>
                </div>
                <div class="form-group required">
                  <div class="col-sm-12">
                    <label class="control-label" for="input-review"><?php echo $entry_review; ?></label>
                    <textarea name="text" rows="5" id="input-review" class="form-control"></textarea>
                    <div class="help-block"><?php echo $text_note; ?></div>
                  </div>
                </div>
                <div class="form-group required">
                  <div class="col-sm-12">
                    <label class="control-label"><?php echo $entry_rating; ?></label>
                    &nbsp;&nbsp;&nbsp; <?php echo $entry_bad; ?>&nbsp;
                    <input type="radio" name="rating" value="1" />
                    &nbsp;
                    <input type="radio" name="rating" value="2" />
                    &nbsp;
                    <input type="radio" name="rating" value="3" />
                    &nbsp;
                    <input type="radio" name="rating" value="4" />
                    &nbsp;
                    <input type="radio" name="rating" value="5" />
                    &nbsp;<?php echo $entry_good; ?></div>
                </div>
                <br/>
                <?php if (version_compare(VERSION, '2.0.2', '<')): ?>
                <div class="form-group required">
                  <div class="col-sm-12">
                    <label class="control-label" for="input-captcha"><?php echo $entry_captcha; ?></label>
                    <input type="text" name="captcha" value="" id="input-captcha" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-12"> <img src="index.php?route=tool/captcha" alt="" id="captcha" /> </div>
                </div>
                <?php elseif (version_compare(VERSION, '2.1', '<')): ?>
                <?php if ($site_key) { ?>
                  <div class="form-group">
                    <div class="col-sm-12">
                      <div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>"></div>
                    </div>
                  </div>
                <?php } ?>
                <?php else: ?>
                <?php echo $captcha; ?>
                <?php endif; ?>
                <div class="buttons">
                  <div class="pull-right">
                    <button type="button" id="button-review" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary button"><?php echo $button_continue; ?></button>
                  </div>
                </div>
                <?php } else { ?>
                <?php echo $text_login; ?>
                <?php } ?>
              </form>
            </div>
            <?php } ?>
            <?php $index = 0; foreach ($this->journal2->settings->get('additional_product_tabs', array()) as $tab): $index++; ?>
              <div id="additional-product-tab-<?php echo $index; ?>" class="tab-pane tab-content journal-custom-tab <?php if ($is_active) { echo 'active'; $is_active = false; } ;?>"><?php echo $tab['content']; ?></div>
            <?php endforeach; ?>
          </div>
          </div>
        </div>
        <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } elseif ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-4'; ?>
        <?php } ?>
        <div class="right">
          <?php if ($this->journal2->settings->get('product_page_title_position', 'top') === 'right'): ?>
          <h1 class="heading-title" itemprop="name"><?php echo $heading_title; ?></h1>
          <?php endif; ?>
          <div id="product" class="product-options">
            <?php foreach ($this->journal2->settings->get('additional_product_description_top', array()) as $tab): ?>
            <div class="journal-custom-tab">
              <?php if ($tab['has_icon']): ?>
              <div class="block-icon block-icon-left" style="<?php echo $tab['icon_css']; ?>"><?php echo $tab['icon']; ?></div>
              <?php endif; ?>
              <?php if ($tab['name']): ?>
              <h3><?php echo $tab['name']; ?></h3>
              <?php endif; ?>
              <?php echo $tab['content']; ?>
            </div>
            <?php endforeach; ?>
          <ul class="list-unstyled description">
            <?php if($this->journal2->settings->get('product_views')): ?>
            <li class="product-views-count"><?php echo $this->journal2->settings->get('product_page_options_views_text'); ?>: <?php echo $this->journal2->settings->get('product_views'); ?></li>
            <?php endif; ?>
            <?php if($this->journal2->settings->get('manufacturer_image') == 'on'): ?>
            <li class="brand-logo">
                <a href="<?php echo $manufacturers; ?>" class="brand-image">
                    <img src="<?php echo $manufacturer_image; ?>" width="<?php echo $manufacturer_image_width; ?>" height="<?php echo $manufacturer_image_height; ?>" alt="<?php echo $manufacturer; ?>" />
                </a>
                <?php if(isset($manufacturer_image_name) && $manufacturer_image_name): ?>
                <a href="<?php echo $manufacturers; ?>" class="brand-logo-text">
                    <?php echo $manufacturer_image_name; ?>
                </a>
                <?php endif; ?>
            </li>
            <?php else: ?>
            <?php if ($manufacturer) { ?>
            <li class="p-brand"><?php echo $text_manufacturer; ?> <a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a></li>
            <?php } ?>
            <?php endif; ?>
            <li class="p-model"><?php echo $text_model; ?> <span class="p-model" itemprop="model"><?php echo $model; ?></span></li>
            <?php if ($reward) { ?>
            <li class="p-rewards"><?php echo $text_reward; ?> <span class="p-rewards"><?php echo $reward; ?></span></li>
            <?php } ?>
            <li class="p-stock"><?php echo $text_stock; ?> <span class="journal-stock <?php echo isset($stock_status) ? $stock_status : ''; ?>"><?php echo $stock; ?></span></li>
          </ul>
          <?php if($this->journal2->settings->get('product_sold')): ?>
          <div class="product-sold-count-text"><?php echo $this->journal2->settings->get('product_sold'); ?></div>
          <?php endif; ?>
          <?php if (isset($date_end) && $date_end && $this->journal2->settings->get('show_countdown_product_page', 'on') == 'on'): ?>
          <div class="countdown-wrapper"><div class="expire-text"><?php echo $this->journal2->settings->get('countdown_product_page_title'); ?></div><div class="countdown"></div></div>
          <script>Journal.countdown($('.right .countdown'), '<?php echo $date_end; ?>');</script>
          <?php endif; ?>
          <?php if ($price) { ?>
          <ul class="list-unstyled price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            <meta itemprop="itemCondition" content="http://schema.org/NewCondition" />
            <meta itemprop="priceCurrency" content="<?php echo $this->journal2->settings->get('product_price_currency'); ?>" />
            <meta itemprop="price" content="<?php echo $this->journal2->settings->get('product_price'); ?>" />
            <?php if ($this->journal2->settings->get('product_in_stock') === 'yes'): ?>
            <link itemprop="availability"  href="http://schema.org/InStock" />
            <?php endif; ?>
            <?php if (!$special) { ?>
            <li class="product-price"><?php echo $price; ?></li>
            <?php } else { ?>
            <li class="price-old"><?php echo $price; ?></li>
            <li class="price-new"><?php echo $special; ?></li>
            <?php } ?>
            <?php if ($tax) { ?>
            <li class="price-tax"><?php echo $text_tax; ?> <?php echo $tax; ?></li>
            <?php } ?>
            <?php if ($points) { ?>
            <li class="reward"><small><?php echo $text_points; ?> <?php echo $points; ?></small></li>
            <?php } ?>
            <?php if ($discounts) { ?>
            <?php foreach ($discounts as $discount) { ?>
            <li class="discounts"><?php echo $discount['quantity']; ?><?php echo $text_discount; ?><?php echo $discount['price']; ?></li>
            <?php } ?>
            <?php } ?>
          </ul>
          <?php } ?>
          <?php if ($options) { ?>
            <div class="options <?php echo $this->journal2->settings->get('product_page_options_push_classes'); ?>">
            <h3><?php echo $text_option; ?></h3>
            <?php foreach ($options as $option) { ?>
            <?php if ($option['type'] == 'select') { ?>
            <div class="option form-group<?php echo ($option['required'] ? ' required' : ''); ?> option-<?php echo $option['type']; ?>">
              <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
              <select name="option[<?php echo $option['product_option_id']; ?>]" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control">
                <option value=""><?php echo $text_select; ?></option>
                <?php foreach ($option['product_option_value'] as $option_value) { ?>
                <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                <?php if ($option_value['price']) { ?>
                (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                <?php } ?>
                </option>
                <?php } ?>
              </select>
            </div>
            <?php } ?>
            <?php if ($option['type'] == 'radio') { ?>
            <div class="option form-group<?php echo ($option['required'] ? ' required' : ''); ?> option-<?php echo $option['type']; ?>">
              <label class="control-label"><?php echo $option['name']; ?></label>
              <div id="input-option<?php echo $option['product_option_id']; ?>">
                <?php foreach ($option['product_option_value'] as $option_value) { ?>
                <div class="radio">
                  <label>
                    <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
                    <?php if (version_compare(VERSION, '2.2', '>=') && $option_value['image']) { ?>
                    <img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail" />
                    <?php } ?>
                    <?php echo $option_value['name']; ?>
                    <?php if ($option_value['price']) { ?>
                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                    <?php } ?>
                  </label>
                </div>
                <?php } ?>
              </div>
            </div>
            <?php } ?>
            <?php if ($option['type'] == 'checkbox') { ?>
            <div class="option form-group<?php echo ($option['required'] ? ' required' : ''); ?> option-<?php echo $option['type']; ?>">
              <label class="control-label"><?php echo $option['name']; ?></label>
              <div id="input-option<?php echo $option['product_option_id']; ?>">
                <?php foreach ($option['product_option_value'] as $option_value) { ?>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" />
                    <?php if (version_compare(VERSION, '2.2', '>=') && $option_value['image']) { ?>
                    <img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail" /> 
                    <?php } ?>
                    <?php echo $option_value['name']; ?>
                    <?php if ($option_value['price']) { ?>
                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                    <?php } ?>
                  </label>
                </div>
                <?php } ?>
              </div>
            </div>
            <?php } ?>
            <?php if (version_compare(VERSION, '2.3', '<')): ?>
            <?php if ($option['type'] == 'image') { ?>
            <div class="option form-group<?php echo ($option['required'] ? ' required' : ''); ?> option-<?php echo $option['type']; ?>">
              <label class="control-label"><?php echo $option['name']; ?></label>
              <div id="input-option<?php echo $option['product_option_id']; ?>">
                <?php foreach ($option['product_option_value'] as $option_value) { ?>
                <div class="radio">
                  <label>
                    <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
                    <img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail" /> <?php echo $option_value['name']; ?>
                    <?php if ($option_value['price']) { ?>
                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                    <?php } ?>
                  </label>
                </div>
                <?php } ?>
              </div>
            </div>
            <?php } ?>
            <?php endif; ?>
            <?php if ($option['type'] == 'text') { ?>
            <div class="option form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
              <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
              <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
            </div>
            <?php } ?>
            <?php if ($option['type'] == 'textarea') { ?>
            <div class="option form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
              <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
              <textarea name="option[<?php echo $option['product_option_id']; ?>]" rows="5" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control"><?php echo $option['value']; ?></textarea>
            </div>
            <?php } ?>
            <?php if ($option['type'] == 'file') { ?>
            <div class="option form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
              <label class="control-label"><?php echo $option['name']; ?></label>
              <button type="button" id="button-upload<?php echo $option['product_option_id']; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default btn-block btn-upload"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></button>
              <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" id="input-option<?php echo $option['product_option_id']; ?>" />
            </div>
            <?php } ?>
            <?php if ($option['type'] == 'date') { ?>
            <div class="option form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
              <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
              <div class="input-group date">
                <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
                <span class="input-group-btn">
                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                </span></div>
            </div>
            <?php } ?>
            <?php if ($option['type'] == 'datetime') { ?>
            <div class="option form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
              <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
              <div class="input-group datetime">
                <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span></div>
            </div>
            <?php } ?>
            <?php if ($option['type'] == 'time') { ?>
            <div class="option form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
              <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
              <div class="input-group time">
                <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span></div>
            </div>
            <?php } ?>
            <?php } ?>
            </div>
            <script>Journal.enableSelectOptionAsButtonsList();</script>
            <?php } ?>
            <?php if ($recurrings) { ?>
            <hr>
            <h3><?php echo $text_payment_recurring ?></h3>
            <div class="form-group required">
              <select name="recurring_id" class="form-control">
                <option value=""><?php echo $text_select; ?></option>
                <?php foreach ($recurrings as $recurring) { ?>
                <option value="<?php echo $recurring['recurring_id'] ?>"><?php echo $recurring['name'] ?></option>
                <?php } ?>
              </select>
              <div class="help-block" id="recurring-description"></div>
            </div>
          <?php } ?>
            <div class="form-group cart <?php echo isset($labels) && is_array($labels) && isset($labels['outofstock']) ? 'outofstock' : ''; ?>">
              <div>
              <?php if($this->journal2->settings->get('hide_add_to_cart_button')): ?>
              <?php foreach ($this->journal2->settings->get('additional_product_enquiry', array()) as $tab): ?>
              <div><?php echo $tab['content']; ?></div>
              <?php endforeach; ?>
              <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
              <?php else: ?>
                <span class="qty">
              <label class="control-label text-qty" for="input-quantity"><?php echo $entry_qty; ?></label>
              <input type="text" name="quantity" value="<?php echo $minimum; ?>" size="2" data-min-value="<?php echo $minimum; ?>" id="input-quantity" class="form-control" />
              <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
              <script>
                /* quantity buttons */
                var $input = $('.cart input[name="quantity"]');
                function up() {
                  var val = parseInt($input.val(), 10) + 1 || parseInt($input.attr('data-min-value'), 10);
                  $input.val(val);
                }
                function down() {
                  var val = parseInt($input.val(), 10) - 1 || 0;
                  var min = parseInt($input.attr('data-min-value'), 10) || 1;
                  $input.val(Math.max(val, min));
                }
                $('<a href="javascript:;" class="journal-stepper">-</a>').insertBefore($input).click(down);
                $('<a href="javascript:;" class="journal-stepper">+</a>').insertAfter($input).click(up);
                $input.keydown(function (e) {
                  if (e.which === 38) {
                    up();
                    return false;
                  }
                  if (e.which === 40) {
                    down();
                    return false;
                  }
                });
              </script>
              </span>
                <button type="button" id="button-cart" data-loading-text="<?php echo $text_loading; ?>" class="button"><span class="button-cart-text"><?php echo $button_cart; ?></span></button>
                <?php endif; ?>
              </div>
            </div>
            <?php if ($minimum > 1) { ?>
            <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $text_minimum; ?></div>
            <?php } ?>
            <div class="wishlist-compare">
              <span class="links">
                  <a onclick="addToWishList('<?php echo $product_id; ?>');"><?php echo $button_wishlist; ?></a>
                  <a onclick="addToCompare('<?php echo $product_id; ?>');"><?php echo $button_compare; ?></a>
              </span>
            </div>
          <?php if ($review_status) { ?>
          <div class="rating">
            <p>
              <?php for ($i = 1; $i <= 5; $i++) { ?>
              <?php if ($rating < $i) { ?>
              <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
              <?php } else { ?>
              <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
              <?php } ?>
              <?php } ?>
              <a href="" onclick="$('a[href=\'#tab-review\']').trigger('click'); return false;"><?php echo $reviews; ?></a> / <a href="" onclick="$('a[href=\'#tab-review\']').trigger('click'); return false;"><?php echo $text_write; ?></a></p>
          </div>
          <?php } ?>
            <?php if ($this->journal2->settings->get('share_buttons_status') && (!Journal2Cache::$mobile_detect->isMobile() || (Journal2Cache::$mobile_detect->isMobile() && !$this->journal2->settings->get('share_buttons_disable_on_mobile', 1))) && $this->journal2->settings->get('share_buttons_position') === 'right' && count($this->journal2->settings->get('config_share_buttons', array()))): ?>
            <div class="social share-this <?php echo $this->journal2->settings->get('share_buttons_disable_on_mobile', 1) ? 'hide-on-mobile' : ''; ?>">
              <div class="social-loaded">
                <script type="text/javascript">var switchTo5x=true;</script>
                <script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
                <script type="text/javascript">stLight.options({publisher: "<?php echo $this->journal2->settings->get('share_buttons_account_key'); ?>", doNotHash: true, doNotCopy: true, hashAddressBar: false});</script>
                <?php foreach ($this->journal2->settings->get('config_share_buttons', array()) as $item): ?>
                <span class="<?php echo $item['class'] . $this->journal2->settings->get('share_buttons_style'); ?>" displayText="<?php echo $this->journal2->settings->get('share_buttons_style') ? $item['name'] : ''; ?>"></span>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
            <?php foreach ($this->journal2->settings->get('additional_product_description_bottom', array()) as $tab): ?>
            <div class="journal-custom-tab">
              <?php if ($tab['has_icon']): ?>
              <div class="block-icon block-icon-left" style="<?php echo $tab['icon_css']; ?>"><?php echo $tab['icon']; ?></div>
              <?php endif; ?>
              <?php if ($tab['name']): ?>
              <h3><?php echo $tab['name']; ?></h3>
              <?php endif; ?>
              <?php echo $tab['content']; ?>
            </div>
            <?php endforeach; ?>
           </div>
          </div>
        </div>
      <?php if ($tags) { ?>
      <p class="tags"><b><?php echo $text_tags; ?></b>
        <?php for ($i = 0; $i < count($tags); $i++) { ?>
        <?php if ($i < (count($tags) - 1)) { ?>
        <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>,
        <?php } else { ?>
        <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
        <?php } ?>
        <?php } ?>
      </p>
      <?php } ?>
      <?php if ($products && $this->journal2->settings->get('related_products_status')) { ?>
      <div class="box related-products <?php echo $this->journal2->settings->get('related_products_carousel') ? 'journal-carousel' : ''; ?> <?php echo $this->journal2->settings->get('related_products_carousel') && $this->journal2->settings->get('related_products_carousel_arrows') === 'top' ? 'arrows-top' : ''; ?> <?php echo $this->journal2->settings->get('related_products_carousel') && $this->journal2->settings->get('related_products_carousel_bullets') ? 'bullets-on' : ''; ?>">
        <div>
          <div class="box-heading"><?php echo $text_related; ?></div>
          <div class="box-product box-content">
          <?php if ($this->journal2->settings->get('related_products_carousel')): ?>
          <div class="swiper">
          <div class="swiper-container" <?php echo $this->journal2->settings->get('rtl') ? 'dir="rtl"' : ''; ?>>
          <div class="swiper-wrapper">
          <?php endif; ?>
            <?php foreach ($products as $product) { ?>
            <div class="product-grid-item swiper-slide <?php echo $this->journal2->settings->get('related_products_grid_classes'); ?> display-<?php echo $this->journal2->settings->get('product_grid_wishlist_icon_display'); ?> <?php echo $this->journal2->settings->get('product_grid_button_block_button'); ?>">
              <div class="product-thumb product-wrapper <?php echo isset($product['labels']) && is_array($product['labels']) && isset($product['labels']['outofstock']) ? 'outofstock' : ''; ?>">
                <div class="image <?php echo $this->journal2->settings->get('show_countdown', 'never') !== 'never' && isset($product['date_end']) && $product['date_end'] ? 'has-countdown' : ''; ?>">
                  <a href="<?php echo $product['href']; ?>" <?php if(isset($product['thumb2']) && $product['thumb2']): ?> class="has-second-image" style="background: url('<?php echo $product['thumb2']; ?>') no-repeat;" <?php endif; ?>>
                  <img class="first-image" src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
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
          </div>
          <?php if ($this->journal2->settings->get('related_products_carousel') && $this->journal2->settings->get('related_products_carousel_arrows') !== 'none'): ?>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
          <?php endif; ?>
          </div>
          <?php if ($this->journal2->settings->get('related_products_carousel') && $this->journal2->settings->get('related_products_carousel_bullets')): ?>
            <div class="swiper-pagination"></div>
          <?php endif; ?>
          </div>
        </div>
      </div>
      <?php /* enable countdown */ ?>
      <?php if ($this->journal2->settings->get('show_countdown', 'never') !== 'never'): ?>
      <script>
        $('.related-products .product-grid-item > div').each(function () {
          var $new = $(this).find('.price-new');
          if ($new.length && $new.attr('data-end-date')) {
            $(this).find('.image').append('<div class="countdown"></div>');
          }
          Journal.countdown($(this).find('.countdown'), $new.attr('data-end-date'));
        });
      </script>
      <?php endif; ?>
      <?php if ($this->journal2->settings->get('related_products_carousel')): ?>
      <?php
      $grid = Journal2Utils::getItemGrid($this->journal2->settings->get('related_products_items_per_row'), $this->journal2->settings->get('site_width', 1024), $this->journal2->settings->get('config_columns_count'));
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
            spaceBetween: parseInt('<?php echo $this->journal2->settings->get('product_grid_item_spacing', '20'); ?>', 10),
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
      <?php } ?>
      <?php echo $content_bottom; ?></div>
    </div>
</div>
<script type="text/javascript"><!--
$('select[name=\'recurring_id\'], input[name="quantity"]').change(function(){
	$.ajax({
		url: 'index.php?route=product/product/getRecurringDescription',
		type: 'post',
		data: $('input[name=\'product_id\'], input[name=\'quantity\'], select[name=\'recurring_id\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#recurring-description').html('');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			
			if (json['success']) {
				$('#recurring-description').html(json['success']);
			}
		}
	});
});
//--></script> 
<script type="text/javascript"><!--
$('#button-cart').on('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-cart').button('loading');
		},
		complete: function() {
			$('#button-cart').button('reset');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');

			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						var element = $('#input-option' + i.replace('_', '-'));
						
						if (element.parent().hasClass('input-group')) {
							element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						} else {
							element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						}
					}
				}
				
				if (json['error']['recurring']) {
					$('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
				}
				
				// Highlight any found errors
				$('.text-danger').parent().addClass('has-error');
			}
			
			if (json['success']) {
                if (!Journal.showNotification(json['success'], json['image'], true)) {
                    $('.breadcrumb').after('<div class="alert alert-success success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }

				$('#cart-total').html(json['total']);

          if (Journal.scrollToTop) {
              $('html, body').animate({ scrollTop: 0 }, 'slow');
          }

				$('#cart ul').load('index.php?route=common/cart/info ul li');
			}
		},
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
	});
});
//--></script> 
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

$('.time').datetimepicker({
	pickDate: false
});

$('button[id^=\'button-upload\']').on('click', function() {
	var node = this;
	
	$('#form-upload').remove();
	
	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');
	
	$('#form-upload input[name=\'file\']').trigger('click');
	
	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);
			
			$.ajax({
				url: 'index.php?route=tool/upload',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).button('loading');
				},
				complete: function() {
					$(node).button('reset');
				},
				success: function(json) {
					$('.text-danger').remove();
					
					if (json['error']) {
						$(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
					}
					
					if (json['success']) {
						alert(json['success']);
						
						$(node).parent().find('input').attr('value', json['code']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});
//--></script> 
<script type="text/javascript"><!--
$('#review').delegate('.pagination a', 'click', function(e) {
  e.preventDefault();

    $('#review').fadeOut('slow');

    $('#review').load(this.href);

    $('#review').fadeIn('slow');
});

$('#review').load('index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');

$('#button-review').on('click', function() {
	$.ajax({
		url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
		type: 'post',
		dataType: 'json',
    <?php if (version_compare(VERSION, '2.0.2', '<')): ?>
		data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val()),
    <?php else: ?>
    data: $("#form-review").serialize(),
    <?php endif; ?>
		beforeSend: function() {
			$('#button-review').button('loading');
		},
		complete: function() {
			$('#button-review').button('reset');
      <?php if (version_compare(VERSION, '2.0.2', '<')): ?>
			$('#captcha').attr('src', 'index.php?route=tool/captcha#'+new Date().getTime());
			$('input[name=\'captcha\']').val('');
      <?php endif; ?>
		},
		success: function(json) {
			$('.alert-success, .alert-danger').remove();
			
			if (json['error']) {
				$('#review').after('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}
			
			if (json['success']) {
				$('#review').after('<div class="alert alert-success success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
				
				$('input[name=\'name\']').val('');
				$('textarea[name=\'text\']').val('');
				$('input[name=\'rating\']:checked').prop('checked', false);
        <?php if (version_compare(VERSION, '2.0.2', '<')): ?>
				$('input[name=\'captcha\']').val('');
        <?php endif; ?>
			}
		}
	});
});

$(document).ready(function() {
	$('.thumbnails').magnificPopup({
		type:'image',
		delegate: 'a',
		gallery: {
			enabled:true
		}
	});
});
//--></script> 
<?php echo $footer; ?>
