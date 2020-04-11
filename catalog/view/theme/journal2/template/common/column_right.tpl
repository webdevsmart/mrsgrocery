<?php if ($modules) { ?>
<div id="column-right" class="col-sm-3 hidden-xs side-column  <?php echo $this->journal2->settings->get('flyout_column_right_active') ? 'flyout-right' : ''; ?>">
  <?php foreach ($modules as $module) { ?>
  <?php echo $module; ?>
  <?php } ?>
</div>
<?php } ?>
