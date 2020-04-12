<div class="journal-slider-<?php echo $module_id; ?> tp-banner-container box <?php echo $js_options['hideThumbs'] ? 'nav-on-hover' : '' ?> <?php echo $slider_class; ?> <?php echo $js_options['thumbAmount'] === '' ? 'full-thumbs' : ''; ?> <?php echo implode(' ', $disable_on_classes); ?> <?php echo Journal2Utils::getProperty($js_options, 'navigationType') === 'none' ? 'hide-navigation' : ''; ?>" style="<?php echo $width; ?>; height: <?php echo $height; ?>px;">
    <div class="tp-banner" id="journal-slider-<?php echo $module; ?>" style="display: none; background-image: url('<?php echo $slides[0]['image']; ?>'); background-size: cover; background-position: center center;">
        <ul>
            <?php foreach ($slides as $slide): ?>
            <li <?php echo $slide['data']; ?> >
            <?php if ($preload_images): ?>
            <img src="<?php echo $dummy_image; ?>" data-lazyload="<?php echo $slide['image']; ?>" width="<?php echo $js_options['startwidth']; ?>" height="<?php echo $js_options['startheight']; ?>" alt="<?php echo $slide['name']; ?>" />
            <?php else: ?>
            <img src="<?php echo $slide['image']; ?>" width="<?php echo $js_options['startwidth']; ?>" height="<?php echo $js_options['startheight']; ?>" alt="<?php echo $slide['name']; ?>" />
            <?php endif; ?>
            <?php foreach ($slide['captions'] as $caption): ?>
            <?php if ($caption['link']): ?>
            <a id="jcaption-<?php echo $caption['id']; ?>" href="<?php echo $caption['link']; ?>" <?php echo $caption['target']; ?> class="tp-caption <?php echo $caption['classes']; ?>" style="<?php echo $caption['css']; ?>" <?php echo $caption['data']; ?>>
            <?php echo $caption['content']; ?>
            </a>
            <?php else: ?>
            <div id="jcaption-<?php echo $caption['id']; ?>" class="tp-caption <?php echo $caption['classes']; ?>" style="<?php echo $caption['css']; ?>" <?php echo $caption['data']; ?>>
            <?php echo $caption['content']; ?>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            </li>
            <?php endforeach; ?>
         </ul>
        <?php if ($timer === 'top'): ?>
        <div class="tp-bannertimer"></div>
        <?php elseif ($timer === 'bottom'): ?>
        <div class="tp-bannertimer tp-bottom"></div>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($css) && $css): ?>
<style>
<?php echo implode (' ', $css); ?>
</style>
<?php endif; ?>

<script>
    (function () {
        $('<style><?php echo implode(" ", $global_style); ?></style>').appendTo($('head'));

        var opts = $.parseJSON('<?php echo json_encode($js_options); ?>');
        opts.hideThumbs = 0;
        $('#journal-slider-<?php echo $module; ?>').show().revolution(opts);
        <?php if ($timer !== 'top' && $timer !== 'bottom'): ?>
        $('#journal-slider-<?php echo $module; ?> .tp-bannertimer').hide();
        <?php endif; ?>
        setTimeout(function() {
            $('#journal-slider-<?php echo $module; ?>').css('background-image', 'none');
        }, 2500);
    })();
</script>
