<div id="journal-super-filter-<?php echo $module_id; ?>" class="journal-sf <?php echo $is_collapsible ? 'filter-collapse' : ''; ?> <?php echo isset($disable_on_classes) ? implode(' ', $disable_on_classes) : ''; ?>" data-filters-action="<?php echo $filter_action_url; ?>" data-products-action="<?php echo $products_action_url; ?>" data-route="<?php echo $route; ?>" data-path="<?php echo $path; ?>" data-full_path="<?php echo $full_path; ?>" data-manufacturer="<?php echo $manufacturer_id; ?>" data-search="<?php echo $search; ?>" data-tag="<?php echo $tag; ?>"  data-loading-text="<?php echo $loading_text; ?>" data-currency-left="<?php echo $currency_left; ?>" data-currency-right="<?php echo $currency_right; ?>" data-currency-decimal="<?php echo $currency_decimal; ?>" data-currency-thousand="<?php echo $currency_thousand; ?>" data-category_id="<?php echo isset($category_id) ? $category_id : null; ?>" data-sub_category="<?php echo isset($sub_category) ? $sub_category : null; ?>" data-st="E.R.">
    <?php if ($reset): ?>
    <a class="sf-reset hint--top sf-<?php echo $this->journal2->settings->get('filter_reset_display'); ?>" data-hint="<?php echo $reset_btn_text; ?>"><span class="sf-reset-text"><?php echo $reset_btn_text; ?></span><i class="sf-reset-icon"></i></a>
    <?php endif; ?>
    <input type="hidden" class="sf-page" value="" />
    <?php foreach ($filter_groups as $filter_group): ?>
    <?php echo $filter_group['html']; ?>
    <?php endforeach; ?>
</div>
<?php if (!$ajax) { ?>
<script>
    Journal.SuperFilter.init($('#journal-super-filter-<?php echo $module_id; ?>'));
</script>
<?php } ?>
