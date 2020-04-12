<div id="side-block-<?php echo $module; ?>" class="side-block-<?php echo $module_id; ?> side-block-<?php echo $alignment; ?> side-block-<?php echo $type; ?>" style="<?php echo isset($css) ? $css : ''; ?>">
    <?php if ($type === 'button'): ?>
    <div class="side-block-icon" style="width: <?php echo $icon_width; ?>; height: <?php echo $icon_height; ?>; line-height: <?php echo $icon_height; ?>; background-color: <?php echo $icon_bgcolor; ?>; <?php echo $icon_border; ?>; <?php echo $pos_offset; ?>"><a href="<?php echo $url; ?>" <?php echo $target; ?>><?php echo $icon; ?></a></div>
    <?php if ($icon_bg_hover_color): ?>
    <script>$('<style>#side-block-<?php echo $module; ?> .side-block-icon:hover { background-color: <?php echo $icon_bg_hover_color; ?> !important; }</style>').appendTo($('head'));</script>
    <?php endif; ?>
    <?php endif; ?>
    <?php if ($type === 'block'): ?>
    <div class="side-block-icon" style="width: <?php echo $icon_width; ?>; height: <?php echo $icon_height; ?>; line-height: <?php echo $icon_height; ?>; background-color: <?php echo $icon_bgcolor; ?>; <?php echo $icon_border; ?>; <?php echo $pos_offset; ?>"><?php echo $icon; ?></div>
    <div class="side-block-content" style="background-color: <?php echo $content_bgcolor; ?>; padding: <?php echo $content_padding; ?>" data-url="<?php echo $url; ?>">Loading...</div>
    <?php endif; ?>
</div>
