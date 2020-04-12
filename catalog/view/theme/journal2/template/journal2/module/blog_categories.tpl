<div id="journal-blog-categories-<?php echo $module; ?>" class="journal-blog-categories-<?php echo $module_id; ?> box side-blog blog-category">
    <div class="box-heading"><?php echo $heading_title; ?></div>
    <div class="box-category box-post">
        <ul>
            <?php foreach ($categories as $category): ?>
            <li>
                <a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>