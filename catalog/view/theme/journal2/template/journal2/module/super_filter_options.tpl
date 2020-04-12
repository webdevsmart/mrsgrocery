<div class="box sf-option sf-<?php echo $option['display_mode']; ?> sf-option-<?php echo $option['option_id']; ?> sf-<?php echo $option['type']; ?> <?php echo $is_collapsed ? 'is-collapsed' : ''; ?>" data-id="option-<?php echo $option['option_id']; ?>">
    <div class="box-heading"><?php echo $option['option_name']; ?></div>
    <div class="box-content">
        <ul class="<?php echo $this->journal2->settings->get('filter_show_box') ? '' : 'hide-checkbox'; ?>">
            <?php foreach ($option['values'] as $value) { ?>
            <?php if ($option['display_mode'] === 'list'): ?>
            <li><label><input data-keyword="<?php echo $value['keyword']?>" type="checkbox" name="option[<?php echo $option['option_id']?>]" value="<?php echo $value['option_value_id']; ?>"><span class="sf-name"><?php echo $value['option_value_name']; ?></span> </label></li>
            <?php elseif ($option['display_mode'] === 'image'): ?>
            <li><label><input data-keyword="<?php echo $value['keyword']?>" type="checkbox" name="option[<?php echo $option['option_id']?>]" value="<?php echo $value['option_value_id']; ?>"><img width="<?php echo $option_image_width; ?>" height="<?php echo $option_image_height; ?>" src="<?php echo $value['image']; ?>" alt="<?php echo $value['option_value_name']; ?>" title="<?php echo $value['option_value_name']; ?>"/></label></li>
            <?php else: ?>
            <li><label><input data-keyword="<?php echo $value['keyword']?>" type="checkbox" name="option[<?php echo $option['option_id']?>]" value="<?php echo $value['option_value_id']; ?>"><img width="<?php echo $option_image_width; ?>" height="<?php echo $option_image_height; ?>" src="<?php echo $value['image']; ?>" alt="<?php echo $value['option_value_name']; ?>"/><span class="sf-name"><?php echo $value['option_value_name']; ?></span> </label></li>
            <?php endif; ?>
            <?php } ?>
        </ul>
    </div>
</div>