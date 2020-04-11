<div class="box oc-module">
  <div class="list-group box-content">
    <ul class="box-category">
      <?php foreach ($categories as $category) { ?>
      <?php if ($category['category_id'] == $category_id) { ?>
      <li><a href="<?php echo $category['href']; ?>" class="list-group-item active"><?php echo $category['name']; ?></a></li>
      <?php if ($category['children']) { ?>
      <?php foreach ($category['children'] as $child) { ?>
      <?php if ($child['category_id'] == $child_id) { ?>
      <li><a href="<?php echo $child['href']; ?>" class="list-group-item active">&nbsp;&nbsp;&nbsp;- <?php echo $child['name']; ?></a></li>
      <?php } else { ?>
      <li><a href="<?php echo $child['href']; ?>" class="list-group-item">&nbsp;&nbsp;&nbsp;- <?php echo $child['name']; ?></a></li>
      <?php } ?>
      <?php } ?>
      <?php } ?>
      <?php } else { ?>
      <li><a href="<?php echo $category['href']; ?>" class="list-group-item"><?php echo $category['name']; ?></a></li>
      <?php } ?>
      <?php } ?>
    </ul>
  </div>
</div>
