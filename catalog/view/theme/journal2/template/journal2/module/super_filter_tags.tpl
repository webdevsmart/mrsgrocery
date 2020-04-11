<div class="box sf-tags <?php echo $is_collapsed ? 'is-collapsed' : ''; ?>" data-id="tags">
    <div class="box-heading"><?php echo $this->journal2->settings->get('filter_tags_text'); ?></div>
    <div class="box-content">
        <ul class="<?php echo $this->journal2->settings->get('filter_show_box') ? '' : 'hide-checkbox'; ?>">
            <?php foreach ($tags as $t) { ?>
            <li><label><input data-keyword="<?php echo $t['keyword']?>" type="checkbox" name="tag" value="<?php echo $t['text']; ?>" <?php echo isset($tag) && $tag == $t['text'] ? 'checked' : '' ?>><span class="sf-name"><?php echo $t['name']; ?> </span></label></li>
            <?php } ?>
        </ul>
    </div>
</div>