<div id="journal-blog-comments-<?php echo $module; ?>" class="journal-blog-comments-<?php echo $module_id; ?> box side-blog side-posts blog-comments">
    <div class="box-heading"><?php echo $heading_title; ?></div>
    <div class="box-comment box-post">
        <?php foreach ($comments as $comment): ?>
        <div class="side-post">
            <a class="side-post-image" href="<?php echo $comment['href']; ?>"><?php echo Journal2Utils::gravatar($comment['email'], $default_author_image, 75, ''); ?></a>
            <div class="side-post-details">
                <a class="side-post-title" href="<?php echo $comment['href']; ?>"><?php echo $comment['post']; ?></a>
                <div class="comment-date">
                    <span class="p-author"><?php echo $comment['name']; ?></span>
                </div>
            </div>
        </div>
        <hr/>
        <?php endforeach; ?>
    </div>
</div>