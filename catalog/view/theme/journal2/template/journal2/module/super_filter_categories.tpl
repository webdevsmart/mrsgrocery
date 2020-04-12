<div class="box sf-category sf-<?php echo $category_display_mode; ?> sf-<?php echo $category_type; ?> <?php echo $is_collapsed ? 'is-collapsed' : ''; ?>" data-id="category">
    <div class="box-heading"><?php echo $this->journal2->settings->get('filter_categories_text', $text_categories); ?></div>
    <div class="box-content">
        <ul class="<?php echo $this->journal2->settings->get('filter_show_box') ? '' : 'hide-checkbox'; ?>">
            <?php foreach ($categories as $category) { ?>
            <?php if ($category_display_mode === 'list'): ?>
            <li><label><input data-keyword="<?php echo $category['keyword']?>" type="checkbox" name="category" value="<?php echo $category['category_id']; ?>"><span class="sf-name"><?php echo $category['name']; ?></span> </label></li>
            <?php elseif ($category_display_mode === 'image'): ?>
            <li><label><input data-keyword="<?php echo $category['keyword']?>" type="checkbox" name="category" value="<?php echo $category['category_id']; ?>"><img width="<?php echo $cat_image_width; ?>" height="<?php echo $cat_image_height; ?>" src="<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>" title="<?php echo $category['name']; ?>"/></label></li>
            <?php else: ?>
            <li><label><input data-keyword="<?php echo $category['keyword']?>" type="checkbox" name="category" value="<?php echo $category['category_id']; ?>"><img width="<?php echo $cat_image_width; ?>" height="<?php echo $cat_image_height; ?>" src="<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>"/><span class="sf-name"><?php echo $category['name']; ?> </span></label></li>
            <?php endif; ?>
            <?php } ?>
        </ul>
    </div>
</div>