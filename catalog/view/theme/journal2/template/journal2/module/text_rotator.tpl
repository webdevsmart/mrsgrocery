<div id="journal-rotator-<?php echo $module; ?>" class="journal-rotator-<?php echo $module_id; ?> journal-rotator box text-rotator <?php echo implode(' ', $disable_on_classes); ?> bullets-<?php echo $bullets_position; ?> align-<?php echo $text_align; ?> bullets-<?php echo $bullets ? 'on' : 'off'; ?>" style="<?php echo $rotator_css; ?>; <?php echo isset($css) ? $css : ''; ?>">
    <?php if ($title): ?>
        <div class="box-heading text-rotator-heading <?php echo implode(' ', $disable_on_classes); ?>"><?php echo $title; ?></div>
    <?php endif; ?>
    <?php foreach ($sections as $section): ?>
    <div class="quote" style="<?php echo $quote_css; ?>">
        <?php if ($section['image']): ?>
        <img width="<?php echo $section['image_width']; ?>" height="<?php echo $section['image_height']; ?>" src="<?php echo $section['image']; ?>" alt="" class="rotator-image image-<?php echo $image_align; ?>" style="<?php echo $image_css; ?>" />
        <?php endif; ?>
        <span class="rotator-text"><?php echo $section['icon']; ?><?php echo $section['text']; ?></span>
        <?php if ($section['author']): ?>
        <div class="rotator-author" style="<?php echo $author_css; ?>">- <?php echo $section['author']; ?></div>
        <?php endif; ?>
        <div class="clearfix"> </div>
    </div>
    <?php endforeach; ?>
</div>
<script>
    (function () {
        var single_quote = parseInt('<?php echo count($sections); ?>', 10) <= 1;

        $('#journal-rotator-<?php echo $module; ?>').quovolver({
            children        : '.quote',
            equalHeight     : false,
            navPosition     : single_quote ? '' : 'below',
            navNum          : '<?php echo $bullets; ?>' ? true : false,
            pauseOnHover    : parseInt('<?php echo $pause_on_hover; ?>', 10) ? true : false,
            autoPlay        : !single_quote,
            autoPlaySpeed   : '<?php echo $transition_delay; ?>',
            transitionSpeed : 300
        });
    })();
</script>