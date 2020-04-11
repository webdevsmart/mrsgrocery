<div class="box sf-attribute sf-attribute-<?php echo $attribute['attribute_id']; ?> sf-<?php echo $attribute['type']; ?> <?php echo $is_collapsed ? 'is-collapsed' : ''; ?>" data-id="attribute-<?php echo $attribute['attribute_id']; ?>">
    <div class="box-heading"><?php echo $attribute['attribute_name']; ?></div>
    <div class="box-content">
        <ul class="<?php echo $this->journal2->settings->get('filter_show_box') ? '' : 'hide-checkbox'; ?>">
            <?php foreach ($attribute['values'] as $value) { ?>
            <li><label><input data-keyword="<?php echo $value['keyword']?>" type="checkbox" name="attribute[<?php echo $attribute['attribute_id']?>]" value="<?php echo $value['text']; ?>"><?php echo $value['name']; ?></label></li>
            <?php } ?>
        </ul>
    </div>
</div>