<div class="box sf-manufacturer sf-<?php echo $manufacturer_display_mode; ?> sf-<?php echo $manufacturer_type; ?> <?php echo $is_collapsed ? 'is-collapsed' : ''; ?>" data-id="manufacturer">
    <div class="box-heading"><?php echo $this->journal2->settings->get('filter_brands_text', $text_manufacturers); ?></div>
    <div class="box-content">
        <ul class="<?php echo $this->journal2->settings->get('filter_show_box') ? '' : 'hide-checkbox'; ?>">
            <?php foreach ($manufacturers as $manufacturer) { ?>
            <?php if ($manufacturer_display_mode === 'list'): ?>
            <li><label><input data-keyword="<?php echo $manufacturer['keyword']?>" type="checkbox" name="manufacturer" value="<?php echo $manufacturer['manufacturer_id']; ?>"><span class="sf-name"><?php echo $manufacturer['name']; ?> </span></label></li>
            <?php elseif ($manufacturer_display_mode === 'image'): ?>
            <li><label><input data-keyword="<?php echo $manufacturer['keyword']?>" type="checkbox" name="manufacturer" value="<?php echo $manufacturer['manufacturer_id']; ?>"><img width="<?php echo $brand_image_width; ?>" height="<?php echo $brand_image_height; ?>" data-hint="<?php echo $manufacturer['name']; ?>" src="<?php echo $manufacturer['image']; ?>" alt="<?php echo $manufacturer['name']; ?>" title="<?php echo $manufacturer['name']; ?>"/></label></li>
            <?php else: ?>
            <li><label><input data-keyword="<?php echo $manufacturer['keyword']?>" type="checkbox" name="manufacturer" value="<?php echo $manufacturer['manufacturer_id']; ?>"><img width="<?php echo $brand_image_width; ?>" height="<?php echo $brand_image_height; ?>" src="<?php echo $manufacturer['image']; ?>" alt="<?php echo $manufacturer['name']; ?>"/><span class="sf-name"><?php echo $manufacturer['name']; ?></span> </label></li>
            <?php endif; ?>
            <?php } ?>
        </ul>
    </div>
</div>