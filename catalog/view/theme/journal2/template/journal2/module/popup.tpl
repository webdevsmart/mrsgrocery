<div id="journal-popup-<?php echo $module; ?>" class="journal-popup-<?php echo $module_id; ?> journal-popup journal-popup-<?php echo $type; ?> <?php echo implode(' ', $disable_on_classes); ?> <?php echo $close_button ? '' : 'hide-close-button'; ?>" style="<?php echo implode('; ', $global_style); ?>; <?php echo $is_ajax? '' : 'display: none'; ?>">
    <?php if ($title): ?>
    <div class="journal-popup-header">
        <div class="journal-popup-header-content heading-title" style="<?php echo implode('; ', $header_style); ?>"><?php echo $title; ?></div>
    </div>
    <?php endif; ?>
    <?php if ($type === 'text'): ?>
    <div class="journal-popup-content  <?php echo $content_overflow; ?>" style="<?php echo implode('; ', $content_style); ?>">
        <?php echo $content; ?>
    </div>
    <?php endif; ?>

    <?php
        if ($type === 'contact') {
            if (class_exists('VQMod')) {
                global $vqmod;
                if ($vqmod !== null) {
                    if (function_exists('modification')) {
                        require $vqmod->modCheck(modification(DIR_TEMPLATE . $this->config->get('config_template') . "/template/information/contact.tpl"));
                    } else {
                        require $vqmod->modCheck(DIR_TEMPLATE . $this->config->get('config_template') . "/template/information/contact.tpl");
                    }
                } else {
                    if (function_exists('modification')) {
                        require VQMod::modCheck(modification(DIR_TEMPLATE . $this->config->get('config_template') . "/template/information/contact.tpl"));
                    } else {
                        require VQMod::modCheck(DIR_TEMPLATE . $this->config->get('config_template') . "/template/information/contact.tpl");
                    }
                }
            } else {
                if (function_exists('modification')) {
                    require modification(DIR_TEMPLATE . $this->config->get('config_template') . "/template/information/contact.tpl");
                } else {
                    require DIR_TEMPLATE . $this->config->get('config_template') . "/template/information/contact.tpl";
                }
            }
        }
    ?>

    <?php if ($newsletter && $type === 'text'): ?>
    <div class="journal-popup-newsletter">
        <div class="journal-popup-newsletter-content" style="<?php echo implode('; ', $newsletter_style); ?>"><?php echo $newsletter; ?></div>
    </div>
    <?php endif; ?>

    <?php if ($footer && $type === 'contact'): ?>
    <div class="journal-popup-footer">
        <div class="journal-popup-footer-content <?php echo $footer_buttons_class; ?>" style="<?php echo implode('; ', $footer_style); ?>">
            <a onclick="Journal.contact($('#journal-popup-<?php echo $module; ?>'))" class="button button-icon-<?php echo $button_submit['icon_position']; ?>" style="<?php echo $button_submit['style']; ?>"><?php echo $button_submit['icon']; ?><?php echo $button_submit['text']; ?></a>
        </div>
    </div>
    <script>$('head').append('<style>.journal-popup-footer-content  .button:hover { <?php echo $button_submit['hover_style']; ?>}</style>');</script>
    <?php endif; ?>

    <?php if ($footer && $type === 'text'): ?>
    <div class="journal-popup-footer">
        <div class="journal-popup-footer-content <?php echo $footer_buttons_class; ?>" style="<?php echo implode('; ', $footer_style); ?>">
            <?php if (!$is_ajax && $do_not_show_again): ?>
            <span class="dont-show-label">
                <label>
                    <input type="checkbox" class="dont-show-me" />
                    <span style="<?php echo implode('; ', $do_not_show_again_font); ?>"><?php echo $do_not_show_again_text; ?></span>
                </label>
            </span>
            <?php endif; ?>
            <?php if ($button_1['status']): ?>
                <?php if ($button_1['link']): ?>
                    <a href="<?php echo $button_1['link']; ?>" class="button button-1 button-icon-<?php echo $button_1['icon_position']; ?>" <?php echo $button_1['target']; ?> style="<?php echo $button_1['style']; ?>"><?php echo $button_1['icon']; ?><?php echo $button_1['text']; ?></a>
                <?php else: ?>
                    <a onclick="$.magnificPopup.close();" class="button button-1 button-icon-<?php echo $button_1['icon_position']; ?>" style="<?php echo $button_1['style']; ?>"><?php echo $button_1['icon']; ?><?php echo $button_1['text']; ?></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($button_2['status']): ?>
                <?php if ($button_2['link']): ?>
                    <a href="<?php echo $button_2['link']; ?>" class="button button-2 button-icon-<?php echo $button_2['icon_position']; ?>" <?php echo $button_2['target']; ?> style="<?php echo $button_2['style']; ?>"><?php echo $button_2['icon']; ?><?php echo $button_2['text']; ?></a>
                <?php else: ?>
                    <a onclick="$.magnificPopup.close();" class="button button-2 button-icon-<?php echo $button_2['icon_position']; ?> " style="<?php echo $button_2['style']; ?>"><?php echo $button_2['icon']; ?><?php echo $button_2['text']; ?></a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php if (!$is_ajax): ?>
<script>
    (function () {
        <?php if ($button_1['status'] && $button_1['hover_style']): ?>
        $('<style>#journal-popup-<?php echo $module; ?> .button-1:hover { <?php echo $button_1['hover_style']; ?> }</style>').appendTo($('head'));
        <?php endif; ?>

        <?php if ($button_2['status'] && $button_2['hover_style']): ?>
        $('<style>#journal-popup-<?php echo $module; ?> .button-2:hover { <?php echo $button_2['hover_style']; ?> }</style>').appendTo($('head'));
        <?php endif; ?>

        var open_after = parseInt('<?php echo $open_after; ?>', 10);
        var close_after = parseInt('<?php echo $close_after; ?>', 10);

        setTimeout(function () {
            $('#journal-popup-<?php echo $module; ?>').show();
            $.magnificPopup.open({
                items: {
                    src: '#journal-popup-<?php echo $module; ?>',
                    type: 'inline'
                },
                showCloseBtn: <?php echo $close_button ? 'true' : 'false'; ?>,
                enableEscapeKey: <?php echo $close_button ? 'true' : 'false'; ?>,
                closeOnContentClick: false,
                closeOnBgClick: false,
                removalDelay: 200,
                callbacks: {
                    close: function () {
                        $('html').removeClass('has-popup');
                        $('#journal-popup-<?php echo $module; ?>').remove();
                    },
                    open: function () {
                        $('html').addClass('has-popup');
                    }
                }
            });

            if (close_after > 0) {
                setTimeout(function () {
                    $.magnificPopup.close();
                }, close_after);
            }
        }, open_after);

        $('#journal-popup-<?php echo $module; ?> .dont-show-me').change(function () {
            if ($(this).is(':checked')) {
                $.cookie('<?php echo $cookie_name; ?>', true, {
                    path: '/',
                    expires: 365
                });
            } else {
                $.removeCookie('<?php echo $cookie_name; ?>')
            }
        });
    }());
</script>
<?php else: ?>
<script type="text/html" style="display: none;">
	<?php if ($button_1['status'] && $button_1['hover_style']): ?>
	$('<style>#journal-popup-<?php echo $module; ?> .button-1:hover { <?php echo $button_1['hover_style']; ?> }</style>').appendTo($('head'));
	<?php endif; ?>

	<?php if ($button_2['status'] && $button_2['hover_style']): ?>
	$('<style>#journal-popup-<?php echo $module; ?> .button-2:hover { <?php echo $button_2['hover_style']; ?> }</style>').appendTo($('head'));
	<?php endif; ?>
</script>
<?php endif; ?>