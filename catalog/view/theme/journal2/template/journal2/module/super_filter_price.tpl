<div class="sf-price <?php echo $price_slider == '1' ? '' : 'sf-input'; ?> box <?php echo $is_collapsed ? 'is-collapsed' : ''; ?>" data-id="price" data-min-price="<?php echo $min_price; ?>" data-max-price="<?php echo $max_price; ?>">
    <div class="box-heading"><?php echo $this->journal2->settings->get('filter_price_text', $text_price); ?></div>
    <div class="box-content">
        <?php if($price_slider == '1'): ?>
        <section class="slider" data-min-value="<?php echo $min_price; ?>" data-max-value="<?php echo $max_price; ?>"></section>
        <?php else: ?>
        <span class="<?php echo $is_currency_left ? 'currency-left' : 'currency-right'; ?>">
            <label for="filter-min-price"><?php echo $is_currency_left ? $currency_left : $currency_right; ?></label>
            <input id="filter-min-price" type="text" class="min-price" value="<?php echo $min_price; ?>" />
        </span>
        <span class="<?php echo $is_currency_left ? 'currency-left' : 'currency-right'; ?>">
            <label for="filter-max-price"><?php echo $is_currency_left ? $currency_left : $currency_right; ?></label>
            <input id="filter-max-price" type="text" class="max-price" value="<?php echo $max_price; ?>" />
        </span>
        <input type="button" class="button btn price-filter-button" value="<?php echo $this->journal2->settings->get('filter_price_apply_text', 'Apply'); ?>" />
        <?php endif; ?>
    </div>
</div>