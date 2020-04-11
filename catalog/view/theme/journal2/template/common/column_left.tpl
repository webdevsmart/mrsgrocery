<?php if ($modules) { ?>
<div id="column-left" class="col-sm-3 hidden-xs side-column <?php echo $this->journal2->settings->get('flyout_column_left_active') ? 'flyout-left' : ''; ?>">
  <?php foreach ($modules as $module) { ?>
  <?php echo $module; ?>
  <?php } ?>
</div>
<?php } ?>