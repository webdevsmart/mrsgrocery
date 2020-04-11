<div id="journal-header-notice-<?php echo $module; ?>" class="journal-header-notice-<?php echo $module_id; ?> header-notice <?php echo $text_align; ?>-notice <?php echo implode(' ', $disable_on_classes); ?> <?php echo $float_icon; ?> <?php echo $fullwidth; ?>" style="<?php echo $css; ?>">
<div>


    <span class="notice-text">
        <?php echo $icon; ?>
        <span><?php echo $text; ?></span>
    </span>

    <?php if ($close_button_type === 'icon'): ?>
        <button class="close-notice x-icon"></button>
    <?php else: ?>
        <button class="close-notice button"><?php echo $close_button_text; ?></button>
    <?php endif; ?>

    <script>
        $('#journal-header-notice-<?php echo $module; ?> .close-notice').click(function () {
            var isSticky = $('html').hasClass('header-sticky');
            var h = $('#journal-header-notice-<?php echo $module; ?>').outerHeight();
            Journal.stickyHeaderHeight -= h;
            $('#journal-header-notice-<?php echo $module; ?>').slideUp(function() {
                if (isSticky) {
                    $('.header').css('top', $('.header').css('top') - h);
                }
            });
            <?php if ($do_not_show_again): ?>
            $.cookie('<?php echo $cookie_name; ?>', true, {
                expires: 365
            });
            <?php endif; ?>
        });

        <?php if ($global_style): ?>
        $('<style><?php echo implode(" ", $global_style); ?></style>').appendTo($('head'));
        <?php endif; ?>
    </script>
</div>
</div>