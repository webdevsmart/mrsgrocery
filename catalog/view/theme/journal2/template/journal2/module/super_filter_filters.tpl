<div class="box sf-filter sf-<?php echo $filter['display_mode']; ?> sf-filter-<?php echo $filter['filter_group_id']; ?> sf-<?php echo $filter['type']; ?> <?php echo $is_collapsed ? 'is-collapsed' : ''; ?>" data-id="filter-<?php echo $filter['filter_group_id']; ?>">
    <div class="box-heading"><?php echo $filter['filter_group_name']; ?></div>
    <div class="box-content">
        <ul class="<?php echo $this->journal2->settings->get('filter_show_box') ? '' : 'hide-checkbox'; ?>">
            <?php foreach ($filter['values'] as $value) { ?>
            <li><label><input data-keyword="<?php echo $value['keyword']?>" type="checkbox" name="filter[<?php echo $filter['filter_group_id']?>]" value="<?php echo $value['filter_id']; ?>"><span class="sf-name"><?php echo $value['filter_name']; ?></span> </label></li>
            <?php } ?>
        </ul>
    </div>
</div>