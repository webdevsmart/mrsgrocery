<div id="journal-newsletter-<?php echo $module; ?>" class="journal-newsletter-<?php echo $module_id; ?> box journal-newsletter text-<?php echo $text_class; ?> <?php echo implode(' ', $disable_on_classes); ?>" style="<?php echo isset($css) ? $css : ''; ?>">
    <?php if ($heading_title): ?>
    <div class="box-heading"><?php echo $heading_title; ?></div>
    <?php endif; ?>
    <div class="box-content" style="<?php echo $module_css; ?>">
        <?php if ($module_text): ?>
        <span class="newsletter-text" style="<?php echo $font_css; ?>"><?php echo $module_text; ?></span>
        <?php endif; ?>
        <span class="newsletter-input-wrap" style="<?php echo $input_style; ?>">
            <input type="text" class="newsletter-email" placeholder="<?php echo $input_placeholder; ?>" style="<?php echo $input_field_style; ?>" />
            <a class="newsletter-button button" onclick="Journal.newsletter($('#journal-newsletter-<?php echo $module; ?>'));" style="<?php echo $button_style; ?>"><?php echo $button_icon; ?><?php echo $button_text; ?></a>
        </span>
    </div>
    <script>
        $("<style><?php echo implode(" ", $global_style); ?></style>").appendTo($('head'));
    </script>
</div>