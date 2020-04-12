<div id="journal-blog-tags-<?php echo $module; ?>" class="journal-blog-tags-<?php echo $module_id; ?> box side-blog side-posts side-blog-tags">
    <div class="box-heading"><?php echo $heading_title; ?></div>
    <div class="box-tag box-post">
        <div>
            <?php foreach ($tags as $tag): ?>
            <a href="<?php echo $tag['href']; ?>"><?php echo $tag['name']; ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</div>