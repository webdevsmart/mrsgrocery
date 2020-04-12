<div id="journal-blog-side-posts-<?php echo $module; ?>" class="journal-blog-side-posts-<?php echo $module_id; ?> box side-blog side-posts">
    <div class="box-heading"><?php echo $heading_title; ?></div>
    <div class="box-post">
    <?php foreach ($posts as $post): ?>
        <div class="side-post">
            <?php if ($post['image']): ?>
            <a class="side-post-image" href="<?php echo $post['href']; ?>"><img src="<?php echo $post['image']; ?>" alt="<?php echo $post['name']; ?>"/></a>
            <?php endif; ?>
            <div class="side-post-details">
                <a class="side-post-title" href="<?php echo $post['href']; ?>"><?php echo $post['name']; ?></a>
                <div class="comment-date">
                    <span class="p-date"><?php echo $post['date']; ?></span>
                    <span class="p-comment"><?php echo $post['comments']; ?></span>
                </div>
            </div>
        </div>
        <hr/>
    <?php endforeach; ?>
    </div>
</div>
