<div id="journal-blog-search-<?php echo $module; ?>" class="journal-blog-search-<?php echo $module_id; ?> box side-blog blog-search">
    <?php if ($heading_title): ?>
    <div class="box-heading"><?php echo $heading_title; ?></div>
    <?php endif; ?>
    <div class="box-post">
       <div class="box-search">
           <input type="text" value="<?php echo $search; ?>" placeholder="<?php echo $placeholder; ?>" />
           <a class="search-btn"></a>
       </div>
    </div>
</div>
<script>Journal.blogSearch($('#journal-blog-search-<?php echo $module; ?>'), '<?php echo $search_url; ?>');</script>